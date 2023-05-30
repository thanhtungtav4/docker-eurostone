@if(isset($isResponseFromCache) && $isResponseFromCache)
    <div class="from-cache">
        <span>{{ _wpcc("Response retrieved from cache.") }}</span>

        {{-- INVALIDATE CACHE --}}
        @if (isset($testUrl) && $testUrl)
            <span>
                <a role="button" class="invalidate-cache-for-this-url" data-url="{{ $testUrl }}" title="{{ _wpcc("Invalidate cache for this URL") }}">{{ _wpcc("Invalidate") }}</a>
            </span>
            <span>
                <a role="button" class="invalidate-all-test-url-caches" data-url="{{ $testUrl }}" title="{{ _wpcc("Invalidate all test URL caches") }}">{{ _wpcc("Invalidate all") }}</a>
            </span>
        @endif
    </div>
@endif