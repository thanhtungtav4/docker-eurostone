<?php
/** @var string $name */
$val = isset($value) ? $value : (isset($settings[$name]) ? (is_array($settings[$name]) ? $settings[$name][0] : $settings[$name]) : '');
$val = isset($inputKey) && $inputKey && isset($val[$inputKey]) ? $val[$inputKey] : $val;

$inputKeyVal = isset($inputKey) && $inputKey ? "[{$inputKey}]" : '';

?>

@if(!isset($showButtons) || $showButtons)
    @include('form-items.partials.short-code-buttons')
@endif
<div class="input-group textarea {{ isset($addon) ? 'addon' : '' }} {{ isset($remove) ? 'remove' : '' }}"
@if(isset($dataKey)) data-key="{{ $dataKey }}" @endif>
    @if(isset($addon))
        @include('form-items.partials.button-addon-test')
    @endif

    <div class="input-container">
        <textarea @if(!isset($noName) || !$noName) name="{{ $name }}{{ $inputKeyVal }}" @endif id="{{ $name }}{{ $inputKeyVal }}"
                  @if(isset($cols)) cols="{{ $cols }}" @endif
                  rows="{{ isset($rows) ? $rows : '10' }}"
                  @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
                  @if(isset($disabled)) disabled @endif
                  @if(isset($readOnly)) readonly="readonly" @endif
        >{!! $val !!}</textarea>
    </div>

    @if(isset($remove))
        @include('form-items/remove-button')
    @endif
</div>
