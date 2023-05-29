{{--
    Required variables:
        string $message: The message to be shown. It can be HTML.
--}}

@if (isset($message))
    {!! $message !!}
@endif

@include('partials.info-list')