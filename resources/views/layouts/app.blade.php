<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PHPeste 2025' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-4 mb-4">
        <div class="container">
            <h1 class="h3 mb-0">PHPeste 2025 - Parnaíba, Piauí</h1>
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>

    <footer class="bg-light text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0 text-muted small">
                O código-fonte e os prompts que geraram este site estão disponíveis em
                <a href="https://github.com/leandrowferreira/claudioquefez" target="_blank" class="text-decoration-none">
                    https://github.com/leandrowferreira/claudioquefez
                </a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
