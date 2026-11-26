<script>
    // Show the selected file name inside upload drop zones.
    document.querySelectorAll('.dropzone input[type=file]').forEach(function (input) {
        input.addEventListener('change', function () {
            var zone = input.closest('.dropzone');
            var label = zone.querySelector('.file-name');
            var file = input.files[0];
            zone.classList.toggle('has-file', !!file);
            if (label) {
                label.textContent = file ? file.name + ' · ' + (file.size / 1048576).toFixed(2) + ' MB' : '';
            }

            // Preview a newly chosen cover image.
            var preview = zone.querySelector('.cover-preview');
            if (preview && file && file.type.indexOf('image/') === 0) {
                preview.src = URL.createObjectURL(file);
                preview.hidden = false;
                var icon = zone.querySelector('.icon');
                if (icon) {
                    icon.style.display = 'none';
                }
            }
        });
    });

    // Ask before destructive actions: <form data-confirm="...">
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    // Open <dialog> elements: <button data-dialog="id">
    document.querySelectorAll('[data-dialog]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById(button.dataset.dialog).showModal();
        });
    });
    document.querySelectorAll('[data-close]').forEach(function (button) {
        button.addEventListener('click', function () {
            button.closest('dialog').close();
        });
    });
</script>
