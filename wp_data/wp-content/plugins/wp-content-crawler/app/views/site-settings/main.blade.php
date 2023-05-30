<div class="panel-wrap wcc-settings-meta-box" data-post-id="{{ $postId }}">
    <?php wp_nonce_field('wcc-settings-metabox', \WPCCrawler\Environment::nonceName()); ?>

    @include('partials.form-error-alert')

    <?php
    /** @var int $postId */
    /**
     * Fires after opening div tag of the meta box that contains the site settings
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/main/above-meta-box', $settings, $postId);

    ?>

    {{-- TABS --}}
    <h2 class="nav-tab-wrapper fixable">
        <a href="#" data-tab="#tab-main"        class="nav-tab nav-tab-active">{{ _wpcc('Main') }}</a>
        <a href="#" data-tab="#tab-category"    class="nav-tab">{{ _wpcc('Category') }}</a>
        <a href="#" data-tab="#tab-post"        class="nav-tab">{{ _wpcc('Post') }}</a>
        <a href="#" data-tab="#tab-templates"   class="nav-tab">{{ _wpcc('Templates') }}</a>
        <a href="#" data-tab="#tab-filters"     class="nav-tab">{{ _wpcc('Filters') }}</a>
        <a href="#" data-tab="#tab-notes"       class="nav-tab">{{ _wpcc('Notes') }}</a>

        <?php

        /**
         * Fires before import export tab in tab title area of site settings page
         *
         * @param array $settings   Existing settings and their values saved by user before
         * @param int $postId       ID of the site
         * @since 1.6.3
         */
        do_action('wpcc/view/site-settings/add_tab_title', $settings, $postId);

        ?>

        <a href="#" data-tab="#tab-general-settings"
           class="nav-tab {{ isset($settings[\WPCCrawler\Objects\Settings\Enums\SettingKey::DO_NOT_USE_GENERAL_SETTINGS]) && $settings[\WPCCrawler\Objects\Settings\Enums\SettingKey::DO_NOT_USE_GENERAL_SETTINGS][0] ? '' : 'hidden' }} nav-tab-highlight-on"
        >
            {{ _wpcc('Settings') }}
        </a>

        <a href="#" data-tab="#tab-import-export-settings" class="nav-tab">
            <span class="dashicons dashicons-upload"></span>
            <span class="dashicons dashicons-download"></span>
        </a>

        @include('partials.input-url-hash')
        @include('partials.button-toggle-info-texts')
    </h2>

    {{-- MAIN PAGE SETTINGS --}}
    <div id="tab-main" class="tab">
        @include('site-settings.tab-main')
    </div>

    {{-- CATEGORY PAGE SETTINGS --}}
    <div id="tab-category" class="tab hidden">
        @include('site-settings.tab-category')
    </div>

    {{-- POST PAGE SETTINGS --}}
    <div id="tab-post" class="tab hidden">
        @include('site-settings.tab-post')
    </div>

    {{-- TEMPLATE SETTINGS --}}
    <div id="tab-templates" class="tab hidden">
        @include('site-settings.tab-templates')
    </div>

    {{-- FILTER SETTINGS --}}
    <div id="tab-filters" class="tab hidden">
        @include('site-settings.tab-filters')
    </div>

    {{-- NOTES --}}
    <div id="tab-notes" class="tab hidden">
        @include('site-settings.tab-notes')
    </div>

    <?php

    /**
     * Fires before import export tab in tab content area of site settings page
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/add_tab_content', $settings, $postId);

    ?>

    {{-- IMPORT/EXPORT SETTINGS --}}
    <div id="tab-import-export-settings" class="tab hidden">
        @include('site-settings.tab-import-export')
    </div>

    {{-- CUSTOM GENERAL SETTINGS --}}
    <div id="tab-general-settings" class="tab hidden">
        @include('general-settings.settings', ['isPostPage' => true])
    </div>

    <?php

    /**
     * Fires before closing div tag of the meta box that contains the site settings
     *
     * @param array $settings   Existing settings and their values saved by user before
     * @param int $postId       ID of the site
     * @since 1.6.3
     */
    do_action('wpcc/view/site-settings/main/below-meta-box', $settings, $postId);

    ?>

    {{-- OPTIONS BOX CONTAINER--}}
    @include('form-items.options-box.options-box-container')

    {{-- QUICK SAVE BUTTON --}}
    @include('form-items.quick-save-settings')

</div>

{{-- DEV TOOLS CONTAINER--}}
@include('form-items.dev-tools.dev-tools-content-container', [
    'data' => [
        'postId' => $postId
    ]
])
