/**
 * Budgets Tab Logic
 */

window.renderBudgetsTab = function (container) {
    container.innerHTML = `
        <!-- ══ LAYER 1: HEADER SUMMARY ══ -->
        <div class="ang-summary-card">
            <div class="ang-sum-title">
                <span>Anggaran Bulan Ini</span>
                <input type="month" class="ang-month-picker" id="ang-filter-month" onchange="loadAnggaran()">
            </div>
            <div class="ang-sum-amounts">
                <div id="ang-sum-used">Rp 0</div>
                <div class="ang-sum-divider">digunakan dari</div>
                <div id="ang-sum-total">Rp 0</div>
            </div>
            <div class="ang-sum-progress-wrap">
                <div class="ang-sum-progress-bar" id="ang-sum-bar"></div>
            </div>
            <div class="ang-sum-footer">
                <div><span id="ang-sum-pct">0%</span> terpakai</div>
                <div style="text-align:right">Sisa: <strong id="ang-sum-left">Rp 0</strong></div>
            </div>
        </div>

        <!-- ══ LAYER 2: BUDGET PROGRESS VISUAL ══ -->
        <div class="card" style="margin-bottom:16px;">
            <div class="card-head"><span class="card-title">Progress Kategori</span></div>
            <div class="card-body-sm" id="ang-progress-list">
                <div class="empty"><i class="fas fa-spinner fa-spin"></i>Memuat progress...</div>
            </div>
        </div>

        <!-- ══ SMART INSIGHT (AI) ══ -->
        <div class="ang-insight-card" id="ang-smart-insight">
            <div class="ang-insight-icon"><i class="fas fa-lightbulb"></i></div>
            <div class="ang-insight-body">
                <div class="ang-insight-title">Insight Bulan Ini</div>
                <div class="ang-insight-text" id="ang-insight-text">Menganalisa pengeluaran...</div>
            </div>
        </div>

        <!-- ══ LAYER 3: CATEGORY BUDGET LIST ══ -->
        <div class="card">
            <div class="card-head" style="padding-bottom:12px; border-bottom:1px solid var(--border);">
                <span class="card-title">Daftar Kategori</span>
                <div style="display:flex;gap:8px;">
                    <button class="btn btn-ghost btn-sm" onclick="copyLastMonthBudget()" title="Copy budget bulan lalu">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="card-body-sm" id="ang-category-list" style="padding:0">
                <div class="empty" style="padding:32px 20px;"><i class="fas fa-folder-open"></i>Belum ada anggaran dibuat.<br><span style="font-size:12px;opacity:0.7;margin-top:6px;display:block;">Anggaran membantu kamu mengontrol pengeluaran.</span></div>
            </div>
        </div>

        <!-- ══ FLOATING ADD BUTTON ══ -->
        <button class="ang-fab" onclick="openModal('modal-budget')">
            <i class="fas fa-plus"></i> Tambah Anggaran
        </button>
    `);

    setTimeout(() => { if (typeof window.loadAnggaran === 'function') window.loadAnggaran(); }, 1);

    return function cleanup() {
        window._angInitDone = false;
    };
};


let _angBudgetItems = [];
let rolloverEnabled = false;

// Attempt to sync local rollover state on load
try {
    rolloverEnabled = JSON.parse(localStorage.getItem('bgt_rollover') || 'false');
} catch (e) { }

const PILLAR_META = {
    wajib: { label: 'Wajib', color: 'var(--info)', bg: 'rgba(59,130,246,0.08)', border: 'rgba(59,130,246,0.25)' },
    growth: { label: 'Growth', color: 'var(--accent)', bg: 'rgba(16,185,129,0.08)', border: 'rgba(16,185,129,0.25)' },
    lifestyle: { label: 'Lifestyle', color: 'var(--warning)', bg: 'rgba(245,158,11,0.08)', border: 'rgba(245,158,11,0.25)' },
    bocor: { label: 'Bocor', color: 'var(--danger)', bg: 'rgba(239,68,68,0.08)', border: 'rgba(239,68,68,0.25)' },
};

