const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

export function isBlank(value) {
    return String(value ?? '').trim() === '';
}

export function isEmail(value) {
    return EMAIL_PATTERN.test(String(value).trim());
}

export function validateCredentials({ email, password }) {
    const errors = {};

    if (isBlank(email)) {
        errors.email = 'Informe o e-mail.';
    } else if (!isEmail(email)) {
        errors.email = 'Informe um e-mail válido.';
    }

    if (isBlank(password)) {
        errors.password = 'Informe a senha.';
    }

    return errors;
}

export function validateCardForm({ nameEn, gameId, editionId, rarityId }) {
    const errors = {};

    if (isBlank(nameEn)) {
        errors.name_en = 'Informe o nome da carta em inglês.';
    }

    if (isBlank(gameId)) {
        errors.game_id = 'Selecione o card game.';
    }

    if (isBlank(editionId)) {
        errors.edition_id = isBlank(gameId)
            ? 'Escolha o card game para liberar as edições.'
            : 'Selecione a edição da carta.';
    }

    if (isBlank(rarityId)) {
        errors.rarity_id = 'Selecione a raridade da carta.';
    }

    return errors;
}
