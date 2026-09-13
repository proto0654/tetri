import {
    clamp,
    easeOutCubic,
    mapRange,
    prefersReducedMotion,
} from './motion-utils';

/**
 * Global progress windows. Max 2 concurrent modules; never overlap story
 * (clip-path) with title (3D glyphs). Neighbors may hand off lightly.
 * Wall-clock (linear master, ~2100ms): bg 0 → header ~168ms → story ~588ms →
 * title ~1050ms → icons ~1386ms → subtitle ~1680ms → settle 2100ms.
 *
 * @type {Record<string, [number, number]>}
 */
export const HERO_RANGES = {
    background: [0.0, 0.3],
    header: [0.08, 0.26],
    story: [0.28, 0.5],
    title: [0.5, 0.68],
    icons: [0.66, 0.82],
    subtitle: [0.8, 1.0],
};

const DURATION_MS = 2100;
const TITLE_ICON_START_VH = 0.2;
/** Story mask hold: start collapse later than title/icons (smaller vh → later). */
const STORY_MASK_START_VH = 0.08;
/** Icons fully gone once their top reaches mid-viewport. */
const ICONS_SCROLL_END_VH = 0.5;
/** Subtitle scroll: fully gone at 20vh (desktop) / 50vh (mobile), fade from rest. */
const SUBTITLE_DESKTOP_START_VH = 0.2;
const SUBTITLE_MOBILE_END_VH = 0.5;
const LG_QUERY = '(min-width: 1024px)';
const SCROLL_TOP_SNAP_PX = 1;
const FULL_LOCAL = 0.999;

/** @type {WeakMap<HTMLElement, { w: number, h: number }>} */
const storyBoxCache = new WeakMap();

/**
 * @param {HTMLElement} frame
 * @param {{ force?: boolean }} [options]
 * @returns {{ w: number, h: number }}
 */
const getStoryBox = (frame, options = {}) => {
    const { force = false } = options;

    if (! force) {
        const cached = storyBoxCache.get(frame);

        if (cached) {
            return cached;
        }
    }

    const box = {
        w: frame.offsetWidth || 1,
        h: frame.offsetHeight || 1,
    };

    storyBoxCache.set(frame, box);

    return box;
};

/**
 * @param {HTMLElement | null} frame
 */
const invalidateStoryBox = (frame) => {
    if (frame) {
        storyBoxCache.delete(frame);
    }
};

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
 * @param {HTMLElement} frame
 * @param {number} local
 */
const syncStoryVideo = (frame, local) => {
    const video = frame.querySelector('video');

    if (! video) {
        return;
    }

    if (local >= FULL_LOCAL) {
        if (video.paused) {
            const playResult = video.play();

            if (playResult?.catch) {
                playResult.catch(() => {});
            }
        }

        return;
    }

    if (! video.paused) {
        video.pause();
    }
};

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
        syncStoryVideo(frame, 0);

        return;
    }

    if (local >= FULL_LOCAL) {
        // Drop clip-path when fully open — Chromium paints a 1px dark fringe
        // along inset(... round ...); overflow + border-radius already clip.
        frame.style.clipPath = 'none';
        frame.style.opacity = '1';
        syncStoryVideo(frame, 1);

        return;
    }

    const { w, h } = getStoryBox(frame);
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
    syncStoryVideo(frame, local);
};

/**
 * Left-to-right glyph flip.
 *
 * @param {HTMLElement} root
 * @param {number} local
 */
const applyTitle = (root, local) => {
    const chars = Array.from(root.querySelectorAll('[data-hero-char]'));

    if (chars.length === 0) {
        return;
    }

    const charWindow = 0.28;
    const spread = Math.min(0.7, 1 - charWindow);

    chars.forEach((char, index) => {
        const start = staggerT(index, chars.length, spread);
        const t = windowed(local, start, charWindow);
        const eased = easeOutCubic(t);

        char.style.opacity = String(eased);
        char.style.transform = `perspective(420px) rotateY(${mix(eased, 90, 0)}deg)`;
    });
};

/**
 * Load: per-bullet stagger chain. Scroll scrub: all icons move together.
 *
 * @param {HTMLElement} root
 * @param {number} local
 * @param {{ together?: boolean }} [options]
 */
