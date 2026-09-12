import {
    clamp,
    easeOutCubic,
    mapRange,
    prefersReducedMotion,
    centerOutOrder,
} from './motion-utils';

/**
 * Global progress windows. Overlap neighbors lightly — do not stack starts.
 * Wall-clock (linear master, ~1600ms): bg 0 → header ~130ms → story ~350ms →
 * title ~780ms → icons ~1150ms → settle 1600ms.
 *
 * @type {Record<string, [number, number]>}
 */
export const HERO_RANGES = {
    background: [0.0, 0.5],
    header: [0.08, 0.36],
    story: [0.22, 0.58],
    title: [0.48, 0.78],
    icons: [0.72, 1.0],
};

const DURATION_MS = 1600;

/**
 * @param {number} t
 * @param {number} a
 * @param {number} b
 */
const mix = (t, a, b) => a + (b - a) * t;

/**
 * @param {number} t local 0–1
 * @param {Array<[number, number]>} stops [at, value]
 */
const sampleStops = (t, stops) => {
    if (t <= stops[0][0]) {
        return stops[0][1];
    }

    for (let i = 1; i < stops.length; i += 1) {
        const [at, value] = stops[i];
        const [prevAt, prevValue] = stops[i - 1];

        if (t <= at) {
            const local = (t - prevAt) / (at - prevAt);

            return mix(easeOutCubic(local), prevValue, value);
        }
    }

    return stops[stops.length - 1][1];
};

/**
 * Stagger start within a local 0–1 range.
 *
 * @param {number} rank
 * @param {number} count
 * @param {number} [spread=0.55]
 */
const staggerT = (rank, count, spread = 0.55) => {
    if (count <= 1) {
        return 0;
    }

    return (rank / (count - 1)) * spread;
};

/**
 * Short local window so items don't all ease across the full module range.
 *
 * @param {number} local
 * @param {number} start
 * @param {number} window
 */
const windowed = (local, start, window) => mapRange(local, start, start + window);

/**
 * @param {HTMLElement} root
 * @param {number} local
 */
const applyStory = (root, local) => {
    const frame = root.querySelector('[data-hero-story]');

    if (! frame) {
        return;
    }

    if (local <= 0) {
        frame.style.clipPath = 'inset(50% 50% 50% 50% round 50%)';
        frame.style.opacity = '0';

        return;
    }

    if (local >= 1) {
        frame.style.clipPath = 'inset(0 round 2rem)';
        frame.style.opacity = '1';

        return;
    }

    const w = frame.offsetWidth || 1;
    const h = frame.offsetHeight || 1;
    const minSide = Math.min(w, h);
    const finalRadius = 32;

    // Hold each shape longer so morph reads as a chain, not a pop.
    const sizeT = sampleStops(local, [
        [0, 0.02],
        [0.18, 0.14],
        [0.4, 0.48],
        [0.62, 0.78],
        [0.88, 1],
        [1, 1],
    ]);

    let boxW;
    let boxH;
    let radius;

    if (local < 0.42) {
        const side = minSide * sizeT;
        boxW = side;
        boxH = side;
        radius = side / 2;
    } else if (local < 0.72) {
        const morph = mapRange(local, 0.42, 0.72);
        const side = minSide * Math.max(sizeT, 0.55);
        boxW = mix(easeOutCubic(morph), side, w * Math.max(sizeT, 0.85));
        boxH = mix(easeOutCubic(morph), side, h * Math.max(sizeT, 0.85));
        radius = mix(
            easeOutCubic(morph),
            side / 2,
            Math.max(finalRadius, Math.min(boxW, boxH) * 0.22),
        );
    } else {
        const morph = mapRange(local, 0.72, 1);
        boxW = mix(easeOutCubic(morph), w * 0.92, w);
        boxH = mix(easeOutCubic(morph), h * 0.92, h);
        radius = mix(easeOutCubic(morph), Math.max(finalRadius, minSide * 0.18), finalRadius);
    }

    const insetX = Math.max(0, (w - boxW) / 2);
    const insetY = Math.max(0, (h - boxH) / 2);
    const opacity = sampleStops(local, [
        [0, 0],
        [0.12, 1],
        [1, 1],
    ]);

    frame.style.clipPath = `inset(${insetY}px ${insetX}px ${insetY}px ${insetX}px round ${radius}px)`;
    frame.style.opacity = String(opacity);
};

/**
 * @param {HTMLElement} root
 * @param {number} local
 */
const applyTitle = (root, local) => {
    const chars = Array.from(root.querySelectorAll('[data-hero-char]'));

    if (chars.length === 0) {
        return;
    }

    const order = centerOutOrder(chars.length);
    // Each glyph flips in a short window; later ranks wait — readable center-out chain.
    const charWindow = 0.28;
    const spread = Math.min(0.7, 1 - charWindow);

    chars.forEach((char, index) => {
        const rank = order.indexOf(index);
        const start = staggerT(rank < 0 ? index : rank, chars.length, spread);
        const t = windowed(local, start, charWindow);
        const eased = easeOutCubic(t);

        char.style.opacity = String(eased);
        char.style.transform = `perspective(420px) rotateY(${mix(eased, 90, 0)}deg)`;
    });
};

