@extends('tools.base.tool-container', [
    'id'                => 'container-url-queue-manual-crawl',
    'noToggleButton'    => true
])

@section('title')
    {{ _wpcc('URLs for Manual Crawling') }}
@overwrite

@section('content')
    {{-- TABLE CONTAINER --}}
    <div class="table-container hidden">

        {{-- CONTROL BUTTONS --}}
        <div class="control-buttons">
            {{-- PAUSE --}}
            <button class="button wpcc-button pause" type="button" title="{{ _wpcc('Pause crawling') }}">
                <span class="dashicons dashicons-controls-pause"></span>
                {{ _wpcc('Pause') }}
            </button>

            {{-- CONTINUE --}}
            <button class="button wpcc-button continue hidden" type="button" title="{{ _wpcc('Continue crawling') }}">
                <span class="dashicons dashicons-controls-play"></span>
                {{ _wpcc('Continue') }}
            </button>
        </div>

        {{-- INFO --}}
        <div class="info">
            <span class="dashicons dashicons-warning"></span>
            {{ _wpcc('Do not close your browser while URLs are being crawled.') }}
        </div>

        {{-- STATUS --}}
        <div id="status"></div>

        {{-- TABLE CONTROLS --}}
        <div class="table-controls">
            <a role="button" class="show-all-responses">{{ _wpcc('Show all results') }}</a>
            <a role="button" class="hide-all-responses">{{ _wpcc('Hide all results') }}</a>
        </div>

        {{-- URL QUEUE TABLE --}}
        <table id="table-url-queue-manual-crawl">
            {{-- COLUMN NAMES --}}
            <thead>
            <tr>
                <th class="status">{{ _wpcc('Status') }}</th>
                <th class="site">{{ _wpcc('Site') }}</th>
                <th class="category">{{ _wpcc('Category') }}</th>
                <th class="image">{{ _wpcc('Image') }}</th>
                <th class="post-url">{{ _wpcc('Post URL') }}</th>
                <th class="controls">
                    <a role="button" class="remove-all">{{ _wpcc('Remove all') }}</a>
                </th>
            </tr>
            </thead>

            {{-- TABLE CONTENT--}}
            <tbody>

            {{-- ROW PROTOTYPE FOR URL--}}
            <tr class="prototype url hidden">
                <td class="status">
                    <span class="dashicons dashicons-controls-pause"></span>
                </td>
                <td class="site"></td>
                <td class="category"></td>
                <td class="image"></td>
                <td class="post-url"></td>
                <td class="controls">
                    {{-- REPEAT BUTTON --}}
                    <button class="button wpcc-button repeat" type="button" title="{{ _wpcc("Retry/recrawl") }}">
                        <span class="dashicons dashicons-controls-repeat"></span>
                    </button>

                    {{-- DELETE BUTTON --}}
                    <button class="button wpcc-button delete" type="button" title="{{ _wpcc("Delete") }}">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </td>
            </tr>

            {{-- ROW PROTOTYPE FOR RESPONSE --}}
            <tr class="prototype response hidden">
                <td class="" colspan="6">
                    <div class="response"></div>
                </td>
            </tr>

            </tbody>
        </table>
    </div>

    {{-- DEFAULT MESSAGE --}}
    <span class="default-message">{{ _wpcc("No URLs waiting to be saved.") }}</span>
@overwrite
