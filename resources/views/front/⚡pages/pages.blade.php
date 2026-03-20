<div>
    <section class=" pb-24 md:pb-40">

        {{-- Page Header --}}
        <div class="border-b border-gray-200">
            <div class="front-container py-6 md:py-8">
                <div class="flex items-center gap-2 text-sm text-gray-400 mb-2">
                    <a href="{{ route('front.home') }}" wire:navigate
                        class="hover:text-primary transition-colors">Home</a>
                    <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="text-gray-600 truncate max-w-[200px]">{{ $page->title }}</span>
                </div>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-gray-900">{{ $page->title }}</h1>
            </div>
        </div>

        <div class="front-container py-10 md:py-14">
            {{-- Main Content Container --}}
            <div class="px-4 md:px-0">
                <div class="prose prose-lg max-w-none text-gray-600 leading-[1.7] mx-auto">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </section>
</div>