function toggleRollover(el) {
    rolloverEnabled = el.checked;
    localStorage.setItem('bgt_rollover', JSON.stringify(rolloverEnabled));
    const label = document.getElementById('ang-rollover-label');
    const val = document.getElementById('ang-val-rollover');
    if (rolloverEnabled) {
        if (label) { label.textContent = 'Aktif'; label.style.color = 'var(--accent)'; }
        if (val) { val.textContent = 'On'; val.style.color = 'var(--accent)'; }
        toast('Rollover aktif: sisa budget akan diteruskan ke bulan depan', 's');
    } else {
        if (label) { label.textContent = 'Nonaktif'; label.style.color = 'var(--text-muted)'; }
        if (val) { val.textContent = 'Off'; val.style.color = 'var(--text-muted)'; }
    }
    loadAnggaran();
}

function refreshRecommendations() {
    const el = document.getElementById('ang-recommendations');
    if (!el) return;
    el.innerHTML = '<div class="empty"><i class="fas fa-spinner fa-spin"></i>Menganalisa...</div>';
    setTimeout(() => computeRecommendations(), 400);
}

function computeRecommendations() {
    const el = document.getElementById('ang-recommendations');
    if (!el) return;
    if (!allTxList || !allTxList.length) {
        el.innerHTML = '<div class="empty"><i class="fas fa-database"></i>Tambah transaksi untuk mendapat rekomendasi</div>';
        return;
    }
    const picker = document.getElementById('ang-filter-month');
    const selMonth = picker ? picker.value : '';
    const now = new Date();
    // Analyze last 3 months
    const catAvg = {};
    const catCount = {};
    for (let m = 1; m <= 3; m++) {
        const d = new Date(now.getFullYear(), now.getMonth() - m, 1);
        const yr = d.getFullYear(), mo = d.getMonth() + 1;
        const monthTx = allTxList.filter(tx => {
            const td = new Date(tx.transaction_date);
            return td.getFullYear() === yr && (td.getMonth() + 1) === mo && tx.type === 'expense';
        });
        monthTx.forEach(tx => {
            const cat = tx.category?.name || 'Tak Berkategori';
            catAvg[cat] = (catAvg[cat] || 0) + parseFloat(tx.amount || 0);
            catCount[cat] = (catCount[cat] || 0) + 1;
        });
    }
    // Average per category
    const recs = Object.entries(catAvg)
        .map(([cat, total]) => ({ cat, avg: Math.round(total / 3), pillar: allTxList.find(tx => tx.category?.name === cat)?.category?.pillar || 'lainnya' }))
        .filter(r => r.avg > 0)
        .sort((a, b) => b.avg - a.avg)
        .slice(0, 5);

    if (!recs.length) {
        el.innerHTML = '<div class="empty"><i class="fas fa-chart-bar"></i>Belum cukup data (min. 1 bulan)</div>';
        return;
    }
    el.innerHTML = `
        <div style="font-size:11px;color:var(--text-muted);margin-bottom:10px;"><i class="fas fa-info-circle" style="margin-right:4px;"></i>Berdasarkan rata-rata 3 bulan lalu</div>
        ${recs.map(r => `
        <div class="rec-item">
            <div style="display:flex;align-items:center;gap:8px;flex:1;min-width:0;">
                <div class="rec-icon"><i class="fas ${(typeof CAT_ICONS !== 'undefined' && CAT_ICONS[r.cat] ? CAT_ICONS[r.cat] : ['fa-tag', '#64748b'])[0]}" style="color:${(typeof CAT_ICONS !== 'undefined' && CAT_ICONS[r.cat] ? CAT_ICONS[r.cat] : ['fa-tag', '#64748b'])[1]}"></i></div>
                <div style="min-width:0;">
                    <div style="font-size:12px;font-weight:600;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${r.cat}</div>
                    <div style="font-size:10px;color:var(--text-muted);margin-top:1px;">Rata-rata: ${rp(r.avg)}/bln</div>
                </div>
            </div>
            <span class="rec-badge" onclick="openBudgetWithSuggestion(${JSON.stringify(r.cat).replace(/"/g, '&quot;')}, ${r.avg})" title="Pakai saran ini">Pakai ${rp(Math.round(r.avg * 1.1))}</span>
        </div>`).join('')}
    `;
}

