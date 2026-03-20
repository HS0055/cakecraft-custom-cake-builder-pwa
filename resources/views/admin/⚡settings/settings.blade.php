<div class="animate-fade-in mx-auto max-w-6xl py-10 px-4 sm:px-6 lg:px-8"
    x-data="{ showSaveConfirm: false, showResetConfirm: false, resetTabName: '' }">
    <div class="relative mb-8 sm:mb-12">
        <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-4xl font-bold tracking-tight text-foreground">
                    {{ __('admin.settings.title') }}
                </h1>
                <p class="mt-2 text-base text-foreground-muted max-w-xl">{{ __('admin.settings.subtitle') }}</p>
            </div>

            <button @click="showSaveConfirm = true" wire:loading.attr="disabled"
                class="hidden sm:inline-flex justify-center rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primary-hover transition-colors">
                <span wire:loading.remove>{{ __('admin.settings.save_configurations') }}</span>
                <span wire:loading class="flex items-center justify-center">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    {{ __('admin.settings.saving_state') }}
                </span>
            </button>
        </div>
    </div>

    @if (session('success'))
        <div wire:key="success-{{ str()->random(10) }}"
            class="mb-4 flex items-center gap-2 rounded-xl bg-success-bg px-4 py-3 text-sm text-success"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-x-12 gap-y-12 lg:grid-cols-4 items-start">
        <!-- Navigation Tabs (Sidebar style on desktop) -->
        <aside class="sticky top-24 shrink-0 lg:col-span-1">
            <nav class="flex flex-col gap-1.5" aria-label="Settings navigation">
                @foreach(['general' => __('admin.settings.tab_general'), 'currency' => __('admin.settings.tab_currency'), 'order' => __('admin.settings.tab_orders'), 'fulfillment' => __('admin.settings.tab_fulfillment'), 'payment' => __('admin.settings.tab_payments'), 'branding' => __('admin.settings.tab_branding'), 'appearance' => __('admin.settings.tab_appearance'), 'social_media' => __('admin.settings.tab_social_media'), 'ready_cake' => __('admin.settings.tab_ready_cakes'), 'system' => __('admin.settings.tab_system')] as $key => $label)
                    <button wire:click="$set('activeTab', '{{ $key }}')"
                        class="group flex items-center justify-between rounded-xl px-4 py-3 text-sm font-medium transition-all duration-200 {{ $activeTab === $key ? 'bg-primary/10 text-primary ring-1 ring-inset ring-primary/20 shadow-sm' : 'text-foreground-muted hover:bg-surface-alt hover:text-foreground' }}">
                        <span>{{ $label }}</span>
                        @if($activeTab === $key)
                            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        @endif
                    </button>
                @endforeach
            </nav>
        </aside>

        <!-- Main Content Cards -->
        <div class="lg:col-span-3 space-y-10 pb-20">

            <!-- General Settings -->
            <div x-show="$wire.activeTab === 'general'" x-collapse>
                <div class="card-base p-6 sm:p-8 relative overflow-hidden group">
                    <div
                        class="absolute top-0 end-0 -mt-16 -me-16 h-32 w-32 rounded-full bg-primary-pale/30 opacity-0 transition-opacity duration-700 group-hover:opacity-100 blur-2xl">
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.store_identities') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'general'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6 relative z-10">
                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.store_name') }}</label>
                            <input wire:model="store_name" type="text"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20">
                            @error('store_name') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.store_email') }}</label>
                            <input wire:model="store_email" type="email"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20">
                            @error('store_email') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.store_phone') }}</label>
                            <input wire:model="store_phone" type="text"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20">
                            @error('store_phone') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.pagination_limit') }}</label>
                            <input wire:model="pagination_limit" type="number"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20">
                            @error('pagination_limit') <span
                                class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-span-1 md:col-span-2 space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.store_address') }}</label>
                            <textarea wire:model="store_address" rows="3"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20 resize-y"></textarea>
                            @error('store_address') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency Settings -->
            <div x-show="$wire.activeTab === 'currency'" x-collapse>
                <div class="card-base p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.regional_pricing') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'currency'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.currency_code') }}</label>
                            <input wire:model="currency_code" type="text"
                                class="input-base w-full uppercase focus:ring-2 focus:ring-primary/20"
                                placeholder="e.g. USD">
                            @error('currency_code') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.currency_symbol') }}</label>
                            <input wire:model="currency_symbol" type="text"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20">
                            @error('currency_symbol') <span
                                class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fulfillment Settings -->
            <div x-show="$wire.activeTab === 'fulfillment'" x-collapse>
                <div class="card-base p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.operations_handling') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'fulfillment'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label
                            class="flex items-center justify-between p-4 bg-surface-alt hover:bg-surface-hover/50 rounded-xl border border-border cursor-pointer transition-colors group">
                            <div>
                                <h3 class="font-medium text-foreground group-hover:text-primary transition-colors">
                                    {{ __('admin.settings.store_pickup') }}
                                </h3>
                                <p class="text-xs text-foreground-muted mt-0.5">
                                    {{ __('admin.settings.store_pickup_desc') }}
                                </p>
                            </div>
                            <input wire:model="enable_pickup" type="checkbox"
                                class="h-5 w-5 text-primary border-border rounded focus:ring-primary cursor-pointer">
                        </label>

                        <label
                            class="flex items-center justify-between p-4 bg-surface-alt hover:bg-surface-hover/50 rounded-xl border border-border cursor-pointer transition-colors group">
                            <div>
                                <h3 class="font-medium text-foreground group-hover:text-primary transition-colors">
                                    {{ __('admin.settings.local_delivery') }}
                                </h3>
                                <p class="text-xs text-foreground-muted mt-0.5">
                                    {{ __('admin.settings.local_delivery_desc') }}
                                </p>
                            </div>
                            <input wire:model="enable_delivery" type="checkbox"
                                class="h-5 w-5 text-primary border-border rounded focus:ring-primary cursor-pointer">
                        </label>
                    </div>

                    <div class="space-y-1.5 pt-4 border-t border-border">
                        <label
                            class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.preparation_queue') }}</label>
                        <input wire:model="default_preparation_time" type="number"
                            class="input-base w-full focus:ring-2 focus:ring-primary/20">
                        @error('default_preparation_time') <span
                        class="text-danger text-xs font-medium">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Payment Settings -->
            <div x-show="$wire.activeTab === 'payment'" x-collapse class="space-y-6 relative">
                <div class="flex items-center justify-end absolute -top-12 end-0 z-10 w-full mb-2">
                    <button type="button" @click="resetTabName = 'payment'; showResetConfirm = true"
                        class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        {{ __('admin.settings.restore_payment_defaults') }}
                    </button>
                </div>

                <!-- Stripe -->
                <div
                    class="card-base p-6 sm:p-8 border-l-4 border-transparent hover:border-l-primary/50 transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-alt">
                                <img src="/images/Stripe.png" alt="Stripe" class="h-6 object-contain">
                            </div>
                            <div>
                                <h3 class=" text-base font-semibold text-foreground">
                                    {{ __('admin.settings.stripe_integration') }}
                                </h3>
                                <p class="text-xs text-foreground-muted">{{ __('admin.settings.stripe_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input wire:model.live="enable_stripe" type="checkbox" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-surface-alt peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary  rtl:peer-checked:after:-translate-x-full">
                            </div>
                        </label>
                    </div>

                    @if($enable_stripe)
                        <div class="grid grid-cols-1 gap-5 mt-6 border-t border-border pt-6 animate-fade-in-down">
                            <div class="space-y-1.5">
                                <label
                                    class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.publishable_key') }}</label>
                                <input wire:model="stripe_public_key" type="text"
                                    class="input-base w-full font-mono text-xs focus:ring-2 focus:ring-primary/20">
                                @error('stripe_public_key') <span
                                class="text-danger text-xs font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label
                                    class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.secret_key') }}</label>
                                <input wire:model="stripe_secret_key" type="password"
                                    class="input-base w-full font-mono text-xs focus:ring-2 focus:ring-primary/20">
                                @error('stripe_secret_key') <span
                                class="text-danger text-xs font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <!-- PayPal -->
                <div
                    class="card-base p-6 sm:p-8 border-l-4 border-transparent hover:border-l-[#003087]/50 transition-colors">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-surface-alt">
                                <img src="/images/PayPal.png" alt="PayPal" class="h-6 object-contain">
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-foreground">
                                    {{ __('admin.settings.paypal_hub') }}
                                </h3>
                                <p class="text-xs text-foreground-muted">{{ __('admin.settings.paypal_desc') }}</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input wire:model.live="enable_paypal" type="checkbox" class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-surface-alt peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#003087]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003087]  rtl:peer-checked:after:-translate-x-full">
                            </div>
                        </label>
                    </div>

                    @if($enable_paypal)
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6 border-t border-border pt-6 animate-fade-in-down">
                            <div class="space-y-1.5 md:col-span-2">
                                <label
                                    class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.client_id') }}</label>
                                <input wire:model="paypal_client_id" type="text"
                                    class="input-base w-full font-mono text-xs focus:ring-2 focus:ring-[#003087]/20">
                                @error('paypal_client_id') <span
                                class="text-danger text-xs font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label
                                    class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.secret_hash') }}</label>
                                <input wire:model="paypal_secret" type="password"
                                    class="input-base w-full font-mono text-xs focus:ring-2 focus:ring-[#003087]/20">
                                @error('paypal_secret') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="space-y-1.5 md:col-span-2">
                                <label
                                    class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.operation_environment') }}</label>
                                <select wire:model="paypal_mode"
                                    class="input-base w-full focus:ring-2 focus:ring-[#003087]/20 bg-surface">
                                    <option value="sandbox">{{ __('admin.settings.sandbox') }}</option>
                                    <option value="live">{{ __('admin.settings.live') }}</option>
                                </select>
                                @error('paypal_mode') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Cash -->
                <div class="card-base p-6 sm:p-8 flex items-center justify-between group cursor-pointer transition-colors hover:border-border-muted"
                    @click="$wire.set('enable_cash', !$wire.enable_cash)">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-foreground">{{ __('admin.settings.cash_handling') }}
                            </h3>
                            <p class="text-xs text-foreground-muted">{{ __('admin.settings.cash_desc') }}</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer pointer-events-none">
                        <input wire:model="enable_cash" type="checkbox" class="sr-only peer">
                        <div
                            class="w-11 h-6 bg-surface-alt peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-500/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500  rtl:peer-checked:after:-translate-x-full">
                        </div>
                    </label>
                </div>
            </div>
            <!-- Branding Settings -->
            <div x-show="$wire.activeTab === 'branding'" x-collapse>
                <div class="card-base p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.store_branding_assets') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'branding'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Logo Upload -->
                        <div
                            class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border bg-surface-alt/50 p-8 transition-all hover:bg-surface-alt hover:border-primary/50 text-center">
                            @if ($logo)
                                <div
                                    class="relative w-full max-w-[200px] aspect-video mb-4 rounded-xl overflow-hidden bg-white/50 backdrop-blur-sm border border-border p-2">
                                    <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-contain">
                                </div>
                            @elseif ($logo_url)
                                <div
                                    class="relative w-full max-w-[200px] aspect-video mb-4 rounded-xl overflow-hidden bg-white/50 backdrop-blur-sm border border-border p-2">
                                    <img src="{{ $logo_url }}" class="w-full h-full object-contain">
                                </div>
                            @else
                                <div
                                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-surface shadow-sm ring-1 ring-border text-foreground-muted group-hover:text-primary transition-colors">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            @endif

                            <div class="space-y-1">
                                <h3 class="text-sm font-semibold text-foreground">
                                    {{ __('admin.settings.primary_store_logo') }}
                                </h3>
                                <p class="text-xs text-foreground-muted max-w-[200px] mx-auto">
                                    {{ __('admin.settings.logo_hint') }}
                                </p>
                            </div>

                            <div class="mt-4 border-t border-border pt-4 w-full flex justify-center">
                                <input type="file" wire:model="logo" id="logo_upload" class="hidden" accept="image/*">
                                <label for="logo_upload"
                                    class="inline-flex items-center gap-2 rounded-lg bg-surface px-4 py-2 text-sm font-medium text-foreground shadow-sm ring-1 ring-inset ring-border hover:bg-surface-hover cursor-pointer transition-colors">
                                    <svg class="w-4 h-4 text-foreground-muted" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                    {{ __('admin.settings.browse_files') }}
                                </label>
                            </div>
                            @error('logo') <span
                                class="absolute bottom-2 text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Favicon Upload -->
                        <div
                            class="group relative flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border bg-surface-alt/50 p-8 transition-all hover:bg-surface-alt hover:border-primary/50 text-center">
                            @if ($favicon)
                                <div
                                    class="relative h-16 w-16 mb-4 rounded-xl overflow-hidden bg-white/50 backdrop-blur-sm border border-border p-2 shadow-sm">
                                    <img src="{{ $favicon->temporaryUrl() }}" class="w-full h-full object-contain">
                                </div>
                            @elseif ($favicon_url)
                                <div
                                    class="relative h-16 w-16 mb-4 rounded-xl overflow-hidden bg-white/50 backdrop-blur-sm border border-border p-2 shadow-sm">
                                    <img src="{{ $favicon_url }}" class="w-full h-full object-contain">
                                </div>
                            @else
                                <div
                                    class="mb-4 flex h-16 w-16 items-center justify-center rounded-xl bg-surface shadow-sm ring-1 ring-border text-foreground-muted group-hover:text-primary transition-colors">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="space-y-1">
                                <h3 class="text-sm font-semibold text-foreground">
                                    {{ __('admin.settings.browser_favicon') }}
                                </h3>
                                <p class="text-xs text-foreground-muted max-w-[200px] mx-auto">
                                    {{ __('admin.settings.favicon_hint') }}
                                </p>
                            </div>

                            <div class="mt-4 border-t border-border pt-4 w-full flex justify-center">
                                <input type="file" wire:model="favicon" id="favicon_upload" class="hidden"
                                    accept=".ico,image/png">
                                <label for="favicon_upload"
                                    class="inline-flex items-center gap-2 rounded-lg bg-surface px-4 py-2 text-sm font-medium text-foreground shadow-sm ring-1 ring-inset ring-border hover:bg-surface-hover cursor-pointer transition-colors">
                                    <svg class="w-4 h-4 text-foreground-muted" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                    {{ __('admin.settings.browse_files') }}
                                </label>
                            </div>
                            @error('favicon') <span
                                class="absolute bottom-2 text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appearance Settings -->
            <div x-show="$wire.activeTab === 'appearance'" x-collapse>
                <div class="card-base p-6 sm:p-8 space-y-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.store_colors') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'appearance'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                            {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.primary_color') }}</label>
                            <p class="text-xs text-foreground-muted mt-1 mb-3">
                                {{ __('admin.settings.primary_color_desc') }}
                            </p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="relative h-12 w-20 overflow-hidden rounded-lg border border-border shadow-sm">
                                    <input wire:model.live="primary_color" type="color"
                                        class="absolute -top-2 -start-2 h-16 w-24 cursor-pointer border-0 p-0">
                                </div>
                                <div class="relative flex-1">
                                    <span
                                        class="absolute start-3 top-1/2 -translate-y-1/2 text-foreground-muted font-mono text-xs">#</span>
                                    <input wire:model.live="primary_color" type="text"
                                        class="input-base w-full uppercase font-mono ps-7 focus:ring-2 focus:ring-primary/20"
                                        maxlength="7">
                                </div>
                            </div>
                            @error('primary_color') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.sidebar_color') }}</label>
                            <p class="text-xs text-foreground-muted mt-1 mb-3">
                                {{ __('admin.settings.sidebar_color_desc') }}
                            </p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="relative h-12 w-20 overflow-hidden rounded-lg border border-border shadow-sm">
                                    <input wire:model.live="admin_sidebar_color" type="color"
                                        class="absolute -top-2 -start-2 h-16 w-24 cursor-pointer border-0 p-0">
                                </div>
                                <div class="relative flex-1">
                                    <span
                                        class="absolute start-3 top-1/2 -translate-y-1/2 text-foreground-muted font-mono text-xs">#</span>
                                    <input wire:model.live="admin_sidebar_color" type="text"
                                        class="input-base w-full uppercase font-mono ps-7 focus:ring-2 focus:ring-primary/20"
                                        maxlength="7">
                                </div>
                            </div>
                            @error('admin_sidebar_color') <span
                            class="text-danger text-xs font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Live Preview Box -->
                    <div class="mt-8 rounded-xl border border-border bg-surface-alt p-6" x-data="{
                            previewPrimary: @entangle('primary_color').live,
                            previewSidebar: @entangle('admin_sidebar_color').live
                         }">
                        <h3 class="text-sm font-semibold text-foreground mb-4">{{ __('admin.settings.live_preview') }}
                        </h3>
                        <div class="rounded-lg border border-border shadow-sm flex overflow-hidden aspect-[2/1] max-w-2xl mx-auto bg-white"
                            style="height: 300px;">
                            <!-- Mock Sidebar -->
                            <div class="w-48 shrink-0 flex flex-col transition-colors duration-200"
                                :style="`background-color: ${previewSidebar}`">
                                <div class="h-12 border-b border-white/10 flex items-center px-4">
                                    <div class="w-8 h-8 rounded bg-white/20"></div>
                                    <div class="ms-3 h-3 w-20 bg-white/20 rounded"></div>
                                </div>
                                <div class="flex-1 p-3 space-y-2">
                                    <div class="h-8 rounded px-3 flex items-center bg-white/10"
                                        :style="`color: ${previewPrimary}`">
                                        <div class="w-4 h-4 rounded-sm bg-current opacity-80"></div>
                                        <div class="ms-3 h-2 w-16 bg-current opacity-80 rounded"></div>
                                    </div>
                                    <div class="h-8 rounded px-3 flex items-center text-white/50">
                                        <div class="w-4 h-4 rounded-sm bg-current"></div>
                                        <div class="ms-3 h-2 w-12 bg-current rounded"></div>
                                    </div>
                                    <div class="h-8 rounded px-3 flex items-center text-white/50">
                                        <div class="w-4 h-4 rounded-sm bg-current"></div>
                                        <div class="ms-3 h-2 w-14 bg-current rounded"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Mock Content -->
                            <div class="flex-1 bg-gray-50 p-6 flex flex-col">
                                <div class="h-6 w-32 bg-gray-200 rounded mb-6"></div>
                                <div
                                    class="flex-1 bg-white rounded-lg border border-gray-200 shadow-sm p-4 flex flex-col items-center justify-center">
                                    <div class="h-32 w-32 rounded-full mb-4 opacity-20 transition-colors duration-200"
                                        :style="`background-color: ${previewPrimary}`"></div>
                                    <button
                                        class="px-6 py-2 rounded-lg text-white text-sm font-medium transition-colors duration-200 shadow-sm"
                                        :style="`background-color: ${previewPrimary}`">
                                        {{ __('admin.settings.primary_action') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media Settings -->
            <div x-show="$wire.activeTab === 'social_media'" x-collapse>
                <div class="card-base p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.social_media_profiles') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'social_media'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                           {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>

                    <div
                        class="grid relative border border-border border-b-0 rounded-xl overflow-hidden divide-y divide-border">
                        <div
                            class="flex md:items-center flex-col md:flex-row p-4 md:p-5 hover:bg-surface-alt/50 transition-colors">
                            <div class="w-full md:w-1/4 flex items-center gap-3 mb-3 md:mb-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-[#1877F2]/10 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.469h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.469h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                    </svg>
                                </div>
                                <span
                                    class="font-medium text-foreground text-sm">{{ __('admin.settings.facebook_url') }}</span>
                            </div>
                            <div class="flex-1 w-full">
                                <input wire:model="facebook_url" type="url" placeholder="https://facebook.com/yourstore"
                                    class="input-base w-full focus:ring-2 focus:ring-[#1877F2]/20 border-transparent bg-surface hover:bg-surface focus:bg-surface focus:border-border transition-all">
                                @error('facebook_url') <span
                                    class="text-danger text-xs mt-1 absolute font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="flex md:items-center flex-col md:flex-row p-4 md:p-5 hover:bg-surface-alt/50 transition-colors">
                            <div class="w-full md:w-1/4 flex items-center gap-3 mb-3 md:mb-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-[#E1306C]/10 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-[#E1306C]" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.88z" />
                                    </svg>
                                </div>
                                <span
                                    class="font-medium text-foreground text-sm">{{ __('admin.settings.instagram_url') }}</span>
                            </div>
                            <div class="flex-1 w-full">
                                <input wire:model="instagram_url" type="url"
                                    placeholder="https://instagram.com/yourstore"
                                    class="input-base w-full focus:ring-2 focus:ring-[#E1306C]/20 border-transparent bg-surface hover:bg-surface focus:bg-surface focus:border-border transition-all">
                                @error('instagram_url') <span
                                    class="text-danger text-xs mt-1 absolute font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="flex md:items-center flex-col md:flex-row p-4 md:p-5 hover:bg-surface-alt/50 transition-colors">
                            <div class="w-full md:w-1/4 flex items-center gap-3 mb-3 md:mb-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-foreground/5 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-foreground" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                    </svg>
                                </div>
                                <span
                                    class="font-medium text-foreground text-sm">{{ __('admin.settings.twitter_url') }}</span>
                            </div>
                            <div class="flex-1 w-full">
                                <input wire:model="twitter_url" type="url" placeholder="https://x.com/yourstore"
                                    class="input-base w-full focus:ring-2 focus:ring-foreground/20 border-transparent bg-surface hover:bg-surface focus:bg-surface focus:border-border transition-all">
                                @error('twitter_url') <span
                                    class="text-danger text-xs mt-1 absolute font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="flex md:items-center flex-col md:flex-row p-4 md:p-5 hover:bg-surface-alt/50 transition-colors">
                            <div class="w-full md:w-1/4 flex items-center gap-3 mb-3 md:mb-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-foreground/5 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-foreground" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 2.78-1.15 5.54-3.33 7.31-1.92 1.57-4.58 2.07-6.93 1.53-2.14-.49-4.04-1.78-5.13-3.7-1.31-2.28-1.2-5.18.23-7.39 1.25-1.95 3.32-3.19 5.58-3.44.02 1.45.03 2.89.01 4.34-.84.22-1.74.52-2.31 1.24-.62.8-.75 1.9-.4 2.88.38 1.05 1.34 1.76 2.45 1.88 1.53.16 3.05-.62 3.65-2.05.3-2.11.23-4.27.24-6.39.01-4.24.01-8.48.01-12.72L12.525.02z" />
                                    </svg>
                                </div>
                                <span
                                    class="font-medium text-foreground text-sm">{{ __('admin.settings.tiktok_url') }}</span>
                            </div>
                            <div class="flex-1 w-full">
                                <input wire:model="tiktok_url" type="url" placeholder="https://tiktok.com/@yourstore"
                                    class="input-base w-full focus:ring-2 focus:ring-foreground/20 border-transparent bg-surface hover:bg-surface focus:bg-surface focus:border-border transition-all">
                                @error('tiktok_url') <span
                                    class="text-danger text-xs mt-1 absolute font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div
                            class="flex md:items-center flex-col md:flex-row p-4 md:p-5 hover:bg-surface-alt/50 transition-colors border-b border-border">
                            <div class="w-full md:w-1/4 flex items-center gap-3 mb-3 md:mb-0">
                                <div
                                    class="w-8 h-8 rounded-full bg-[#25D366]/10 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                    </svg>
                                </div>
                                <span
                                    class="font-medium text-foreground text-sm">{{ __('admin.settings.whatsapp_number') }}</span>
                            </div>
                            <div class="flex-1 w-full relative">
                                <span
                                    class="absolute start-3 top-1/2 -translate-y-1/2 text-foreground-muted font-mono text-sm pointer-events-none">+</span>
                                <input wire:model="whatsapp_number" type="tel" placeholder="1234567890"
                                    class="input-base w-full ps-7 focus:ring-2 focus:ring-[#25D366]/20 border-transparent bg-surface hover:bg-surface focus:bg-surface focus:border-border transition-all">
                                @error('whatsapp_number') <span
                                    class="text-danger text-xs mt-1 absolute font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Settings -->
            <div x-show="$wire.activeTab === 'order'" x-collapse>
                <div class="card-base p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.tax_fees') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'order'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                           {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.tax_percentage') }}</label>
                            <input wire:model="tax_percentage" type="number" step="0.01"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20">
                            @error('tax_percentage') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label
                                class="block text-sm font-medium leading-6 text-foreground">{{ __('admin.settings.delivery_fee', ['symbol' => settings(\App\Settings\CurrencySettings::class)->currency_symbol]) }}</label>
                            <input wire:model="delivery_fee" type="number" step="0.01"
                                class="input-base w-full focus:ring-2 focus:ring-primary/20">
                            @error('delivery_fee') <span class="text-danger text-xs font-medium">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                </div>
            </div>

            <!-- Ready Cake Settings -->
            <div x-show="$wire.activeTab === 'ready_cake'" x-collapse>
                <div class="card-base p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.ready_cake_defaults') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'ready_cake'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                           {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label
                            class="flex items-center justify-between p-4 bg-surface-alt hover:bg-surface-hover/50 rounded-xl border border-border cursor-pointer transition-colors group">
                            <div>
                                <h3 class="font-medium text-foreground group-hover:text-primary transition-colors">
                                    {{ __('admin.settings.default_active_status') }}
                                </h3>
                                <p class="text-xs text-foreground-muted mt-0.5">
                                    {{ __('admin.settings.default_active_desc') }}
                                </p>
                            </div>
                            <input wire:model="ready_cake_default_is_active" type="checkbox"
                                class="h-5 w-5 text-primary border-border rounded focus:ring-primary cursor-pointer">
                        </label>

                        <label
                            class="flex items-center justify-between p-4 bg-surface-alt hover:bg-surface-hover/50 rounded-xl border border-border cursor-pointer transition-colors group">
                            <div>
                                <h3 class="font-medium text-foreground group-hover:text-primary transition-colors">
                                    {{ __('admin.settings.default_customizable') }}
                                </h3>
                                <p class="text-xs text-foreground-muted mt-0.5">
                                    {{ __('admin.settings.default_customizable_desc') }}
                                </p>
                            </div>
                            <input wire:model="ready_cake_default_is_customizable" type="checkbox"
                                class="h-5 w-5 text-primary border-border rounded focus:ring-primary cursor-pointer">
                        </label>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div x-show="$wire.activeTab === 'system'" x-collapse>
                <div class="card-base p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-semibold leading-7 text-foreground">
                            {{ __('admin.settings.emergency_controls') }}
                        </h2>
                        <button type="button" @click="resetTabName = 'system'; showResetConfirm = true"
                            class="text-xs font-medium text-foreground-muted hover:text-primary transition-colors flex items-center gap-1.5 bg-surface-alt px-2.5 py-1.5 rounded-lg border border-border hover:border-primary/30">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                           {{ __('admin.settings.restore_defaults') }}
                        </button>
                    </div>

                    <div
                        class="flex items-center justify-between p-6 bg-danger/5 border border-danger/20 rounded-xl relative overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-danger/[0.02] opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                        <div class="relative z-10 w-full flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-danger flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    {{ __('admin.settings.maintenance_mode') }}
                                </h3>
                                <p class="text-sm text-danger/80 mt-1 max-w-md">
                                    {{ __('admin.settings.maintenance_desc') }}
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="maintenance_mode" type="checkbox" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-surface-alt peer-focus:outline-none ring-1 ring-border rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-border after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-danger peer-checked:ring-danger  rtl:peer-checked:after:-translate-x-full">
                                </div>
                            </label>
                        </div>
                    </div>

                    <div
                        class="mt-6 flex items-start gap-3 bg-surface-alt/50 p-4 rounded-xl text-sm border border-border">
                        <svg class="w-5 h-5 text-foreground-muted shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-foreground-muted space-y-1">
                            <p><strong
                                    class="text-foreground font-medium">{{ __('admin.settings.system_core') }}</strong>
                                {{ __('admin.settings.system_core_info') }}</p>
                            <p>{{ __('admin.settings.system_diagnostics') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Save Confirmation Modal --}}
    <template x-if="showSaveConfirm">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" x-transition.opacity
                @click="showSaveConfirm = false"></div>
            <div class="relative w-full max-w-sm rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10 text-primary mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="text-center font-display text-lg font-semibold text-foreground">
                    {{ __('admin.settings.save_confirm_title') }}
                </h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">
                    {{ __('admin.settings.save_confirm_message') }}
                </p>
                <div class="mt-6 flex gap-3">
                    <button @click="showSaveConfirm = false"
                        class="btn-base flex-1 border border-border bg-surface text-foreground hover:bg-surface-alt">
                        {{ __('admin.settings.cancel') }}
                    </button>
                    <button @click="showSaveConfirm = false; $wire.save()"
                        class="btn-base flex-1 bg-primary text-primary-foreground hover:bg-primary-hover shadow-sm">
                        {{ __('admin.settings.save_changes') }}
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Reset Confirmation Modal --}}
    <template x-if="showResetConfirm">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-espresso/40 backdrop-blur-sm" x-transition.opacity
                @click="showResetConfirm = false"></div>
            <div class="relative w-full max-w-sm rounded-2xl bg-surface p-6 shadow-modal animate-scale-in">
                <div
                    class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-danger/10 text-danger mx-auto">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-center font-display text-lg font-semibold text-foreground">
                    {{ __('admin.settings.reset_confirm_title') }}
                </h3>
                <p class="mt-2 text-center text-sm text-foreground-muted">
                    {{ __('admin.settings.reset_confirm_message') }}
                </p>
                <div class="mt-6 flex gap-3">
                    <button @click="showResetConfirm = false"
                        class="btn-base flex-1 border border-border bg-surface text-foreground hover:bg-surface-alt">
                        {{ __('admin.settings.cancel') }}
                    </button>
                    <button @click="showResetConfirm = false; $wire.resetTab(resetTabName)"
                        class="btn-base flex-1 bg-danger text-white hover:bg-danger-hover shadow-sm">
                        {{ __('admin.settings.restore_defaults') }}
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>