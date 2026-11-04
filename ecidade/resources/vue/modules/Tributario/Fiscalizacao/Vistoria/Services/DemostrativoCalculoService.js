export default new class DenomostrativoCalculoService {

    /**
     * Calculos de Vistorias
     *
     * @param {object} params
     * @returns {Promise}
     */
    async getCalculos(params) {
        const route = 'v4/api/tributario/fiscalizacao/vistoria/';
        const url = route + 'demostraivo-calculo';

        const req = await axios.get(url, { params })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }

    /**
     * Exercicios que possuem calculos
     */
    async getExerciciosCalculados(params) {
        const route = 'v4/api/tributario/fiscalizacao/vistoria/';
        const url = route + 'exercicios-calculados';

        const req = await axios.get(url, { params })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }

     /**
     * Exercicios que possuem calculos
     */
     async getTiposVistorias(params) {
        const route = 'v4/api/tributario/fiscalizacao/vistoria/';
        const url = route + 'tipos-vistorias-calculados';

        const req = await axios.get(url, { params })
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        return resp.data;
    }
}
