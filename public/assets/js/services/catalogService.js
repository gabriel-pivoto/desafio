import { request } from './httpClient.js';

export const catalogService = {
    catalog: () => request('/catalog'),
    editions: (game, signal) => request('/editions', { query: { game }, signal }),
};
