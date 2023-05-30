<button type="button" class="button wpcc-button wcc-options-box" title="{{ _wpcc("Options") }}"
        data-settings="{{ isset($optionsBox) && is_array($optionsBox) ? json_encode($optionsBox) : '{}' }}">
    <span class="dashicons dashicons-admin-settings"></span>
    <input type="hidden" name="{{ $name . '[options_box]' }}" value="{{ isset($value['options_box']) ? $value['options_box'] : '{}' }}">
    <div class="summary-colors"></div>
</button>