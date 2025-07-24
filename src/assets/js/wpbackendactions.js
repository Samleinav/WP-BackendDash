/**
 * Simplifica el uso de notify.js
 * @param {string} msg - El mensaje a mostrar.
 * @param {string} action - Tipo de notificación: "success", "error", "info", "warn".
 * @param {string} position - Posición: "right", "left", "top", "bottom".
 * @param {string|jQuery} [target] - Selector jQuery o elemento sobre el que mostrar el mensaje. Por defecto es body.
 */
function showNotify(msg, action = "info", position = "top", target = "body") {
    const $el = (typeof target === 'string') ? $(target) : target;
    if (!$el || $el.length === 0) return console.warn("Elemento objetivo no encontrado");

    $el.notify(msg, {
        position: position,
        className: action
    });
}
