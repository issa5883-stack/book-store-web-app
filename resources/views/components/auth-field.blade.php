{{--
    One labelled input for the sign-in / sign-up forms: leading icon, inline error,
    a show/hide button on password fields, and optional live helpers:
    - strength: shows a password strength meter under the field
    - matches:  id of the password input this one must repeat
--}}
@props([
    'name',
    'label',
    'type' => 'text',
    'icon' => null,
    'id' => null,
    'strength' => false,
    'matches' => null,
])
@php
    $id ??= $name;
    $isPassword = $type === 'password';
    $hasError = $errors->has($name);
    $describedBy = collect([
        $hasError ? $id.'-error' : null,
        $strength ? $id.'-strength' : null,
        $matches ? $id.'-match' : null,
    ])->filter()->implode(' ');
@endphp
<div class="field">
    <label for="{{ $id }}">{{ $label }}</label>
    <div @class(['input-wrap', 'has-icon' => $icon, 'has-toggle' => $isPassword])>
        @if ($icon)
            <x-icon :name="$icon" class="input-icon" />
        @endif
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            @unless ($isPassword) value="{{ old($name) }}" @endunless
            @if ($hasError) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            @if ($strength) data-strength="#{{ $id }}-strength" @endif
            @if ($matches) data-matches="#{{ $matches }}" @endif
            @if ($isPassword) data-caps="#{{ $id }}-caps" @endif
            {{ $attributes->class(['input', 'is-invalid' => $hasError]) }}
        >
        @if ($isPassword)
            <button type="button" class="input-toggle" data-toggle-password="#{{ $id }}"
                    aria-controls="{{ $id }}" aria-pressed="false"
                    aria-label="{{ __('Show password') }}"
                    data-label-show="{{ __('Show password') }}" data-label-hide="{{ __('Hide password') }}">
                <x-icon name="eye" class="when-hidden" />
                <x-icon name="eye-off" class="when-shown" />
            </button>
        @endif
    </div>

    @if ($hasError)
        <div class="error-text" id="{{ $id }}-error"><x-icon name="alert" /> {{ $errors->first($name) }}</div>
    @endif

    @if ($isPassword)
        <div class="hint caps-hint" id="{{ $id }}-caps" hidden><x-icon name="alert" /> {{ __('Caps Lock is on') }}</div>
    @endif

    @if ($strength)
        <div class="strength" id="{{ $id }}-strength" data-level="0" aria-live="polite"
             data-labels="{{ json_encode([__('Too short'), __('Weak'), __('Fair'), __('Good'), __('Strong')]) }}">
            <span class="strength-bars" aria-hidden="true"><i></i><i></i><i></i><i></i></span>
            <span class="strength-text">{{ __('At least 8 characters') }}</span>
        </div>
    @endif

    @if ($matches)
        <div class="hint match-hint" id="{{ $id }}-match" aria-live="polite" hidden
             data-ok="{{ __('Passwords match') }}" data-bad="{{ __('Passwords do not match') }}"></div>
    @endif
</div>
