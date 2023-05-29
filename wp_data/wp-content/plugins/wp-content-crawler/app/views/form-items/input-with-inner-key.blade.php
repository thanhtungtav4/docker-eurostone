{{--
    Required variables:
        string  $name:          Name of the input without the inner key part. E.g. if the final name of the input should
                                be '_wpcc_setting["selector"]', then this value should be '_wpcc_setting'.
        string  $innerKey:      Inner key that will added to the input name. E.g. if input name is "_wpcc_setting"
                                and this is "selector", input name will be '_wpcc_setting["selector"]'
        array   $value:         An array containing the value of the input under $innerKey. For example, if the inner key
                                is "selector" then $value["selector"] should give the value of this input. If the $value
                                does not contain the key, no value will be assigned to the input.

    Optional variables:
        string  $type:          The input's "type" attribute's value. Defaults to "text"
        string  $placeholder:   Placeholder text for the input
        bool    $showId:        If this is false, ID attribute of the input will not be set. Defaults to true. When
                                true, ID attribute's value of the input will be the same as its name attribute's value.
        bool    $showTooltip:   If this is true, the data attribute used for showing a tooltip will be added to the
                                input. Otherwise, nothing will be done. Defaults to false.
        string  $classAttr:     Value of the class attribute of the input
        string  $titleAttr:     Title attribute's value
--}}

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

<input type="{{ $type }}"
       name="{{ $preparedName }}"
       @if(!isset($showId) || $showId) id="{{ $preparedName }}" @endif
       @if(isset($classAttr)) class="{{ $classAttr }}" @endif
       @if(isset($titleAttr)) title="{{ $titleAttr }}" @endif
       @if(isset($showTooltip) && $showTooltip) data-wpcc-toggle="wpcc-tooltip" @endif
       @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
       @if($isCheckbox)
            @if(isset($value[$innerKey])) checked="checked" @endif
       @else
            value="{{ isset($value[$innerKey]) ? $value[$innerKey] : '' }}"
       @endif
       tabindex="0">