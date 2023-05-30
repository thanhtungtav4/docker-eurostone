{{--
    Required variables:
        DashboardPost[] $posts

    Optional variables:
        string $tableClass
--}}

<?php
    $isRecrawl = isset($type) && $type && $type != 'crawl';
    $now = strtotime(current_time('mysql'));
?>

{{-- TABLE --}}
<table class="section-table {{ isset($tableClass) && $tableClass ? $tableClass : '' }}" @if(isset($id) && $id) id="{{ $id }}" @endif>

    {{-- THEAD --}}
    <thead>
        <tr>
            <th>{{ _wpcc("Post") }}</th>
            <th>{{ $isRecrawl ? _wpcc("Recrawled") : _wpcc("Saved") }}</th>
            @if($isRecrawl)
                <th class="col-update-count">{{ _wpcc("Update Count") }}</th>
            @endif
        </tr>
    </thead>

    {{-- TBODY --}}
    <tbody>
        @foreach($posts as $dashboardPost)
            @if(!($dashboardPost instanceof \WPCCrawler\Objects\Dashboard\DashboardPost) || !$dashboardPost->getUrlTuple()->getSite())
                @continue
            @endif

            <?php
                /** @var \WPCCrawler\Objects\Dashboard\DashboardPost $dashboardPost */
                $post = $dashboardPost->getPost();
                $urlTuple = $dashboardPost->getUrlTuple();
                $site = $urlTuple->getSite();
            ?>
            <tr>
                {{-- POST --}}
                <td class="col-post">
                    {{-- TITLE --}}
                    <div class="post-title">
                        <a href="{!! get_permalink($post->ID) !!}" target="_blank">
                            {{ $post->post_title }}
                        </a>

                        {{-- EDIT LINK --}}
                        <span class="edit-link">
                            - <a href="{!! get_edit_post_link($post->ID) !!}" target="_blank">
                                {{ _wpcc("Edit") }}
                            </a>
                        </span>
                    </div>

                    {{-- DETAILS --}}
                    <div class="post-details">
                        {{-- SITE --}}
                        @include('dashboard.partials.site-link', ['site' => $site])

                        {{-- POST TYPE --}}
                        <span class="post-type">
                            ({{ $post->post_type }})
                        </span>

                        {{-- ID --}}
                        <span class="id">
                            {{ _wpcc("ID") }}: {{ $post->ID }}
                        </span> -

                        {{-- TARGET URL --}}
                        <span class="target-url">
                            <a href="{!! $urlTuple->getUrl() !!}" target="_blank">
                                {!! mb_strlen($urlTuple->getUrl()) > 255 ? mb_substr($urlTuple->getUrl(), 0, 255) . "..." : $urlTuple->getUrl() !!}
                            </a>
                        </span>
                    </div>

                </td>

                {{-- DATE --}}
                <td class="col-date">
                    {{-- Diff for humans --}}
                    <span class="diff-for-humans">
                        <?php
                            $date = $isRecrawl ? $urlTuple->getRecrawledAt() : $urlTuple->getSavedAt();
                            $timestamp = $date ? $date->getTimestamp() : -1;
                        ?>
                        @if ($timestamp === -1)
                            {{ '-' }}
                        @else
                            {{ \WPCCrawler\Utils::getDiffForHumans($timestamp) }}
                            {{ $timestamp > $now ? _wpcc("later") : _wpcc("ago") }}
                        @endif
                    </span>

                    <span class="date">
                        ({{ $timestamp === -1 ? '-' : \WPCCrawler\Utils::getDateFormatted($timestamp) }})
                    </span>
                </td>

                {{-- UPDATE COUNT --}}
                @if($isRecrawl)
                    <td class="col-update-count">
                        {{ $urlTuple->getUpdateCount() }}
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>

</table>
