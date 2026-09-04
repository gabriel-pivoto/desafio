import { el } from '../helpers/element.js';

export function skeletonList(rows = 4) {
    return Array.from({ length: rows }, () => el('div', { class: 'skeleton-row' }, [
        el('span', { class: 'skeleton skeleton--thumb' }),
        el('span', { class: 'skeleton skeleton--line' }),
        el('span', { class: 'skeleton skeleton--short' }),
        el('span', { class: 'skeleton skeleton--short' }),
    ]));
}

export function stateMessage({ icon, title, text, actions = [] }) {
    return el('div', { class: 'state' }, [
        el('div', { class: 'state__icon', 'aria-hidden': 'true', text: icon }),
        el('p', { class: 'state__title', text: title }),
        el('p', { text }),
        actions.length > 0 ? el('div', { class: 'state__actions' }, actions) : null,
    ]);
}

export function stateButton(label, onClick, variant = 'btn--ghost') {
    return el('button', { class: `btn ${variant}`, type: 'button', text: label, onClick });
}
