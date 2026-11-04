import Pusher from "pusher-js";

window.pusher = null;

let config = document.querySelector('[name="PUSHER_CONFIG"]')
config = config ? JSON.parse(config.content) : null;

if (config && config.enabled) {
    let requestPath = document.querySelector('[name="ECIDADE_REQUEST_PATH"]');
    requestPath = requestPath ? requestPath.content : '';

    const token = JSON.parse(localStorage.getItem('ecidade@user_token'));

    try {
        // Gerenciador de notificações
        window.pusher = new Pusher(config.appKey, {
            wsHost: config.host,
            wsPort: config.port,
            wssPort: config.port,
            cluster: '',
            forceTLS: false,
            encrypted: true,
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            authEndpoint: `${requestPath}v4/broadcasting/auth`,
            auth: {
                headers: {
                    Authorization: `Bearer ${token?.access_token}`
                }
            }
        });
    } catch (e) {
    }
}
