@props(['vm'])
@php $en = $vm->lang() === 'en'; @endphp
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
        <span>{{ $en ? 'Technical and financial proposal' : 'Proposta técnica e financeira' }}</span>
        <h1>{{ $vm->serviceTitle }}</h1>
        <p>{{ $en ? 'Prepared for' : 'Preparada para' }} {{ $vm->clientName }}</p>
    </div>
    <div class="proposal-cover-photo">
        <img src="{{ $vm->coverImageUrl }}" alt="">
    </div>
    @php $co = $vm->company(); @endphp
    <div class="proposal-cover-band">
        <div class="proposal-cover-band-main">
            <div>
                <span>{{ $en ? 'Client' : 'Cliente' }}</span>
                <strong>{{ $vm->clientName }}</strong>
            </div>
            <div>
                <span>{{ $en ? 'Delivery model' : 'Modelo de entrega' }}</span>
                <strong>{{ $vm->pricingPackage['label'] ?? ($en ? 'Custom proposal' : 'Proposta personalizada') }}</strong>
            </div>
            <div>
                <span>{{ $en ? 'Validity' : 'Validade' }}</span>
                <strong>{{ $en ? 'Until' : 'Até' }} {{ $vm->formattedValidUntil() }}</strong>
            </div>
        </div>
        @if (!empty($co['email']) || !empty($co['phone']) || !empty($co['website']))
        <div class="proposal-cover-band-contact">
            @if (!empty($co['email']))<span>{{ $co['email'] }}</span>@endif
            @if (!empty($co['phone']))<span>{{ $co['phone'] }}</span>@endif
            @if (!empty($co['website']))<span>{{ $co['website'] }}</span>@endif
            @if (!empty($co['address']))<span>{{ $co['address'] }}</span>@endif
        </div>
        @endif
    </div>
</section>
