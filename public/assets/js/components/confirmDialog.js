import { $ } from '../helpers/dom.js';
import { clear, el } from '../helpers/element.js';
import { joinWithDot, registeredOn } from '../helpers/format.js';
import { setButtonLoading } from '../helpers/button.js';
import { thumbnail } from './thumbnail.js';

export function createConfirmDialog({ onConfirm }) {
    const dialog = $('#confirm-dialog');
    const confirmButton = $('#confirm-delete');
    const cancelButton = $('#confirm-cancel');
    const details = $('#confirm-card');

    let pending = null;

    function open(card) {
        pending = card;
        clear(details);
        details.append(
            thumbnail(card),
            el('div', {}, [
                el('strong', { text: card.name_en }),
                el('div', {
                    class: 'confirm__card-meta',
                    text: joinWithDot(card.game.name, card.edition.name, registeredOn(card.created_at)),
                }),
            ]),
        );

        dialog.showModal();
        cancelButton.focus();
    }

    cancelButton.addEventListener('click', () => dialog.close());

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    confirmButton.addEventListener('click', async () => {
        if (!pending) {
            return;
        }

        setButtonLoading(confirmButton, true, 'Excluindo...');

        try {
            await onConfirm(pending);
            pending = null;
            dialog.close();
        } finally {
            setButtonLoading(confirmButton, false);
        }
    });

    return { open };
}
