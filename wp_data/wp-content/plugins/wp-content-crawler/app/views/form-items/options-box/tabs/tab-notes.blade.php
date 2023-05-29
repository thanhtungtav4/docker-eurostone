<div class="description">
    {{ _wpcc('You can take notes about the options you configured, or anything. This tab is just for taking notes.') }}
</div>

<table class="wcc-settings">

    @include('form-items.combined.textarea-with-label', [
        'name'          => \WPCCrawler\Objects\Settings\Enums\SettingKey::OPTIONS_BOX_NOTE,
        'rows'          => 6,
        'showButtons'   => false,
        'placeholder'   =>  _wpcc('Notes...'),
        'noLabel'       => true,
    ])

</table>