{{--
    Required variables:
        string $info: The information to be shown
        string $name: Name/title of the information

    Optional variables:
        string $class: Class of the div element that encapsulates the information
--}}
<?php /** @var string $info */ ?>
@if($info)
    <div @if(isset($class) && $class) class="{{ $class }}" @endif>
        <span class="name">{{ $name }}</span>
        <span class="info-value">{{ $info }}</span>
    </div>
@endif