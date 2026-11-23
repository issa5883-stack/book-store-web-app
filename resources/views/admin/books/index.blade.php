@extends('layouts.admin')

@section('title', 'All books')

@section('actions')
    <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> Upload a book</a>
@endsection

@section('content')
    <form action="{{ route('admin.books.index') }}" method="GET" class="toolbar">
        <div class="search">
            <x-icon name="search" />
            <input type="text" name="search" class="input" placeholder="Search title or author…" value="{{ request('search') }}">
        </div>
        <select name="category" class="select" style="width: auto;" onchange="this.form.submit()">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="select" style="width: auto;" onchange="this.form.submit()">
            <option value="">All statuses</option>
            @foreach (['approved', 'pending', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary" type="submit">Filter</button>
        @if (request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('admin.books.index') }}" class="btn btn-light">Reset</a>
        @endif
    </form>

    <div class="card">
        @if ($books->isEmpty())
            <x-empty-state title="No books found">
                <a href="{{ route('admin.books.create') }}" class="btn btn-primary"><x-icon name="plus" /> Upload a book</a>
            </x-empty-state>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Book</th><th>Category</th><th>Uploaded by</th><th>Status</th><th>Downloads</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td>
                                    <div class="cell-book">
                                        <x-book-cover :book="$book" />
                                        <div><strong>{{ $book->title }}</strong><span>{{ $book->author }}</span></div>
                                    </div>
                                </td>
                                <td>{{ $book->category?->name ?? '—' }}</td>
                                <td>{{ $book->uploaderName() }}</td>
                                <td><x-status-badge :status="$book->status" /></td>
                                <td>{{ number_format($book->downloads) }}</td>
                                <td>
                                    <div class="actions">
                                        @if ($book->status === 'pending')
                                            <a href="{{ route('admin.requests.show', $book) }}" class="btn btn-primary btn-sm">Review</a>
                                        @endif
                                        <a href="{{ $book->pdfUrl() }}" target="_blank" class="btn btn-light btn-sm btn-icon" title="Open PDF"><x-icon name="eye" /></a>
                                        <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-light btn-sm btn-icon" title="Edit"><x-icon name="edit" /></a>
                                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" data-confirm="Delete “{{ $book->title }}” permanently?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-soft btn-sm btn-icon" title="Delete"><x-icon name="trash" /></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    {{ $books->links('partials.pagination') }}
@endsection
