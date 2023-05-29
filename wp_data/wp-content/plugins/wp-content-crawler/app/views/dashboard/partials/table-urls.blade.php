{{--
    Required variables:
        $urls: (DashboardUrlTuple[])
        $dateColumName: (string) Name for the date column
        $fieldName: (string) Getter method name of a DashboardUrlTuple, used to get the date for the date column. The
                             method must return a DateTime or null.

    Optional variables:
        $tableClass: (string)
--}}

{{-- TABLE --}}
<table class="section-table {{ isset($tableClass) && $tableClass ? $tableClass : '' }}" @if(isset($id) && $id) id="{!! $id !!}" @endif>

    {{-- THEAD --}}
    <thead>
        <tr>
            <th>{{ _wpcc("URL") }}</th>
            <th>{{ $dateColumnName }}</th>
        </tr>
    </thead>

    {{-- TBODY --}}
    <tbody>
        @foreach($urls as $url)
            <?php /** @var \WPCCrawler\Objects\Dashboard\DashboardUrlTuple $url */ ?>
            <tr>
                {{-- URL --}}
                <td class="col-post">
                    {{-- URL --}}
                    <div class="post-title">
                        <a href="{!! $url->getUrl() !!}" target="_blank">
                            {!! mb_strlen($url->getUrl()) > 255 ? mb_substr($url->getUrl(), 0, 255) . "..." : $url->getUrl() !!}
                        </a>
                    </div>

                    {{-- DETAILS --}}
                    <div class="post-details">
                        {{-- SITE --}}
                        @if($url->getSite())
                            @include('dashboard.partials.site-link', ['site' => $url->getSite()])
                        @endif

                        {{-- ID --}}
                        <span class="id">
                            {{ _wpcc("ID") }}: {{ $url->getId() }}
                        </span>
                    </div>

                </td>

                {{-- DATE --}}
                <td class="col-date">
                    <?php
                    /** @var string $fieldName */
                    /** @var DateTime|null $date */
                    $date = method_exists($url, $fieldName) ? $url->$fieldName() : null;
                    $date = $date instanceof DateTime ? $date : null;
                    ?>
                    {{-- Diff for humans --}}
                    <span class="diff-for-humans">
                        {{ $date ? \WPCCrawler\Utils::getDiffForHumans($date->getTimestamp()) : "-" }}
                        {{ _wpcc("ago") }}
                    </span>

                    <span class="date">
                        ({{ $date ? \WPCCrawler\Utils::getDateFormatted($date->getTimestamp()) : "-" }})
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>

</table>
