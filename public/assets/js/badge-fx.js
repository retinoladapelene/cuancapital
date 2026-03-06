/* =========================================================================
   PURE 2D ELITE AVATAR BORDERS ENGINE
   (Education & Business Professional System - No 3D)
   ========================================================================= */

class BadgeFX {
    constructor(element, preset = 'bronze') {
        this.container = element;
        this.presetName = preset;

        // Map old preset names if needed
        const rarityMap = {
            'common': 'bronze',
            'uncommon': 'silver',
            'rare': 'gold',
            'epic': 'platinum',
            'legendary': 'diamond',
            'mythic': 'mythic'
        };
        if (rarityMap[this.presetName]) {
            this.presetName = rarityMap[this.presetName];
        }

        this.init();
    }

    init() {
        // Clear previous injected layers if re-initializing
        Array.from(this.container.children).forEach(child => {
            if (child.tagName !== 'IMG') {
                child.remove();
            }
        });

        this.container.classList.add('badge-avatar-pro');
        this.container.classList.add(`border-badge-${this.presetName}`);

        const img = this.container.querySelector('img');
        if (img && !img.classList.contains('badge-img')) {
            img.classList.add('badge-img');
        }

        // Add the common border-fx layer used by CSS
        const borderFx = document.createElement('div');
        borderFx.classList.add('border-fx');
        this.container.appendChild(borderFx);

        this.borderFx = borderFx;

        // Apply specific JS effects based on tier
        switch (this.presetName) {
            case 'bronze':
            case 'silver':
                // Handled purely by CSS
                break;
            case 'gold':
                this.initGoldCanvas();
                break;
            case 'platinum':
                this.initPlatinumSVG();
                break;
            case 'diamond':
                this.initDiamondFlares();
                break;
            case 'mythic':
                this.initMythicEnergy();
                break;
            default:
                break;
        }
    }

    /* ------------------------------------------------------------------------
       GOLD: Canvas Particles strictly bound to border
       ------------------------------------------------------------------------ */
    initGoldCanvas() {
        const canvas = document.createElement('canvas');
        canvas.classList.add('spark-layer');
        this.container.appendChild(canvas);
        const ctx = canvas.getContext("2d");

        const resize = () => {
            canvas.width = canvas.offsetWidth || 120;
            canvas.height = canvas.offsetHeight || 120;
        };

        // Wait a small tick for CSS to apply dimensions
        setTimeout(() => {
            resize();

            const cx = canvas.width / 2;
            const cy = canvas.height / 2;
            const radius = (canvas.width / 2) - 5; // Spawn on inner border edge

            let particles = [];
            for (let i = 0; i < 20; i++) {
                particles.push(this.createGoldParticle(cx, cy, radius));
            }

            const observer = new MutationObserver(() => {
                if (!document.body.contains(canvas)) {
                    this.destroyCanvas = true;
                    observer.disconnect();
                }
            });
            observer.observe(document.body, { childList: true, subtree: true });

            const draw = () => {
                if (this.destroyCanvas) return;
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                particles.forEach((p, index) => {
                    // Update position
                    p.x += p.vx;
                    p.y -= p.vy; // float upwards
                    p.life -= 0.015;

                    if (p.life <= 0) {
                        particles[index] = this.createGoldParticle(cx, cy, radius);
                    } else {
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                        ctx.fillStyle = `rgba(255, 215, 0, ${p.life})`;
                        ctx.fill();
                    }
                });

                requestAnimationFrame(draw);
            };
            draw();
        }, 50);

        window.addEventListener("resize", resize);
    }

    createGoldParticle(cx, cy, radius) {
        // Spawn randomly around the circumference
        const angle = Math.random() * Math.PI * 2;
        // Jitter slightly
        const r = radius + (Math.random() * 4 - 2);
        return {
            x: cx + Math.cos(angle) * r,
            y: cy + Math.sin(angle) * r,
            size: Math.random() * 1.5 + 0.5,
            vx: (Math.random() - 0.5) * 0.2, // slow drift
            vy: Math.random() * 0.5 + 0.2,   // drift up
            life: Math.random() * 0.5 + 0.5
        };
    }

