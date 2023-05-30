{{--
    Required variables are the same as the required variables of the parent view.
--}}

<?php

$videoUrl        = 'https://www.youtube.com/watch?v=imQd2pGj7-o';
$suffixApiKey    = 'api_key';
$suffixProjectId = 'project_id';

?>

@extends('general-settings.translation.translation-api-settings-base', [
    'apiOptionKeySuffixes' => [$suffixApiKey, $suffixProjectId],
])

@section('api-options')

    {{-- GOOGLE TRANSLATE - PROJECT ID --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixProjectId, $clientClass),
        'title' => _wpcc('Project ID'),
        'info'  => _wpcc('Project ID retrieved from Google Cloud Console.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ])

    {{-- GOOGLE TRANSLATE - API KEY --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey($suffixApiKey, $clientClass),
        'title' =>  _wpcc('API Key'),
        'info'  =>  _wpcc('API key retrieved from Google Cloud Console.') . ' ' . _wpcc_trans_how_to_get_it($videoUrl),
        'class' => $clientKey
    ])
    
@overwrite