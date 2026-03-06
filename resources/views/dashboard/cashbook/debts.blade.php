<div id="tab-debts" class="tab-pane">
            
            <!-- Layer 1: Debt Overview -->
            <div class="utang-overview-card" style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:20px 24px; margin-bottom:32px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
                <div style="font-size:16px; font-weight:700; color:var(--text); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                    <i class="fas fa-hand-holding-dollar" style="color:var(--text-muted);"></i> Debt Management Center
                </div>
                
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px;">
                    <!-- Total Hutang -->
                    <div style="padding:16px; background:rgba(245,158,11,0.05); border-radius:12px; border:1px solid rgba(245,158,11,0.15);">
                        <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total Hutang</div>
                        <div id="debt-val-total-payable" style="font-size:22px; font-weight:800; color:#f59e0b; font-family:'JetBrains Mono', monospace;">Rp 0</div>
                        <div id="debt-count-payable" style="font-size:11px; color:var(--text-muted); margin-top:4px;">0 hutang aktif</div>
                    </div>
                    
                    <!-- Total Piutang -->
                    <div style="padding:16px; background:rgba(16,185,129,0.05); border-radius:12px; border:1px solid rgba(16,185,129,0.15);">
                        <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; font-weight:600;">Total Piutang</div>
                        <div id="debt-val-total-receivable" style="font-size:22px; font-weight:800; color:#10b981; font-family:'JetBrains Mono', monospace;">Rp 0</div>
                        <div id="debt-count-receivable" style="font-size:11px; color:var(--text-muted); margin-top:4px;">0 piutang aktif</div>
                    </div>
                    
                    <!-- Cicilan Mendatang / Bulan Ini -->
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
                
                <!-- Hutang Saya -->
                <div>
                    <div style="padding-bottom:12px; margin-bottom:20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:15px; font-weight:700; color:var(--text);"><i class="fas fa-hand-holding-dollar" style="color:#f59e0b; margin-right:8px;"></i>Hutang Saya</span>
                        <span id="dt-count-payable" style="font-size:11px; background:var(--surface2); padding:4px 10px; border-radius:12px; font-weight:600;">0 items</span>
                    </div>
                    <div id="utang-list-payable" style="display:flex; flex-direction:column; gap:16px;">
                        <!-- Payable items will be rendered here -->
                    </div>
                </div>

                <!-- Piutang Saya -->
                <div>
                    <div style="padding-bottom:12px; margin-bottom:20px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:15px; font-weight:700; color:var(--text);"><i class="fas fa-hand-holding-medical" style="color:#10b981; margin-right:8px;"></i>Piutang Saya</span>
                        <span id="dt-count-receivable" style="font-size:11px; background:var(--surface2); padding:4px 10px; border-radius:12px; font-weight:600;">0 items</span>
                    </div>
                    <div id="utang-list-receivable" style="display:flex; flex-direction:column; gap:16px;">
                        <!-- Receivable items will be rendered here -->
                    </div>
                </div>
            </div>
            
            <!-- Layer 3: Assistant / Smart Insight -->
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

        </div>
