@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <section class="section">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> / <a href="{{ route('books.index') }}">Books</a>
                @if ($book->category)
                    / <a href="{{ route('books.index', ['category' => $book->category_id]) }}">{{ $book->category->name }}</a>
                @endif
            </div>

            <div class="book-detail" style="margin-top: 1.5rem;">
                <x-book-cover :book="$book" />

                <div>
                    @if ($book->category)
                        <span class="chip" style="margin-bottom: 1rem;">{{ $book->category->name }}</span>
                    @endif
                    <h1>{{ $book->title }}</h1>
                    <div class="by">by <strong>{{ $book->author }}</strong></div>

                    <div class="btn-row">
                        <a href="{{ route('books.download', $book) }}" class="btn btn-primary btn-lg"><x-icon name="download" /> Download PDF</a>
                        <a href="{{ $book->pdfUrl() }}" target="_blank" class="btn btn-light btn-lg"><x-icon name="eye" /> Read online</a>
                    </div>

                    <div class="facts">
                        <div><span>Pages</span><strong>{{ $book->pages ?? '—' }}</strong></div>
                        <div><span>Language</span><strong>{{ $book->language ?? '—' }}</strong></div>
                        <div><span>Year</span><strong>{{ $book->published_year ?? '—' }}</strong></div>
                        <div><span>Downloads</span><strong>{{ number_format($book->downloads) }}</strong></div>
                    </div>

                    <h3>About this book</h3>
                    <p class="description">{{ $book->description }}</p>

                    <p class="muted small" style="margin-top: 1.5rem;">
                        <x-icon name="upload" /> Shared by {{ $book->uploaderName() }} · {{ $book->created_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    @if ($relatedBooks->isNotEmpty())
        <section class="section" style="padding-top: 0;">
            <div class="container">
                <div class="section-head"><h2>You may also like</h2></div>
                <div class="books-grid">
                    @foreach ($relatedBooks as $related)
                        <x-book-card :book="$related" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
