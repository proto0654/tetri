import gsap from 'gsap';
import Swiper from 'swiper';
import { Keyboard, Mousewheel } from 'swiper/modules';
import { prefersReducedMotion } from './motion-utils';

const LG_QUERY = '(min-width: 1024px)';
const CLICK_THRESHOLD_PX = 8;
const FLIP_DURATION = 0.36;
const HINT_STORAGE_KEY = 'stories-swipe-hint-seen';
const HINT_AUTO_HIDE_MS = 2500;
const SOURCE_RADIUS = '1.75rem';

/**
 * @param {HTMLVideoElement | null} video
 */
const ensureVideoSource = (video) => {
    if (! video) {
        return false;
    }

    const source = video.querySelector('source[data-src]');

    if (! source) {
        return Boolean(video.currentSrc || video.src);
    }

    if (source.getAttribute('src')) {
        return true;
    }

    source.setAttribute('src', source.dataset.src ?? '');
    video.load();

    return true;
};

/**
 * Play from the start once enough data is buffered.
 *
 * @param {HTMLVideoElement | null} video
 * @param {{ muted?: boolean }} [options]
 */
const playVideo = (video, options = {}) => {
    if (! video) {
        return;
    }

    ensureVideoSource(video);

    const token = Symbol('play');
    video._storyPlayToken = token;

    const start = () => {
        if (video._storyPlayToken !== token) {
            return;
        }

        try {
            video.currentTime = 0;
        } catch {
            // Ignore seek before metadata.
        }

        video.muted = options.muted ?? false;

        const result = video.play();

        if (result?.catch) {
            result.catch(() => {
                if (video._storyPlayToken !== token) {
                    return;
                }

                video.muted = true;
                video.play()?.catch?.(() => {});
            });
        }
    };

    if (video.readyState >= HTMLMediaElement.HAVE_CURRENT_DATA) {
        start();

        return;
    }

    const onReady = () => {
        video.removeEventListener('loadeddata', onReady);
        video.removeEventListener('canplay', onReady);
        start();
    };

    video.addEventListener('loadeddata', onReady);
    video.addEventListener('canplay', onReady);
};

/**
 * @param {HTMLVideoElement | null} video
 */
const pauseVideo = (video) => {
    if (! video) {
        return;
    }

    video._storyPlayToken = null;

    if (! video.paused) {
        video.pause();
    }
};

/**
 * @param {DOMRect} source
 * @param {DOMRect} target
 */
const flipFromRects = (source, target) => {
    const sourceCx = source.left + source.width / 2;
    const sourceCy = source.top + source.height / 2;
    const targetCx = target.left + target.width / 2;
    const targetCy = target.top + target.height / 2;

    return {
        x: sourceCx - targetCx,
        y: sourceCy - targetCy,
        scaleX: source.width / Math.max(target.width, 1),
        scaleY: source.height / Math.max(target.height, 1),
    };
};

/**
 * @param {ParentNode} [root]
 */
