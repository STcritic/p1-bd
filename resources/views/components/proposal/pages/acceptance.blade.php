@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp

<x-proposal.page number="19"
    :label="$en ? 'Proposal acceptance' : 'Aceitação da proposta'"
    :title="$en ? 'Authorisation and start confirmation' : 'Autorização e confirmação de início'"
    variant="acceptance">
    <div class="proposal-acceptance-intro">
        <p>{{ $vm->closingNote }}</p>
    </div>

    <div class="proposal-acceptance-grid">
        <div class="proposal-block proposal-acceptance-block">
            <h3>{{ $en ? 'On behalf of the Client' : 'Em nome do Cliente' }}</h3>
            <div class="proposal-signature-area">
                <div class="proposal-signature-line"></div>
                <p>{{ $en ? 'Signature and stamp' : 'Assinatura e carimbo' }}</p>
            </div>
            <div class="proposal-acceptance-fields">
                <div><span>{{ $en ? 'Name:' : 'Nome:' }}</span><div class="proposal-field-line"></div></div>
                <div><span>{{ $en ? 'Title:' : 'Cargo:' }}</span><div class="proposal-field-line"></div></div>
                <div><span>{{ $en ? 'Date:' : 'Data:' }}</span><div class="proposal-field-line"></div></div>
            </div>
            <div class="proposal-acceptance-client-prefill">
                <p><strong>{{ $vm->clientName }}</strong></p>
                <p>{{ $vm->clientPosition }}</p>
                <p>{{ $vm->clientContact }}</p>
            </div>
        </div>

        <div class="proposal-block proposal-acceptance-block proposal-highlight proposal-digital-certification">
            <h3>{{ $en ? 'Business Diversity digital certification' : 'Certificação digital Business Diversity' }}</h3>
            <p>
                {{ $en
                    ? 'This proposal is issued by Business Diversity and can be verified online through the QR code below.'
                    : 'Esta proposta é emitida pela Business Diversity e pode ser verificada online através do QR code abaixo.' }}
            </p>

            @if ($vm->isVerificationAvailable())
                <div class="proposal-certification-grid">
                    <div class="proposal-certification-qr">
                        <img src="{{ $vm->verificationQrUrl() }}" alt="{{ $en ? 'Proposal verification QR code' : 'QR code de verificação da proposta' }}">
                    </div>
                    <div class="proposal-certification-meta">
                        <span>{{ $en ? 'Verification code' : 'Código de verificação' }}</span>
                        <strong>{{ $vm->verificationCode() }}</strong>
                        <small>{{ $en ? 'Status' : 'Estado' }}: {{ $vm->verificationStatusLabel() }}</small>
                        <small>{{ $en ? 'Certified on' : 'Certificada em' }}: {{ $vm->certifiedAtFormatted() }}</small>
                        <small>{{ $en ? 'Valid until' : 'Válida até' }}: {{ $vm->formattedValidUntil() }}</small>
                    </div>
                </div>
                <p class="proposal-certification-url">{{ $vm->verificationUrl() }}</p>
                <small class="proposal-certification-note">
                    {{ $en
                        ? 'Verification confirms the authenticity and validity of this version; client acceptance remains in the signature block.'
                        : 'A verificação confirma a autenticidade e validade desta versão; a aceitação do cliente permanece no bloco de assinatura.' }}
                </small>
            @else
                <div class="proposal-certification-fallback">
                    <strong>{{ $vm->verificationCode() }}</strong>
                    <span>{{ $en ? 'Stored in the BD proposal system.' : 'Registada no sistema de propostas BD.' }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="proposal-reference-footer">
        <span>Ref. {{ $vm->reference }}</span>
        <span>{{ $en ? 'Verification' : 'Verificação' }} {{ $vm->verificationCode() }}</span>
        <span>{{ $en ? 'Valid until' : 'Válida até' }} {{ $vm->formattedValidUntil() }}</span>
        <span>{{ $vm->company()['short_name'] ?? $vm->company()['legal_name'] ?? '' }}</span>
    </div>
</x-proposal.page>
