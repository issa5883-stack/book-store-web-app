@extends('layouts.app')

@section('title', 'Submit a book')

@section('page-head')
    <section class="page-head">
        <div class="container">
            <div>
                <div class="breadcrumb"><a href="{{ route('my-books.index') }}">My books</a> / <span>Submit</span></div>
                <h1>Submit a book</h1>
                <p class="muted" style="margin: 0;">Your book will be published after an administrator approves it.</p>
            </div>
        </div>
    </section>
@endsection

@section('content')
    <section class="section" style="padding-top: 2rem;">
        <div class="container" style="max-width: 860px;">
            <div class="alert alert-warning"><x-icon name="clock" /> <div>Requests are usually reviewed within 24 hours. You can follow the status from <a href="{{ route('my-books.index') }}">My books</a>.</div></div>

            <form action="{{ route('my-books.store') }}" method="POST" enctype="multipart/form-data" class="card card-body">
                @csrf
                @include('partials.book-form-fields')
                <div class="form-actions">
                    <a href="{{ route('my-books.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary"><x-icon name="upload" /> Send for review</button>
                </div>
            </form>
        </div>
    </section>
@endsection
