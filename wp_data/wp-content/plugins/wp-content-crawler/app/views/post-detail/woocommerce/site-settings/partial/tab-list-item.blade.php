{{--
    Required variables:
        string $href: Target tab content container's ID. E.g. #tab-general
        string $title: Title of the tab

    Optional variables:
        bool $active: True if the tab is currently active.
        string $icon: A dashicon name to be used in the link

--}}

<li class="
        @if(isset($active) && $active) active @endif
        @if(isset($class) && $class) {{ $class }} @endif
    ">
    <a role="button" data-tab="{{ $href }}">
        @if(isset($icon) && $icon)<span class="icon dashicons dashicons-{{ $icon }}"></span>@endif
        <span>{{ $title }}</span>
    </a>
</li>