const API_SCRIPT_ID = 'yandex-maps-api';

/** @type {Promise<typeof window.ymaps> | null} */
let apiPromise = null;

/**
 * @param {string} apiKey
 * @returns {Promise<typeof window.ymaps>}
 */
const loadYmaps = (apiKey) => {
    if (window.ymaps?.ready) {
        return new Promise((resolve) => {
            window.ymaps.ready(() => resolve(window.ymaps));
        });
    }

    if (apiPromise) {
        return apiPromise;
    }

    apiPromise = new Promise((resolve, reject) => {
        const existing = document.getElementById(API_SCRIPT_ID);

        if (existing) {
            existing.addEventListener('load', () => {
                window.ymaps.ready(() => resolve(window.ymaps));
            });
            existing.addEventListener('error', () => reject(new Error('Yandex Maps failed to load')));

            return;
        }

        const script = document.createElement('script');
        script.id = API_SCRIPT_ID;
        script.src = `https://api-maps.yandex.ru/2.1/?apikey=${encodeURIComponent(apiKey)}&lang=ru_RU`;
        script.async = true;
        script.onload = () => {
            window.ymaps.ready(() => resolve(window.ymaps));
        };
        script.onerror = () => reject(new Error('Yandex Maps failed to load'));
        document.head.appendChild(script);
    });

    return apiPromise;
};

/**
 * @param {string} value
 * @returns {string}
 */
const escapeHtml = (value) =>
    value
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');

/**
 * @param {string} value
 * @returns {string}
 */
const escapeAttr = (value) => escapeHtml(value).replaceAll('`', '&#96;');

/**
 * @param {typeof window.ymaps} ymaps
 * @param {{ label: string, logoUrl: string }} options
 */
const buildPlacemarkLayout = (ymaps, { label, logoUrl }) => {
    const mark = logoUrl
        ? `<img class="site-yandex-map-pin-logo" src="${escapeAttr(logoUrl)}" alt="" width="120" height="40" decoding="async">`
        : `<span class="site-yandex-map-pin-text">${escapeHtml(label || 'ТЕТРИ')}</span>`;

    return ymaps.templateLayoutFactory.createClass(
        `<div class="site-yandex-map-pin">
            <div class="site-yandex-map-pin-head">
                ${mark}
            </div>
            <span class="site-yandex-map-pin-stem" aria-hidden="true"></span>
        </div>`,
    );
};

/**
 * @param {HTMLElement} el
 */
const initMapElement = async (el) => {
    if (el.dataset.yandexMapReady === 'true') {
        return;
    }

    const lat = Number.parseFloat(el.dataset.lat ?? '');
    const lng = Number.parseFloat(el.dataset.lng ?? '');
    const apiKey = el.dataset.apikey ?? '';
    const label = (el.dataset.label ?? '').trim();
    const logoUrl = (el.dataset.logo ?? '').trim();

    if (! Number.isFinite(lat) || ! Number.isFinite(lng) || apiKey === '') {
        return;
    }

    el.dataset.yandexMapReady = 'true';

    try {
        const ymaps = await loadYmaps(apiKey);
        const map = new ymaps.Map(
            el,
            {
                center: [lat, lng],
                zoom: 16,
                controls: [],
            },
            {
                suppressMapOpenBlock: true,
            },
        );

        const placemark = new ymaps.Placemark(
            [lat, lng],
            {
                hintContent: label || 'ТЕТРИ',
            },
            {
                iconLayout: buildPlacemarkLayout(ymaps, { label, logoUrl }),
                iconOffset: [0, 0],
                iconShape: {
                    type: 'Rectangle',
                    coordinates: [
                        [-48, -64],
                        [48, 0],
                    ],
                },
            },
        );

        map.geoObjects.add(placemark);
    } catch {
        el.dataset.yandexMapReady = 'false';
    }
};

export const initYandexMaps = () => {
    document.querySelectorAll('[data-yandex-map]').forEach((el) => {
        if (el instanceof HTMLElement) {
            void initMapElement(el);
        }
    });
};
