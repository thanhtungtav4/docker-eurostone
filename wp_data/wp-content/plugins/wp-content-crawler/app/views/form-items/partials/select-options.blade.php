{{--
    Required variables:
        array $options:     An associative array where keys are option names the values are option values
        mixed $selected:    A sequential array or a single value such as a string or a number. If this is an array, the
                            values must be composed of selected option values.
--}}

@foreach($options as $optionValue => $optionName)
    <option value="{{ $optionValue }}" @if(\WPCCrawler\Utils::isOptionSelected($optionValue, $selected)) selected="selected" @endif>{{ $optionName }}</option>
@endforeach