import { ApiError } from '../services/httpClient.js';
import { authService } from '../services/authService.js';
import { catalogService } from '../services/catalogService.js';
import { cardService } from '../services/cardService.js';
import { $, redirectTo } from '../helpers/dom.js';
import { el } from '../helpers/element.js';
import { initials } from '../helpers/format.js';
import { setButtonLoading } from '../helpers/button.js';
import { useAbortableRequest } from '../hooks/useAbortableRequest.js';
import { useCardFilters } from '../hooks/useCardFilters.js';
import { useDebounce } from '../hooks/useDebounce.js';
import { useSession } from '../hooks/useSession.js';
import { createCardResults } from '../components/cardResults.js';
import { renderPagination } from '../components/pagination.js';
import { renderSummary } from '../components/summaryPanel.js';
import { createCardFormDialog } from '../components/cardFormDialog.js';
import { createConfirmDialog } from '../components/confirmDialog.js';
import { toast } from '../components/toast.js';

const PER_PAGE = 10;

const pagination = $('#pagination');
const resultCount = $('#result-count');
const clearFiltersButton = $('#clear-filters');

const listRequest = useAbortableRequest();
const filters = useCardFilters(() => loadCards());

let lastItems = [];
let cardForm = null;
let confirmDialog = null;

const results = createCardResults({
    node: $('#results'),
    actions: {
        onEdit: (card) => cardForm.open(card),
        onDelete: (card) => confirmDialog.open(card),
    },
    onRetry: () => loadCards(),
    onResetFilters: () => filters.reset(),
    onCreate: () => cardForm.open(),
});

function showEmpty() {
    lastItems = [];
    pagination.hidden = true;
    resultCount.textContent = 'Nenhuma carta encontrada.';

    if (filters.hasActive()) {
        results.emptyWithFilters();
    } else {
        results.emptyCollection();
    }
}

function showPage(data) {
    const first = (data.page - 1) * data.per_page + 1;

    resultCount.textContent = `Mostrando ${first}-${first + data.items.length - 1} de ${data.total} cartas.`;
    lastItems = data.items;
    results.items(data.items, filters.current().view);
    renderPagination(pagination, data, (page) => filters.update({ page }, { resetPage: false }));
}

function handleFailure(error, fallback) {
    if (error.name === 'AbortError') {
        return;
    }

    if (error instanceof ApiError && error.isUnauthenticated) {
        redirectTo('/');

        return;
    }

    fallback(error);
}

async function loadCards() {
    results.skeleton();
    pagination.hidden = true;
    resultCount.textContent = 'Carregando cartas...';
    clearFiltersButton.hidden = !filters.hasActive();

    try {
        const data = await listRequest.run((signal) => cardService.list(filters.query(PER_PAGE), signal));

        renderSummary($('#summary'), data.summary);

        if (data.items.length === 0) {
            showEmpty();

            return;
        }

        showPage(data);
    } catch (error) {
        handleFailure(error, () => {
            resultCount.textContent = '';
            results.failure(error.message);
        });
    }
}

async function removeCard(card) {
    try {
        const result = await cardService.remove(card.id);
        toast(result.message || 'Carta excluída.', 'success');
        loadCards();
    } catch (error) {
        handleFailure(error, () => toast(error.message || 'Não foi possível excluir a carta.', 'error'));
    }
}

function fillFilterOptions(catalog) {
    catalog.games.forEach((game) => {
        $('#filter-game').append(el('option', { value: String(game.id), text: game.name }));
    });

    catalog.rarities.forEach((rarity) => {
        $('#filter-rarity').append(el('option', { value: String(rarity.id), text: rarity.name }));
    });
}

function switchView(view) {
    if (filters.setView(view) && lastItems.length > 0) {
        results.items(lastItems, view);
    }
}

function bindControls() {
    $('#new-card').addEventListener('click', () => cardForm.open());
    clearFiltersButton.addEventListener('click', () => filters.reset());
    $('#view-table').addEventListener('click', () => switchView('table'));
    $('#view-grid').addEventListener('click', () => switchView('grid'));

    $('#filter-search').addEventListener('input', useDebounce((event) => {
        filters.update({ search: event.target.value.trim() });
    }));
    $('#filter-game').addEventListener('change', (event) => filters.update({ game_id: event.target.value }));
    $('#filter-rarity').addEventListener('change', (event) => filters.update({ rarity_id: event.target.value }));
    $('#filter-sort').addEventListener('change', (event) => filters.update({ sort: event.target.value }));

    $('#logout').addEventListener('click', async () => {
        setButtonLoading($('#logout'), true, 'Saindo...');

        try {
            await authService.logout();
        } finally {
            redirectTo('/');
        }
    });
}

function showUser(user) {
    $('#user-name').textContent = user.name;
    $('#user-email').textContent = user.email;
    $('#user-avatar').textContent = initials(user.name);
}

(async function start() {
    let user;

    try {
        user = await useSession().load();
    } catch {
        results.unavailable();

        return;
    }

    if (!user) {
        redirectTo('/');

        return;
    }

    showUser(user);

    const catalog = await catalogService.catalog();
    fillFilterOptions(catalog);

    cardForm = createCardFormDialog({ catalog, onSaved: loadCards });
    confirmDialog = createConfirmDialog({ onConfirm: removeCard });

    bindControls();
    filters.restore();
    loadCards();
})();
