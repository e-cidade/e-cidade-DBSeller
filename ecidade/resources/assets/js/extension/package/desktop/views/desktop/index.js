'use strict';

const notifications = [];

const userIdMeta = document.querySelector('[name="USER_ID"]');
const userId = userIdMeta ? userIdMeta.content : null;
const blockButton = document.getElementById('block');
const logoutButton = document.getElementById('logout');

const token = JSON.parse(localStorage.getItem('ecidade@user_token'));

// Bloqueia a sessão quando o token expirar
let timeoutToken = setTimeout(blockWindow, token?.expires_at - Date.now());

logoutButton.addEventListener('click', onClickLogout);

window.addEventListener('storage', (e) => {
    if (e.key === 'ecidade@user_token') {
        onChangeToken(e);
    }
    if (e.key === 'ecidade@user_logout') {
        onChangeLogout(e);
    }
});

if (pusher !== null && userId !== null) {
    /**
     * @todo renomear o nome do canal para algo melhor
     */
    const channel = pusher.subscribe(`private-App.Domain.Configuracao.Usuario.Models.Usuario.${userId}`);
    channel.bind('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', addNotification);
}

// Permite que os menus abertos possam ser reordenados.
Sortable.create(document.getElementById('taskbar-buttons'), {
    animation: 350,
    forceFallback: false,
    setData: (dataTransfer, dragEl) => {
        dataTransfer.setDragImage(new Image(), 0, 0);
        dataTransfer.setData('Text', dragEl.textContent);
    }
});

window.onload = () => {
    // Destrói a sessão caso não haja token ou o usuário tenha clicado em logout.
    // Esse caso pode acontecer devido o usuário fechar a aba antes do logout terminar.
    if (localStorage.getItem('ecidade@user_logout') || token === null) {
        localStorage.removeItem('ecidade@user_token');
        localStorage.removeItem('ecidade@user_logout');
        killUserSession().then();
        return;
    }

    setDesktopLoading(false);
    getUserNotifications().then();
    totalUserDocuments().then();
};

async function getUserNotifications() {
    setNotificationsLoading(true);
    try {
        const response = await axios.get(`${urlApi}/configuracao/usuario/notificacoes`);
        notifications.length = 0;
        for (const notification of response.data.data) {
            notifications.push(notification);
        }
    } catch (e) {
    }
    renderNotificationsMenu();
    setNotificationsLoading(false);
}


async function totalUserDocuments() {
    try {
        const resp = await axios.get(`${urlApi}/patrimonial/protocolo/documentos/usuario/total`);
        const data = await resp.data;
        const elTotalDocuments = document.getElementById('documentos-atividades');
        const total = data.data.total;
        const divNotify = document.createElement("div");
        divNotify.classList.add('notify');

        if (total > 0 && total < 100) {
            divNotify.innerText = data.data.total;
            elTotalDocuments.appendChild(divNotify);
            return;
        }

        if (total >= 100) {
            divNotify.innerText = '99+';
            elTotalDocuments.appendChild(divNotify);
            return;
        }
    } catch (e) {
    }
}

function addNotification(notification) {
    notifications.unshift(notification);
    renderNotificationsMenu();
    if (alertify !== undefined) {
        alertify.log(notification.title, 'alert', 0);
    }
}

function renderNotificationsMenu() {
    clearNotificationList();
    for (const notification of notifications) {
        renderNotification(notification);
    }

    countNotifications();
}

function clearNotificationList() {
    const notificationsList = document.getElementById('notifications');
    notificationsList.innerHTML = '';
}

function limpaTelaAssinatura() {
    if (localStorage.getItem("telaNotificacaoAssinatura") !== null) {
        localStorage.removeItem("telaNotificacaoAssinatura");
    }
}

window.addEventListener('beforeunload', function () {
    localStorage.removeItem("telaNotificacaoAssinatura");
});

function renderNotification(notification) {
    const notificationsList = document.getElementById('notifications');

    const li = document.createElement('li');
    li.classList.add('notification');

    let notificationContent = document.createElement('div');
    notificationContent.id = 'notificationContent';

    let titleDiv = document.createElement('div');
    titleDiv.classList.add('title');
    titleDiv.innerHTML = notification.title;

    const span = document.createElement('span');
    span.dataset.id = notification.id;
    span.onclick = markAsRead;
    span.innerText = 'x';
    span.title = 'Marcar como visto';
    span.id = 'removeNotificacao';
    titleDiv.appendChild(span);

    notificationContent.appendChild(titleDiv);

    let messageDiv = document.createElement('div');
    messageDiv.innerText = notification.message;

    notificationContent.appendChild(messageDiv);

    li.appendChild(notificationContent);

    let divActions = document.createElement('div');
    divActions.id = 'notification-actions';
    divActions.style = 'gap:2px; display: flex; justify-content: center; align-items: center';

    if (notification.action) {
        divActions.appendChild(createButtoRedirecMenu(notification));
    }

    if (notification.externalLinks) {
        createExternalLinks(divActions, notification);
    }

    li.appendChild(divActions);

    notificationsList.appendChild(li);
}

