{{--
    Required variables are the same as the required variables of the parent view.
--}}

<?php

$apiRetrievalUrl = 'https://tech.yandex.com/translate/';
$apiRetrievalAnchor = sprintf('<a href="%2$s" target="_blank">%1$s</a>', _wpcc('Click here to get your API key.'), $apiRetrievalUrl);
$suffixApiKey = 'api_key';

?>

@extends('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [$suffixApiKey],
])

@section('api-options')

    {{-- YANDEX TRANSLATE - API KEY --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixApiKey, $clientClass),
        'title' => _wpcc('API Key'),
        'info'  => _wpcc('API key retrieved from Yandex Translate.') . ' ' . $apiRetrievalAnchor,
        'class' => $clientKey
    ])

@overwrite