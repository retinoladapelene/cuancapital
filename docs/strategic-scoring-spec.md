# Strategic Scoring Specification (v1.1)

> **Source of Truth** for Business Mentor Lab Engine.
> All logic must strictly follow this document. No arbitrary thresholds in code.

---

## 1. Input Mapping (Raw -> Normalized Value)

### 1.1 Capital & Experience
| Raw Input | Normalized Value (IDR) | Experience Level |
| :--- | :--- | :--- |
| `under_5` | 3,000,000 | Beginner (0.5) |
| `5_20` | 12,500,000 | Intermediate (1.0) |
| `20_100` | 60,000,000 | Advanced (1.5) |
| `over_100` | 150,000,000 | Expert (2.0) |

### 1.2 Intent & Risk Tolerance
| Raw Input | Risk Modifier | Growth Multiplier |
| :--- | :--- | :--- |
| `stable_income` | -10 | 1.0 |
| `scale_fast` | +20 | 1.5 |
| `market_dominance` | +40 | 2.0 |

---

## 2. Metric Normalization (0 - 100)

### 2.1 Feasibility Score (Ability to Execute)
**Concept**: Can you afford the operational cost for 3-6 months based on target?
**Refinement**: Use stricter cost assumption to avoid margin gaming.

**Formula**:
1.  `EstimatedCOGS = TargetRevenue * (1 - GrossMargin)`
2.  `EstimatedOpEx = TargetRevenue * 0.15` (15% Overhead Assumption - Marketing, Ops, Etc)
3.  `MonthlyBurn = EstimatedCOGS + EstimatedOpEx`
4.  `RunwayMonths = Capital / MonthlyBurn` (If MonthlyBurn <= 0, Runway = infinite/100)
5.  `FeasibilityScore = (RunwayMonths / 6) * 100`

**Normalization**:
*   Cap at 100.
*   Floor at 10.

### 2.2 Profit Score (ROI Potential)
**Concept**: Return on Investment relative to capital (Monthly basis).

**Formula**:
1.  `EstimatedProfit = TargetRevenue * GrossMargin - EstimatedOpEx` (Using same OpEx assumption)
2.  `ROI_Multiplier = EstimatedProfit / Capital`
3.  `ProfitScore = min(100, ROI_Multiplier * 200)` (50% Monthly ROI = 100 Score)

**Normalization**:
*   Cap at 100.
*   Floor at 0.

### 2.3 Risk Score (Exposure)
**Concept**: How dangerous is this strategy?
**Refinement**: 3-Layer Single Component Model (No double counting).

**Formula Components**:
1.  `StructuralRisk` = IntentModifier (from table 1.2)
2.  `CapitalRisk` = `min(40, (TargetRevenue / Capital) * 3)` (Ratio based, max 40 points)
3.  `ExperienceReducer` = `ExperienceLevel * 10` (Max 20 discount)

**Final Calculation**:
`RiskScore = 50 + StructuralRisk + CapitalRisk - ExperienceReducer`

**Normalization**:
*   Cap at 100.
*   Floor at 10.
*   0-30: Safe (Deposite)
*   31-60: Moderate (Business)
*   61-80: High (Venture)
*   81-100: Suicide Mission

### 2.4 Efficiency Score (Capital Leverage)
**Concept**: Revenue generated per unit of capital.
**Refinement**: Adjusted for sensitivity at high range.

**Formula**:
1.  `LeverageRatio = TargetRevenue / Capital`
2.  `EfficiencyScore = min(100, LeverageRatio * 4)`

**Calculations**:
*   Ratio 5 (5x Revenue of Capital) -> Score 20 (Low Efficiency/Capital Heavy)
*   Ratio 12.5 -> Score 50 (Moderate)
*   Ratio 25 -> Score 100 (High Efficiency/Service)

**Normalization**:
*   Cap at 100.
*   Floor at 0.

---

## 3. Priority Rule Tree (Classification)

**Evaluation Order** (First Match Wins):

1.  **⛔ UNREALISTIC**
    *   `FeasibilityScore < 30`
    *   *Effect*: Stop analysis, warn user to lower target or increase capital.

2.  **⚠️ HIGH EXPOSURE**
    *   `RiskScore > 80`
    *   *Effect*: Warn about highburn rate & operational complexity.

3.  **📉 CAPITAL STRAINED**
    *   `EfficiencyScore < 30` AND `ProfitScore < 40`
    *   *Effect*: Suggest improving margin or reducing COGS.

4.  **🚀 AGGRESSIVE SCALING**
    *   `Intent = scale_fast` AND `FeasibilityScore > 60` AND `RiskScore > 40`
    *   *Label*: "Aggressive Growth Blueprint"

5.  **🛡️ STABLE GROWTH**
    *   `Intent = stable` AND `RiskScore < 40` AND `ProfitScore > 50`
    *   *Label*: "Stable Income Blueprint"

6.  **⚖️ BALANCED**
    *   Default fallback.
    *   *Label*: "Balanced Strategy"

---

## 4. Recommendation Mapping

### 4.1 Unrealistic
*   "Target revenue terlalu tinggi untuk modal saat ini."
*   "Kurangi target menjadi 30% dari angka sekarang."
*   "Fokus pada organic traffic dahulu."

### 4.2 High Exposure
*   "Risiko cashflow sangat tinggi."
*   "Siapkan dana darurat minimal 3x operational cost."
*   "Jangan hire staff tetap di 6 bulan pertama."

### 4.3 Capital Strained
*   "Margin terlalu tipis."
*   "Naikkan harga jual atau cari supplier lebih murah."
*   "Hindari paid ads sampai conversion rate > 2%."

### 4.4 Aggressive Scaling
*   "Investasikan 60% profit kembali ke marketing."
*   "Fokus pada User Acquisition Cost (CAC)."
*   "Scale winning campaign segera."

### 4.5 Stable Growth
*   "Jaga retensi pelanggan lama."
*   "Optimalkan LTV (Lifetime Value)."
*   "Bangun SOP operasional."

### 4.6 Balanced
*   "Lakukan tes pasar kecil-kecilan."
*   "Validasi produk sebelum stock banyak."
*   "Fokus pada kepuasan pelanggan pertama."
