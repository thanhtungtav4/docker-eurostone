@extends('tools.base.tool-container', [
    'id' => 'tool-clear-urls'
])

@section('title')
    {{ _wpcc("Clear URLs") }}
@overwrite

@section('content')
    <form action="" class="tool-form">
        {{--{!! wp_nonce_field('wcc-tools', \WPCCrawler\Environment::nonceName()) !!}--}}

        @include('partials.form-nonce-and-action')
        <input type="hidden" name="tool_type" value="delete_urls">

        <div class="panel-wrap">

            <table class="wcc-settings">
                {{-- SITE --}}
                @include('form-items.combined.select-with-label', [
                    'name'      =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_CLEAR_URLS_SITE_ID,
                    'title'     =>  _wpcc('Site'),
                    'info'      =>  _wpcc('Select the site whose URLs you want to be deleted from the database.'),
                    'options'   =>  $sites,
                ])

                {{-- URL TYPE --}}
                @include('form-items.combined.select-with-label', [
                    'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_URL_TYPE,
                    'title' =>  _wpcc('URL Type'),
                    'info'  =>  _wpcc('Select URL types to be cleared for the specified site. If you clear the URLs
                        waiting in the queue, those URLs will not be saved, unless they are collected again. If you
                        clear already-saved URLs, those URLs may end up in the queue again, and they may be saved
                        as posts again. So, you may want to delete the posts as well, unless you want duplicate content.'),
                    'options'   =>  $urlTypes,
                ])

                {{-- SAFETY CHECK --}}
                @include('form-items.combined.checkbox-with-label', [
                    'name'  =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_SAFETY_CHECK,
                    'title' =>  _wpcc("I'm sure"),
                    'info'  =>  _wpcc('Check this to indicate that you are sure about this.'),
                ])
            </table>

            @include('form-items/submit-button', [
                'text'  =>  _wpcc('Delete URLs')
            ])

            @include('partials/test-result-container')

        </div>
    </form>
@overwrite
