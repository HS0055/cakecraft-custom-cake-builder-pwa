<div class="card-base p-8 sm:p-10 animate-fade-in">
    <div class="mb-8 text-center sm:text-start">
        <h1 class="font-display text-3xl font-bold text-foreground mb-2">{{ __('auth.login.welcome_back') }}</h1>
        <p class="text-foreground-muted text-sm">{{ __('auth.login.sign_in_subtitle') }}</p>
    </div>

    <form wire:submit="login" class="space-y-5 flex flex-col items-stretch">
        {{-- Email --}}
        <div>
            <label for="email"
                class="mb-1.5 block text-sm font-semibold text-foreground">{{ __('auth.login.email') }}</label>
            <input wire:model="email" id="email" type="email" autocomplete="email" autofocus
                placeholder="{{ __('auth.login.email_placeholder') }}" class="input-base" />
            @error('email')
                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
            <label for="password"
                class="mb-1.5 block text-sm font-semibold text-foreground">{{ __('auth.login.password') }}</label>
            <div class="relative">
                <input wire:model="password" id="password" :type="show ? 'text' : 'password'"
                    autocomplete="current-password" placeholder="••••••••"
                    class="input-base font-mono tracking-wider pe-10" />

                <button type="button" @click="show = !show"
                    class="absolute inset-y-0 end-0 flex items-center pe-3 text-foreground-muted hover:text-foreground focus:outline-none transition-colors">
                    {{-- Eye open --}}
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    {{-- Eye closed --}}
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs font-medium text-danger">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember --}}
        <div class="flex items-center gap-2 pt-1">
            <input wire:model="remember" id="remember" type="checkbox"
                class="h-4 w-4 shrink-0 rounded border-border text-primary focus:ring-ring focus:ring-offset-1 focus:ring-offset-background" />
            <label for="remember"
                class="text-sm font-medium text-foreground cursor-pointer select-none">{{ __('auth.login.remember_device') }}</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="btn-base w-full bg-primary text-primary-foreground hover:bg-primary-hover shadow-sm mt-3 flex"
            wire:loading.attr="disabled" wire:loading.class="opacity-75">

            <span wire:loading.remove class="flex items-center justify-center gap-2">
                {{ __('auth.login.sign_in') }}
                <svg class="w-4 h-4 translate-y-[0.5px] rtl:rotate-180" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </span>

            <span wire:loading class="flex items-center justify-center gap-2">
                <svg class="h-4 w-4 animate-spin opacity-80" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>

            </span>
        </button>
    </form>
</div>