@extends('announcements.layout')
@section('title', isset($savedProposal) ? 'Proposta: ' . $savedProposal->client_name : 'Proposta gerada')

@section('content')
@php $en = ($collabLang ?? 'pt') === 'en'; @endphp
<main class="proposal-preview-shell">
    <div class="proposal-toolbar no-print">
        <div class="proposal-toolbar-nav">
            <a href="{{ route('collaborator.proposals.index') }}">← {{ $en ? 'New proposal' : 'Nova proposta' }}</a>
            <a href="{{ route('collaborator.proposals.saved') }}">{{ $en ? 'Saved proposals' : 'Propostas guardadas' }}</a>
        </div>
        @isset($savedProposal)
            <span class="proposal-status-badge proposal-status-{{ $savedProposal->statusColor() }}">{{ $savedProposal->statusLabel() }}</span>
            <span class="proposal-toolbar-ref">{{ $savedProposal->reference }}</span>
        @endisset
        @isset($savedProposal)
            <a href="{{ route('collaborator.proposals.edit', $savedProposal) }}" class="proposal-edit-btn">{{ $en ? 'Edit proposal' : 'Editar proposta' }}</a>
        @endisset
        <button type="button" class="proposal-print-btn"
            data-download-pdf
            data-pdf-reference="{{ $savedProposal->reference ?? ($vm->reference ?? 'proposta') }}">
            {{ $en ? 'Download PDF' : 'Baixar PDF' }}
        </button>
    </div>

    @isset($savedProposal)
    <div class="proposal-followup no-print">
        @if (session('success'))
            <div class="proposal-followup-notice">{{ session('success') }}</div>
        @endif
        <form method="POST" action="{{ route('collaborator.proposals.update-status', $savedProposal) }}" class="proposal-followup-form">
            @csrf @method('PATCH')
            <label class="proposal-followup-field">
                <span>{{ $en ? 'Status' : 'Estado' }}</span>
                <select name="status">
                    @foreach (\App\Models\Proposal::statuses() as $key => $label)
                        <option value="{{ $key }}" @selected($savedProposal->status === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="proposal-followup-field proposal-followup-notes">
                <span>{{ $en ? 'Follow-up notes' : 'Notas de follow-up' }}</span>
                <textarea name="notes" rows="2" placeholder="{{ $en ? 'E.g.: meeting scheduled for the 10th, client asked for price revision…' : 'Ex: reunião marcada para dia 10, cliente pediu revisão de preço…' }}">{{ $savedProposal->notes }}</textarea>
            </label>
            <div class="proposal-followup-actions">
                <button type="submit" class="button button-secondary">{{ $en ? 'Save status' : 'Guardar estado' }}</button>
                @if ($savedProposal->expires_at)
                    <span class="proposal-followup-expiry {{ $savedProposal->isExpired() ? 'is-expired' : '' }}">
                        {{ $en ? 'Valid until' : 'Válida até' }} {{ $savedProposal->expires_at->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        </form>
        <form method="POST" action="{{ route('collaborator.proposals.destroy', $savedProposal) }}"
              onsubmit="return confirm('{{ $en ? 'Permanently delete this proposal?' : 'Eliminar esta proposta permanentemente?' }}')" class="proposal-followup-delete">
            @csrf @method('DELETE')
            <button type="submit">{{ $en ? 'Delete proposal' : 'Eliminar proposta' }}</button>
        </form>
    </div>
    @endisset

    <article class="proposal-document proposal-premium-document">
        <x-proposal.pages.cover :vm="$vm" />
        <x-proposal.pages.toc :vm="$vm" />
        <x-proposal.pages.letter :vm="$vm" />
        <x-proposal.pages.client-context :vm="$vm" />
        <x-proposal.pages.executive-summary :vm="$vm" />
        <x-proposal.pages.critical-case :vm="$vm" />
        <x-proposal.pages.about :vm="$vm" />
        <x-proposal.pages.scope :vm="$vm" />
        <x-proposal.pages.methodology :vm="$vm" />
        <x-proposal.pages.timeline :vm="$vm" />
        <x-proposal.pages.practical-outputs :vm="$vm" />
        <x-proposal.pages.kpis :vm="$vm" />
        <x-proposal.pages.financial :vm="$vm" />
        <x-proposal.pages.terms :vm="$vm" />
        <x-proposal.pages.featured-case :vm="$vm" />
        <x-proposal.pages.proof :vm="$vm" />
        <x-proposal.pages.clients :vm="$vm" />
        <x-proposal.pages.team :vm="$vm" />
        <x-proposal.pages.certifications :vm="$vm" />
        <x-proposal.pages.faq :vm="$vm" />
        <x-proposal.pages.acceptance :vm="$vm" />
    </article>
</main>
<script>
(function () {
    const btn = document.querySelector('[data-download-pdf]');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var ref  = btn.dataset.pdfReference || 'proposta';
        var prev = document.title;
        document.title = ref;
        function restore() {
            document.title = prev;
            window.removeEventListener('afterprint', restore);
        }
        window.addEventListener('afterprint', restore);
        setTimeout(function () { window.print(); }, 60);
    });
}());
</script>
@endsection
