<?php
    /** @var string $name */
    /** @var string|array $optionData */
?>
<div class="input-group">
    <div class="input-container">
        <select name="{{ $name }}" id="{{ $name }}" {{ isset($disabled) && $disabled ? 'disabled' : '' }} tabindex="0">
            <?php $selectedKey = isset($settings[$name]) ? (isset($isOption) && $isOption ? $settings[$name] : $settings[$name][0]) : false; ?>
            @foreach($options as $key => $optionData)
                <?php
                    // If the option data is an array
                    $isArr = is_array($optionData);
                    if ($isArr) {
                        // Get the option name and the dependants if there exists any
                        $optionName = \WPCCrawler\Utils::array_get($optionData, 'name');
                        $dependants = \WPCCrawler\Utils::array_get($optionData, 'dependants');
                    } else {
                        // Otherwise, option data is the name of the option and there is no dependant.
                        $optionName = $optionData;
                        $dependants = null;
                    }
                ?>

                <option value="{{ $key }}"
                    @if($selectedKey && $key == $selectedKey) selected="selected" @endif
                    @if($dependants) data-dependants="{{ $dependants }}" @endif
                >{{ $optionName }}</option>
            @endforeach
        </select>
    </div>
</div>