function openBudgetWithSuggestion(catName, suggestedAmt) {
    if (typeof openModal === 'function') openModal('modal-budget');
    setTimeout(() => {
        const limitEl = document.getElementById('bgt-limit');
        if (limitEl) limitEl.value = Math.round(suggestedAmt * 1.1);

        const cat = (categories || []).find(c => c.name === catName);
        if (cat) {
            const catVal = document.getElementById('bgt-cat-val');
            if (catVal) catVal.value = cat.id;

            if (typeof setCddDisplay === 'function') {
                const [ico, col] = (typeof CAT_ICONS !== 'undefined' && CAT_ICONS[cat.name]) ? CAT_ICONS[cat.name] : ['fa-tag', '#64748b'];
                setCddDisplay('cdd-bgt-icon', 'cdd-bgt-lbl', cat.name, ico, col, false);
            }
        }
    }, 200);
}

function renderBgtTrendChart(budgetsByMonth) {
    const el = document.getElementById('ang-trend-chart');
    if (!el) return;
    if (!budgetsByMonth || !budgetsByMonth.length) {
        el.innerHTML = '<div class="empty"><i class="fas fa-chart-line"></i>Belum ada data historis</div>';
        return;
    }
    const maxPct = Math.max(...budgetsByMonth.map(m => m.pct), 20);
    el.innerHTML = `
    <div style="display:flex;gap:20px;align-items:flex-end;">
        <div style="flex:1;">
            <div style="display:grid;grid-template-columns:repeat(${budgetsByMonth.length},1fr);gap:8px;align-items:flex-end;height:72px;margin-bottom:6px;">
                ${budgetsByMonth.map(m => {
        const h = Math.max(6, (m.pct / maxPct) * 72);
        const col = m.pct >= 100 ? 'var(--danger)' : m.pct > 80 ? 'var(--warning)' : 'var(--accent)';
        return `<div style="display:flex;flex-direction:column;align-items:center;gap:3px;justify-content:flex-end;height:72px;">
                        <div style="font-size:10px;font-weight:700;color:${col};">${m.pct.toFixed(0)}%</div>
                        <div class="bgt-trend-bar" data-tip="${m.label}: ${rp(m.used)} / ${rp(m.budget)}" style="height:${h}px;background:${col};width:100%;border-radius:3px 3px 0 0;"></div>
                    </div>`;
    }).join('')}
            </div>
            <div style="display:grid;grid-template-columns:repeat(${budgetsByMonth.length},1fr);gap:8px;border-top:1px solid var(--border);padding-top:6px;">
                ${budgetsByMonth.map(m => `<div style="font-size:10px;color:var(--text-muted);text-align:center;">${m.label}</div>`).join('')}
            </div>
        </div>
        <div style="width:120px;flex-shrink:0;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text-muted);margin-bottom:8px;">Ringkasan</div>
            <div style="font-size:11px;color:var(--text-sub);margin-bottom:4px;">Rata-rata pemakaian:</div>
            <div style="font-size:15px;font-weight:800;color:var(--text);font-family:'JetBrains Mono',monospace;">${(budgetsByMonth.reduce((a, m) => a + m.pct, 0) / budgetsByMonth.length).toFixed(0)}%</div>
            <div style="font-size:10px;color:var(--text-muted);margin-top:8px;">Bulan over budget:</div>
            <div style="font-size:15px;font-weight:800;color:${budgetsByMonth.filter(m => m.pct >= 100).length > 0 ? 'var(--danger)' : 'var(--accent)'};font-family:'JetBrains Mono',monospace;">${budgetsByMonth.filter(m => m.pct >= 100).length}x</div>
        </div>
    </div>`;
}

