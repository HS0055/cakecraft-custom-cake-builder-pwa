<div>
    <section class="min-h-[60vh]">

        {{-- Page Header --}}
        <div class="border-b border-gray-200">
            <div class="front-container py-6 md:py-8">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                    <a href="{{ route('front.home') }}" wire:navigate
                        class="hover:text-primary transition-colors">{{ __('front.breadcrumb.home') }}</a>
                    <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="text-gray-600">{{ __('front.breadcrumb.faqs') }}</span>
                </div>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ __('front.faqs.title') }}</h1>
            </div>
        </div>

        <div class="front-container py-10 md:py-14">
            {{-- FAQ Accordion List --}}
            <div class="space-y-4">
                @forelse($faqs as $faq)
                    <div x-data="{ expanded: false }"
                        class="group bg-white rounded-xl border border-gray-200 overflow-hidden transition-all duration-200"
                        :class="expanded ? 'border-primary/20 shadow-sm' : ''">

                        <button @click="expanded = !expanded"
                            class="w-full px-5 py-5 md:px-6 md:py-6 flex items-center justify-between text-start transition-colors cursor-pointer outline-none focus-visible:ring-2 focus-visible:ring-primary/30 rounded-xl">

                            <h3
                                class="font-display text-start md:text-lg font-semibold text-gray-900 pe-6 group-hover:text-primary transition-colors duration-150">
                                {{ $faq->question }}
                            </h3>

                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-all duration-200"
                                :class="{ 'bg-primary text-white': expanded, 'bg-gray-100 text-gray-400': !expanded }">
                                <svg :class="{ 'rotate-180': expanded }"
                                    class="w-4 h-4 transition-transform duration-200 transform" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <div x-show="expanded" x-collapse>
                            <div class="px-5 pb-5 pt-0 md:px-6 md:pb-6 text-gray-500 text-sm md:text-base leading-relaxed">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                @empty
                    <x-front.empty-state :title="__('front.faqs.empty_title')" :message="__('front.faqs.empty_message')" />
                @endforelse
            </div>
        </div>
    </section>
</div>