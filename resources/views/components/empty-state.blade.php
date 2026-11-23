@props(['icon' => 'book-open', 'title', 'text' => null])
<div class="empty">
    <div class="empty-icon"><x-icon :name="$icon" /></div>
    <h3>{{ $title }}</h3>
    @if ($text)
        <p>{{ $text }}</p>
    @endif
    {{ $slot }}
</div>
