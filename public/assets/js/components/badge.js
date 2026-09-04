import { el } from '../helpers/element.js';

export function gameBadge(game, extraClass = '') {
    return el('span', {
        class: `badge badge--game ${extraClass}`.trim(),
        dataset: { game: game.slug },
        text: game.name,
    });
}

export function rarityBadge(rarity) {
    return el('span', {
        class: 'badge badge--rarity',
        dataset: { rarity: rarity.slug },
        text: rarity.name,
    });
}
