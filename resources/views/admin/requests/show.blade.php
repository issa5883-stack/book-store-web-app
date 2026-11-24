@extends('layouts.admin')

@section('title', __('Review request'))

@section('actions')
    <x-status-badge :status="$book->status" />
@endsection

@section('content')
    <a href="{{ route('admin.requests.index') }}" class="btn btn-ghost btn-sm" style="margin-bottom: 1rem;"><x-icon name="arrow-left" class="flip-rtl" /> {{ __('Back to requests') }}</a>

    <div class="review-grid">
        <x-book-cover :book="$book" />

        <div class="card card-body">
            @if ($book->category)
                <span class="chip" style="margin-bottom: .75rem;">{{ $book->category->localizedName() }}</span>
            @endif
            <h2 style="font-size: 1.6rem;">{{ $book->title }}</h2>
            <p class="muted">{{ __('by') }} <strong style="color: var(--ink);">{{ $book->author }}</strong></p>

            <div class="facts" style="margin: 1.25rem 0;">
                <div><span>{{ __('Pages') }}</span><strong>{{ $book->pages ?? '—' }}</strong></div>
                <div><span>{{ __('Language') }}</span><strong>{{ $book->language ?? '—' }}</strong></div>
                <div><span>{{ __('Year') }}</span><strong>{{ $book->published_year ?? '—' }}</strong></div>
                <div><span>{{ __('Submitted') }}</span><strong>{{ $book->created_at->translatedFormat('M d') }}</strong></div>
            </div>

            <h3>{{ __('Description') }}</h3>
            <p class="description" dir="auto">{{ $book->description }}</p>

            <a href="{{ $book->pdfUrl() }}" target="_blank" class="btn btn-light"><x-icon name="file" /> {{ __('Open the PDF file') }}</a>
        </div>

        <div class="card">
            <div class="card-head"><h3>{{ __('Submitted by') }}</h3></div>
            <div class="card-body">
                <div class="cell-user" style="margin-bottom: 1.5rem;">
                    <span class="avatar avatar-lg">{{ mb_substr($book->user?->name ?? '?', 0, 1) }}</span>
                    <div><strong>{{ $book->user?->name }}</strong><div class="muted small">{{ $book->user?->email }}</div></div>
                </div>

                @if ($book->status === 'pending')
                    <form action="{{ route('admin.requests.approve', $book) }}" method="POST" style="margin-bottom: .75rem;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block btn-lg"><x-icon name="check" /> {{ __('Approve & publish') }}</button>
                    </form>
                    <button type="button" class="btn btn-danger-soft btn-block" data-dialog="reject-{{ $book->id }}"><x-icon name="x" /> {{ __('Reject') }}</button>
                    @include('admin.requests.reject-dialog', ['book' => $book])
                @else
                    <p class="muted small">
                        @if ($book->reviewed_at)
                            {{ __('This request was already :status on :date.', ['status' => __(ucfirst($book->status)), 'date' => $book->reviewed_at->translatedFormat('M d, Y')]) }}
                        @else
                            {{ __('This request was already :status.', ['status' => __(ucfirst($book->status))]) }}
                        @endif
                    </p>
                    @if ($book->rejection_reason)
                        <div class="alert alert-error">{{ $book->rejection_reason }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
