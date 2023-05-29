<?php
/** @var array $settings */
?>

<div class="wcc-settings-title">
    <h3>{{ _wpcc('Main Settings') }}</h3>
    <span>{{ _wpcc('Set main page URL, scheduling options, duplicate post checking, cookies...') }}</span>
</div>

{{-- SECTION NAVIGATION --}}
@include('partials.tab-section-navigation')

<table class="wcc-settings">

    {{-- SITE URL --}}
    @include('form-items.combined.input-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::MAIN_PAGE_URL,
        'title' => _wpcc('Site URL'),
        'info'  => sprintf(_wpcc('Target site\'s URL that consists of its domain. E.g. %1$s. Do not include the
                parts other than the domain. For example, instead of %2$s, write %3$s. The URL must start with %4$s.
                You must fill this field.'),
                '<span class="highlight url">https://wordpress.org/</span>',
                '<span class="highlight url strike">https://wordpress.org/plugins/</span>',
                '<span class="highlight url">https://wordpress.org/</span>',
                '<b>http</b>'
                ),
        'type'  => 'url',
        'markRequired' => true,
    ])

    {{-- ACTIVE SCHEDULING --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::ACTIVE,
        'title' =>  _wpcc('Active for scheduling?'),
        'info'  =>  _wpcc('If you want to activate this site for crawling, check this. If you do not check this,
            the site will not be crawled, no posts will be saved.')
    ])

    {{-- ACTIVE RECRAWLING --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::ACTIVE_RECRAWLING,
        'class' =>  'label-recrawl',
        'title' =>  _wpcc('Active for recrawling?'),
        'info'  =>  _wpcc('If you want to activate this site for post recrawling, check this. If you do not check this,
            the posts will not be recrawled.')
    ])

    {{-- ACTIVE POST DELETING --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::ACTIVE_POST_DELETING,
        'class' =>  'label-post-deleting',
        'title' =>  _wpcc('Active for post deleting?'),
        'info'  =>  _wpcc('If you want to activate this site for post deleting, check this. If you do not check
            this, the posts will not be deleted.')
    ])

    {{-- ACTIVE TRANSLATION --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::ACTIVE_TRANSLATION,
        'class'         =>  'label-post-translation',
        'title'         =>  _wpcc('Active for post translation?'),
        'info'          =>  _wpcc('If you want to activate this site for post translation, check this. If you do not check
            this, the posts will not be translated.'),
        'dependants'    => '["#translatable-fields"]',
    ])

    {{-- TRANSLATABLE FIELDS --}}
    @include('form-items.combined.multi-select-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::TRANSLATABLE_FIELDS,
        'class'     =>  'label-post-translation',
        'title'     =>  _wpcc('Translatable Fields'),
        'info'      =>  _wpcc('You can select which fields should be translated. If you select none, it means all
            fields are translatable. <b>Note that</b> the fields are translated after the templates are prepared.
            So, if a translatable field is used in a non-translatable field via short codes, value of the
            translatable field is included into the non-translatable field without translation.'),
        'id'        => 'translatable-fields',
        'options'   => $transformableFields,
    ])

    {{-- ACTIVE SPINNING --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::ACTIVE_SPINNING,
        'class'         =>  'label-post-spinning',
        'title'         =>  _wpcc('Active for post spinning?'),
        'info'          =>  _wpcc('If you want to activate this site for post spinning, check this. If you do not check
            this, the posts will not be spun.'),
        'dependants'    => '["#spinnable-fields"]'
    ])

    {{-- SPINNABLE FIELDS --}}
    @include('form-items.combined.multi-select-with-label', [
        'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::SPINNABLE_FIELDS,
        'class'     =>  'label-post-spinning',
        'title'     =>  _wpcc('Spinnable Fields'),
        'info'      =>  _wpcc('You can select which fields should be spun. If you select none, it means all
            fields are spinnable. <b>Note that</b> the fields are spun after the templates are prepared. So,
            if a spinnable field is used in a non-spinnable field via short codes, value of the spinnable field
            is included into the non-spinnable field without spinning. <b>Every item will be sent in a new request
            unless otherwise is selected. For example, if you select tags and there are 10 tags, 10 requests will be
            sent to the selected API. If you select only the content, then only 1 request will be sent. Please keep this
            in mind when selecting the spinnable fields If the API limits the number of requests.</b>'),
        'id'        => 'spinnable-fields',
        'options'   => $transformableFields,
    ])

    {{-- DUPLICATE CHECKING --}}
    <?php
        $duplicatePostCheckTypes = \WPCCrawler\Objects\Crawling\Savers\PostSaver::getDuplicateCheckOptionsForSelect($settings);
        $duplicatePostViewOptions = [
            'options' => $duplicatePostCheckTypes["values"],
        ];

        // Set the default values if this is a new site that is being created right now. Otherwise, we'll
        // use the settings saved previously.
        if(!isset($_GET["post"]) || !$_GET["post"]) {
            $duplicatePostViewOptions['value'] = $duplicatePostCheckTypes["defaults"];
        }
    ?>
    @include('form-items.combined.multi-checkbox-with-label', array_merge([
        'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::DUPLICATE_CHECK_TYPES,
        'title' =>  _wpcc('Check duplicate posts via'),
        'info'  =>  _wpcc('Set how to decide whether a post is duplicate or not. Duplicate checking will be
            performed in this order: URL, title, content. If one of them is found, the post is considered as
            duplicate.')
    ], $duplicatePostViewOptions))

    {{-- USE CUSTOM GENERAL SETTINGS --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::DO_NOT_USE_GENERAL_SETTINGS,
        'title'         =>  _wpcc('Use custom general settings?'),
        'info'          =>  _wpcc('If you want to specify different settings for this site (not use general settings),
            check this. When you check this, settings tabs will appear.'),
        'dependants'    => '["[data-tab=\'#tab-general-settings\']"]',
    ])

    {{-- SECTION: REQUEST --}}
    @include('partials.table-section-title', ['title' => _wpcc("Request")])

    {{-- COOKIES --}}
    @include('form-items.combined.multiple-key-value-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::COOKIES,
        'title' => _wpcc('Cookies'),
        'info'  => _wpcc('You can provide cookies that will be attached to every request. For example, you can
            provide a session cookie to crawl a site by a logged-in user.'),
        'keyPlaceholder'    => _wpcc('Cookie name'),
        'valuePlaceholder'  => _wpcc('Cookie content'),
        'hasExportButton'   => true,
        'hasImportButton'   => true,
    ])

    {{-- HEADERS --}}
    @include('form-items.combined.multiple-key-value-with-label', [
        'name'  => \WPCCrawler\Objects\Settings\Enums\SettingKey::REQUEST_HEADERS,
        'title' => _wpcc('Headers'),
        'info'  => _wpcc('You can provide HTTP request headers that will be attached to every request.'),
        'keyPlaceholder'    => _wpcc('Header name'),
        'valuePlaceholder'  => _wpcc('Header value'),
        'hasExportButton'   => true,
        'hasImportButton'   => true,
    ])

    {{-- SECTION: SETTINGS PAGE --}}
    @include('partials.table-section-title', ['title' => _wpcc("Settings Page")])

    {{-- USE CACHE FOR TEST URLS --}}
    @include('form-items.combined.checkbox-with-label', [
        'name' => \WPCCrawler\Objects\Settings\Enums\SettingKey::CACHE_TEST_URL_RESPONSES,
        'title' => _wpcc('Use cache for test URLs'),
        'info' => _wpcc('Check this if you want the plugin to cache the responses retrieved from the test URLs. By this
            way, you can test faster and send less number of requests to the target site. Caching will only be done for
            the tests done here in site settings.'),
    ])

    {{-- FIX TABS --}}
    @include('form-items.combined.checkbox-with-label', [
        'name' => \WPCCrawler\Objects\Settings\Enums\SettingKey::FIX_TABS,
        'title' => _wpcc('Fix tabs when page is scrolled down'),
        'info' => _wpcc('Check this if you want to fix the tabs at the top of the page when the page is scrolled down.'),
    ])

    {{-- FIX CONTENT NAVIGATION --}}
    @include('form-items.combined.checkbox-with-label', [
        'name' => \WPCCrawler\Objects\Settings\Enums\SettingKey::FIX_CONTENT_NAVIGATION,
        'title' => _wpcc('Fix content navigation when page is scrolled down'),
        'info' => _wpcc('Check this if you want to fix the content navigation at the top of the page when the page is
            scrolled down.'),
    ])

    <?php

    /** @var int $postId */
    /**
     * Fires before closing table tag in main tab of site settings page.
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/tab/main', $settings, $postId);

    ?>

</table>
