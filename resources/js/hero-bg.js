import { clamp, easeOutCubic, mapRange, prefersReducedMotion } from './motion-utils';

const LOAD_FALLBACK_MS = 100;
const BAND_BASE_DELAY_MS = 38;

/**
 * Standalone Hero background controller.
 * Progress 0 = cream bands cover the image; 1 = fully revealed.
 * Driven by hero entrance (load + scroll scrub).
 *
 * Band order is always top→bottom lift (band 0 clears first). On scroll reverse
 * that means covering starts from the bottom — keep it. Load uses easeOutCubic
 * (snappy clear); scroll scrub uses linear opacity so covering reads immediately
 * (easeOutCubic stays ~0 until late in each band window).
 *
 * @param {ParentNode} [root=document]
 * @param {{ autoPlay?: boolean }} [options]
 * @returns {{
 *   setProgress: (n: number, opts?: { scrollScrub?: boolean }) => void,
 *   play: () => void,
 *   finish: () => void,
 *   getState: () => string,
 *   getProgress: () => number,
 *   whenReady: () => Promise<void>,
 * } | null}
 */
export const initHeroBg = (root = document, options = {}) => {
    const { autoPlay = true } = options;
    const el = root.querySelector('[data-hero-bg]');

    if (! el || el.dataset.heroBgReady === 'true') {
        return el?.heroBg ?? null;
    }

    el.dataset.heroBgReady = 'true';

    const bands = Array.from(el.querySelectorAll('.hero-bg__band'));
    const image = el.querySelector('.hero-bg__image');
    let progress = 0;
    /** @type {(() => void) | null} */
    let readyResolve = null;
    const readyPromise = new Promise((resolve) => {
        readyResolve = resolve;
    });

    const bandTimings = bands.map((band, index) => {
        const styles = getComputedStyle(band);
        const jitter = Number.parseFloat(styles.getPropertyValue('--jitter')) || 0;
        const durSec = Number.parseFloat(styles.getPropertyValue('--dur')) || 0.7;
        const delayMs = index * BAND_BASE_DELAY_MS + jitter;
        const durMs = Math.max(durSec, 0.1) * 1000;

        return { delayMs, durMs };
    });

    const timelineMs = bandTimings.reduce(
        (max, { delayMs, durMs }) => Math.max(max, delayMs + durMs),
        1,
    );

    /**
     * @param {number} next
     * @param {{ scrollScrub?: boolean }} [opts]
     */
    const setProgress = (next, opts = {}) => {
        const { scrollScrub = false } = opts;
        progress = clamp(next);
        el.style.setProperty('--hero-bg-progress', String(progress));

        if (prefersReducedMotion()) {
            bands.forEach((band) => {
                band.style.opacity = '0';
            });
            el.dataset.state = 'done';

            return;
        }

        bands.forEach((band, index) => {
            const { delayMs, durMs } = bandTimings[index];
            const start = delayMs / timelineMs;
            const end = (delayMs + durMs) / timelineMs;
            const t = mapRange(progress, start, end);
            // Load: easeOut clears fast at the start of each window.
            // Scroll: linear so opacity rises as soon as progress drops below end.
            const opacity = scrollScrub ? 1 - t : 1 - easeOutCubic(t);

            band.style.opacity = String(opacity);
        });

        if (progress >= 1) {
            el.dataset.state = 'done';
        } else if (progress > 0) {
            el.dataset.state = 'playing';
        } else {
            el.dataset.state = 'pending';
        }
    };

    const finish = () => {
        setProgress(1);
    };

    const play = () => {
        finish();
    };

    const whenReady = () => readyPromise;

    const api = {
        setProgress,
        play,
        finish,
        getState: () => el.dataset.state ?? 'pending',
        getProgress: () => progress,
        whenReady,
    };

    el.heroBg = api;

    if (prefersReducedMotion()) {
        finish();
        readyResolve?.();

        return api;
    }

    setProgress(0);

    const markReady = () => {
        readyResolve?.();
    };

    const startWhenReady = () => {
        markReady();

        if (! autoPlay) {
            return;
        }

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                finish();
            });
        });
    };

    if (! image || image.complete) {
        startWhenReady();
    } else {
        let started = false;

        const startOnce = () => {
            if (started) {
                return;
            }

            started = true;
            startWhenReady();
        };

        image.addEventListener('load', startOnce, { once: true });
        image.addEventListener('error', startOnce, { once: true });
        window.setTimeout(startOnce, LOAD_FALLBACK_MS);
    }

    return api;
};
