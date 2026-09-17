import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import SplitType from 'split-type';
import { prefersReducedMotion } from './motion-utils';
import {
    applyMorphClip,
    cacheMorphBox,
    morphClipFinal,
    MORPH_CLIP_START,
} from './motion-morph';

gsap.registerPlugin(ScrollTrigger);
// Default lagSmoothing(500) still lets a ~200ms image first-paint hitch jump opacity.
gsap.ticker.lagSmoothing(120, 33);

/** Overlapping timeline positions (seconds offsets relative to previous add). */
const ENTRANCE_OFFSETS = {
    title: 0,
    fade: '-=0.35',
    list: '-=0.35',
    /** Media/slides start after copy phase (`>` via label) — not overlapped into title/fade. */
    info: '-=0.3',
    /** CTA after media chain; slight overlap with last card. */
    cta: '-=0.15',
};

/** Soft cap — clip-path morph is paint-heavy. */
const MAX_MORPH = 3;
const MAX_FADE_MEDIA = 8;
const FADE_DURATION = 0.55;
/** Swiper card fades — match readable left→right chain (stories). */
const MEDIA_FADE_DURATION = 0.65;
const TITLE_DURATION = 0.65;
const MORPH_DURATION = 0.85;
/** Title lines — wider gap so each line reads before the next. */
const TITLE_LINE_STAGGER = 0.16;
/** Info / secondary line fades. */
const LINE_STAGGER = 0.08;
const ITEM_STAGGER = 0.07;
const MEDIA_STAGGER = 0.16;
/** Settle after scroll enter so the block is readable before the chain. */
const ENTER_PLAY_DELAY_MS = 160;
/**
 * Post related ([data-entrance-follow]): extra delay when both roots are visible on load,
 * so the second title does not slide up with the first.
 */
const FOLLOW_WHEN_BOTH_VISIBLE_MS = 1800;
/** Section top must reach this viewport line before onEnter. */
const ENTRANCE_START = 'top 65%';

/**
 * Nested [data-entrance] roots (e.g. footer inside contacts) own their own attrs.
 *
 * @param {HTMLElement} section
 * @param {Element} el
 */
const isOwnedBySection = (section, el) => el.closest('[data-entrance]') === section;

/**
 * @param {HTMLElement} section
 * @param {string} selector
 * @returns {HTMLElement[]}
 */
const queryOwned = (section, selector) => Array.from(section.querySelectorAll(selector))
    .filter((el) => isOwnedBySection(section, el));

/**
 * @param {HTMLElement} section
 * @param {string} selector
 * @returns {HTMLElement | null}
 */
const queryOwnedOne = (section, selector) => queryOwned(section, selector)[0] ?? null;

/**
 * Loop filler / Swiper clones — hide until section done, never snap visible mid-chain.
 *
 * @param {HTMLElement} el
 */
const isSwiperDuplicateMedia = (el) => {
    const slide = el.closest('.swiper-slide');

    if (! slide) {
        return false;
    }

    // Our ×2 loop-width clones (app.js). Do not use aria-hidden — Swiper may set it on originals.
    if (slide.dataset.entranceClone === 'true') {
        return true;
    }

    return slide.classList.contains('swiper-slide-duplicate');
};

/**
 * Swiper slide media uses opacity fade (clip-path morph kills FPS on many cards).
 *
 * @param {HTMLElement} el
 */
const isSlideMediaFade = (el) => Boolean(el.closest('.swiper-slide'));

/**
 * Decode + near-invisible paint during copy so the first visible fade frame
 * does not upload large bitmaps and hitch (~200ms → GSAP opacity catch-up).
 *
 * @param {HTMLElement[]} els
 */
const prewarmFadeMedia = (els) => {
    if (els.length === 0) {
        return;
    }

    els.forEach((el) => {
        const img = el.querySelector('img');

        if (img?.decode) {
            void img.decode().catch(() => {});
        }

        el.style.opacity = '0.001';
    });

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            els.forEach((el) => {
                // Proxy fade may already own opacity if copy phase was skipped.
                if (Number.parseFloat(el.style.opacity || '0') <= 0.002) {
                    el.style.opacity = '0';
                }
            });
        });
    });
};

/**
 * @param {HTMLElement} section
 * @returns {{ fade: HTMLElement[], morph: HTMLElement[] }}
 */
