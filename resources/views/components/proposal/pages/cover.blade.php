@props(['vm'])
<section class="proposal-cover proposal-page proposal-cover-premium proposal-cover-editorial">
    <div class="proposal-cover-watermark" aria-hidden="true">BD</div>
    <div class="proposal-cover-top">
        <img class="proposal-cover-logo" src="{{ asset('assets/img/logo/logo.png') }}" alt="Business Diversity">
        <div class="proposal-cover-reference">
            <span>{{ $vm->reference }}</span>
            <strong>{{ $vm->formattedDate() }}</strong>
        </div>
    </div>
    <div class="proposal-cover-title">
        <span>Proposta técnica e financeira</span>
        <h1>{{ $vm->serviceTitle }}</h1>
        <p>Preparada para {{ $vm->clientName }}</p>
    </div>
    <div class="proposal-cover-photo">
        <img src="{{ $vm->coverImageUrl }}" alt="">
    </div>
    <div class="proposal-cover-band">
        <div>
            <span>Cliente</span>
            <strong>{{ $vm->clientName }}</strong>
        </div>
        <div>
            <span>Modelo de entrega</span>
            <strong>{{ $vm->pricingPackage['label'] ?? 'Proposta personalizada' }}</strong>
        </div>
        <div>
            <span>Validade</span>
            <strong>Até {{ $vm->formattedValidUntil() }}</strong>
        </div>
    </div>
</section>
