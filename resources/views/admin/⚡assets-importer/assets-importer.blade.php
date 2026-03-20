<div class="animate-fade-in space-y-6" x-on:continue-import.window="$wire.processNextBatch()">
    <div class="flex items-center justify-between flex-wrap gap-4">
        <h1 class="font-display text-2xl font-semibold text-foreground">{{ __('admin.assets_importer.title') }}</h1>
        <div class="flex gap-2">
            <button wire:click="scan"
                class="btn-base border border-border bg-surface text-foreground hover:bg-surface-alt"
                wire:loading.attr="disabled" @if(!$baseFolder || $importing) disabled @endif>
                <span wire:loading.remove wire:target="scan">{{ __('admin.assets_importer.scan_assets') }}</span>
                <span wire:loading wire:target="scan"
                    class="flex items-center justify-center">{{ __('admin.assets_importer.scanning') }}</span>
            </button>
            <button wire:click="startImport" class="btn-base bg-primary text-primary-foreground hover:bg-primary-hover"
                wire:loading.attr="disabled" @if(!$scanned || $importing) disabled title="Scan first" @endif>
                <span wire:loading.remove
                    wire:target="startImport,processNextBatch">{{ __('admin.assets_importer.run_import') }}</span>
                <span wire:loading wire:target="startImport,processNextBatch"
                    class="flex items-center justify-center">{{ __('admin.assets_importer.importing') }}</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        {{-- Folder Selector --}}
        <div class="card-base p-5">
            <label for="baseFolder" class="block text-sm font-semibold text-foreground mb-2">
                <svg class="inline h-4 w-4 me-1 -mt-0.5 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                </svg>
                {{ __('admin.assets_importer.source_folder') }}
            </label>
            <div class="!flex items-center gap-2">
                <span class="text-sm text-foreground-muted font-mono whitespace-nowrap">public/</span>
                <input type="text" wire:model.live="baseFolder" id="baseFolder" placeholder="e.g. assets"
                    class="flex-1 max-w-sm rounded-xl border border-border bg-surface px-4 py-2.5 text-sm text-foreground shadow-sm placeholder:text-foreground-muted/50 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-mono">
            </div>
            <p class="mt-2 text-xs text-foreground-muted">
                {!! __('admin.assets_importer.source_folder_hint', ['public' => '<code class="bg-surface-alt px-1.5 py-0.5 rounded font-mono">public/</code>']) !!}
            </p>
        </div>

        {{-- Default Prices --}}
        <div class="card-base p-5">
            <h3 class="font-semibold text-foreground mb-4">{{ __('admin.assets_importer.default_prices') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3 gap-4">
                <div>
                    <label for="defaultShapePrice" class="block text-sm font-medium text-foreground-muted mb-1">
                        {{ __('admin.assets_importer.shape_base_price') }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-foreground-muted">$</span>
                        <input type="number" step="0.01" wire:model="defaultShapePrice" id="defaultShapePrice"
                            class="w-full ps-7 rounded-lg border border-border bg-surface px-4 py-2 text-sm text-foreground shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
                <div>
                    <label for="defaultFlavorPrice" class="block text-sm font-medium text-foreground-muted mb-1">
                        {{ __('admin.assets_importer.flavor_extra_price') }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-foreground-muted">$</span>
                        <input type="number" step="0.01" wire:model="defaultFlavorPrice" id="defaultFlavorPrice"
                            class="w-full ps-7 rounded-lg border border-border bg-surface px-4 py-2 text-sm text-foreground shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
                <div>
                    <label for="defaultToppingPrice" class="block text-sm font-medium text-foreground-muted mb-1">
                        {{ __('admin.assets_importer.topping_price') }}
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-foreground-muted">$</span>
                        <input type="number" step="0.01" wire:model="defaultToppingPrice" id="defaultToppingPrice"
                            class="w-full ps-7 rounded-lg border border-border bg-surface px-4 py-2 text-sm text-foreground shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    </div>
                </div>
            </div>
            <p class="mt-3 text-xs text-foreground-muted">
                {!! __('admin.assets_importer.default_prices_hint') !!}
            </p>
        </div>
    </div>

    {{-- Folder Structure Guide --}}
    <div class="card-base overflow-hidden" x-data="{ showGuide: false }">
        <button @click="showGuide = !showGuide"
            class="w-full flex items-center justify-between p-4 text-start hover:bg-surface-alt/50 transition-colors cursor-pointer">
            <div class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-accent/10 text-accent">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </span>
                <span
                    class="font-semibold text-sm text-foreground">{{ __('admin.assets_importer.folder_guide_title') }}</span>
            </div>
            <svg class="h-5 w-5 text-foreground-muted transition-transform duration-200"
                :class="showGuide && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>
        <div x-show="showGuide" x-collapse x-cloak>
            <div class="px-4 pb-5 border-t border-border/50">
                <div
                    class="mt-4 bg-surface-alt rounded-xl p-4 font-mono text-xs leading-relaxed text-foreground-muted overflow-x-auto">
                    <pre class="whitespace-pre">public/<span class="text-primary font-bold">your-folder</span>/
├── <span class="text-accent font-bold">shapes/</span>
│   ├── heart/
│   │   ├── thumbnail.png
│   │   ├── base.png
│   │   └── cut.png
│   ├── round/
│   ├── high-round/
│   ├── square/
│   ├── rectangular/
│   └── two-layer/
│
├── <span class="text-accent font-bold">flavors/</span>
│   ├── chocolate/
│   │   ├── thumbnail.png
│   │   └── shapes/
│   │       ├── heart/
│   │       │   ├── full.png
│   │       │   └── cut.png
│   │       ├── round/
│   │       └── ...
│   └── vanilla/
│
└── <span class="text-accent font-bold">toppings/</span>
    ├── baby/
    │   ├── <span class="text-warning">01</span>-heart-baby.png
    │   ├── <span class="text-warning">01</span>-round-baby.png
    │   ├── <span class="text-warning">01</span>-square-baby.png    ← <span class="text-success">same number = 1 topping</span>
    │   ├── <span class="text-warning">01</span>-rectangular-baby.png
    │   ├── <span class="text-warning">01</span>-high-round-baby.png
    │   ├── <span class="text-warning">01</span>-two-layer-baby.png
    │   ├── <span class="text-warning">02</span>-heart-baby.png    ← <span class="text-success">next topping</span>
    │   └── ...
    ├── teenager/
    └── flowers/</pre>
                </div>
                <div class="mt-3 space-y-1.5 text-xs text-foreground-muted">
                    <p><span
                            class="inline-flex items-center rounded-full bg-primary/10 text-primary px-2 py-0.5 font-semibold me-1">Shapes</span>
                        {{ __('admin.assets_importer.shapes_guide') }}</p>
                    <p><span
                            class="inline-flex items-center rounded-full bg-primary/10 text-primary px-2 py-0.5 font-semibold me-1">Flavors</span>
                        {!! __('admin.assets_importer.flavors_guide', ['shapes' => '<code class="bg-surface-alt px-1 rounded">shapes/</code>']) !!}
                    </p>
                    <p><span
                            class="inline-flex items-center rounded-full bg-primary/10 text-primary px-2 py-0.5 font-semibold me-1">Toppings</span>
                        {!! __('admin.assets_importer.toppings_guide') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Counters -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-base p-4 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold text-foreground">{{ $counts['shapes'] }}</span>
            <span
                class="text-sm uppercase tracking-wider text-foreground-muted">{{ __('admin.assets_importer.shapes_found') }}</span>
        </div>
        <div class="card-base p-4 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold text-foreground">{{ $counts['flavors'] }}</span>
            <span
                class="text-sm uppercase tracking-wider text-foreground-muted">{{ __('admin.assets_importer.flavors_found') }}</span>
        </div>
        <div class="card-base p-4 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold text-foreground">{{ $counts['toppings'] }}</span>
            <span
                class="text-sm uppercase tracking-wider text-foreground-muted">{{ __('admin.assets_importer.toppings_found') }}</span>
        </div>
    </div>

    <!-- Live Import Log -->
    <div class="card-base overflow-hidden flex flex-col h-[500px]">
        {{-- Log Header & Progress --}}
        <div class="p-4 border-b border-border bg-surface shrink-0 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="font-semibold text-foreground flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 0 1-2.25 2.25M16.5 7.5V18a2.25 2.25 0 0 0 2.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 0 0 2.25 2.25h13.5M6 7.5h3v3H6v-3Z" />
                        </svg>
                        {{ __('admin.assets_importer.import_log') }}
                    </h2>
                    @if($importing)
                        <span class="flex h-2.5 w-2.5 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
                        </span>
                    @endif
                </div>

                {{-- Status Pills --}}
                @if(!empty($results['created']) || !empty($results['skipped']) || !empty($results['errors']))
                    <div class="flex gap-2 text-xs font-semibold">
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-success-bg/50 border border-success/20 px-3 py-1 text-success">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            {{ count($results['created']) }} {{ __('admin.assets_importer.created') }}
                        </span>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full bg-warning-bg/50 border border-warning/20 px-3 py-1 text-warning">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.22 14.78a.75.75 0 001.06 0l7.22-7.22v5.69a.75.75 0 001.5 0v-7.5a.75.75 0 00-.75-.75h-7.5a.75.75 0 000 1.5h5.69l-7.22 7.22a.75.75 0 000 1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ count($results['skipped']) }} {{ __('admin.assets_importer.skipped') }}
                        </span>
                        @if(count($results['errors']) > 0)
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-danger-bg/50 border border-danger/20 px-3 py-1 text-danger">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                {{ count($results['errors']) }} {{ __('admin.assets_importer.errors') }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Progress Bar --}}
            @if($importing || $importQueueProcessed > 0)
                <div class="space-y-2">
                    <div class="flex justify-between text-xs font-semibold">
                        <span class="text-primary">{{ __('admin.assets_importer.importing') }}...</span>
                        <span class="text-foreground-muted">{{ $importQueueProcessed }} / {{ $importQueueTotal }}
                            {{ __('admin.assets_importer.folders') ?? 'Folders' }}</span>
                    </div>
                    <div class="w-full bg-surface-alt rounded-full h-2 shadow-inner overflow-hidden">
                        <div class="bg-primary h-2 rounded-full transition-all duration-300 ease-out"
                            style="width: {{ $importQueueTotal > 0 ? min(100, round(($importQueueProcessed / $importQueueTotal) * 100)) : 0 }}%">
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Terminal Output --}}
        <div class="p-0 overflow-hidden flex-1 bg-[#1A1B23]" x-data="{
                init() {
                    const el = this.$refs.logScroll;
                    const observer = new MutationObserver(() => {
                        el.scrollTop = el.scrollHeight;
                    });
                    observer.observe(el, { childList: true, subtree: true });
                }
            }">
            <div x-ref="logScroll"
                class="h-full overflow-y-auto p-5 font-mono text-[13px] leading-relaxed space-y-1.5 shadow-inner">
                <div id="importLog">
                    @if(!$importing && empty($results['created']) && empty($results['skipped']) && empty($results['errors']) && empty($results['info']))
                        <div class="text-white/30 italic flex items-center justify-center h-full mt-24">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-8 h-8 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.25 9.75 16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                                </svg>
                                <span>{{ __('admin.assets_importer.waiting') }}</span>
                            </div>
                        </div>
                    @else
                        @foreach($results['info'] as $msg)
                            <div class="text-[#89DDFF] flex gap-3">
                                <span class="text-white/30 select-none">[{{ now()->format('H:i:s') }}]</span>
                                <span class="uppercase w-12 shrink-0 font-bold opacity-80">INFO</span>
                                <span class="text-[#A6ACCD]">{{ $msg }}</span>
                            </div>
                        @endforeach
                        @foreach($results['created'] as $msg)
                            <div class="text-[#C3E88D] flex gap-3">
                                <span class="text-white/30 select-none">[{{ now()->format('H:i:s') }}]</span>
                                <span class="uppercase w-12 shrink-0 font-bold opacity-80">CREAT</span>
                                <span>{{ $msg }}</span>
                            </div>
                        @endforeach
                        @foreach($results['skipped'] as $msg)
                            <div class="text-[#FFCB6B] flex gap-3">
                                <span class="text-white/30 select-none">[{{ now()->format('H:i:s') }}]</span>
                                <span class="uppercase w-12 shrink-0 font-bold opacity-80">SKIP</span>
                                <span>{{ $msg }}</span>
                            </div>
                        @endforeach
                        @foreach($results['errors'] as $msg)
                            <div class="text-[#F07178] flex gap-3 bg-[#F07178]/10 rounded px-2 -mx-2 py-0.5">
                                <span class="text-white/30 select-none">[{{ now()->format('H:i:s') }}]</span>
                                <span class="uppercase w-12 shrink-0 font-bold opacity-90">ERROR</span>
                                <span>{{ $msg }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>