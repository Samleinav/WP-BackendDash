/**
 * Simplifica el uso de notify.js
 * @param {string} msg - El mensaje a mostrar.
 * @param {string} action - Tipo de notificación: "success", "error", "info", "warn".
 * @param {string} position - Posición: "right", "left", "top", "bottom".
 * @param {string|jQuery} [target] - Selector jQuery o elemento sobre el que mostrar el mensaje. Por defecto es body.
 */
function wbeShowNotify(msg, action = "info", position = "top", target = "body") {
    const $el = (typeof target === 'string') ? jQuery(target) : target;
    if (!$el || $el.length === 0) return console.warn("Elemento objetivo no encontrado");

    $el.notify(msg, {
        position: position,
        className: action
    });
}


/**
 * wait * @param {number} ms - Tiempo en milisegundos a esperar.
 * @returns {Promise} - Promesa que se resuelve después de esperar el tiempo especific
 * 
 */
function wbeWait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}


/**
 * WBERedirect
 * Redirige a una URL específica.
 * @param {string} url - URL a la que redirigir.
 * @param {boolean} [force=false] - Si es true, fuerza la redire
 * 
 * 
 */
function wbeRedirect(url, force = false) {
    if (force || !window.location.href.includes(url)) {
        window.location.href = url;
    }
}