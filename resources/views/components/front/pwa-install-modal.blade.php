<div x-data="{
        showModal: false,
        deferredPrompt: null,
        isIos: false,
        init() {
            // Check if user has already dismissed the modal recently
            const lastDismissed = localStorage.getItem('pwa-install-dismissed');
            const now = new Date().getTime();
            
            // If dismissed less than 24 hours ago, don't show
            if (lastDismissed && (now - lastDismissed) < 24 * 60 * 60 * 1000) {
                return;
            }

            // Detect if iOS and not in standalone mode
            const userAgent = window.navigator.userAgent.toLowerCase();
            this.isIos = /iphone|ipad|ipod/.test(userAgent);
            const isInStandaloneMode = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone || document.referrer.includes('android-app://');

            if (isInStandaloneMode) {
                return; // Already installed
            }

            if (this.isIos) {
                // Delay showing the manual instructions on iOS by 3 seconds
                setTimeout(() => {
                    this.showModal = true;
                }, 3000);
            }

            window.addEventListener('beforeinstallprompt', (e) => {
                // Prevent Chrome 67+ from automatically showing the prompt
                e.preventDefault();
                // Stash the event so it can be triggered later.
                this.deferredPrompt = e;
                // Update UI notify the user they can add to home screen
                this.showModal = true;
            });

            window.addEventListener('appinstalled', () => {
                // Log install to analytics
                console.log('PWA was installed');
                this.showModal = false;
                this.deferredPrompt = null;
            });
        },
        async installPWA() {
            if (!this.deferredPrompt) return;
            
            // Show the prompt
            this.deferredPrompt.prompt();
            
            // Wait for the user to respond to the prompt
            const { outcome } = await this.deferredPrompt.userChoice;
            console.log(`User response to the install prompt: ${outcome}`);
            
            // We've used the prompt, and can't use it again, throw it away
            this.deferredPrompt = null;
            this.showModal = false;
        },
        dismissModal() {
            this.showModal = false;
            // Record dismissal to prevent showing it again too soon
            localStorage.setItem('pwa-install-dismissed', new Date().getTime());
        }
    }" x-show="showModal" x-cloak
    class="fixed bottom-0 start-0 end-0 z-[10000] flex justify-center pointer-events-none"
    x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-y-full"
    x-transition:enter-end="translate-y-0" x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full">

    {{-- Bottom Sheet Card (Mini version) --}}
    <div
        class="relative w-full max-w-xl bg-white rounded-t-3xl shadow-[0_-8px_30px_rgb(0,0,0,0.12)] overflow-hidden pointer-events-auto border-t border-gray-100">
        <div class="p-5 sm:p-6">
            {{-- Content --}}
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                    <img src="/images/icons/icon-192x192.png" alt="CakeCraft Logo" class="w-10 h-10 object-contain">
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-display font-bold text-espresso leading-tight">
                        {{ __('front.pwa.install_title', ['name' => config('app.name')]) }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-show="!isIos">
                        {{ __('front.pwa.install_subtitle') }}
                    </p>
                    <p class="text-xs text-primary font-medium mt-0.5" x-show="isIos">
                        {{ __('front.pwa.ios_instruction') }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3">
                <button @click="dismissModal()"
                    class="flex-1 py-3.5 text-gray-500 font-bold rounded-2xl bg-gray-50 hover:bg-gray-100 transition-colors text-center text-sm">
                    {{ __('front.pwa.not_now') }}
                </button>
                <button x-show="!isIos" @click="installPWA()"
                    class="flex-1 py-3.5 bg-primary text-white font-bold rounded-2xl hover:bg-primary-hover transition-all duration-300 shadow-md shadow-primary/10 active:scale-[0.98] text-center text-sm">
                    {{ __('front.pwa.install_button') }}
                </button>
            </div>

            {{-- Safe Area Bottom Padding --}}
            <div class="h-safe-bottom sm:h-2"></div>
        </div>
    </div>
</div>