    /* ------------------------------------------------------------------------
       PLATINUM: SVG Stroke sequential light activation
       ------------------------------------------------------------------------ */
    initPlatinumSVG() {
        const svgNS = "http://www.w3.org/2000/svg";
        const svg = document.createElementNS(svgNS, "svg");
        svg.classList.add('svg-layer');
        svg.setAttribute('viewBox', '0 0 100 100');

        // We create 3 rings that rotate at different speeds with different segments
        const createRing = (r, strokeW, dash, speed, direction = 1, color = "rgba(229, 228, 226, 0.8)") => {
            const circle = document.createElementNS(svgNS, "circle");
            circle.setAttribute("cx", "50");
            circle.setAttribute("cy", "50");
            circle.setAttribute("r", r);
            circle.setAttribute("fill", "none");
            circle.setAttribute("stroke", color);
            circle.setAttribute("stroke-width", strokeW);
            circle.setAttribute("stroke-dasharray", dash);
            circle.setAttribute("stroke-linecap", "round");

            circle.style.transformOrigin = "50px 50px";

            // Generate unique animation name for inline style
            const animName = `plat-spin-${Math.floor(Math.random() * 10000)}`;
            const dir = direction > 0 ? '360deg' : '-360deg';

            const style = document.createElement('style');
            style.innerHTML = `@keyframes ${animName} { 100% { transform: rotate(${dir}); } }`;
            document.head.appendChild(style);

            circle.style.animation = `${animName} ${speed}s linear infinite`;
            return circle;
        };

        // Inner solid-ish ring
        svg.appendChild(createRing(48, 1, "5 10", 20, 1));

        // Mid segmented neural-like ring
        svg.appendChild(createRing(50, 1.5, "15 5 1 5 1 10", 12, -1, "#fff"));

        // Outer slow tracking ring (glowy)
        const outer = createRing(52, 0.5, "50 150", 8, 1, "#b9f2ff");
        outer.style.filter = "drop-shadow(0 0 2px #b9f2ff)";
        svg.appendChild(outer);

        this.container.appendChild(svg);
    }

    /* ------------------------------------------------------------------------
       DIAMOND: JS Random Flare Spawns
       ------------------------------------------------------------------------ */
    initDiamondFlares() {
        const createFlare = () => {
            if (!document.body.contains(this.container)) return; // stop if removed

            const flare = document.createElement('div');
            flare.classList.add('diamond-flare');

            // Randomly position on the circumference of a circle
            const angle = Math.random() * Math.PI * 2;
            const radius = 50; // percentage

            const x = 50 + Math.cos(angle) * radius;
            const y = 50 + Math.sin(angle) * radius;

            // Set dynamic vars
            flare.style.left = `${x}%`;
            flare.style.top = `${y}%`;

            // Point towards center mostly, maybe some random tilt
            const rot = (angle * 180 / Math.PI) + 90 + (Math.random() * 20 - 10);
            flare.style.setProperty('--rot', `${rot}deg`);

            const duration = Math.random() * 1 + 0.5; // 0.5 to 1.5s
            flare.style.animation = `flare-blink ${duration}s ease-in-out forwards`;

            this.borderFx.appendChild(flare);

            // Cleanup
            setTimeout(() => {
                if (flare.parentNode) flare.remove();
            }, duration * 1000);

            // Call next flare
            setTimeout(createFlare, Math.random() * 400 + 100);
        };

        // Start 3 concurrent flare generators
        createFlare();
        setTimeout(createFlare, 200);
        setTimeout(createFlare, 400);
    }

