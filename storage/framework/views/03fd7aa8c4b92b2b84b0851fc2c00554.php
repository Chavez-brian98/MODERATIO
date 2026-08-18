<?php if(! empty($crumbs)): ?>
    <nav aria-label="Breadcrumb" class="mb-4">
        <ol class="flex flex-wrap items-center gap-x-1.5 gap-y-1 text-sm">
            <?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $crumb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (! ($loop->last)): ?>
                    <li class="flex items-center gap-x-1.5">
                        <?php if(! empty($crumb['url'])): ?>
                            <a href="<?php echo e($crumb['url']); ?>" class="text-neutral-500 transition-colors hover:text-brand-700 dark:text-neutral-400 dark:hover:text-brand-400"><?php echo e($crumb['label']); ?></a>
                        <?php else: ?>
                            <span class="text-neutral-500 dark:text-neutral-400"><?php echo e($crumb['label']); ?></span>
                        <?php endif; ?>
                        <i class="fa-solid fa-chevron-right text-xs text-neutral-300 dark:text-neutral-600" aria-hidden="true"></i>
                    </li>
                <?php else: ?>
                    <li aria-current="page">
                        <span class="font-medium text-neutral-900 dark:text-white"><?php echo e($crumb['label']); ?></span>
                    </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ol>
    </nav>
<?php endif; ?>
<?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/partials/breadcrumbs.blade.php ENDPATH**/ ?>