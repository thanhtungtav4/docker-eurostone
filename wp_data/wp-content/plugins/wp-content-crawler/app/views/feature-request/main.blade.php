<?php

$buttonText  = _wpcc('Submit Feature Request');
$buttonTitle = _wpcc('A confirmation email will be sent after you hit the button.');

?>

<div class="wrap container-feature-request">

    <h1>{{ _wpcc('Feature Request') }}</h1>

    {{-- CONFIRMATION MESSAGE --}}
    @if (isset($confirmationMessage) && $confirmationMessage)
        @include('partials.alert', [
            'message' => $confirmationMessage
        ])
    @endif

    @include('partials.success-alert')

    <form action="admin-post.php" method="post" id="post">
        {{-- ADD NONCE AND ACTION --}}
        @include('partials.form-nonce-and-action')

        {{-- SUBMIT BUTTON --}}
        @include('form-items.partials.form-button-container', ['class' => 'top right', 'id' => 'submit-top', 'dataPlacement' => 'left'])

        {{--<input type="hidden" name="action" value="general_settings">--}}
        <div class="details">
            <div class="inside">
                <div class="panel-wrap wcc-settings-meta-box wcc-feature-request">

                    @include('partials/form-error-alert')

                    @include('feature-request/form')

                </div>
            </div>
        </div>

        {{-- SUBMIT BUTTON --}}
        @include('form-items.partials.form-button-container', ['id' => 'submit-bottom'])
    </form>

</div>