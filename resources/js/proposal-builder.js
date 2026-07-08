(() => {
    const dataEl = document.getElementById('proposal-builder-data');
    if (!dataEl) return;

    const { serviceQuestions: data, packages, complexity, oldSelection: previous } =
        JSON.parse(dataEl.textContent);

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const form             = document.querySelector('.proposal-builder-form');
    const select           = document.querySelector('[data-proposal-service]');
    const packageSelect    = document.querySelector('[data-proposal-package]');
    const complexitySelect = document.querySelector('[data-proposal-complexity]');
    const titleEl          = document.querySelector('[data-proposal-question-title]');
    const questionsEl      = document.querySelector('[data-proposal-questions]');
    const pricingEl        = document.querySelector('[data-proposal-pricing]');
    const profilesEl       = document.querySelector('[data-proposal-profiles]');
    const financialsEl     = document.querySelector('[data-proposal-financials]');
    const scopeEl          = document.querySelector('[data-proposal-scope]');
    const deliverablesEl   = document.querySelector('[data-proposal-deliverables]');
    const targets = {
        approaches:   document.querySelector('[data-proposal-approaches]'),
        modules:      document.querySelector('[data-proposal-modules]'),
        deliverables: document.querySelector('[data-proposal-deliverable-options]'),
        profiles:     document.querySelector('[data-proposal-profile-options]'),
    };

    // ── Utilities ─────────────────────────────────────────────────────────────
    const esc = (v) => String(v ?? '')
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#039;');

    const debounce = (fn, ms) => {
        let t;
        return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
    };

    const fmt = (n, currency = '') => {
        const s = n.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return currency ? `${s} ${currency}` : s;
    };

    const previousFor = (key) =>
        previous.service === select.value && Array.isArray(previous[key]) ? previous[key] : [];

    // ── Check card rendering ──────────────────────────────────────────────────
    const renderChecks = (target, name, items, prevValues = [], profileMode = false) => {
        if (!target) return;
        target.innerHTML = items.map((item, i) => {
            const value   = profileMode ? item.key  : item;
            const text    = profileMode ? item.text : item;
            const checked = prevValues.length ? prevValues.includes(value) : true;
            return `<label class="proposal-check-card">
                <input type="checkbox" name="${name}[]" value="${esc(value)}" ${checked ? 'checked' : ''}>
                <span>${String(i + 1).padStart(2, '0')}</span>
                <p>${esc(text)}</p>
            </label>`;
        }).join('');
    };

    const selectedLabels = (target) =>
        Array.from(target?.querySelectorAll('input:checked') ?? [])
            .map(cb => cb.closest('label')?.querySelector('p')?.textContent.trim())
            .filter(Boolean);

    // ── Sync optional text from checkbox state ────────────────────────────────
    const syncOptionalText = () => {
        return;
        const item = data[select.value];
        if (!item) return;
        if (scopeEl && !scopeEl.value.trim()) {
            const modules = selectedLabels(targets.modules);
            scopeEl.value = [
                item.scope,
                modules.length ? `\nMódulos previstos:\n${modules.join('\n')}` : '',
            ].join('\n').trim();
        }
        if (deliverablesEl && !deliverablesEl.value.trim()) {
            deliverablesEl.value = selectedLabels(targets.deliverables).join('\n');
        }
    };

    // ── Side panel ────────────────────────────────────────────────────────────
    const renderSidePanel = (item) => {
        if (!item) return;
        titleEl.textContent = item.title;

        questionsEl.innerHTML = (item.questions ?? []).map((q, i) =>
            `<article><span>${String(i + 1).padStart(2, '0')}</span><p>${esc(q)}</p></article>`
        ).join('');

        const pkg     = packages[packageSelect.value] ?? {};
        const cxLabel = complexity[complexitySelect.value] ?? '';
        const drivers = item.pricing?.drivers ?? [];
        const ranges  = item.pricing?.ranges ?? {};

        pricingEl.innerHTML = `
            <strong>${esc(pkg.label ?? 'Pacote')}</strong>
            <p>${esc(pkg.pricing ?? item.pricing?.base ?? '')}</p>
            <small>${esc(cxLabel)}</small>
            ${drivers.length ? `<ul>${drivers.map(d => `<li>${esc(d)}</li>`).join('')}</ul>` : ''}
            ${Object.keys(ranges).length
                ? `<div class="proposal-price-ranges">${Object.entries(ranges).map(([l, v]) => `<span>${esc(l)}</span><strong>${esc(v)}</strong>`).join('')}</div>`
                : ''}
        `;

        profilesEl.innerHTML = (item.profiles ?? []).map(p =>
            `<article><span>${esc(p.key)}</span><p>${esc(p.text)}</p></article>`
        ).join('');
    };

    // ── Main render ───────────────────────────────────────────────────────────
    const render = () => {
        const item = data[select.value];
        if (!item) return;
        renderChecks(targets.approaches,   'selected_approaches',   item.approaches   ?? [], previousFor('selected_approaches'));
        renderChecks(targets.modules,      'selected_modules',      item.modules      ?? [], previousFor('selected_modules'));
        renderChecks(targets.deliverables, 'selected_deliverables', item.deliverables ?? [], previousFor('selected_deliverables'));
        renderChecks(targets.profiles,     'selected_profiles',     item.profiles     ?? [], previousFor('selected_profiles'), true);
        renderSidePanel(item);
        syncOptionalText();
    };

    // ── Live financial calculator ─────────────────────────────────────────────
    const initFinancials = () => {
        const feeInput      = form?.querySelector('[name="fee"]');
        const expensesInput = form?.querySelector('[name="expenses"]');
        const vatRateInput  = form?.querySelector('[name="vat_rate"]');
        const currencyInput = form?.querySelector('[name="currency"]');

        const update = () => {
            if (!financialsEl) return;
            const fee      = parseFloat(feeInput?.value)    || 0;
            const expenses = parseFloat(expensesInput?.value) || 0;
            const vatRate  = parseFloat(vatRateInput?.value)  || 0;
            const currency = currencyInput?.value?.trim()    || '';
            const subtotal = fee + expenses;
            const vat      = subtotal * (vatRate / 100);
            const total    = subtotal + vat;

            if (subtotal === 0) {
                financialsEl.innerHTML = '<p class="proposal-financial-empty">Preencha os honorários para ver o resumo.</p>';
                return;
            }

            financialsEl.innerHTML = `
                <div class="proposal-financial-row"><span>Honorários</span><strong>${fmt(fee, currency)}</strong></div>
                ${expenses > 0 ? `<div class="proposal-financial-row"><span>Despesas</span><strong>${fmt(expenses, currency)}</strong></div>` : ''}
                <div class="proposal-financial-row proposal-financial-sub"><span>Subtotal</span><strong>${fmt(subtotal, currency)}</strong></div>
                <div class="proposal-financial-row"><span>IVA${vatRate > 0 ? ` (${vatRate}%)` : ''}</span><strong>${fmt(vat, currency)}</strong></div>
                <div class="proposal-financial-row proposal-financial-total"><span>Total</span><strong>${fmt(total, currency)}</strong></div>
            `;
        };

        [feeInput, expensesInput, vatRateInput, currencyInput].forEach(el =>
            el?.addEventListener('input', update)
        );
        update();
    };

    // ── Character counters ────────────────────────────────────────────────────
    const initCharCounters = () => {
        document.querySelectorAll('[data-char-max]').forEach(textarea => {
            const max     = parseInt(textarea.dataset.charMax, 10);
            const counter = textarea.closest('label')?.querySelector('[data-char-counter]');
            if (!counter) return;

            const update = () => {
                const len = textarea.value.length;
                counter.textContent = `${len.toLocaleString('pt-PT')} / ${max.toLocaleString('pt-PT')}`;
                counter.className = 'field-counter'
                    + (len >= max ? ' is-danger' : len > max * 0.85 ? ' is-warning' : '');
            };

            textarea.addEventListener('input', update);
            update();
        });
    };

    // ── Select-all toggle per section ─────────────────────────────────────────
    const initSelectAll = () => {
        document.querySelectorAll('[data-select-all]').forEach(btn => {
            btn.addEventListener('click', () => {
                const section    = btn.closest('.proposal-preset-section');
                const checkboxes = Array.from(section?.querySelectorAll('input[type="checkbox"]') ?? []);
                const allChecked = checkboxes.every(cb => cb.checked);
                checkboxes.forEach(cb => { cb.checked = !allChecked; });
                syncOptionalText();
            });
        });
    };

    // ── Cover image preview ───────────────────────────────────────────────────
    const initCoverPreview = () => {
        const urlInput = form?.querySelector('[name="cover_image_url"]');
        const preview  = document.querySelector('[data-cover-preview]');
        if (!urlInput || !preview) return;

        const update = debounce(() => {
            const url = urlInput.value.trim();
            if (!url) { preview.hidden = true; return; }
            preview.hidden = false;
            preview.innerHTML = `<img src="${esc(url)}" alt="Pré-visualização" loading="lazy" onerror="this.closest('[data-cover-preview]').hidden=true">`;
        }, 700);

        urlInput.addEventListener('input', update);
        update();
    };

    // ── Auto-save draft ───────────────────────────────────────────────────────
    const DRAFT_KEY = 'bd_proposal_draft_v1';

    const collectFormData = () => {
        if (!form) return {};
        const fd = new FormData(form);
        const out = {};
        for (const key of new Set(fd.keys())) {
            if (key === '_token') continue;
            const vals = fd.getAll(key);
            out[key] = vals.length === 1 ? vals[0] : vals;
        }
        return out;
    };

    const saveDraft = debounce(() => {
        try {
            localStorage.setItem(DRAFT_KEY, JSON.stringify({ data: collectFormData(), ts: Date.now() }));
        } catch {}
    }, 1500);

    const restoreDraft = (saved) => {
        if (!form || !saved?.data) return;
        const d = saved.data;

        // Restore service first so renderChecks fires with the right item
        if (d.service_slug && select) {
            select.value = d.service_slug;
            if (scopeEl) scopeEl.value = '';
            if (deliverablesEl) deliverablesEl.value = '';
            render();
        }

        // Overwrite checkbox selections after render
        ['selected_approaches', 'selected_modules', 'selected_deliverables', 'selected_profiles'].forEach(base => {
            const vals = Array.isArray(d[base]) ? d[base] : (d[base] ? [d[base]] : []);
            form.querySelectorAll(`[name="${base}[]"]`).forEach(cb => {
                cb.checked = vals.includes(cb.value);
            });
        });

        // Restore plain fields
        const skip = new Set(['service_slug', 'selected_approaches', 'selected_modules', 'selected_deliverables', 'selected_profiles']);
        Object.entries(d).forEach(([key, value]) => {
            if (skip.has(key)) return;
            const el = form.querySelector(`[name="${key}"]`);
            if (el && el.type !== 'checkbox' && el.type !== 'radio') {
                el.value = Array.isArray(value) ? value.join('\n') : value;
            }
        });

        syncOptionalText();
        // Re-apply saved text that syncOptionalText may have overwritten
        if (d.scope && scopeEl)          scopeEl.value        = d.scope;
        if (d.deliverables && deliverablesEl) deliverablesEl.value = d.deliverables;
    };

    const initDraft = () => {
        const notice     = document.querySelector('[data-draft-notice]');
        const clearBtn   = notice?.querySelector('[data-draft-clear]');
        const restoreBtn = notice?.querySelector('[data-draft-restore]');

        let saved = null;
        try {
            const raw = localStorage.getItem(DRAFT_KEY);
            if (raw) saved = JSON.parse(raw);
        } catch {}

        const FORTY_EIGHT_H = 48 * 3600 * 1000;
        const isRecent = saved && (Date.now() - saved.ts < FORTY_EIGHT_H);

        if (isRecent && saved.data?.client_name && notice) {
            const when = new Date(saved.ts).toLocaleString('pt-PT', {
                day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit',
            });
            const clientEl = notice.querySelector('[data-draft-client]');
            const timeEl   = notice.querySelector('[data-draft-time]');
            if (clientEl) clientEl.textContent = `"${saved.data.client_name}"`;
            if (timeEl)   timeEl.textContent   = when;
            notice.hidden = false;
        }

        clearBtn?.addEventListener('click', () => {
            try { localStorage.removeItem(DRAFT_KEY); } catch {}
            if (notice) notice.hidden = true;
        });

        restoreBtn?.addEventListener('click', () => {
            if (saved) restoreDraft(saved);
            if (notice) notice.hidden = true;
        });

        form?.addEventListener('input',  saveDraft);
        form?.addEventListener('change', saveDraft);
        form?.addEventListener('submit', () => {
            try { localStorage.removeItem(DRAFT_KEY); } catch {}
        });
    };

    // ── Service change wires scope/deliverables reset ─────────────────────────
    select?.addEventListener('change', () => {
        if (scopeEl) scopeEl.value = '';
        if (deliverablesEl) deliverablesEl.value = '';
        render();
    });
    packageSelect?.addEventListener('change',    () => renderSidePanel(data[select.value]));
    complexitySelect?.addEventListener('change', () => renderSidePanel(data[select.value]));
    Object.values(targets).forEach(t => t?.addEventListener('change', syncOptionalText));

    // ── Boot ──────────────────────────────────────────────────────────────────
    render();
    initFinancials();
    initCharCounters();
    initSelectAll();
    initCoverPreview();
    initDraft();
})();
