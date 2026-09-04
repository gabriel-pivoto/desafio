import { $, setPressed } from '../helpers/dom.js';
import { useUrlState } from './useUrlState.js';

export const FILTER_DEFAULTS = {
    search: '',
    game_id: '',
    rarity_id: '',
    sort: 'recent',
    page: 1,
    view: 'table',
};

export function useCardFilters(onChange) {
    const urlState = useUrlState(FILTER_DEFAULTS);

    let state = { ...FILTER_DEFAULTS };

    function applyToControls() {
        $('#filter-search').value = state.search;
        $('#filter-game').value = state.game_id;
        $('#filter-rarity').value = state.rarity_id;
        $('#filter-sort').value = state.sort;
        setPressed($('#view-table'), state.view === 'table');
        setPressed($('#view-grid'), state.view === 'grid');
    }

    function current() {
        return state;
    }

    function hasActive() {
        return Boolean(
            state.search || state.game_id || state.rarity_id || state.sort !== FILTER_DEFAULTS.sort,
        );
    }

    function update(patch, { resetPage = true } = {}) {
        state = { ...state, ...patch, ...(resetPage ? { page: 1 } : {}) };
        urlState.write(state);
        onChange();
    }

    function reset() {
        state = { ...FILTER_DEFAULTS, view: state.view };
        applyToControls();
        urlState.write(state);
        onChange();
    }

    function setView(view) {
        if (view === state.view) {
            return false;
        }

        state = { ...state, view };
        applyToControls();
        urlState.write(state);

        return true;
    }

    function restore() {
        state = urlState.read();
        applyToControls();
    }

    function query(perPage) {
        return {
            search: state.search,
            game_id: state.game_id,
            rarity_id: state.rarity_id,
            sort: state.sort,
            page: state.page,
            per_page: perPage,
        };
    }

    return { current, hasActive, update, reset, setView, restore, query };
}
