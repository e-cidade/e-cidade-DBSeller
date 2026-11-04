export default new class EmpenhoService {

    /**
     * Empenhos a mapear
     *
     * @param {string|numebr} exercicio
     * @param {string|numer} reduzido
     * @returns {Promise}
     */
    async getEmpenhos(exercicio, reduzido, tipoLiquidacao,tipoBusca) {
        const route = 'v4/api/financeiro/contabilidade/';
        const url = route + 'procedimento/mapeamento-empenho-rp-conta/empenhos';
        const params = { exercicio, reduzido, tipoLiquidacao,  tipoBusca }

        const req = await axios.get(url, { params })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }

    /**
     * Exclui empenho
     *
     * @param {string|number} numemp Sequencial do Mapeamento
     * @returns {Promise}
     */
    async deleteEmpenho(codigo) {
        const route = 'v4/api/financeiro/contabilidade/';
        const url = route + 'procedimento/mapeamento-empenho-rp-conta';
        const data = { codigo }

        const req = await axios.delete(url, { data })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }

    /**
     * Empenhos ja mapeados
     *
     * @param {string|number} exercicio
     * @param {string|number} reduzido
     * @param {string|number} empenho
     * @param {object} paginator
     * @returns {Promise}
     */
    async getEmpenhosMapeados(exercicio, reduzido, paginator = null, empenho = null) {
        const route = 'v4/api/financeiro/contabilidade/';
        const url = route + 'procedimento/mapeamento-empenho-rp-conta/empenhos-conta';
        const params = {
            exercicio,
            reduzido,
            empenho
        }

        if (paginator) {
            params.page = paginator.page + 1;
        }

        const req = await axios.get(url, { params })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }

    /**
     * Exclui todos os empenhos
     *
     * @param {string|number} exercicio
     * @param {string|number} reduzido
     * @returns {Promise}
     */
    async deleteAllEmpenhos(exercicio, reduzido) {
        const route = 'v4/api/financeiro/contabilidade/';
        const url = route + 'procedimento/mapeamento-empenho-rp-conta/empenhos';
        const data = { exercicio, reduzido }

        const req = await axios.delete(url, { data })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }
}
