<script>
    // Show / hide password: <button data-toggle-password="#input-id">
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        var input = document.querySelector(button.dataset.togglePassword);
        button.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.setAttribute('aria-pressed', show ? 'true' : 'false');
            button.setAttribute('aria-label', show ? button.dataset.labelHide : button.dataset.labelShow);
            input.focus({ preventScroll: true });
        });
    });

    // Warn when Caps Lock is on while typing a password.
    document.querySelectorAll('input[data-caps]').forEach(function (input) {
        var hint = document.querySelector(input.dataset.caps);
        ['keydown', 'keyup'].forEach(function (type) {
            input.addEventListener(type, function (event) {
                if (event.getModifierState) {
                    hint.hidden = !event.getModifierState('CapsLock');
                }
            });
        });
        input.addEventListener('blur', function () { hint.hidden = true; });
    });

    // Password strength meter: 0 = too short, 1-4 = weak … strong.
    document.querySelectorAll('input[data-strength]').forEach(function (input) {
        var meter = document.querySelector(input.dataset.strength);
        var text = meter.querySelector('.strength-text');
        var labels = JSON.parse(meter.dataset.labels);
        var initial = text.textContent;
        input.addEventListener('input', function () {
            var value = input.value;
            var level = 0;
            if (value.length >= 8) {
                level = 1;
                if (value.length >= 12) { level++; }
                if (/[a-z]/.test(value) && /[A-Z]/.test(value)) { level++; }
                if (/\d/.test(value) && /[^A-Za-z0-9]/.test(value)) { level++; }
                level = Math.min(level, 4);
            }
            meter.dataset.level = value ? level : 0;
            meter.classList.toggle('is-active', value.length > 0);
            text.textContent = value ? labels[level] : initial;
        });
    });

    // "Confirm password" match check.
    document.querySelectorAll('input[data-matches]').forEach(function (input) {
        var original = document.querySelector(input.dataset.matches);
        var hint = document.getElementById(input.id + '-match');
        function check() {
            if (!input.value) {
                hint.hidden = true;
                input.setCustomValidity('');
                return;
            }
            var ok = input.value === original.value;
            hint.hidden = false;
            hint.textContent = ok ? hint.dataset.ok : hint.dataset.bad;
            hint.classList.toggle('is-ok', ok);
            hint.classList.toggle('is-bad', !ok);
            input.setCustomValidity(ok ? '' : hint.dataset.bad);
        }
        input.addEventListener('input', check);
        original.addEventListener('input', check);
    });

    // Clear a field's server error as soon as the user edits it.
    document.querySelectorAll('.auth-card .input.is-invalid').forEach(function (input) {
        input.addEventListener('input', function () {
            input.classList.remove('is-invalid');
            input.removeAttribute('aria-invalid');
        }, { once: true });
    });

    // Submit once: show a loading state and block double clicks.
    document.querySelectorAll('form[data-loading]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (form.dataset.sending) {
                event.preventDefault();
                return;
            }
            form.dataset.sending = '1';
            var button = form.querySelector('button[type=submit]');
            button.classList.add('is-loading');
            button.setAttribute('aria-busy', 'true');
        });
    });

    // Restore the form when the page comes back from the browser's back/forward cache.
    window.addEventListener('pageshow', function (event) {
        if (!event.persisted) { return; }
        document.querySelectorAll('form[data-loading]').forEach(function (form) {
            delete form.dataset.sending;
            var button = form.querySelector('button[type=submit]');
            button.classList.remove('is-loading');
            button.removeAttribute('aria-busy');
        });
    });
</script>
