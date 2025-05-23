<!DOCTYPE html>
<html class="overflow-hidden" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', default: 'LMS') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <script src="{{ asset('js/plugin/webfont/webfont.min.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('css/fonts.min.css') }}"/>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
