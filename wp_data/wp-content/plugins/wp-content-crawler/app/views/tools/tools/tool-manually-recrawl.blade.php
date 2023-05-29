@extends('tools.base.tool-container')

@section('title')
    {{ _wpcc('Manually recrawl (update) a post') }}
@overwrite

@section('content')
    <form action="" class="tool-form">
        {{--{!! wp_nonce_field('wcc-tools', \WPCCrawler\Environment::nonceName()) !!}--}}

        @include('partials.form-nonce-and-action')

        <input type="hidden" name="tool_type" value="recrawl_post">

        <div class="panel-wrap">

            <table class="wcc-settings">
                {{-- SITE --}}
                @include('form-items.combined.input-with-label', [
                    'name'          =>  \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_TOOLS_RECRAWL_POST_ID,
                    'title'         =>  _wpcc('Post ID'),
                    'info'          =>  _wpcc('Write the ID of the post you want to update.'),
                    'type'          =>  'number',
                    'min'           =>  0,
                    'placeholder'   => _wpcc('Post ID...')
                ])

            </table>

            @include('form-items/submit-button', [
                'text'  =>  _wpcc('Recrawl'),
                'class' => 'recrawl'
            ])

            @include('partials/test-result-container')
        </div>
    </form>
@overwrite
