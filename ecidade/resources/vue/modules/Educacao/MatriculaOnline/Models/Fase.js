export class Fase {
	constructor(parametros) {
		this._codigo = parametros.codigo
		this._desrcicao = parametros.descricao
		this._dataCorte = parametros.dataCorte
		this._dataInicio = parametros.dataInicio
		this._dataFim = parametros.dataFim
		this._ciclo = parametros.ciclo
		this._isProcessada = parametros.isProcessada
		this._isEncerrada = parametros.isEncerrada
		this._publicosAlvo = parametros.publicosAlvo.map(pb => pb.value)
		this._horaInicio = parametros.horaInicio
		this._horaFim = parametros.horaFim
		this._opcoesEscolha = parametros.opcoesEscolha
		this._opcoesObrigatoria = parametros.opcoesObrigatoria
		this._parametros = parametros;
		this._exibeEscolaOrigem = parametros.exibeEscolaOrigem
		this._etapas = parametros.etapas
	}

	get etapas() {
		return this._etapas;
	}

	set etapas(value) {
		this._etapas = value;
	}

	get exibeEscolaOrigem() {
		return this._exibeEscolaOrigem;
	}

	set exibeEscolaOrigem(value) {
		this._exibeEscolaOrigem = value;
	}

	get parametros() {
		return this._parametros;
	}

	set parametros(value) {
		this._parametros = value;
	}

	get codigo() {
		return this._codigo;
	}

	set codigo(value) {
		this._codigo = value;
	}

	get desrcicao() {
		return this._desrcicao;
	}

	set desrcicao(value) {
		this._desrcicao = value;
	}

	get dataCorte() {
		return this._dataCorte;
	}

	set dataCorte(value) {
		this._dataCorte = value;
	}

	get dataInicio() {
		return this._dataInicio;
	}

	set dataInicio(value) {
		this._dataInicio = value;
	}

	get dataFim() {
		return this._dataFim;
	}

	set dataFim(value) {
		this._dataFim = value;
	}

	get ciclo() {
		return this._ciclo;
	}

	set ciclo(value) {
		this._ciclo = value;
	}

	get isProcessada() {
		return this._isProcessada;
	}

	set isProcessada(value) {
		this._isProcessada = value;
	}

	get isEncerrada() {
		return this._isEncerrada;
	}

	set isEncerrada(value) {
		this._isEncerrada = value;
	}

	get publicosAlvo() {
		return this._publicosAlvo;
	}

	set publicosAlvo(value) {
		this._publicosAlvo = value;
	}

	get horaInicio() {
		return this._horaInicio;
	}

	set horaInicio(value) {
		this._horaInicio = value;
	}

	get horaFim() {
		return this._horaFim;
	}

	set horaFim(value) {
		this._horaFim = value;
	}

	get opcoesEscolha() {
		return this._opcoesEscolha;
	}

	set opcoesEscolha(value) {
		this._opcoesEscolha = value;
	}

	get opcoesObrigatoria() {
        return this._opcoesObrigatoria;
    }

    set opcoesObrigatoria(value) {
        this._opcoesObrigatoria = value;
    }
}
