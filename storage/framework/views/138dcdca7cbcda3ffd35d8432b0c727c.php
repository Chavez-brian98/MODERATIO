<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['paginator' => null, 'perPageOptions' => [10, 25, 50, 100]]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['paginator' => null, 'perPageOptions' => [10, 25, 50, 100]]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $currentPerPage = $paginator->perPage();
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();
    $total = $paginator->total();
    $from = $paginator->firstItem();
    $to = $paginator->lastItem();
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-4 px-4 border-t border-brand-100 dark:border-neutral-800">
    <div class="flex items-center gap-3">
        <span class="text-sm text-neutral-600 dark:text-neutral-400">
            Mostrando <strong><?php echo e($from); ?></strong> a <strong><?php echo e($to); ?></strong> de <strong><?php echo e($total); ?></strong> resultados
        </span>

        <div class="relative">
            <select
                id="per-page-select"
                name="per_page"
                wire:model.defer="perPage"
                class="appearance-none rounded-lg border border-brand-200 bg-white px-3 py-1.5 pr-8 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200"
                onchange="this.form.submit()"
            >
                <?php $__currentLoopData = $perPageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($option); ?>" <?php echo e($currentPerPage == $option ? 'selected' : ''); ?>>
                        <?php echo e($option); ?> por página
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-neutral-400">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <?php if($paginator->onFirstPage()): ?>
            <button type="button" disabled class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-medium text-neutral-400 cursor-not-allowed dark:text-neutral-500" aria-label="Página anterior">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-medium text-neutral-700 hover:bg-brand-100 hover:text-brand-800 transition-colors dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white" aria-label="Página anterior">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        <?php endif; ?>

        <span class="px-3 py-1.5 text-sm font-medium text-neutral-700 dark:text-neutral-300" aria-current="page">
            Página <?php echo e($currentPage); ?> de <?php echo e($lastPage); ?>

        </span>

        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-medium text-neutral-700 hover:bg-brand-100 hover:text-brand-800 transition-colors dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white" aria-label="Página siguiente">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <button type="button" disabled class="inline-flex items-center justify-center rounded-lg px-3 py-1.5 text-sm font-medium text-neutral-400 cursor-not-allowed dark:text-neutral-500" aria-label="Página siguiente">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        <?php endif; ?>
    </div>
</div>

<form id="per-page-form" method="GET" class="hidden">
    <?php $__currentLoopData = request()->except(['page', 'per_page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(is_array($value)): ?>
            <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="<?php echo e($key); ?>[]" value="<?php echo e($v); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <input type="hidden" name="page" value="1">
    <input type="hidden" name="per_page" id="per-page-input" value="<?php echo e($currentPerPage); ?>">
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
</script><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/partials/pagination.blade.php ENDPATH**/ ?>