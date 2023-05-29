{{--
    Required variables are the same as the required variables of the parent view.
--}}

<?php

$videoUrl           = 'https://www.youtube.com/watch?v=VHZIQcctixY';
$suffixClientSecret = 'client_secret';

?>

@extends('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [$suffixClientSecret],
])

@section('api-options')

    {{-- MICROSOFT TRANSLATOR TEXT - CLIENT SECRET --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixClientSecret, $clientClass),
        'title' =>  _wpcc('Client Secret'),
        'info'  =>  _wpcc('Client secret retrieved from Microsoft Azure Portal.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ])

@overwrite