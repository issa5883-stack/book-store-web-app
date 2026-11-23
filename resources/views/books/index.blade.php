@extends('layouts.app')

@section('title', 'Browse books')

@section('page-head')
    <section class="page-head">
        <div class="container">
            <div>
                <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> / <span>Books</span></div>
                <h1>Browse the library</h1>
                <p class="muted" style="margin: 0;">{{ $books->total() }} {{ Str::plural('book', $books->total()) }} available</p>
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
                    <input type="text" name="search" class="input" placeholder="Search by title or author…" value="{{ request('search') }}">
                </div>
                <select name="category" class="select" style="width: auto;" onchange="this.form.submit()">
                    <option value="">All categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="sort" class="select" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Newest first</option>
                    <option value="popular" @selected(request('sort') === 'popular')>Most downloaded</option>
                    <option value="title" @selected(request('sort') === 'title')>Title A–Z</option>
                </select>
                <button class="btn btn-primary" type="submit">Search</button>
                @if (request()->hasAny(['search', 'category', 'sort']))
                    <a href="{{ route('books.index') }}" class="btn btn-light">Reset</a>
                @endif
            </form>

            @if ($books->isEmpty())
                <div class="card">
                    <x-empty-state icon="search" title="No books found" text="Try another keyword or category." />
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
