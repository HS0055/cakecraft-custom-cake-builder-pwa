@props([
    'model',
    'label' => null,
    'accept' => 'image/*',
    'hint' => 'Click or drag to upload',
    'preview' => null,
    'removeSignal' => null,
])

<div x-data="{
    files: null,
    modelName: '{{ $model }}',
    removeSignal: '{{ $removeSignal ?? "" }}',
    preview: @js($preview),
    dragging: false,
    handleDrop(e) {
        e.preventDefault();
        this.dragging = false;
        if (e.dataTransfer.files.length > 0) {
            this.handleFiles(e.dataTransfer.files);
        }
    },
    handleChange(e) {
        if (e.target.files.length > 0) {
            this.handleFiles(e.target.files);
        }
    },
    handleFiles(fileList) {
        this.files = fileList;
        // The input change event is what triggers Livewire, so if we drop, we must manually trigger input
        if (this.$refs.input.files !== fileList) {
             this.$refs.input.files = fileList;
             this.$refs.input.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (this.removeSignal) {
            this.$wire.set(this.removeSignal, false);
        }
    },
    remove() {
        this.files = null;
        this.preview = null;
        this.$refs.input.value = null; // Clear input
        this.$wire.set(this.modelName, null); // Clear Livewire model
        if (this.removeSignal) {
            this.$wire.set(this.removeSignal, true);
        }
    },
    isImage(file) {
        return file.type.startsWith('image/') && file.type !== 'image/svg+xml';
    }
}"
     @dragover.prevent="dragging = true"
     @dragleave.prevent="dragging = false"
     @drop="handleDrop($event)"
     class="group relative w-full"
>
    <!-- Label -->
    @if($label)
        <label class="mb-1.5 block text-sm font-medium text-foreground">{{ $label }}</label>
    @endif

    <!-- Upload Area -->
    <div :class="{
            'border-accent bg-accent/5 ring-2 ring-accent/20': dragging,
            'border-border bg-surface-alt/30 hover:border-primary/50 hover:bg-surface-alt/60': !dragging && !files && !preview,
            'border-success/30 bg-success/5': files || preview
         }"
         class="relative flex min-h-[120px] cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed transition-all duration-200"
    >
        <!-- Loading Overlay -->
        <div wire:loading wire:target="{{ $model }}" class="absolute inset-0 z-20 flex items-center justify-center rounded-xl bg-surface/80 backdrop-blur-sm transition-opacity duration-200">
            <div class="flex flex-col items-center gap-2">
                <svg class="h-8 w-8 animate-spin text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="text-xs font-medium text-foreground-muted">UPLOADING...</div>
            </div>
        </div>

        <!-- Input (Hidden but Functional) -->
        <input x-ref="input"
               type="file"
               wire:model="{{ $model }}"
               accept="{{ $accept }}"
               class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
               @change="handleChange($event)"
        />

        <!-- Empty State -->
        <div x-show="!files && !preview" class="flex flex-col items-center gap-2 p-4 text-center transition-opacity duration-200">
            <div class="rounded-full bg-surface p-3 shadow-sm ring-1 ring-border/50 group-hover:scale-110 group-hover:ring-primary/20 transition-all duration-200">
                <svg class="h-6 w-6 text-foreground-muted group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
            </div>
            <div class="space-y-1">
                <p class="text-sm font-medium text-foreground">
                    <span class="text-primary hover:underline">Click or drag</span> to upload
                </p>
                <p class="text-xs text-foreground-subtle">{{ $hint }}</p>
            </div>
        </div>

        <!-- Selected State -->
        <div x-show="files || preview" class="relative flex h-full w-full flex-col items-center justify-center p-2" style="display: none;">
            <!-- Valid Image Preview (New Upload or Existing) -->
            <template x-if="(files && isImage(files[0])) || (!files && preview)">
                <div class="relative h-full w-full flex flex-col items-center justify-center">
                    <div class="relative h-24 w-full overflow-hidden rounded-lg border border-border/50 bg-checkered">
                         <!-- New Upload -->
                         <template x-if="files && isImage(files[0])">
                             <img :src="URL.createObjectURL(files[0])" class="h-full w-full object-contain" @load="URL.revokeObjectURL($el.src)" />
                         </template>
                         <!-- Existing Image -->
                         <template x-if="!files && preview">
                             <img :src="preview" class="h-full w-full object-contain" />
                         </template>
                    </div>
                </div>
            </template>
            
            <!-- File Icon (Non-Image / SVG) -->
            <template x-if="files && !isImage(files[0])">
                <div class="flex flex-col items-center gap-3 py-2">
                    <div class="rounded-lg bg-surface-alt p-3 shadow-inner text-foreground-muted ring-1 ring-border/50">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                             <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                </div>
            </template>

            <!-- File Metadata & Actions -->
            <div class="mt-2 flex w-full items-center justify-between gap-2 rounded-md bg-surface ps-3 pe-1 py-1.5 shadow-sm ring-1 ring-border relative z-[99999]">
            <div class="flex flex-1 flex-col truncate">
                    <template x-if="files">
                        <div>
                            <span class="truncate text-xs font-medium text-foreground" x-text="files[0].name"></span>
                            <span class="text-[10px] text-foreground-muted uppercase" x-text="files[0].name.split('.').pop()"></span>
                        </div>
                    </template>
                </div>
                
                <!-- Clear Button -->
                <button type="button" 
                        @click.prevent="remove()" 
                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded text-foreground-muted hover:bg-danger-bg hover:text-danger transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @error($model) 
        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-danger animate-slide-up">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
            {{ $message }}
        </p> 
    @enderror
</div>

<style>
/* Optional: Checkered background for transparent images */
.bg-checkered {
    background-color: white;
    background-image: 
        linear-gradient(45deg, #f0f0f0 25%, transparent 25%),
        linear-gradient(-45deg, #f0f0f0 25%, transparent 25%),
        linear-gradient(45deg, transparent 75%, #f0f0f0 75%),
        linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}
</style>
