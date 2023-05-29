<?php

/** @var \WPCCrawler\Objects\Crawling\Data\Taxonomy\TaxonomyItem $item */
$content = $item->getData();

?>

<div class="post-taxonomy-item">
    {{-- TAXONOMY NAME --}}
    <div class="post-taxonomy-name">
        <span class="name">{{ _wpcc("Taxonomy") }}</span>
        <span>{{ $item->getTaxonomy() }}</span>
    </div>

    {{-- APPEND --}}
    <div class="post-meta-append">
        <span class="name">{{ _wpcc("Append") }}</span>
        <span class="dashicons dashicons-{{ $item->isAppend() ? 'yes' : 'no' }}"></span>
    </div>

    {{-- TAXONOMY VALUE --}}
    <div class="post-taxonomy-value">
        <span class="name">{{ _wpcc("Value") }}</span>
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