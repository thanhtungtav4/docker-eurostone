{{--
    Required variables:
        string $name        Name of the hidden input
        string $eventGroups One of the constants defined in EventGroupKey class. These define the event groupe for which
                            this filter setting is suitable.

    Optional variables:
        string $value       Value of the hidden input
        bool   $isOption    True if this is for an option retrieved from options table. If this is a post meta, false.
                            Defaults to false.
        string $filterClass Additional classes that will be added to "filter-setting" element
--}}

<?php
/** @var string $name */
/** @var string $eventGroup */
$val = isset($value) ? $value : (isset($settings[$name]) ? (isset($isOption) && $isOption ? $settings[$name] : $settings[$name][0]) : '');
$config = [
    'eventGroup' => $eventGroup
];

$additionalClasses = isset($filterClass) ? $filterClass : '';
?>

<div class="input-group filter-setting {{ $additionalClasses }}" data-config="{{ json_encode($config) }}">
    <div class="input-container">
        <input type="hidden"
               class="filter-serialized-input"
               id="{{ isset($name) ? $name : '' }}"
               name="{{ isset($name) ? $name : '' }}"
               value="{{ $val }}" />
        <div class="filters-loading">
            <span>{{ _wpcc('Loading...') }}</span>
        </div>
    </div>
</div>