@if(isset($urls) && $urls)
    <h3>{{ _wpcc('Post URLs') }}</h3>
    @include('site-tester/urls-with-test', [
        'urls'      =>  $urls,
        'testType'  =>  \WPCCrawler\Test\Enums\TestType::POST
    ])
@endif

@if(isset($nextPageUrl) && $nextPageUrl)
    <h3>{{ _wpcc('Next Page URL') }}</h3>
    <div class="next-page-url">
        @include('site-tester/button-test-this', ['url' => $nextPageUrl, 'type' => 'test_category', 'class' => 'test-next-page'])
        <a target="_blank" href="{{ $nextPageUrl }}">{{ $nextPageUrl }}</a>
    </div>
@endif