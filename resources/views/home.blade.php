@extends('layouts.app')

@section('title', 'Home')

@section('page-head')
    <section class="hero">
        <div class="container">
            <div>
                <span class="eyebrow"><x-icon name="sparkles" /> Free community library</span>
                <h1>Discover, read and share <span class="highlight">great books</span>.</h1>
                <p class="lead">Thousands of readers share their favourite books here. Search the collection, download in one click, or upload your own.</p>

                <form action="{{ route('books.index') }}" method="GET" class="hero-search">
                    <input type="text" name="search" placeholder="Search by title or author…" aria-label="Search books">
                    <button class="btn btn-primary" type="submit"><x-icon name="search" /> Search</button>
                </form>

                <div class="hero-stats">
                    <div><strong>{{ number_format($stats['books']) }}</strong><span>Books</span></div>
                    <div><strong>{{ number_format($stats['categories']) }}</strong><span>Categories</span></div>
                    <div><strong>{{ number_format($stats['downloads']) }}</strong><span>Downloads</span></div>
                </div>
            </div>

            <div class="hero-books">
                @foreach ($popularBooks->take(3) as $book)
                    <x-book-cover :book="$book" />
                @endforeach
            </div>
        </div>
    </section>
@endsection

@section('content')
    @if ($categories->isNotEmpty())
        <section class="section">
            <div class="container">
                <div class="section-head">
                    <div>
                        <h2>Browse by category</h2>
                        <p>Find the right book faster.</p>
                    </div>
                    <a href="{{ route('books.index') }}" class="btn btn-light">All books <x-icon name="arrow-right" /></a>
                </div>
                <div class="cat-grid">
                    @foreach ($categories as $category)
                        <a href="{{ route('books.index', ['category' => $category->id]) }}" class="cat-card">
                            <span class="cat-icon"><x-icon name="layers" /></span>
                            <div>
                                <strong>{{ $category->name }}</strong>
                                <span>{{ $category->books_count }} {{ Str::plural('book', $category->books_count) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section" style="padding-top: 1rem;">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>Recently added</h2>
                    <p>The newest books approved by our team.</p>
                </div>
                <a href="{{ route('books.index') }}" class="btn btn-light">View all <x-icon name="arrow-right" /></a>
            </div>

            @if ($latestBooks->isEmpty())
                <div class="card"><x-empty-state title="No books yet" text="Be the first to share a book with the community." /></div>
            @else
                <div class="books-grid">
                    @foreach ($latestBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="section" style="padding-top: 1rem;">
        <div class="container">
            <div class="cta">
                <div>
                    <h2>Have a book worth sharing?</h2>
                    <p>Upload it in a few seconds. An administrator will review your request and publish it for everyone.</p>
                </div>
                @auth
                    <a href="{{ route('my-books.create') }}" class="btn btn-white btn-lg"><x-icon name="upload" /> Submit a book</a>
                @else
                    <a href="{{ route('sign_up') }}" class="btn btn-white btn-lg">Get started — it's free <x-icon name="arrow-right" /></a>
                @endauth
            </div>
        </div>
    </section>
@endsection
