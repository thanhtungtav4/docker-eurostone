@if(isset($message))
    <p>{!! $message !!}</p>
@endif

@foreach($results as $k => $result)
    <div><span class="inner-title">{{ $k }}</span></div>
    <div class="raw-html-find-replace-results">
        @foreach($result as $key => $value)
            <div class="result-item">
                <div><span class="result-item-title">{!! $key !!} ({{ _wpcc('Length') }}: {{ mb_strlen($value) }}):</span></div>
                @if($value)
                    <textarea class="large">{{ $value }}</textarea>
                @else
                    <span>{{ _wpcc('No HTML. If you expect HTML, make sure the HTML is valid or there is network connection.') }}</span>
                @endif
            </div>
        @endforeach
    </div>

@endforeach

@if(empty($results))
    <span class="no-result">{{ _wpcc('No result') }}</span>
@endif

{{-- "FROM CACHE" INFO --}}
@include('.partials.notification-for-url-cache')

{{-- INFO LIST --}}
@include('partials.info-list')