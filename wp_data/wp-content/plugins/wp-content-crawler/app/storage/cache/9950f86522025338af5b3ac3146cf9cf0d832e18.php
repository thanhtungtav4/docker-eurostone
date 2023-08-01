<?php if($site instanceof WP_Post): ?>
    <span class="site">
        <a href="<?php echo get_edit_post_link($site->ID); ?>" target="_blank"><?php echo e($site->post_title); ?></a>
    </span>
<?php endif; ?><?php /**PATH /var/www/html/wp-content/plugins/wp-content-crawler/app/views/dashboard/partials/site-link.blade.php ENDPATH**/ ?>