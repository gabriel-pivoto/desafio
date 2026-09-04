import { clear, el } from '../helpers/element.js';

function item(value, label, game = null) {
    return el('div', { class: 'summary__item', dataset: game ? { game } : {} }, [
        el('div', { class: 'summary__value', text: String(value) }),
        el('div', { class: 'summary__label', text: label }),
    ]);
}

export function renderSummary(node, summary) {
    clear(node);
    node.append(item(summary.total, 'Cartas no total'));

    summary.by_game.forEach((game) => {
        node.append(item(game.total, game.name, game.slug));
    });
}
