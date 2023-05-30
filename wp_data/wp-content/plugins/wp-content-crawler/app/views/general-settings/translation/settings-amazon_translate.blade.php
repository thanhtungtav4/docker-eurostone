{{--
    Required variables are the same as the required variables of the parent view.
--}}

<?php

$videoUrl     = 'https://www.youtube.com/watch?v=yRzzUjE0rzk';

$suffixAccessKey    = 'access_key';
$suffixSecret       = 'secret';
$suffixRegion       = 'region';

?>

@extends('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [
        $suffixAccessKey,
        $suffixSecret,
        $suffixRegion
    ],
])

@section('api-options')

    {{-- AMAZON TRANSLATE - ACCESS KEY --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixAccessKey, $clientClass),
        'title' => _wpcc('Access Key'),
        'info'  => _wpcc('Access key retrieved from Amazon Translate.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ])

    {{-- AMAZON TRANSLATE - SECRET --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixSecret, $clientClass),
        'title' => _wpcc('Secret'),
        'info'  => _wpcc('Secret retrieved from Amazon Translate.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ])

    {{-- AMAZON TRANSLATE - REGION --}}
    @include('form-items.combined.select-with-label', [
        'name'      => $service->getOptionKey($suffixRegion, $clientClass),
        'title'     => _wpcc('Region'),
        'info'      => _wpcc('Region to which the API requests will be sent. You can select the region closest to where your server is.'),
        'options'   => \WPCCrawler\Objects\Transformation\Translation\Clients\AmazonTranslateAPIClient::getRegions(),
        'class'     => $clientKey
    ])

@overwrite