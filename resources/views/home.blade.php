@extends('layouts.app')

@section('title', __('Discover your next book'))

@section('page-head')
    <section class="home-hero">
        <div class="container">
            <h1>{{ __('Discover your next book') }}</h1>
            <p class="lead">{{ __('Read and download free PDF books shared by our community. Every book is reviewed before it goes public.') }}</p>

            <form action="{{ route('books.index') }}" method="GET" class="hero-search" role="search">
                <x-icon name="search" />
                <input type="search" name="search" placeholder="{{ __('Search for books, authors, or categories…') }}" aria-label="{{ __('Search for books, authors, or categories…') }}">
                <button type="submit" class="btn btn-primary">{{ __('Search') }}</button>
            </form>

            <p class="home-hero-meta">
                {{ trans_choice(':count book|:count books', $stats['books']) }}
                · {{ trans_choice(':count category|:count categories', $stats['categories']) }}
                · {{ __('Free to read and download') }}
            </p>
        </div>
    </section>
@endsection

@section('content')
    @if ($popularBooks->isEmpty())
        <section class="section">
            <div class="container">
                <div class="card">
                    <x-empty-state :title="__('No books yet')" :text="__('Be the first to share a book with the library.')" />
                </div>
            </div>
        </section>
    @else
        <section class="section" id="popular">
            <div class="container">
                <div class="section-head">
                    <h2>{{ __('Popular books') }}</h2>
                    <a href="{{ route('books.index', ['source' => 'admin', 'sort' => 'popular']) }}" class="see-all">{{ __('View all') }} <x-icon name="arrow-right" class="flip-rtl" /></a>
                </div>
                <div class="books-grid">
                    @foreach ($popularBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section" id="latest" style="padding-top: 0;">
            <div class="container">
                <div class="section-head">
                    <h2>{{ __('Latest books') }}</h2>
                    <a href="{{ route('books.index', ['source' => 'admin']) }}" class="see-all">{{ __('View all') }} <x-icon name="arrow-right" class="flip-rtl" /></a>
                </div>
                <div class="books-grid">
                    @foreach ($latestBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section" id="user-books" style="padding-top: 0;">
        <div class="container">
            <div class="section-head">
                <div>
                    <h2>{{ __('Books by users') }}</h2>
                    <p>{{ __('Books shared by our members and approved by the library team.') }}</p>
                </div>
                @if ($userBooks->isNotEmpty())
                    <a href="{{ route('books.index', ['source' => 'user']) }}" class="see-all">{{ __('View all user books') }} <x-icon name="arrow-right" class="flip-rtl" /></a>
                @endif
            </div>
            @if ($userBooks->isEmpty())
                <div class="card">
                    <x-empty-state icon="upload" :title="__('No user books yet')" :text="__('Share a book you love — once our team approves it, it will appear here.')">
                        <a href="{{ route('my-books.create') }}" class="btn btn-primary"><x-icon name="upload" /> {{ __('Add a book') }}</a>
                    </x-empty-state>
                </div>
            @else
                <div class="books-grid">
                    @foreach ($userBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($categories->isNotEmpty())
        <section class="section home-band" id="categories">
            <div class="container">
                <div class="section-head">
                    <h2>{{ __('Browse categories') }}</h2>
                </div>
                <div class="category-grid category-grid--compact">
                    @foreach ($categories as $category)
                        <a href="{{ route('books.index', ['category' => $category->id]) }}" class="category-card">
                            <span class="category-icon"><x-icon name="tag" /></span>
                            <span>
                                <strong>{{ $category->localizedName() }}</strong>
                                <small>{{ trans_choice(':count book|:count books', $category->books_count) }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($popularAuthors->isNotEmpty())
        <section class="section" id="authors">
            <div class="container">
                <div class="section-head">
                    <h2>{{ __('Popular authors') }}</h2>
                </div>
                <div class="author-grid">
                    @foreach ($popularAuthors as $author)
                        <a href="{{ route('books.index', ['search' => $author->author]) }}" class="author-card">
                            <span class="avatar avatar-lg">{{ mb_substr($author->author, 0, 1) }}</span>
                            <span>
                                <strong>{{ $author->author }}</strong>
                                <small>{{ trans_choice(':count book|:count books', $author->books_count) }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section" style="padding-top: 0;">
        <div class="container">
            <div class="cta-band">
                <div>
                    <h2>{{ __('Share your book') }}</h2>
                    <p>{{ __('Have a PDF book worth reading? Upload it and our team will review it before it goes public.') }}</p>
                </div>
                <a href="{{ route('my-books.create') }}" class="btn btn-primary btn-lg"><x-icon name="upload" /> {{ __('Add a book') }}</a>
            </div>
        </div>
    </section>
@endsection
