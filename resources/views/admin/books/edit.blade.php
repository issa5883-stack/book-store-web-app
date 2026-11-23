@extends('layouts.admin')

@section('title', 'Edit book')

@section('actions')
    <x-status-badge :status="$book->status" />
@endsection

@section('content')
    <div style="max-width: 900px;">
        <form action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data" class="card card-body">
            @csrf
            @method('PUT')
            @include('partials.book-form-fields', ['book' => $book])
            <div class="form-actions">
                <a href="{{ route('admin.books.index') }}" class="btn btn-light">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="check" /> Save changes</button>
            </div>
        </form>
    </div>
@endsection
