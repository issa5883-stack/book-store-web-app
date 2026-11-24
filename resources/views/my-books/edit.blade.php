@extends('layouts.app')

@section('title', __('Edit request'))

@section('page-head')
    <section class="page-head">
        <div class="container">
            <div>
                <div class="breadcrumb"><a href="{{ route('my-books.index') }}">{{ __('My books') }}</a> / <span>{{ __('Edit') }}</span></div>
                <h1>{{ __('Edit “:title”', ['title' => $book->title]) }}</h1>
                <p class="muted" style="margin: 0;">{{ __('Saving sends the request back to the admins for review.') }}</p>
            </div>
            <x-status-badge :status="$book->status" />
        </div>
    </section>
@endsection

@section('content')
    <section class="section" style="padding-top: 2rem;">
        <div class="container" style="max-width: 860px;">
            @if ($book->rejection_reason)
                <div class="alert alert-error"><x-icon name="alert" /> <div><strong>{{ __('Rejected by admin:') }}</strong> {{ $book->rejection_reason }}</div></div>
            @endif

            <form action="{{ route('my-books.update', $book) }}" method="POST" enctype="multipart/form-data" class="card card-body">
                @csrf
                @method('PUT')
                @include('partials.book-form-fields', ['book' => $book])
                <div class="form-actions">
                    <a href="{{ route('my-books.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary"><x-icon name="check" /> {{ __('Save & resubmit') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection
