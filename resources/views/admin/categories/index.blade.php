@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
    <div class="dash-grid" style="grid-template-columns: 1.4fr 1fr; align-items: start;">
        <div class="card">
            <div class="card-head"><h3>All categories ({{ $categories->count() }})</h3></div>
            @if ($categories->isEmpty())
                <x-empty-state icon="tag" title="No categories yet" text="Add your first category using the form." />
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Name</th><th>Books</th><th></th></tr></thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="inline-form">
                                            @csrf
                                            @method('PUT')
                                            <input name="name" class="input" value="{{ $category->name }}" style="padding: .45rem .7rem;" required>
                                            <button type="submit" class="btn btn-light btn-sm btn-icon" title="Save"><x-icon name="check" /></button>
                                        </form>
                                    </td>
                                    <td><span class="chip">{{ $category->books_count }}</span></td>
                                    <td>
                                        <div class="actions">
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" data-confirm="Delete this category? Its books will become uncategorised.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger-soft btn-sm btn-icon" title="Delete"><x-icon name="trash" /></button>
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

        <div class="card">
            <div class="card-head"><h3>Add category</h3></div>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="card-body">
                @csrf
                <div class="field">
                    <label for="name">Name</label>
                    <input id="name" name="name" class="input" placeholder="e.g. Philosophy" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;"><x-icon name="plus" /> Add category</button>
            </form>
        </div>
    </div>
@endsection
