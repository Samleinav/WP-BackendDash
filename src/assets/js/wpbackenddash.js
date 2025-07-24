class ActionDispatcher {
    constructor() {
        this.classes = {}; // Para registrar clases con métodos estáticos
    }

    /**
     * Registrar clases con métodos estáticos o instancias
     * @param {string} className 
     * @param {object} classObject 
     */
    registerClass(className, classObject) {
        this.classes[className] = classObject;
    }

    /**
     * Ejecutar múltiples acciones definidas en el arreglo
     * @param {Array} actionTodo 
     */
    runActions(actionTodo) {
        if (!Array.isArray(actionTodo)) return;

        actionTodo.forEach((action) => {
            if (typeof action !== 'object') return;

            const [methodRef, params] = action;
            this.execute(methodRef, params);
        });
    }

    /**
     * Ejecutar una acción específica
     * @param {string} methodRef - Puede ser "methodName" o "ClassName.methodName"
     * @param {Array|any} params - Argumentos para pasar a la función
     */
    execute(methodRef, params = []) {
        try {
            if (typeof methodRef !== 'string') return;

            let func = null;

            if (methodRef.includes('.')) {
                const [className, methodName] = methodRef.split('.');
                if (this.classes[className] && typeof this.classes[className][methodName] === 'function') {
                    func = this.classes[className][methodName].bind(this.classes[className]);
                }
            } else if (typeof window[methodRef] === 'function') {
                func = window[methodRef];
            }

            if (func) {
                if (Array.isArray(params)) {
                    func(...params);
                } else {
                    func(params);
                }
            } else {
                console.warn(`Método no encontrado: ${methodRef}`);
            }
        } catch (e) {
            console.error(`Error al ejecutar ${methodRef}`, e);
        }
    }
}

const actionDispatcher = new ActionDispatcher();
window.actionDispatcher = actionDispatcher; // Exponer el dispatcher globalmente

class WPRequest {
    constructor(baseURL = null) {
        this.baseURL = baseURL || window.ajaxurl || '/wp-admin/admin-ajax.php';
    }

    /**
     * Envía un request a WordPress
     * @param {Object} options
     * @param {string} options.action - Nombre de la acción AJAX en PHP
     * @param {string|FormData|object|HTMLFormElement} [options.data] - Datos a enviar
     * @param {Function} [options.onSuccess]
     * @param {Function} [options.onError]
     * @param {Function} [options.onAlways]
     * @param {string} [options.method] - POST o GET
     * @param {boolean} [options.dispatchActions] - Si debe ejecutar los actiontodo automáticamente
     */
    send(options = {}) {
        const {
            action,
            data = {},
            onSuccess,
            onError,
            onAlways,
            method = 'POST',
            dispatchActions = true
        } = options;

        if (!action) throw new Error('Falta el parámetro `action`');

        let payload;

        if (data instanceof HTMLFormElement) {
            payload = new FormData(data);
        } else if (data instanceof FormData) {
            payload = data;
        } else if (typeof data === 'object') {
            payload = new FormData();
            for (const key in data) {
                payload.append(key, data[key]);
            }
        } else {
            throw new Error('El parámetro `data` debe ser un objeto, FormData o formulario');
        }

        payload.append('action', action);

        return $.ajax({
            url: this.baseURL,
            method: method,
            data: payload,
            processData: false,
            contentType: false,
        })
        .done((response) => {
            if (response.success) {
                if (dispatchActions && response.data?.actiontodo) {
                    window.actionDispatcher?.runActions(response.data.actiontodo);
                }
                onSuccess?.(response.data, response);
            } else {
                onError?.(response.data || {}, response);
            }
        })
        .fail((jqXHR, textStatus, errorThrown) => {
            console.error('Error AJAX', textStatus, errorThrown);
            onError?.({ message: errorThrown || 'Request failed' }, jqXHR);
        })
        .always(() => {
            onAlways?.();
        });
    }
}

const wpAjax = new WPRequest();
window.wpAjax = wpAjax;