function renderBgtBarChart(budgets, timePct) {
    const el = document.getElementById('ang-bar-chart');
    if (!el) return;
    if (!budgets.length) {
        el.innerHTML = '<div class="empty"><i class="fas fa-chart-bar"></i>Belum ada budget</div>';
        return;
    }
    const sorted = [...budgets].sort((a, b) => b.pct - a.pct);
    el.innerHTML = '<div class="bgt-chart-wrap">' + sorted.map(b => {
        const barColor = b.pct >= 100 ? 'var(--danger)' : b.pct > 80 ? 'var(--warning)' : 'var(--accent)';
        const warnPill = b.pct >= 100
            ? '<span class="bgt-warn-pill bgt-warn-over"><i class="fas fa-fire" style="font-size:9px;"></i>OVER</span>'
            : b.pct > 80
                ? '<span class="bgt-warn-pill bgt-warn-warn"><i class="fas fa-triangle-exclamation" style="font-size:9px;"></i>WASPADA</span>'
                : '<span class="bgt-warn-pill bgt-warn-ok"><i class="fas fa-check" style="font-size:9px;"></i>OK</span>';
        return `<div class="bgt-bar-row">
            <div class="bgt-bar-label" title="${b.catName}">${b.catName}</div>
            <div>
                <div class="bgt-bar-track">
                    <div class="bgt-bar-fill" style="width:${Math.min(b.pct, 100).toFixed(1)}%;background:${barColor};"></div>
                    <div class="bgt-bar-time" style="left:${timePct.toFixed(1)}%;"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:3px;">
                    <span style="font-size:10px;color:var(--text-muted);">Pakai: ${rp(b.usage)}</span>
                    <span style="font-size:10px;color:var(--text-muted);">Limit: ${rp(b.limit)}</span>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;">
                <span class="bgt-bar-val" style="color:${barColor};">${b.pct.toFixed(0)}%</span>
                ${warnPill}
            </div>
        </div>`;
    }).join('') + '</div>';
}

function editBudget(budgetId, catName, limit, month) {
    const idEl = document.getElementById('edit-bgt-id');
    const nameEl = document.getElementById('edit-bgt-cat-name');
    const moEl = document.getElementById('edit-bgt-month');
    const limEl = document.getElementById('edit-bgt-limit');

    if (idEl) idEl.value = budgetId;
    if (nameEl) nameEl.textContent = catName;
    if (moEl) moEl.value = month;
    if (limEl) limEl.value = limit;

    if (typeof openModal === 'function') openModal('modal-edit-budget');
}

async function submitEditBudget() {
    const id = document.getElementById('edit-bgt-id')?.value;
    const month = document.getElementById('edit-bgt-month')?.value;
    const limit = parseFloat(document.getElementById('edit-bgt-limit')?.value);

    try {
        await CashbookService.saveBudget({ id, month, limit_amount: limit });
        toast('Budget berhasil diperbarui!', 's');
        if (typeof closeModal === 'function') closeModal('modal-edit-budget');
        if (typeof refreshUI === 'function') await refreshUI();
    } catch (e) { toast(e.message || 'Gagal memperbarui budget', 'e'); }
}

async function deleteBudget(budgetId, catName) {
    if (typeof confirmDialog === 'function') {
        confirmDialog(
            'Hapus Budget',
            `Budget untuk "${catName}" akan dihapus. Lanjutkan?`,
            'danger',
            async () => {
                try {
                    await CashbookService.deleteBudget(String(budgetId));
                    toast('Budget dihapus', 's');
                    if (typeof refreshUI === 'function') await refreshUI();
                } catch (e) { toast(e.message || 'Gagal menghapus budget', 'e'); }
            }
        );
    }
}

function angEditById(btn) {
    const bid = btn.dataset.bid;
    const item = _angBudgetItems.find(b => String(b.id) === String(bid));
    if (!item) return;
    const selMonth = document.getElementById('ang-filter-month')?.value || '';
    editBudget(item.id, item.catName, item.limit, selMonth);
}

function angDeleteById(btn) {
    const bid = btn.dataset.bid;
    const item = _angBudgetItems.find(b => String(b.id) === String(bid));
    if (!item) return;
    deleteBudget(item.id, item.catName);
}

