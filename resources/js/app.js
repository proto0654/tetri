import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';
import 'swiper/css';

const initSwipers = (root = document) => {
    root.querySelectorAll('[data-swiper]').forEach((el) => {
        if (el.swiper) {
            return;
        }

        const container = el.closest('[data-swiper-root]') ?? el.parentElement;
        const spaceBetween = Number(el.dataset.spaceBetween ?? 20);

        new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 'auto',
            spaceBetween,
            watchOverflow: true,
            navigation: {
                prevEl: container?.querySelector('[data-swiper-prev]') ?? null,
                nextEl: container?.querySelector('[data-swiper-next]') ?? null,
            },
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    initSwipers();
});

document.addEventListener('livewire:navigated', () => {
    initSwipers();
});
