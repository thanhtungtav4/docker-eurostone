<?php /** @var array $content */ ?>

<ol>
    @foreach($content as $k => $v)
        @if (!is_array($v))
            <li>{!! $v !!}</li>
        @else
            @include('site-tester.partial.list', [
                'content' => $v
            ])
        @endif
    @endforeach
</ol>