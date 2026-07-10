@php $en = ($lang ?? 'pt') === 'en'; @endphp
<!DOCTYPE html>
<html lang="{{ $lang ?? 'pt' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $en ? 'Diagnostic' : 'Diagnóstico' }}: {{ $opportunity->client_name }}</title>
    @vite(['resources/css/app.css'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="portal-body">

<div class="portal-wrap">

    {{-- Header --}}
    <header class="portal-header">
        <div class="portal-header-brand">
            @if(file_exists(public_path('images/logo.svg')))
                <img src="/images/logo.svg" alt="BD" height="32">
            @else
                <strong class="portal-brand-text">BD</strong>
            @endif
        </div>
        <div class="portal-header-meta">
            <span>{{ $en ? 'Confidential diagnostic' : 'Diagnóstico confidencial' }}</span>
            @if($session->expires_at)
                <span>{{ $en ? 'Valid until' : 'Válido até' }} {{ $session->expires_at->format('d/m/Y') }}</span>
            @endif
        </div>
    </header>

    {{-- Intro --}}
    <section class="portal-intro">
        <h1>{{ $guide['title'] }}</h1>
        <p>{{ $guide['intro'] }}</p>
        <div class="portal-intro-meta">
            <span>{{ $en ? 'Organisation' : 'Organização' }}: <strong>{{ $opportunity->client_name }}</strong></span>
            <span>{{ $en ? 'Service' : 'Serviço' }}: <strong>{{ $opportunity->service_title }}</strong></span>
        </div>
    </section>

    {{-- Progress bar --}}
    <div class="portal-progress" id="portalProgress">
        <div class="portal-progress-bar" id="progressBar"></div>
        <span class="portal-progress-label" id="progressLabel">0%</span>
    </div>

    {{-- Form --}}
    <form action="{{ route('diagnostic.submit', $session->token) }}" method="POST"
          id="diagnosticForm" enctype="multipart/form-data" class="portal-form" novalidate>
        @csrf

        @foreach($guide['groups'] as $groupIndex => $group)
            @php
                $conditions = $group['conditions'] ?? [];
                $condAttrs  = empty($conditions) ? '' : "data-conditions='" . json_encode($conditions) . "'";
            @endphp

            <fieldset class="portal-group" id="group-{{ $group['key'] }}" {!! $condAttrs !!}>
                <div class="portal-group-head">
                    <div class="portal-group-num">{{ str_pad($groupIndex + 1, 2, '0', STR_PAD_LEFT) }}</div>
                    <div>
                        <legend class="portal-group-legend">{{ $group['label'] }}</legend>
                        @if($group['guide'] ?? null)
                            <p class="portal-group-guide">{{ $group['guide'] }}</p>
                        @endif
                    </div>
                </div>

                <div class="portal-group-fields">
                    @foreach($group['questions'] as $question)
                        @php
                            $key        = $question['key'];
                            $type       = $question['type'];
                            $required   = $question['required'] ?? false;
                            $hint       = $question['hint'] ?? null;
                            $qCond      = $question['conditions'] ?? [];
                            $saved      = $draft[$key] ?? null;
                            $qCondAttr  = empty($qCond) ? '' : "data-conditions='" . json_encode($qCond) . "'";
                        @endphp

                        <div class="portal-field" id="field-{{ $key }}" {!! $qCondAttr !!}>
                            <label for="{{ $key }}" class="portal-label">
                                {{ $question['label'] }}
                                @if($required)<span class="portal-required">*</span>@endif
                            </label>

                            @if($hint)
                                <p class="portal-hint">{{ $hint }}</p>
                            @endif

                            @switch($type)
                                @case('text')
                                    <input type="text" name="{{ $key }}" id="{{ $key }}"
                                           value="{{ old($key, $saved) }}"
                                           class="portal-input" @if($required) required @endif
                                           maxlength="500">
                                    @break

                                @case('textarea')
                                    <textarea name="{{ $key }}" id="{{ $key }}"
                                              class="portal-textarea" rows="4"
                                              @if($required) required @endif
                                              maxlength="3000">{{ old($key, $saved) }}</textarea>
                                    @break

                                @case('number')
                                    <input type="number" name="{{ $key }}" id="{{ $key }}"
                                           value="{{ old($key, $saved) }}"
                                           class="portal-input portal-input--number"
                                           @if($required) required @endif min="0">
                                    @break

                                @case('select')
                                    <select name="{{ $key }}" id="{{ $key }}"
                                            class="portal-select" @if($required) required @endif>
                                        <option value="">{{ $en ? 'Select an option' : 'Seleccione uma opção' }}</option>
                                        @foreach($question['options'] ?? [] as $val => $lbl)
                                            <option value="{{ $val }}"
                                                @selected(old($key, $saved) === $val)>
                                                {{ $lbl }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('radio')
                                    <div class="portal-radio-group">
                                        @foreach($question['options'] ?? [] as $val => $lbl)
                                            <label class="portal-radio-option">
                                                <input type="radio" name="{{ $key }}" value="{{ $val }}"
                                                       @checked(old($key, $saved) === $val)
                                                       @if($required) required @endif>
                                                <span>{{ $lbl }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @break

                                @case('multiselect')
                                    <div class="portal-multiselect">
                                        @foreach($question['options'] ?? [] as $val => $lbl)
                                            @php $savedArr = is_array($saved) ? $saved : []; @endphp
                                            <label class="portal-check-option">
                                                <input type="checkbox" name="{{ $key }}[]" value="{{ $val }}"
                                                       @checked(in_array($val, $savedArr))>
                                                <span>{{ $lbl }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @break

                                @case('boolean')
                                    <div class="portal-bool-group">
                                        <label class="portal-radio-option">
                                            <input type="radio" name="{{ $key }}" value="1"
                                                   @checked(old($key, $saved) == '1')
                                                   @if($required) required @endif>
                                            <span>{{ $en ? 'Yes' : 'Sim' }}</span>
                                        </label>
                                        <label class="portal-radio-option">
                                            <input type="radio" name="{{ $key }}" value="0"
                                                   @checked(old($key, $saved) == '0')>
                                            <span>{{ $en ? 'No' : 'Não' }}</span>
                                        </label>
                                    </div>
                                    @break

                                @case('date')
                                    <input type="date" name="{{ $key }}" id="{{ $key }}"
                                           value="{{ old($key, $saved) }}"
                                           class="portal-input"
                                           @if($required) required @endif>
                                    @break

                                @case('file')
                                    <div class="portal-file-zone" data-key="{{ $key }}">
                                        <input type="file" name="{{ $key }}" id="{{ $key }}"
                                               class="portal-file-input"
                                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tiff">
                                        <label for="{{ $key }}" class="portal-file-label">
                                            <span class="portal-file-icon">📎</span>
                                            <span>{{ $en ? 'Click to select or drag file' : 'Clique para seleccionar ou arraste o ficheiro' }}</span>
                                            <small>PDF, Word, Excel, {{ $en ? 'Image' : 'Imagem' }} (máx. 20MB)</small>
                                        </label>
                                        <div class="portal-file-name" hidden></div>
                                    </div>
                                    @break
                            @endswitch

                            @error($key)
                                <span class="portal-field-error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </fieldset>
        @endforeach

        <div class="portal-form-footer">
            <p class="portal-autosave-status" id="autosaveStatus">
                {{ $en ? 'Progress is saved automatically.' : 'O progresso é guardado automaticamente.' }}
            </p>
            <button type="submit" class="portal-submit-btn" id="submitBtn">
                {{ $en ? 'Submit diagnostic →' : 'Submeter diagnóstico →' }}
            </button>
        </div>
    </form>

    <footer class="portal-footer">
        <p>{{ $en ? 'This diagnostic is confidential and will be used exclusively for the construction of the proposal.' : 'Este diagnóstico é confidencial e será utilizado exclusivamente para a construção da proposta.' }}</p>
        <p>{{ $en ? 'If in doubt, contact your BD team.' : 'Em caso de dúvida, contacte a sua equipa BD.' }}</p>
    </footer>
</div>

<script>
(function () {
    const SAVE_URL = '{{ route('diagnostic.save', $session->token) }}';
    const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
    const IS_EN    = {{ $en ? 'true' : 'false' }};

    // ── Conditional display ───────────────────────────────────────────────────
    function getFieldValues() {
        const vals = {};
        document.querySelectorAll('[name]').forEach(el => {
            if (el.type === 'checkbox' || el.type === 'radio') {
                if (el.checked) {
                    if (!vals[el.name]) vals[el.name] = el.value;
                }
            } else {
                vals[el.name] = el.value;
            }
        });
        return vals;
    }

    function evaluate(conditions, values) {
        return conditions.every(c => {
            const v = values[c.field];
            switch (c.operator) {
                case 'eq':  return String(v) === String(c.value);
                case 'in':  return Array.isArray(c.value) ? c.value.includes(v) : v === c.value;
                case 'neq': return String(v) !== String(c.value);
                default:    return true;
            }
        });
    }

    function refreshConditions() {
        const values = getFieldValues();

        // Groups
        document.querySelectorAll('.portal-group[data-conditions]').forEach(group => {
            const cond = JSON.parse(group.dataset.conditions);
            group.style.display = cond.length && !evaluate(cond, values) ? 'none' : '';
        });

        // Individual fields
        document.querySelectorAll('.portal-field[data-conditions]').forEach(field => {
            const cond = JSON.parse(field.dataset.conditions);
            field.style.display = cond.length && !evaluate(cond, values) ? 'none' : '';
        });

        updateProgress();
    }

    // ── Progress ──────────────────────────────────────────────────────────────
    function updateProgress() {
        const fields  = [...document.querySelectorAll('.portal-field:not([style*="display: none"])')];
        const filled  = fields.filter(f => {
            const inputs = f.querySelectorAll('input:not([type=file]),select,textarea');
            return [...inputs].some(i => i.value && i.value.trim());
        });
        const pct = fields.length ? Math.round(filled.length / fields.length * 100) : 0;
        document.getElementById('progressBar').style.width = pct + '%';
        document.getElementById('progressLabel').textContent = pct + '%';
    }

    // ── Autosave ──────────────────────────────────────────────────────────────
    let saveTimer;
    function scheduleAutosave() {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(autosave, 3000);
    }

    function collectAnswers() {
        const form = document.getElementById('diagnosticForm');
        const data = new FormData(form);
        const ans  = {};
        for (const [k, v] of data.entries()) {
            if (!k.startsWith('_') && !k.includes('ficheiro') && !k.includes('file') && !k.includes('documento')) {
                ans[k] = v;
            }
        }
        return ans;
    }

    function autosave() {
        const status = document.getElementById('autosaveStatus');
        status.textContent = IS_EN ? 'Saving…' : 'A guardar…';
        fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ answers: collectAnswers() }),
        })
        .then(r => r.json())
        .then(d => { status.textContent = (IS_EN ? 'Saved at ' : 'Guardado às ') + (d.saved_at || ''); })
        .catch(() => { status.textContent = IS_EN ? 'Error saving. No connection?' : 'Erro ao guardar. Sem ligação?'; });
    }

    // ── File drag & drop ──────────────────────────────────────────────────────
    document.querySelectorAll('.portal-file-zone').forEach(zone => {
        const input = zone.querySelector('input[type=file]');
        const nameEl = zone.querySelector('.portal-file-name');

        input.addEventListener('change', () => {
            if (input.files[0]) {
                nameEl.textContent = '📎 ' + input.files[0].name;
                nameEl.hidden = false;
            }
        });

        zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
        zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
        zone.addEventListener('drop', e => {
            e.preventDefault();
            zone.classList.remove('drag-over');
            const dt = e.dataTransfer;
            if (dt.files[0]) {
                const transfer = new DataTransfer();
                transfer.items.add(dt.files[0]);
                input.files = transfer.files;
                nameEl.textContent = '📎 ' + dt.files[0].name;
                nameEl.hidden = false;
            }
        });
    });

    // ── Submit guard ──────────────────────────────────────────────────────────
    document.getElementById('diagnosticForm').addEventListener('submit', function () {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').textContent = IS_EN ? 'Submitting…' : 'A enviar…';
    });

    // ── Init ─────────────────────────────────────────────────────────────────
    document.addEventListener('input', () => { refreshConditions(); scheduleAutosave(); });
    document.addEventListener('change', () => { refreshConditions(); scheduleAutosave(); });
    refreshConditions();
})();
</script>

</body>
</html>
