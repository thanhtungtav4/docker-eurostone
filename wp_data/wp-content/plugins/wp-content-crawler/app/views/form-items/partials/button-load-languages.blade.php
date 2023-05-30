<div class="input-container">
    <div class="input-group button-input-group">

        @include('form-items.partials.single-button', [
            'text'          => $isLanguagesAvailable ? _wpcc('Refresh languages') : _wpcc('Load languages'),
            'buttonClass'   => "load-languages {$class}",
        ])

    </div>
</div>
@include('partials/test-result-container')