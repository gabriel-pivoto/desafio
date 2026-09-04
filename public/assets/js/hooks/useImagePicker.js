import { clear, el } from '../helpers/element.js';

const DEFAULT_HINT = 'JPG, PNG, WEBP ou GIF de até 2 MB.';
const MAX_BYTES = 2 * 1024 * 1024;

export function useImagePicker({ input, preview, name, removeButton, pickButton, onError }) {
    let objectUrl = null;
    let removed = false;

    function release() {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
    }

    function show(source, label) {
        clear(preview);
        preview.append(source ? el('img', { src: source, alt: '' }) : document.createTextNode('🖼️'));
        name.textContent = label;
        removeButton.hidden = !source;
    }

    function reset(currentPath) {
        release();
        removed = false;
        input.value = '';
        show(currentPath || null, currentPath ? 'Imagem atual da carta.' : DEFAULT_HINT);
    }

    pickButton.addEventListener('click', () => input.click());

    input.addEventListener('change', () => {
        const file = input.files[0];

        if (!file) {
            return;
        }

        if (file.size > MAX_BYTES) {
            input.value = '';
            onError('A imagem deve ter no máximo 2 MB.');

            return;
        }

        release();
        objectUrl = URL.createObjectURL(file);
        removed = false;
        show(objectUrl, file.name);
    });

    removeButton.addEventListener('click', () => {
        release();
        input.value = '';
        removed = true;
        show(null, DEFAULT_HINT);
    });

    return {
        reset,
        release,
        file: () => input.files[0] || null,
        wasRemoved: () => removed,
    };
}
