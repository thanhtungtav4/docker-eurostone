{{--
    Required variables:
        DashboardSite[] $activeSites
--}}

@extends('dashboard.partials.section', [
    'id' => 'section-active-sites'
])

@section('content-class') @overwrite
@section('header') @overwrite

@section('title')
    {{ _wpcc("Active sites") }} ({{ sizeof($activeSites) }})
@overwrite

@section('content')
    @if(!empty($activeSites))
        <table class="section-table detail-card white">
            <thead>
                <tr>
                    <th></th>
                    <th>{{ _wpcc("Last") }}</th>
                    <th>{{ _wpcc("Active") }}</th>
                    <th>{{ _wpcc("Today") }}</th>
                    <th>{{ _wpcc("All") }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activeSites as $activeSite)
                    <?php /** @var \WPCCrawler\Objects\Dashboard\DashboardSite $activeSite */ ?>
                    <tr>
                        <td class="site-name">
                            <a href="{!! get_edit_post_link($activeSite->getSite()->ID) !!}" target="_blank">
                                {{ $activeSite->getSite()->post_title }}
                            </a>
                        </td>
                        <td class="last-cron-dates">
                            <?php
                                $lastEventDates = [
                                    _wpcc("URL Collection") => ['date-url-collection',  $activeSite->getLastCheckedAt()],
                                    _wpcc("Post Crawl")     => ['date-post-crawl',      $activeSite->getLastCrawledAt()],
                                    _wpcc("Post Recrawl")   => ['date-post-recrawl',    $activeSite->getLastRecrawledAt()],
                                    _wpcc("Post Delete")    => ['date-post-delete',     $activeSite->getLastDeletedAt()],
                                ];
                            ?>

                            @foreach($lastEventDates as $eventName => $mValue)
                                <div class="{{ $mValue[0] }}"><span>{{ $eventName }}:</span> <span class="diff-for-humans">{!! sprintf(_wpcc('%1$s ago'), \WPCCrawler\Utils::getDiffForHumans(strtotime($mValue[1]))) !!}</span> <span class="date">({{ \WPCCrawler\Utils::getDateFormatted($mValue[1]) }})</span> </div>
                            @endforeach
                        </td>
                        <td class="active-event-types">
                            <?php
                                $activeStatuses = [
                                    _wpcc("Scheduling") => ['event-scheduling', $activeSite->isActiveScheduling()],
                                    _wpcc("Recrawling") => ['event-recrawling', $activeSite->isActiveRecrawling()],
                                    _wpcc("Deleting")   => ['event-deleting',   $activeSite->isActiveDeleting()],
                                ];
                            ?>

                            @foreach($activeStatuses as $eventName => $mValue)
                                <div class="{{ $mValue[0] }}"><span>{{ $eventName }}</span>: <span class="dashicons dashicons-{{ $mValue[1] ? 'yes' : 'no'}}"></span></div>
                            @endforeach
                        </td>
                        <td class="counts-today">
                            <?php
                                $countsToday = [
                                    _wpcc("Queue")   => ['count-queue',     $activeSite->getCountQueueToday()],
                                    _wpcc("Saved")   => ['count-saved',     $activeSite->getCountSavedToday()],
                                    _wpcc("Updated") => ['count-updated',   $activeSite->getCountRecrawledToday()],
                                    _wpcc("Deleted") => ['count-deleted',   $activeSite->getCountDeletedToday()],
                                ];
                            ?>

                            @foreach($countsToday as $mName => $mValue)
                                <div class="{{ $mValue[0] }}"><span class="name">{{ $mName }}:</span> <span class="number">{{ $mValue[1] }}</span></div>
                            @endforeach
                        </td>
                        <td class="counts-all">
                            <?php
                                $countsAll = [
                                    _wpcc("Queue")   => ['count-queue',     $activeSite->getCountQueue()],
                                    _wpcc("Saved")   => ['count-saved',     $activeSite->getCountSaved()],
                                    _wpcc("Updated") => ['count-updated',   $activeSite->getCountRecrawled()],
                                    _wpcc("Deleted") => ['count-deleted',   $activeSite->getCountDeleted()],
                                ];
                            ?>

                            @foreach($countsAll as $mName => $mValue)
                                <div class="{{ $mValue[0] }}"><span class="name">{{ $mName }}:</span> <span class="number">{{ $mValue[1] }}</span></div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else

        {{ _wpcc("No active sites.") }}

    @endif

@overwrite
