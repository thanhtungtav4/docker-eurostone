<?php

// Prepare dependants for each spinning service option. By this way, when a spinning service is selected, related
// options for the selected service will be shown and others will be hidden.
/** @var array $spinningApiClients */
foreach($spinningApiClients as $apiClientKey => &$data) {
    if (!is_array($data)) continue;
    $data['dependants'] = '[".' . $apiClientKey . '"]';
}

$service = \WPCCrawler\Objects\Transformation\Spinning\SpinningService::getInstance();

?>

<div class="wcc-settings-title">
    <h3>{{ _wpcc('Spinning') }}</h3>
    <span>{{ _wpcc('Set content spinning options. Spinning is basically paraphrasing the text so that it becomes
    a different text having the same meaning as the original text. This feature, for example, can be used for search
    engine optimization purposes. If translation is enabled, spinning is applied after the post is translated.') }}</span>
</div>

<table class="wcc-settings" id="spinning-settings">

    @if($isGeneralPage)
        {{-- SPINNING IS ACTIVE --}}
        @include('form-items.combined.checkbox-with-label', [
            'name'   =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_SPINNING_ACTIVE,
            'title' =>  _wpcc('Spinning is active?'),
            'info'  =>  _wpcc('If you want to activate automated content spinning, check this. Note that
                spinning will increase the time required to crawl a post. The posts will be spun only if the
                spinning is activated in site settings as well.')
        ])
    @endif

    {{-- SPIN WITH --}}
    @include('form-items.combined.select-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_SELECTED_SPINNING_SERVICE,
        'title'     =>  _wpcc('Spin with'),
        'info'      =>  _wpcc('Select the spinning service you want to use to spin contents. You also need
            to properly configure the settings of the selected API below.'),
        'options'   =>  $spinningApiClients,
        'isOption'  =>  $isOption,
    ])

    {{-- SEND IN ONE REQUEST --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_SPINNING_SEND_IN_ONE_REQUEST,
        'title' =>  _wpcc('Send everything in one request?'),
        'info'  =>  _wpcc("If you want to send the values of every spinnable field to the selected API in one request,
            check this. If the selected spinning API limits the number of requests that can be sent, then sending every
            value in just one request might be good for you. When this is checked, the plugin will mark each item and
            combine them into one text. Hence, the selected spinning API must return the result with the same markings
            so that the plugin can find each different item in the response. If the response does not have the same
            markings, then the plugin will not be able to assign each item's spun value properly. <b>You should use this
            setting with caution</b>. For example, reordering paragraphs might cause the markings to change, making the
            plugin unable to find each item properly.")
    ])

    {{-- PROTECTED TERMS --}}
    @include('form-items.combined.textarea-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_SPINNING_PROTECTED_TERMS,
        'title'         =>  _wpcc('Protected Terms'),
        'info'          =>  _wpcc('You can enter comma-separated terms that should not be spun. These terms will be sent
            to the selected API if it supports protected terms feature.'),
        'placeholder'   =>  _wpcc('Comma-separated terms...'),
        'rows'          =>  3,
    ])

    {{-- API SETTINGS --}}
    @foreach(\WPCCrawler\Objects\Transformation\Spinning\SpinningService::getInstance()->getAPIs() as $clientClass => $clientKey)
        @include('general-settings.spinning.settings-' . $clientKey, [
            // Actually, the variable names and their keys in this array are the same. Hence, we do not need to include
            // these, because they will be available in the view by default. But, to see what happens better, we define
            // this array.
            'service'     => $service,
            'clientClass' => $clientClass,
            'clientKey'   => $clientKey,
            'isOption'    => $isOption
        ])
    @endforeach

    <?php

    /**
     * Fires before closing table tag in spinning tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/spinning', $settings, $isGeneralPage, $isOption);

    ?>

</table>
