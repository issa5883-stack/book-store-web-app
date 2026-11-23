@extends('layouts.admin')

@section('title', 'Book requests')

@section('content')
    <p class="muted">Books submitted by users. Approve them to publish, or reject with a reason the user can see.</p>

    <div class="card">
        @if ($requests->isEmpty())
            <x-empty-state icon="check-circle" title="You're all caught up" text="There are no pending requests right now." />
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr><th>Book</th><th>Submitted by</th><th>Category</th><th>Date</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $book)
                            <tr>
                                <td>
                                    <div class="cell-book">
                                        <x-book-cover :book="$book" />
                                        <div><strong>{{ $book->title }}</strong><span>{{ $book->author }}</span></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="cell-user">
                                        <span class="avatar">{{ mb_substr($book->user?->name ?? '?', 0, 1) }}</span>
                                        <div><strong>{{ $book->user?->name }}</strong><div class="muted small">{{ $book->user?->email }}</div></div>
                                    </div>
                                </td>
                                <td>{{ $book->category?->name ?? '—' }}</td>
                                <td class="muted">{{ $book->created_at->diffForHumans() }}</td>
                                <td>
                                    <div class="actions">
                                        <a href="{{ route('admin.requests.show', $book) }}" class="btn btn-light btn-sm"><x-icon name="eye" /> Review</a>
                                        <form action="{{ route('admin.requests.approve', $book) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm"><x-icon name="check" /> Approve</button>
                                        </form>
                                        <button type="button" class="btn btn-danger-soft btn-sm" data-dialog="reject-{{ $book->id }}"><x-icon name="x" /> Reject</button>
                                    </div>
                                    @include('admin.requests.reject-dialog', ['book' => $book])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    {{ $requests->links('partials.pagination') }}
@endsection
