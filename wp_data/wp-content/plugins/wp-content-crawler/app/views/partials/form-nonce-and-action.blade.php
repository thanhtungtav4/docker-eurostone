{{--
    Required:
        string $pageActionKey

    Optional:
        bool $noNonceAndAction
--}}
<?php /** @var string $pageActionKey */ ?>
@if(!isset($noNonceAndAction) || !$noNonceAndAction)
    <?php wp_nonce_field($pageActionKey, \WPCCrawler\Environment::nonceName()); ?>

    <input type="hidden" name="action" value="{{ $pageActionKey }}" id="hiddenaction">
@endif