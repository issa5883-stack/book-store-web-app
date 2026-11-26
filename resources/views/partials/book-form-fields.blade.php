{{-- Shared by user requests and admin uploads. Expects $categories and optional $book. --}}
@php($book = $book ?? null)
<div class="form-grid">
    <div class="field full">
        <label for="title">{{ __('Book title') }} <span class="req">*</span></label>
        <input id="title" name="title" class="input @error('title') is-invalid @enderror" value="{{ old('title', $book?->title) }}" placeholder="{{ __('e.g. Clean Code') }}" required>
        @error('title') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="author">{{ __('Author') }} <span class="req">*</span></label>
        <input id="author" name="author" class="input @error('author') is-invalid @enderror" value="{{ old('author', $book?->author) }}" placeholder="{{ __('Author name') }}" required>
        @error('author') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="category_id">{{ __('Category') }}</label>
        <select id="category_id" name="category_id" class="select @error('category_id') is-invalid @enderror">
            <option value="">{{ __('— Choose a category —') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $book?->category_id) == $category->id)>{{ $category->localizedName() }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label for="language">{{ __('Language') }}</label>
        <input id="language" name="language" class="input @error('language') is-invalid @enderror" value="{{ old('language', $book?->language) }}" placeholder="{{ __('English, Arabic…') }}">
        @error('language') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div>
            <label for="published_year">{{ __('Year') }}</label>
            <input id="published_year" type="number" name="published_year" class="input @error('published_year') is-invalid @enderror" value="{{ old('published_year', $book?->published_year) }}" placeholder="2024">
            @error('published_year') <div class="error-text">{{ $message }}</div> @enderror
        </div>
        <div>
            <label for="pages">{{ __('Pages') }}</label>
            <input id="pages" type="number" name="pages" class="input @error('pages') is-invalid @enderror" value="{{ old('pages', $book?->pages) }}" placeholder="320">
            @error('pages') <div class="error-text">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="field full">
        <label for="description">{{ __('Description') }} <span class="req">*</span></label>
        <textarea id="description" name="description" class="textarea @error('description') is-invalid @enderror" placeholder="{{ __('What is this book about?') }}" required>{{ old('description', $book?->description) }}</textarea>
        @error('description') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label>{{ __('Book file (PDF)') }} @if (! $book)<span class="req">*</span>@endif</label>
        <label class="dropzone @error('pdf') is-invalid @enderror">
            <input type="file" name="pdf" accept="application/pdf" @required(! $book)>
            <x-icon name="file" />
            <strong>{{ $book ? __('Replace the PDF file') : __('Click or drop your PDF here') }}</strong>
            <span class="small">{{ __('PDF only · up to 50 MB') }}</span>
            <span class="file-name">@if ($book) {{ __('Current:') }} {{ basename($book->pdf) }} @endif</span>
        </label>
        @error('pdf') <div class="error-text">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label>{{ __('Cover image') }}</label>
        <label class="dropzone @error('cover') is-invalid @enderror">
            <input type="file" name="cover" accept="image/*">
            @if ($book?->cover)
                <img class="cover-preview" src="{{ $book->coverUrl() }}" alt="{{ $book->title }}">
            @else
                <img class="cover-preview" alt="" hidden>
                <x-icon name="image" />
            @endif
            <strong>{{ $book?->cover ? __('Replace the cover') : __('Upload a cover (optional)') }}</strong>
            <span class="small">{{ __('JPG, PNG or WEBP · up to 4 MB') }}</span>
            <span class="file-name">@if ($book?->cover) {{ __('Current:') }} {{ basename($book->cover) }} @endif</span>
        </label>
        @error('cover') <div class="error-text">{{ $message }}</div> @enderror
    </div>
</div>
