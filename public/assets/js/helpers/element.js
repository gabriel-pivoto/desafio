const ATTRIBUTE_HANDLERS = {
    class: (node, value) => {
        node.className = value;
    },
    text: (node, value) => {
        node.textContent = value;
    },
    dataset: (node, value) => {
        Object.assign(node.dataset, value);
    },
};

function applyAttribute(node, key, value) {
    if (value === null || value === undefined || value === false) {
        return;
    }

    if (ATTRIBUTE_HANDLERS[key]) {
        ATTRIBUTE_HANDLERS[key](node, value);

        return;
    }

    if (key.startsWith('on') && typeof value === 'function') {
        node.addEventListener(key.slice(2).toLowerCase(), value);

        return;
    }

    node.setAttribute(key, value === true ? '' : value);
}

export function el(tag, attributes = {}, children = []) {
    const node = document.createElement(tag);

    Object.entries(attributes).forEach(([key, value]) => applyAttribute(node, key, value));

    (Array.isArray(children) ? children : [children])
        .filter((child) => child !== null && child !== undefined && child !== false)
        .forEach((child) => node.append(child));

    return node;
}

export function clear(node) {
    while (node.firstChild) {
        node.removeChild(node.firstChild);
    }
}
