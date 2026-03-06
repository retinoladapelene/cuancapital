/**
 * CuanCapital Experience OS: Elements of Earth Avatar Borders
 */
window.ElementBorders = (function () {
    const elements = [
        { id: 'fire', name: 'Api', cssClass: 'element-fire' },
        { id: 'water', name: 'Air', cssClass: 'element-water' },
        { id: 'earth', name: 'Tanah', cssClass: 'element-earth' },
        { id: 'lightning', name: 'Petir', cssClass: 'element-lightning' },
        { id: 'ice', name: 'Es', cssClass: 'element-ice' },
        { id: 'wind', name: 'Angin', cssClass: 'element-wind' },
        { id: 'forest', name: 'Hutan', cssClass: 'element-forest' },
        { id: 'storm', name: 'Badai', cssClass: 'element-storm' },
        { id: 'sea', name: 'Laut Dalam', cssClass: 'element-sea' }
    ];

    function buildInner(id) {
        if (id === 'fire') {
            const flamesText = [
                { a: 0, h: 22, t: 0.88, d: 0 }, { a: 18, h: 16, t: 1.05, d: .09 }, { a: 36, h: 24, t: .82, d: .18 },
                { a: 54, h: 18, t: 1.1, d: .06 }, { a: 72, h: 20, t: .95, d: .24 }, { a: 90, h: 15, t: 1.2, d: .12 },
                { a: 108, h: 23, t: .86, d: .03 }, { a: 126, h: 17, t: 1.0, d: .21 }, { a: 144, h: 21, t: .9, d: .15 },
                { a: 162, h: 14, t: 1.15, d: .08 }, { a: 180, h: 25, t: .78, d: .27 }, { a: 198, h: 19, t: 1.05, d: .06 },
                { a: 216, h: 22, t: .92, d: .19 }, { a: 234, h: 16, t: 1.08, d: .13 }, { a: 252, h: 20, t: .84, d: .04 },
                { a: 270, h: 24, t: .96, d: .22 }, { a: 288, h: 17, t: 1.12, d: .1 }, { a: 306, h: 21, t: .87, d: .26 },
                { a: 324, h: 15, t: 1.02, d: .07 }, { a: 342, h: 23, t: .93, d: .16 }
            ].map(f => `<div class="flame-arm" style="transform:rotate(${f.a}deg) translateY(-90px)"><div class="flame-tip" style="height:${f.h}px;animation-duration:${f.t}s;animation-delay:${f.d}s"></div></div>`).join('');
            return `
                <div class="fire-glow-ring" style="width:182px;height:182px;"></div>
                <div class="fire-glow-ring" style="width:160px;height:160px;border-color:rgba(255,80,0,.14);box-shadow:none;"></div>
                <div class="fire-orbit-wrap">${flamesText}</div>
                <div class="ember-p" style="width:3px;height:3px;--ex:-52px;--ey:38px;--ex2:-78px;--ey2:-30px;animation-duration:1.5s;animation-delay:0s"></div>
                <div class="ember-p" style="width:2px;height:2px;--ex:60px;--ey:22px;--ex2:88px;--ey2:-40px;animation-duration:1.9s;animation-delay:.3s"></div>
                <div class="ember-p" style="width:3px;height:3px;--ex:18px;--ey:-68px;--ex2:38px;--ey2:-96px;animation-duration:1.3s;animation-delay:.7s"></div>
                <div class="ember-p" style="width:4px;height:4px;--ex:-65px;--ey:-28px;--ex2:-92px;--ey2:-58px;animation-duration:2.1s;animation-delay:1.1s"></div>
                <div class="ember-p" style="width:2px;height:2px;--ex:42px;--ey:62px;--ex2:65px;--ey2:35px;animation-duration:1.6s;animation-delay:1.5s"></div>
                <div class="ember-p" style="width:3px;height:3px;--ex:-35px;--ey:70px;--ex2:-55px;--ey2:35px;animation-duration:1.8s;animation-delay:.5s"></div>
                <div class="ember-p" style="width:2px;height:2px;--ex:75px;--ey:-40px;--ex2:98px;--ey2:-68px;animation-duration:2.2s;animation-delay:.9s"></div>
                <div class="av" style="border:2px solid rgba(255,90,0,.55);box-shadow:0 0 28px rgba(255,65,0,.75),0 0 65px rgba(255,30,0,.35),inset 0 0 22px rgba(255,20,0,.15)"></div>`;
        }
        else if (id === 'water') {
            return `
                <div class="water-bg"></div>
                <div class="wave w1"></div><div class="wave w2"></div><div class="wave w3"></div>
                <div class="wdrop" style="--da:0deg;animation-duration:4.5s;animation-delay:0s"></div>
                <div class="wdrop" style="--da:72deg;animation-duration:4.5s;animation-delay:.9s"></div>
                <div class="wdrop" style="--da:144deg;animation-duration:4.5s;animation-delay:1.8s"></div>
                <div class="wdrop" style="--da:216deg;animation-duration:4.5s;animation-delay:2.7s"></div>
                <div class="wdrop" style="--da:288deg;animation-duration:4.5s;animation-delay:3.6s"></div>
                <div class="ripple-r" style="animation-duration:3.2s;animation-delay:0s"></div>
                <div class="ripple-r" style="animation-duration:3.2s;animation-delay:1.06s"></div>
                <div class="ripple-r" style="animation-duration:3.2s;animation-delay:2.13s"></div>
                <div class="av" style="border:2px solid rgba(0,165,255,.5);box-shadow:0 0 25px rgba(0,145,255,.65),0 0 55px rgba(0,100,220,.28),inset 0 0 18px rgba(0,165,255,.12)"></div>`;
        }
        else if (id === 'earth') {
            return `
                <div class="earth-strata" style="width:194px;height:194px;border-color:rgba(100,60,25,.32)"></div>
                <div class="earth-strata" style="width:172px;height:172px;border-color:rgba(115,72,32,.27)"></div>
                <div class="earth-strata" style="width:150px;height:150px;border-color:rgba(128,82,38,.22)"></div>
                <div class="earth-strata" style="width:128px;height:128px;border-color:rgba(110,68,30,.2)"></div>
                <div class="earth-strata" style="width:106px;height:106px;border-color:rgba(95,58,24,.18)"></div>
                <svg style="position:absolute;inset:0;width:100%;height:100%;overflow:hidden;" viewBox="0 0 200 200">
                  <defs>
                    <filter id="rf-${id}"><feGaussianBlur stdDeviation="1.5" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                    <style>@keyframes rdraw{0%{stroke-dashoffset:200}100%{stroke-dashoffset:0}}</style>
                  </defs>
                  <g filter="url(#rf-${id})" stroke="rgba(110,70,28,.75)" stroke-width="1.4" fill="none">
                    <path d="M100,100 Q112,84 132,72 Q148,62 162,58" stroke-dasharray="200" style="animation:rdraw 3.5s linear infinite"/>
                    <path d="M100,100 Q118,102 140,106 Q156,108 172,102" stroke-dasharray="200" style="animation:rdraw 4s linear infinite .5s"/>
                    <path d="M100,100 Q106,120 108,142 Q110,158 102,172" stroke-dasharray="200" style="animation:rdraw 3s linear infinite 1s"/>
                    <path d="M100,100 Q86,120 76,142 Q66,158 58,172" stroke-dasharray="200" style="animation:rdraw 3.8s linear infinite 1.5s"/>
                    <path d="M100,100 Q82,102 60,106 Q44,108 28,102" stroke-dasharray="200" style="animation:rdraw 4.2s linear infinite .8s"/>
                    <path d="M100,100 Q88,84 68,72 Q52,62 38,58" stroke-dasharray="200" style="animation:rdraw 3.2s linear infinite .3s"/>
                    <path d="M100,100 Q103,80 106,60 Q108,44 104,28" stroke-dasharray="200" style="animation:rdraw 2.8s linear infinite 1.2s"/>
                    <path d="M100,100 Q115,88 135,82 Q152,78 168,82" stroke-dasharray="200" style="animation:rdraw 3.6s linear infinite .6s"/>
                  </g>
                  <g fill="rgba(130,88,42,.4)">
                    <circle cx="150" cy="52" r="2.2"/><circle cx="170" cy="78" r="1.6"/><circle cx="176" cy="106" r="2"/>
                    <circle cx="164" cy="132" r="1.6"/><circle cx="145" cy="152" r="2.2"/><circle cx="120" cy="168" r="1.6"/>
                    <circle cx="94" cy="172" r="2"/><circle cx="68" cy="164" r="1.6"/><circle cx="48" cy="148" r="2.2"/>
                    <circle cx="26" cy="100" r="2"/><circle cx="32" cy="74" r="1.6"/><circle cx="50" cy="52" r="2.2"/>
                    <circle cx="100" cy="32" r="2"/><circle cx="130" cy="38" r="1.6"/>
                  </g>
                </svg>
                <div class="mineral" style="--ma:0deg;animation-duration:22s"><div class="mineral-gem" style="--mw:7px;--mh:7px;--mc:rgba(255,200,80,.7);--mc2:rgba(255,160,40,.4);background:radial-gradient(circle at 35% 30%,#ffe090,#cc8800)"></div></div>
                <div class="mineral" style="--ma:72deg;animation-duration:28s"><div class="mineral-gem" style="--mw:5px;--mh:8px;--mc:rgba(150,220,150,.7);--mc2:rgba(80,180,80,.4);background:radial-gradient(circle at 35% 30%,#a0e890,#308830)"></div></div>
                <div class="mineral" style="--ma:144deg;animation-duration:18s"><div class="mineral-gem" style="--mw:8px;--mh:5px;--mc:rgba(120,180,255,.7);--mc2:rgba(60,120,220,.4);background:radial-gradient(circle at 35% 30%,#90c8ff,#2060c0)"></div></div>
                <div class="mineral" style="--ma:216deg;animation-duration:32s"><div class="mineral-gem" style="--mw:6px;--mh:6px;--mc:rgba(220,80,80,.6);--mc2:rgba(180,30,30,.35);background:radial-gradient(circle at 35% 30%,#ff9090,#c02020)"></div></div>
                <div class="mineral" style="--ma:288deg;animation-duration:25s"><div class="mineral-gem" style="--mw:5px;--mh:9px;--mc:rgba(200,160,255,.7);--mc2:rgba(140,80,220,.4);background:radial-gradient(circle at 35% 30%,#d8a0ff,#7030c0)"></div></div>
                <div class="av" style="border:2px solid rgba(130,82,36,.6);box-shadow:0 0 22px rgba(110,65,20,.65),0 0 45px rgba(85,42,10,.32)"></div>`;
        }
        else if (id === 'lightning') {
            return `
                <div class="bolt-ring" style="width:195px;height:195px;"></div>
                <div class="bolt-ring" style="width:172px;height:172px;opacity:.7;"></div>
                <div class="bolt-ring" style="width:149px;height:149px;opacity:.4;"></div>
                <svg class="bolt-svg" viewBox="0 0 200 200">
                  <defs>
                    <filter id="boltglow-${id}"><feGaussianBlur stdDeviation="2.5" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                  </defs>
                  <g filter="url(#boltglow-${id})" stroke-linecap="round" stroke-linejoin="round" fill="none">
                    <polyline points="100,10 95,34 106,30 99,55" stroke="#b8d8ff" stroke-width="2.5" style="animation:bflash 2s ease-in-out infinite"/>
                    <polyline points="190,100 166,95 169,105 144,100" stroke="#b8d8ff" stroke-width="2.5" style="animation:bflash2 1.8s ease-in-out infinite .4s"/>
                    <polyline points="100,190 105,166 95,169 100,144" stroke="#b8d8ff" stroke-width="2.5" style="animation:bflash3 2.2s ease-in-out infinite .9s"/>
                    <polyline points="10,100 34,105 31,95 56,100" stroke="#b8d8ff" stroke-width="2.5" style="animation:bflash4 2.1s ease-in-out infinite 1.4s"/>
                    <polyline points="165,35 148,50 156,56 138,74" stroke="#a0c8ff" stroke-width="2" style="animation:bflash 2.4s ease-in-out infinite .2s"/>
                    <polyline points="165,165 148,150 156,144 138,126" stroke="#a0c8ff" stroke-width="2" style="animation:bflash2 2.1s ease-in-out infinite 1.1s"/>
                    <polyline points="35,165 52,150 44,144 62,126" stroke="#a0c8ff" stroke-width="2" style="animation:bflash3 1.9s ease-in-out infinite .7s"/>
                    <polyline points="35,35 52,50 44,56 62,74" stroke="#a0c8ff" stroke-width="2" style="animation:bflash4 2.3s ease-in-out infinite 1.6s"/>
                  </g>
                </svg>
                <div class="ice-spark" style="width:3px;height:3px;--idx:78px;--idy:8px;--idx2:95px;--idy2:-18px;animation-duration:1.6s;animation-delay:0s"></div>
                <div class="ice-spark" style="width:2px;height:2px;--idx:-72px;--idy:30px;--idx2:-90px;--idy2:8px;animation-duration:2s;animation-delay:.3s"></div>
                <div class="ice-spark" style="width:3px;height:3px;--idx:20px;--idy:-80px;--idx2:38px;--idy2:-96px;animation-duration:1.4s;animation-delay:.8s"></div>
                <div class="ice-spark" style="width:2px;height:2px;--idx:-62px;--idy:-52px;--idx2:-80px;--idy2:-68px;animation-duration:1.8s;animation-delay:1.2s"></div>
                <div class="ice-spark" style="width:3px;height:3px;--idx:60px;--idy:-58px;--idx2:78px;--idy2:-74px;animation-duration:1.5s;animation-delay:.5s"></div>
                <div class="av" style="border:2px solid rgba(170,200,255,.62);box-shadow:0 0 26px rgba(140,185,255,.82),0 0 65px rgba(100,150,255,.4),inset 0 0 18px rgba(120,170,255,.12)"></div>`;
        }
        else if (id === 'ice') {
            return `
                <div class="frost-outer fo1"></div><div class="frost-outer fo2"></div>
                <svg class="ice-svg" viewBox="0 0 200 200">
                  <defs>
                    <filter id="iceglow-${id}"><feGaussianBlur stdDeviation="2" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                    <style>@keyframes irot{from{transform-origin:100px 100px;transform:rotate(0)}to{transform-origin:100px 100px;transform:rotate(360deg)}} @keyframes irot2{from{transform-origin:100px 100px;transform:rotate(0)}to{transform-origin:100px 100px;transform:rotate(-360deg)}}</style>
                  </defs>
                  <!-- SVG internal layout (lines/polygons) skipped for brevity to reduce string length in this example, same base mechanics but simplified string -->
                  <g filter="url(#iceglow-${id})" stroke="rgba(175,228,255,.75)" stroke-width="1.4" fill="none" style="animation:irot 32s linear infinite">
                    ${[0, 60, 120, 180, 240, 300].map(a => `<line x1="100" y1="100" x2="100" y2="12" transform="rotate(${a},100,100)"/><line x1="90" y1="38" x2="110" y2="38" transform="rotate(${a},100,100)"/>`).join('')}
                    <polygon points="100,80 116,90 116,110 100,120 84,110 84,90" stroke-opacity=".5"/>
                  </g>
                  <g filter="url(#iceglow-${id})" stroke="rgba(200,242,255,.52)" stroke-width=".9" fill="none" style="animation:irot2 20s linear infinite">
                    ${[0, 60, 120, 180, 240, 300].map(a => `<line x1="100" y1="100" x2="100" y2="52" transform="rotate(${a},100,100)"/>`).join('')}
                  </g>
                </svg>
                <div class="flake-o" style="--foa:0deg;animation-duration:14s">❄</div>
                <div class="flake-o" style="--foa:60deg;animation-duration:14s">❅</div>
                <div class="flake-o" style="--foa:120deg;animation-duration:14s">❆</div>
                <div class="flake-o" style="--foa:180deg;animation-duration:14s">❄</div>
                <div class="flake-o" style="--foa:240deg;animation-duration:14s">❅</div>
                <div class="flake-o" style="--foa:300deg;animation-duration:14s">❆</div>
                <div class="ice-spark" style="--idx:-82px;--idy:18px;--idx2:-96px;--idy2:-8px;animation-duration:3s;animation-delay:0s"></div>
                <div class="ice-spark" style="--idx:75px;--idy:-42px;--idx2:90px;--idy2:-60px;animation-duration:3.5s;animation-delay:1s"></div>
                <div class="ice-spark" style="--idx:20px;--idy:88px;--idx2:35px;--idy2:70px;animation-duration:2.8s;animation-delay:2s"></div>
                <div class="ice-spark" style="--idx:-60px;--idy:-70px;--idx2:-74px;--idy2:-86px;animation-duration:4s;animation-delay:.5s"></div>
                <div class="av" style="border:2px solid rgba(182,234,255,.72);box-shadow:0 0 26px rgba(145,215,255,.72),0 0 55px rgba(100,185,255,.32),inset 0 0 18px rgba(182,234,255,.22)"></div>`;
        }
        else if (id === 'wind') {
            return `
                <div class="wind-ring" style="width:195px;height:195px;"></div>
                <div class="wind-ring" style="width:170px;height:170px;opacity:.65;"></div>
                <div class="wind-ring" style="width:145px;height:145px;opacity:.35;"></div>
                <svg style="position:absolute;inset:0;width:100%;height:100%;overflow:hidden;" viewBox="0 0 200 200">
                  <defs><style>@keyframes wdash{0%{stroke-dashoffset:120}100%{stroke-dashoffset:-120}} @keyframes wdash2{0%{stroke-dashoffset:-80}100%{stroke-dashoffset:80}}</style></defs>
                  <g stroke-linecap="round" fill="none" opacity=".65">
                    <path d="M22,80 Q50,70 80,78 Q100,82 115,74" stroke="rgba(200,228,248,.5)" stroke-width="1.8" stroke-dasharray="12 8" style="animation:wdash 2s linear infinite"/>
                    <path d="M35,110 Q65,98 95,108 Q118,114 140,104" stroke="rgba(180,218,245,.45)" stroke-width="1.5" stroke-dasharray="10 8" style="animation:wdash 2.5s linear infinite .3s"/>
                    <path d="M15,130 Q55,118 85,128 Q110,135 138,122" stroke="rgba(190,224,248,.4)" stroke-width="1.4" stroke-dasharray="14 10" style="animation:wdash 3s linear infinite .6s"/>
                    <path d="M178,60 Q150,50 118,60 Q95,67 76,58" stroke="rgba(200,228,248,.45)" stroke-width="1.6" stroke-dasharray="10 8" style="animation:wdash2 2.2s linear infinite .4s"/>
                    <path d="M185,90 Q155,80 122,90 Q99,97 72,88" stroke="rgba(180,218,245,.4)" stroke-width="1.4" stroke-dasharray="12 10" style="animation:wdash2 2.8s linear infinite .8s"/>
                    <path d="M165,145 Q138,132 108,142 Q84,150 55,138" stroke="rgba(190,224,248,.35)" stroke-width="1.2" stroke-dasharray="10 10" style="animation:wdash2 3.2s linear infinite 1.2s"/>
                  </g>
                </svg>
                <div class="dust" style="width:4px;height:4px;--dx:72px;--dy:24px;--dx2:90px;--dy2:2px;animation-duration:2.2s;animation-delay:0s"></div>
                <div class="dust" style="width:3px;height:3px;--dx:-68px;--dy:38px;--dx2:-86px;--dy2:14px;animation-duration:2.8s;animation-delay:.4s"></div>
                <div class="dust" style="width:5px;height:5px;--dx:32px;--dy:-74px;--dx2:50px;--dy2:-92px;animation-duration:2s;animation-delay:.9s"></div>
                <div class="dust" style="width:3px;height:3px;--dx:-76px;--dy:-32px;--dx2:-94px;--dy2:-54px;animation-duration:3.2s;animation-delay:.2s"></div>
                <div class="dust" style="width:4px;height:4px;--dx:55px;--dy:58px;--dx2:74px;--dy2:36px;animation-duration:2.5s;animation-delay:1.3s"></div>
                <div class="av" style="border:2px solid rgba(185,224,250,.5);box-shadow:0 0 22px rgba(155,208,250,.6),0 0 50px rgba(110,185,245,.22)"></div>`;
        }
        else if (id === 'forest') {
            return `
                <div class="vine-ring" style="width:194px;height:194px;"></div>
                <div class="vine-ring" style="width:170px;height:170px;border-color:rgba(55,148,55,.19)"></div>
                <div class="vine-ring" style="width:146px;height:146px;border-color:rgba(60,158,60,.15)"></div>
                <div class="vine-ring" style="width:108px;height:108px;border-color:rgba(50,138,50,.12)"></div>
                <svg style="position:absolute;inset:0;width:100%;height:100%;overflow:hidden;" viewBox="0 0 200 200">
                  <defs><filter id="vglow-${id}"><feGaussianBlur stdDeviation="1.5" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs>
                  <g filter="url(#vglow-${id})" stroke="rgba(45,165,55,.68)" stroke-width="1.6" fill="none">
                    <path d="M100,100 Q112,76 130,60 Q148,44 158,28 Q165,16 160,6" stroke-dasharray="260" style="animation:vgrow 4s ease-in-out infinite"/>
                    <path d="M100,100 Q124,112 142,130 Q158,148 168,165 Q174,178 168,190" stroke-dasharray="260" style="animation:vgrow 4.5s ease-in-out infinite .6s"/>
                    <path d="M100,100 Q76,112 58,130 Q42,148 32,165 Q26,178 32,190" stroke-dasharray="260" style="animation:vgrow 3.8s ease-in-out infinite 1.2s"/>
                    <path d="M100,100 Q88,76 70,60 Q52,44 42,28 Q35,16 40,6" stroke-dasharray="260" style="animation:vgrow 5s ease-in-out infinite 1.8s"/>
                  </g>
                </svg>
                <div class="leaf-orbit" style="--loa:15deg;animation-duration:11s">🍃</div><div class="leaf-orbit" style="--loa:105deg;animation-duration:13s">🌿</div>
                <div class="leaf-orbit" style="--loa:195deg;animation-duration:10s">🍃</div><div class="leaf-orbit" style="--loa:285deg;animation-duration:12s">🌿</div>
                <div class="spore" style="width:4px;height:4px;--spx:62px;--spy:52px;--spx2:82px;--spy2:28px;animation-duration:2.8s;animation-delay:0s"></div>
                <div class="spore" style="width:3px;height:3px;--spx:-58px;--spy:-46px;--spx2:-78px;--spy2:-66px;animation-duration:3.2s;animation-delay:.9s"></div>
                <div class="spore" style="width:5px;height:5px;--spx:42px;--spy:-72px;--spx2:62px;--spy2:-90px;animation-duration:2.4s;animation-delay:1.8s"></div>
                <div class="av" style="border:2px solid rgba(45,165,55,.52);box-shadow:0 0 24px rgba(35,148,48,.65),0 0 55px rgba(22,105,32,.28),inset 0 0 16px rgba(45,165,55,.12)"></div>`;
        }
        else if (id === 'storm') {
            return `
                <div class="storm-ring" style="width:195px;height:195px;"></div>
                <div class="storm-ring" style="width:168px;height:168px;opacity:.7;"></div>
                <div class="storm-ring" style="width:141px;height:141px;opacity:.4;"></div>
                <svg style="position:absolute;inset:0;width:100%;height:100%;overflow:visible" viewBox="0 0 200 200">
                  <defs><filter id="sglow-${id}"><feGaussianBlur stdDeviation="2.5" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs>
                  <g filter="url(#sglow-${id})" stroke-linecap="round" stroke-linejoin="round" fill="none">
                    <polyline points="100,52 93,74 104,70 96,94" stroke="#c0d8ff" stroke-width="2.8" style="animation:thunder-flash 2s ease-in-out infinite"/>
                    <polyline points="125,62 119,80 128,77 122,96" stroke="#a8ccff" stroke-width="2.2" style="animation:thunder-flash2 2.4s ease-in-out infinite .6s"/>
                    <polyline points="72,65 66,84 76,80 70,100" stroke="#a8ccff" stroke-width="2.2" style="animation:thunder-flash3 2.8s ease-in-out infinite 1.2s"/>
                  </g>
                </svg>
                <div class="rain-d" style="height:15px;--rx:52px;--ry:-72px;--rx2:50px;--ry2:8px;animation-duration:1.2s;animation-delay:0s"></div>
                <div class="rain-d" style="height:11px;--rx:-62px;--ry:-65px;--rx2:-64px;--ry2:5px;animation-duration:1.45s;animation-delay:.15s"></div>
                <div class="rain-d" style="height:17px;--rx:20px;--ry:-82px;--rx2:18px;--ry2:-2px;animation-duration:1.1s;animation-delay:.3s"></div>
                <div class="rain-d" style="height:13px;--rx:-32px;--ry:-75px;--rx2:-34px;--ry2:5px;animation-duration:1.35s;animation-delay:.45s"></div>
                <div class="rain-d" style="height:15px;--rx:72px;--ry:-60px;--rx2:70px;--ry2:10px;animation-duration:1.2s;animation-delay:.6s"></div>
                <div class="rain-d" style="height:11px;--rx:-78px;--ry:-55px;--rx2:-80px;--ry2:12px;animation-duration:1.55s;animation-delay:.75s"></div>
                <div class="av" style="border:2px solid rgba(135,158,205,.42);box-shadow:0 0 22px rgba(105,128,188,.55),0 0 55px rgba(82,105,168,.22)"></div>`;
        }
        else if (id === 'sea') {
            return `
                <div class="sea-bg"></div>
                <svg class="sea-svg" style="position:absolute;inset:0;width:100%;height:100%;border-radius:50%" viewBox="0 0 200 200">
                  <defs>
                    <filter id="bioglow-${id}"><feGaussianBlur stdDeviation="3" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
                    <style>@keyframes jbob{0%,100%{transform:translate(var(--jx),var(--jy)) scaleY(1)}50%{transform:translate(var(--jx),var(--jy)) scaleY(.72)}} @keyframes tentacle{0%,100%{opacity:.5;transform:scaleY(1)}50%{opacity:.85;transform:scaleY(1.4)}} @keyframes plankton-blink{0%,100%{opacity:.4}50%{opacity:1}}</style>
                  </defs>
                  
                  <g style="--jx:-50px;--jy:-28px;animation:jbob 2.1s ease-in-out infinite" filter="url(#bioglow-${id})">
                    <ellipse cx="50" cy="72" rx="11" ry="7" fill="rgba(80,255,195,.35)" stroke="rgba(80,255,195,.65)" stroke-width=".7"/>
                    <line x1="45" y1="79" x2="43" y2="94" stroke="rgba(80,255,195,.35)" stroke-width="1" style="animation:tentacle 2.1s ease-in-out infinite"/>
                    <line x1="49" y1="79" x2="49" y2="96" stroke="rgba(80,255,195,.35)" stroke-width="1" style="animation:tentacle 2.1s ease-in-out infinite .2s"/>
                    <line x1="53" y1="79" x2="55" y2="93" stroke="rgba(80,255,195,.35)" stroke-width="1" style="animation:tentacle 2.1s ease-in-out infinite .4s"/>
                  </g>
                  <g style="--jx:50px;--jy:28px;animation:jbob 2.6s ease-in-out infinite .5s" filter="url(#bioglow-${id})">
                    <ellipse cx="150" cy="128" rx="9" ry="5.5" fill="rgba(175,95,255,.32)" stroke="rgba(175,95,255,.58)" stroke-width=".7"/>
                    <line x1="145" y1="134" x2="143" y2="147" stroke="rgba(175,95,255,.28)" stroke-width=".9" style="animation:tentacle 2.6s ease-in-out infinite"/>
                    <line x1="150" y1="134" x2="150" y2="149" stroke="rgba(175,95,255,.28)" stroke-width=".9" style="animation:tentacle 2.6s ease-in-out infinite .3s"/>
                  </g>
                  <g filter="url(#bioglow-${id})" style="animation:plankton-blink 3s ease-in-out infinite">
                    <circle cx="30" cy="52" r="1.8" fill="rgba(80,255,195,.85)"/><circle cx="55" cy="24" r="1.2" fill="rgba(70,195,255,.75)"/>
                    <circle cx="172" cy="42" r="1.8" fill="rgba(175,95,255,.85)"/><circle cx="160" cy="70" r="1.2" fill="rgba(80,255,195,.7)"/>
                  </g>
                </svg>
                <div class="bio-particle" style="width:5px;height:5px;--bc:rgba(80,255,195,.8); --boa:0deg;  animation-duration:9s"></div>
                <div class="bio-particle" style="width:4px;height:4px;--bc:rgba(70,195,255,.7); --boa:51deg; animation-duration:9s;animation-delay:.6s"></div>
                <div class="bio-particle" style="width:6px;height:6px;--bc:rgba(175,95,255,.8); --boa:102deg;animation-duration:9s;animation-delay:1.2s"></div>
                <div class="sea-bubble-r" style="width:7px;height:7px;--brx:-42px;--brd:6px;animation-duration:5.5s;animation-delay:0s"></div>
                <div class="sea-bubble-r" style="width:5px;height:5px;--brx:22px;--brd:-9px;animation-duration:7s;animation-delay:1.5s"></div>
                <div class="sea-bubble-r" style="width:9px;height:9px;--brx:-8px;--brd:13px;animation-duration:6.5s;animation-delay:3s"></div>
                <div class="av" style="border:2px solid rgba(0,178,205,.48);box-shadow:0 0 26px rgba(0,162,205,.65),0 0 55px rgba(0,125,185,.28),inset 0 0 18px rgba(0,178,220,.12)"></div>`;
        }
    }

    function render(cssClass, scalePxContainerSize) {
        const id = cssClass.replace('element-', '');
        const scale = scalePxContainerSize / 200; // Base size of Elements borders is 200px

        // s-water and s-sea need explicit overflow hidden on the inner scene wrapper as well
        let innerClasses = `scene s-${id}`;

        return `<div class="element-aw" style="transform: translate(-50%, -50%) scale(${scale});">
            <div class="${innerClasses}">
                ${buildInner(id)}
            </div>
        </div>`;
    }

    return {
        renderHTML: render,
        getMeta: function (cssClass) {
            const id = cssClass.replace('element-', '');
            return elements.find(z => z.id === id);
        }
    };
})();
