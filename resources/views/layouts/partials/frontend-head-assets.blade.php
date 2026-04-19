<link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link
    id="bootstrap-icons-stylesheet"
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
>
<script>
    window.loadStylesheetOnce = function (id, href) {
        if (document.getElementById(id)) {
            return;
        }

        var link = document.createElement('link');
        link.id = id;
        link.rel = 'stylesheet';
        link.href = href;
        link.crossOrigin = 'anonymous';
        link.referrerPolicy = 'no-referrer';

        document.head.appendChild(link);
    };

    document.getElementById('bootstrap-icons-stylesheet')?.addEventListener('error', function () {
        window.loadStylesheetOnce(
            'bootstrap-icons-fallback',
            'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css'
        );
    });
</script>
