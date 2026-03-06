/**
 * CuanCapital Experience OS: Universe Avatar Borders
 * Generates HTML nodes for 15 cosmological avatar rings.
 */

window.UniverseBorders = (function () {

    function buildInner(id) {
        switch (id) {
            case 'universe-solar':
                return `
                    <div class="sol-orbit-ring" style="width:76px;height:76px;"></div>
                    <div class="sol-orbit-ring" style="width:100px;height:100px;"></div>
                    <div class="sol-orbit-ring" style="width:124px;height:124px;"></div>
                    <div class="sol-orbit-ring" style="width:148px;height:148px;"></div>
                    <div class="sol-orbit-ring" style="width:172px;height:172px;"></div>
                    <div class="sol-orbit-ring" style="width:196px;height:196px;"></div>
                    <div class="sol-orbit-ring" style="width:220px;height:220px;"></div>
                    <div class="sol-orbit-ring" style="width:244px;height:244px;"></div>
                    <div class="sol-planet p-mercury" style="width:6px;height:6px;margin:-3px 0 0 -3px;background:radial-gradient(circle at 40% 35%,#d4d4d4,#888,#555);"></div>
                    <div class="sol-planet p-venus"   style="width:9px;height:9px;margin:-4.5px 0 0 -4.5px;background:radial-gradient(circle at 35% 30%,#f7eaaa,#e8c46a,#c9a040);"></div>
                    <div class="sol-planet p-earth"   style="width:10px;height:10px;margin:-5px 0 0 -5px;background:radial-gradient(circle at 35% 30%,#72d9ff,#3b8fd8,#2a6faa);box-shadow:0 0 5px rgba(50,150,255,.8);"></div>
                    <div class="sol-planet p-mars"    style="width:7px;height:7px;margin:-3.5px 0 0 -3.5px;background:radial-gradient(circle at 35% 30%,#e8835a,#c04f2a,#8c2818);"></div>
                    <div class="sol-planet p-jupiter" style="width:15px;height:15px;margin:-7.5px 0 0 -7.5px;background:radial-gradient(circle at 35% 30%,#f5ddb0,#d4a96a,#a87840);box-shadow:0 0 6px rgba(220,170,80,.5);"></div>
                    <div class="sol-planet p-saturn"  style="width:12px;height:12px;margin:-6px 0 0 -6px;background:radial-gradient(circle at 35% 30%,#f0e0b0,#c8a878,#a08050);"></div>
                    <div class="sol-planet p-uranus"  style="width:10px;height:10px;margin:-5px 0 0 -5px;background:radial-gradient(circle at 40% 35%,#b8f0f0,#78d0d0,#4aabab);"></div>
                    <div class="sol-planet p-neptune" style="width:10px;height:10px;margin:-5px 0 0 -5px;background:radial-gradient(circle at 35% 30%,#9090ff,#4040cc,#1e1e80);"></div>
                    <div class="sol-saturn-wrap" style="position:absolute;left:50%;top:50%;width:0;height:0;z-index:6;pointer-events:none;">
                        <div style="position:absolute;width:26px;height:6px;border:1.5px solid rgba(210,170,100,.75);border-radius:50%;transform:translate(-50%,-50%) rotateX(68deg);"></div>
                    </div>
                `;

            case 'universe-blackhole':
                return `
                    <div class="bh-disk bh-d2"></div>
                    <div class="bh-disk bh-d1"></div>
                    <div class="bh-lens bh-l1"></div>
                    <div class="bh-lens bh-l2"></div>
                    <div class="bh-rad" style="--a:0deg;  animation-duration:1.8s; animation-delay:0s;"></div>
                    <div class="bh-rad" style="--a:45deg; animation-duration:2.2s; animation-delay:.3s;"></div>
                    <div class="bh-rad" style="--a:90deg; animation-duration:1.6s; animation-delay:.6s;"></div>
                    <div class="bh-rad" style="--a:135deg;animation-duration:2s;   animation-delay:.9s;"></div>
                    <div class="bh-rad" style="--a:180deg;animation-duration:1.9s; animation-delay:1.2s;"></div>
                    <div class="bh-rad" style="--a:225deg;animation-duration:2.1s; animation-delay:1.5s;"></div>
                    <div class="bh-rad" style="--a:270deg;animation-duration:1.7s; animation-delay:.2s;"></div>
                    <div class="bh-rad" style="--a:315deg;animation-duration:2.3s; animation-delay:.8s;"></div>
                `;

            case 'universe-nebula':
                return `
                    <div class="neb-cloud neb-c1"></div>
                    <div class="neb-cloud neb-c2"></div>
                    <div class="neb-cloud neb-c3"></div>
                    <div class="neb-cloud neb-c4"></div>
                    <div class="neb-star" style="width:3px;height:3px;top:28px;left:55px;--op:.9;animation-duration:2.1s;box-shadow:0 0 4px #fff;"></div>
                    <div class="neb-star" style="width:2px;height:2px;top:45px;left:120px;--op:.7;animation-duration:3.2s;"></div>
                    <div class="neb-star" style="width:4px;height:4px;top:80px;left:30px;--op:1;animation-duration:1.8s;box-shadow:0 0 6px rgba(200,150,255,.8);"></div>
                    <div class="neb-star" style="width:2px;height:2px;top:130px;left:140px;--op:.8;animation-duration:2.7s;"></div>
                    <div class="neb-star" style="width:3px;height:3px;top:155px;left:60px;--op:.6;animation-duration:4s;"></div>
                    <div class="neb-ring" style="width:180px;height:180px;animation-duration:60s;"></div>
                `;

            case 'universe-comet':
                return `
                    <div class="comet-star" style="width:1.5px;height:1.5px;top:20px;left:30px;--op:.5;animation-duration:3s;"></div>
                    <div class="comet-star" style="width:1px;height:1px;top:50px;left:180px;--op:.4;animation-duration:4s;"></div>
                    <div class="comet-star" style="width:2px;height:2px;top:100px;left:20px;--op:.6;animation-duration:2.5s;"></div>
                    <div class="comet-star" style="width:1px;height:1px;top:130px;left:200px;--op:.3;animation-duration:5s;"></div>
                    <div class="comet-star" style="width:1.5px;height:1.5px;top:160px;left:80px;--op:.5;animation-duration:3.5s;"></div>
                    
                    <div style="position:absolute;width:4px;height:4px;border-radius:50%;background:rgba(180,220,255,.5);animation:comet-fly 3.5s ease-in-out infinite .4s;filter:blur(1px);"></div>
                    <div style="position:absolute;width:3px;height:3px;border-radius:50%;background:rgba(180,220,255,.4);animation:comet-fly 3.5s ease-in-out infinite .7s;filter:blur(1px);"></div>
                    
                    <div class="comet-tail2" style="width:60px;transform:translate(-80px,-46px) rotate(37deg);"></div>
                    <div class="comet-tail" style="width:140px;height:5px;transform:translate(-60px,-44px) rotate(37deg);"></div>
                    
                    <div class="comet-obj" style="width:10px;height:10px;"></div>
                    <div style="position:absolute;width:24px;height:24px;border-radius:50%;background:radial-gradient(circle,rgba(200,230,255,.3),transparent 70%);filter:blur(4px);animation:comet-fly 3.5s ease-in-out infinite;z-index:9;"></div>
                `;

            case 'universe-pulsar':
                return `
                    <div class="pulsar-ring pr1"></div>
                    <div class="pulsar-ring pr2"></div>
                    <div class="pulsar-ring pr3"></div>
                    <div class="pulsar-cone"></div>
                    <div class="pulsar-beam"></div>
                    <div class="pulsar-beam2"></div>
                `;

            case 'universe-galaxy':
                return `
                    <div class="gal-star" style="width:2px;height:2px;top:24px;left:90px;--op:.8;animation-duration:2.5s;"></div>
                    <div class="gal-star" style="width:1.5px;height:1.5px;top:40px;left:140px;--op:.6;animation-duration:3.5s;"></div>
                    <div class="gal-star" style="width:2px;height:2px;top:60px;left:28px;--op:.7;animation-duration:2s;"></div>
                    <div class="gal-star" style="width:3px;height:3px;top:80px;left:168px;--op:.9;animation-duration:1.8s;box-shadow:0 0 4px rgba(255,220,150,.7);"></div>
                    <div class="gal-star" style="width:1.5px;height:1.5px;top:140px;left:44px;--op:.6;animation-duration:4s;"></div>
                    <div class="gal-star" style="width:2px;height:2px;top:160px;left:130px;--op:.7;animation-duration:3s;"></div>
                    <div class="gal-star" style="width:1px;height:1px;top:170px;left:80px;--op:.5;animation-duration:5s;"></div>
                    <div class="gal-star" style="width:2px;height:2px;top:32px;left:55px;--op:.65;animation-duration:2.8s;"></div>
                    
                    <svg class="galaxy-svg" viewBox="0 0 200 200">
                      <defs>
                        <radialGradient id="galcenter" cx="50%" cy="50%" r="50%">
                          <stop offset="0%" stop-color="rgba(255,220,150,0.5)"/>
                          <stop offset="40%" stop-color="rgba(255,180,80,0.2)"/>
                          <stop offset="100%" stop-color="transparent"/>
                        </radialGradient>
                        <filter id="galblur"><feGaussianBlur stdDeviation="3"/></filter>
                      </defs>
                      <circle cx="100" cy="100" r="40" fill="url(#galcenter)" filter="url(#galblur)"/>
                      <path d="M100,100 Q120,75 145,65 Q165,58 178,70 Q188,82 182,98 Q175,115 160,125 Q140,135 120,130 Q105,125 100,115" fill="none" stroke="rgba(255,220,150,0.35)" stroke-width="8" filter="url(#galblur)"/>
                      <path d="M100,100 Q80,125 55,135 Q35,142 22,130 Q12,118 18,102 Q25,85 40,75 Q60,65 80,70 Q95,75 100,85" fill="none" stroke="rgba(255,200,120,0.3)" stroke-width="8" filter="url(#galblur)"/>
                      <circle cx="100" cy="100" r="92" fill="none" stroke="rgba(255,200,100,0.08)" stroke-width="6" filter="url(#galblur)"/>
                      <path d="M100,100 Q118,72 142,63 Q163,56 176,68" fill="none" stroke="rgba(255,230,180,0.5)" stroke-width="1.5"/>
                      <path d="M100,100 Q82,128 58,137 Q37,144 24,132" fill="none" stroke="rgba(255,210,150,0.45)" stroke-width="1.5"/>
                      <path d="M100,100 Q125,92 148,102 Q168,112 172,130" fill="none" stroke="rgba(255,220,160,0.3)" stroke-width="1"/>
                      <path d="M100,100 Q75,108 52,98 Q32,88 28,70" fill="none" stroke="rgba(255,200,140,0.28)" stroke-width="1"/>
                    </svg>
                `;

            case 'universe-asteroid':
                return `
                    <div style="position:absolute;width:196px;height:196px;border-radius:50%;border:1px dashed rgba(180,160,120,.12);"></div>
                    <div style="position:absolute;width:170px;height:170px;border-radius:50%;border:1px dashed rgba(160,140,100,.1);"></div>
                    
                    <div class="ast-belt" style="width:190px;height:190px;animation-duration:32s;">
                      <div class="ast-p" style="width:5px;height:4px;top:-2px;left:92.5px;border-radius:30% 60% 40% 70%;"></div>
                      <div class="ast-p" style="width:4px;height:5px;top:94px;left:-2px;border-radius:50% 30% 60% 40%;"></div>
                      <div class="ast-p" style="width:6px;height:4px;top:186px;left:92px;border-radius:40% 70% 30% 55%;"></div>
                      <div class="ast-p" style="width:4px;height:6px;top:92px;left:186px;border-radius:55% 35% 65% 30%;"></div>
                      <div class="ast-p" style="width:5px;height:4px;top:14px;left:25px;border-radius:40%;transform:rotate(25deg);"></div>
                      <div class="ast-p" style="width:4px;height:5px;top:25px;left:150px;border-radius:35%;transform:rotate(-15deg);"></div>
                      <div class="ast-p" style="width:6px;height:4px;top:153px;left:20px;border-radius:45%;transform:rotate(40deg);"></div>
                      <div class="ast-p" style="width:4px;height:6px;top:155px;left:158px;border-radius:30%;transform:rotate(-35deg);"></div>
                    </div>
                    
                    <div class="ast-belt" style="width:160px;height:160px;animation-duration:20s;animation-direction:reverse;">
                      <div class="ast-p" style="width:4px;height:3px;top:-1.5px;left:78px;border-radius:50%;background:rgba(180,160,130,.9);"></div>
                      <div class="ast-p" style="width:3px;height:4px;top:78px;left:-1.5px;border-radius:50%;background:rgba(160,140,110,.8);"></div>
                      <div class="ast-p" style="width:5px;height:3px;top:157px;left:77.5px;border-radius:50%;background:rgba(200,180,150,.8);"></div>
                      <div class="ast-p" style="width:3px;height:5px;top:77.5px;left:157px;border-radius:50%;background:rgba(170,150,120,.9);"></div>
                      <div class="ast-p" style="width:4px;height:3px;top:10px;left:18px;border-radius:40%;transform:rotate(20deg);background:rgba(190,170,140,.7);"></div>
                      <div class="ast-p" style="width:3px;height:4px;top:18px;left:134px;border-radius:35%;transform:rotate(-10deg);background:rgba(180,160,130,.8);"></div>
                      <div class="ast-p" style="width:5px;height:3px;top:134px;left:12px;border-radius:40%;transform:rotate(45deg);background:rgba(170,150,120,.9);"></div>
                      <div class="ast-p" style="width:3px;height:4px;top:136px;left:140px;border-radius:30%;transform:rotate(-30deg);background:rgba(200,180,150,.7);"></div>
                    </div>
                `;

            case 'universe-supernova':
                return `
                    <div class="sn-wave sw1"></div>
                    <div class="sn-wave sw2"></div>
                    <div class="sn-wave sw3"></div>
                    <div class="sn-wave sw4"></div>
                    
                    <div class="sn-ray" style="--r:0deg;   height:90px;"></div>
                    <div class="sn-ray" style="--r:30deg;  height:78px;"></div>
                    <div class="sn-ray" style="--r:60deg;  height:85px;"></div>
                    <div class="sn-ray" style="--r:90deg;  height:80px;"></div>
                    <div class="sn-ray" style="--r:120deg; height:88px;"></div>
                    <div class="sn-ray" style="--r:150deg; height:76px;"></div>
                    <div class="sn-ray" style="--r:180deg; height:82px;"></div>
                    <div class="sn-ray" style="--r:210deg; height:79px;"></div>
                    <div class="sn-ray" style="--r:240deg; height:87px;"></div>
                    <div class="sn-ray" style="--r:270deg; height:81px;"></div>
                    <div class="sn-ray" style="--r:300deg; height:83px;"></div>
                    <div class="sn-ray" style="--r:330deg; height:77px;"></div>
                `;

            case 'universe-wormhole':
                return `
                    <div class="worm-ring wr6"></div>
                    <div class="worm-ring wr5"></div>
                    <div class="worm-ring wr4"></div>
                    <div class="worm-ring wr3"></div>
                    <div class="worm-ring wr2"></div>
                    <div class="worm-ring wr1"></div>
                    <div class="worm-oval wo2"></div>
                    <div class="worm-oval wo1"></div>
                `;

            case 'universe-magnetar':
                return `
                    <div class="mag-ring mr3"></div>
                    <div class="mag-ring mr2"></div>
                    <div class="mag-ring mr1"></div>
                    <div class="mag-disk"></div>
                    <div class="mag-jet mj3"></div>
                    <div class="mag-jet mj2"></div>
                    <div class="mag-jet mj1"></div>
                `;

            case 'universe-darkmatter':
                return `
                    <svg class="dark-svg" viewBox="0 0 200 200">
                      <defs>
                        <filter id="filweb">
                          <feGaussianBlur stdDeviation="1.5" result="b"/>
                          <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                        <radialGradient id="nglow" cx="50%" cy="50%" r="50%">
                          <stop offset="0%" stop-color="rgba(150,120,255,0.9)"/>
                          <stop offset="100%" stop-color="transparent"/>
                        </radialGradient>
                      </defs>
                      <g filter="url(#filweb)" stroke="rgba(100,80,200,0.35)" stroke-width="1" fill="none">
                        <path d="M20,30  Q60,50 100,100"/>
                        <path d="M180,25 Q140,55 100,100"/>
                        <path d="M10,110 Q55,105 100,100"/>
                        <path d="M190,105 Q148,103 100,100"/>
                        <path d="M30,175 Q65,145 100,100"/>
                        <path d="M172,178 Q140,148 100,100"/>
                        <path d="M100,10  Q100,55 100,100"/>
                        <path d="M100,190 Q100,145 100,100"/>
                        <path d="M20,30  Q35,15 60,20" stroke-opacity=".5"/>
                        <path d="M60,20  Q100,10 140,18" stroke-opacity=".4"/>
                        <path d="M180,25 Q170,15 155,22" stroke-opacity=".4"/>
                        <path d="M10,110 Q15,80 18,55" stroke-opacity=".35"/>
                        <path d="M190,105 Q188,75 185,50" stroke-opacity=".35"/>
                        <path d="M30,175 Q15,160 12,135" stroke-opacity=".4"/>
                        <path d="M172,178 Q188,162 192,138" stroke-opacity=".4"/>
                        <path d="M30,175 Q60,185 100,190" stroke-opacity=".45"/>
                        <path d="M172,178 Q140,188 100,190" stroke-opacity=".4"/>
                        <path d="M60,20  Q90,40 120,60 Q140,70 180,25"  stroke-opacity=".25"/>
                        <path d="M10,110 Q40,80 70,60  Q90,45 100,10"   stroke-opacity=".2"/>
                        <path d="M190,105 Q160,130 130,150 Q110,165 100,190" stroke-opacity=".22"/>
                        <path d="M30,175 Q50,140 80,120 Q95,110 10,110"  stroke-opacity=".2"/>
                      </g>
                      
                      <!-- Glowing nodes -->
                      <circle cx="100" cy="100" r="5"  fill="url(#nglow)" filter="url(#filweb)" opacity=".9"/>
                      
                      <!-- Base nodes -->
                      <circle cx="20"  cy="30"  r="3"  fill="rgba(120,100,220,.7)" filter="url(#filweb)"/>
                      <circle cx="180" cy="25"  r="2.5" fill="rgba(100,80,200,.6)"  filter="url(#filweb)"/>
                      <circle cx="10"  cy="110" r="2"  fill="rgba(120,100,220,.55)" filter="url(#filweb)"/>
                      <circle cx="190" cy="105" r="2"  fill="rgba(100,80,200,.5)"   filter="url(#filweb)"/>
                      <circle cx="30"  cy="175" r="2.5" fill="rgba(120,100,220,.6)"  filter="url(#filweb)"/>
                      <circle cx="172" cy="178" r="2"  fill="rgba(100,80,200,.55)"  filter="url(#filweb)"/>
                      <circle cx="100" cy="10"  r="2"  fill="rgba(130,110,230,.5)"  filter="url(#filweb)"/>
                      <circle cx="100" cy="190" r="2"  fill="rgba(110,90,210,.5)"   filter="url(#filweb)"/>
                      
                      <!-- Small intersection / background nodes -->
                      <circle cx="60"  cy="20"  r="1.5" fill="rgba(100,80,200,.4)" filter="url(#filweb)"/>
                      <circle cx="140" cy="18"  r="1.5" fill="rgba(100,80,200,.4)" filter="url(#filweb)"/>
                      <circle cx="185" cy="50"  r="1.5" fill="rgba(100,80,200,.35)" filter="url(#filweb)"/>
                      <circle cx="192" cy="138" r="1.5" fill="rgba(100,80,200,.35)" filter="url(#filweb)"/>
                      <circle cx="100" cy="190" r="2"   fill="rgba(100,80,200,.4)"  filter="url(#filweb)"/>
                      <circle cx="15"  cy="55"  r="1"   fill="rgba(120,100,220,.3)" filter="url(#filweb)"/>
                    </svg>
                `;

            case 'universe-quasar':
                return `
                    <div class="qsr-ring qr3"></div>
                    <div class="qsr-ring qr2"></div>
                    <div class="qsr-ring qr1"></div>
                    <div class="qsr-disk"></div>
                    
                    <div class="qsr-jet qj3"></div>
                    <div class="qsr-jet qj2"></div>
                    <div class="qsr-jet qj1"></div>
                `;

            case 'universe-cmb':
                return `
                    <div class="cmb-layer cmb-l1"></div>
                    <div class="cmb-layer cmb-l2"></div>
                `;

            case 'universe-nursery':
                return `
                    <div class="nur-cloud nc1"></div>
                    <div class="nur-cloud nc2"></div>
                    <div class="nur-cloud nc3"></div>
                    <div class="nur-cloud nc4"></div>
                    
                    <div class="protostar" style="width:5px;height:5px;background:rgba(255,250,220,.95);box-shadow:0 0 8px rgba(255,240,180,.9),0 0 16px rgba(255,200,100,.6);top:35px;left:55px;--op:1;animation-duration:1.4s;"></div>
                    <div class="protostar" style="width:4px;height:4px;background:rgba(255,240,200,.85);box-shadow:0 0 6px rgba(255,230,150,.8),0 0 12px rgba(255,180,80,.5);top:70px;left:130px;--op:.9;animation-duration:1.8s;"></div>
                    <div class="protostar" style="width:3px;height:3px;background:rgba(255,200,150,.8);box-shadow:0 0 5px rgba(255,180,100,.7);top:120px;left:45px;--op:.8;animation-duration:2.2s;"></div>
                    <div class="protostar" style="width:6px;height:6px;background:rgba(255,255,240,.9);box-shadow:0 0 10px rgba(255,240,200,.9),0 0 20px rgba(255,200,120,.5);top:50px;left:90px;--op:1;animation-duration:1.6s;"></div>
                    <div class="protostar" style="width:3px;height:3px;background:rgba(255,180,100,.75);box-shadow:0 0 5px rgba(255,150,80,.6);top:145px;left:120px;--op:.7;animation-duration:2.6s;"></div>
                    
                    <div class="nur-ring" style="width:190px;height:190px;animation-duration:80s;"></div>
                `;

            case 'universe-gravitational':
                return `
                    <div class="grav-wave gw1"></div>
                    <div class="grav-wave gw2"></div>
                    <div class="grav-wave gw3"></div>
                    <div class="grav-wave gw4"></div>
                    <div class="grav-wave gw5"></div>
                    <div class="grav-wave gw6"></div>
                    
                    <svg class="grav-grid" viewBox="0 0 200 200">
                      <defs><filter id="ggblur"><feGaussianBlur stdDeviation="0.5"/></filter></defs>
                      <g stroke="rgba(80,160,255,0.5)" stroke-width="0.5" fill="none" filter="url(#ggblur)">
                        <path d="M0,40  Q100,38 200,40"/>
                        <path d="M0,60  Q100,56 200,60"/>
                        <path d="M0,80  Q100,72 200,80"/>
                        <path d="M0,100 Q100,88 200,100"/>
                        <path d="M0,120 Q100,108 200,120"/>
                        <path d="M0,140 Q100,128 200,140"/>
                        <path d="M0,160 Q100,158 200,160"/>
                        
                        <path d="M40,0  Q38,100 40,200"/>
                        <path d="M60,0  Q56,100 60,200"/>
                        <path d="M80,0  Q72,100 80,200"/>
                        <path d="M100,0 Q88,100 100,200"/>
                        <path d="M120,0 Q108,100 120,200"/>
                        <path d="M140,0 Q128,100 140,200"/>
                        <path d="M160,0 Q158,100 160,200"/>
                      </g>
                    </svg>
                    
                    <div style="position:absolute;width:10px;height:10px;border-radius:50%;background:radial-gradient(circle,#a0c8ff,#4080d0);box-shadow:0 0 8px rgba(100,180,255,.8);margin-left:-68px;"></div>
                    <div style="position:absolute;width:10px;height:10px;border-radius:50%;background:radial-gradient(circle,#ffa060,#d06030);box-shadow:0 0 8px rgba(255,150,80,.8);margin-left:58px;"></div>
                `;

            default: return '';
        }
    }

    const PLANET_DATA = [
        { c: 'p-mercury', r: 38, period: 3.5, startAngle: 0 },
        { c: 'p-venus', r: 50, period: 8, startAngle: 45 },
        { c: 'p-earth', r: 62, period: 12, startAngle: 120 },
        { c: 'p-mars', r: 74, period: 22, startAngle: 200 },
        { c: 'p-jupiter', r: 86, period: 48, startAngle: 280 },
        { c: 'p-saturn', r: 98, period: 90, startAngle: 160 },
        { c: 'p-uranus', r: 110, period: 168, startAngle: 320 },
        { c: 'p-neptune', r: 122, period: 330, startAngle: 60 },
    ];

    let tLoopId = null;
    let tLastTime = null;

    function startEngine() {
        if (tLoopId) return;
        tLastTime = performance.now();
        tLoopId = requestAnimationFrame(tick);
    }

    function tick(timestamp) {
        let dt = timestamp - tLastTime;
        tLastTime = timestamp;

        // Find all solar system instances in the DOM
        let solars = document.querySelectorAll('.u-solar-scene');
        if (solars.length > 0) {
            solars.forEach(scene => {
                PLANET_DATA.forEach(pd => {
                    // Current angle for this instance?
                    if (!scene._t) scene._t = 0;
                    scene._t += dt;

                    let speed = (2 * Math.PI) / (pd.period * 1000);
                    let currentAngle = (pd.startAngle * Math.PI / 180) + (speed * scene._t);
                    let x = pd.r * Math.cos(currentAngle);
                    let y = pd.r * Math.sin(currentAngle);

                    let el = scene.querySelector('.' + pd.c);
                    if (el) {
                        el.style.transform = `translate(${x}px, ${y}px)`;
                    }
                    if (pd.c === 'p-saturn') {
                        let sw = scene.querySelector('.sol-saturn-wrap');
                        if (sw) sw.style.transform = `translate(${x}px, ${y}px)`;
                    }
                });
            });
        }
        tLoopId = requestAnimationFrame(tick);
    }

    return {
        /**
         * Returns inner HTML for the given universe id/class string.
         */
        renderHTML: function (cssClass, iconSize = 60) {
            let id = cssClass.trim();
            // Default size scale relative to 200px baseline layout coordinate space
            let scale = (id === 'universe-solar') ? (iconSize / 58) : (iconSize / 76);

            let extraClasses = '';
            if (id === 'universe-solar') {
                extraClasses = 'u-solar-scene';
                // Trigger engine lazily
                startEngine();
            }

            return `<div class="universe-aw ${extraClasses}" style="transform: translate(-50%, -50%) scale(${scale});">
                <div style="position:relative; width: 200px; height: 200px; display:flex; align-items:center; justify-content:center;">
                    ${buildInner(id)}
                </div>
            </div>`;
        }
    };

})();

