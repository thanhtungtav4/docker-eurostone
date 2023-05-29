<div class="url-item">
    @include('site-tester/button-test-this', ['url' => $url, 'type' => $testType])
    <a target="_blank" href="{{ $url }}">{{ $url }}</a>
</div>