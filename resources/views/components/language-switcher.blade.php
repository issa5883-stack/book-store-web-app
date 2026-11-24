{{-- Links to every supported language except the current one, e.g. "العربية" while browsing in English. --}}
@foreach (config('app.supported_locales') as $code => $language)
    @continue($code === app()->getLocale())
    <a href="{{ route('language.switch', $code) }}" lang="{{ $code }}" hreflang="{{ $code }}" title="{{ __('Change language') }}" {{ $attributes->merge(['class' => 'lang-switch']) }}>
        <x-icon name="globe" /> <span>{{ $language['name'] }}</span>
    </a>
@endforeach
