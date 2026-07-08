@props(['vm'])
<x-proposal.page number="16" label="Equipa do projecto"
    title="Perfis técnicos indicados para esta intervenção"
    variant="team">
    <div class="proposal-team-intro">
        <p>A equipa proposta combina liderança técnica sénior, conhecimento de RH e competências modernas em digitalização, dados, comunicação e optimização de processos.</p>
        <strong>{{ $vm->team }}</strong>
    </div>
    @if (!empty($vm->teamMembers))
        <div class="proposal-team-grid {{ count($vm->teamMembers) === 1 ? 'proposal-team-grid-solo' : '' }}">
            @foreach ($vm->teamMembers as $member)
                <article>
                    <img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}">
                    <div>
                        <h3>{{ $member['name'] }}</h3>
                        <span>{{ $member['role'] }}</span>
                        <p>{{ $member['specialty'] }}</p>
                        @if (!empty($member['project_role']))
                            <p class="proposal-team-role">{{ $member['project_role'] }}</p>
                        @endif
                        @if (!empty($member['certifications']))
                            <div class="proposal-team-tags">
                                @foreach ($member['certifications'] as $tag)
                                    <small>{{ $tag }}</small>
                                @endforeach
                            </div>
                        @endif
                        <strong>{{ $member['experience'] }}</strong>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-proposal.page>
