<?php
    $key = \WPCCrawler\Objects\Settings\Enums\SettingKey::NOTES_SIMPLE;
?>

<textarea style="width: 100%;"
          name="{{ $key }}"
          id="{{ $key }}"
          rows="20">{{ isset($notesSimple) && !empty($notesSimple) ? $notesSimple[0] : ''  }}</textarea>

<?php

/**
 * Fires below the notes meta box in site settings page
 *
 * @since 1.6.3
 */
do_action('wpcc/view/site-settings/meta-box/notes');

?>
