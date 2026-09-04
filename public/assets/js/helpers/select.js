import { clear, el } from './element.js';

export function fillSelect(select, items, placeholder, selected = '') {
    clear(select);
    select.append(el('option', { value: '', text: placeholder }));

    items.forEach((item) => {
        select.append(el('option', { value: String(item.id), text: item.name }));
    });

    select.value = selected ? String(selected) : '';
}
