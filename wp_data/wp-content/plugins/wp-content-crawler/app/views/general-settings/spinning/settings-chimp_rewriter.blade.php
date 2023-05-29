{{--
    Required variables are the same as the required variables of the parent view.
--}}

<?php

$suffixApiKey = 'api_key';
$suffixEmail  = 'email';
$suffixAppId  = 'app_id';

$apiDocsUrl = 'https://chimprewriter.com/api/documentation/';
$transApiDocsUrl = sprintf('<a href="%1$s" target="_blank">%2$s</a>', $apiDocsUrl, _wpcc("API documentation"));
$transVisitApiPage = sprintf(_wpcc('For more information, visit %1$s section in %2$s.'), '<b>"Methods > ChimpRewrite"</b>', $transApiDocsUrl);
$transFormatEnableFeature = _wpcc('Check this to enable "%1$s" feature.');

$callbackCreateCheckboxVars = function($suffix, $title, $featureName) use (&$clientKey, &$clientClass, &$service, &$transVisitApiPage, &$transFormatEnableFeature) {
    /** @var \WPCCrawler\Objects\Transformation\Spinning\SpinningService $service */
    return [
        'name'  => $service->getOptionKey($suffix, $clientClass),
        'title' => $title,
        'info'  => sprintf($transFormatEnableFeature, $featureName) . ' ' . $transVisitApiPage,
        'class' => $clientKey
    ];
};

$checkboxOptions = [
    $callbackCreateCheckboxVars('sentence_rewrite',             _wpcc('Sentence rewrite'),              _wpcc('sentence rewrite')),
    $callbackCreateCheckboxVars('grammar_check',                _wpcc('Grammar check'),                 _wpcc('grammar check')),
    $callbackCreateCheckboxVars('reorder_paragraphs',           _wpcc('Reorder paragraphs'),            _wpcc('reorder paragraphs')),
    $callbackCreateCheckboxVars('replace_phrases_with_phrases', _wpcc('Replace phrases with phrases'),  _wpcc('replace phrases with phrases')),
    $callbackCreateCheckboxVars('spin_within_spin',             _wpcc('Spin within spin'),              _wpcc('spin within spin')),
    $callbackCreateCheckboxVars('spin_tidy',                    _wpcc('Spin tidy'),                     _wpcc('spin tidy')),
    $callbackCreateCheckboxVars('exclude_original',             _wpcc('Exclude original'),              _wpcc('exclude original')),
];

?>

@extends('general-settings.spinning.spinning-api-settings-base', [
    'apiOptionKeySuffixes' => [$suffixApiKey, $suffixEmail, $suffixAppId],
    'registrationLink'     => 'https://wpcontentcrawler--chimprewriter.thrivecart.com/chimp-rewriter-api-3000/'
])

