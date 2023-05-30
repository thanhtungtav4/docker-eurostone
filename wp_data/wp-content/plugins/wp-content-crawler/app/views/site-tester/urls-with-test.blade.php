<ul class="post-url-list">
    @foreach($urls as $i => $url)
        <li>
            <div class="number"><span>{{ $i + 1 }}.</span></div>
            <div class="controls">
                @include('site-tester/button-test-this', ['url' => $url["data"], 'type' => $testType])
            </div>
            <div class="thumbnail-container @if(isset($hideThumbnails) && $hideThumbnails) {{ 'hidden' }} @endif">
                @if(isset($url["thumbnail"]))
                    <a href="{{ $url["thumbnail"] }}"
                       target="_blank"
                       data-html="true"
                       data-wpcc-toggle="wpcc-tooltip"
                       data-placement="right"
                       title="<img src='{{ $url["thumbnail"] }}' />"
                    >
                        <img class="small" src="{{ $url["thumbnail"] }}" width="30" height="30" alt="">
                    </a>
                @endif
            </div>
            <div class="post-url">
                <a target="_blank" href="{{ $url["data"] }}">{{ $url["data"] }}</a>
            </div>
        </li>
    @endforeach
</ul>