function renderBgtWizard() {
    const wrap = document.getElementById('ang-pillars-wrap');
    if (!wrap) return;
    const pillarPresets = [
        { label: 'Kebutuhan Pokok', icon: 'fa-house', catPillar: 'wajib', hint: 'Sewa, Listrik, Makan' },
        { label: 'Investasi', icon: 'fa-seedling', catPillar: 'growth', hint: 'Tabungan, Saham' },
        { label: 'Gaya Hidup', icon: 'fa-star', catPillar: 'lifestyle', hint: 'Hiburan, Belanja' },
        { label: 'Lainnya', icon: 'fa-tag', catPillar: 'bocor', hint: 'Pengeluaran tak terduga' },
    ];
    wrap.innerHTML = `
    <div class="bgt-wizard">
        <div style="width:52px;height:52px;border-radius:14px;background:var(--accent-glow);border:2px solid var(--border-accent);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:20px;color:var(--accent);">
            <i class="fas fa-sliders"></i>
        </div>
        <div style="font-size:17px;font-weight:800;color:var(--text);margin-bottom:8px;">Atur Anggaran Pertamamu</div>
        <div style="font-size:13px;color:var(--text-sub);margin-bottom:20px;line-height:1.6;">Tetapkan limit pengeluaran per kategori untuk mengontrol keuangan dengan lebih disiplin.</div>
        <div class="wizard-steps">
            ${pillarPresets.map(p => `
            <div class="wizard-step" onclick="typeof openModal === 'function' ? openModal('modal-budget') : null" title="${p.hint}">
                <div class="wizard-step-icon"><i class="fas ${p.icon}"></i></div>
                <div class="wizard-step-label">${p.label}</div>
                <div style="font-size:9px;color:var(--text-muted);margin-top:1px;">${p.hint}</div>
            </div>`).join('')}
        </div>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <button class="btn btn-accent" onclick="typeof openModal === 'function' ? openModal('modal-budget') : null"><i class="fas fa-plus"></i> Set Budget Baru</button>
            <button class="btn btn-ghost btn-sm" onclick="copyLastMonthBudget()"><i class="fas fa-copy"></i> Copy Bulan Lalu</button>
        </div>
    </div>`;
}

