@extends('layouts.admin')

@section('title', 'Review request')

@section('actions')
    <x-status-badge :status="$book->status" />
@endsection

@section('content')
    <a href="{{ route('admin.requests.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom: 1rem;"><x-icon name="arrow-left" /> Back to requests</a>

    <div class="review-grid">
        <x-book-cover :book="$book" />

        <div class="card card-body">
            @if ($book->category)
                <span class="chip" style="margin-bottom: .75rem;">{{ $book->category->name }}</span>
            @endif
            <h2 style="font-size: 1.6rem;">{{ $book->title }}</h2>
            <p class="muted">by <strong style="color: var(--ink);">{{ $book->author }}</strong></p>

            <div class="facts" style="margin: 1.25rem 0;">
                <div><span>Pages</span><strong>{{ $book->pages ?? '—' }}</strong></div>
                <div><span>Language</span><strong>{{ $book->language ?? '—' }}</strong></div>
                <div><span>Year</span><strong>{{ $book->published_year ?? '—' }}</strong></div>
                <div><span>Submitted</span><strong>{{ $book->created_at->format('M d') }}</strong></div>
            </div>

            <h3>Description</h3>
            <p class="description">{{ $book->description }}</p>

            <a href="{{ $book->pdfUrl() }}" target="_blank" class="btn btn-light"><x-icon name="file" /> Open the PDF file</a>
        </div>

        <div class="card">
            <div class="card-head"><h3>Submitted by</h3></div>
            <div class="card-body">
                <div class="cell-user" style="margin-bottom: 1.5rem;">
                    <span class="avatar avatar-lg">{{ mb_substr($book->user?->name ?? '?', 0, 1) }}</span>
                    <div><strong>{{ $book->user?->name }}</strong><div class="muted small">{{ $book->user?->email }}</div></div>
                </div>

                @if ($book->status === 'pending')
                    <form action="{{ route('admin.requests.approve', $book) }}" method="POST" style="margin-bottom: .75rem;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block btn-lg"><x-icon name="check" /> Approve & publish</button>
                    </form>
                    <button type="button" class="btn btn-danger-soft btn-block" data-dialog="reject-{{ $book->id }}"><x-icon name="x" /> Reject</button>
                    @include('admin.requests.reject-dialog', ['book' => $book])
                @else
                    <p class="muted small">This request was already {{ $book->status }}{{ $book->reviewed_at ? ' on '.$book->reviewed_at->format('M d, Y') : '' }}.</p>
                    @if ($book->rejection_reason)
                        <div class="alert alert-error">{{ $book->rejection_reason }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
