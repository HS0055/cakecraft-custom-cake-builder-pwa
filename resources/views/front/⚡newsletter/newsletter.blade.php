<div>
    <h4 class="font-display text-sm font-semibold text-white uppercase tracking-wider mb-5">
        {{ __('front.newsletter.stay_sweet') }}
    </h4>

    @if($subscribed)
        <div
            class="bg-white/10 border border-white/20 rounded-xl px-4 py-4 text-center animate-fade-in relative overflow-hidden">
            <div
                class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent bg-[length:200%_100%] animate-shimmer">
            </div>
            <div class="relative z-10 flex flex-col items-center gap-2">
                <svg class="w-8 h-8 text-white mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <p class="text-sm font-medium text-white">{{ __('front.newsletter.thanks_subscribing') }}</p>
                <p class="text-xs text-white/80">{{ __('front.newsletter.keep_updated') }}</p>
            </div>
        </div>
    @else
        <p class="text-sm text-white/80 leading-relaxed mb-4">
            {{ __('front.newsletter.subscribe_cta') }}
        </p>
        <form wire:submit="subscribe" class="flex flex-col gap-2 relative" x-data="{ subscribing: false }"
            x-on:submit="subscribing = true" x-on:livewire-upload-finish="subscribing = false"
            x-on:livewire-upload-error="subscribing = false">

            <div class="relative">
                <input wire:model="email" type="email" placeholder="{{ __('front.newsletter.enter_email') }}" required
                    class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-2.5 text-sm text-white placeholder:text-white/50 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-transparent transition-all disabled:opacity-50">
                @error('email') <span
                class="text-white text-xs mt-1 absolute -bottom-5 start-1 font-medium">{{ $message }}</span> @enderror
            </div>
            <button type="submit" wire:loading.attr="disabled"
                class="w-full bg-white text-espresso font-semibold text-sm px-4 py-2.5 rounded-xl hover:bg-white/90 active:scale-95 transition-all duration-200 mt-3 relative overflow-hidden flex items-center justify-center min-h-[40px]">

                <span wire:loading.remove>{{ __('front.newsletter.subscribe') }}</span>

                <span wire:loading class="flex items-center gap-2 text-espresso">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </span>
            </button>
        </form>
    @endif
</div>