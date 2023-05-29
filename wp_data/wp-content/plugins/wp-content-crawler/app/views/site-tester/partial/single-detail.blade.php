{{--
    Required variables:
        string $name:       Name of the detail
        mixed  $content:    Content of the detail
--}}

<tr>
    <td class="detail-name">
        <span class="detail-name">{{ $name }}</span>
    </td>
    <td class="detail-value">
        {{-- If boolean, show an icon. --}}
        @if (is_bool($content))
            <span class="dashicons dashicons-{{ $content ? 'yes' : 'no' }}"></span>

        @elseif(is_array($content))
            @include('site-tester.partial.list', [
                'content' => $content
            ])

        @elseif(!$content)
            <span class="no-result">-</span>

        @else
            {!! $content !!}
        @endif
    </td>
</tr>