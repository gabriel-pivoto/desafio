import { el } from './element.js';

export function setButtonLoading(button, loading, loadingLabel = 'Salvando...') {
    if (!button) {
        return;
    }

    const label = button.querySelector('.btn__label') || button;

    if (loading) {
        button.dataset.originalLabel = label.textContent;
        label.textContent = loadingLabel;
        button.classList.add('is-loading');
        button.disabled = true;

        if (!button.querySelector('.btn__spinner')) {
            button.prepend(el('span', { class: 'btn__spinner', 'aria-hidden': 'true' }));
        }

        return;
    }

    if (button.dataset.originalLabel) {
        label.textContent = button.dataset.originalLabel;
    }

    button.classList.remove('is-loading');
    button.disabled = false;
    button.querySelector('.btn__spinner')?.remove();
}
