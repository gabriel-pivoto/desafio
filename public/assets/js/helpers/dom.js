export const $ = (selector, scope = document) => scope.querySelector(selector);

export const $$ = (selector, scope = document) => Array.from(scope.querySelectorAll(selector));

export function setPressed(node, pressed) {
    node.setAttribute('aria-pressed', String(pressed));
}

export function redirectTo(path) {
    window.location.replace(path);
}
