import { el } from '../helpers/element.js';

export function cardActions(card, { onEdit, onDelete }) {
    return [
        el('button', {
            class: 'icon-btn',
            type: 'button',
            'aria-label': `Editar ${card.name_en}`,
            onClick: () => onEdit(card),
        }, [el('span', { 'aria-hidden': 'true', text: '✎' }), 'Editar']),
        el('button', {
            class: 'icon-btn icon-btn--danger',
            type: 'button',
            'aria-label': `Excluir ${card.name_en}`,
            onClick: () => onDelete(card),
        }, [el('span', { 'aria-hidden': 'true', text: '🗑' }), 'Excluir']),
    ];
}
