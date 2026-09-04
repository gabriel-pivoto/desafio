import { catalogService } from '../services/catalogService.js';
import { fillSelect } from '../helpers/select.js';
import { useAbortableRequest } from './useAbortableRequest.js';

const MIN_LOADING_MS = 320;

const hold = (ms) => (ms > 0 ? new Promise((resolve) => window.setTimeout(resolve, ms)) : Promise.resolve());

export function useEditionSelect({ select, hint, loading, onError }) {
    const requests = useAbortableRequest();

    function setState(state, { editions = [], selected = '' } = {}) {
        loading.hidden = state !== 'loading';
        hint.hidden = state === 'loading';
        select.disabled = state !== 'ready';

        if (state === 'idle') {
            fillSelect(select, [], 'Selecione um card game primeiro');
            hint.textContent = 'A lista de edições é carregada conforme o card game escolhido.';
        }

        if (state === 'loading') {
            fillSelect(select, [], 'Carregando edições...');
        }

        if (state === 'error') {
            fillSelect(select, [], 'Não foi possível carregar');
            hint.textContent = 'Escolha o card game novamente para tentar mais uma vez.';
        }

        if (state === 'ready') {
            fillSelect(select, editions, 'Selecione a edição', selected);
            hint.textContent = `${editions.length} edições disponíveis para este card game.`;
        }
    }

    async function load(gameId, selected = '') {
        if (!gameId) {
            requests.abort();
            setState('idle');

            return;
        }

        setState('loading');
        const startedAt = Date.now();

        try {
            const editions = await requests.run(async (signal) => {
                const payload = await catalogService.editions(gameId, signal);
                await hold(MIN_LOADING_MS - (Date.now() - startedAt));

                return signal.aborted ? null : payload.editions;
            });

            if (editions !== null) {
                setState('ready', { editions, selected });
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                setState('error');
                onError(error);
            }
        }
    }

    return { load, reset: () => setState('idle'), abort: requests.abort };
}
