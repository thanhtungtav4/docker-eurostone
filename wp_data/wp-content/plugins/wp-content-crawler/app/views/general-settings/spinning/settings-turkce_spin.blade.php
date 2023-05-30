{{--
    Required variables are the same as the required variables of the parent view.
--}}

<?php

$registrationUrl = "http://www.turkcespin.com/";
$apiDocsUrl      = "http://www.turkcespin.com/api";

$transVisitApiPage = sprintf(_wpcc('For more information, visit %1$s page.'), '<a href="' . $apiDocsUrl . '" target="_blank">' . _wpcc('API documentation') . '</a>');

$suffixApiToken = 'api_token';

?>

@extends('general-settings.spinning.spinning-api-settings-base', [
    'apiOptionKeySuffixes'  => [$suffixApiToken],
    'registrationLink'      => $registrationUrl,
])

@section('api-options')

    {{-- API TOKEN --}}
    @include('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey($suffixApiToken, $clientClass),
        'title'         => _wpcc('API Token'),
        'info'          => _wpcc('API token retrieved from Türkçe Spin.') . ' ' . $transVisitApiPage,
        'placeholder'   => _wpcc('API token...'),
        'class'         => $clientKey
    ])

@overwrite