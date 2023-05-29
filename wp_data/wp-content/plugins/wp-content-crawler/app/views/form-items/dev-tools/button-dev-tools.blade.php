<?php
    // Defaults
    $mData = [
        "testType" => \WPCCrawler\Test\Test::$TEST_TYPE_SOURCE_CODE,
    ];

    // If there is a data array, merge it with the defaults so that the default values are always kept.
    if(isset($data) && is_array($data)) {
        $data = array_merge($data, $mData);
    }

    if(isset($urlSelector) && $urlSelector) $data["urlSelector"] = $urlSelector;
?>
<button type="button" class="button wpcc-button wcc-dev-tools"
        data-wcc="{{ json_encode($data) }}"
        title="{{ _wpcc("Visual Inspector") }}"
>
    <span class="dashicons dashicons-admin-tools"></span>
</button>