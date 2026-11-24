@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('actions')
    <a href="{{ route('admin.books.create') }}" class="btn btn-primary btn-sm"><x-icon name="upload" /> {{ __('Upload a book') }}</a>
@endsection

@section('content')
    <div class="welcome">
        <div>
            <h2>{{ __('Welcome back, :name', ['name' => $currentAdmin->name]) }} </h2>
            <p>
                @if ($stats['pending'])
                    {!! trans_choice('You have <strong>:count</strong> book request waiting for review.|You have <strong>:count</strong> book requests waiting for review.', $stats['pending']) !!}
                @else
                    {{ __('All caught up — there are no pending requests.') }}
                @endif
            </p>
        </div>
        <a href="{{ route('admin.requests.index') }}" class="btn btn-white"><x-icon name="inbox" /> {{ __('Review requests') }}</a>
    </div>

    <div class="stats-grid">
        <div class="card stat"><span class="stat-icon tone-indigo"><x-icon name="book" /></span><div><strong>{{ number_format($stats['books']) }}</strong><span>{{ __('Published books') }}</span></div></div>
        <div class="card stat"><span class="stat-icon tone-amber"><x-icon name="clock" /></span><div><strong>{{ number_format($stats['pending']) }}</strong><span>{{ __('Pending requests') }}</span></div></div>
        <div class="card stat"><span class="stat-icon tone-green"><x-icon name="users" /></span><div><strong>{{ number_format($stats['users']) }}</strong><span>{{ __('Registered users') }}</span></div></div>
        <div class="card stat"><span class="stat-icon tone-sky"><x-icon name="tag" /></span><div><strong>{{ number_format($stats['categories']) }}</strong><span>{{ __('Categories') }}</span></div></div>
        <div class="card stat"><span class="stat-icon tone-pink"><x-icon name="download" /></span><div><strong>{{ number_format($stats['downloads']) }}</strong><span>{{ __('Total downloads') }}</span></div></div>
    </div>

    <div class="dash-grid">
        <div class="card">
            <div class="card-head">
                <h3>{{ __('Latest requests') }}</h3>
                <a href="{{ route('admin.requests.index') }}" class="btn btn-light btn-sm">{{ __('View all') }}</a>
            </div>
            @if ($pendingBooks->isEmpty())
                <x-empty-state icon="check-circle" :title="__('No pending requests')" :text="__('New user submissions will appear here.')" />
            @else
                <ul class="list">
                    @foreach ($pendingBooks as $book)
                        <li>
                            <div class="cell-book" style="min-width: 0;">
                                <x-book-cover :book="$book" />
                            </div>
                            <div class="grow">
                                <strong>{{ $book->title }}</strong>
                                <div class="muted small">{{ __('by') }} {{ $book->user?->name }} · {{ $book->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('admin.requests.show', $book) }}" class="btn btn-primary btn-sm">{{ __('Review') }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card">
            <div class="card-head">
                <h3>{{ __('Recently published') }}</h3>
                <a href="{{ route('admin.books.index') }}" class="btn btn-light btn-sm">{{ __('All books') }}</a>
            </div>
            @if ($latestBooks->isEmpty())
                <x-empty-state :title="__('No books yet')" />
            @else
                <ul class="list">
                    @foreach ($latestBooks as $book)
                        <li>
                            <div class="grow">
                                <strong>{{ $book->title }}</strong>
                                <div class="muted small">{{ $book->author }}</div>
                            </div>
                            <span class="muted small"><x-icon name="download" /> {{ $book->downloads }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
