<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#073b73">
    <title>@yield('title', 'Portal BD') | Business Diversity</title>
    <link rel="icon" href="{{ asset('favicon/favicon.ico') }}">
    @vite('resources/js/app.js')
</head>
<body class="announcement-admin-body">
    @yield('content')
</body>
</html>
