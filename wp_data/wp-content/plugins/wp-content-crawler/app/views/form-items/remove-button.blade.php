<button class="button wpcc-button wcc-remove" title="{{ _wpcc("Remove") }}">
    <span class="dashicons dashicons-trash"></span>
</button>

@if(!isset($disableSort) || !$disableSort)
    <div class="wcc-sort"><span class="dashicons dashicons-move"></span></div>
@endif