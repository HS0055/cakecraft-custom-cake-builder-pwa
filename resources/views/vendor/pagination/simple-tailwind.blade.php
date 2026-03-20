@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex gap-2 items-center justify-between">

        @if ($paginator->onFirstPage())
            <span
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-foreground-muted bg-surface border border-border cursor-default leading-5 rounded-md">
                {!! __('pagination.previous') !!}
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-foreground-muted bg-surface border border-border leading-5 rounded-md hover:text-foreground focus:outline-none focus:ring ring-primary/50 active:bg-surface-hover transition ease-in-out duration-150">
                {!! __('pagination.previous') !!}
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-foreground-muted bg-surface border border-border leading-5 rounded-md hover:text-foreground focus:outline-none focus:ring ring-primary/50 active:bg-surface-hover transition ease-in-out duration-150">
                {!! __('pagination.next') !!}
            </a>
        @else
            <span
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-foreground-muted bg-surface border border-border cursor-default leading-5 rounded-md">
                {!! __('pagination.next') !!}
            </span>
        @endif

    </nav>
@endif