<a role="button" class="toggle">{{ $toggleText }}</a>

<div class="toggleable @if(isset($hidden) && $hidden) hidden @endif" id="{{ $id }}">
    <div class="section-title">
        {{ $title }}
    </div>

    <textarea class="data" rows="16">{{ $content }}</textarea>
</div>