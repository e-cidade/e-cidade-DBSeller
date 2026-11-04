export default new class MapeamentoService {
    /**
     * Salva os mapeamento
     *
     * @param {string|number} reduzido
     * @param {string|number} exercicio
     * @param {object[]} empenhos
     *
     * @returns Promise
     */
    async save(reduzido, exercicio, empenhos) {
        const route = 'v4/api/financeiro/contabilidade/';
        const url = route + 'procedimento/mapeamento-empenho-rp-conta';

        const data = {
            reduzido,
            exercicio,
            empenhos
        }

        const req = await axios.post(url, data)
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }

    /**
     *
     * fetch dos mapeamentos
     *
     * @param {string|number} exercicio
     * @param {string|number} instituicao
     * @returns {Promise}
     */
    async getMapeamentos(exercicio, instituicao) {
        const route = 'v4/api/financeiro/contabilidade/';
        const url = route + 'procedimento/mapeamento-empenho-rp-conta/';

        const params = {
            instituicao,
            exercicio
        }

        const req = await axios.get(url, { params })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }
}