const applyIcons = (root, local, options = {}) => {
    const { together = false } = options;
    const icons = Array.from(root.querySelectorAll('[data-hero-icon]'));
    const iconWindow = 0.4;
    const spread = together ? 0 : Math.min(0.55, 1 - iconWindow);

    icons.forEach((icon, index) => {
        const start = together ? 0 : staggerT(index, Math.max(icons.length, 1), spread);
        const t = together ? clamp(local) : windowed(local, start, iconWindow);
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
            const glyphT = mapRange(t, 0.55, 1);

            glyph.style.opacity = String(easeOutCubic(glyphT));
            glyph.style.transform = `scale(${mix(easeOutCubic(glyphT), 0.85, 1)})`;
        }
    });
};

/**
 * Long opacity-only fade for desktop + mobile subtitle nodes.
 *
 * @param {HTMLElement} root
 * @param {number} local
 */
const applySubtitle = (root, local) => {
    const nodes = Array.from(root.querySelectorAll('[data-hero-subtitle]'));

    if (nodes.length === 0) {
        return;
    }

    const opacity = easeOutCubic(clamp(local));

    nodes.forEach((node) => {
        node.style.opacity = String(opacity);
    });
};

/**
 * @typedef {{
 *   background: number,
 *   story: number,
 *   title: number,
 *   icons: number,
 *   subtitle: number,
 *   header?: number,
 * }} HeroLocals
 */

/**
 * @param {HTMLElement} root
 * @param {{ setProgress: (n: number) => void, finish: () => void } | null} header
 * @param {{ setProgress?: (n: number, opts?: { scrollScrub?: boolean }) => void, play?: () => void, finish: () => void } | null} bg
 * @param {HeroLocals} locals
 * @param {{ driveHeader?: boolean, scrollScrub?: boolean, lastLocals?: HeroLocals | null }} [options]
 * @returns {HeroLocals}
 */
const applyLocals = (root, header, bg, locals, options = {}) => {
    const { driveHeader = true, scrollScrub = false, lastLocals = null } = options;
    const background = clamp(locals.background);
    const story = clamp(locals.story);
    const title = clamp(locals.title);
    const icons = clamp(locals.icons);
    const subtitle = clamp(locals.subtitle);
    const headerLocal = clamp(locals.header ?? 1);

    /**
     * Skip rewriting styles when a module already settled last frame (and still is).
     *
     * @param {keyof HeroLocals} key
     * @param {number} value
     */
    const isSteady = (key, value) => {
        if (scrollScrub) {
            return false;
        }

        const prev = lastLocals?.[key];

        return value >= FULL_LOCAL && typeof prev === 'number' && prev >= FULL_LOCAL;
    };

    root.style.setProperty(
        '--hero-progress',
        String(Math.min(background, story, title, icons, subtitle)),
    );

    if (! isSteady('background', background) && bg?.setProgress) {
        bg.setProgress(background, { scrollScrub });
    }

    if (driveHeader && header && ! isSteady('header', headerLocal)) {
        header.setProgress(headerLocal);
    }

    if (! isSteady('story', story)) {
        applyStory(root, story);
    }

    if (! isSteady('title', title)) {
        applyTitle(root, title);
    }

    if (! isSteady('icons', icons)) {
        applyIcons(root, icons, { together: scrollScrub });
    }

    if (! isSteady('subtitle', subtitle)) {
        applySubtitle(root, subtitle);
    }

    return {
        background,
        header: headerLocal,
        story,
        title,
        icons,
        subtitle,
    };
};

/**
 * @param {number} progress
 * @returns {HeroLocals}
 */
const localsFromMaster = (progress) => {
    const p = clamp(progress);

    return {
        background: mapRange(p, ...HERO_RANGES.background),
        header: mapRange(p, ...HERO_RANGES.header),
        story: mapRange(p, ...HERO_RANGES.story),
        title: mapRange(p, ...HERO_RANGES.title),
        icons: mapRange(p, ...HERO_RANGES.icons),
        subtitle: mapRange(p, ...HERO_RANGES.subtitle),
    };
};

/**
 * @param {HTMLElement | null} el
 * @returns {number}
 */
