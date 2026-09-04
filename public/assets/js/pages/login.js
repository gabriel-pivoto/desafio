import { ApiError } from '../services/httpClient.js';
import { authService } from '../services/authService.js';
import { $, redirectTo } from '../helpers/dom.js';
import { setButtonLoading } from '../helpers/button.js';
import { createFormState } from '../helpers/formState.js';
import { validateCredentials } from '../helpers/validation.js';
import { useSession } from '../hooks/useSession.js';

const PORTAL_URL = '/cartas.html';

const form = $('#login-form');
const submitButton = $('#login-submit');
const emailInput = $('#email');
const passwordInput = $('#password');
const toggleButton = $('#toggle-password');

const state = createFormState(['email', 'password'], $('#login-alert'));
const session = useSession();

const ready = session.load()
    .then((user) => {
        if (user) {
            redirectTo(PORTAL_URL);
        }
    })
    .catch(() => {
        state.showAlert('Não foi possível falar com o servidor. Confira se os containers estão no ar.');
    });

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    state.clearAll();

    const errors = validateCredentials({ email: emailInput.value, password: passwordInput.value });

    if (Object.keys(errors).length > 0) {
        state.showErrors(errors);

        return;
    }

    setButtonLoading(submitButton, true, 'Entrando...');

    try {
        await ready;
        await authService.login(emailInput.value.trim(), passwordInput.value);
        redirectTo(PORTAL_URL);
    } catch (error) {
        setButtonLoading(submitButton, false);

        if (error instanceof ApiError && error.isValidation) {
            state.showErrors(error.errors, error.message);
        } else {
            state.showAlert(error.message || 'Não foi possível entrar.');
        }

        passwordInput.focus();
        passwordInput.select();
    }
});

toggleButton.addEventListener('click', () => {
    const showing = passwordInput.type === 'text';

    passwordInput.type = showing ? 'password' : 'text';
    toggleButton.textContent = showing ? 'Mostrar' : 'Ocultar';
    toggleButton.setAttribute('aria-pressed', String(!showing));
    passwordInput.focus();
});

$('#fill-demo').addEventListener('click', () => {
    emailInput.value = 'admin@cartas.local';
    passwordInput.value = 'admin123';
    state.clearAll();
    submitButton.focus();
});
