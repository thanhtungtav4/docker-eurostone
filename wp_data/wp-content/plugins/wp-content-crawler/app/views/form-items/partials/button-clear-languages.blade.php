<div class="input-container">
    <div class="input-group button-input-group">

        @include('form-items.partials.single-button', [
            'text'          => _wpcc('Clear languages'),
            'buttonClass'   => "clear-languages {$class}",
        ])

    </div>
</div>
@include('partials/test-result-container')