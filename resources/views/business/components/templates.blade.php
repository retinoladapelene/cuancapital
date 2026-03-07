<!-- ══════════════════════════════════════════════════════════════
     HTML TEMPLATES FOR JS INJECTION (Vue/Alpine-like)
     ══════════════════════════════════════════════════════════════ -->

<!-- Command Palette Modal -->
<div class="biz-modal-bg" id="biz-modal-cmd" onclick="bizCloseOut(event.target === this ? this : null)">
  <div class="biz-cmd-palette" role="dialog" aria-modal="true">
    <div class="biz-cmd-header">
      <i class="fas fa-search"></i>
      <input type="text" id="biz-cmd-input" placeholder="Search actions, pages, products..." autocomplete="off">
      <button onclick="bizCloseModal('biz-modal-cmd')" class="biz-cmd-close"><kbd>ESC</kbd></button>
    </div>
    <div class="biz-cmd-body">
      <div class="biz-cmd-group" id="biz-cmd-results">
        <!-- JS rendered results -->
      </div>
    </div>
  </div>
</div>

<!-- Template: Skeleton KPI Card -->
<template id="tpl-skeleton-kpi">
  <div class="biz-card biz-skeleton-card" style="padding:16px; display:flex; flex-direction:column; justify-content:center;">
    <div class="biz-skeleton-line" style="height:10px; width:40%; margin-bottom:8px"></div>
    <div class="biz-skeleton-line" style="height:28px; width:70%;"></div>
  </div>
</template>

<!-- Template: AI Insight Feed -->
<template id="tpl-ai-insight">
  <div class="biz-ai-insight-card" style="display:flex; gap:12px; align-items:flex-start; padding:12px 14px; background:var(--biz-surface-2); border:1px solid var(--biz-border); border-radius:12px; margin-bottom:12px;">
      <div class="biz-ai-icon" data-icon style="font-size:18px; margin-top:2px"></div>
      <div class="biz-ai-body" style="flex:1;">
          <div class="biz-ai-text" data-desc style="font-size:12px; line-height:1.45; color:var(--biz-text); font-weight:500;"></div>
      </div>
  </div>
</template>

<!-- Template: Dashboard Shell -->
<template id="tpl-dashboard-shell">
    <div class="biz-page" id="biz-dash-page">
        <!-- Dashboard Header -->
        <div class="biz-section-header" style="margin-bottom:16px; border-bottom:1px solid var(--biz-border); padding-bottom:12px; display:flex; justify-content:space-between; align-items:center">
            <div>
                <h2 class="biz-page-title" style="font-size:22px;letter-spacing:-0.5px">Business Command</h2>
                <div style="font-size:13px;color:var(--biz-text-muted);font-weight:600" id="dash-date"></div>
            </div>
            <div class="biz-quick-actions" style="display:flex; gap:8px;">
                <button class="biz-btn biz-btn-primary" style="padding:6px 12px; font-size:12px" onclick="bizOpenModal('biz-modal-quick-sale')"><i class="fas fa-plus"></i> Sale</button>
            </div>
        </div>

        <!-- 12-Column Main Grid Wrapper -->
        <div style="display:grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap:16px;">
            
            <!-- ZONE 1: Strategic Business Pulse (KPI Cards) -->
            <div style="grid-column: span 12; display:grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 140px), 1fr)); gap:12px;" id="dash-zone1-kpi"></div>

            <!-- ZONE 2 & 3 CONTAINER: Pulse + Insights -->
            <div style="grid-column: span 12; display:grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap:16px;" class="dash-mid-grid">
                
                <!-- ZONE 4 & 2: AI CFO Insight + Live Pulse -->
                <div class="dash-ai-col" style="grid-column: span 12; display:flex; flex-direction:column; gap:16px;">
                    
                    <!-- ZONE 2: Live Business Pulse -->
                    <div class="biz-card" style="background:var(--biz-surface-2)">
                        <div style="padding:12px 16px; display:flex; justify-content:space-between; align-items:center">
                            <div style="display:flex; align-items:center; gap:8px">
                                <div style="width:8px; height:8px; border-radius:50%; background:var(--biz-danger); box-shadow:0 0 8px var(--biz-danger); animation:pulse 1.5s infinite"></div>
                                <div style="font-size:12px; font-weight:700; color:var(--biz-text-dim); text-transform:uppercase">Live: Last 60 Mins</div>
                            </div>
                            <div id="dash-zone2-live" style="display:flex; gap:16px; font-weight:700; font-size:14px">
                                <div><i class="fas fa-shopping-bag" style="color:var(--biz-text-muted)"></i> <span id="dash-live-qty">-</span></div>
                                <div><i class="fas fa-sack-dollar" style="color:var(--biz-success)"></i> <span id="dash-live-rev">-</span></div>
                            </div>
                        </div>
                    </div>

                    <!-- ZONE 4: AI CFO Panel -->
                    <div class="biz-card" style="flex:1; border:1px solid var(--biz-border-strong); border-bottom:3px solid var(--biz-success)">
                        <div class="biz-card-header" style="margin-bottom:8px">
                            <div class="biz-card-title"><i class="fas fa-robot" style="color:var(--biz-success)"></i> AI CFO Insights</div>
                        </div>
                        <div id="dash-zone4-insights" style="display:flex; flex-direction:column; gap:8px; padding:0 16px 16px 16px;">
                            <div class="biz-loading"><i class="fas fa-spinner fa-spin"></i> Analyzing Business Health...</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ZONE 5: Historical Trends (Bottom) -->
            <div style="grid-column: span 12; margin-top:8px;">
                <div class="biz-card">
                    <div class="biz-card-header">
                        <div class="biz-card-title">Penjualan Terbaru</div>
                        <button class="biz-card-action" onclick="bizSwitchTab('sales')">Semua </button>
                    </div>
                    <div id="dash-recent-sales"><div class="biz-loading"><i class="fas fa-spinner fa-spin"></i></div></div>
                </div>
            </div>
            
        </div>
    </div>
</template>

