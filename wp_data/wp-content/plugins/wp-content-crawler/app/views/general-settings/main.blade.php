<div class="wrap container-general-settings">
    <h1>{{ _wpcc('General Settings') }}</h1>

    @include('partials.success-alert')

    {{-- Show confirmation alert --}}
    @if (isset($confirmationMessage) && $confirmationMessage)
        @include('partials.alert', [
            'message' => $confirmationMessage
        ])
    @endif

    <form action="admin-post.php" method="post" id="post">
        {{-- Tests here will be done in PostService class. Below nonce field is there because of this. --}}

        {{-- ADD NONCE AND ACTION --}}
        @include('partials.form-nonce-and-action')

        {{-- SUBMIT BUTTON --}}
        @include('form-items.partials.form-button-container', ['class' => 'top right', 'id' => 'submit-top'])

        <div class="details">
            <div class="inside">
                <div class="panel-wrap wcc-settings-meta-box wcc-general-settings">

                    @include('partials/form-error-alert')

                    @include('general-settings/settings')

                </div>
            </div>
        </div>

        {{-- SUBMIT BUTTON --}}
        @include('form-items.partials.form-button-container', ['id' => 'submit-bottom'])

        {{-- RESET --}}
        <div class="reset-container">
            <input type="submit" name="reset" class="button right"
                   value="{{ _wpcc('Reset General Settings') }}"
                   title="{{ _wpcc('Reset the general settings to their defaults') }}"
            >
        </div>
    </form>

</div>