const collectMedia = (section) => {
    const all = queryOwned(section, '[data-entrance-media]')
        .filter((el) => ! isSwiperDuplicateMedia(el));

    const fade = [];
    const morph = [];

    all.forEach((el) => {
        if (isSlideMediaFade(el) || el.dataset.entranceMedia === 'fade') {
            fade.push(el);
        } else {
            morph.push(el);
        }
    });

    return {
        fade: fade.slice(0, MAX_FADE_MEDIA),
        morph: morph.slice(0, MAX_MORPH),
    };
};

/**
 * @param {HTMLElement} el
 * @param {{ width: number, height: number, finalRadius: number }} box
 */
const setMorphFinal = (el, box) => {
    el.style.clipPath = morphClipFinal(box.finalRadius);
    el.style.opacity = '1';
    el.style.willChange = 'auto';
};

/**
 * @param {HTMLElement} section
 * @param {HTMLElement[]} activeFade
 * @param {HTMLElement[]} activeMorph
 */
const snapSkippedMedia = (section, activeFade, activeMorph) => {
    queryOwned(section, '[data-entrance-media]').forEach((el) => {
        if (activeFade.includes(el) || activeMorph.includes(el)) {
            return;
        }

        if (isSlideMediaFade(el) || el.dataset.entranceMedia === 'fade' || isSwiperDuplicateMedia(el)) {
            // Inline hide until done — CSS playing must not set opacity (fights GSAP on originals).
            el.style.opacity = '0';
            el.style.clipPath = 'none';
            el.style.transform = '';
            el.style.willChange = 'auto';

            return;
        }

        setMorphFinal(el, cacheMorphBox(el));
    });
};

/**
 * @param {HTMLElement[]} lines
 */
const maskLines = (lines) => {
    lines.forEach((line) => {
        if (line.parentElement?.classList.contains('entrance-line-mask')) {
            return;
        }

        const wrap = document.createElement('div');
        wrap.className = 'entrance-line-mask';
        line.parentNode?.insertBefore(wrap, line);
        wrap.appendChild(line);
    });
};

/**
 * @param {HTMLElement} titleEl
 * @returns {HTMLElement[]}
 */
const splitTitleByBreaks = (titleEl) => {
    const parts = titleEl.innerHTML
        .split(/<br\s*\/?>/i)
        .map((part) => part.trim())
        .filter((part) => part.length > 0);

    if (parts.length === 0) {
        return [];
    }

    titleEl.dataset.entranceSplit = 'br';
    titleEl.innerHTML = '';

    return parts.map((part) => {
        const mask = document.createElement('div');
        mask.className = 'entrance-line-mask';

        const line = document.createElement('div');
        line.className = 'entrance-line';
        line.innerHTML = part;

        mask.appendChild(line);
        titleEl.appendChild(mask);

        return line;
    });
};

/**
 * @param {HTMLElement} titleEl
 * @returns {{ lines: HTMLElement[], split: import('split-type').default | null, restoreHtml: string | null }}
 */
const prepareTitleLines = (titleEl) => {
    const restoreHtml = titleEl.innerHTML;
    const usesShift = titleEl.classList.contains('site-text-shift');
    const hasBreak = /<br\s*\/?>/i.test(titleEl.innerHTML);

    if (usesShift || hasBreak) {
        return {
            lines: splitTitleByBreaks(titleEl),
            split: null,
            restoreHtml,
        };
    }

    const split = new SplitType(titleEl, { types: 'lines', lineClass: 'entrance-line' });
    const lines = split.lines ?? [];
    maskLines(lines);

    return { lines, split, restoreHtml: null };
};

/**
 * Hand final styles back to CSS `[data-state=done]` rules.
 *
 * @param {HTMLElement[]} elements
 */
const releaseInlineMotion = (elements) => {
    if (elements.length === 0) {
        return;
    }

    gsap.set(elements, { clearProps: 'opacity,transform,translate,rotate,scale,clipPath,willChange' });
};

/**
 * @param {HTMLElement} section
 */