@section('api-options')

    {{-- EMAIL --}}
    @include('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey($suffixEmail, $clientClass),
        'title'         => _wpcc('Email address'),
        'info'          => _wpcc('Email address that you used to register to Chimp Rewriter.'),
        'placeholder'   => _wpcc('Email address...'),
        'class'         => $clientKey
    ])

    {{-- API KEY --}}
    @include('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey($suffixApiKey, $clientClass),
        'title'         => _wpcc('API Key'),
        'info'          => _wpcc('API key retrieved from Chimp Rewriter.'),
        'placeholder'   => _wpcc('API key...'),
        'class'         => $clientKey
    ])

    {{-- APP ID --}}
    @include('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey($suffixAppId, $clientClass),
        'title'         => _wpcc('App ID'),
        'info'          => sprintf(_wpcc('App ID that will be sent to Chimp Rewriter. This can be %1$s characters at maximum.
            For example, you can write your domain or the name of your site.'), 100),
        'placeholder'   => _wpcc('App ID...'),
        'class'         => $clientKey
    ])

    {{-- QUALITY --}}
    @include('form-items.combined.select-with-label', [
        'name'      => $service->getOptionKey('quality', $clientClass),
        'title'     => _wpcc('Synonym Replacement Quality'),
        'info'      => _wpcc('Select the synonym replacement quality.') . ' ' . $transVisitApiPage,
        'options'   => \WPCCrawler\Objects\Transformation\Spinning\Clients\ChimpRewriterClient::getQualityOptionsForSelect(),
        'class'     => $clientKey
    ])

    {{-- PHRASE QUALITY --}}
    @include('form-items.combined.select-with-label', [
        'name'      => $service->getOptionKey('phrase_quality', $clientClass),
        'title'     => _wpcc('Phrase Replacement Quality'),
        'info'      => _wpcc('Select the phrase replacement quality.') . ' ' . $transVisitApiPage,
        'options'   => \WPCCrawler\Objects\Transformation\Spinning\Clients\ChimpRewriterClient::getPhraseQualityOptionsForSelect(),
        'class'     => $clientKey
    ])

    {{-- POS MATCH --}}
    @include('form-items.combined.select-with-label', [
        'name'      => $service->getOptionKey('pos_match', $clientClass),
        'title'     => _wpcc('POS Match'),
        'info'      => _wpcc('Select the part of speech (POS) match for a spin.') . ' ' . $transVisitApiPage,
        'options'   => \WPCCrawler\Objects\Transformation\Spinning\Clients\ChimpRewriterClient::getPosMatchOptionsForSelect(),
        'class'     => $clientKey
    ])

    {{-- LANGUAGE --}}
    @include('form-items.combined.input-with-label', [
        'name'          => $service->getOptionKey('language', $clientClass),
        'title'         => _wpcc('Language'),
        'info'          => _wpcc('Enter two-char language. This defaults to English.') . ' ' . $transVisitApiPage,
        'placeholder'   => _wpcc('Language code...'),
        'class'         => $clientKey,
    ])

    {{-- CHECKBOX OPTIONS --}}
    @foreach($checkboxOptions as $variables)
        @include('form-items.combined.checkbox-with-label', $variables)
    @endforeach

    {{-- REPLACE FREQUENCY --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey('replace_frequency', $clientClass),
        'type'  => 'number',
        'min'   => 1,
        'step'  => 1,
        'title' => _wpcc('Replace frequency'),
        'info'  => sprintf(_wpcc('Controls the percentage of words that are spun. Minimum %1$s.'), 1) . ' ' . $transVisitApiPage,
        'class' => $clientKey,
    ])

    {{-- MAX SYNONYMS --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey('max_syns', $clientClass),
        'type'  => 'number',
        'min'   => 1,
        'step'  => 1,
        'title' => _wpcc('Maximum synonyms'),
        'info'  => _wpcc('Maximum number of synonyms to be used for a word or a phrase.') . ' ' . $transVisitApiPage,
        'class' => $clientKey,
    ])

    {{-- INSTANT UNIQUE --}}
    @include('form-items.combined.select-with-label', [
        'name'      => $service->getOptionKey('instant_unique', $clientClass),
        'title'     => _wpcc('Instant unique'),
        'info'      => _wpcc('Check this to run an instance unique pass over the text.') . ' ' . $transVisitApiPage,
        'options'   => \WPCCrawler\Objects\Transformation\Spinning\Clients\ChimpRewriterClient::getInstantUniqueOptionsForSelect(),
        'class'     => $clientKey
    ])

    {{-- MAX SPIN DEPTH  --}}
    @include('form-items.combined.input-with-label', [
        'name'  => $service->getOptionKey('max_spin_depth', $clientClass),
        'type'  => 'number',
        'min'   => 0,
        'step'  => 1,
        'title' => _wpcc('Maximum spin depth'),
        'info'  => _wpcc('Define the maximum spin depth. This is only valid when getting a spintax.') . ' ' . $transVisitApiPage,
        'class' => $clientKey,
    ])

    {{-- DO NOT REWRITE --}}
    @include('form-items.combined.checkbox-with-label', [
        'name'  => $service->getOptionKey('do_not_rewrite', $clientClass),
        'title' => _wpcc('Get spintax?'),
        'info'  => _wpcc('Check this if you want to get the spinning result in spin syntax, not human readable text.') . ' ' . $transVisitApiPage,
        'class' => $clientKey,
    ])

@overwrite