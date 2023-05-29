<?php /** @var array $testHistory */ ?>
<div class="details test-history" id="test-history">

    {{-- TITLE --}}
    <h2>
        <span>{{ _wpcc('Recent Tests') }}</span>
        <div class="toggle-indicator">
            <span class="dashicons toggle dashicons-arrow-up"></span>
        </div>
    </h2>

    {{-- HISTORY --}}
    <div class="inside">
        @if($testHistory)
        <table>
            {{-- TABLE HEAD--}}
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>{{ _wpcc("Site") }}</th>
                    <th>{{ _wpcc("Test Type") }}</th>
                    <th>{{ _wpcc("Test URL") }}</th>
                    <th class="delete-all-container"><a href="#" role="button" class="delete-all">{{ _wpcc('Delete All') }}</a></th>
                </tr>
            </thead>

            {{-- HISTORY ITEMS --}}
            <tbody>
                <?php $i = sizeof($testHistory); ?>
                @foreach($testHistory as $history)
                    @include('site-tester.partial.test-history-item', [
                        'number'    => $i--,
                        'siteId'    => $history['siteId'],
                        'siteName'  => $history['siteName'],
                        'testName'  => $history['testName'],
                        'testKey'   => $history['testKey'],
                        'testUrl'   => $history['testUrl']
                    ])
                @endforeach
            </tbody>
        </table>
        @else
            <span>
                {{ _wpcc("No previous tests.") }}
            </span>
        @endif
    </div>

</div>