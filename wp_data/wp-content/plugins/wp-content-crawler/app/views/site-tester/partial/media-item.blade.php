{{--
    Required variables:
        string  $url      The URL of the media item
        ?string $editUrl  The edit URL of the media item
        iny     $imageId  The ID of the image
--}}

<?php
/** @var string $url */
/** @var string|null $editUrl */
/** @var int $imageId */
?>
<div class="media-item attachment-item">
    {{-- ITEM --}}
    <div class="media-url">
        <span>
            <a href="{{ $editUrl ?? '#' }}" target="_blank" class="item-url"
                data-html="true" data-wpcc-toggle="wpcc-tooltip" title="<img src='{{ $url }}'>"
            >
                {{ $editUrl ?? $url }}
            </a>
        </span>
    </div>

    {{-- IMAGE ID --}}
    @include('site-tester.partial.attachment-item-info', [
        'name'  => _wpcc('ID'),
        'info'  => $imageId,
        'class' => 'media-id',
    ])
</div>