export const initStoryViewer = (root = document) => {
    const viewer = root.querySelector('[data-story-viewer]');

    if (! (viewer instanceof HTMLElement) || viewer.dataset.ready === 'true') {
        return;
    }

    const frame = viewer.querySelector('[data-story-viewer-frame]');
    const stage = viewer.querySelector('[data-story-viewer-stage]');
    const scrim = viewer.querySelector('[data-story-viewer-scrim]');
    const closeButtons = Array.from(viewer.querySelectorAll('[data-story-viewer-close]'));
    const prevBtn = viewer.querySelector('[data-story-viewer-prev]');
    const nextBtn = viewer.querySelector('[data-story-viewer-next]');
    const swiperEl = viewer.querySelector('[data-story-viewer-swiper]');
    const hint = viewer.querySelector('[data-story-viewer-hint]');
    const hintText = viewer.querySelector('[data-story-viewer-hint-text]');
    const openers = Array.from(root.querySelectorAll('[data-story-open]'));

    if (! (stage instanceof HTMLElement) || ! (swiperEl instanceof HTMLElement) || openers.length === 0) {
        return;
    }

    viewer.dataset.ready = 'true';

    /** @type {import('swiper').Swiper | null} */
    let swiper = null;
    /** @type {HTMLElement | null} */
    let activeSource = null;
    /** @type {gsap.core.Tween | null} */
    let flipTween = null;
    let isOpen = false;
    let isAnimating = false;
    let lockedScrollY = 0;
    /** @type {ReturnType<typeof setTimeout> | null} */
    let hintTimer = null;
    const lgMq = window.matchMedia(LG_QUERY);

    const isDesktop = () => lgMq.matches;

    const lockScroll = () => {
        lockedScrollY = window.scrollY;
        document.documentElement.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${lockedScrollY}px`;
        document.body.style.width = '100%';
    };

    const unlockScroll = () => {
        const html = document.documentElement;
        const y = lockedScrollY;

        // html { scroll-behavior: smooth } would animate from 0 → y after fixed unlock.
        html.style.scrollBehavior = 'auto';

        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        html.style.overflow = '';
        document.body.style.overflow = '';

        window.scrollTo({ top: y, left: 0, behavior: 'instant' });

        requestAnimationFrame(() => {
            html.style.scrollBehavior = '';
        });
    };

    const findSourceById = (id) =>
        openers.find((el) => el instanceof HTMLElement && el.dataset.storyId === String(id)) ?? null;

    const currentSlide = () => {
        if (! swiper) {
            return null;
        }

        const slide = swiper.slides[swiper.activeIndex];

        return slide instanceof HTMLElement ? slide : null;
    };

    const syncNavButtons = () => {
        if (! swiper) {
            return;
        }

        const atStart = swiper.isBeginning;
        const atEnd = swiper.isEnd;

        if (prevBtn instanceof HTMLButtonElement) {
            prevBtn.disabled = atStart;
            prevBtn.setAttribute('aria-disabled', atStart ? 'true' : 'false');
        }

        if (nextBtn instanceof HTMLButtonElement) {
            nextBtn.disabled = atEnd;
            nextBtn.setAttribute('aria-disabled', atEnd ? 'true' : 'false');
        }
    };

    const syncActiveSource = () => {
        const slide = currentSlide();
        const id = slide?.dataset.storyId;
        const nextSource = id ? findSourceById(id) : null;

        if (activeSource && activeSource !== nextSource) {
            setSourceHidden(activeSource, false);
        }

        activeSource = nextSource;
        setSourceHidden(activeSource, true);
    };

    const syncActiveVideo = () => {
        if (! swiper) {
            return;
        }

        swiper.slides.forEach((slide, index) => {
            if (! (slide instanceof HTMLElement)) {
                return;
            }

            const video = slide.querySelector('video');

            if (! (video instanceof HTMLVideoElement)) {
                return;
            }

            const isActive = index === swiper.activeIndex;
            const isNeighbor = Math.abs(index - swiper.activeIndex) === 1;

            if (isActive || isNeighbor) {
                ensureVideoSource(video);
            }

            if (isActive) {
                playVideo(video, { muted: false });
            } else {
                pauseVideo(video);
            }
        });
    };

    const pauseAllViewerVideos = () => {
        viewer.querySelectorAll('video').forEach((video) => {
            if (video instanceof HTMLVideoElement) {
                pauseVideo(video);
            }
        });
    };

    const hideHint = () => {
        if (hintTimer) {
            clearTimeout(hintTimer);
            hintTimer = null;
        }

        if (! (hint instanceof HTMLElement)) {
            return;
        }

        hint.hidden = true;
        hint.dataset.visible = 'false';
    };

    const markHintSeen = () => {
        try {
            sessionStorage.setItem(HINT_STORAGE_KEY, '1');
        } catch {
            // Ignore quota / private mode.
        }
    };

    const hintAlreadySeen = () => {
        try {
            return sessionStorage.getItem(HINT_STORAGE_KEY) === '1';
        } catch {
            return false;
        }
    };

    const showHintIfNeeded = () => {
        if (! (hint instanceof HTMLElement) || ! (hintText instanceof HTMLElement) || hintAlreadySeen()) {
            return;
        }

        hintText.textContent = isDesktop()
            ? 'Прокрутите колесиком'
            : 'Свайпните вверх';
        hint.hidden = false;
        hint.dataset.visible = 'true';

        hintTimer = setTimeout(() => {
            hideHint();
            markHintSeen();
        }, HINT_AUTO_HIDE_MS);
    };

    /**
     * @param {HTMLElement | null} source
     * @param {boolean} hidden
     */
    const setSourceHidden = (source, hidden) => {
        if (! source) {
            return;
        }

        source.style.opacity = hidden ? '0' : '';
        source.setAttribute('aria-hidden', hidden ? 'true' : 'false');
    };

    const applyStageMode = () => {
        viewer.dataset.mode = isDesktop() ? 'lightbox' : 'fullscreen';
        stage.style.borderRadius = isDesktop() ? SOURCE_RADIUS : '0';
    };

    const ensureSwiper = () => {
        if (swiper) {
            return swiper;
        }

        swiper = new Swiper(swiperEl, {
            modules: [Mousewheel, Keyboard],
            direction: 'vertical',
            slidesPerView: 1,
            speed: 350,
            resistanceRatio: 0.65,
            mousewheel: {
                forceToAxis: true,
                sensitivity: 1,
                releaseOnEdges: true,
            },
            keyboard: {
                enabled: true,
                onlyInViewport: false,
            },
            allowTouchMove: true,
            on: {
                slideChange() {
                    hideHint();
                    markHintSeen();
                    syncActiveSource();
                    syncNavButtons();
                },
                slideChangeTransitionEnd() {
                    syncActiveVideo();
                },
            },
        });

        return swiper;
    };

    const killFlip = () => {
        if (flipTween) {
            flipTween.kill();
            flipTween = null;
        }

        gsap.killTweensOf([stage, scrim, frame].filter(Boolean));
    };

    /**
     * @param {HTMLElement} source
     * @param {number} index
     */
    const open = (source, index) => {
        if (isOpen || isAnimating) {
            return;
        }

        isAnimating = true;
        activeSource = source;
        applyStageMode();
        lockScroll();

        viewer.hidden = false;
        viewer.setAttribute('aria-hidden', 'false');
        viewer.dataset.open = 'true';

        const instance = ensureSwiper();
        instance.slideTo(index, 0, false);
        instance.update();
        syncNavButtons();

        // Force layout so stage target rect matches fullscreen/lightbox CSS.
        void stage.offsetWidth;

        const reduced = prefersReducedMotion();
        const sourceRect = source.getBoundingClientRect();
        const targetRect = stage.getBoundingClientRect();

        setSourceHidden(source, true);

        if (scrim instanceof HTMLElement) {
            gsap.set(scrim, { opacity: reduced ? 1 : 0 });
        }

        if (frame instanceof HTMLElement) {
            gsap.set(frame, { opacity: 1 });
        }

        const finishOpen = () => {
            flipTween = null;
            gsap.set(stage, { clearProps: 'willChange' });
            applyStageMode();
            isOpen = true;
            isAnimating = false;
            syncActiveVideo();
            showHintIfNeeded();
            closeButtons[0]?.focus({ preventScroll: true });
        };

        if (reduced) {
            gsap.set(stage, { clearProps: 'transform,borderRadius,willChange' });
            applyStageMode();
            finishOpen();

            return;
        }

        const flip = flipFromRects(sourceRect, targetRect);

        gsap.set(stage, {
            x: flip.x,
            y: flip.y,
            scaleX: flip.scaleX,
            scaleY: flip.scaleY,
            borderRadius: SOURCE_RADIUS,
            willChange: 'transform',
            transformOrigin: 'center center',
        });

        const tl = gsap.timeline({
            onComplete: finishOpen,
        });

        flipTween = tl;

        if (scrim instanceof HTMLElement) {
            tl.to(scrim, { opacity: 1, duration: FLIP_DURATION * 0.7, ease: 'power2.out' }, 0);
        }

        tl.to(
            stage,
            {
                x: 0,
                y: 0,
                scaleX: 1,
                scaleY: 1,
                borderRadius: isDesktop() ? SOURCE_RADIUS : '0px',
                duration: FLIP_DURATION,
                ease: 'power2.inOut',
            },
            0,
        );
    };

    const close = () => {
        if ((! isOpen && viewer.dataset.open !== 'true') || isAnimating) {
            return;
        }

        isAnimating = true;
        hideHint();
        pauseAllViewerVideos();
        killFlip();

        const source = activeSource
            ?? findSourceById(currentSlide()?.dataset.storyId ?? '')
            ?? (openers[0] instanceof HTMLElement ? openers[0] : null);
        const reduced = prefersReducedMotion();

        const finishClose = () => {
            viewer.hidden = true;
            viewer.setAttribute('aria-hidden', 'true');
            viewer.dataset.open = 'false';
            gsap.set(stage, { clearProps: 'transform,borderRadius,willChange,x,y,scaleX,scaleY' });

            if (scrim instanceof HTMLElement) {
                gsap.set(scrim, { clearProps: 'opacity' });
            }

            setSourceHidden(source, false);
            activeSource = null;
            unlockScroll();
            isOpen = false;
            isAnimating = false;
            source?.focus({ preventScroll: true });
        };

        if (reduced || ! source) {
            finishClose();

            return;
        }

        const sourceRect = source.getBoundingClientRect();
        const targetRect = stage.getBoundingClientRect();
        const flip = flipFromRects(sourceRect, targetRect);

        gsap.set(stage, { willChange: 'transform', transformOrigin: 'center center' });

        const tl = gsap.timeline({
            onComplete: finishClose,
        });

        flipTween = tl;

        if (scrim instanceof HTMLElement) {
            tl.to(scrim, { opacity: 0, duration: FLIP_DURATION * 0.7, ease: 'power2.in' }, 0);
        }

        tl.to(
            stage,
            {
                x: flip.x,
                y: flip.y,
                scaleX: flip.scaleX,
                scaleY: flip.scaleY,
                borderRadius: SOURCE_RADIUS,
                duration: FLIP_DURATION,
                ease: 'power2.inOut',
            },
            0,
        );
    };

    const onResize = () => {
        if (! isOpen || isAnimating) {
            return;
        }

        applyStageMode();
        swiper?.update();
        syncNavButtons();
        gsap.set(stage, { clearProps: 'transform,x,y,scaleX,scaleY' });
    };

    /** @type {Map<Element, { x: number, y: number }>} */
    const pointerStarts = new Map();
    const listeners = new AbortController();
    const { signal } = listeners;

    openers.forEach((opener) => {
        if (! (opener instanceof HTMLElement)) {
            return;
        }

        opener.addEventListener('pointerdown', (event) => {
            if (! (event instanceof PointerEvent)) {
                return;
            }

            if (event.button !== 0 && event.pointerType === 'mouse') {
                return;
            }

            pointerStarts.set(opener, { x: event.clientX, y: event.clientY });
        }, { signal });

        opener.addEventListener('pointerup', (event) => {
            if (! (event instanceof PointerEvent)) {
                return;
            }

            const start = pointerStarts.get(opener);
            pointerStarts.delete(opener);

            if (! start) {
                return;
            }

            const dx = Math.abs(event.clientX - start.x);
            const dy = Math.abs(event.clientY - start.y);

            if (dx > CLICK_THRESHOLD_PX || dy > CLICK_THRESHOLD_PX) {
                return;
            }

            const index = Number(opener.dataset.storyIndex ?? 0);
            open(opener, Number.isFinite(index) ? index : 0);
        }, { signal });

        opener.addEventListener('pointercancel', () => {
            pointerStarts.delete(opener);
        }, { signal });

        opener.addEventListener('keydown', (event) => {
            if (! (event instanceof KeyboardEvent)) {
                return;
            }

            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            const index = Number(opener.dataset.storyIndex ?? 0);
            open(opener, Number.isFinite(index) ? index : 0);
        }, { signal });
    });

    closeButtons.forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            close();
        }, { signal });
    });

    prevBtn?.addEventListener('click', (event) => {
        event.preventDefault();

        if (! isOpen || isAnimating) {
            return;
        }

        swiper?.slidePrev();
    }, { signal });

    nextBtn?.addEventListener('click', (event) => {
        event.preventDefault();

        if (! isOpen || isAnimating) {
            return;
        }

        swiper?.slideNext();
    }, { signal });

    scrim?.addEventListener('click', () => {
        if (isDesktop()) {
            close();
        }
    }, { signal });

    document.addEventListener('keydown', (event) => {
        if (! isOpen || event.key !== 'Escape') {
            return;
        }

        event.preventDefault();
        close();
    }, { signal });

    lgMq.addEventListener('change', onResize, { signal });
    window.addEventListener('resize', onResize, { signal });

    viewer._storyViewerDestroy = () => {
        listeners.abort();
        hideHint();
        pauseAllViewerVideos();
        killFlip();

        if (isOpen || viewer.dataset.open === 'true') {
            setSourceHidden(activeSource, false);
            unlockScroll();
            viewer.hidden = true;
            viewer.setAttribute('aria-hidden', 'true');
            viewer.dataset.open = 'false';
        }

        swiper?.destroy(true, true);
        swiper = null;
        delete viewer.dataset.ready;
        delete viewer._storyViewerDestroy;
    };
};

/**
 * @param {ParentNode} [root]
 */
export const destroyStoryViewer = (root = document) => {
    root.querySelectorAll('[data-story-viewer]').forEach((viewer) => {
        if (viewer instanceof HTMLElement) {
            viewer._storyViewerDestroy?.();
        }
    });
};
