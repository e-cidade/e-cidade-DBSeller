/**
 * Classe javascript responsavel por carregar os dados da session do php
 *
 * Para ter os dados na inicialização da página deve-se chamar o loadData
 * @example Exemplo de uso
 * window.addEventListener('load', () => {
 *     console.log('loading ', PHPSession.requestApi); // undefined
 *     debugger;
 *     PHPSession.loadData().then(() => {
 *         console.log('loading ', PHPSession.requestApi); // http://localhost/e-cidade/v4/api
 *         debugger;
 *     });
 * });
 */
class Session {

    data = [];
    requestPath;
    requestApi;

    loadData() {
        const formData = new FormData();
        formData.append('acao', 'session');
        return HttpClient.post('con4_ecidadeinfo.RPC.php', {body: formData}).then(response => {
            this.data = response.session;
            this.requestPath = response.requestPath;
            this.requestApi = response.requestApi;
            this.UF = response.UF;
            if (response.error) {
                return Promise.reject(response.mensagem);
            }

            return Promise.resolve(this);
        });
    }

    appendFormData(formData) {
        for (var session of this.data) {
            formData.append(session.name, session.value);
        }
    }

    getSession(variavel) {
        return this.data.filter(function (data) {
            return data.name === variavel;
        }).shift();
    }

    getValueSession(variavel) {
        return this.getSession(variavel).value;
    }

    getToken(){
        const token = JSON.parse(window.localStorage.getItem('ecidade@user_token'));

        return token?.access_token;
    }

    getConfigRequest(){
        return {
            credentials: 'include',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Authorization': `Bearer ${this.getToken()}`
            }
        }
    }
}

var PHPSession = new Session();

if (document.body) {
    PHPSession.loadData().catch(error => {
        alert(error);
    });
}
