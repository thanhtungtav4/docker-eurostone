<div class="wcc-settings-title">
    <span>{{ _wpcc('If there is a feature that you think it should be in WP Content Crawler, you can send it through this form.
        Please provide a valid email address because a confirmation link will be sent to your email.') }}</span>
</div>

<table class="wcc-settings">
    {{-- EMAIL ADDRESS --}}
    @include('form-items.combined.input-with-label', [
        'name'  => '_wpcc_feature_request_email',
        'title' => _wpcc('Your email address'),
        'type'  => 'email',
        'info'  => _wpcc('Please enter your email address. Your request will be taken into account only when you click
            the confirmation link sent to your email.'),
        'placeholder' => _wpcc('Valid email address...'),
    ])

    {{-- FEATURE REQUEST CONTENT --}}
    @include('form-items.combined.textarea-with-label', [
        'name'  => '_wpcc_feature_request_content',
        'title' => _wpcc('Feature request'),
        'info'  => sprintf(_wpcc('Enter your feature request here. You can give as much detail as you want. Actually, the more
            detail, the better. For example, along with your request, you can tell how it should work, what is its
            expected behavior, etc. Please enter at least <b>%1$s characters</b>.'), \WPCCrawler\Objects\FeatureRequest::MIN_REQUEST_CONTENT_LENGTH),
        'placeholder' => _wpcc('Feature request details...'),
    ])

</table>