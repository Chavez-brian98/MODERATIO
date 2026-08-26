@props(['paginator' => null, 'perPageOptions' => [10, 25, 50, 100]])

@php
    $currentPerPage = $paginator->perPage();
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $total = $paginator->total();
    $from = $paginator->firstItem();
    $to = $paginator->lastItem();

    $pages = [];

    if ($lastPage <= 7) {
        $pages = range(1, $lastPage);
    } else {
        $pages[] = 1;

        $windowStart = max(2, $currentPage - 3);
        $windowEnd = min($lastPage - 1, $currentPage + 3);

        if ($windowStart > 2) {
            $pages[] = '...';
        }

        for ($i = $windowStart; $i <= $windowEnd; $i++) {
            $pages[] = $i;
        }

        if ($windowEnd < $lastPage - 1) {
            $pages[] = '...';
        }

        $pages[] = $lastPage;
    }
@endphp

<div class="flex flex-col gap-4 py-4 px-4 border-t border-brand-100 dark:border-neutral-800 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <span class="text-sm text-neutral-600 dark:text-neutral-400">
            Mostrando <strong>{{ $from }}</strong> a <strong>{{ $to }}</strong> de <strong>{{ $total }}</strong> resultados
        </span>

        <div class="relative">
            <select
                id="per-page-select"
                name="per_page"
                class="appearance-none rounded-lg border border-brand-200 bg-white px-3 py-1.5 pr-8 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
            >
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>
                        {{ $option }} por página
                    </option>
                @endforeach
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    @if ($lastPage > 1)
        <nav class="flex items-center gap-1" aria-label="Paginación">
            @if ($paginator->onFirstPage())
                <button type="button" disabled class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-neutral-400 cursor-not-allowed dark:text-neutral-500" aria-label="Página anterior">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-neutral-700 hover:bg-brand-100 hover:text-brand-800 transition-colors dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white" aria-label="Página anterior">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
            @endif

            @foreach ($pages as $page)
                @if ($page === '...')
                    <span class="inline-flex h-9 w-9 items-center justify-center text-sm text-neutral-400 dark:text-neutral-500">…</span>
                @elseif ($page == $currentPage)
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-brand-700 text-sm font-semibold text-white shadow-sm dark:bg-brand-600" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $paginator->url($page) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-neutral-700 hover:bg-brand-100 hover:text-brand-800 transition-colors dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-neutral-700 hover:bg-brand-100 hover:text-brand-800 transition-colors dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white" aria-label="Página siguiente">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            @else
                <button type="button" disabled class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-sm font-medium text-neutral-400 cursor-not-allowed dark:text-neutral-500" aria-label="Página siguiente">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            @endif
        </nav>
    @endif
</div>

<form id="per-page-form" method="GET" class="hidden">
    @foreach (request()->except(['page', 'per_page']) as $key => $value)
        @if (is_array($value))
            @foreach ($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    <input type="hidden" name="page" value="1">
    <input type="hidden" name="per_page" id="per-page-input" value="{{ $currentPerPage }}">
</form>

<script>
    (() => {
        const select = document.getElementById('per-page-select');
        const form = document.getElementById('per-page-form');
        const input = document.getElementById('per-page-input');

        if (select && form && input) {
            select.addEventListener('change', function () {
                input.value = this.value;
                form.submit();
            });
        }
    })();
</script>
