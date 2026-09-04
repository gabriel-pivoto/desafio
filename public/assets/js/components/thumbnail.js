import { el } from '../helpers/element.js';

export function thumbnail(card, className = 'thumb') {
    if (card.image_path) {
        return el('img', {
            class: className,
            src: card.image_path,
            alt: `Imagem da carta ${card.name_en}`,
            loading: 'lazy',
        });
    }

    return el('span', { class: `${className} thumb--empty`, 'aria-hidden': 'true', text: '🃏' });
}
