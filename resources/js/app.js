import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';

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

const initSwipers = (root = document) => {
    root.querySelectorAll('[data-swiper]').forEach((el) => {
        if (el.swiper) {
            return;
        }

        const container = el.closest('[data-swiper-root]') ?? el.parentElement;
        const spaceBetween = Number(el.dataset.spaceBetween ?? 20);
        const slidesOffsetBefore = resolveSlidesOffset(el, 'slidesOffsetBefore');
        const slidesOffsetAfter = resolveSlidesOffset(el, 'slidesOffsetAfter');

        const swiper = new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 'auto',
            spaceBetween,
            slidesOffsetBefore,
            slidesOffsetAfter,
            watchOverflow: true,
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

document.addEventListener('DOMContentLoaded', () => {
    initSwipers();
});

document.addEventListener('livewire:navigated', () => {
    initSwipers();
});