const elementTop = (el) => {
    if (! el) {
        return Number.POSITIVE_INFINITY;
    }

    return el.getBoundingClientRect().top;
};

/**
 * @param {HTMLElement | null} el
 * @returns {number}
 */
const elementCenterY = (el) => {
    if (! el) {
        return 0;
    }

    const rect = el.getBoundingClientRect();

    return rect.top + rect.height / 2;
};

/**
 * Title: full while top is below the 20vh line; collapse by the time top hits 0.
 *
 * @param {number} top
 * @returns {number}
 */
const delayedScrollLocal = (top) => {
    const startY = window.innerHeight * TITLE_ICON_START_VH;

    return mapRange(top, 0, startY);
};

/**
 * @returns {boolean}
 */
const isDesktopViewport = () => window.matchMedia(LG_QUERY).matches;

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
    let scrubbing = false;
    /** @type {{ setProgress?: (n: number) => void, play?: () => void, finish: () => void, whenReady?: () => Promise<void> } | null} */
    let bg = null;
    /** @type {{ setProgress: (n: number) => void, finish: () => void } | null} */
    let header = null;
    /** @type {number | null} */
    let scrollRaf = null;
    let scrollDriverEnabled = false;
    /** @type {HeroLocals | null} */
    let lastLocals = null;

    const storyEl = () => el.querySelector('[data-hero-story]');
    const titleEl = () => el.querySelector('[data-hero-title]');
    const iconsEl = () => el.querySelector('[data-hero-icons]');
    const subtitleEl = () => {
        const nodes = Array.from(el.querySelectorAll('[data-hero-subtitle]'));

        return nodes.find((node) => node.offsetParent !== null) ?? nodes[0] ?? null;
    };

    /**
     * Bg: 1 at page top; 0 when story center hits the viewport top.
     * Rest position derived from document geometry so resize/scroll stay in sync.
     *
     * @returns {number}
     */
    const storyScrollLocal = () => {
        const frame = storyEl();

        if (! frame) {
            return 1;
        }

        const scrollY = window.scrollY || window.pageYOffset || 0;

        if (scrollY <= SCROLL_TOP_SNAP_PX) {
            return 1;
        }

        const centerY = elementCenterY(frame);
        const restCenterY = Math.max(centerY + scrollY, 1);

        return mapRange(centerY, 0, restCenterY);
    };

    /**
     * Story mask: hold full until top crosses STORY_MASK_START_VH,
     * then collapse to 0 when center hits the viewport top — shorter window.
     *
     * @returns {number}
     */
    const storyMaskScrollLocal = () => {
        const frame = storyEl();

        if (! frame) {
            return 1;
        }

        const scrollY = window.scrollY || window.pageYOffset || 0;

        if (scrollY <= SCROLL_TOP_SNAP_PX) {
            return 1;
        }

        const rect = frame.getBoundingClientRect();
        const centerY = rect.top + rect.height / 2;
        const holdCenterY = window.innerHeight * STORY_MASK_START_VH + rect.height / 2;

        return mapRange(centerY, 0, Math.max(holdCenterY, 1));
    };

    /**
     * Icons: 1 at page top; 0 when icons top hits mid-viewport (earlier exit).
     *
     * @returns {number}
     */
    const iconsScrollLocal = () => {
        const frame = iconsEl();

        if (! frame) {
            return 1;
        }

        const scrollY = window.scrollY || window.pageYOffset || 0;

        if (scrollY <= SCROLL_TOP_SNAP_PX) {
            return 1;
        }

        const top = elementTop(frame);
        const restTop = Math.max(top + scrollY, 1);
        const endY = window.innerHeight * ICONS_SCROLL_END_VH;

        return mapRange(top, endY, restTop);
    };

    /**
     * Subtitle: desktop fully gone at 20vh; mobile fully gone at mid-viewport.
     * Fade from rest position → endY (not only the last strip to y=0).
     *
     * @returns {number}
     */
    const subtitleScrollLocal = () => {
        const node = subtitleEl();

        if (! node) {
            return 1;
        }

        const scrollY = window.scrollY || window.pageYOffset || 0;

        if (scrollY <= SCROLL_TOP_SNAP_PX) {
            return 1;
        }

        const top = elementTop(node);
        const endY = window.innerHeight * (
            isDesktopViewport() ? SUBTITLE_DESKTOP_START_VH : SUBTITLE_MOBILE_END_VH
        );
        const restTop = Math.max(top + scrollY, endY + 1);

        return mapRange(top, endY, restTop);
    };

    /**
     * @param {HeroLocals} locals
     * @param {{ driveHeader?: boolean, scrollScrub?: boolean }} [options]
     */
    const paint = (locals, options = {}) => {
        lastLocals = applyLocals(el, header, bg, locals, {
            ...options,
            lastLocals,
        });

        const minLocal = Math.min(
            lastLocals.background,
            lastLocals.story,
            lastLocals.title,
            lastLocals.icons,
            lastLocals.subtitle,
        );

        if (minLocal >= 1) {
            el.dataset.state = 'done';
        } else if (minLocal > 0 || scrubbing || playing) {
            el.dataset.state = 'playing';
        } else {
            el.dataset.state = 'pending';
        }
    };

    const localsFromScroll = () => {
        const scrollY = window.scrollY || window.pageYOffset || 0;

        if (scrollY <= SCROLL_TOP_SNAP_PX) {
            return {
                background: 1,
                story: 1,
                title: 1,
                icons: 1,
                subtitle: 1,
            };
        }

        return {
            background: storyScrollLocal(),
            story: storyMaskScrollLocal(),
            title: delayedScrollLocal(elementTop(titleEl())),
            icons: iconsScrollLocal(),
            subtitle: subtitleScrollLocal(),
        };
    };

    const applyScroll = () => {
        scrubbing = true;
        playing = false;
        paint(localsFromScroll(), { driveHeader: false, scrollScrub: true });
    };

    const onScroll = () => {
        if (prefersReducedMotion()) {
            return;
        }

        if (playing && raf !== null) {
            cancelAnimationFrame(raf);
            raf = null;
            playing = false;
            header?.finish();
        }

        if (scrollRaf !== null) {
            return;
        }

        scrollRaf = requestAnimationFrame(() => {
            scrollRaf = null;
            applyScroll();
        });
    };

    const onResize = () => {
        invalidateStoryBox(storyEl());

        if (scrubbing || progress >= 1) {
            applyScroll();
        }
    };

    const enableScrollDriver = () => {
        if (scrollDriverEnabled) {
            return;
        }

        scrollDriverEnabled = true;
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onResize);
    };

    const setProgress = (next) => {
        progress = clamp(next);
        paint(localsFromMaster(progress), { driveHeader: true });

        if (progress >= 1) {
            playing = false;
        }
    };

    const finish = () => {
        if (raf !== null) {
            cancelAnimationFrame(raf);
            raf = null;
        }

        playing = false;
        progress = 1;
        paint({
            background: 1,
            header: 1,
            story: 1,
            title: 1,
            icons: 1,
            subtitle: 1,
        }, { driveHeader: true });
        bg?.finish();
        header?.finish();
        el.dataset.state = 'done';
        enableScrollDriver();

        if ((window.scrollY || 0) > 0) {
            applyScroll();
        }
    };

    const api = {
        setProgress,
        finish,
        getProgress: () => progress,
        /**
         * @param {{ setProgress?: (n: number) => void, play?: () => void, finish: () => void, whenReady?: () => Promise<void> } | null} nextBg
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

            if (playing || (progress >= 1 && ! scrubbing)) {
                if (progress >= 1) {
                    enableScrollDriver();
                }

                return;
            }

            if ((window.scrollY || 0) > 2) {
                header?.finish();
                enableScrollDriver();
                applyScroll();

                return;
            }

            playing = true;
            scrubbing = false;
            el.dataset.state = 'playing';
            invalidateStoryBox(storyEl());
            const frame = storyEl();

            if (frame) {
                getStoryBox(frame, { force: true });
            }

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
                    header?.finish();
                    el.dataset.state = 'done';
                    enableScrollDriver();
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
 *   bg?: { setProgress?: (n: number) => void, play?: () => void, finish: () => void, whenReady?: () => Promise<void> } | null,
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