async function loadAnggaran() {
    const picker = document.getElementById('ang-filter-month');
    if (!picker) return;
    if (!picker.value) {
        const n = new Date();
        picker.value = `${n.getFullYear()}-${String(n.getMonth() + 1).padStart(2, '0')}`;
    }
    const selMonth = picker.value;
    const [yr, mo] = selMonth.split('-').map(Number);

    let budgets = [];
    try {
        const allBgts = await localAPI.budgets.getAll();
        budgets = allBgts.filter(b => !b.month || b.month === selMonth || (b.month && b.month.startsWith(selMonth)));
    } catch (e) { }

    const monthTx = (allTxList || []).filter(tx => {
        const d = new Date(tx.transaction_date);
        return d.getFullYear() === yr && (d.getMonth() + 1) === mo && tx.type === 'expense';
    });

    const catSpent = {};
    monthTx.forEach(tx => {
        const key = String(tx.category_id || 'uncategorized');
        catSpent[key] = (catSpent[key] || 0) + parseFloat(tx.amount || 0);
    });

    let totalBudget = 0, totalUsed = 0;
    const budgetItems = [];

    budgets.forEach(b => {
        const limit = parseFloat(b.limit || b.limit_amount || 0);
        const usage = parseFloat(b.usage || catSpent[String(b.category_id)] || 0);
        const pct = limit > 0 ? (usage / limit) * 100 : 0;
        const catInfo = (categories || []).find(c => String(c.id) == String(b.category_id)) || {};
        const catName = b.category_name || catInfo.name || 'Lainnya';
        totalBudget += limit;
        totalUsed += usage;
        budgetItems.push({ ...b, limit, usage, pct, catName, id: b.id });
    });

    const totalRemaining = totalBudget - totalUsed;
    const spendPct = totalBudget > 0 ? (totalUsed / totalBudget) * 100 : 0;

    // ── 1. Header Summary ──
    const setEl = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };
    setEl('ang-sum-used', rp(totalUsed));
    setEl('ang-sum-total', rp(totalBudget));
    setEl('ang-sum-left', rp(Math.max(0, totalRemaining)));
    setEl('ang-sum-pct', `${spendPct.toFixed(0)}%`);

    const sumBar = document.getElementById('ang-sum-bar');
    if (sumBar) {
        sumBar.style.width = `${Math.min(100, spendPct)}%`;
        sumBar.style.background = spendPct >= 100 ? 'var(--danger)' : spendPct > 80 ? 'var(--warning)' : 'var(--accent)';
    }

    const progList = document.getElementById('ang-progress-list');
    const catList = document.getElementById('ang-category-list');

    if (!budgetItems.length) {
        if (progList) progList.innerHTML = '<div class="empty"><i class="fas fa-folder-open"></i>Belum ada data progress</div>';
        if (catList) catList.innerHTML = '<div class="empty" style="padding:32px 20px;"><i class="fas fa-folder-open"></i>Belum ada anggaran dibuat.<br><span style="font-size:12px;opacity:0.7;margin-top:6px;display:block;">Anggaran membantu kamu mengontrol pengeluaran.</span></div>';
        const insEl = document.getElementById('ang-insight-text');
        if (insEl) insEl.innerHTML = 'Buat anggaran pertamamu untuk mendapatkan AI insight seputar pengeluaranmu.';
        return;
    }

    budgetItems.sort((a, b) => b.pct - a.pct); // Highest % first
    _angBudgetItems = budgetItems;

    // ── 2. Progress Visuals ──
    if (progList) {
        const fragP = document.createDocumentFragment();
        budgetItems.slice(0, 5).forEach(b => {
            const color = b.pct >= 100 ? 'var(--danger)' : b.pct > 80 ? 'var(--warning)' : 'var(--accent)';
            const el = document.createElement('div');
            el.className = 'ang-prog-item';
            el.setAttribute('onclick', `if(typeof openModal === 'function'){openModal('modal-budget'); setTimeout(()=>{document.getElementById('modal-budget').dataset.editId='${b.id}';const c=document.getElementById('bgt-cat-val');if(c)c.value='${b.category_id}';const l=document.getElementById('bgt-limit');if(l)l.value='${b.limit}';},100)}`);
            el.innerHTML = `
                <div class="ang-prog-header">
                    <span class="ang-prog-name">${b.catName}</span>
                    <span class="ang-prog-pct" style="color:${color}">${b.pct.toFixed(0)}%</span>
                    <div class="ang-prog-acts">
                        <button class="edit" title="Edit" data-bid="${b.id}" onclick="event.stopPropagation(); angEditById(this)"><i class="fas fa-pen"></i></button>
                        <button class="del" title="Hapus" data-bid="${b.id}" onclick="event.stopPropagation(); angDeleteById(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
                <div class="ang-prog-track">
                    <div class="ang-prog-fill" style="width:${Math.min(100, b.pct)}%; background:${color};"></div>
                </div>
                <div class="ang-prog-meta">
                    <span>${rp(b.usage)}</span>
                    <span>dari ${rp(b.limit)}</span>
                </div>
            `;
            fragP.appendChild(el);
        });
        progList.replaceChildren(fragP);
    }

    // ── 3. Category List ──
    if (catList) {
        const fragC = document.createDocumentFragment();
        budgetItems.forEach(b => {
            const color = b.pct >= 100 ? 'var(--danger)' : b.pct > 80 ? 'var(--warning)' : 'var(--accent)';
            const el = document.createElement('div');
            el.style.cssText = 'padding:16px 20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;';
            el.innerHTML = `
                <div>
                    <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;">${b.catName}</div>
                    <div style="font-size:12px;color:var(--text-muted);"><span style="color:${color};font-weight:700;">${rp(b.usage)}</span> / ${rp(b.limit)}</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px;">Sisa: ${rp(Math.max(0, b.limit - b.usage))}</div>
                    <div style="display:flex;gap:4px;justify-content:flex-end;">
                        <button class="btn btn-ghost btn-sm" style="padding:4px 8px;" data-bid="${b.id}" onclick="angEditById(this)"><i class="fas fa-pen"></i></button>
                        <button class="btn btn-ghost btn-sm" style="padding:4px 8px;color:var(--danger);" data-bid="${b.id}" onclick="angDeleteById(this)"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            `;
            fragC.appendChild(el);
        });
        catList.replaceChildren(fragC);
    }

    // ── 4. Smart Insight ──
    const today = new Date();
    const daysInMonth = new Date(yr, mo, 0).getDate();
    const dayOfMonth = (today.getFullYear() === yr && (today.getMonth() + 1) === mo) ? today.getDate() : daysInMonth;
    const timePct = (dayOfMonth / daysInMonth) * 100;

    let insightText = '';
    if (spendPct >= 100) {
        insightText = `<strong>Anggaran habis!</strong> Kamu sudah memakai 100% anggaran. Tahan pengeluaran yang tidak penting sampai bulan depan.`;
    } else if (spendPct > timePct + 15) {
        insightText = `<strong>Pengereman diperlukan.</strong> Pemakaian anggaran (${spendPct.toFixed(0)}%) lebih cepat dari waktu berjalan (${timePct.toFixed(0)}%).`;
    } else if (budgetItems.length > 0 && budgetItems[0].pct >= 90) {
        insightText = `Kategori <strong>${budgetItems[0].catName}</strong> hampir mencapai limit (${budgetItems[0].pct.toFixed(0)}%). Hati-hati bocor di kategori ini.`;
    } else {
        insightText = `<strong>Good job!</strong> Keuanganmu bulan ini berjalan sesuai rencana. Sisa anggaranmu masih ${rp(totalRemaining)}.`;
    }
    const insEl = document.getElementById('ang-insight-text');
    if (insEl) insEl.innerHTML = insightText;
}

