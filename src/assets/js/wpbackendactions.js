/**
 * Simplifica el uso de notify.js
 * @param {string} msg - El mensaje a mostrar.
 * @param {string} action - Tipo de notificación: "success", "error", "info", "warn".
 * @param {string} position - Posición: "right", "left", "top", "bottom".
 */
function wbeShowNotify(title, msg, action = "info", position = "right top") {
    new Notify({
        status : action,
        title: title,
        text: msg,
        position : position,
    })
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