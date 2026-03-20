<div>
    @php
        $fulfillment = settings(\App\Settings\FulfillmentSettings::class);
        $payment = settings(\App\Settings\PaymentSettings::class);
    @endphp
    @if($orderPlaced)
        {{-- ═══ SUCCESS STATE ═══ --}}
        <div class="min-h-[60vh] flex items-center justify-center py-12 md:py-16">
            <div class="text-center max-w-lg mx-auto px-4 w-full animate-scale-in">

                <div class="rounded-xl border border-gray-200 p-8 md:p-12 relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-primary/10 flex items-center justify-center">
                            <svg class="w-10 h-10 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>

                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                            {{ __('front.checkout.order_placed') }}</h1>

                        <p class="text-gray-900 font-medium text-lg mb-2">
                            {{ __('front.checkout.thank_you', ['name' => $customer_name]) }}</p>

                        <div class="bg-primary/5 rounded-xl p-4 mb-6 border border-primary/10">
                            <p class="text-gray-500 text-sm leading-relaxed">
                                {!! __('front.checkout.order_received', ['id' => '<span class="font-mono font-bold text-primary text-base">#' . $orderId . '</span>']) !!}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-3 justify-center">
                            <x-front.btn href="{{ route('front.home') }}" variant="primary" size="md" wire:navigate
                                class="w-full sm:w-auto text-base">
                                {{ __('front.checkout.back_to_home') }}
                            </x-front.btn>
                            <x-front.btn href="{{ route('front.shop') }}" variant="secondary" size="md" wire:navigate
                                class="w-full sm:w-auto text-base">
                                {{ __('front.checkout.order_more') }}
                            </x-front.btn>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @else
            {{-- ═══ CHECKOUT FORM ═══ --}}
            <section class="border-b border-gray-200">
                <div class="front-container py-6 md:py-8">
                    <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                        <a href="{{ route('front.home') }}" wire:navigate
                            class="hover:text-primary transition-colors">{{ __('front.breadcrumb.home') }}</a>
                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                        <a href="{{ route('front.cart') }}" wire:navigate
                            class="hover:text-primary transition-colors">{{ __('front.breadcrumb.cart') }}</a>
                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                        <span class="text-gray-600">{{ __('front.breadcrumb.checkout') }}</span>
                    </div>
                    <h1 class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ __('front.checkout.checkout') }}
                    </h1>
                </div>
            </section>

            <div class="front-container py-8 md:py-12">
                @if(count($this->items))
                    <form x-data="stripePaymentLogic($wire.entangle('payment_method'))" @submit.prevent="submit"
                        @input.debounce.300ms="validateInputs" @change="validateInputs" id="checkout-form">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            {{-- Form Column --}}
                            <div class="lg:col-span-2 space-y-6">

                                {{-- Customer Info --}}
                                <div class="rounded-xl border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                                        <span
                                            class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">1</span>
                                        {{ __('front.checkout.your_information') }}
                                    </h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-900 mb-1.5">{{ __('front.checkout.full_name') }}</label>
                                            <input type="text" wire:model="customer_name" placeholder="John Doe"
                                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors">
                                            @error('customer_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-900 mb-1.5">{{ __('front.checkout.phone') }}</label>
                                            <input type="tel" wire:model="customer_phone" placeholder="+1 234 567 890"
                                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors">
                                            @error('customer_phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-gray-900 mb-1.5">{{ __('front.checkout.email') }}
                                                <span class="text-gray-400">{{ __('front.checkout.optional') }}</span></label>
                                            <input type="email" wire:model="customer_email" placeholder="you@example.com"
                                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors">
                                            @error('customer_email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- Fulfillment --}}
                                <div class="rounded-xl border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                                        <span
                                            class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">2</span>
                                        {{ __('front.checkout.fulfillment') }}
                                    </h3>

                                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                                        @if($fulfillment->enable_pickup)
                                            <label @class([
                                                'flex-1 flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition-all duration-200',
                                                'border-primary bg-primary/5' => $fulfillment_type === 'pickup',
                                                'border-gray-200 hover:border-primary/30' => $fulfillment_type !== 'pickup',
                                            ])>
                                                <input type="radio" wire:model.live="fulfillment_type" value="pickup" class="sr-only">
                                                <div @class([
                                                    'w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0',
                                                    'border-primary' => $fulfillment_type === 'pickup',
                                                    'border-gray-300' => $fulfillment_type !== 'pickup',
                                                ])>
                                                    @if($fulfillment_type === 'pickup')
                                                        <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-sm text-gray-900 flex items-center">
                                                        <svg class="w-4 h-4 me-1.5 text-primary shrink-0" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.38 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                                        </svg>
                                                        {{ __('front.checkout.pickup') }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">{{ __('front.checkout.collect_from_store') }}</p>
                                                </div>
                                            </label>
                                        @endif

                                        @if($fulfillment->enable_delivery)
                                            <label @class([
                                                'flex-1 flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition-all duration-200',
                                                'border-primary bg-primary/5' => $fulfillment_type === 'delivery',
                                                'border-gray-200 hover:border-primary/30' => $fulfillment_type !== 'delivery',
                                            ])>
                                                <input type="radio" wire:model.live="fulfillment_type" value="delivery" class="sr-only">
                                                <div @class([
                                                    'w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0',
                                                    'border-primary' => $fulfillment_type === 'delivery',
                                                    'border-gray-300' => $fulfillment_type !== 'delivery',
                                                ])>
                                                    @if($fulfillment_type === 'delivery')
                                                        <div class="w-2.5 h-2.5 rounded-full bg-primary"></div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-sm text-gray-900 flex items-center">
                                                        <svg class="w-4 h-4 me-1.5 text-primary shrink-0" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                                        </svg>
                                                        {{ __('front.checkout.delivery') }}
                                                    </p>
                                                    <p class="text-xs text-gray-400">{{ __('front.checkout.to_your_doorstep') }}</p>
                                                </div>
                                            </label>
                                        @endif
                                    </div>

                                    @if($fulfillment_type === 'delivery')
                                        <div class="animate-slide-up mt-4">
                                            <label
                                                class="block text-sm font-medium text-gray-900 mb-1.5">{{ __('front.checkout.delivery_address') }}</label>
                                            <textarea wire:model="address_text" rows="2"
                                                placeholder="{{ __('front.checkout.enter_delivery_address') }}"
                                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors resize-none"></textarea>
                                            @error('address_text') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    @endif

                                    <div class="mt-4" x-data="{ 
                                                        initDatepicker() {
                                                            if (typeof flatpickr !== 'undefined') {
                                                                this.setupFlatpickr();
                                                            } else {
                                                                const script = document.createElement('script');
                                                                script.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
                                                                script.onload = () => this.setupFlatpickr();
                                                                document.head.appendChild(script);

                                                                if (!document.getElementById('flatpickr-css')) {
                                                                    const link = document.createElement('link');
                                                                    link.id = 'flatpickr-css';
                                                                    link.rel = 'stylesheet';
                                                                    link.href = 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css';
                                                                    document.head.appendChild(link);
                                                                }
                                                            }
                                                        },
                                                        setupFlatpickr() {
                                                            flatpickr(this.$refs.dateInput, {
                                                                enableTime: true,
                                                                dateFormat: 'Y-m-d H:i',
                                                                minDate: 'today',
                                                                disableMobile: 'true', // force custom picker on mobile
                                                                onChange: (selectedDates, dateStr) => {
                                                                    @this.set('scheduled_at', dateStr);
                                                                }
                                                            });
                                                        }
                                                    }" x-init="initDatepicker()">

                                        <label
                                            class="block text-sm font-medium text-gray-900 mb-1.5">{{ __('front.checkout.schedule') }}</label>
                                        <div class="relative">
                                            <input type="text" x-ref="dateInput" wire:model.live="scheduled_at"
                                                placeholder="{{ __('front.checkout.schedule') }}..."
                                                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors ps-10">
                                            <div class="absolute inset-y-0 start-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                                </svg>
                                            </div>
                                        </div>
                                        @error('scheduled_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                {{-- Payment --}}
                                <div class="rounded-xl border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-5 flex items-center gap-2">
                                        <span
                                            class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">3</span>
                                        {{ __('front.checkout.payment_method') }}
                                    </h3>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        @if($payment->enable_cash)
                                            <label @class([
                                                'flex justify-center items-center gap-2 p-4 rounded-xl border cursor-pointer transition-all duration-200',
                                                'border-primary bg-primary/5' => $payment_method === 'cash',
                                                'border-gray-200 hover:border-primary/30' => $payment_method !== 'cash',
                                            ])>
                                                <input type="radio" wire:model.live="payment_method" name="payment_method" value="cash"
                                                    class="sr-only">
                                                <svg class="w-6 h-6 text-primary shrink-0" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m-1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span
                                                    class="font-semibold text-sm text-gray-900 ms-1">{{ __('front.checkout.cash') }}</span>
                                            </label>
                                        @endif

                                        @if($payment->enable_stripe)
                                            <label @class([
                                                'flex justify-center items-center gap-2 p-4 rounded-xl border cursor-pointer transition-all duration-200',
                                                'border-primary bg-primary/5' => $payment_method === 'card',
                                                'border-gray-200 hover:border-primary/30' => $payment_method !== 'card',
                                            ])>
                                                <input type="radio" wire:model.live="payment_method" name="payment_method" value="card"
                                                    class="sr-only">
                                                <img src="{{ asset('images/Stripe.png') }}" class="h-6 w-auto object-contain shrink-0"
                                                    alt="Stripe">
                                            </label>
                                        @endif

                                        @if($payment->enable_paypal)
                                            <label @class([
                                                'flex justify-center items-center gap-2 p-4 rounded-xl border cursor-pointer transition-all duration-200',
                                                'border-primary bg-primary/5' => $payment_method === 'online',
                                                'border-gray-200 hover:border-primary/30' => $payment_method !== 'online',
                                            ])>
                                                <input type="radio" wire:model.live="payment_method" name="payment_method"
                                                    value="online" class="sr-only">
                                                <img src="{{ asset('images/PayPal.png') }}" class="h-6 w-auto object-contain shrink-0"
                                                    alt="PayPal">
                                            </label>
                                        @endif
                                    </div>

                                    {{-- Stripe Element Container --}}
                                    @if($payment_method === 'card')
                                        <div class="mt-4 p-4 border border-gray-200 rounded-xl focus-within:border-primary transition-colors"
                                            wire:ignore>
                                            <label
                                                class="block text-sm font-medium text-gray-900 mb-3">{{ __('front.checkout.card_details') }}</label>
                                            <div x-init="mountStripe" id="card-element-container">
                                                <div x-ref="cardElement" class="w-full"></div>
                                                <div x-text="errorMessage" class="text-red-500 text-sm mt-2" x-show="errorMessage">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @error('payment_method') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
                                </div>

                                {{-- Notes & Attachments --}}
                                <div class="rounded-xl border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <span
                                            class="w-8 h-8 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center">4</span>
                                        {{ __('front.checkout.special_requests') }}
                                    </h3>
                                    <div class="space-y-4">
                                        <textarea wire:model="notes" rows="3"
                                            placeholder="{{ __('front.checkout.notes_placeholder') }}"
                                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm text-gray-900 placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/10 transition-colors resize-none"></textarea>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-900 mb-1.5">{{ __('front.checkout.attach_media') }}
                                                <span class="text-gray-400">{{ __('front.checkout.optional') }}</span></label>
                                            <div x-data="{ isUploading: false, progress: 0, isDropping: false }"
                                                x-on:livewire-upload-start="isUploading = true"
                                                x-on:livewire-upload-finish="isUploading = false"
                                                x-on:livewire-upload-error="isUploading = false"
                                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                                                x-on:dragover.prevent="isDropping = true"
                                                x-on:dragleave.prevent="isDropping = false"
                                                x-on:drop.prevent="isDropping = false; if ($event.dataTransfer.files.length > 0) { $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change', { bubbles: true })); }"
                                                x-on:click="$refs.fileInput.click()"
                                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border border-dashed rounded-xl relative transition-colors cursor-pointer group"
                                                :class="isDropping ? 'border-primary bg-primary/5' : 'border-gray-300 hover:bg-primary/5'">

                                                <div class="space-y-1 text-center w-full">
                                                    @if($media)
                                                        <div class="flex flex-col items-center">
                                                            <svg class="mx-auto h-10 w-10 text-primary mb-2" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <div
                                                                class="text-sm text-gray-900 font-medium truncate max-w-[200px] sm:max-w-xs px-2">
                                                                {{ $media->getClientOriginalName() }}</div>
                                                            <button type="button" wire:click.stop="$set('media', null)"
                                                                class="text-xs text-red-500 hover:text-red-700 mt-2 font-medium bg-red-50 hover:bg-red-100 transition-colors px-3 py-1 rounded-full relative z-10">{{ __('front.checkout.remove_file') }}</button>
                                                        </div>
                                                    @else
                                                        <svg class="mx-auto h-12 w-12 text-gray-300 group-hover:text-primary transition-colors"
                                                            stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <div
                                                            class="flex flex-col sm:flex-row items-center text-sm text-gray-400 justify-center mt-2 pointer-events-none">
                                                            <span
                                                                class="font-semibold text-primary">{{ __('front.checkout.upload_a_file') }}</span>
                                                            <p class="ps-1 hidden sm:block">{{ __('front.checkout.or_drag_drop') }}</p>
                                                        </div>
                                                        <input type="file" wire:model="media" x-ref="fileInput" accept="image/*,.pdf"
                                                            class="sr-only">
                                                        <p class="text-xs text-gray-400 mt-1 pointer-events-none">
                                                            {{ __('front.checkout.file_types') }}</p>

                                                        <div x-show="isUploading" x-transition
                                                            class="w-full max-w-[200px] mx-auto bg-gray-200 rounded-full h-1.5 mt-4 overflow-hidden">
                                                            <div class="bg-primary h-1.5 rounded-full transition-all duration-300"
                                                                :style="`width: ${progress}%`"></div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @error('media') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Order Summary Sidebar --}}
                            <div class="lg:col-span-1">
                                <div class="rounded-xl border border-gray-200 p-6 sticky top-24">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-5">{{ __('front.checkout.order_summary') }}
                                    </h3>

                                    {{-- Items --}}
                                    <div class="space-y-3 mb-5 max-h-64 overflow-y-auto custom-scrollbar">
                                        @foreach($this->items as $item)
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center text-sm flex-shrink-0 overflow-hidden relative border border-gray-100">
                                                    @php $visual = $this->getVisualDataForItem($item); @endphp
                                                    @if($visual && $visual['shape'])
                                                        <div class="absolute inset-0 p-1">
                                                            <x-cake-visual class="w-full h-full object-contain" :shape="$visual['shape']"
                                                                :color="$visual['color']" :flavorLayer="$visual['flavorLayer']"
                                                                :toppingLayers="$visual['toppingLayers']" mode="final" />
                                                        </div>
                                                    @elseif(!empty($item['image']))
                                                        <img src="{{ $item['image'] }}" alt=""
                                                            class="absolute inset-0 w-full h-full object-cover">
                                                    @else
                                                        <div class="absolute inset-0 flex items-center justify-center">
                                                            @if($item['type'] === 'custom')
                                                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="1.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                                                                </svg>
                                                            @else
                                                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor" stroke-width="1.5">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M21 7.5l-2.25-1.313M21 7.5v2.25m0-2.25l-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3l2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75l2.25-1.313M12 21.75V19.5m0 2.25l-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25" />
                                                                </svg>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                                                    <p class="text-xs text-gray-400">
                                                        {{ __('front.checkout.qty', ['qty' => $item['quantity']]) }}</p>
                                                </div>
                                                <x-front.price :amount="$item['price'] * $item['quantity']"
                                                    class="text-sm text-gray-900" />
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="border-t border-gray-200 pt-4 space-y-3">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">{{ __('front.checkout.subtotal') }}</span>
                                            <x-front.price :amount="$this->subtotal" />
                                        </div>

                                        @if($this->taxPercentage > 0)
                                            <div class="flex justify-between text-sm">
                                                <span
                                                    class="text-gray-400">{{ __('front.checkout.tax', ['percentage' => $this->taxPercentage]) }}</span>
                                                <x-front.price :amount="$this->taxAmount" />
                                            </div>
                                        @endif

                                        @if($this->deliveryFee > 0)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-400">{{ __('front.checkout.delivery_fee') }}</span>
                                                <x-front.price :amount="$this->deliveryFee" />
                                            </div>
                                        @endif
                                    </div>

                                    <div class="border-t border-gray-200 mt-4 pt-4">
                                        <div class="flex justify-between items-center">
                                            <span class="font-semibold text-gray-900">{{ __('front.checkout.total') }}</span>
                                            <x-front.price :amount="$this->total" class="text-2xl text-primary" />
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <button x-show="paymentMethod !== 'online'" type="submit" wire:loading.attr="disabled"
                                            :disabled="(paymentMethod === 'card' && !cardComplete) || isProcessing || !formValid"
                                            :class="{ 'opacity-50 cursor-not-allowed': (paymentMethod === 'card' && !cardComplete) || isProcessing || !formValid }"
                                            class="w-full inline-flex items-center justify-center gap-2 bg-primary text-white font-medium rounded-xl px-6 py-3.5 hover:opacity-90 active:scale-[0.97] transition-all duration-200 disabled:opacity-50 cursor-pointer"
                                            aria-label="Place your order">

                                            <span x-show="!isProcessing" class="flex items-center justify-center gap-2">
                                                {{ __('front.checkout.place_order') }}
                                                <svg class="w-4 h-4 rtl:rotate-180 translate-y-[0.5px]" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                                </svg>
                                            </span>

                                            <span x-show="isProcessing" style="display: none;"
                                                class="flex items-center justify-center gap-2">
                                                <svg class="animate-spin w-4 h-4 opacity-80" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                </svg>

                                            </span>
                                        </button>
                                        <p x-show="!formValid && paymentMethod !== 'online'"
                                            class="text-xs text-center text-red-500 mt-2 font-medium">
                                            {{ __('front.checkout.complete_details') }}</p>

                                        {{-- PayPal Button Container --}}
                                        <div x-show="paymentMethod === 'online'" class="w-full relative" wire:ignore>
                                            <div x-show="!formValid"
                                                class="absolute inset-0 bg-white/80 z-10 flex items-center justify-center cursor-not-allowed text-center p-4">
                                                <div
                                                    class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium border border-gray-200 shadow-sm">
                                                    {{ __('front.checkout.complete_to_pay') }}
                                                </div>
                                            </div>
                                            <div id="paypal-button-container" x-init="mountPaypal"
                                                class="w-full transition-opacity duration-200"
                                                :class="{ 'opacity-40 filter grayscale': !formValid }"></div>
                                        </div>
                                    </div>

                                    <a href="{{ route('front.cart') }}" wire:navigate
                                        class="block text-center text-sm text-gray-400 hover:text-primary transition-colors mt-4">
                                        {{ __('front.checkout.back_to_cart') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <x-front.empty-state :title="__('front.checkout.empty_title')" :message="__('front.checkout.empty_message')"
                        :actionLabel="__('front.checkout.shop_cakes')" actionHref="{{ route('front.shop') }}" />
                @endif
            </div>

        </div>
    @endif

{{-- Stripe & PayPal JS Logic --}}
@if($payment->enable_stripe || $payment->enable_paypal)
    @if($payment->enable_stripe)
        <script src="https://js.stripe.com/v3/"></script>
    @endif


    @if($payment->enable_paypal)
        <script
            src="https://www.paypal.com/sdk/js?client-id={{ $payment->paypal_client_id }}&currency={{ settings(\App\Settings\CurrencySettings::class)->currency_code ?? 'USD' }}"></script>
    @endif
@endif

{{-- Revised Script for Alpine + Window access for Submit Handler --}}
<script>
    // Define the logic function globally so it works across Livewire navigations
    window.stripePaymentLogic = function (paymentMethodEntangle) {
        return {
            paymentMethod: paymentMethodEntangle,
            errorMessage: '',
            isProcessing: false,
            cardComplete: false,
            formValid: false,

            init() {
                // Initial check
                this.mountStripe();
                this.mountPaypal();
                setTimeout(() => this.validateInputs(), 200);

                this.$watch('paymentMethod', value => {
                    if (value === 'card') {
                        setTimeout(() => this.mountStripe(), 100);
                    }
                    if (value === 'online') {
                        setTimeout(() => this.mountPaypal(), 100);
                        setTimeout(() => this.validateInputs(), 200);
                    }
                });

                window.addEventListener('stripe-requires-action', async (e) => {
                    this.isProcessing = true;
                    if (!window.stripe) return;

                    const clientSecret = e.detail.clientSecret;
                    const { paymentIntent, error } = await window.stripe.confirmCardPayment(clientSecret);

                    if (error) {
                        this.errorMessage = error.message;
                        this.isProcessing = false;
                    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                        this.$wire.finalizeStripeOrder(paymentIntent.id).then(() => {
                            this.isProcessing = false;
                        }).catch(() => {
                            this.isProcessing = false;
                        });
                    } else {
                        this.errorMessage = "Payment was not successful.";
                        this.isProcessing = false;
                    }
                });
            },

            validateInputs() {
                const form = document.getElementById('checkout-form');
                if (!form) return;

                const getVal = (selector) => form.querySelector(selector)?.value?.trim();
                const name = getVal('[wire\\:model="customer_name"]');
                const phone = getVal('[wire\\:model="customer_phone"]');

                const deliveryRadio = form.querySelector('input[value="delivery"]');
                const isDelivery = deliveryRadio && deliveryRadio.checked;

                let valid = !!name && !!phone;

                if (valid && isDelivery) {
                    const address = getVal('[wire\\:model="address_text"]');
                    valid = !!address;
                }

                this.formValid = valid;
            },

            mountStripe() {
                if (this.paymentMethod !== 'card') return;

                const stripeKey = "{{ config('services.stripe.key') ?? settings(\App\Settings\PaymentSettings::class)->stripe_public_key }}";
                if (!stripeKey) return;

                if (!window.stripe) {
                    try {
                        window.stripe = Stripe(stripeKey);
                        window.elements = window.stripe.elements();
                    } catch (e) {
                        console.error("Stripe initialization failed", e);
                        return;
                    }
                }

                // Check if the ref exists currently (it might be hidden)
                if (!this.$refs.cardElement) return;

                if (!window.card) {
                    window.card = window.elements.create('card', {
                        hidePostalCode: true,
                        style: {
                            base: {
                                color: '#32325d',
                                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                                fontSmoothing: 'antialiased',
                                fontSize: '16px',
                                '::placeholder': { color: '#aab7c4' }
                            },
                            invalid: { color: '#fa755a', iconColor: '#fa755a' }
                        }
                    });

                    // Mount to the x-ref
                    window.card.mount(this.$refs.cardElement);

                    window.card.on('change', (event) => {
                        this.errorMessage = event.error ? event.error.message : '';
                        this.cardComplete = event.complete;
                    });
                } else {
                    // Remount if needed
                    try {
                        window.card.mount(this.$refs.cardElement);
                    } catch (e) {
                        // Already mounted or invalid
                    }
                }
            },

            mountPaypal() {
                if (this.paymentMethod !== 'online') return;

                // Wait for PayPal SDK
                if (!window.paypal) {
                    setTimeout(() => this.mountPaypal(), 100);
                    return;
                }

                const container = document.getElementById('paypal-button-container');
                if (!container) return;

                // Avoid duplicate buttons
                if (container.innerHTML.trim().length > 0) return;

                window.paypal.Buttons({
                    fundingSource: window.paypal.FUNDING.PAYPAL,
                    style: {
                        layout: 'vertical',
                        color: 'gold',
                        shape: 'rect',
                        label: 'paypal'
                    },
                    onClick: async (data, actions) => {
                        this.validateInputs();
                        if (!this.formValid) return actions.reject();

                        const isValid = await this.$wire.validateForm();
                        if (!isValid) return actions.reject();

                        return actions.resolve();
                    },
                    createOrder: async (data, actions) => {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '{{ $this->total }}'
                                }
                            }]
                        });
                    },
                    onApprove: async (data, actions) => {
                        this.isProcessing = true;

                        return actions.order.capture().then((details) => {
                            this.$wire.placeOrder(JSON.stringify(details)).then(() => {
                                this.isProcessing = false;
                            });
                        });
                    },
                    onError: (err) => {
                        console.error('PayPal Error', err);
                        this.errorMessage = 'An error occurred with PayPal.';
                    }
                }).render('#paypal-button-container');
            },

            async submit() {
                if (this.isProcessing) return;
                this.isProcessing = true;

                const paymentMethod = await this.$wire.get('payment_method');

                if (paymentMethod === 'online') {
                    this.errorMessage = "Please click the PayPal button to proceed.";
                    this.isProcessing = false;
                    return;
                }

                if (paymentMethod === 'card') {
                    if (!window.stripe || !window.card) {
                        this.mountStripe();
                        if (!window.stripe || !window.card) {
                            alert('Payment system not ready. Please refresh.');
                            this.isProcessing = false;
                            return;
                        }
                    }

                    const { paymentMethod: stripePaymentMethod, error } = await window.stripe.createPaymentMethod({
                        type: 'card',
                        card: window.card,
                        billing_details: {
                            name: await this.$wire.get('customer_name'),
                            phone: await this.$wire.get('customer_phone'),
                            email: await this.$wire.get('customer_email')
                        }
                    });

                    if (error) {
                        this.errorMessage = error.message;
                        this.isProcessing = false;
                    } else {
                        this.$wire.placeOrder(stripePaymentMethod.id).then(() => {
                            this.isProcessing = false;
                        }).catch(() => {
                            this.isProcessing = false;
                        });
                    }
                } else {
                    // Cash or other methods
                    this.$wire.placeOrder().then(() => {
                        this.isProcessing = false;
                    }).catch(() => {
                        this.isProcessing = false;
                    });
                }
            }
        };
    }
</script>
</div>