import { easeOutCubic, mapRange } from './motion-utils';

export const MORPH_CLIP_START = 'inset(50% 50% 50% 50% round 50%)';

/**
 * @param {number} [radius=32]
 */
export const morphClipFinal = (radius = 32) => `inset(0 round ${radius}px)`;

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
 * Clip-path morph math (hero story language): point → circle → rounded → full.
 *
 * @param {number} local 0–1
 * @param {{ width: number, height: number, finalRadius?: number }} box
 * @returns {{ clipPath: string, opacity: number }}
 */
export const morphClipProgress = (local, box) => {
    const w = box.width || 1;
    const h = box.height || 1;
    const finalRadius = box.finalRadius ?? 32;

    if (local <= 0) {
        return { clipPath: MORPH_CLIP_START, opacity: 0 };
    }

    if (local >= 1) {
        return { clipPath: morphClipFinal(finalRadius), opacity: 1 };
    }

    const minSide = Math.min(w, h);

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

    return {
        clipPath: `inset(${insetY}px ${insetX}px ${insetY}px ${insetX}px round ${radius}px)`,
        opacity,
    };
};

/**
 * Apply morph styles to an element. Pass cached box to avoid layout thrash.
 *
 * @param {HTMLElement} el
 * @param {number} local
 * @param {{ width: number, height: number, finalRadius?: number }} [box]
 */
export const applyMorphClip = (el, local, box) => {
    const resolved = box ?? {
        width: el.offsetWidth || 1,
        height: el.offsetHeight || 1,
        finalRadius: Number.parseFloat(getComputedStyle(el).borderTopLeftRadius) || 32,
    };
    const { clipPath, opacity } = morphClipProgress(local, resolved);

    el.style.clipPath = clipPath;
    el.style.opacity = String(opacity);
};

/**
 * Snapshot size + radius once before animating.
 *
 * @param {HTMLElement} el
 * @returns {{ width: number, height: number, finalRadius: number }}
 */
export const cacheMorphBox = (el) => {
    const radiusAttr = el.dataset.entranceRadius;
    const fromAttr = radiusAttr !== undefined && radiusAttr !== ''
        ? Number.parseFloat(radiusAttr)
        : Number.NaN;

    return {
        width: el.offsetWidth || 1,
        height: el.offsetHeight || 1,
        finalRadius: Number.isFinite(fromAttr)
            ? fromAttr
            : (Number.parseFloat(getComputedStyle(el).borderTopLeftRadius) || 32),
    };
};