    /* ------------------------------------------------------------------------
       MYTHIC: SVG Flowing Energy + Harmonic Pulse
       ------------------------------------------------------------------------ */
    initMythicEnergy() {
        // We use an SVG to draw intricate geometrical overlapping rings 
        // that pulse and rotate smoothly, mimicking a quantum core.

        const svgNS = "http://www.w3.org/2000/svg";
        const svg = document.createElementNS(svgNS, "svg");
        svg.classList.add('svg-layer');
        svg.setAttribute('viewBox', '0 0 100 100');
        svg.style.position = 'absolute';
        svg.style.inset = '-8px';
        svg.style.width = 'calc(100% + 16px)';
        svg.style.height = 'calc(100% + 16px)';
        svg.style.zIndex = '6';
        svg.style.pointerEvents = 'none';

        const createEnergyRing = (r, strokeW, dash, speed, direction, opacity, isDotted) => {
            const circle = document.createElementNS(svgNS, "circle");
            circle.setAttribute("cx", "50");
            circle.setAttribute("cy", "50");
            circle.setAttribute("r", r);
            circle.setAttribute("fill", "none");

            if (isDotted) {
                circle.setAttribute("stroke", `rgba(0, 255, 200, ${opacity})`);
                circle.setAttribute("stroke-dasharray", dash);
                circle.setAttribute("stroke-linecap", "round");
            } else {
                circle.setAttribute("stroke", `rgba(138, 43, 226, ${opacity})`);
                circle.setAttribute("stroke-dasharray", dash);
            }
            circle.setAttribute("stroke-width", strokeW);

            circle.style.transformOrigin = "50px 50px";

            const animName = `mythic-spin-${Math.floor(Math.random() * 10000)}`;
            const dir = direction > 0 ? '360deg' : '-360deg';

            const style = document.createElement('style');
            style.innerHTML = `@keyframes ${animName} { 100% { transform: rotate(${dir}); } }`;
            document.head.appendChild(style);

            circle.style.animation = `${animName} ${speed}s linear infinite`;
            return circle;
        };

        // Layered harmonic rings
        svg.appendChild(createEnergyRing(46, 0.5, "1 3", 15, -1, 0.8, true));
        svg.appendChild(createEnergyRing(49, 1.5, "20 5 10 5", 20, 1, 0.6, false));
        svg.appendChild(createEnergyRing(51, 1, "50 100", 10, -1, 0.8, false));

        // Add a pulsing glow
        const glow = createEnergyRing(50, 2, "full", 0, 1, 0.3, false);
        glow.setAttribute("stroke-dasharray", "none");
        glow.style.animation = "mythic-pulse 3s ease-in-out infinite alternate";

        const pulseStyle = document.createElement('style');
        pulseStyle.innerHTML = `@keyframes mythic-pulse { 0% { opacity: 0.3; stroke-width: 2; } 100% { opacity: 0.8; stroke-width: 4; stroke: rgba(0,255,200,0.5); } }`;
        document.head.appendChild(pulseStyle);

        svg.appendChild(glow);

        this.container.appendChild(svg);
    }

    // Static helper to initialize all badges with 'data-badge-fx'
    static initializeAll() {
        document.querySelectorAll('[data-badge-fx]').forEach(el => {
            if (!el.dataset.badgeFxInitialized) {
                new BadgeFX(el, el.getAttribute('data-badge-fx'));
                el.dataset.badgeFxInitialized = "true";
            }
        });
    }
}

window.BadgeFX = BadgeFX;

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        BadgeFX.initializeAll();
    }, 100);
});

const dynObserver = new MutationObserver((mutationsList) => {
    for (let mutation of mutationsList) {
        if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === 1) {
                    if (node.hasAttribute('data-badge-fx') && !node.dataset.badgeFxInitialized) {
                        new BadgeFX(node, node.getAttribute('data-badge-fx'));
                        node.dataset.badgeFxInitialized = "true";
                    }
                    node.querySelectorAll('[data-badge-fx]').forEach(el => {
                        if (!el.dataset.badgeFxInitialized) {
                            new BadgeFX(el, el.getAttribute('data-badge-fx'));
                            el.dataset.badgeFxInitialized = "true";
                        }
                    });
                }
            });
        }
    }
});
dynObserver.observe(document.body, { childList: true, subtree: true });
