/**
 * Optimized Virtual List Helper for Cashbook Mobile Performance
 * Fokus: DOM Reuse, Minimal Reflow, & Memory Safety
 */
window.VirtualList = class VirtualList {
    constructor(container, options) {
        this.container = container;
        this.items = options.items || [];
        this.renderItem = options.renderItem;
        this.getItemHeight = options.getItemHeight || (() => 64);
        this.overscan = options.overscan || 5; // Dikurangi sedikit untuk hemat memori mobile

        this.scrollParent = options.scrollParent || window;
        this.cachedOffset = null;
        this.renderedNodes = new Map();

        // Pool untuk menyimpan elemen yang sedang tidak dipakai (DOM Reuse)
        this.nodePool = [];

        this.scrollPad = document.createElement('div');
        this.scrollPad.style.cssText = 'position:relative; width:100%; contain:layout;';
        this.container.appendChild(this.scrollPad);

        this.calculatePositions();

        this.onScroll = this.throttle(() => {
            requestAnimationFrame(() => this._updateVisibleItems());
        }, 16); // Target ~60fps

        this.scrollParent.addEventListener('scroll', this.onScroll, { passive: true });

        this.onResize = () => { this.cachedOffset = null; this.onScroll(); };
        window.addEventListener('resize', this.onResize, { passive: true });

        this._updateVisibleItems();
    }

    calculatePositions() {
        let currentY = 0;
        this.itemPositions = this.items.map(item => {
            const h = this.getItemHeight(item);
            const pos = { y: currentY, h: h };
            currentY += h;
            return pos;
        });
        this.scrollPad.style.height = `${currentY}px`;
    }

    // Mendapatkan elemen dari pool atau buat baru jika kosong
    _getElement() {
        if (this.nodePool.length > 0) return this.nodePool.pop();
        const el = document.createElement('div');
        el.style.cssText = 'position:absolute; width:100%; will-change:transform;';
        return el;
    }

    _updateVisibleItems() {
        if (!this.container || !this.container.isConnected) return;

        const scrollTop = this.scrollParent === window ? window.scrollY : this.scrollParent.scrollTop;
        const viewportHeight = this.scrollParent === window ? window.innerHeight : this.scrollParent.clientHeight;

        if (this.cachedOffset === null) {
            this.cachedOffset = this.container.getBoundingClientRect().top + scrollTop;
        }

        const visibleTop = Math.max(0, scrollTop - this.cachedOffset);
        const visibleBottom = visibleTop + viewportHeight;

        let startIndex = this._findIndexForPosition(visibleTop);
        startIndex = Math.max(0, startIndex - this.overscan);

        let endIndex = startIndex;
        const maxCheck = visibleBottom + (this.overscan * 64);
        while (endIndex < this.items.length && this.itemPositions[endIndex].y < maxCheck) {
            endIndex++;
        }
        endIndex = Math.min(this.items.length, endIndex + this.overscan);

        const activeKeys = new Set();

        // Render/Update items
        for (let i = startIndex; i < endIndex; i++) {
            activeKeys.add(i);
            if (!this.renderedNodes.has(i)) {
                const node = this._getElement();
                node.innerHTML = this.renderItem(this.items[i], i);
                node.style.transform = `translateY(${this.itemPositions[i].y}px)`;

                this.scrollPad.appendChild(node);
                this.renderedNodes.set(i, node);
            }
        }

        // Cleanup: Masukkan elemen yang tidak terlihat kembali ke Pool
        for (const [i, node] of this.renderedNodes.entries()) {
            if (!activeKeys.has(i)) {
                this.scrollPad.removeChild(node);
                this.nodePool.push(node);
                this.renderedNodes.delete(i);
            }
        }
    }

    _findIndexForPosition(yPos) {
        let low = 0, high = this.itemPositions.length - 1;
        while (low <= high) {
            const mid = (low + high) >> 1;
            if (this.itemPositions[mid].y <= yPos) low = mid + 1;
            else high = mid - 1;
        }
        return Math.max(0, low - 1);
    }

    throttle(fn, ms) {
        let timeout;
        return () => {
            if (timeout) return;
            timeout = setTimeout(() => { fn(); timeout = null; }, ms);
        };
    }

    destroy() {
        this.scrollParent.removeEventListener('scroll', this.onScroll);
        window.removeEventListener('resize', this.onResize);
        if (this.container) this.container.innerHTML = '';
        this.renderedNodes.clear();
        this.nodePool = [];
    }
};
