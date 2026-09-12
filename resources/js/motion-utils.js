const REDUCED_MOTION = '(prefers-reduced-motion: reduce)';

/**
 * @param {number} value
 * @param {number} min
 * @param {number} max
 */
export const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

/**
 * Map global progress into a local 0–1 within [start, end].
 *
 * @param {number} progress
 * @param {number} start
 * @param {number} end
 */
export const mapRange = (progress, start, end) => {
    if (end <= start) {
        return progress >= end ? 1 : 0;
    }

    return clamp((progress - start) / (end - start));
};

/**
 * @param {number} t
 */
export const easeOutCubic = (t) => 1 - (1 - t) ** 3;

export const prefersReducedMotion = () => window.matchMedia(REDUCED_MOTION).matches;

/**
 * Center-out stagger order indices for n items.
 *
 * @param {number} count
 * @returns {number[]}
 */
export const centerOutOrder = (count) => {
    if (count <= 0) {
        return [];
    }

    const order = [];
    const mid = Math.floor((count - 1) / 2);

    order.push(mid);

    for (let offset = 1; order.length < count; offset += 1) {
        const right = mid + offset;
        const left = mid - offset;

        if (count % 2 === 0 && offset === 1 && mid + 1 < count && ! order.includes(mid + 1)) {
            order.push(mid + 1);
        }

        if (left >= 0 && ! order.includes(left)) {
            order.push(left);
        }

        if (right < count && ! order.includes(right)) {
            order.push(right);
        }
    }

    return order;
};
