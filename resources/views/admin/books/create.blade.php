@extends('layouts.admin')

@section('title', 'Upload a book')

@section('content')
    <div style="max-width: 900px;">
        <div class="alert alert-success"><x-icon name="shield" /> <div>Books uploaded by an administrator are <strong>published immediately</strong> — no review needed.</div></div>

        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" class="card card-body">
            @csrf
            @include('partials.book-form-fields')
            <div class="form-actions">
                <a href="{{ route('admin.books.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="upload" /> Publish book</button>
            </div>
        </form>
    </div>
@endsection
