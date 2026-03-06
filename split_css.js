import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

try {
    const cssPath = path.join(__dirname, 'public', 'assets', 'css', 'cashbook.css');
    console.log("Reading from:", cssPath);
    const css = fs.readFileSync(cssPath, 'utf8');

    const slices = {
        anggaran: { start: '/* === ANGGARAN FEATURE STYLES === */', end: '/* === LAPORAN FEATURE STYLES === */', file: 'cashbook-anggaran.css' },
        laporan: { start: '/* === LAPORAN FEATURE STYLES === */', end: '/* ─── DEBT TAB ─── */', file: 'cashbook-laporan.css' },
        debts: { start: '/* ─── DEBT TAB ─── */', end: '/* \n   CUSTOM COMPONENT CLASSES (sub-blade partials)\n    */', file: 'cashbook-debts.css' },
        overview: { start: '/*  OVERVIEW: Hero card  */', end: '/*  TRANSACTIONS: Quick Add  */', file: 'cashbook-overview.css' },
        transactions: { start: '/*  TRANSACTIONS: Quick Add  */', end: '/*  ANGGARAN: Summary Card  */', file: 'cashbook-transactions.css' },
        anggaran2: { start: '/*  ANGGARAN: Summary Card  */', end: '/*  LAPORAN: Metric cards (used in reports.blade.php)  */', file: 'cashbook-anggaran.css', append: true },
        laporan2: { start: '/*  LAPORAN: Metric cards (used in reports.blade.php)  */', end: '/*  Modal layout classes (used by modals.blade.php)  */', file: 'cashbook-laporan.css', append: true },
        transactions2: { start: '/*  Transaction Ledger Row (used by transactions.js)  */', end: '/*  lap-mom-badge variants  */', file: 'cashbook-transactions.css', append: true },
        laporan3: { start: '/*  lap-mom-badge variants  */', end: '/* \n   UTANG / PIUTANG CARDS\n    */', file: 'cashbook-laporan.css', append: true },
        debts2: { start: '/* \n   UTANG / PIUTANG CARDS\n    */', end: '/* Auto-backup option cards */', file: 'cashbook-debts.css', append: true },
        overview2: { start: '/* \n   RECENT TRANSACTIONS (OVERVIEW)\n    */', end: '/* \n   ====================================================\n    MOBILE PERFORMANCE GUARD\n   ====================================================', file: 'cashbook-overview.css', append: true }
    };

    let coreCss = css.replace(/\r\n/g, '\n');

    for (const [key, config] of Object.entries(slices)) {
        const startStr = config.start.replace(/\r\n/g, '\n');
        const endStr = config.end.replace(/\r\n/g, '\n');

        const startIdx = coreCss.indexOf(startStr);
        const endIdx = coreCss.indexOf(endStr);

        if (startIdx !== -1 && endIdx !== -1) {
            const chunk = coreCss.substring(startIdx, endIdx);
            coreCss = coreCss.substring(0, startIdx) + coreCss.substring(endIdx);

            const outPath = path.join(__dirname, 'public', 'assets', 'css', config.file);
            if (config.append && fs.existsSync(outPath)) {
                fs.appendFileSync(outPath, '\n' + chunk);
            } else {
                fs.writeFileSync(outPath, chunk);
            }
            console.log(`Extracted ${key} to ${config.file}`);
        } else {
            console.log(`Failed to find bounds for ${key}`);
        }
    }

    fs.writeFileSync(path.join(__dirname, 'public', 'assets', 'css', 'cashbook-core.css'), coreCss);
    console.log('Successfully extracted partials and saved core css!');
} catch (e) {
    console.error(e.message);
}
