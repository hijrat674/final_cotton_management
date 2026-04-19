<span id="bootstrapIconProbe" class="bi bi-check2 visually-hidden" aria-hidden="true"></span>
<script defer src="{{ asset('assets/vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script defer src="{{ asset('assets/js/app.js') }}"></script>
<script>
    window.addEventListener('load', function () {
        var probe = document.getElementById('bootstrapIconProbe');

        if (!probe) {
            return;
        }

        var iconContent = window.getComputedStyle(probe, '::before').getPropertyValue('content');
        var hasIcons = iconContent && !['none', 'normal', '""', "''"].includes(iconContent);

        if (!hasIcons) {
            window.loadStylesheetOnce?.(
                'bootstrap-icons-fallback',
                'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css'
            );
        }
    });
</script>
