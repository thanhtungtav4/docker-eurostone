
<?php /** @var string $pageActionKey */ ?>
<?php if(!isset($noNonceAndAction) || !$noNonceAndAction): ?>
    <?php wp_nonce_field($pageActionKey, \WPCCrawler\Environment::nonceName()); ?>

    <input type="hidden" name="action" value="<?php echo e($pageActionKey); ?>" id="hiddenaction">
<?php endif; ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/partials/form-nonce-and-action.blade.php ENDPATH**/ ?>