@extends('layouts.app', ['locale' => $locale ?? 'pt'])

@section('title', 'Verificação de proposta')
@section('description', 'Confirme a validade de uma proposta emitida pela Business Diversity.')

@section('content')
@php
    $valid = $status === 'valid';
    $expired = $status === 'expired';
    $revoked = $status === 'revoked';
    $inactive = $status === 'inactive';
@endphp

<section class="proposal-verification-hero">
    <div class="container proposal-verification-card">
        <div class="proposal-verification-brand">
            <img src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
            <span>Verificação digital</span>
        </div>

        @if (! $proposal)
            <div class="proposal-verification-status proposal-verification-invalid">
                <span>Código inválido</span>
                <h1>Documento não encontrado.</h1>
                <p>Não foi possível confirmar esta proposta nos registos da Business Diversity. Verifique o link ou solicite uma nova proposta.</p>
            </div>
        @else
            <div @class([
                'proposal-verification-status',
                'proposal-verification-valid' => $valid,
                'proposal-verification-expired' => $expired || $revoked || $inactive,
            ])>
                <span>{{ $proposal->verificationStatusLabel() }}</span>
                <h1>
                    @if ($valid)
                        Documento certificado pela Business Diversity.
                    @elseif ($expired)
                        Esta proposta expirou.
                    @elseif ($revoked)
                        Esta proposta foi revogada.
                    @elseif ($inactive)
                        Esta proposta está sem efeito.
                    @else
                        Estado da proposta indisponível.
                    @endif
                </h1>
                <p>
                    @if ($valid)
                        Este link confirma que a proposta abaixo foi emitida e certificada digitalmente pela Business Diversity.
                    @else
                        Este documento já não deve ser usado para decisão comercial. Solicite uma versão actualizada à Business Diversity.
                    @endif
                </p>
            </div>

            <div class="proposal-verification-details">
                <article>
                    <span>Referência</span>
                    <strong>{{ $proposal->reference }}</strong>
                </article>
                <article>
                    <span>Código de verificação</span>
                    <strong>{{ $proposal->verification_code }}</strong>
                </article>
                <article>
                    <span>Cliente</span>
                    <strong>{{ $proposal->client_name }}</strong>
                </article>
                <article>
                    <span>Serviço</span>
                    <strong>{{ $proposal->service_title }}</strong>
                </article>
                <article>
                    <span>Certificada em</span>
                    <strong>{{ optional($proposal->certified_at)->format('d/m/Y H:i') ?? '—' }}</strong>
                </article>
                <article>
                    <span>Válida até</span>
                    <strong>{{ optional($proposal->expires_at)->format('d/m/Y') ?? '—' }}</strong>
                </article>
            </div>
        @endif

        <div class="proposal-verification-actions">
            <a class="button button-primary" href="mailto:info@bdiversity.co.mz">Contactar BD <span>→</span></a>
            <a class="text-link" href="{{ route('home') }}">Voltar ao website <span>→</span></a>
        </div>
    </div>
</section>
@endsection
