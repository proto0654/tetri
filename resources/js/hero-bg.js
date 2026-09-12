const REDUCED_MOTION = '(prefers-reduced-motion: reduce)';
const LOAD_FALLBACK_MS = 100;

/**
 * Standalone Hero background entrance controller.
 * When autoPlay is false, the shared hero entrance calls play().
 * --hero-bg-progress reserved for future scrub.
 *
 * @param {ParentNode} [root=document]
 * @param {{ autoPlay?: boolean }} [options]
 * @returns {{ play: () => void, finish: () => void, getState: () => string, whenReady: () => Promise<void> } | null}
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
    let remaining = bands.length;
    /** @type {(() => void) | null} */
    let readyResolve = null;
    const readyPromise = new Promise((resolve) => {
        readyResolve = resolve;
    });

    const finish = () => {
        el.dataset.state = 'done';
    };

    const play = () => {
        if (window.matchMedia(REDUCED_MOTION).matches) {
            finish();

            return;
        }

        if (el.dataset.state === 'playing' || el.dataset.state === 'done') {
            return;
        }

        el.dataset.state = 'playing';
    };

    const whenReady = () => readyPromise;

    const api = {
        play,
        finish,
        getState: () => el.dataset.state ?? 'pending',
        whenReady,
    };

    el.heroBg = api;

    if (window.matchMedia(REDUCED_MOTION).matches) {
        finish();
        readyResolve?.();

        return api;
    }

    bands.forEach((band) => {
        band.addEventListener('animationend', (event) => {
            if (event.target !== band || event.animationName !== 'hero-bg-band-out') {
                return;
            }

            remaining -= 1;

            if (remaining <= 0) {
                finish();
            }
        });
    });

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
                play();
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
