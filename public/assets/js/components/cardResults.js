import { clear } from '../helpers/element.js';
import { cardGallery } from './cardGallery.js';
import { cardTable } from './cardTable.js';
import { skeletonList, stateButton, stateMessage } from './listStates.js';

export function createCardResults({ node, actions, onRetry, onResetFilters, onCreate }) {
    function reset(isGrid) {
        clear(node);
        node.classList.toggle('is-grid', isGrid);
    }

    function skeleton() {
        reset(false);
        skeletonList().forEach((row) => node.append(row));
    }

    function items(list, view) {
        reset(view === 'grid');
        node.append(view === 'grid' ? cardGallery(list, actions) : cardTable(list, actions));
    }

    function emptyWithFilters() {
        reset(false);
        node.append(stateMessage({
            icon: '🔍',
            title: 'Nenhuma carta com esses filtros',
            text: 'Tente outro termo de busca ou volte para a lista completa.',
            actions: [stateButton('Limpar filtros', onResetFilters)],
        }));
    }

    function emptyCollection() {
        reset(false);
        node.append(stateMessage({
            icon: '🃏',
            title: 'O acervo está vazio',
            text: 'Cadastre a primeira carta para começar.',
            actions: [stateButton('Cadastrar carta', onCreate, 'btn--confirm')],
        }));
    }

    function failure(message) {
        reset(false);
        node.append(stateMessage({
            icon: '⚠️',
            title: 'Não foi possível carregar as cartas',
            text: message,
            actions: [stateButton('Tentar novamente', onRetry)],
        }));
    }

    function unavailable() {
        reset(false);
        node.append(stateMessage({
            icon: '⚠️',
            title: 'Servidor indisponível',
            text: 'Confira se os containers estão no ar e recarregue a página.',
        }));
    }

    return { skeleton, items, emptyWithFilters, emptyCollection, failure, unavailable };
}
