import { el } from '../helpers/element.js';

const ICONS = { success: '✓', error: '!', info: 'i' };

function container() {
    let node = document.querySelector('.toasts');

    if (!node) {
        node = el('div', { class: 'toasts', role: 'status', 'aria-live': 'polite' });
        document.body.append(node);
    }

    return node;
}

export function toast(message, type = 'success', timeout = 4200) {
    const node = el('div', { class: `toast toast--${type}` }, [
        el('span', { class: 'toast__icon', 'aria-hidden': 'true', text: ICONS[type] || ICONS.info }),
        el('span', { class: 'toast__text', text: message }),
        el('button', {
            class: 'toast__close',
            type: 'button',
            'aria-label': 'Fechar aviso',
            text: '×',
            onClick: () => node.remove(),
        }),
    ]);

    container().append(node);
    window.setTimeout(() => node.remove(), timeout);
}
