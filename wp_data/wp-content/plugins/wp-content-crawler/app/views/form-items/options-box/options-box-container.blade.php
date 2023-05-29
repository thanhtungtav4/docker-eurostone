<?php
    /**
     * For the tests in the options box, we will send the test data by getting it from the test data presenter. So,
     * the value of the 'extra' key for the data sent via AJAX must contain the selector and the target data attribute
     * for the test data presenter. Instead of hard-coding this for every testable form item in the options box, we
     * define it here once and use it for the form items. This value will be available for every view included in this
     * view. If more items need to be added to $dataExtra, then it should be merged with $dataExtra to make sure the
     * test data is always included in the AJAX request.
     *
     * @var array $dataExtra
     */
    $dataExtra = [
        'test'   => [
            'selector'  => '#test-data-presenter',
            'data'      => 'results'
        ]
    ];
?>

<div class="options-box-container hidden" id="options-box-container">

    <div class="options-box">
        {{-- BOX TITLE: Title is set dynamically --}}
        <div class="box-title"></div>

        {{-- INPUT DETAILS: This is set dynamically --}}
        <div class="input-details"></div>

        {{-- BOX CONTAINER --}}
        <div class="box-container">

            {{-- TABS --}}
            <h2 class="nav-tab-wrapper">
                {{-- DEFAULT --}}
                <a href="#" data-tab="#tab-options-box-find-replace"        class="nav-tab nav-tab-active">{{ _wpcc('Find-Replace') }}</a>
                <a href="#" data-tab="#tab-options-box-general"             class="nav-tab">{{ _wpcc('General') }}</a>
                <a href="#" data-tab="#tab-options-box-calculations"        class="nav-tab">{{ _wpcc('Calculations') }}</a>
                <a href="#" data-tab="#tab-options-box-templates"           class="nav-tab">{{ _wpcc('Templates') }}</a>

                {{-- FILE --}}
                <a href="#" data-tab="#tab-options-box-file-find-replace"   class="nav-tab">{{ _wpcc('Find-Replace') }}</a>
                <a href="#" data-tab="#tab-options-box-file-operations"     class="nav-tab">{{ _wpcc('File Operations') }}</a>
                <a href="#" data-tab="#tab-options-box-file-templates"      class="nav-tab">{{ _wpcc('Templates') }}</a>

                {{-- NOTES --}}
                <a href="#" data-tab="#tab-options-box-notes"               class="nav-tab">{{ _wpcc('Notes') }}</a>

                {{-- IMPORT/EXPORT --}}
                <a href="#" data-tab="#tab-options-box-import-export"       class="nav-tab">
                    <span class="dashicons dashicons-upload"></span>
                    <span class="dashicons dashicons-download"></span>
                </a>
            </h2>

            {{-- TAB CONTENT --}}
            <div class="tab-content section">

                {{-- TAB: FIND-REPLACE --}}
                <div id="tab-options-box-find-replace" class="tab">
                    @include('form-items.options-box.tabs.tab-find-replace')
                </div>

                {{-- TAB: FILE FIND-REPLACE --}}
                <div id="tab-options-box-file-find-replace" class="tab hidden">
                    @include('form-items.options-box.tabs.file.tab-file-find-replace')
                </div>

                {{-- TAB: FILE OPERATIONS --}}
                <div id="tab-options-box-file-operations" class="tab hidden">
                    @include('form-items.options-box.tabs.file.tab-file-operations')
                </div>

                {{-- TAB: GENERAL --}}
                <div id="tab-options-box-general" class="tab hidden">
                    @include('form-items.options-box.tabs.tab-general')
                </div>

                {{-- TAB: CALCULATIONS --}}
                <div id="tab-options-box-calculations" class="tab hidden">
                    @include('form-items.options-box.tabs.tab-calculations')
                </div>

                {{-- TAB: TEMPLATES --}}
                <div id="tab-options-box-templates" class="tab hidden">
                    @include('form-items.options-box.tabs.tab-templates')
                </div>

                {{-- TAB: FILE TEMPLATES --}}
                <div id="tab-options-box-file-templates" class="tab hidden">
                    @include('form-items.options-box.tabs.file.tab-file-templates')
                </div>

                {{-- TAB: NOTES --}}
                <div id="tab-options-box-notes" class="tab hidden">
                    @include('form-items.options-box.tabs.tab-notes')
                </div>

                {{-- TAB: IMPORT/EXPORT --}}
                <div id="tab-options-box-import-export" class="tab hidden">
                    @include('form-items.options-box.tabs.tab-import-export')
                </div>

            </div>

            {{-- CURRENT TEST DATA --}}
            <div class="test-data-presenter" id="test-data-presenter">
                {{-- HEADER --}}
                <div class="header">

                    {{-- TITLE --}}
                    <div class="title">
                        <span>{{ _wpcc('Current test data') }}</span>
                        <span class="count">(<span class="number">0</span>)</span>
                        <a role="button" class="invalidate hidden">{{ _wpcc('Invalidate') }}</a>
                    </div>

                </div>

                {{-- DATA CONTAINER--}}
                <div class="data hidden"></div>
            </div>

        </div>
    </div>

</div>