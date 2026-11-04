import axios from 'axios'

const ECIDADE_REQUEST_PATH = window.document.querySelector('[name="ECIDADE_REQUEST_PATH"]') ?? '';
window.ECIDADE_PATH = ECIDADE_REQUEST_PATH.content;
const urlMatch = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g);
const locationUrl = urlMatch !== null ? urlMatch[0] : '';
const configAxios = {
    headers: {
        'X-Window-Session': locationUrl !== '' ? locationUrl.split('/').reverse()[0] : '',
    },
    baseURL: ECIDADE_REQUEST_PATH.content ?? ''
};
window.axios = axios.create(configAxios);
window.axios.interceptors.request.use((config) => {
    const token = JSON.parse(window.localStorage.getItem('ecidade@user_token'));
    config.headers['Authorization'] = token ? `Bearer ${token.access_token}` : '';

    return config
})
