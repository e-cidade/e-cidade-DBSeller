/**
 * Percebi que estava repetindo o mesmo código sempre, vendo isso resolvi encapular a lógica de buscar os planos
 * em desenvolvimento e criar o select
 * Para usar a classe você precisa ter um elemento select no html. Esse elemento deve ser passado no construtor
 *
 * @example Exemplo de uso
 * const planejamento = new Planejamento(document.getElementById('planejamento'));
 * planejamento.load();
 */
class Planejamento {

    /**
     *
     * @type {boolean}
     * @private
     */
    _considerarGet = true;
    _rota = 'financeiro/planejamento/consulta/planos-ativos';
    _planos = [];
    _callbackChange = () => {
    }

    /**
     * @param {HTMLSelectElement} element
     */
    constructor(element) {
        this.element = element;
    }

    /**
     *
     * @returns {HTMLSelectElement}
     */
    getElement = () => {
        return this.element;
    };

    /**
     * Retorna o valor atualmente selecionado
     * @returns {string}
     */
    getValue = () => {
        return this.element.value;
    };

    setCallbackChange = (f) => {
        this._callbackChange = f;
    };

    /**
     * Retorna a instancia do plano selecionado
     * @returns {*[]}
     */
    getPlano = () => {
        if (this.getValue() === '') {
            return;
        }
        return this._planos.filter((plano) => {
            if (plano.pl2_codigo == this.getValue()) {
                return plano;
            }
        }).shift();
    };

    load = () => {
        this.element.addEventListener('change', (event) => {
            this._callbackChange(event, this);
        });

        this.element.options.length = 0;
        this.element.add(new Option('Selecione um plano', ''));

        if (PHPSession.requestApi === undefined) {
            PHPSession.loadData().then(() => {
                this._buscaPlanos();
            });
        } else {
            this._buscaPlanos();
        }
    }

    _buscaPlanos = () => {
        let rota = `${PHPSession.requestApi}/${this._rota}`;
        const get = this._getURLParameters(window.location.search);
        if (this._considerarGet && get.tipo) {
            rota += `/${get.tipo}`;
        }

        HttpClient.get(`${rota}`).then(response => {
            response.data.map((plano) => {
                if (typeof planejamentosTiposExibir !== 'undefined') {
                    if (planejamentosTiposExibir.includes(plano.pl2_tipo)) {
                        this.element.add(new Option(plano.pl2_titulo, plano.pl2_codigo));
                    }
                } else {
                    this.element.add(new Option(plano.pl2_titulo, plano.pl2_codigo));
                }
                this._planos.push(plano);
            });

            if (response.data.length === 1) {
                this.element.value = response.data.shift().pl2_codigo;
                this.element.dispatchEvent(new Event('change'));
            } else if (get.planejamento) {
                // caso seja informado um planejamento na url já trazemos o plano pré-selecionado.
                this.element.value = get.planejamento;
                this.element.dispatchEvent(new Event('change'));
                get.planejamento = '';
            }
        });
    };

    _getURLParameters = (url) =>
        (url.match(/([^?=&]+)(=([^&]*))/g) || []).reduce(
            (a, v) => ((a[v.slice(0, v.indexOf('='))] = v.slice(v.indexOf('=') + 1)), a),
            {}
        );

}
