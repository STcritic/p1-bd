@php $en = ($lang ?? 'pt') === 'en'; @endphp
<!DOCTYPE html>
<html lang="{{ $lang ?? 'pt' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $en ? 'Link expired' : 'Link expirado' }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="portal-body">
<div class="portal-wrap portal-wrap--centered">
    <div class="portal-state-card portal-state-card--expired">
        <div class="portal-state-icon">⊘</div>
        <h1>{{ $en ? 'Diagnostic link expired' : 'Link de diagnóstico expirado' }}</h1>
        <p>{{ $en ? 'This link is no longer active. Contact the BD team to receive a new diagnostic link.' : 'Este link já não está activo. Contacte a equipa BD para receber um novo link de diagnóstico.' }}</p>
    </div>
</div>
</body>
</html>
