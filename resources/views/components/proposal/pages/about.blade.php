@props(['vm'])
@php $company = $vm->company(); $en = $vm->lang() === 'en'; @endphp
<x-proposal.page number="05"
    :label="$en ? 'About us' : 'Sobre nós'"
    :title="$company['short_name'] ?? 'Business Diversity'"
    variant="about">
    <div class="proposal-about-grid">
        <div class="proposal-about-copy">
            <p class="proposal-lead">{{ $company['summary'] ?? '' }}</p>
            @if (!empty($company['mission']) || !empty($company['vision']))
                <div class="proposal-mission-vision">
                    @if (!empty($company['mission']))
                        <article><span>{{ $en ? 'Mission' : 'Missão' }}</span><p>{{ $company['mission'] }}</p></article>
                    @endif
                    @if (!empty($company['vision']))
                        <article><span>{{ $en ? 'Vision' : 'Visão' }}</span><p>{{ $company['vision'] }}</p></article>
                    @endif
                </div>
            @endif
            <div class="proposal-positioning-card">
                <span>{{ $en ? 'Our positioning' : 'O nosso posicionamento' }}</span>
                <p>{{ $vm->positioningStatement }}</p>
            </div>
            <div class="proposal-bd-signature-grid">
                @foreach ($vm->bdSignature as $item)
                    <article>
                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3>{{ $item['label'] }}</h3>
                        <p>{{ $item['text'] }}</p>
                    </article>
                @endforeach
            </div>
            <div class="proposal-mini-metrics">
                @foreach (($company['experience'] ?? []) as $item)
                    <article>
                        <strong>{{ \Illuminate\Support\Str::before($item, ' ') }}</strong>
                        <span>{{ \Illuminate\Support\Str::after($item, ' ') }}</span>
                    </article>
                @endforeach
            </div>
            @if (!empty($company['values']))
                <div class="proposal-values-row">
                    @foreach ($company['values'] as $value)
                        <span>{{ $value }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="proposal-about-side">
            <div class="proposal-about-photo-card">
                <img src="{{ asset('assets/images/About.jpg') }}" alt="Business Diversity">
            </div>
            <div class="proposal-block proposal-highlight">
                <h3>{{ $en ? 'Quality principles' : 'Princípios de qualidade' }}</h3>
                <ul>
                    @foreach ($vm->qualityPrinciples() as $principle)
                        <li>{{ $principle }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-proposal.page>