/**
 * @param {HTMLElement} root
 * @param {number} local
 */
const applyIcons = (root, local) => {
    const icons = Array.from(root.querySelectorAll('[data-hero-icon]'));

    const iconWindow = 0.4;
    const spread = Math.min(0.55, 1 - iconWindow);

    icons.forEach((icon, index) => {
        const start = staggerT(index, Math.max(icons.length, 1), spread);
        const t = windowed(local, start, iconWindow);
        const scale = sampleStops(t, [
            [0, 0.5],
            [0.65, 1.05],
            [1, 1],
        ]);
        const opacity = sampleStops(t, [
            [0, 0],
            [0.3, 1],
            [1, 1],
        ]);

        icon.style.opacity = String(opacity);
        icon.style.transform = `scale(${scale})`;

        const glyph = icon.querySelector('[data-hero-icon-glyph]');

        if (glyph) {
            // Glyph after the circle has mostly landed.
            const glyphT = mapRange(t, 0.55, 1);

            glyph.style.opacity = String(easeOutCubic(glyphT));
            glyph.style.transform = `scale(${mix(easeOutCubic(glyphT), 0.85, 1)})`;
        }
    });
};

/**
 * @param {HTMLElement} root
 * @param {{ setProgress: (n: number) => void, finish: () => void } | null} header
 * @param {{ play: () => void, finish: () => void } | null} bg
 * @param {number} progress
 */
const applyAll = (root, header, bg, progress) => {
    const p = clamp(progress);

    root.style.setProperty('--hero-progress', String(p));

    const [bgStart] = HERO_RANGES.background;

    if (bg && p > bgStart) {
        bg.play();
    }

    if (header) {
        header.setProgress(mapRange(p, ...HERO_RANGES.header));
    }

    applyStory(root, mapRange(p, ...HERO_RANGES.story));
    applyTitle(root, mapRange(p, ...HERO_RANGES.title));
    applyIcons(root, mapRange(p, ...HERO_RANGES.icons));
};

/**
 * @param {ParentNode} [root=document]
 */
export const initHeroEntrance = (root = document) => {
    const el = root.querySelector('[data-hero-entrance]');

    if (! el || el.dataset.heroEntranceReady === 'true') {
        return el?.heroEntrance ?? null;
    }

    el.dataset.heroEntranceReady = 'true';

    let progress = 0;
    /** @type {number | null} */
    let raf = null;
    let playing = false;
    /** @type {{ play: () => void, finish: () => void, whenReady?: () => Promise<void> } | null} */
    let bg = null;
    /** @type {{ setProgress: (n: number) => void, finish: () => void } | null} */
    let header = null;

    const setProgress = (next) => {
        progress = clamp(next);
        applyAll(el, header, bg, progress);

        if (progress >= 1) {
            el.dataset.state = 'done';
            playing = false;
        } else if (progress > 0) {
            el.dataset.state = 'playing';
        }
    };

    const finish = () => {
        if (raf !== null) {
            cancelAnimationFrame(raf);
            raf = null;
        }

        playing = false;
        setProgress(1);
        bg?.finish();
        header?.finish();
        el.dataset.state = 'done';
    };

    const api = {
        setProgress,
        finish,
        getProgress: () => progress,
        /**
         * @param {{ play: () => void, finish: () => void, whenReady?: () => Promise<void> } | null} nextBg
         * @param {{ setProgress: (n: number) => void, finish: () => void } | null} nextHeader
         */
        bind(nextBg, nextHeader) {
            bg = nextBg;
            header = nextHeader;
        },
        play() {
            if (prefersReducedMotion()) {
                finish();

                return;
            }

            if (playing || progress >= 1) {
                return;
            }

            playing = true;
            el.dataset.state = 'playing';
            const start = performance.now();

            const tick = (now) => {
                // Linear master progress keeps module delays readable.
                // Easing lives inside each module, not on the global clock.
                const t = clamp((now - start) / DURATION_MS);
                setProgress(t);

                if (t < 1) {
                    raf = requestAnimationFrame(tick);
                } else {
                    raf = null;
                    playing = false;
                    el.dataset.state = 'done';
                    header?.finish();
                }
            };

            raf = requestAnimationFrame(tick);
        },
    };

    el.heroEntrance = api;

    return api;
};

/**
 * @param {ParentNode} [root=document]
 * @param {{
 *   bg?: { play: () => void, finish: () => void, whenReady?: () => Promise<void> } | null,
 *   header?: { setProgress: (n: number) => void, finish: () => void } | null,
 * }} [deps]
 */
export const startHeroEntrance = async (root = document, deps = {}) => {
    const api = initHeroEntrance(root);

    if (! api) {
        return null;
    }

    api.bind(deps.bg ?? null, deps.header ?? null);

    if (prefersReducedMotion()) {
        api.finish();

        return api;
    }

    if (deps.bg?.whenReady) {
        await deps.bg.whenReady();
    }

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            api.play();
        });
    });

    return api;
};
