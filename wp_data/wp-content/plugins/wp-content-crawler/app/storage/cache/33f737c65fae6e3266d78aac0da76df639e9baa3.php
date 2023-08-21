

<?php
/**
 * @var string $name
 * @var string $innerKey
 * @var array  $value
 * @var bool   $showId
 * @var bool   $showTooltip
 * @var string $classAttr
 * @var string $titleAttr
 */

$preparedName   = $name . '[' . $innerKey . ']';
$type           = $type ?? 'text';
$isCheckbox     = $type === 'checkbox';

?>

<input type="<?php echo e($type); ?>"
       name="<?php echo e($preparedName); ?>"
       <?php if(!isset($showId) || $showId): ?> id="<?php echo e($preparedName); ?>" <?php endif; ?>
       <?php if(isset($classAttr)): ?> class="<?php echo e($classAttr); ?>" <?php endif; ?>
       <?php if(isset($titleAttr)): ?> title="<?php echo e($titleAttr); ?>" <?php endif; ?>
       <?php if(isset($showTooltip) && $showTooltip): ?> data-wpcc-toggle="wpcc-tooltip" <?php endif; ?>
       <?php if(isset($placeholder)): ?> placeholder="<?php echo e($placeholder); ?>" <?php endif; ?>
       <?php if($isCheckbox): ?>
            <?php if(isset($value[$innerKey])): ?> checked="checked" <?php endif; ?>
       <?php else: ?>
            value="<?php echo e(isset($value[$innerKey]) ? $value[$innerKey] : ''); ?>"
       <?php endif; ?>
       tabindex="0"><?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/form-items/input-with-inner-key.blade.php ENDPATH**/ ?>