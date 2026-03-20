<div class="animate-fade-in mx-auto max-w-4xl py-10 px-4 sm:px-6 lg:px-8">
    <div class="relative mb-12">
        <div class="relative z-10 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-display text-4xl font-bold tracking-tight text-foreground">
                    {{ __('admin.profile.title') }}
                </h2>
                <p class="mt-2 text-base text-foreground-muted max-w-xl">{{ __('admin.profile.subtitle') }}</p>
            </div>
            <div class="hidden sm:block">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-accent text-2xl font-bold text-white">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div wire:key="success-{{ str()->random(10) }}"
            class="mb-8 flex items-center gap-2 rounded-xl bg-success-bg px-4 py-3 text-sm text-success"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @error('system')
        <div wire:key="error-{{ str()->random(10) }}"
            class="mb-8 flex items-center gap-2 rounded-xl bg-danger-bg px-4 py-3 text-sm text-danger"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ $message }}
        </div>
    @enderror

    <form wire:submit="save" class="space-y-10">

        {{-- General Information Section --}}
        <div class="grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-3">
            <div class="px-4 sm:px-0">
                <h2 class="text-base font-semibold leading-7 text-foreground">{{ __('admin.profile.personal_info') }}
                </h2>
                <p class="mt-1 text-sm leading-6 text-foreground-muted">{{ __('admin.profile.personal_info_subtitle') }}
                </p>
            </div>

            <div class="card-base p-6 sm:p-8 lg:col-span-2 relative overflow-hidden group">
                <div
                    class="absolute top-0 end-0 -mt-16 -me-16 h-32 w-32 rounded-full bg-primary/5 opacity-0 transition-opacity duration-500 group-hover:opacity-100 blur-2xl">
                </div>

                <div class="space-y-6 relative z-10">
                    <div>
                        <label for="name"
                            class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.profile.name') }}</label>
                        <div class="mt-2">
                            <input wire:model="name" type="text" id="name"
                                class="input-base w-full transition-all focus:ring-2 focus:ring-primary/30"
                                placeholder="Jane Doe" />
                        </div>
                        @error('name') <p class="mt-2 text-xs font-medium text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email"
                            class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.profile.email_address') }}</label>
                        <div class="mt-2">
                            <input wire:model="email" type="email" id="email"
                                class="input-base w-full transition-all focus:ring-2 focus:ring-primary/30"
                                placeholder="jane@example.com" />
                        </div>
                        @error('email') <p class="mt-2 text-xs font-medium text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden sm:block">
            <div class="border-t border-border/60"></div>
        </div>

        {{-- Security Section --}}
        <div class="grid grid-cols-1 gap-x-8 gap-y-8 lg:grid-cols-3">
            <div class="px-4 sm:px-0">
                <h2 class="text-base font-semibold leading-7 text-foreground">{{ __('admin.profile.security') }}</h2>
                <p class="mt-1 text-sm leading-6 text-foreground-muted">{{ __('admin.profile.security_subtitle') }}</p>
            </div>

            <div class="card-base border-dashed border-border p-6 sm:p-8 lg:col-span-2 relative overflow-hidden group">
                <div
                    class="absolute bottom-0 start-0 -mb-16 -ms-16 h-40 w-40 rounded-full bg-accent/5 opacity-0 transition-opacity duration-500 group-hover:opacity-100 blur-3xl">
                </div>

                <div class="space-y-6 relative z-10">
                    <div>
                        <label for="password"
                            class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.profile.new_password') }}</label>
                        <div class="mt-2">
                            <input wire:model="password" type="password" id="password"
                                class="input-base w-full focus:border-accent focus:ring-accent/20"
                                placeholder="{{ __('admin.profile.new_password_placeholder') }}" />
                        </div>
                        @error('password') <p class="mt-2 text-xs font-medium text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.profile.confirm_password') }}</label>
                        <div class="mt-2">
                            <input wire:model="password_confirmation" type="password" id="password_confirmation"
                                class="input-base w-full focus:border-accent focus:ring-accent/20"
                                placeholder="{{ __('admin.profile.confirm_password_placeholder') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex items-center justify-end gap-x-6 border-t border-border pt-8">
            <a href="{{ route('admin.dashboard') }}"
                class="text-sm font-semibold leading-6 text-foreground hover:text-primary transition-colors">{{ __('admin.profile.cancel') }}</a>
            <button type="submit"
                class="inline-flex justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all active:scale-95"
                wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('admin.profile.save_changes') }}</span>
                <span wire:loading class="flex items-center justify-center">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>

                </span>
            </button>
        </div>

    </form>
</div>