export default new class ContaService {

    /**
     * Empenhos a mapear
     *
     * @param {object} params
     * @returns {Promise}
     */
    async getContas(params) {
        const route = 'v4/api/financeiro/contabilidade/';
        const url = route + 'procedimento/mapeamento-empenho-rp-conta/contas';

        const req = await axios.get(url, { params })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }
}
