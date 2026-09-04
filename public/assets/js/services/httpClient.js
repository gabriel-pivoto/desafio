const BASE_URL = '/api';

let csrfToken = '';

export function setCsrfToken(token) {
    csrfToken = token || '';
}

export class ApiError extends Error {
    constructor(message, status, errors) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.errors = errors || {};
    }

    get isValidation() {
        return this.status === 422 && Object.keys(this.errors).length > 0;
    }

    get isUnauthenticated() {
        return this.status === 401;
    }
}

function buildUrl(path, query) {
    const url = new URL(BASE_URL + path, window.location.origin);

    Object.entries(query || {}).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            url.searchParams.set(key, value);
        }
    });

    return url.toString();
}

function buildOptions(method, body, signal) {
    const options = { method, signal, headers: {}, credentials: 'same-origin' };

    if (csrfToken) {
        options.headers['X-CSRF-Token'] = csrfToken;
    }

    if (body instanceof FormData) {
        options.body = body;
    } else if (body !== null) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    return options;
}

async function parse(response) {
    if (response.status === 204) {
        return null;
    }

    const raw = await response.text();

    if (!raw) {
        return {};
    }

    try {
        return JSON.parse(raw);
    } catch {
        throw new ApiError('O servidor devolveu uma resposta inesperada.', response.status, {});
    }
}

export async function request(path, { method = 'GET', body = null, query = null, signal = null } = {}) {
    let response;

    try {
        response = await fetch(buildUrl(path, query), buildOptions(method, body, signal));
    } catch (error) {
        if (error.name === 'AbortError') {
            throw error;
        }

        throw new ApiError('Não foi possível falar com o servidor. Verifique sua conexão.', 0, {});
    }

    const payload = await parse(response);

    if (!response.ok) {
        throw new ApiError(payload.message || 'Algo deu errado.', response.status, payload.errors);
    }

    return payload;
}
