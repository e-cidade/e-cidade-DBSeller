import '../../vue/providers/AxiosProvider';
import './providers/PusherProvider';

let requestPath = document.querySelector('[name="ECIDADE_REQUEST_PATH"]');
requestPath = requestPath ? requestPath.content : '';

// url do Laravel
window.urlApi = `${requestPath}v4/api`;
window.requestPath = requestPath;

/**
 * Registra globais para usar nas janelas do e-cidade
 */
Desktop.axios = window.axios;
Desktop.urlApi = window.urlApi;
Desktop.pusher = window.pusher;
Desktop.requestPath = requestPath;

try {
    window.Sortable = require('sortablejs').Sortable;
} catch (e) {
}

window.addEventListener('storage', (e) => {
    if (e.key !== 'ecidade@user_token' || e.newValue === null) {
        return;
    }

    const token = JSON.parse(e.newValue)
    window.pusher.auth.headers['Authorization'] = `Bearer ${token?.access_token}`;
    Desktop.pusher = window.pusher
});
