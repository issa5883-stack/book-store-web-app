@extends('layouts.admin')

@section('title', __('Users'))

@section('content')
    <form action="{{ route('admin.users.index') }}" method="GET" class="toolbar">
        <div class="search">
            <x-icon name="search" />
            <input type="text" name="search" class="input" placeholder="{{ __('Search by name or email…') }}" value="{{ request('search') }}">
        </div>
        <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
    </form>

    <div class="card">
        @if ($users->isEmpty())
            <x-empty-state icon="users" :title="__('No users found')" />
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>{{ __('User') }}</th><th>{{ __('Submitted') }}</th><th>{{ __('Published') }}</th><th>{{ __('Pending') }}</th><th>{{ __('Joined') }}</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="cell-user">
                                        <span class="avatar">{{ mb_substr($user->name, 0, 1) }}</span>
                                        <div><strong>{{ $user->name }}</strong><div class="muted small">{{ $user->email }}</div></div>
                                    </div>
                                </td>
                                <td>{{ $user->books_count }}</td>
                                <td><span class="badge badge-approved">{{ $user->approved_books_count }}</span></td>
                                <td><span class="badge badge-pending">{{ $user->pending_books_count }}</span></td>
                                <td class="muted">{{ $user->created_at->translatedFormat('M d, Y') }}</td>
                                <td>
                                    <div class="actions">
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" data-confirm="{{ __('Delete :name and all of their books?', ['name' => $user->name]) }}">
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
    {{ $users->links('partials.pagination') }}
@endsection
