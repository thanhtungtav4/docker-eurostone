<script>
    var min_refresh_interval = {{ \WPCCrawler\Objects\Dashboard\Dashboard::getMinRefreshInterval() }} - 1;
</script>

<div class="wrap container-dashboard">

    <h1>{{ _wpcc('WP Content Crawler Dashboard') }}</h1>

    {{-- "Scheduling is not active" notice --}}
    @include('dashboard.partials.notice-passive-scheduling')

    <div class="auto-refresh-container">
        {!! sprintf(
            _wpcc("Auto refresh every %s seconds"),
            '<input type="number" name="refresh" id="refresh" placeholder=">= ' . \WPCCrawler\Objects\Dashboard\Dashboard::getMinRefreshInterval() . '" title="' . sprintf(_wpcc("At least %s seconds"), \WPCCrawler\Objects\Dashboard\Dashboard::getMinRefreshInterval()) . '">'
        ) !!}

        <span class="next-refresh-in hidden">
            - {!! sprintf(_wpcc("Next refresh in %s"), '<span class="remaining">0</span>') !!}
        </span>
    </div>

    {{-- Main container --}}
    <div class="container-fluid" id="dashboard-container">
        <div class="row">
            {{-- Active sites --}}
            <div class="col col-sm-12">
                @include('dashboard.section-active-sites', [
                    'activeSites' => $dashboard->getActiveSites(),
                ])
            </div>

            <?php
            /** @var \WPCCrawler\Objects\Dashboard\Dashboard $dashboard */
            /**
             * Fires in the main row in Dashboard page, just after Active Sites section
             *
             * @param \WPCCrawler\Objects\Dashboard\Dashboard $dashboard
             * @since 1.6.3
             */
            do_action('wpcc/view/dashboard/main-row', $dashboard);

            ?>

        </div>

        <div class="row">
            <div class="col col-sm-6">
                <div class="row">
                    {{-- What's happening --}}
                    <div class="col col-sm-12">
                        @include('dashboard.section-whats-happening')
                    </div>

                    {{-- Last recrawled posts --}}
                    <div class="col col-sm-12">
                        @include('dashboard.section-last-posts', [
                            'id'                => 'section-last-recrawled-posts',
                            'title'             => _wpcc("Last recrawled posts"),
                            'posts'             => $dashboard->getLastRecrawledPosts(),
                            'type'              => 'recrawl',
                            'countOptionName'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_DASHBOARD_COUNT_LAST_RECRAWLED_POSTS,
                        ])
                    </div>

                    {{-- Last URLs marked as deleted --}}
                    <div class="col col-sm-12">
                        @include('dashboard.section-last-urls', [
                            'id'                => 'section-last-deleted-urls',
                            'title'             => _wpcc("URLs of the last deleted posts"),
                            'urls'              => $dashboard->getLastUrlsMarkedAsDeleted(),
                            'countOptionName'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_DASHBOARD_COUNT_LAST_DELETED_URLS,
                            'dateColumnName'    => _wpcc("Deleted"),
                            'fieldName'         => 'getDeletedAt',
                        ])
                    </div>

                    <?php

                    /**
                     * Fires at the end of left column in Dashboard page, just after Last Deleted URLs table
                     *
                     * @param \WPCCrawler\Objects\Dashboard\Dashboard $dashboard
                     * @since 1.6.3
                     */
                    do_action('wpcc/view/dashboard/left-col', $dashboard);

                    ?>

                </div>
            </div>

            <div class="col col-sm-6">
                <div class="row">
                    {{-- Last crawled posts --}}
                    <div class="col col-sm-12">
                        @include('dashboard.section-last-posts', [
                            'id'                => 'section-last-crawled-posts',
                            'title'             => _wpcc("Last crawled posts"),
                            'posts'             => $dashboard->getLastCrawledPosts(),
                            'countOptionName'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_DASHBOARD_COUNT_LAST_CRAWLED_POSTS,
                        ])
                    </div>

                    {{-- Last URLs added to the queue --}}
                    <div class="col col-sm-12">
                        @include('dashboard.section-last-urls', [
                            'id'                => 'section-last-urls-added-to-queue',
                            'title'             => _wpcc("Last URLs added to the queue"),
                            'urls'              => $dashboard->getLastUrlsInQueue(),
                            'countOptionName'   => \WPCCrawler\Objects\Settings\Enums\SettingKey::WPCC_DASHBOARD_COUNT_LAST_URLS,
                            'dateColumnName'    => _wpcc("Created"),
                            'fieldName'         => 'getCreatedAt',
                        ])
                    </div>

                    <?php

                    /**
                     * Fires at the end of right column in Dashboard page, just after Last Added URLs table
                     *
                     * @param \WPCCrawler\Objects\Dashboard\Dashboard $dashboard
                     * @since 1.6.3
                     */
                    do_action('wpcc/view/dashboard/right-col', $dashboard);

                    ?>
                </div>
            </div>

        </div>
    </div>


</div>
