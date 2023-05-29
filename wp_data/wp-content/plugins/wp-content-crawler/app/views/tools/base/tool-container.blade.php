<div class="details" @if(isset($id) && $id) id="{{ $id }}" @endif>
    <h2>
        <span>@yield('title')</span>

        @if (!isset($noToggleButton) || !$noToggleButton)
            @include('partials.button-toggle-info-texts')
        @endif
    </h2>
    <div class="inside">
        @yield('content')
    </div>
</div>