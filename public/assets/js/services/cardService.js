import { request } from './httpClient.js';

export const cardService = {
    list: (filters, signal) => request('/cards', { query: filters, signal }),
    create: (formData) => request('/cards', { method: 'POST', body: formData }),
    update: (id, formData) => {
        formData.set('_method', 'PUT');

        return request(`/cards/${id}`, { method: 'POST', body: formData });
    },
    remove: (id) => request(`/cards/${id}`, { method: 'DELETE', body: {} }),
};
