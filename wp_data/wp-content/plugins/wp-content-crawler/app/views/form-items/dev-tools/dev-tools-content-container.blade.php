<?php
    $keyDevToolsState       = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_STATE;
    $keyUrl                 = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_URL;
    $keyTestButtonBehavior  = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_TEST_BUTTON_BEHAVIOR;
    $keyTargetHtmlTag       = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_TARGET_HTML_TAG;
    $keySelectionBehavior   = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_SELECTION_BEHAVIOR;
    $keyApplyManipulations  = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_APPLY_MANIPULATION_OPTIONS;
    $keyUseImmediately      = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_USE_IMMEDIATELY;
    $keyRemoveScripts       = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_REMOVE_SCRIPTS;
    $keyRemoveStyles        = \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_REMOVE_STYLES;
?>

<input type="hidden" name="{{ $keyDevToolsState }}" value='{!! isset($settings[$keyDevToolsState]) ? $settings[$keyDevToolsState][0] : '' !!}'>
<div class="dev-tools-content-container" data-wcc="{{ isset($data) && $data ? json_encode($data) : json_encode([]) }}">
    {{-- This is the element that stores all of the functionality of the developer tools --}}
    <div class="dev-tools-content" tabindex="-1">
        {{-- Lightbox title. This will be used as lightbox title later and won't be left here. It will be moved. --}}
        <div class="lightbox-title">Hi</div>

        {{-- Toolbar --}}
        <div class="toolbar">
            {{-- Address bar --}}
            <div class="address-bar">
                <div class="button-container">
                    {{-- Back button --}}
                    <span class="dashicons dashicons-arrow-left-alt button-option back disabled"
                          title="{{ _wpcc("Click to go back") }}"></span>

                    {{-- Forward button --}}
                    <span class="dashicons dashicons-arrow-right-alt button-option forward disabled"
                          title="{{ _wpcc("Click to go forward") }}"></span>

                    {{-- Refresh button --}}
                    <span class="dashicons dashicons-update button-option refresh disabled"
                          title="{{ _wpcc("Click to refresh") }}"></span>
                </div>

                @include('form-items.text', [
                    'name' => $keyUrl,
                    'class' => 'toolbar-input-container url-input',
                    'placeholder' => _wpcc('URL starting with http...'),
                ])

                <div class="button-container">
                    {{-- Go button --}}
                    <span class="dashicons dashicons-admin-collapse button-option go"
                          title="{{ _wpcc("Click to go to the URL") }}"></span>

                    {{-- Sidebar button --}}
                    <span class="dashicons dashicons-menu button-option sidebar-open"
                          title="{{ _wpcc("Click to open the sidebar") }}"></span>
                </div>
            </div>

            {{-- CSS selector tools --}}
            <div class="css-selector-tools">
                <div class="button-container">
                    {{-- Use button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-use',
                        'iconClass' => 'dashicons dashicons-yes',
                        'title' => _wpcc('Use the selector'),
                    ])
                </div>

                @include('form-items.text', [
                    'name' => \WPCCrawler\Objects\Settings\Enums\SettingKey::DEV_TOOLS_CSS_SELECTOR,
                    'class' => 'toolbar-input-container css-selector-input',
                    'placeholder' => _wpcc('CSS selector...'),
                ])

                <div class="button-container">
                    {{-- Test button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-test',
                        'iconClass' => 'dashicons dashicons-search',
                        'title' => _wpcc('Test the selector'),
                        'data' => [
                            'urlSelector' => sprintf('#%s', $keyUrl),
                            'testType' => \WPCCrawler\Test\Test::$TEST_TYPE_HTML,
                            'url' => 0
                        ]
                    ])

                    {{-- Clear highlights button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-clear-highlights',
                        'iconClass' => 'dashicons dashicons-editor-removeformatting',
                        'title' => _wpcc('Clear the highlights'),
                    ])

                    {{-- Show alternatives button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-show-alternatives',
                        'iconClass' => 'dashicons dashicons-image-rotate-right',
                        'title' => _wpcc('Show alternative selectors'),
                    ])

                    {{-- Remove elements button --}}
                    @include('form-items.partials.button-icon', [
                        'buttonClass' => 'css-selector-remove-elements',
                        'iconClass' => 'dashicons dashicons-trash',
                        'title' => _wpcc('Remove elements matching this CSS selector from current page'),
                    ])
                </div>
            </div>

            {{-- Other options --}}
            <div class="options">
                {{-- Options on the left --}}
                <div class="left">
                    {{-- Toggle hover select button --}}
                    <button class="button wpcc-button button-small button-option toggle-hover-select active" title="{{ _wpcc("Toggle hover select") }}">
                        <span class="dashicons dashicons-external"></span>
                    </button>

                    {{-- Separator --}}
                    @include('form-items.dev-tools.partial.vertical-separator')

                    {{-- Target HTML tag --}}
                    <label for="{{ $keyTargetHtmlTag }}">
                        <input type="text" name="{{ $keyTargetHtmlTag }}" class="target-html-tag" placeholder="{{ _wpcc("Target tag...") }}"
                            title="{{ sprintf(_wpcc('Enter an HTML element tag name to restrict the selection with only elements having this tag name. E.g. %1$s'), 'img') }}">
                    </label>

                    {{-- Separator --}}
                    @include('form-items.dev-tools.partial.vertical-separator')

                    {{-- Behavior of CSS selector finder --}}
                    <select name="{{ $keySelectionBehavior }}" title="{{ _wpcc("Select the behavior of CSS selector finder") }}">
                        <option value="unique">{{ _wpcc("Unique") }}</option>
                        <option value="similar">{{ _wpcc("Similar") }}</option>
                        <option value="similar_specific">{{ _wpcc("Similar (Specific)") }}</option>
                        <option value="contains">{{ _wpcc("Contains") }}</option>
                    </select>

                    {{-- Information about currently selected elements --}}
                    <div class="selected-elements"></div>

                </div>

                {{-- Options on the right --}}
                <div class="right">
                    {{-- Test button behavior --}}
                    <label for="{{ $keyTestButtonBehavior }}">
                        {{ _wpcc("Test via") }}
                        <select name="{{ $keyTestButtonBehavior }}" id="{{ $keyTestButtonBehavior }}" class="test-button-behavior">
                            <option value="php">{{ _wpcc("PHP") }}</option>
                            <option value="js">{{ _wpcc("JavaScript") }}</option>
                            <option value="both" selected="selected">{{ _wpcc("Both") }}</option>
                        </select>
                    </label>

                    {{-- Separator --}}
                    @include('form-items.dev-tools.partial.vertical-separator')

                    {{-- Apply manipulation options --}}
                    <label title="{{ _wpcc('When checked, manipulation options defined in the settings will be applied to the source code before showing the source code') }}"
                            for="{{ $keyApplyManipulations }}">
                        <input type="checkbox"
                                id="{{ $keyApplyManipulations }}"
                                class="apply-manipulation-options"
                                name="{{ $keyApplyManipulations }}"
                                tabindex="-1"
                                checked="checked"> {{ _wpcc("Manipulations") }}
                    </label>

                    {{-- Separator --}}
                    @include('form-items.dev-tools.partial.vertical-separator')

                    {{-- Use immediately --}}
                    <label for="{{ $keyUseImmediately }}" title="{{ _wpcc("Use the selector immediately when clicked") }}">
                        <input type="checkbox" id="{{ $keyUseImmediately }}" class="use-immediately" name="{{ $keyUseImmediately }}" tabindex="-1"> {{ _wpcc("Use immediately") }}
                    </label>

                    {{-- Separator --}}
                    @include('form-items.dev-tools.partial.vertical-separator')

                    {{-- Remove scripts --}}
                    <label for="{{ $keyRemoveScripts }}">
                        <input type="checkbox" id="{{ $keyRemoveScripts }}" class="remove-scripts" name="{{ $keyRemoveScripts }}" tabindex="-1" checked="checked"> {{ _wpcc("Remove scripts") }}
                    </label>

                    {{-- Separator --}}
                    @include('form-items.dev-tools.partial.vertical-separator')

                    {{-- Remove styles --}}
                    <label for="{{ $keyRemoveStyles }}">
                        <input type="checkbox" id="{{ $keyRemoveStyles }}" class="remove-styles" name="{{ $keyRemoveStyles }}" tabindex="-1"> {{ _wpcc("Remove styles") }}
                    </label>
                </div>
            </div>

            @include('partials/test-result-container')
        </div>

        {{-- iframe will be used to show the content --}}
        <iframe frameborder="0" class="source"></iframe>

        {{-- Sidebar --}}
        <div class="sidebar">
            {{-- Close button --}}
            <span class="dashicons dashicons-no-alt sidebar-close"></span>

            @include('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('History'),
                'class' => 'history',
                'buttons' => [
                    'dashicons dashicons-trash clear-history'
                ]
            ])

            @include('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('Alternative Selectors'),
                'class' => 'alternative-selectors'
            ])

            @include('form-items.dev-tools.sidebar-section', [
                'title' => _wpcc('All Used Selectors'),
                'class' => 'used-selectors'
            ])

        </div>

        {{-- Used to display IFrame status --}}
        <div class="iframe-status hidden"></div>
    </div>
</div>

{{-- This style will be copied to each page loaded into the iframe --}}
<style id="iframe-style">{!! \WPCCrawler\Factory::assetManager()->getDevToolsIframeStyle() !!}</style>
