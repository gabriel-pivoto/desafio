export function useUrlState(defaults, omitted = []) {
    function read() {
        const params = new URLSearchParams(window.location.search);
        const state = { ...defaults };

        Object.keys(defaults).forEach((key) => {
            if (!params.has(key)) {
                return;
            }

            const raw = params.get(key);
            state[key] = typeof defaults[key] === 'number' ? Number(raw) || defaults[key] : raw;
        });

        return state;
    }

    function write(state) {
        const params = new URLSearchParams();

        Object.entries(state).forEach(([key, value]) => {
            if (omitted.includes(key) || value === '' || value === null || value === defaults[key]) {
                return;
            }

            params.set(key, value);
        });

        const query = params.toString();
        window.history.replaceState({}, '', query ? `?${query}` : window.location.pathname);
    }

    return { read, write };
}
