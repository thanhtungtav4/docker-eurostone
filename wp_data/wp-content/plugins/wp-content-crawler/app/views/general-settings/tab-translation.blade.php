<?php

// Prepare dependants for each translation service option. By this way, when a translation service is selected, related
// options for the selected service will be shown and others will be hidden.
/** @var array $translationApiClients */
foreach($translationApiClients as $apiClientKey => &$data) {
    if (!is_array($data)) continue;
    $data['dependants'] = '[".' . $apiClientKey . '"]';
}

$service = \WPCCrawler\Objects\Transformation\Translation\TranslationService::getInstance();

?>

<div class="wcc-settings-title">
    <h3>{{ _wpcc('Translation') }}</h3>
    <span>{{ _wpcc('Set content translation options') }}</span>
</div>

<table class="wcc-settings" id="translation-settings">

    @if($isGeneralPage)
        {{-- TRANSLATION IS ACTIVE --}}
        @include('form-items.combined.checkbox-with-label', [
            'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_IS_TRANSLATION_ACTIVE,
            'title' =>  _wpcc('Translation is active?'),
            'info'  =>  _wpcc('If you want to activate automated content translation, check this. Note that
                    translating will increase the time required to crawl a post. The posts will be translated
                    only if the translation is activated in site settings as well.')
        ])
    @endif

    {{-- TRANSLATE WITH --}}
    @include('form-items.combined.select-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_SELECTED_TRANSLATION_SERVICE,
        'title'     =>  _wpcc('Translate with'),
        'info'      =>  _wpcc('Select the translation service you want to use to translate contents. You also need
            to properly configure the settings of the selected API below.'),
        'options'   =>  $translationApiClients,
        'isOption'  =>  $isOption,
    ])

    {{-- API SETTINGS --}}
    @foreach(\WPCCrawler\Objects\Transformation\Translation\TranslationService::getInstance()->getAPIs() as $clientClass => $clientKey)
        @include('general-settings.translation.settings-' . $clientKey, [
            // Actually, the variable names and their keys in this array are the same. Hence, we do not need to include
            // these, because they will be available in the view by default. But, to see what happens better, we define
            // this array.
            'service'               => $service,
            'clientClass'           => $clientClass,
            'clientKey'             => $clientKey,
            'translationLanguages'  => $translationLanguages,
            'isOption'              => $isOption
        ])
    @endforeach

    <?php

    /**
     * Fires before closing table tag in translation tab of general settings page.
     *
     * @param array $settings       Existing settings and their values saved by user before
     * @param bool  $isGeneralPage  True if this is called from a general settings page.
     * @param bool  $isOption       True if this is an option, instead of a setting. A setting is a post meta, while
     *                              an option is a WordPress option. This is true when this is fired from general
     *                              settings page.
     * @since 1.6.3
     */
    do_action('wpcc/view/general-settings/tab/translation', $settings, $isGeneralPage, $isOption);

    ?>

</table>
