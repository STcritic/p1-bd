@props(['vm'])
@php
    $credentials  = $vm->credentials();
    $hasCerts     = !empty($credentials['certifications']);
    $hasPartners  = !empty($credentials['partnerships']);
    $hasMemberships = !empty($credentials['memberships']);
    $hasAwards    = !empty($credentials['awards']);
    $profileItems = collect($credentials['profile'] ?? []);
    // Legacy: flat string array format
    $textItems    = $profileItems->isNotEmpty()
        ? $profileItems
        : collect($credentials)->filter(fn ($v) => is_string($v))->values();
    $hasText      = $textItems->isNotEmpty();
    $hasAny       = $hasCerts || $hasPartners || $hasMemberships || $hasAwards || $hasText;
    $en           = $vm->lang() === 'en';
@endphp
@if ($hasAny)
<x-proposal.page number="17"
    :label="$en ? 'Credentials and partnerships' : 'Credenciais e parcerias'"
    :title="$en ? 'Certifications, partnerships and recognitions' : 'Certificações, parcerias e reconhecimentos'"
    variant="certifications">

    {{-- Profile text --}}
    @if ($hasText)
        <div class="proposal-block proposal-credentials-profile">
            <h3>{{ $en ? 'Credentials profile and quality commitment' : 'Perfil de credenciais e compromisso de qualidade' }}</h3>
            <ul class="proposal-credential-text-list">
                @foreach ($textItems as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Memberships --}}
    @if ($hasMemberships)
        <div class="proposal-block">
            <h3>{{ $en ? 'Memberships' : 'Membresías' }}</h3>
            <div class="proposal-memberships-grid">
                @foreach ($credentials['memberships'] as $member)
                    <article class="proposal-membership-card">
                        @if (!empty($member['logo']))
                            <img src="{{ asset($member['logo']) }}" alt="{{ $member['name'] }}">
                        @endif
                        <div>
                            <strong>{{ $member['name'] }}</strong>
                            @if (!empty($member['type']))<span>{{ $member['type'] }}</span>@endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Structured certifications --}}
    @if ($hasCerts)
        <div class="proposal-block">
            <h3>{{ $en ? 'Active certifications' : 'Certificações activas' }}</h3>
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
            <h3>{{ $en ? 'Strategic partnerships' : 'Parcerias estratégicas' }}</h3>
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
            <h3>{{ $en ? 'Awards and recognitions' : 'Prémios e reconhecimentos' }}</h3>
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
