{{--
    Required variables:
        string $name:       Name of the select element
        array $options:     This can be a 1-dimensional or 2-dimensional array. When 2 dimensional, each inner array
                            defines values for an option group. When 1 dimensional, a key-value pair array where keys
                            are option values and the values are option descriptions. When 2 dimensional, the keys are
                            option group descriptions while the values are the 1 dimensional array, described previously.
    Optional variables:
        array $selected:    A sequential array consisting of selected option values. If not given, this will be tried to
                            be retrieved from $settings.
--}}

<?php
/** @var string $name */
$optionsValues = array_values($options);
$hasOptGroups = isset($optionsValues[0]) && is_array($optionsValues[0]);

$selected = isset($selected) ? $selected : (isset($settings[$name]) && $settings[$name][0] ? unserialize($settings[$name][0]) : []);

?>

<div class="input-group multi-select">
    <div class="input-container">
        <select name="{{ $name }}[]" id="{{ $name }}[]" multiple="multiple">
            {{-- If there are option groups --}}
            @if($hasOptGroups)
                {{-- Given options array is structured as ["option group name" => [ "val1" => "name1", "val2" => "name2", ...], ...] --}}
                @foreach($options as $optGroupName => $optGroupValues)
                    <optgroup label="{{ $optGroupName }}">
                        {{-- Add the options --}}
                        @include('form-items.partials.select-options', [
                            'options'   => $optGroupValues,
                            'selected'  => $selected,
                        ])
                    </optgroup>
                @endforeach

            @else
                {{-- Given options array is structured as ["option value" => "option name", ...] --}}
                @include('form-items.partials.select-options', [
                    'options'   => $options,
                    'selected'  => $selected,
                ])
            @endif
        </select>
    </div>
</div>