import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';
import { initHeroBg } from './hero-bg';
import { startHeroEntrance } from './hero-entrance';
import { initHeaderEntrance } from './header-entrance';
import { destroySectionEntrance, initSectionEntrance } from './section-entrance';

const resolveSlidesOffset = (el, datasetKey) => {
    const raw = el.dataset[datasetKey];

    if (raw === undefined || raw === '') {
        return 0;
    }

    if (raw === 'shell') {
        const shell = el.closest('[data-site-shell]') ?? document.querySelector('[data-site-shell]');

        if (! shell) {
            return 0;
        }

        const shellStyles = window.getComputedStyle(shell);
        const padLeft = Number.parseFloat(shellStyles.paddingLeft) || 0;
        const shellLeft = shell.getBoundingClientRect().left;
        const swiperLeft = el.getBoundingClientRect().left;

        return Math.max(0, Math.round(shellLeft + padLeft - swiperLeft));
    }

    const parsed = Number(raw);

    return Number.isFinite(parsed) ? parsed : 0;
};

/**
 * Swiper loop needs enough slide width to fill ~2× the viewport.
 * When short, duplicate the original slides once (×2) before init.
 */
const ensureEnoughLoopSlides = (el, spaceBetween) => {
    const wrapper = el.querySelector('.swiper-wrapper');

    if (! wrapper || wrapper.dataset.loopDuplicated === 'true') {
        return;
    }

    const slides = Array.from(wrapper.children);

    if (slides.length === 0) {
        return;
    }

    const slidesWidth = slides.reduce((sum, slide, index) => {
        return sum + slide.offsetWidth + (index > 0 ? spaceBetween : 0);
    }, 0);

    // Need roughly two track lengths for loop + clones to work with slidesPerView: auto.
    if (slidesWidth >= el.offsetWidth * 2) {
        return;
    }

    slides.forEach((slide) => {
        const clone = slide.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        clone.dataset.entranceClone = 'true';
        clone.querySelectorAll('a, button, [tabindex]').forEach((node) => {
            node.setAttribute('tabindex', '-1');
        });
        wrapper.appendChild(clone);
    });

    wrapper.dataset.loopDuplicated = 'true';
};

const initSwipers = (root = document) => {
    root.querySelectorAll('[data-swiper]').forEach((el) => {
        if (el.swiper) {
            return;
        }

        const container = el.closest('[data-swiper-root]') ?? el.parentElement;
        const spaceBetween = Number(el.dataset.spaceBetween ?? 20);
        const slidesOffsetBefore = resolveSlidesOffset(el, 'slidesOffsetBefore');
        const slidesOffsetAfter = resolveSlidesOffset(el, 'slidesOffsetAfter');

        const rewind = el.hasAttribute('data-rewind') && el.dataset.rewind !== 'false';
        // Loop + slidesOffsetBefore prepends last-slide clones into the left gutter (stories).
        const shellOffset = el.dataset.slidesOffsetBefore === 'shell' || el.dataset.slidesOffsetAfter === 'shell';
        const loop = el.hasAttribute('data-loop') && el.dataset.loop !== 'false' && ! rewind && ! shellOffset;

        if (loop) {
            ensureEnoughLoopSlides(el, spaceBetween);
        }

        const swiper = new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 'auto',
            spaceBetween,
            slidesOffsetBefore,
            slidesOffsetAfter,
            loop,
            rewind,
            loopAdditionalSlides: loop ? 2 : 0,
            watchOverflow: ! loop,
            navigation: {
                prevEl: container?.querySelector('[data-swiper-prev]') ?? null,
                nextEl: container?.querySelector('[data-swiper-next]') ?? null,
            },
        });

        if (el.dataset.slidesOffsetBefore === 'shell' || el.dataset.slidesOffsetAfter === 'shell') {
            const syncShellOffsets = () => {
                swiper.params.slidesOffsetBefore = resolveSlidesOffset(el, 'slidesOffsetBefore');
                swiper.params.slidesOffsetAfter = resolveSlidesOffset(el, 'slidesOffsetAfter');
                swiper.update();
            };

            window.addEventListener('resize', syncShellOffsets);
        }
    });
};

const initSiteEntrance = () => {
    const hasHero = Boolean(document.querySelector('[data-hero-entrance]'));
    const header = initHeaderEntrance(document, { external: hasHero });

    if (! hasHero) {
        return;
    }

    const bg = initHeroBg(document, { autoPlay: false });

    void startHeroEntrance(document, { bg, header });
};

document.addEventListener('DOMContentLoaded', () => {
    initSwipers();
    initSiteEntrance();
    initSectionEntrance();
});

document.addEventListener('livewire:navigated', () => {
    destroySectionEntrance();
    initSwipers();
    initSiteEntrance();
    initSectionEntrance();
});
