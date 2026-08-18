@if (! empty($crumbs))
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-sm">
            @foreach ($crumbs as $crumb)
                @unless ($loop->last)
                    <li class="flex items-center gap-x-1.5">
                        @if (! empty($crumb['url']))
                            <a href="{{ $crumb['url'] }}" class="text-neutral-500 transition-colors hover:text-brand-700 dark:text-neutral-400 dark:hover:text-brand-400">{{ $crumb['label'] }}</a>
                        @else
                            <span class="text-neutral-500 dark:text-neutral-400">{{ $crumb['label'] }}</span>
                        @endif
                        <i class="fa-solid fa-chevron-right text-xs text-neutral-300 dark:text-neutral-600" aria-hidden="true"></i>
                    </li>
                @else
                    <li aria-current="page">
                        <span class="font-medium text-neutral-900 dark:text-white">{{ $crumb['label'] }}</span>
                    </li>
                @endunless
            @endforeach
        </ol>
    </nav>
@endif
