<?php
    $key = \WPCCrawler\Objects\Settings\Enums\SettingKey::NOTES_SIMPLE;
?>

<textarea style="width: 100%;"
          name="<?php echo e($key); ?>"
          id="<?php echo e($key); ?>"
          rows="20"><?php echo e(isset($notesSimple) && !empty($notesSimple) ? $notesSimple[0] : ''); ?></textarea>

<?php

/**
 * Fires below the notes meta box in site settings page
 *
 * @since 1.6.3
 */
do_action('wpcc/view/site-settings/meta-box/notes');

?>
<?php /**PATH /var/www/nttung.dev/htdocs/wp-content/plugins/wp-content-crawler/app/views/site-settings/meta-box-notes.blade.php ENDPATH**/ ?>