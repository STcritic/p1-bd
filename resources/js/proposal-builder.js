(() => {
    const dataEl = document.getElementById('proposal-builder-data');
    if (!dataEl) return;

    const { serviceQuestions: data, packages, complexity, oldSelection: previous } =
        JSON.parse(dataEl.textContent);

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const form             = document.querySelector('.proposal-builder-form');
    const select           = document.querySelector('[data-proposal-service]');
    let expenseUpdater = null; // set by initExpenseItems
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
        const policy  = item.commercialPolicy ?? {};

        let policySection = '';
        if (policy.bands && policy.bands.length) {
            const bandRows = policy.bands.map(b => {
                const rate = b.rate != null ? `${b.rate}%` : `${b.rate_min}–${b.rate_max}%`;
                return `<tr><td>${esc(b.label)}</td><td><strong>${rate}</strong></td><td>${b.guarantee_days}d</td></tr>`;
            }).join('');
            policySection = `
                <div class="proposal-side-policy-sep"><strong>Tabela de honorários (política interna)</strong></div>
                <p class="proposal-policy-model-note">${esc(policy.model_label ?? '')}</p>
                <table class="proposal-policy-side-table">
                    <thead><tr><th>Faixa salarial</th><th>Fee</th><th>Garantia</th></tr></thead>
                    <tbody>${bandRows}</tbody>
                </table>
                ${policy.mass_note ? `<p class="proposal-policy-note">${esc(policy.mass_note)}</p>` : ''}
                ${policy.guarantee ? `<div class="proposal-side-guarantee">
                    <strong>Garantia</strong>
                    <p>${esc(policy.guarantee.note ?? '')}</p>
                    <ul>${(policy.guarantee.credit_options ?? []).map(c =>
                        `<li>Crédito ${c.credit_pct}% se saída até ${c.within_days}d</li>`
                    ).join('')}</ul>
                </div>` : ''}
            `;
        }

        pricingEl.innerHTML = `
            <strong>${esc(pkg.label ?? 'Pacote')}</strong>
            <p>${esc(pkg.pricing ?? item.pricing?.base ?? '')}</p>
            <small>${esc(cxLabel)}</small>
            ${drivers.length ? `<ul>${drivers.map(d => `<li>${esc(d)}</li>`).join('')}</ul>` : ''}
            ${Object.keys(ranges).length
                ? `<div class="proposal-price-ranges">${Object.entries(ranges).map(([l, v]) => `<span>${esc(l)}</span><strong>${esc(v)}</strong>`).join('')}</div>`
                : ''}
            ${policySection}
        `;

        profilesEl.innerHTML = (item.profiles ?? []).map(p =>
            `<article><span>${esc(p.key)}</span><p>${esc(p.text)}</p></article>`
        ).join('');
    };

    // ── Recruitment-specific UI (salary calc + policy editor) ────────────────
    const initRecruitmentFields = () => {
        const SLUG          = 'recrutamento-seleccao';
        const salaryCalcEl  = document.querySelector('[data-salary-calc]');
        const policyEdEl    = document.querySelector('[data-policy-editor]');
        const salaryInput   = document.querySelector('[data-candidate-salary]');
        const feeInput      = form?.querySelector('[data-fee-input]');
        const bandLabelEl   = document.querySelector('[data-salary-band-label]');
        const feePreviewEl  = document.querySelector('[data-salary-fee-preview]');
        const currencyInput = form?.querySelector('[name="currency"]');
        const salaryRowEl   = document.querySelector('[data-salary-row]');

        const policyBands = data[SLUG]?.commercialPolicy?.bands ?? [];

        const isHeadhunting = () =>
            document.querySelector('[name="recruit_type"]:checked')?.value === 'headhunting';

        const findBand = (salary) => {
            if (isHeadhunting()) {
                // Executive band: rate_min / rate_max
                return policyBands.find(b => b.rate_min != null) ?? null;
            }
            if (!salary || salary <= 0) return null;
            const simple = policyBands.filter(b => b.rate != null);
            if (salary <= 1000000) return simple[0] ?? null;
            if (salary <= 2000000) return simple[1] ?? null;
            return simple[2] ?? null;
        };

        const updateSalaryCalc = () => {
            const isHH     = isHeadhunting();
            const salary   = parseFloat(salaryInput?.value) || 0;
            const currency = currencyInput?.value?.trim() || '';
            const band     = findBand(salary);

            // Headhunting: show rate range, no auto-fill (negotiated)
            if (isHH) {
                if (salaryRowEl) salaryRowEl.hidden = false;
                if (!band) {
                    if (bandLabelEl)  bandLabelEl.textContent  = '—';
                    if (feePreviewEl) feePreviewEl.textContent = '';
                    return;
                }
                const rateLabel = `${band.rate_min}–${band.rate_max}%`;
                if (bandLabelEl)  bandLabelEl.textContent  = `${band.label} — ${rateLabel}`;
                if (feePreviewEl) {
                    if (salary > 0) {
                        const lo = salary * (band.rate_min / 100);
                        const hi = salary * (band.rate_max / 100);
                        feePreviewEl.textContent = `Intervalo: ${fmt(lo, currency)} – ${fmt(hi, currency)}`;
                    } else {
                        feePreviewEl.textContent = `Fee negociado — insira o valor acordado`;
                    }
                }
                return; // never auto-fill fee for headhunting
            }

            // Standard: show band + auto-fill fee
            if (salaryRowEl) salaryRowEl.hidden = false;
            if (!band || salary <= 0) {
                if (bandLabelEl)  bandLabelEl.textContent  = '—';
                if (feePreviewEl) feePreviewEl.textContent = '';
                return;
            }

            const fee = salary * (band.rate / 100);
            if (bandLabelEl)  bandLabelEl.textContent  = `${band.label} — ${band.rate}% / ${band.guarantee_days}d`;
            if (feePreviewEl) feePreviewEl.textContent = `Fee calculado: ${fmt(fee, currency)}`;

            // Always update the fee field from calculator
            if (feeInput) {
                feeInput.value = fee.toFixed(2);
                feeInput.dispatchEvent(new InputEvent('input', { bubbles: true }));
            }
        };

        const toggleRecruitmentUI = () => {
            const isRec = select?.value === SLUG;
            if (salaryCalcEl) salaryCalcEl.hidden = !isRec;
            if (policyEdEl)   policyEdEl.hidden   = !isRec;
        };

        salaryInput?.addEventListener('input', updateSalaryCalc);
        currencyInput?.addEventListener('input', updateSalaryCalc);

        // Headhunting / standard radio change
        document.querySelectorAll('[name="recruit_type"]').forEach(r =>
            r.addEventListener('change', () => {
                updateSalaryCalc();
                // Clear fee when switching to headhunting (negotiated)
                if (isHeadhunting() && feeInput) feeInput.value = '';
            })
        );

        select?.addEventListener('change', () => {
            toggleRecruitmentUI();
            if (select.value === SLUG) {
                // Re-run calc in case salary was already filled
                updateSalaryCalc();
            } else if (feeInput) {
                feeInput.value = '';
            }
        });

        toggleRecruitmentUI();
        // Run on init if service is already recruitment
        if (select?.value === SLUG) updateSalaryCalc();
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
        const expensesInput = form?.querySelector('[data-expenses-total]');
        const vatRateInput  = form?.querySelector('[name="vat_rate"]');
        const currencyInput = form?.querySelector('[name="currency"]');

        const update = () => {
            if (!financialsEl) return;
            const fee      = parseFloat(feeInput?.value)     || 0;
            const expenses = parseFloat(expensesInput?.value) || 0;
            const vatRate  = parseFloat(vatRateInput?.value)  || 0;
            const currency = currencyInput?.value?.trim()     || '';
            const subtotal = fee + expenses;
            const vat      = subtotal * (vatRate / 100);
            const total    = subtotal + vat;

            if (subtotal === 0) {
                financialsEl.innerHTML = '<p class="proposal-financial-empty">Preencha os honorários para ver o resumo.</p>';
                return;
            }

            const expenseRows = [];
            form?.querySelectorAll('.proposal-expense-row').forEach(row => {
                const label  = row.querySelector('.proposal-expense-label')?.value?.trim();
                const amount = parseFloat(row.querySelector('.proposal-expense-amount')?.value) || 0;
                if (amount > 0) expenseRows.push({ label: label || 'Despesa', amount });
            });

            financialsEl.innerHTML = `
                <div class="proposal-financial-row"><span>Honorários</span><strong>${fmt(fee, currency)}</strong></div>
                ${expenseRows.length > 1
                    ? expenseRows.map(r => `<div class="proposal-financial-row proposal-financial-expense"><span>${esc(r.label)}</span><strong>${fmt(r.amount, currency)}</strong></div>`).join('')
                    : (expenses > 0 ? `<div class="proposal-financial-row"><span>Despesas</span><strong>${fmt(expenses, currency)}</strong></div>` : '')}
                <div class="proposal-financial-row proposal-financial-sub"><span>Subtotal</span><strong>${fmt(subtotal, currency)}</strong></div>
                <div class="proposal-financial-row"><span>IVA${vatRate > 0 ? ` (${vatRate}%)` : ''}</span><strong>${fmt(vat, currency)}</strong></div>
                <div class="proposal-financial-row proposal-financial-total"><span>Total</span><strong>${fmt(total, currency)}</strong></div>
            `;
        };

        [feeInput, expensesInput, vatRateInput, currencyInput].forEach(el =>
            el?.addEventListener('input', update)
        );
        update();

        return update;
    };

    // ── Expense line items ────────────────────────────────────────────────────
    const initExpenseItems = (financialUpdate) => {
        const rowsEl       = document.querySelector('[data-expense-rows]');
        const totalInput   = document.querySelector('[data-expenses-total]');
        const totalDisplay = document.querySelector('[data-expenses-total-display]');
        const addBtn       = document.querySelector('[data-add-expense]');
        const currencyInput = form?.querySelector('[name="currency"]');
        if (!rowsEl || !totalInput) return;

        const reindex = () => {
            rowsEl.querySelectorAll('.proposal-expense-row').forEach((row, i) => {
                row.querySelector('.proposal-expense-label').name  = `expense_items[${i}][label]`;
                row.querySelector('.proposal-expense-amount').name = `expense_items[${i}][amount]`;
            });
        };

        const recalcTotal = () => {
            reindex();
            let total = 0;
            rowsEl.querySelectorAll('.proposal-expense-amount').forEach(inp => {
                total += parseFloat(inp.value) || 0;
            });
            totalInput.value = total;
            const currency = currencyInput?.value?.trim() || '';
            if (totalDisplay) {
                totalDisplay.textContent = total > 0 ? fmt(total, currency) : '';
            }
            totalInput.dispatchEvent(new Event('input'));
            if (financialUpdate) financialUpdate();
        };

        const createRow = (label, amount) => {
            const idx = rowsEl.querySelectorAll('.proposal-expense-row').length;
            const div = document.createElement('div');
            div.className = 'proposal-expense-row';
            div.innerHTML = `
                <input type="text"   class="proposal-expense-label"  name="expense_items[${idx}][label]"  placeholder="Descrição" value="${esc(String(label ?? ''))}">
                <input type="number" class="proposal-expense-amount" name="expense_items[${idx}][amount]" min="0" step="0.01" placeholder="0.00" value="${amount != null && amount !== '' ? esc(String(amount)) : ''}">
                <button type="button" class="proposal-remove-expense" aria-label="Remover">×</button>
            `;
            div.querySelector('.proposal-expense-amount').addEventListener('input', recalcTotal);
            div.querySelector('.proposal-expense-label').addEventListener('input',  recalcTotal);
            div.querySelector('.proposal-remove-expense').addEventListener('click', () => {
                div.remove();
                recalcTotal();
            });
            rowsEl.appendChild(div);
        };

        const loadTypes = (types) => {
            rowsEl.innerHTML = '';
            (types || []).forEach(t => createRow(t.label, ''));
            recalcTotal();
        };

        addBtn?.addEventListener('click', () => {
            createRow('', '');
            rowsEl.querySelector('.proposal-expense-row:last-child .proposal-expense-label')?.focus();
            recalcTotal();
        });

        currencyInput?.addEventListener('input', recalcTotal);

        // Restore from prefill / old() / previous edit
        const savedItems    = previous.expense_items;
        const savedExpenses = parseFloat(previous.expenses) || 0;

        if (Array.isArray(savedItems) && savedItems.length > 0) {
            savedItems.forEach(item => createRow(item.label ?? '', item.amount ?? ''));
            recalcTotal();
        } else if (savedExpenses > 0) {
            createRow('Despesas', savedExpenses);
            recalcTotal();
        } else {
            const item = data[select?.value];
            loadTypes(item?.expenseTypes);
        }

        // When service changes, reload expense types only if all amounts are zero
        select?.addEventListener('change', () => {
            const hasValues = Array.from(rowsEl.querySelectorAll('.proposal-expense-amount'))
                .some(inp => parseFloat(inp.value) > 0);
            if (!hasValues) {
                const item = data[select.value];
                loadTypes(item?.expenseTypes);
            }
        });
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
        const skip = new Set(['service_slug', 'selected_approaches', 'selected_modules', 'selected_deliverables', 'selected_profiles', 'expense_items']);
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
        let submitted = false;
        form?.addEventListener('submit', (event) => {
            if (submitted) {
                event.preventDefault();
                return;
            }

            submitted = true;
            form.querySelectorAll('button[type="submit"]').forEach(button => {
                button.disabled = true;
                button.textContent = 'A gerar...';
            });

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
    const financialUpdate = initFinancials();
    initExpenseItems(financialUpdate);
    initRecruitmentFields();
    initCharCounters();
    initSelectAll();
    initCoverPreview();
    initDraft();
})();
