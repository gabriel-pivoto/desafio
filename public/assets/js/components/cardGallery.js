import { el } from '../helpers/element.js';
import { joinWithDot, registeredOn } from '../helpers/format.js';
import { gameBadge, rarityBadge } from './badge.js';
import { cardActions } from './cardActions.js';
import { thumbnail } from './thumbnail.js';

function tile(card, actions) {
    return el('article', { class: 'card-tile' }, [
        el('div', { class: 'card-tile__art' }, [
            thumbnail(card, 'card-tile__image'),
            gameBadge(card.game, 'card-tile__game'),
        ]),
        el('div', { class: 'card-tile__body' }, [
            el('h3', { class: 'card-tile__name', text: card.name_en }),
            card.name_pt
                ? el('p', { class: 'card-tile__name-pt', text: card.name_pt })
                : el('p', { class: 'card-tile__name-pt' }, el('em', { text: 'Sem nome em português' })),
            rarityBadge(card.rarity),
            el('p', {
                class: 'card-tile__meta',
                text: joinWithDot(card.edition.name, registeredOn(card.created_at)),
            }),
        ]),
        el('div', { class: 'card-tile__actions' }, cardActions(card, actions)),
    ]);
}

export function cardGallery(items, actions) {
    return el('div', { class: 'grid' }, items.map((card) => tile(card, actions)));
}
