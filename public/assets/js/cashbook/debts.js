/**
 * Debts Tab Logic
 */

window.renderDebtsTab = function (container) {
    window.injectHTML(container, `
        <!-- Layer 1: Debt Overview -->
        <div class="utang-overview-card" style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:20px 24px; margin-bottom:32px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="font-size:16px; font-weight:700; color:var(--text); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <i class="fas fa-hand-holding-dollar" style="color:var(--text-muted);"></i> Debt Management Center
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px;">
                <div style="padding:16px; background:rgba(245,158,11,0.05); border-radius:12px; border:1px solid rgba(245,158,11,0.15);">
                    <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total Hutang</div>
                    <div id="debt-val-total-payable" style="font-size:22px; font-weight:800; color:#f59e0b; font-family:'JetBrains Mono', monospace;">Rp 0</div>
                    <div id="debt-count-payable" style="font-size:11px; color:var(--text-muted); margin-top:4px;">0 hutang aktif</div>
                </div>
                <div style="padding:16px; background:rgba(16,185,129,0.05); border-radius:12px; border:1px solid rgba(16,185,129,0.15);">
                    <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total Piutang</div>
                    <div id="debt-val-total-receivable" style="font-size:22px; font-weight:800; color:#10b981; font-family:'JetBrains Mono', monospace;">Rp 0</div>
                    <div id="debt-count-receivable" style="font-size:11px; color:var(--text-muted); margin-top:4px;">0 piutang aktif</div>
                </div>
                <div style="padding:16px; background:rgba(59,130,246,0.05); border-radius:12px; border:1px solid rgba(59,130,246,0.15);">
                    <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Sudah Lunas</div>
                    <div id="debt-val-paid-off" style="font-size:22px; font-weight:800; color:#3b82f6; font-family:'JetBrains Mono', monospace;">Rp 0</div>
                    <div id="debt-count-paid-off" style="font-size:11px; color:var(--text-muted); margin-top:4px;">0 pembayaran</div>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:flex-end; margin-bottom:24px;">
            <button class="btn btn-accent" onclick="openModal('modal-add-debt')" style="border-radius:24px; padding:10px 20px; font-size:13px; box-shadow:0 4px 12px rgba(59,130,246,0.25);">
                <i class="fas fa-plus"></i> Catat Utang / Piutang
            </button>
        </div>

        <!-- Layer 2: Debt & Piutang Lists -->
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:32px;">
            <div>
                <div style="padding-bottom:12px; margin-bottom:20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:15px; font-weight:700; color:var(--text);"><i class="fas fa-hand-holding-dollar" style="color:#f59e0b; margin-right:8px;"></i>Hutang Saya</span>
                    <span id="dt-count-payable" style="font-size:11px; background:var(--surface2); padding:4px 10px; border-radius:12px; font-weight:600;">0 items</span>
                </div>
                <div id="utang-list-payable" style="display:flex; flex-direction:column; gap:16px;"></div>
            </div>
            <div>
                <div style="padding-bottom:12px; margin-bottom:20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:15px; font-weight:700; color:var(--text);"><i class="fas fa-hand-holding-medical" style="color:#10b981; margin-right:8px;"></i>Piutang Saya</span>
                    <span id="dt-count-receivable" style="font-size:11px; background:var(--surface2); padding:4px 10px; border-radius:12px; font-weight:600;">0 items</span>
                </div>
                <div id="utang-list-receivable" style="display:flex; flex-direction:column; gap:16px;"></div>
            </div>
        </div>

        <!-- Layer 3: Smart Insight -->
        <div id="utang-insight-box" style="margin-top:32px; background:linear-gradient(135deg, rgba(59,130,246,0.1) 0%, rgba(59,130,246,0.02) 100%); border:1px solid rgba(59,130,246,0.2); border-left:4px solid #3b82f6; border-radius:var(--radius); padding:20px; display:flex; gap:16px; align-items:flex-start;">
            <div style="width:40px; height:40px; border-radius:50%; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#3b82f6; font-size:16px;">
                <i class="fas fa-lightbulb"></i>
            </div>
            <div>
                <div style="font-size:13px; font-weight:700; color:var(--text); margin-bottom:6px;">Assistant Insight</div>
                <div id="utang-smart-insight" style="font-size:13px; color:var(--text-muted); line-height:1.6;">
                    <i class="fas fa-spinner fa-spin"></i> Menganalisa struktur utang...
                </div>
            </div>
        </div>
    `);

    setTimeout(() => { if (typeof window.loadDebtsTab === 'function') window.loadDebtsTab(); }, 1);

    return function cleanup() {
        window._debtInitDone = false;
    };
};