function createExternalLinks(divActions, notification) {
    for (const link of notification.externalLinks) {
        const btn = document.createElement('button');
        btn.innerHTML = link.label;
        btn.classList.add('btn-notification');

        btn.addEventListener('click', function () {
            window.open(link.url)
        })

        divActions.appendChild(btn);
    }
}

function createButtoRedirecMenu(notification) {

    const btn = document.createElement('button');
    btn.innerHTML = 'Menu';
    btn.classList.add('btn-notification');
    btn.addEventListener('click', function () {
        if (localStorage.getItem("telaNotificacaoAssinatura") === null) {
            localStorage.setItem("telaNotificacaoAssinatura", true);
            var parametros = notification.action;
            let nomeMenu = 'Assinatura Documentos';
            if (notification.menuName) {
                nomeMenu = notification.menuName;
            }
            Desktop.Window.create(nomeMenu, parametros, limpaTelaAssinatura);
        }
    });
    return btn;
}

function countNotifications() {
    const countNotification = document.getElementById('count-notifications');

    countNotification.style.display = notifications.length > 0 ? '' : 'none';
    countNotification.innerText = notifications.length.toString();

    if (notifications.length <= 0) {
        const notificationsList = document.getElementById('notifications');
        const li = document.createElement('li');
        li.name = 'no-notifications';
        li.innerText = 'Não há notificações.';
        li.style.pointerEvents = 'none';
        notificationsList.appendChild(li);
    }
}

async function markAsRead(e) {
    const id = e.target.dataset.id;

    setNotificationsLoading(true);
    try {
        await axios.put(`${urlApi}/configuracao/usuario/notificacoes/${id}`);
        await getUserNotifications();
    } catch (e) {
    }
    setNotificationsLoading(false);
}

function setNotificationsLoading(loading) {
    const divLoading = document.getElementById('notification-loader');
    const notificationsList = document.getElementById('notifications');
    divLoading.style.display = loading ? 'flex' : 'none';
    notificationsList.style.pointerEvents = loading ? 'none' : 'all';
}

function blockWindow() {
    blockButton.click();
}

function setDesktopLoading(loading, message = 'Carregando... Por favor, aguarde.') {
    const disableClicks = (e) => {
        e.stopPropagation();
        e.preventDefault();
    }

    if (loading) {
        document.addEventListener('click', disableClicks, true);
    } else {
        document.removeEventListener('click', disableClicks, true);
    }

    const divLoading = document.getElementById('desktop-loader');
    const messageContent = document.getElementById('desktop-loader-messager');

    divLoading.style.display = loading ? '' : 'none';
    messageContent.innerHTML = message;
}

function onClickLogout(e) {
    e.preventDefault();

    alertify.confirm('Tem certeza que deseja fazer logout?', async (confirm) => {
        if (!confirm) {
            return;
        }

        logoutButton.removeEventListener('click', onClickLogout);
        await logout();
    });
}

async function logout() {
    localStorage.setItem('ecidade@user_logout', '1');
    setDesktopLoading(true, 'Realizando logout... Por favor, aguarde.');
    try {
        await axios.put(`${requestPath}v4/logout`);
    } catch (e) {
    }

    localStorage.removeItem('ecidade@user_token');
    localStorage.removeItem('ecidade@user_logout');
    location.reload();
}

async function killUserSession() {
    try {
        setDesktopLoading(true);
        await axios.put(`${requestPath}v4/kill`);
        location.reload();
    } catch (e) {
        console.error('Erro ao finalizar sessão!');
    }
}

function onChangeToken(e) {
    clearTimeout(timeoutToken);
    if (e.newValue === null) {
        killUserSession().then();
        return;
    }

    // cria um novo timeout quando o token for regerado
    const token = JSON.parse(e.newValue);
    timeoutToken = setTimeout(blockWindow, token.expires_at - Date.now());
}

function onChangeLogout(e) {
    if (e.newValue === null) {
        location.reload();
        return;
    }

    setDesktopLoading(true, 'Realizando logout... Por favor, aguarde.');
}
