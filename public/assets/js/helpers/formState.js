import { $ } from './dom.js';

export function createFormState(fields, alertNode) {
    function clearField(field) {
        $(`#field-${field}`)?.classList.remove('has-error');

        const message = $(`#error-${field}`);

        if (message) {
            message.textContent = '';
        }
    }

    function clearAll() {
        if (alertNode) {
            alertNode.hidden = true;
            alertNode.textContent = '';
        }

        fields.forEach(clearField);
    }

    function showAlert(message) {
        if (alertNode && message) {
            alertNode.textContent = message;
            alertNode.hidden = false;
        }
    }

    function showErrors(errors, message = '') {
        const first = Object.keys(errors)[0];

        Object.entries(errors).forEach(([field, text]) => {
            const wrapper = $(`#field-${field}`);
            const target = $(`#error-${field}`);

            if (wrapper && target) {
                wrapper.classList.add('has-error');
                target.textContent = text;
            }
        });

        showAlert(message);

        if (first) {
            $(`#${first}`)?.focus();
        }
    }

    function watchFields() {
        fields.forEach((field) => {
            const control = $(`#${field}`);

            control?.addEventListener('input', () => clearField(field));
            control?.addEventListener('change', () => clearField(field));
        });
    }

    return { clearAll, clearField, showAlert, showErrors, watchFields };
}
