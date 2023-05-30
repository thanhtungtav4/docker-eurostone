<?php
/** @var string $name */
// If the options box is enabled and there is a configuration for this form item name
if (isset($optionsBox) && $optionsBox && isset($optionsBoxConfigs) && isset($optionsBoxConfigs[$name])) {
    // Change $optionsBox variable with the given configuration so that 'button-options-box' form item can use
    // the configuration.
    $optionsBox = $optionsBoxConfigs[$name];
}

$showExport = isset($hasExportButton) && $hasExportButton;
$showImport = isset($hasImportButton) && $hasImportButton;

?>

<div class="inputs" data-name="{{ $name  }}">
    @if(!isset($settings[$name]) || !$settings[$name] || !$settings[$name][0])
        @include($include, [
            'name'      => $name . '[' . (isset($addKeys) ? 0 : '') . ']',
            'remove'    => true,
            'value'     => '',
            'dataKey'   => isset($addKeys) ? 0 : ''
        ])
    @else
        @foreach(unserialize($settings[$name][0]) as $key => $value)
            @include($include, [
                'name'      => $name . '[' . (isset($addKeys) ? $key : '') . ']',
                'remove'    => true,
                'value'     => $value,
                'dataKey'   => $key,
            ])
        @endforeach
    @endif
</div>
@if(!isset($max) || $max != 1)
    <div style="clear: both;"></div>
    <div class="actions">
        <button class="button wpcc-button wcc-add-new" data-max="{{isset($max) ? $max : 0}}">{{ _wpcc('Add New') }}</button>

        @if($showExport)
            <button class="button wpcc-button setting-export">{{ _wpcc('Export') }}</button>
        @endif

        @if($showImport)
            <button class="button wpcc-button setting-import">{{ _wpcc('Import') }}</button>
        @endif
    </div>

    @if ($showExport || $showImport)
        <div class="setting-import-export hidden"></div>
    @endif

@endif