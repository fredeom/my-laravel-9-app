<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>my-laravel-9-app</title>
        <link rel="stylesheet" href="/css/simple.css">
        <link rel="stylesheet" href="/css/style_buttons.css">
        <link rel="stylesheet" href="/css/style_modal.css">
        <script src="/js/_hyperscript.min.js"></script>
        <script src="/js/htmx.min.js"></script>
        @vite([
            'resources/css/app.css',
            'resources/js/app.js'
        ])
        <script>
            document.addEventListener("htmx:configRequest", (event) => {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                event.detail.headers['X-CSRF-TOKEN'] = token;
            });
        </script>
    </head>
    <body>
        <nav>
            <ul>
                <li>
                    <a hx-get="/products" hx-target="main">Товары</a>
                </li>
                <li>
                    <a hx-get="/orders" hx-target="main">Заказы</a>
                </li>
            </ul>
        </nav>
        <main>
            <hr />
            <p>
                Добро пожаловать.
            </p>
            <p>
                Вверху можно выбрать Заказы или Товары.
            </p>
        </main>
        <div id="m"></div>
    </body>
</html>
