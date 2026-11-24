<dialog id="reject-{{ $book->id }}">
    <form action="{{ route('admin.requests.reject', $book) }}" method="POST">
        @csrf
        @method('PATCH')
        <div class="card-head">
            <h3>{{ __('Reject “:title”', ['title' => $book->title]) }}</h3>
            <button type="button" class="btn btn-ghost btn-icon" data-close><x-icon name="x" /></button>
        </div>
        <div class="card-body">
            <div class="field">
                <label for="reason-{{ $book->id }}">{{ __('Reason (shown to the user)') }} <span class="req">*</span></label>
                <textarea id="reason-{{ $book->id }}" name="rejection_reason" class="textarea" style="min-height: 100px;" maxlength="255" placeholder="{{ __('e.g. The PDF is incomplete or the content is copyrighted.') }}" required></textarea>
            </div>
            <div class="form-actions" style="margin-top: 1rem;">
                <button type="button" class="btn btn-light" data-close>{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger"><x-icon name="x" /> {{ __('Reject request') }}</button>
            </div>
        </div>
    </form>
</dialog>
