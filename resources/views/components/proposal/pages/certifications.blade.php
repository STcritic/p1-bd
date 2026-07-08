@props(['vm'])
@php
    $credentials  = $vm->credentials();
    $hasCerts     = !empty($credentials['certifications']);
    $hasPartners  = !empty($credentials['partnerships']);
    $hasAwards    = !empty($credentials['awards']);
    // Flat string array format (current config format)
    $textItems    = collect($credentials)->filter(fn ($v) => is_string($v))->values();
    $hasText      = $textItems->isNotEmpty();
    $hasAny       = $hasCerts || $hasPartners || $hasAwards || $hasText;
@endphp
@if ($hasAny)
<x-proposal.page number="17" label="Credenciais e parcerias"
    title="Certificações, parcerias e reconhecimentos"
    variant="certifications">

    {{-- Flat text credentials (current config format) --}}
    @if ($hasText && !$hasCerts && !$hasPartners && !$hasAwards)
        <div class="proposal-block proposal-credentials-profile">
            <h3>Perfil de credenciais e compromisso de qualidade</h3>
            <ul class="proposal-credential-text-list">
                @foreach ($textItems as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Structured certifications --}}
    @if ($hasCerts)
        <div class="proposal-block">
            <h3>Certificações activas</h3>
            <div class="proposal-certifications-grid">
                @foreach ($credentials['certifications'] as $cert)
                    <article>
                        @if (!empty($cert['logo']))
                            <img src="{{ asset($cert['logo']) }}" alt="{{ $cert['name'] }}">
                        @endif
                        <div>
                            <strong>{{ $cert['name'] }}</strong>
                            @if (!empty($cert['issuer']))<span>{{ $cert['issuer'] }}</span>@endif
                            @if (!empty($cert['year']))<small>{{ $cert['year'] }}</small>@endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif

    @if ($hasPartners)
        <div class="proposal-block">
            <h3>Parcerias estratégicas</h3>
            <div class="proposal-partnerships-grid">
                @foreach ($credentials['partnerships'] as $partner)
                    <article>
                        @if (!empty($partner['logo']))
                            <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }}">
                        @endif
                        <div>
                            <strong>{{ $partner['name'] }}</strong>
                            @if (!empty($partner['type']))<span>{{ $partner['type'] }}</span>@endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif

    @if ($hasAwards)
        <div class="proposal-block">
            <h3>Prémios e reconhecimentos</h3>
            <ul class="proposal-awards-list">
                @foreach ($credentials['awards'] as $award)
                    <li>
                        <strong>{{ $award['name'] }}</strong>
                        @if (!empty($award['year']))<span>{{ $award['year'] }}</span>@endif
                        @if (!empty($award['description']))<p>{{ $award['description'] }}</p>@endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

</x-proposal.page>
@endif
