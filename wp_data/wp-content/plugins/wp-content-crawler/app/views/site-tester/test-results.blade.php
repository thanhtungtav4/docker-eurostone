@if(isset($template))
    <div class="details">
        <h2>
            <span>{{ _wpcc('Template') }}</span>
            @if(isset($templateMessage) && $templateMessage)
                <span class="small">{{ $templateMessage }}</span>
            @endif
            <button class="button wpcc-button" id="go-to-details">{{ _wpcc('Go to details') }}</button>
        </h2>
        <div class="inside">
            <div class="template">
                {!! $template !!}
            </div>

            {{-- SOURCE CODE --}}
            @if(isset($template) && isset($showSourceCode) && $showSourceCode)
                <div class="source-code-container">
                    @include('site-tester.partial.toggleable-textarea', [
                        'title'      => _wpcc('Source Code') . ' (' . _wpcc("Character count") . ': ' . mb_strlen($template) . ')',
                        'toggleText' => _wpcc('Toggle source code'),
                        'id'         => 'source-code',
                        'hidden'     => true,
                        'content'    => $template
                    ])
                </div>
            @endif

            <div class="clear-fix"></div>
        </div>
        <div class="clear-fix"></div>
    </div>
@endif

{{-- SHOW INFORMATION IF THERE ARE ANY--}}
@if(\WPCCrawler\Objects\Informing\Informer::getInfos())
    <div class="details information">
        <h2>
            <span>{{ _wpcc('Information') }}</span>
            <button class="button wpcc-button go-to-top">{{ _wpcc('Go to top') }}</button>
        </h2>
        <div class="inside">
            @include('partials.info-list', ['noTitle' => true])
        </div>
    </div>
@endif

{{-- SHOW OTHER POST DETAIL VIEWS --}}
@if (isset($postDetailViews))
    {!! $postDetailViews !!}
@endif

{{-- POST DETAILS --}}
<div class="details" id="details">
    <h2>
        <span>{{ _wpcc('Details') }}</span>
        <button class="button wpcc-button go-to-top">{{ _wpcc('Go to top') }}</button>
    </h2>
    <div class="inside">
        @include('site-tester.partial.detail-table', [
            'tableData' => $info
        ])

        {{-- POST DATA--}}
        @if(isset($data))
            <div class="data-container">
                <?php
                    /** @var array $data */
                    $str = (print_r($data, true));
                ?>
                @include('site-tester.partial.toggleable-textarea', [
                    'title'      => _wpcc('Data'),
                    'toggleText' => _wpcc('Toggle data'),
                    'id'         => 'post-data',
                    'hidden'     => true,
                    'content'    => $str
                ])
            </div>
        @endif

        <div class="clear-fix"></div>
        <div class="go-to-top-container">
            <button class="button wpcc-button go-to-top">{{ _wpcc('Go to top') }}</button>
        </div>

    </div>
</div>