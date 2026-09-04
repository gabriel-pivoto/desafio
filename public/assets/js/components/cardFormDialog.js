import { ApiError } from '../services/httpClient.js';
import { cardService } from '../services/cardService.js';
import { $, redirectTo } from '../helpers/dom.js';
import { setButtonLoading } from '../helpers/button.js';
import { createFormState } from '../helpers/formState.js';
import { fillSelect } from '../helpers/select.js';
import { validateCardForm } from '../helpers/validation.js';
import { useEditionSelect } from '../hooks/useEditionSelect.js';
import { useImagePicker } from '../hooks/useImagePicker.js';
import { toast } from './toast.js';

const FIELDS = ['name_en', 'name_pt', 'game_id', 'edition_id', 'rarity_id', 'image'];

export function createCardFormDialog({ catalog, onSaved }) {
    const dialog = $('#card-dialog');
    const form = $('#card-form');
    const submitButton = $('#card-submit');
    const gameSelect = $('#game_id');
    const raritySelect = $('#rarity_id');
    const editionSelect = $('#edition_id');

    const state = createFormState(FIELDS, $('#card-form-alert'));

    const editions = useEditionSelect({
        select: editionSelect,
        hint: $('#edition-hint'),
        loading: $('#edition-loading'),
        onError: (error) => toast(error.message || 'Não foi possível carregar as edições.', 'error'),
    });

    const image = useImagePicker({
        input: $('#image'),
        preview: $('#image-preview'),
        name: $('#image-name'),
        removeButton: $('#image-remove'),
        pickButton: $('#image-pick'),
        onError: (message) => state.showErrors({ image: message }),
    });

    let editing = null;

    fillSelect(gameSelect, catalog.games, 'Selecione o jogo');
    fillSelect(raritySelect, catalog.rarities, 'Selecione a raridade');
    state.watchFields();

    gameSelect.addEventListener('change', () => {
        editions.load(gameSelect.value);
        state.clearField('edition_id');
    });

    function buildPayload() {
        const payload = new FormData();

        payload.set('name_en', $('#name_en').value.trim());
        payload.set('name_pt', $('#name_pt').value.trim());
        payload.set('game_id', gameSelect.value);
        payload.set('edition_id', editionSelect.value);
        payload.set('rarity_id', raritySelect.value);

        const file = image.file();

        if (file) {
            payload.set('image', file);
        }

        if (image.wasRemoved()) {
            payload.set('remove_image', '1');
        }

        return payload;
    }

    async function submit(event) {
        event.preventDefault();
        state.clearAll();

        const errors = validateCardForm({
            nameEn: $('#name_en').value,
            gameId: gameSelect.value,
            editionId: editionSelect.value,
            rarityId: raritySelect.value,
        });

        if (Object.keys(errors).length > 0) {
            state.showErrors(errors, 'Revise os campos destacados.');

            return;
        }

        setButtonLoading(submitButton, true, 'Salvando...');

        try {
            const payload = buildPayload();
            const result = editing
                ? await cardService.update(editing.id, payload)
                : await cardService.create(payload);

            dialog.close();
            toast(result.message || 'Carta salva.', 'success');
            onSaved();
        } catch (error) {
            handleFailure(error);
        } finally {
            setButtonLoading(submitButton, false);
        }
    }

    function handleFailure(error) {
        if (error instanceof ApiError && error.isValidation) {
            state.showErrors(error.errors, error.message);

            return;
        }

        if (error instanceof ApiError && error.isUnauthenticated) {
            redirectTo('/');

            return;
        }

        state.showAlert(error.message || 'Não foi possível salvar a carta.');
    }

    function open(card = null) {
        editing = card;
        form.reset();
        state.clearAll();
        image.reset(card?.image_path);

        $('#card-dialog-title').textContent = card ? 'Editar carta' : 'Nova carta';
        $('#card-dialog-subtitle').textContent = card
            ? `Alterações em "${card.name_en}" valem assim que você salvar.`
            : 'Preencha os dados da carta. Só o nome em português é opcional.';
        submitButton.querySelector('.btn__label').textContent = card ? 'Salvar alterações' : 'Cadastrar carta';

        $('#name_en').value = card ? card.name_en : '';
        $('#name_pt').value = card?.name_pt ?? '';
        gameSelect.value = card ? String(card.game.id) : '';
        raritySelect.value = card ? String(card.rarity.id) : '';

        if (card) {
            editions.load(String(card.game.id), String(card.edition.id));
        } else {
            editions.reset();
        }

        dialog.showModal();
        $('#name_en').focus();
    }

    form.addEventListener('submit', submit);
    $('#card-cancel').addEventListener('click', () => dialog.close());
    $('#card-dialog-close').addEventListener('click', () => dialog.close());

    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    dialog.addEventListener('close', () => {
        editions.abort();
        image.release();
    });

    return { open };
}
