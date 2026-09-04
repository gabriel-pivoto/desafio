import { el } from '../helpers/element.js';
import { formatDate } from '../helpers/format.js';
import { gameBadge, rarityBadge } from './badge.js';
import { cardActions } from './cardActions.js';
import { thumbnail } from './thumbnail.js';

const COLUMNS = ['Carta', 'Card game', 'Edição', 'Raridade', 'Criada em'];

function names(card) {
    return el('div', { class: 'card-cell__names' }, [
        el('strong', { text: card.name_en }),
        card.name_pt
            ? el('span', { text: card.name_pt })
            : el('em', { text: 'Sem nome em português' }),
    ]);
}

function row(card, actions) {
    return el('tr', {}, [
        el('td', { class: 'card-cell-td', 'data-label': 'Carta' }, [
            el('div', { class: 'card-cell' }, [thumbnail(card), names(card)]),
        ]),
        el('td', { 'data-label': 'Card game' }, [gameBadge(card.game)]),
        el('td', { 'data-label': 'Edição' }, [el('span', { text: card.edition.name })]),
        el('td', { 'data-label': 'Raridade' }, [rarityBadge(card.rarity)]),
        el('td', { 'data-label': 'Criada em', text: formatDate(card.created_at) }),
        el('td', { 'data-label': 'Ações' }, [
            el('div', { class: 'row-actions' }, cardActions(card, actions)),
        ]),
    ]);
}

export function cardTable(items, actions) {
    const head = el('thead', {}, [
        el('tr', {}, [
            ...COLUMNS.map((label) => el('th', { scope: 'col', text: label })),
            el('th', { scope: 'col' }, el('span', { class: 'sr-only', text: 'Ações' })),
        ]),
    ]);

    return el('table', { class: 'table' }, [
        head,
        el('tbody', {}, items.map((card) => row(card, actions))),
    ]);
}
