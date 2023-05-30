<?php

$dirPathExplanation = sprintf(
    _wpcc('The folder paths will be considered as they are relative to uploads directory of WordPress. E.g. if you write
        %1$s, it is considered as %2$s. You cannot define a folder outside of uploads directory of WordPress.'),
    "'images/views'",
    "'wp-content/uploads/images/views'"
);

$folderPathPlaceholder = _wpcc('Folder path relative to uploads directory of WordPress...');

?>

<div class="description">
    {{ _wpcc("Move and copy the files. Before applying the options in this tab, find-replace options will be applied.") }}
    {!! _wpcc_file_options_box_tests_note() !!}
</div>

<table class="wcc-settings">

    {{-- MOVE --}}
    @include('form-items.combined.multiple-text-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_MOVE,
        'title'         => _wpcc('Move files to folder'),
        'info'          => _wpcc("Define the folders in which the saved files should be stored. If you set more than
            one path, a random one will be selected.") . ' ' . $dirPathExplanation,
        'placeholder'   => $folderPathPlaceholder,
        'inputKey'      => 'path',
        'addon'         => 'dashicons dashicons-search',
        'test'          => true,
        'data'          => [
            'testType'      => \WPCCrawler\Test\Test::$TEST_TYPE_FILE_MOVE,
            'extra'         => $dataExtra
        ],
        'addonClasses'  => 'wcc-test-move'
    ])

    {{-- COPY --}}
    @include('form-items.combined.multiple-text-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_COPY,
        'title'         => _wpcc('Copy files to folder'),
        'info'          => _wpcc('Define the folders to which the saved files should be copied. If you set more than
            one path, the files will be copied to all.') . ' ' . $dirPathExplanation,
        'placeholder'   => $folderPathPlaceholder,
        'inputKey'      => 'path',
        'addon'         => 'dashicons dashicons-search',
        'test'          => true,
        'data'          => [
            'testType'      => \WPCCrawler\Test\Test::$TEST_TYPE_FILE_COPY,
            'extra'         => $dataExtra
        ],
        'addonClasses'  => 'wcc-test-copy'
    ])

</table>