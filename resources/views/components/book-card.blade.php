@props(['book'])
<a href="{{ route('books.show', $book) }}" class="book-card">
    <x-book-cover :book="$book" />
    <div class="book-info">
        <h3>{{ $book->title }}</h3>
        <div class="author">{{ $book->author }}</div>
        <div class="book-meta">
            @if ($book->category)
                <span class="chip">{{ $book->category->localizedName() }}</span>
            @else
                <span></span>
            @endif
            <span><x-icon name="download" /> {{ number_format($book->downloads) }}</span>
        </div>
    </div>
</a>