let allDebts = [];

// Track the currently selected backup interval in the UI
window._selectedBackupInterval = window._selectedBackupInterval || 7;
let _selectedBackupInterval = window._selectedBackupInterval;

async function loadDebts() {
    const uPayable = document.getElementById('utang-list-payable');
    const uReceivable = document.getElementById('utang-list-receivable');
    if (uPayable) uPayable.innerHTML = '<div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat...</div>';
    if (uReceivable) uReceivable.innerHTML = '<div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat...</div>';

    try {
        if (typeof localAPI !== 'undefined' && localAPI.debts) {
            allDebts = await localAPI.debts.getAll();
        }
    } catch (e) {
        console.error('Failed to load debts:', e);
        return;
    }
    renderDebtGrid();
}

function renderDebtGrid() {
    // Summary metrics
    let totalPayable = 0, countPayable = 0;
    let totalReceivable = 0, countReceivable = 0;
    let countPaidOff = 0;

    const payableList = [];
    const receivableList = [];

    (allDebts || []).forEach(d => {
        const total = parseFloat(d.total_amount || 0);
        const paid = parseFloat(d.paid_amount || 0);
        const remaining = Math.max(0, total - paid);

        if (d.status === 'paid_off') {
            countPaidOff++;
        } else {
            if (d.debt_type === 'payable') {
                totalPayable += remaining; countPayable++;
                payableList.push({ ...d, total, paid, remaining });
            } else {
                totalReceivable += remaining; countReceivable++;
                receivableList.push({ ...d, total, paid, remaining });
            }
        }
    });

    // 1. Update Overview Card
    const setEl = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    setEl('debt-val-total-payable', rp(totalPayable));
    setEl('debt-count-payable', `${countPayable} hutang aktif`);
    setEl('debt-val-total-receivable', rp(totalReceivable));
    setEl('debt-count-receivable', `${countReceivable} piutang aktif`);
    setEl('debt-val-paid-off', `${countPaidOff}`);
    setEl('debt-count-paid-off', 'pembayaran lunas');

    // 2. Render Lists
    setEl('dt-count-payable', `${countPayable} items`);
    setEl('dt-count-receivable', `${countReceivable} items`);

    const renderItem = (d, type) => {
        const pct = d.total > 0 ? Math.min(100, (d.paid / d.total) * 100) : 0;
        const tClass = type === 'payable' ? 'payable' : 'receivable';
        const bgFill = type === 'payable' ? '#f59e0b' : '#10b981';
        const btnLabel = type === 'payable' ? 'Bayar Cicilan' : 'Catat Penerimaan';
        const icon = type === 'payable' ? 'fa-money-bill-wave' : 'fa-hand-holding-dollar';

        const rawName = d.name || d.debt_name || 'Tanpa Nama';
        const cleanName = rawName.replace(/&amp;/g, '&').replace(/'/g, "\\'");
        const safeType = d.debt_type;

        return `
        <div class="utang-item">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="font-size:15px; font-weight:700; color:var(--text);">${rawName}</div>
                <span class="utang-badge ${tClass}">${pct.toFixed(0)}% LUNAS</span>
            </div>
            
            <div class="utang-prog-bg">
                <div class="utang-prog-fill" style="width:${pct}%; background:${bgFill};"></div>
            </div>
            
            <div style="display:flex; flex-direction:column; gap:6px; margin-top:4px;">
                <div class="utang-meta-row">
                    <span>Total ${type === 'payable' ? 'Hutang' : 'Piutang'}</span>
                    <span class="utang-meta-val">${rp(d.total)}</span>
                </div>
                <div class="utang-meta-row">
                    <span>Sudah Dibayar</span>
                    <span class="utang-meta-val" style="color:${bgFill};">${rp(d.paid)}</span>
                </div>
                ${d.due_date ? `
                <div class="utang-meta-row">
                    <span>Jatuh Tempo</span>
                    <span class="utang-meta-val" style="color:var(--text);">${new Date(d.due_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' })}</span>
                </div>` : ''}
                <div class="utang-meta-row" style="border-top:1px solid var(--border); padding-top:6px; margin-top:2px;">
                    <span style="font-weight:600; color:var(--text);">Sisa</span>
                    <span class="utang-meta-val" style="font-size:14px; font-weight:800; color:var(--text);">${rp(d.remaining)}</span>
                </div>
            </div>
            
            <div class="utang-actions">
                <button class="utang-btn primary" onclick="openPayInstallment('${d.id}', '${cleanName}', ${d.remaining}, '${safeType}')">
                    <i class="fas ${icon}"></i> ${btnLabel}
                </button>
                <button class="utang-btn secondary" data-did="${d.id}" onclick="safeDeleteDebt(this)" style="max-width:40px;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
    };

    const uPay = document.getElementById('utang-list-payable');
    if (uPay) {
        if (payableList.length > 0) {
            const frag = document.createDocumentFragment();
            payableList.forEach(d => {
                const el = document.createElement('div');
                el.innerHTML = renderItem(d, 'payable');
                frag.appendChild(el.firstElementChild);
            });
            uPay.replaceChildren(frag);
        } else {
            uPay.innerHTML = '<div class="empty" style="padding:40px 0;"><i class="fas fa-check-circle" style="color:#10b981;font-size:32px;margin-bottom:12px;"></i><div style="color:var(--text);">Semua hutang sudah lunas!</div></div>';
        }
    }

    const uRec = document.getElementById('utang-list-receivable');
    if (uRec) {
        if (receivableList.length > 0) {
            const frag = document.createDocumentFragment();
            receivableList.forEach(d => {
                const el = document.createElement('div');
                el.innerHTML = renderItem(d, 'receivable');
                frag.appendChild(el.firstElementChild);
            });
            uRec.replaceChildren(frag);
        } else {
            uRec.innerHTML = '<div class="empty" style="padding:40px 0;"><i class="fas fa-box-open" style="font-size:28px;margin-bottom:12px;"></i><div>Tidak ada piutang aktif.</div></div>';
        }
    }

    // 3. Update Insight
    const insightBox = document.getElementById('utang-smart-insight');
    if (insightBox) {
        if (totalPayable === 0 && totalReceivable === 0) {
            insightBox.innerHTML = "Selamat! Kamu tidak memiliki hutang maupun piutang aktif saat ini. Keuanganmu sangat sehat.";
        } else if (totalPayable > 0 && totalReceivable === 0) {
            const biggest = payableList.sort((a, b) => b.remaining - a.remaining)[0];
            insightBox.innerHTML = `Fokus lunasi <strong>${biggest.name || biggest.debt_name}</strong> terlebih dahulu karena memiliki sisa terbesar (${rp(biggest.remaining)}). Tetap konsisten menyisihkan budget setiap bulan!`;
        } else if (totalReceivable > totalPayable) {
            insightBox.innerHTML = `Kondisi sangat baik! Total piutangmu (${rp(totalReceivable)}) lebih besar dari hutang (${rp(totalPayable)}). Pastikan kamu mem-follow up piutang yang sudah mendekati jatuh tempo.`;
        } else {
            insightBox.innerHTML = `Kamu memiliki kewajiban hutang sebesar <strong>${rp(totalPayable)}</strong>. Coba alokasikan sebagian dari penagihan piutangmu (${rp(totalReceivable)}) untuk mempercepat pelunasan.`;
        }
    }
}

async function submitDebt() {
    const name = document.getElementById('debt-name')?.value?.trim();
    const type = document.getElementById('debt-type')?.value || 'payable';
    const total = parseFloat(document.getElementById('debt-total')?.value || 0);
    const due = document.getElementById('debt-due')?.value || null;
    const notes = document.getElementById('debt-notes')?.value?.trim() || '';

    if (!name) return toast('Nama wajib diisi', 'e');
    if (!total || total <= 0) return toast('Total jumlah harus lebih dari 0', 'e');

    const debt = {
        id: 'debt_' + Date.now(),
        name, debt_type: type,
        total_amount: total, paid_amount: 0,
        due_date: due, notes, status: 'active',
        created_at: new Date().toISOString()
    };
    try {
        await localAPI.debts.save(debt);
        toast('Utang/piutang berhasil ditambahkan!', 's');
        if (typeof closeModal === 'function') closeModal('modal-add-debt');
        ['debt-name', 'debt-total', 'debt-due', 'debt-notes'].forEach(id => { const el = document.getElementById(id); if (el) el.value = ''; });
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) { toast(e?.message || 'Gagal menyimpan', 'e'); }
}

function openPayInstallment(debtId, debtName, remaining, debtType) {
    const isPayable = debtType === 'payable';
    const debtIdEl = document.getElementById('inst-debt-id');
    if (debtIdEl) debtIdEl.value = debtId;

    // Adapt modal title and label based on debt type
    const titleEl = document.querySelector('#modal-pay-installment .modal-title');
    if (titleEl) titleEl.innerHTML = isPayable
        ? '<i class="fas fa-money-bill-wave" style="color:var(--accent);margin-right:8px"></i>Bayar Cicilan'
        : '<i class="fas fa-arrow-down-to-line" style="color:var(--info);margin-right:8px"></i>Catat Penerimaan Piutang';

    const accLabelEl = document.querySelector('label[for="inst-account-id"], #modal-pay-installment .flabel');
    if (accLabelEl) accLabelEl.textContent = isPayable ? 'Bayar dari Akun' : 'Masuk ke Akun';

    const dnEl = document.getElementById('inst-debt-name-display');
    if (dnEl) dnEl.textContent = (isPayable ? 'Membayar cicilan untuk: ' : 'Mencatat penerimaan piutang: ') + debtName;

    const dremEl = document.getElementById('inst-debt-remaining-display');
    if (dremEl) dremEl.textContent = (isPayable ? 'Sisa utang: ' : 'Sisa piutang: ') + rp(remaining);

    const today = new Date().toISOString().split('T')[0];
    const dtEl = document.getElementById('inst-date');
    if (dtEl) dtEl.value = today;

    if (document.getElementById('inst-amount')) document.getElementById('inst-amount').value = '';
    if (document.getElementById('inst-notes')) document.getElementById('inst-notes').value = '';

    if (debtIdEl) debtIdEl.dataset.debtType = debtType;

    // Populate account dropdown from global accounts array
    const sel = document.getElementById('inst-account-id');
    const balEl = document.getElementById('inst-account-balance');
    if (sel) {
        sel.innerHTML = '<option value="">-- Pilih akun --</option>';
        (accounts || []).forEach(a => {
            const opt = document.createElement('option');
            opt.value = a.id;
            opt.textContent = a.name + ' (' + rp(a.balance_cached || 0) + ')';
            sel.appendChild(opt);
        });

        const amountInput = document.getElementById('inst-amount');
        const payBtn = document.querySelector('#modal-pay-installment .btn-accent');

        function checkBalance() {
            const acc = (accounts || []).find(a => String(a.id) === String(sel.value));
            const bal = acc ? parseFloat(acc.balance_cached || 0) : null;
            const amt = parseFloat(amountInput?.value) || 0;
            if (acc) {
                if (isPayable && bal < amt && amt > 0) {
                    if (balEl) balEl.innerHTML = `<span style="color:var(--danger);"><i class="fas fa-exclamation-circle"></i> Saldo tidak cukup! Saldo: ${rp(bal)}</span>`;
                    if (payBtn) payBtn.disabled = true;
                } else {
                    const hint = isPayable ? 'Saldo tersedia: ' : 'Saldo saat ini: ';
                    if (balEl) balEl.innerHTML = `<span style="color:var(--text-muted);">${hint}${rp(bal)}</span>`;
                    if (payBtn) payBtn.disabled = false;
                }
            } else {
                if (balEl) balEl.textContent = '';
                if (payBtn) payBtn.disabled = false;
            }
        }

        sel.onchange = checkBalance;
        if (amountInput) amountInput.oninput = checkBalance;

        // Pre-select first active account and run check
        if (sel.options.length > 1) { sel.selectedIndex = 1; checkBalance(); }
    }

    if (typeof openModal === 'function') openModal('modal-pay-installment');
}

async function submitInstallment() {
    const debtIdEl = document.getElementById('inst-debt-id');
    if (!debtIdEl) return;
    const debtId = debtIdEl.value;
    const accountId = document.getElementById('inst-account-id')?.value;
    const amount = parseFloat(document.getElementById('inst-amount')?.value || 0);
    const dateVal = document.getElementById('inst-date')?.value || new Date().toISOString().split('T')[0];
    const notes = document.getElementById('inst-notes')?.value?.trim() || '';
    const debtType = debtIdEl.dataset.debtType || 'payable';

    if (!debtId) return toast('ID utang tidak ditemukan', 'e');
    if (!accountId) return toast('Pilih akun', 'e');
    if (!amount || amount <= 0) return toast('Jumlah harus lebih dari 0', 'e');

    try {
        const allDbts = await localAPI.debts.getAll();
        const debt = allDbts.find(d => String(d.id) === String(debtId));
        if (!debt) return toast('Data utang tidak ditemukan', 'e');

        debt.paid_amount = Math.min(debt.total_amount, parseFloat(debt.paid_amount || 0) + amount);
        if (debt.paid_amount >= debt.total_amount) debt.status = 'paid_off';
        await localAPI.debts.save(debt);

        // Record transaction
        const txType = debtType === 'payable' ? 'expense' : 'income';
        const tx = {
            id: 'tx_' + Date.now(), type: txType,
            account_id: accountId, amount,
            transaction_date: dateVal + 'T00:00:00',
            note: (debtType === 'payable' ? 'Bayar cicilan: ' : 'Terima piutang: ') + (debt.name || ''),
            created_at: new Date().toISOString()
        };
        await localAPI.transactions.save(tx);

        // Reload globals
        allTxList = await localAPI.transactions.getAll();
        allTxList.sort((a, b) => new Date(b.transaction_date) - new Date(a.transaction_date));
        window.allTxList = allTxList;
        accounts = await localAPI.accounts.getAll();
        window.accounts = accounts;

        toast(debtType === 'payable' ? 'Cicilan berhasil dibayar!' : 'Penerimaan piutang dicatat!', 's');
        if (typeof closeModal === 'function') closeModal('modal-pay-installment');
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) { toast(e?.message || 'Gagal menyimpan cicilan', 'e'); }
}

function safeDeleteDebt(btn) {
    const id = btn.dataset.did;
    const item = allDebts.find(x => String(x.id) === String(id));
    if (!item) return;
    const name = item.name || item.debt_name || 'Tanpa Nama';
    deleteDebt(id, name);
}

async function deleteDebt(debtId, debtName) {
    if (typeof confirmDialog === 'function') {
        confirmDialog(
            'Hapus Utang/Piutang',
            `Data "${debtName}" beserta riwayat cicilannya akan dihapus. Lanjutkan?`,
            'danger',
            async () => {
                try {
                    await localAPI.debts.delete(String(debtId));
                    toast('Data berhasil dihapus', 's');
                    if (typeof refreshUI === 'function') await refreshUI();
                } catch (e) { toast(e?.message || 'Gagal menghapus data', 'e'); }
            }
        );
    }
}

// Data & backup settings
async function loadBackupSettings() {
    try {
        if (typeof localAPI === 'undefined' || !localAPI.meta) return;

        const enabled = (await localAPI.meta.get('auto_backup_enabled')) === 'true' || (await localAPI.meta.get('auto_backup_enabled')) === true;
        const interval = parseInt(await localAPI.meta.get('backup_interval_days') || '7');
        const lastStr = await localAPI.meta.get('last_backup_at');

        // Toggle state
        const toggle = document.getElementById('ab-enabled-toggle');
        if (toggle) toggle.checked = enabled;
        _selectedBackupInterval = interval || 7;
        _updateToggleUI(enabled);

        // Card selection
        selectBackupInterval(_selectedBackupInterval, false);

        // Last / next backup info
        const lastEl = document.getElementById('ab-last-backup');
        const nextEl = document.getElementById('ab-next-backup');
        if (lastStr && lastStr !== 'null') {
            const last = new Date(lastStr);
            if (lastEl) lastEl.textContent = last.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            if (enabled) {
                const next = new Date(last.getTime() + _selectedBackupInterval * 86400000);
                if (nextEl) nextEl.textContent = next.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            } else {
                if (nextEl) nextEl.textContent = 'Nonaktif';
            }
        } else {
            if (lastEl) lastEl.textContent = 'Belum pernah';
            if (nextEl) nextEl.textContent = enabled ? 'Saat app dibuka berikutnya' : 'Nonaktif';
        }
    } catch (e) { console.warn('loadBackupSettings error', e); }
}

function _updateToggleUI(enabled) {
    const track = document.getElementById('ab-track');
    const thumb = document.getElementById('ab-thumb');
    const label = document.getElementById('ab-toggle-label');
    const opts = document.getElementById('ab-options');
    const hint = document.getElementById('ab-hint');
    if (!track) return;
    if (enabled) {
        track.style.background = 'var(--accent)';
        if (thumb) { thumb.style.left = '22px'; thumb.style.background = '#fff'; }
        if (label) { label.textContent = 'Aktif'; label.style.color = 'var(--accent)'; }
        if (opts) opts.style.display = 'block';
        if (hint) hint.style.display = 'none';
    } else {
        track.style.background = 'var(--surface3)';
        if (thumb) { thumb.style.left = '2px'; thumb.style.background = 'var(--text-muted)'; }
        if (label) { label.textContent = 'Nonaktif'; label.style.color = 'var(--text-muted)'; }
        if (opts) opts.style.display = 'none';
        if (hint) hint.style.display = 'block';
    }
}

function onAutoBackupToggle(checkbox) {
    _updateToggleUI(checkbox.checked);
}

function selectBackupInterval(days, autoSave = false) {
    _selectedBackupInterval = days;
    [1, 7, 30].forEach(d => {
        const card = document.getElementById(`ab-opt-${d}`);
        if (card) card.className = 'ab-opt-card' + (d === days ? ' selected' : '');
    });
    if (autoSave) saveBackupSettings();
}

async function saveBackupSettings() {
    const enabled = document.getElementById('ab-enabled-toggle')?.checked || false;
    try {
        await localAPI.meta.set('auto_backup_enabled', String(enabled));
        await localAPI.meta.set('backup_interval_days', String(_selectedBackupInterval));
        toast(enabled ? `Backup otomatis aktif setiap ${_selectedBackupInterval} hari!` : 'Backup otomatis dinonaktifkan', 's');
        await loadBackupSettings(); // refresh display
    } catch (e) { toast('Gagal menyimpan pengaturan backup', 'e'); }
}

async function exportData() {
    toast('Sedang menyiapkan file backup...', 'i');
    try {
        let installments = [];
        try { installments = await localAPI.installments.getAll(); } catch (_) { }

        const backupData = {
            exported_at: new Date().toISOString(),
            version: 1,
            data: {
                meta: await localAPI.meta.getAll(),
                categories: await localAPI.categories.getAll(),
                accounts: await localAPI.accounts.getAll(),
                transactions: await localAPI.transactions.getAll(),
                budgets: await localAPI.budgets.getAll(),
                debts: await localAPI.debts.getAll(),
                installments
            }
        };

        const blob = new Blob([JSON.stringify(backupData, null, 2)], { type: 'application/json' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = `cuan_cashbook_backup_${new Date().toISOString().slice(0, 10)}.json`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        toast('Backup berhasil diunduh!', 's');
        if (typeof closeModal === 'function') closeModal('modal-data');
    } catch (e) {
        toast('Gagal melakukan export data: ' + e.message, 'e');
    }
}

async function handleImportFile(input) {
    if (!input.files || input.files.length === 0) return;
    const file = input.files[0];

    if (file.type !== 'application/json' && !file.name.endsWith('.json')) {
        toast('File harus berformat .json', 'e');
        input.value = '';
        return;
    }

    toast('Sedang memulihkan data...', 'i');
    try {
        const reader = new FileReader();
        reader.onload = async (e) => {
            try {
                const parsed = JSON.parse(e.target.result);
                if (!parsed.data || typeof parsed.data !== 'object') throw new Error("Format JSON tidak valid");

                const d = parsed.data;

                const clearStore = (storeName) => localAPI._runOp(storeName, 'readwrite', store => store.clear());

                // Clear existing data (except meta, we just update it)
                if (d.categories) { await clearStore('categories'); for (const item of d.categories) await localAPI.categories.save(item); }
                if (d.accounts) { await clearStore('accounts'); for (const item of d.accounts) await localAPI.accounts.save(item); }
                if (d.transactions) { await clearStore('transactions'); for (const item of d.transactions) await localAPI.transactions.save(item); }
                if (d.budgets) { await clearStore('budgets'); for (const item of d.budgets) await localAPI.budgets.save(item); }
                if (d.debts) { await clearStore('debts'); for (const item of d.debts) await localAPI.debts.save(item); }

                // installments store may not exist in older DBs — skip gracefully
                if (d.installments && d.installments.length) {
                    try {
                        await clearStore('installments');
                        for (const item of d.installments) await localAPI.installments.save(item);
                    } catch (_) { }
                }
                if (d.meta) { for (const item of d.meta) await localAPI.meta.set(item.key, item.value); }

                toast('Restore Sukses! Memuat ulang Cashbook...', 's');
                if (typeof closeModal === 'function') closeModal('modal-data');
                setTimeout(() => window.location.reload(), 1500);
            } catch (err) {
                toast('Gagal membaca JSON: ' + err.message, 'e');
            }
        };
        reader.readAsText(file);
    } catch (e) {
        toast('Terjadi kesalahan sistem saat me-restore data', 'e');
    } finally {
        input.value = ''; // reset input
    }
}

// Expose to window
window.loadDebts = loadDebts;
window.submitDebt = submitDebt;
window.openPayInstallment = openPayInstallment;
window.submitInstallment = submitInstallment;
window.safeDeleteDebt = safeDeleteDebt;
window.deleteDebt = deleteDebt;

window.loadBackupSettings = loadBackupSettings;
window.onAutoBackupToggle = onAutoBackupToggle;
window.selectBackupInterval = selectBackupInterval;
window.exportData = exportData;
window.handleImportFile = handleImportFile;
