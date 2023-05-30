@extends('dashboard.partials.section', [
    'id' => 'section-whats-happening'
])

@section('content-class') whats-happening @overwrite
@section('header') @overwrite

@section('title')
    {{ _wpcc("What's happening") }}
@overwrite

@section('content')
    <?php
        $now = strtotime(current_time('mysql'));
        $classUrlCollection = 'url-collection';
        $classPostCrawl     = 'post-crawl';
        $classPostRecrawl   = 'post-recrawl';
        $classPostDelete    = 'post-delete';

        $classCountQueue        = 'queue';
        $classCountSavedPosts   = 'saved-posts';
        $classCountUpdatedPosts = 'updated-posts';
        $classCountDeletedPosts = 'deleted-posts';
    ?>

    {{-- CRON EVENTS --}}
    <h3 class="cron-events">{{ _wpcc("CRON Events") }} <span class="now">({{ sprintf(_wpcc('Now: %1$s'), \WPCCrawler\Utils::getDateFormatted(current_time('mysql'))) }})</span></h3>
    <table class="detail-card orange" id="cron-events">
        <thead>
            <tr>
                <?php
                    $tableHeadValues = [
                        _wpcc("URL Collection") => [$classUrlCollection,    \WPCCrawler\Factory::schedulingService()->eventCollectUrls],
                        _wpcc("Post Crawl")     => [$classPostCrawl,        \WPCCrawler\Factory::schedulingService()->eventCrawlPost],
                        _wpcc("Post Recrawl")   => [$classPostRecrawl,      \WPCCrawler\Factory::schedulingService()->eventRecrawlPost],
                        _wpcc("Post Delete")    => [$classPostDelete,       \WPCCrawler\Factory::schedulingService()->eventDeletePosts],
                    ];
                ?>
                <th></th>
                @foreach($tableHeadValues as $name => $value)
                    <?php
                        /** @var string[] $value */
                        $eventClass = $value[0];
                        $eventKey   = $value[1];
                    ?>
                    <th class="{{ $eventClass }}">
                        {{ $name }}
                        <div class="interval-description">{{ $dashboard->getCronEventIntervalDescription($eventKey) }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr class="events-next">
                <?php
                    /** @var \WPCCrawler\Objects\Dashboard\Dashboard $dashboard */
                    $nextEventDates = [
                        [$classUrlCollection,   $dashboard->getNextUrlCollectionDate(),    $dashboard->getNextUrlCollectionSite()],
                        [$classPostCrawl,       $dashboard->getNextPostCrawlDate(),        $dashboard->getNextPostCrawlSite()],
                        [$classPostRecrawl,     $dashboard->getNextPostRecrawlDate(),      $dashboard->getNextPostRecrawlSite()],
                        [$classPostDelete,      $dashboard->getNextPostDeleteDate(),       $dashboard->getNextPostDeleteSite()],
                    ];
                ?>
                <td>{{ _wpcc("Next") }}</td>

                @foreach($nextEventDates as $value)
                    <?php /** @var array $value */ ?>
                    <?php
                        $eventClass = $value[0];
                        $date       = $value[1];
                        $timestamp  = strtotime($date ?? '');
                        $site       = $value[2];
                    ?>
                    <td class="{{ $eventClass }}">
                        <div class="diff-for-humans">
                            {{ \WPCCrawler\Utils::getDiffForHumans(strtotime($date ?? '')) }}
                            {{ $timestamp > $now ? _wpcc("later") : _wpcc("ago") }}
                        </div>
                        <span class="date">({{ \WPCCrawler\Utils::getDateFormatted($date) }})</span>
                        @if($site)
                            <div class="next-site">
                                @include('dashboard.partials.site-link', ['site' => $site])
                            </div>
                        @endif
                    </td>
                @endforeach
            </tr>
            <tr class="events-last">
                <td>{{ _wpcc("Last") }}</td>
                <?php
                    $lastEventDates = [
                        [$classUrlCollection,   $dashboard->getLastUrlCollectionDate()],
                        [$classPostCrawl,       $dashboard->getLastPostCrawlDate()],
                        [$classPostRecrawl,     $dashboard->getLastPostRecrawlDate()],
                        [$classPostDelete,      $dashboard->getLastPostDeleteDate()],
                    ];
                ?>
                @foreach($lastEventDates as $value)
                    <?php
                        /** @var string[] $value */
                        $eventClass = $value[0];
                        $eventDate  = $value[1];
                    ?>
                    <td class="{{ $eventClass }}">
                        <div class="diff-for-humans">
                            {!! sprintf(_wpcc("%s ago"), \WPCCrawler\Utils::getDiffForHumans(strtotime($eventDate ?? ''))) !!}
                        </div>
                        <span class="date">({{ \WPCCrawler\Utils::getDateFormatted($eventDate) }})</span>
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>

    {{-- COUNTS --}}
    <h3>{{ _wpcc("Counts") }}</h3>
    <table class="detail-card counts teal" id="counts">
        <thead>
            <tr>
                <th></th>
                <th class="{{ $classCountQueue }}">{{ _wpcc("URLs in Queue") }}</th>
                <th class="{{ $classCountSavedPosts }}">{{ _wpcc("Saved Posts") }}</th>
                <th class="{{ $classCountUpdatedPosts }}">{{ _wpcc("Updated Posts") }}</th>
                <th class="{{ $classCountDeletedPosts }}">{{ _wpcc("Deleted Posts") }}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="counts-today">
                <td>{{ _wpcc("Today") }}</td>
                <td class="{{ $classCountQueue }}">{{ $dashboard->getTotalUrlsInQueueAddedToday() }}</td>
                <td class="{{ $classCountSavedPosts }}">{{ $dashboard->getTotalSavedPostsToday() }}</td>
                <td class="{{ $classCountUpdatedPosts }}">{{ $dashboard->getTotalRecrawledPostsToday() }}</td>
                <td class="{{ $classCountDeletedPosts }}">{{ $dashboard->getTotalDeletedPostsToday() }}</td>
            </tr>
            <tr class="counts-all">
                <td>{{ _wpcc("All") }}</td>
                <td class="{{ $classCountQueue }}">{{ $dashboard->getTotalUrlsInQueue() }}</td>
                <td class="{{ $classCountSavedPosts }}">{{ $dashboard->getTotalSavedPosts() }}</td>
                <td class="{{ $classCountUpdatedPosts }}">{{ $dashboard->getTotalRecrawledPosts() }}</td>
                <td class="{{ $classCountDeletedPosts }}">{{ $dashboard->getTotalDeletedPosts() }}</td>
            </tr>
        </tbody>
    </table>

    {{-- CURRENTLY - URLS --}}
    @if($dashboard->getUrlsCurrentlyBeingCrawled())
        <h3>{{ _wpcc("URLs being crawled right now") }}</h3>
        @include('dashboard.partials.table-urls', [
            'id'                => 'urls-being-crawled',
            'urls'              => $dashboard->getUrlsCurrentlyBeingCrawled(),
            'tableClass'        => 'detail-card green currently-being-crawled',
            'dateColumnName'    => _wpcc('Created'),
            'fieldName'         => 'getCreatedAt',
        ])

    @endif

    {{-- CURRENTLY - POSTS --}}
    @if($dashboard->getPostsCurrentlyBeingSaved())
        <h3>{{ _wpcc("Posts being saved right now") }}</h3>
        @include('dashboard.partials.table-posts', [
            'id'            => 'posts-being-saved',
            'posts'         => $dashboard->getPostsCurrentlyBeingSaved(),
            'tableClass'    => 'detail-card green currently-being-saved'
        ])
    @endif

@overwrite
