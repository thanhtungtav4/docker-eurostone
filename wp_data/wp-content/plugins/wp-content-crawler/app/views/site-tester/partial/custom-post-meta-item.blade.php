<?php

/** @var \WPCCrawler\Objects\Crawling\Data\Meta\PostMeta $item */
$content = $item->getData();

?>

<div class="post-meta-item">
    {{-- META KEY --}}
    <div class="post-meta-key">
        <span class="name">{{ _wpcc("Meta key") }}</span>
        <span>{{ $item->getKey() }}</span>
    </div>

    {{-- MULTIPLE --}}
    <div class="post-meta-multiple">
        <span class="name">{{ _wpcc("Multiple") }}</span>
        <span class="dashicons dashicons-{{ $item->isMultiple() ? 'yes' : 'no' }}"></span>
    </div>

    {{-- META CONTENT --}}
    <div class="post-meta-content">
        <span class="name">{{ _wpcc("Content") }}</span>
        @if(is_array($content))
            <div>
                <ol>
                    @foreach($content as $value)
                        <li>{{ $value }}</li>
                    @endforeach
                </ol>
            </div>
        @else
            <span>{{ $content }}</span>
        @endif
    </div>
</div>