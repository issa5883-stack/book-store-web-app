@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <section class="section">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">{{ __('Home') }}</a> / <a href="{{ route('books.index') }}">{{ __('Books') }}</a>
                @if ($book->category)
                    / <a href="{{ route('books.index', ['category' => $book->category_id]) }}">{{ $book->category->localizedName() }}</a>
                @endif
            </div>

            <div class="book-detail" style="margin-top: 1.5rem;">
                <x-book-cover :book="$book" />

                <div>
                    @if ($book->category)
                        <span class="chip" style="margin-bottom: 1rem;">{{ $book->category->localizedName() }}</span>
                    @endif
                    <h1>{{ $book->title }}</h1>
                    <div class="by">{{ __('by') }} <strong>{{ $book->author }}</strong></div>

                    <div class="btn-row">
                        <a href="{{ route('books.download', $book) }}" class="btn btn-primary btn-lg"><x-icon name="download" /> {{ __('Download PDF') }}</a>
                        <a href="{{ route('books.read', $book) }}" @auth target="_blank" @endauth class="btn btn-light btn-lg"><x-icon name="eye" /> {{ __('Read online') }}</a>
                    </div>

                    <div class="facts">
                        <div><span>{{ __('Pages') }}</span><strong>{{ $book->pages ?? '—' }}</strong></div>
                        <div><span>{{ __('Language') }}</span><strong>{{ $book->language ?? '—' }}</strong></div>
                        <div><span>{{ __('Year') }}</span><strong>{{ $book->published_year ?? '—' }}</strong></div>
                        <div><span>{{ __('Downloads') }}</span><strong>{{ number_format($book->downloads) }}</strong></div>
                    </div>

                    <h3>{{ __('About this book') }}</h3>
                    <p class="description" dir="auto">{{ $book->description }}</p>

                    <p class="muted small" style="margin-top: 1.5rem;">
                        <x-icon name="upload" /> {{ __('Shared by :name', ['name' => $book->uploaderName()]) }} · {{ $book->created_at->translatedFormat('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    @if ($relatedBooks->isNotEmpty())
        <section class="section" style="padding-top: 0;">
            <div class="container">
                <div class="section-head"><h2>{{ __('You may also like') }}</h2></div>
                <div class="books-grid">
                    @foreach ($relatedBooks as $related)
                        <x-book-card :book="$related" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
