

export class CensoLocalidadesService {
	constructor () {
		this.rota = 'v4/api/educacao/central-de-matriculas/processo-inscricao/'
	}

	async getEstados () {
		return (await window.axios.get(this.rota + 'estados')).data.data.map(estado => {
			return {label: estado.nome.toUpperCase(), value: estado.codigo}
		})
	}

	async getPaises () {
		return (await window.axios.get(this.rota + 'paises')).data.data.map(pais => {
			return {label: pais.nome.toUpperCase(), value: pais.codigo}
		})
	}

	async getMunicipios (estado) {
		return (await window.axios.get(this.rota + `estados/${estado}/municipios`)).data.data.map(municipio => {
			return {label: municipio.nome.toUpperCase(), value: municipio.codigo}
		})
	}
}