const finishSection = (section) => {
    section.dataset.state = 'done';

    queryOwned(section, '[data-entrance-media]').forEach((el) => {
        el.style.willChange = 'auto';

        if (isSlideMediaFade(el) || el.dataset.entranceMedia === 'fade') {
            // Plain style clear — gsap.clearProps can miss nodes never registered with CSSPlugin.
            el.style.opacity = '';
            el.style.clipPath = '';
            el.style.transform = '';
            el.style.translate = '';
            el.style.rotate = '';
            el.style.scale = '';
            el.style.willChange = 'auto';

            return;
        }

        const box = cacheMorphBox(el);
        el.style.clipPath = morphClipFinal(box.finalRadius);
        el.style.opacity = '';
    });

    releaseInlineMotion(queryOwned(
        section,
        '[data-entrance-fade], [data-entrance-cta], [data-entrance-list] > *, .entrance-line',
    ));
};

/**
 * Built lazily on ScrollTrigger enter — avoids SplitType/layout work for off-screen sections.
 *
 * @param {HTMLElement} section
 */
const buildSectionTimeline = (section) => {
    const splits = [];
    const titleEl = queryOwnedOne(section, '[data-entrance-title]');
    const infoEls = queryOwned(section, '[data-entrance-info]');
    const fadeEls = queryOwned(section, '[data-entrance-fade]');
    const listItems = queryOwned(section, '[data-entrance-list] > *');
    const ctaEls = queryOwned(section, '[data-entrance-cta]');
    const { fade: mediaFadeEls, morph: mediaMorphEls } = collectMedia(section);

    snapSkippedMedia(section, mediaFadeEls, mediaMorphEls);

    let titleLines = [];
    /** @type {string | null} */
    let titleRestoreHtml = null;

    if (titleEl) {
        const prepared = prepareTitleLines(titleEl);
        titleLines = prepared.lines;
        titleRestoreHtml = prepared.restoreHtml;

        if (prepared.split) {
            splits.push(prepared.split);
        }

        gsap.set(titleLines, { yPercent: 110, opacity: 0, force3D: true });
    }

    const infoLineGroups = infoEls.map((el) => {
        const split = new SplitType(el, { types: 'lines', lineClass: 'entrance-line' });
        splits.push(split);
        const lines = split.lines ?? [];
        gsap.set(lines, { opacity: 0, force3D: true });

        return lines;
    });

    if (fadeEls.length) {
        gsap.set(fadeEls, { opacity: 0, force3D: true });
    }

    if (listItems.length) {
        gsap.set(listItems, { opacity: 0, force3D: true });
    }

    if (ctaEls.length) {
        gsap.set(ctaEls, { opacity: 0, force3D: true });
    }

    if (mediaFadeEls.length) {
        // Drive opacity via proxy — CSSPlugin would stamp translate(0,0) and flicker
        // inside Swiper's transformed wrapper.
        mediaFadeEls.forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = '';
            el.style.translate = '';
            el.style.rotate = '';
            el.style.scale = '';
            el.style.clipPath = 'none';
        });
    }

    mediaMorphEls.forEach((el) => {
        el.style.clipPath = MORPH_CLIP_START;
        el.style.opacity = '0';
    });

    const tl = gsap.timeline({
        paused: true,
        defaults: { ease: 'power2.out' },
        onStart: () => {
            section.dataset.state = 'playing';
            // Warm GPU textures during title/fade before media+=0.
            prewarmFadeMedia(mediaFadeEls);
        },
        onComplete: () => {
            // Clears fade media (incl. skipped clones) + text roles; CSS done owns finals.
            finishSection(section);
        },
    });

    if (titleLines.length) {
        tl.to(titleLines, {
            yPercent: 0,
            opacity: 1,
            duration: TITLE_DURATION,
            stagger: TITLE_LINE_STAGGER,
            force3D: true,
        }, ENTRANCE_OFFSETS.title);
    }

    if (fadeEls.length) {
        tl.to(fadeEls, {
            opacity: 1,
            duration: FADE_DURATION,
            stagger: ITEM_STAGGER,
            force3D: true,
        }, titleLines.length ? ENTRANCE_OFFSETS.fade : 0);
    }

    if (listItems.length) {
        tl.to(listItems, {
            opacity: 1,
            duration: FADE_DURATION,
            stagger: ITEM_STAGGER,
            force3D: true,
        }, ENTRANCE_OFFSETS.list);
    }

    // Own phase after title/fade/list — slides must not share the copy-role `-=` overlap
    // (menu-preview: short title + 2 fades drowned the card stagger; stories felt fine
    // because the title phase is longer). Label + absolute offsets = stable stagger.
    const hasMedia = mediaFadeEls.length > 0 || mediaMorphEls.length > 0;

    if (hasMedia) {
        tl.add('media', '>');
    }

    mediaFadeEls.forEach((el, index) => {
        const proxy = { o: 0 };

        tl.to(proxy, {
            o: 1,
            duration: MEDIA_FADE_DURATION,
            ease: 'sine.out',
            onUpdate: () => {
                el.style.opacity = String(proxy.o);
            },
        }, `media+=${index * MEDIA_STAGGER}`);
    });

    mediaMorphEls.forEach((el, index) => {
        const proxy = { t: 0 };
        /** @type {{ width: number, height: number, finalRadius: number } | null} */
        let box = null;

        tl.to(proxy, {
            t: 1,
            duration: MORPH_DURATION,
            ease: 'power2.out',
            onStart: () => {
                box = cacheMorphBox(el);
                el.style.willChange = 'clip-path, opacity';
            },
            onUpdate: () => applyMorphClip(el, proxy.t, box ?? cacheMorphBox(el)),
            onComplete: () => setMorphFinal(el, box ?? cacheMorphBox(el)),
        }, `media+=${index * MEDIA_STAGGER}`);
    });

    infoLineGroups.forEach((lines, index) => {
        if (! lines.length) {
            return;
        }

        tl.to(lines, {
            opacity: 1,
            duration: FADE_DURATION,
            stagger: LINE_STAGGER,
        }, index === 0 ? ENTRANCE_OFFSETS.info : `-=${FADE_DURATION - LINE_STAGGER}`);
    });

    if (ctaEls.length) {
        tl.to(ctaEls, {
            opacity: 1,
            duration: FADE_DURATION,
            stagger: ITEM_STAGGER,
        }, ENTRANCE_OFFSETS.cta);
    }

    return { tl, splits, titleEl, titleRestoreHtml };
};

