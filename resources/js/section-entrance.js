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

/** Overlapping timeline positions (seconds offsets relative to previous add). */
const ENTRANCE_OFFSETS = {
    title: 0,
    fade: '-=0.35',
    list: '-=0.35',
    media: '-=0.25',
    info: '-=0.3',
    cta: '-=0.2',
};

/** Soft cap — clip-path morph is paint-heavy. */
const MAX_MORPH = 3;
const MAX_FADE_MEDIA = 8;
const FADE_DURATION = 0.55;
const TITLE_DURATION = 0.65;
const MORPH_DURATION = 0.85;
const LINE_STAGGER = 0.08;
const ITEM_STAGGER = 0.07;
const MEDIA_STAGGER = 0.1;

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
 * @param {HTMLElement} el
 */
const isSwiperDuplicateMedia = (el) => {
    const slide = el.closest('.swiper-slide');

    if (! slide) {
        return false;
    }

    if (slide.classList.contains('swiper-slide-duplicate')) {
        return true;
    }

    return slide.getAttribute('aria-hidden') === 'true';
};

/**
 * Swiper slide media uses opacity fade (clip-path morph kills FPS on many cards).
 *
 * @param {HTMLElement} el
 */
const isSlideMediaFade = (el) => Boolean(el.closest('.swiper-slide'));

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
            el.style.opacity = '1';
            el.style.clipPath = 'none';
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
            el.style.opacity = '';
            el.style.clipPath = '';

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
        gsap.set(mediaFadeEls, { opacity: 0, clipPath: 'none', force3D: true });
    }

    mediaMorphEls.forEach((el) => {
        el.style.clipPath = MORPH_CLIP_START;
        el.style.opacity = '0';
    });

    /** @type {HTMLElement[]} */
    const clearable = [
        ...titleLines,
        ...fadeEls,
        ...listItems,
        ...ctaEls,
        ...mediaFadeEls,
        ...infoLineGroups.flat(),
    ];

    const tl = gsap.timeline({
        paused: true,
        defaults: { ease: 'power2.out', force3D: true },
        onStart: () => {
            section.dataset.state = 'playing';
        },
        onComplete: () => {
            section.dataset.state = 'done';
            mediaMorphEls.forEach((el) => {
                el.style.willChange = 'auto';
            });
            // Let CSS done rules own finals; drop GSAP inline props.
            releaseInlineMotion(clearable);
            mediaMorphEls.forEach((el) => {
                const box = cacheMorphBox(el);
                el.style.clipPath = morphClipFinal(box.finalRadius);
                el.style.opacity = '';
            });
        },
    });

    if (titleLines.length) {
        tl.to(titleLines, {
            yPercent: 0,
            opacity: 1,
            duration: TITLE_DURATION,
            stagger: LINE_STAGGER,
        }, ENTRANCE_OFFSETS.title);
    }

    if (fadeEls.length) {
        tl.to(fadeEls, {
            opacity: 1,
            duration: FADE_DURATION,
            stagger: ITEM_STAGGER,
        }, titleLines.length ? ENTRANCE_OFFSETS.fade : 0);
    }

    if (listItems.length) {
        tl.to(listItems, {
            opacity: 1,
            duration: FADE_DURATION,
            stagger: ITEM_STAGGER,
        }, ENTRANCE_OFFSETS.list);
    }

    if (mediaFadeEls.length) {
        tl.to(mediaFadeEls, {
            opacity: 1,
            duration: FADE_DURATION,
            stagger: MEDIA_STAGGER,
        }, ENTRANCE_OFFSETS.media);
    }

    mediaMorphEls.forEach((el, index) => {
        const proxy = { t: 0 };
        /** @type {{ width: number, height: number, finalRadius: number } | null} */
        let box = null;
        const position = index === 0
            ? (mediaFadeEls.length ? `-=${FADE_DURATION - MEDIA_STAGGER}` : ENTRANCE_OFFSETS.media)
            : `-=${MORPH_DURATION - MEDIA_STAGGER}`;

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
        }, position);
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
 * Register ScrollTrigger only — timeline/SplitType built on first enter.
 *
 * @param {HTMLElement} section
 */
const setupSection = (section) => {
    if (section.dataset.entranceReady === 'true') {
        return;
    }

    section.dataset.entranceReady = 'true';
    section.dataset.state = section.dataset.state || 'pending';

    const triggerId = `entrance-${section.id || Math.random().toString(36).slice(2, 9)}`;

    ScrollTrigger.create({
        id: triggerId,
        trigger: section,
        start: 'top 78%',
        once: true,
        fastScrollEnd: true,
        onEnter: () => {
            if (section.dataset.state === 'done' || section.dataset.state === 'playing') {
                return;
            }

            const { tl, splits, titleEl, titleRestoreHtml } = buildSectionTimeline(section);

            section._entranceSplits = splits;
            section._entranceTimeline = tl;
            section._entranceTitleRestore = titleEl && titleRestoreHtml
                ? { el: titleEl, html: titleRestoreHtml }
                : null;

            // Wait a paint so SplitType layout settles before first tween frame.
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    if (section.dataset.state === 'done') {
                        return;
                    }

                    tl.play(0);
                });
            });
        },
    });
};

/**
 * @param {ParentNode} [root=document]
 */
export const destroySectionEntrance = (root = document) => {
    root.querySelectorAll('[data-entrance]').forEach((section) => {
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

    sections.forEach((section) => setupSection(section));
};
