export function initials(name) {
    return String(name || '?')
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
}

export function formatDate(value) {
    if (!value) {
        return '';
    }

    const date = new Date(String(value).replace(' ', 'T'));

    if (Number.isNaN(date.getTime())) {
        return '';
    }

    return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

export function registeredOn(value) {
    const date = formatDate(value);

    return date === '' ? '' : `Cadastrada em ${date}`;
}

export function joinWithDot(...parts) {
    return parts.filter(Boolean).join(' · ');
}