/**
 * Any pixel counts — including a 1px edge at the bottom of the viewport.
 *
 * @param {HTMLElement} el
 */
const isPartiallyInViewport = (el) => {
    const rect = el.getBoundingClientRect();

    return rect.top < window.innerHeight && rect.bottom > 0;
};

/**
 * @param {HTMLElement} section
 * @param {HTMLElement[]} ordered
 * @returns {HTMLElement | null}
 */
const previousEntrance = (section, ordered) => {
    const index = ordered.indexOf(section);

    return index > 0 ? ordered[index - 1] : null;
};

/**
 * @param {HTMLElement} section
 * @param {HTMLElement[]} ordered
 * @returns {HTMLElement | null}
 */
const nextEntrance = (section, ordered) => {
    const index = ordered.indexOf(section);

    return index >= 0 && index < ordered.length - 1 ? ordered[index + 1] : null;
};

/**
 * Follow root shares the screen with the previous one (even by an edge).
 *
 * @param {HTMLElement} section
 * @param {HTMLElement[]} ordered
 */
const followSharesViewportWithPrev = (section, ordered) => {
    if (! section.hasAttribute('data-entrance-follow')) {
        return false;
    }

    const prev = previousEntrance(section, ordered);

    return Boolean(prev && isPartiallyInViewport(prev) && isPartiallyInViewport(section));
};

/**
 * Build timeline and play after paint + settle delay.
 *
 * @param {HTMLElement} section
 * @param {{ delayMs?: number }} [options]
 */
const beginSectionPlay = (section, options = {}) => {
    const { delayMs = ENTER_PLAY_DELAY_MS } = options;

    if (
        section._entranceStarted
        || section.dataset.state === 'done'
        || section.dataset.state === 'playing'
    ) {
        return;
    }

    section._entranceStarted = true;

    const { tl, splits, titleEl, titleRestoreHtml } = buildSectionTimeline(section);

    section._entranceSplits = splits;
    section._entranceTimeline = tl;
    section._entranceTitleRestore = titleEl && titleRestoreHtml
        ? { el: titleEl, html: titleRestoreHtml }
        : null;

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            if (section.dataset.state === 'done') {
                return;
            }

            if (delayMs <= 0) {
                tl.play(0);

                return;
            }

            section._entranceDelayTimer = window.setTimeout(() => {
                section._entranceDelayTimer = null;

                if (section.dataset.state === 'done') {
                    return;
                }

                tl.play(0);
            }, delayMs);
        });
    });
};

