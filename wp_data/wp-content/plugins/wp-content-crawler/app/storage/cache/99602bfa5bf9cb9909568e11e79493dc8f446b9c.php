<?php /** @var \WPCCrawler\Objects\File\MediaFile $item */ ?>
<div class="attachment-item">
    
    <div class="media-url">
        <?php if($item->isGalleryImage()): ?>
            <span class="name"><?php echo e(_wpcc("Gallery Item")); ?></span>
        <?php endif; ?>
        <span><a href="<?php echo e($item->getLocalUrl()); ?>" target="_blank" class="item-url"
           <?php if(isset($tooltip) && $tooltip): ?>
             data-html="true" data-wpcc-toggle="wpcc-tooltip" title="<img src='<?php echo e($item->getLocalUrl()); ?>'>"
           <?php endif; ?>>
            <?php echo e($item->getLocalUrl()); ?>

        </a></span>
    </div>

    
    <?php echo $__env->make('site-tester.partial.attachment-item-info', [
        'name'  => _wpcc('Title'),
        'info'  => $item->getMediaTitle(),
        'class' => 'media-title',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('site-tester.partial.attachment-item-info', [
        'name'  => _wpcc('Description'),
        'info'  => $item->getMediaDescription(),
        'class' => 'media-desc',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('site-tester.partial.attachment-item-info', [
        'name' => _wpcc('Caption'),
        'info' => $item->getMediaCaption(),
        'class' => 'media-caption',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->make('site-tester.partial.attachment-item-info', [
        'name' => _wpcc('Alternate text'),
        'info' => $item->getMediaAlt(),
        'class' => 'media-alt',
    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php if($item->getCopyFileUrls()): ?>
        <div class="copy-file-urls">
            <span class="name"><?php echo e(_wpcc('Copy file URLs')); ?></span>
            <ol>
                <?php $__currentLoopData = $item->getCopyFileUrls(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $copyFileUrl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e($copyFileUrl); ?>" target="_blank"
                        <?php if(isset($tooltip) && $tooltip): ?>
                            data-html="true" data-wpcc-toggle="wpcc-tooltip" title="<img src='<?php echo e($item->getLocalUrl()); ?>'>"
                        <?php endif; ?>>
                            <?php echo e($copyFileUrl); ?></a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        </div>
    <?php endif; ?>
</div><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-tester/partial/attachment-item.blade.php ENDPATH**/ ?>