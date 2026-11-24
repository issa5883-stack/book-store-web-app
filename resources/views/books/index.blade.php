@extends('layouts.app')

@section('title', __('Browse books'))

@section('page-head')
    <section class="page-head">
        <div class="container">
            <div>
                <div class="breadcrumb"><a href="{{ route('home') }}">{{ __('Home') }}</a> / <span>{{ __('Books') }}</span></div>
                <h1>{{ __('Browse the library') }}</h1>
                <p class="muted" style="margin: 0;">{{ trans_choice(':count book available|:count books available', $books->total()) }}</p>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="section" style="padding-top: 2rem;">
        <div class="container">
            <form action="{{ route('books.index') }}" method="GET" class="toolbar">
                <div class="search">
                    <x-icon name="search" />
                    <input type="text" name="search" class="input" placeholder="{{ __('Search by title or author…') }}" value="{{ request('search') }}">
                </div>
                <select name="category" class="select" style="width: auto;" onchange="this.form.submit()">
                    <option value="">{{ __('All categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="sort" class="select" style="width: auto;" onchange="this.form.submit()">
                    <option value="">{{ __('Newest first') }}</option>
                    <option value="popular" @selected(request('sort') === 'popular')>{{ __('Most downloaded') }}</option>
                    <option value="title" @selected(request('sort') === 'title')>{{ __('Title A–Z') }}</option>
                </select>
                <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
                @if (request()->hasAny(['search', 'category', 'sort']))
                    <a href="{{ route('books.index') }}" class="btn btn-light">{{ __('Reset') }}</a>
                @endif
            </form>

            @if ($books->isEmpty())
                <div class="card">
                    <x-empty-state icon="search" :title="__('No books found')" :text="__('Try another keyword or category.')" />
                </div>
            @else
                <div class="books-grid">
                    @foreach ($books as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>
                {{ $books->links('partials.pagination') }}
            @endif
        </div>
    </section>
@endsection
