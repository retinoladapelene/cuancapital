/**
 * Lightweight Virtual List Helper for Cashbook Mobile Performance
 * Only renders items that are currently visible in the scroll viewport.
 */
window.VirtualList = class VirtualList {
    constructor(container, options) {
        this.container = container;
        this.items = options.items || [];
        this.renderItem = options.renderItem;
        this.getItemHeight = options.getItemHeight || (() => 64);
        this.overscan = options.overscan || 8; // Render 8 items above/below viewport

        // Cache offset to prevent Layout Thrashing during scroll!
        this.cachedOffset = null;

        // Create the scroll pad that dictates the total height
        this.scrollPad = document.createElement('div');
        this.scrollPad.style.position = 'relative';
        this.scrollPad.style.width = '100%';
        this.scrollPad.style.willChange = 'transform'; // Promote to GPU layer
        this.scrollPad.style.transform = 'translateZ(0)';
        this.container.appendChild(this.scrollPad);

        this.itemPositions = [];
        let currentY = 0;
        for (let i = 0; i < this.items.length; i++) {
            const h = this.getItemHeight(this.items[i]);
            this.itemPositions.push({ y: currentY, h: h });
            currentY += h;
        }
        this.scrollPad.style.height = `${currentY}px`;

        this.renderedNodes = new Map();

        // The page scrolls, so window is the scroll parent
        this.scrollParent = options.scrollParent || window;

        this.isScrolling = false;
        this.onScroll = () => {
            if (!this.isScrolling) {
                this.isScrolling = true;
                requestAnimationFrame(this._onScroll.bind(this));
            }
        };

        this.onResize = () => {
            this.cachedOffset = null; // Invalidate cache on resize
            this.onScroll();
        };

        this.scrollParent.addEventListener('scroll', this.onScroll, { passive: true });
        this.scrollParent.addEventListener('resize', this.onResize, { passive: true });

        // Initial render
        this.onScroll();
    }

    // Fast O(log N) binary search for starting index
    _findIndexForPosition(yPos) {
        let low = 0;
        let high = this.itemPositions.length - 1;
        while (low <= high) {
            const mid = Math.floor((low + high) / 2);
            const pos = this.itemPositions[mid];
            if (yPos >= pos.y && yPos < pos.y + pos.h) {
                return mid;
            } else if (yPos < pos.y) {
                high = mid - 1;
            } else {
                low = mid + 1;
            }
        }
        return low;
    }

    _onScroll() {
        if (!this.container || !this.container.isConnected) {
            this.destroy();
            return;
        }

        const scrollTop = this.scrollParent === window ? window.scrollY : this.scrollParent.scrollTop;
        const viewportHeight = this.scrollParent === window ? window.innerHeight : this.scrollParent.clientHeight;

        // Cache the offset to eliminate GET BOUNDING CLIENT RECT reflow!
        if (this.cachedOffset === null && this.scrollParent === window) {
            const rect = this.container.getBoundingClientRect();
            this.cachedOffset = scrollTop + rect.top;
        } else if (this.scrollParent !== window) {
            this.cachedOffset = 0;
        }

        const visibleTop = Math.max(0, scrollTop - this.cachedOffset);
        const visibleBottom = visibleTop + viewportHeight;

        // O(log N) search instead of O(N) loop
        let startIndex = this._findIndexForPosition(visibleTop);
        startIndex = Math.max(0, startIndex - this.overscan);

        let endIndex = startIndex;
        while (endIndex < this.itemPositions.length && this.itemPositions[endIndex].y < visibleBottom) {
            endIndex++;
        }
        endIndex = Math.min(this.items.length, endIndex + this.overscan);

        const newKeys = new Set();
        const fragment = document.createDocumentFragment();

        for (let i = startIndex; i < endIndex; i++) {
            newKeys.add(i);
            if (!this.renderedNodes.has(i)) {
                let wrapper;
                // DOM Object Pooling: Reuse old nodes if available
                if (this.nodePool.length > 0) {
                    wrapper = this.nodePool.pop();
                } else {
                    wrapper = document.createElement('div');
                    wrapper.style.position = 'absolute';
                    wrapper.style.width = '100%';
                    // REMOVED will-change: transform per item to save massive amounts of mobile GPU VRAM
                }

                const nodeStr = this.renderItem(this.items[i], i);
                wrapper.innerHTML = nodeStr.trim();
                wrapper.style.transform = `translateY(${this.itemPositions[i].y}px) translateZ(0)`;

                fragment.appendChild(wrapper);
                this.renderedNodes.set(i, wrapper);
            }
        }

        if (fragment.children.length > 0) {
            this.scrollPad.appendChild(fragment);
        }

        // Cleanup out-of-bounds nodes & recycle them
        for (const [i, el] of this.renderedNodes.entries()) {
            if (!newKeys.has(i)) {
                if (el.parentNode) el.parentNode.removeChild(el);
                this.nodePool.push(el); // Send back to the pool
                this.renderedNodes.delete(i);
            }
        }

        this.isScrolling = false;
    }

    destroy() {
        this.scrollParent.removeEventListener('scroll', this.onScroll);
        this.scrollParent.removeEventListener('resize', this.onResize);
        this.container.innerHTML = '';
        this.renderedNodes.clear();
        this.nodePool = [];
    }
};
