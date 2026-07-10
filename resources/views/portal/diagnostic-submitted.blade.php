@php $en = ($lang ?? 'pt') === 'en'; @endphp
<!DOCTYPE html>
<html lang="{{ $lang ?? 'pt' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $en ? 'Diagnostic submitted' : 'Diagnóstico enviado' }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="portal-body">
<div class="portal-wrap portal-wrap--centered">
    <div class="portal-state-card portal-state-card--success">
        <div class="portal-state-icon">✓</div>
        <h1>{{ $en ? 'Diagnostic submitted successfully' : 'Diagnóstico enviado com sucesso' }}</h1>
        <p>{{ $en ? 'Your response has been received. The BD team will analyse the information and prepare a personalised proposal for your organisation.' : 'A sua resposta foi recebida. A equipa BD irá analisar a informação e preparar uma proposta personalizada para a sua organização.' }}</p>
        <p class="portal-state-sub">{{ $en ? 'You may close this window.' : 'Pode fechar esta janela.' }}</p>
    </div>
</div>
</body>
</html>