/**
 * Register ScrollTrigger only — timeline/SplitType built on first enter.
 *
 * @param {HTMLElement} section
 * @param {HTMLElement[]} ordered
 */
const setupSection = (section, ordered) => {
    if (section.dataset.entranceReady === 'true') {
        return;
    }

    section.dataset.entranceReady = 'true';
    section.dataset.state = section.dataset.state || 'pending';

    const triggerId = `entrance-${section.id || Math.random().toString(36).slice(2, 9)}`;

    ScrollTrigger.create({
        id: triggerId,
        trigger: section,
        start: ENTRANCE_START,
        once: true,
        fastScrollEnd: true,
        onEnter: () => {
            if (
                section._entranceStarted
                || section.dataset.state === 'done'
                || section.dataset.state === 'playing'
            ) {
                return;
            }

            const delayMs = ENTER_PLAY_DELAY_MS + (
                followSharesViewportWithPrev(section, ordered)
                    ? FOLLOW_WHEN_BOTH_VISIBLE_MS
                    : 0
            );

            beginSectionPlay(section, { delayMs });

            // Edge of related already on screen — don't leave it pending until 65%.
            const next = nextEntrance(section, ordered);

            if (
                next
                && next.hasAttribute('data-entrance-follow')
                && ! next._entranceStarted
                && isPartiallyInViewport(next)
            ) {
                beginSectionPlay(next, {
                    delayMs: ENTER_PLAY_DELAY_MS + FOLLOW_WHEN_BOTH_VISIBLE_MS,
                });
            }
        },
    });
};

/**
 * @param {ParentNode} [root=document]
 */
export const destroySectionEntrance = (root = document) => {
    root.querySelectorAll('[data-entrance]').forEach((section) => {
        if (section._entranceDelayTimer) {
            window.clearTimeout(section._entranceDelayTimer);
            section._entranceDelayTimer = null;
        }

        section._entranceTimeline?.kill();
        section._entranceSplits?.forEach((split) => {
            try {
                split.revert();
            } catch {
                // SplitType may already be detached after navigate.
            }
        });

        const titleRestore = section._entranceTitleRestore;

        if (titleRestore?.el && typeof titleRestore.html === 'string') {
            titleRestore.el.innerHTML = titleRestore.html;
            delete titleRestore.el.dataset.entranceSplit;
        }

        section._entranceTimeline = undefined;
        section._entranceSplits = undefined;
        section._entranceTitleRestore = undefined;
        section._entranceStarted = undefined;
        delete section.dataset.entranceReady;
    });

    ScrollTrigger.getAll().forEach((st) => {
        if (typeof st.vars?.id === 'string' && st.vars.id.startsWith('entrance-')) {
            st.kill();
        }
    });
};

/**
 * @param {ParentNode} [root=document]
 */
export const initSectionEntrance = (root = document) => {
    const sections = Array.from(root.querySelectorAll('[data-entrance]'));

    if (sections.length === 0) {
        return;
    }

    if (prefersReducedMotion()) {
        sections.forEach((section) => {
            section.dataset.entranceReady = 'true';
            finishSection(section);
        });

        return;
    }

    sections.forEach((section) => setupSection(section, sections));
};

/**
 * Recalculate entrance ScrollTriggers after DOM height changes
 * (e.g. Livewire MenuGrid category/pagination morph). Without this,
 * pending footers keep stale start positions and never fire onEnter.
 */
let entranceRefreshQueued = false;

export const refreshSectionEntrance = () => {
    if (entranceRefreshQueued) {
        return;
    }

    entranceRefreshQueued = true;

    // Double rAF: wait until morph layout + paint settle.
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            entranceRefreshQueued = false;
            ScrollTrigger.refresh();
        });
    });
};

/**
 * Bind once — Livewire AJAX morphs change page height without navigating.
 */
export const bindLivewireSectionEntranceRefresh = () => {
    const bind = () => {
        if (typeof Livewire === 'undefined' || typeof Livewire.hook !== 'function') {
            return;
        }

        Livewire.hook('morphed', () => {
            refreshSectionEntrance();
        });
    };

    if (typeof window.Livewire !== 'undefined') {
        bind();

        return;
    }

    document.addEventListener('livewire:init', bind, { once: true });
};
