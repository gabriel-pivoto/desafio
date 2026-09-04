import { clear, el } from '../helpers/element.js';

const WINDOW_LIMIT = 7;

function pageNumbers(page, pages) {
    if (pages <= WINDOW_LIMIT) {
        return Array.from({ length: pages }, (_, index) => index + 1);
    }

    const wanted = new Set([1, pages, page, page - 1, page + 1]);
    const sorted = [...wanted].filter((number) => number >= 1 && number <= pages).sort((a, b) => a - b);
    const output = [];

    sorted.forEach((number, index) => {
        if (index > 0 && number - sorted[index - 1] > 1) {
            output.push('gap');
        }

        output.push(number);
    });

    return output;
}

function pageButton(label, attributes, onClick) {
    return el('button', { class: 'pagination__page', type: 'button', text: label, ...attributes, onClick });
}

export function renderPagination(node, { page, pages, total }, onGoTo) {
    clear(node);

    if (pages <= 1) {
        node.hidden = true;

        return;
    }

    node.hidden = false;

    const buttons = el('div', { class: 'pagination__pages' });

    buttons.append(pageButton('‹', { 'aria-label': 'Página anterior', disabled: page <= 1 }, () => onGoTo(page - 1)));

    pageNumbers(page, pages).forEach((number) => {
        if (number === 'gap') {
            buttons.append(el('span', { class: 'pagination__gap', 'aria-hidden': 'true', text: '…' }));

            return;
        }

        buttons.append(pageButton(
            String(number),
            { 'aria-current': number === page ? 'page' : null },
            () => onGoTo(number),
        ));
    });

    buttons.append(pageButton('›', { 'aria-label': 'Próxima página', disabled: page >= pages }, () => onGoTo(page + 1)));

    node.append(el('span', { text: `Página ${page} de ${pages} · ${total} cartas` }), buttons);
}