async function copyLastMonthBudget() {
    const picker = document.getElementById('ang-filter-month');
    if (!picker || !picker.value) return toast('Pilih bulan terlebih dahulu', 'e');
    const [yr, mo] = picker.value.split('-').map(Number);
    const lastMo = mo === 1 ? 12 : mo - 1;
    const lastYr = mo === 1 ? yr - 1 : yr;
    const lastMonth = `${lastYr}-${String(lastMo).padStart(2, '0')}`;
    try {
        const allBgts = await localAPI.budgets.getAll();
        const lastBudgets = allBgts.filter(b => b.month && b.month.startsWith(lastMonth));
        if (!lastBudgets.length) return toast(`Tidak ada budget di ${lastMonth}`, 'e');

        let copied = 0;
        for (const b of lastBudgets) {
            // Check if already exists this month
            const exist = allBgts.find(cb => cb.category_id == b.category_id && cb.month === picker.value);
            if (!exist) {
                await localAPI.budgets.save({ category_id: b.category_id, month: picker.value, limit_amount: parseFloat(b.limit || b.limit_amount || 0) });
                copied++;
            }
        }
        if (copied > 0) {
            toast(`${copied} budget disalin dari bulan lalu!`, 's');
            loadAnggaran();
            if (typeof refreshUI === 'function') await refreshUI();
        } else {
            toast('Semua budget bulan lalu sudah ada di bulan ini', 'i');
        }
    } catch (e) { toast('Gagal menyalin budget: ' + e.message, 'e'); }
}

// Expose to window
window.toggleRollover = toggleRollover;
window.refreshRecommendations = refreshRecommendations;
window.computeRecommendations = computeRecommendations;
window.openBudgetWithSuggestion = openBudgetWithSuggestion;
window.renderBgtTrendChart = renderBgtTrendChart;
window.renderBgtBarChart = renderBgtBarChart;
window.editBudget = editBudget;
window.submitEditBudget = submitEditBudget;
window.deleteBudget = deleteBudget;
window.angEditById = angEditById;
window.angDeleteById = angDeleteById;
window.renderBgtWizard = renderBgtWizard;
window.loadAnggaran = loadAnggaran;
window.copyLastMonthBudget = copyLastMonthBudget;
