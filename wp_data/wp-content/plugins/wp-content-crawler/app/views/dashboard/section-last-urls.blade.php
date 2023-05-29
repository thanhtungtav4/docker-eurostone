{{--
    Required variables:
        $urls: (DashboardUrlTuple[]) An array of URL tuples
--}}

@extends('dashboard.partials.section', [
    'id' => isset($id) && $id ? $id : null
])

@section('content-class') @overwrite

@section('header')
    @include('dashboard.partials.select-table-item-count')
@overwrite

@section('title')
    {{ $title }}
@overwrite

@section('content')
    @if(!empty($urls))
        @include('dashboard.partials.table-urls', [
            'urls'              => $urls,
            'fieldName'         => $fieldName,
            'dateColumnName'    => $dateColumnName,
        ])

    @else

        {{ _wpcc("No URLs.") }}

    @endif

@overwrite
