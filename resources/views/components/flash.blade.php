@if (session('success'))
    <div class="alert alert-success"><x-icon name="check-circle" /> <div>{{ session('success') }}</div></div>
@endif

@if (session('error'))
    <div class="alert alert-error"><x-icon name="alert" /> <div>{{ session('error') }}</div></div>
@endif

@if ($errors->any())
    <div class="alert alert-error">
        <x-icon name="alert" />
        <div>
            <strong>Please fix the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
