import { request } from './httpClient.js';

export const authService = {
    session: () => request('/auth/me'),
    login: (email, password) => request('/auth/login', { method: 'POST', body: { email, password } }),
    logout: () => request('/auth/logout', { method: 'POST', body: {} }),
};
