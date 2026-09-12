import {
    clamp,
    easeOutCubic,
    mapRange,
    prefersReducedMotion,
} from './motion-utils';

const INNER_DURATION_MS = 520;

/**
 * @typedef {{
 *   setProgress: (local: number) => void,
 *   finish: () => void,
 *   getProgress: () => number,
 *   isExternallyDriven: boolean,
 * }} HeaderEntranceApi
 */

/**
 * @param {number} t
 * @param {number} a
 * @param {number} b
 */
const mix = (t, a, b) => a + (b - a) * t;

/**
 * @param {HTMLElement} el
 * @param {number} local 0–1 within header range
 */
const applyHeader = (el, local) => {
    const variant = el.dataset.headerVariant ?? 'bar';
    const shell = el.querySelector('[data-header-shell]');
    const logo = el.querySelector('[data-header-logo]');
    const navItems = Array.from(el.querySelectorAll('[data-header-nav-item]'));
    const cta = el.querySelector('[data-header-cta]');

    if (shell && variant === 'pill') {
        const shellT = mapRange(local, 0, 0.45);
        const scaleX = mix(easeOutCubic(shellT), 0, 1);

        shell.style.transform = `scaleX(${scaleX})`;
        shell.style.opacity = String(shellT > 0 ? 1 : 0);
    } else if (shell) {
        shell.style.transform = '';
        shell.style.opacity = '';
    }

    // Content waits for shell unfold, then logo → nav → CTA with clearer gaps.
    const contentStart = variant === 'pill' ? 0.38 : 0;
    const contentLocal = mapRange(local, contentStart, 1);

    if (logo) {
        const t = mapRange(contentLocal, 0, 0.32);
        logo.style.opacity = String(easeOutCubic(t));
    }

    navItems.forEach((item, index) => {
        const start = 0.22 + index * 0.12;
        const t = mapRange(contentLocal, start, start + 0.28);
        item.style.opacity = String(easeOutCubic(t));
    });

    if (cta) {
        const t = mapRange(contentLocal, 0.58, 0.92);
        cta.style.opacity = String(easeOutCubic(t));
    }
};

/**
 * @param {ParentNode} [root=document]
 * @param {{ external?: boolean }} [options]
 * @returns {HeaderEntranceApi | null}
 */
export const initHeaderEntrance = (root = document, options = {}) => {
    const el = root.querySelector('[data-header-entrance]');

    if (! el || el.dataset.headerEntranceReady === 'true') {
        return el?.headerEntrance ?? null;
    }

    el.dataset.headerEntranceReady = 'true';

    const hasHero = Boolean(root.querySelector('[data-hero-entrance]'));
    const external = options.external ?? hasHero;

    let progress = 0;
    /** @type {number | null} */
    let raf = null;

    const setProgress = (local) => {
        progress = clamp(local);
        applyHeader(el, progress);

        if (progress >= 1) {
            el.dataset.state = 'done';
        } else if (progress > 0) {
            el.dataset.state = 'playing';
        }
    };

    const finish = () => {
        if (raf !== null) {
            cancelAnimationFrame(raf);
            raf = null;
        }

        setProgress(1);
        el.dataset.state = 'done';
    };

    /** @type {HeaderEntranceApi} */
    const api = {
        setProgress,
        finish,
        getProgress: () => progress,
        isExternallyDriven: external,
    };

    el.headerEntrance = api;

    if (prefersReducedMotion()) {
        finish();

        return api;
    }

    if (! external) {
        el.dataset.state = 'playing';
        const start = performance.now();

        const tick = (now) => {
            const t = clamp((now - start) / INNER_DURATION_MS);
            setProgress(easeOutCubic(t));

            if (t < 1) {
                raf = requestAnimationFrame(tick);
            } else {
                raf = null;
                el.dataset.state = 'done';
            }
        };

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                raf = requestAnimationFrame(tick);
            });
        });
    } else {
        el.dataset.state = 'pending';
        setProgress(0);
    }

    return api;
};
