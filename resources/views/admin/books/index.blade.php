@extends('layouts.admin')

@section('title', __('All books'))

@section('actions')
    <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm"><x-icon name="plus" /> {{ __('Upload a book') }}</a>
@endsection

@section('content')
    <form action="{{ route('admin.books.index') }}" method="GET" class="toolbar">
        <div class="search">
            <x-icon name="search" />
            <input type="text" name="search" class="input" placeholder="{{ __('Search title or author…') }}" value="{{ request('search') }}">
        </div>
        <select name="category" class="select" style="width: auto;" onchange="this.form.submit()">
            <option value="">{{ __('All categories') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="status" class="select" style="width: auto;" onchange="this.form.submit()">
            <option value="">{{ __('All statuses') }}</option>
            @foreach (['approved', 'pending', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst($status)) }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary" type="submit">{{ __('Filter') }}</button>
        @if (request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('admin.books.index') }}" class="btn btn-light">{{ __('Reset') }}</a>
        @endif
    </form>

    <div class="card">
        @if ($books->isEmpty())
            <x-empty-state :title="__('No books found')">
                <a href="{{ route('admin.books.create') }}" class="btn btn-primary"><x-icon name="plus" /> {{ __('Upload a book') }}</a>
            </x-empty-state>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>{{ __('Book') }}</th><th>{{ __('Category') }}</th><th>{{ __('Uploaded by') }}</th><th>{{ __('Status') }}</th><th>{{ __('Downloads') }}</th><th></th></tr>
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
                                            <a href="{{ route('admin.requests.show', $book) }}" class="btn btn-primary btn-sm">{{ __('Review') }}</a>
                                        @endif
                                        <a href="{{ $book->pdfUrl() }}" target="_blank" class="btn btn-light btn-sm btn-icon" title="{{ __('Open PDF') }}"><x-icon name="eye" /></a>
                                        <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-light btn-sm btn-icon" title="{{ __('Edit') }}"><x-icon name="edit" /></a>
                                        <form action="{{ route('admin.books.destroy', $book) }}" method="POST" data-confirm="{{ __('Delete “:title” permanently?', ['title' => $book->title]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger-soft btn-sm btn-icon" title="{{ __('Delete') }}"><x-icon name="trash" /></button>
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
