@extends('layouts.app')

@section('title', __('My books'))

@section('page-head')
    <section class="page-head">
        <div class="container">
            <div>
                <div class="breadcrumb"><a href="{{ route('home') }}">{{ __('Home') }}</a> / <span>{{ __('My books') }}</span></div>
                <h1>{{ __('My books') }}</h1>
                <p class="muted" style="margin: 0;">{{ __('Track the books you submitted and their review status.') }}</p>
            </div>
            <a href="{{ route('my-books.create') }}" class="btn btn-primary"><x-icon name="plus" /> {{ __('Submit a new book') }}</a>
        </div>
    </section>
@endsection

@section('content')
    <section class="section" style="padding-top: 2rem;">
        <div class="container">
            <div class="stats-grid">
                <div class="card stat"><span class="stat-icon tone-amber"><x-icon name="clock" /></span><div><strong>{{ $counts['pending'] ?? 0 }}</strong><span>{{ __('Waiting for review') }}</span></div></div>
                <div class="card stat"><span class="stat-icon tone-green"><x-icon name="check-circle" /></span><div><strong>{{ $counts['approved'] ?? 0 }}</strong><span>{{ __('Published') }}</span></div></div>
                <div class="card stat"><span class="stat-icon tone-pink"><x-icon name="x" /></span><div><strong>{{ $counts['rejected'] ?? 0 }}</strong><span>{{ __('Rejected') }}</span></div></div>
            </div>

            <div class="tabs">
                <a href="{{ route('my-books.index') }}" @class(['active' => ! request('status')])>{{ __('All') }} <small>{{ $counts->sum() }}</small></a>
                @foreach (['pending', 'approved', 'rejected'] as $status)
                    <a href="{{ route('my-books.index', ['status' => $status]) }}" @class(['active' => request('status') === $status])>{{ __(ucfirst($status)) }} <small>{{ $counts[$status] ?? 0 }}</small></a>
                @endforeach
            </div>

            <div class="card">
                @if ($books->isEmpty())
                    <x-empty-state icon="upload" :title="__('No books here yet')" :text="__('Submit your first book and our admins will review it.')">
                        <a href="{{ route('my-books.create') }}" class="btn btn-primary"><x-icon name="plus" /> {{ __('Submit a book') }}</a>
                    </x-empty-state>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr><th>{{ __('Book') }}</th><th>{{ __('Category') }}</th><th>{{ __('Status') }}</th><th>{{ __('Submitted') }}</th><th></th></tr>
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
                                        <td>
                                            <x-status-badge :status="$book->status" />
                                            @if ($book->rejection_reason)
                                                <div class="error-text">{{ __('Reason:') }} {{ $book->rejection_reason }}</div>
                                            @endif
                                        </td>
                                        <td class="muted">{{ $book->created_at->diffForHumans() }}</td>
                                        <td>
                                            <div class="actions">
                                                @if ($book->isApproved())
                                                    <a href="{{ route('books.show', $book) }}" class="btn btn-light btn-sm"><x-icon name="eye" /> {{ __('View') }}</a>
                                                @else
                                                    <a href="{{ route('my-books.edit', $book) }}" class="btn btn-light btn-sm"><x-icon name="edit" /> {{ __('Edit') }}</a>
                                                @endif
                                                <form action="{{ route('my-books.destroy', $book) }}" method="POST" data-confirm="{{ __('Delete this book permanently?') }}">
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
        </div>
    </section>
@endsection
