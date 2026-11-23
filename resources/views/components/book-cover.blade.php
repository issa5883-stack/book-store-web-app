@props(['book'])
@php([$from, $to] = $book->coverColors())
<div {{ $attributes->merge(['class' => 'book-cover']) }}>
    @if ($book->cover)
        <img src="{{ $book->coverUrl() }}" alt="{{ $book->title }}" loading="lazy">
    @else
        <div class="cover-art" style="background: linear-gradient(150deg, {{ $from }}, {{ $to }});">
            <div class="cover-cat">{{ $book->category?->name ?? 'Book' }}</div>
            <div class="cover-title">{{ $book->title }}</div>
            <div class="cover-author">{{ $book->author }}</div>
        </div>
    @endif
</div>
