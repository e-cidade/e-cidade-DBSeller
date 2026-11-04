export class InscricaoService {
    constructor() {
        this.route = {
            consulta: 'v4/api/educacao/central-de-matriculas/processo-inscricao/inscricao/consulta',
            protocolo: 'api/processamento/ecidade/emitirComprovante',
            excluir: 'api/processamento/ecidade/inscricao',
            consultaCpf: 'v4/api/educacao/central-de-matriculas/processo-inscricao/inscricao/consultaCpf',
            consultaCandidato: 'v4/api/educacao/central-de-matriculas/processo-inscricao/inscricao/consulta-candidato'
        }
    }

    async consulta (tipo, valor, cpfAluno = null, edicao = false) {
        let rota = `${this.route.consulta}/${tipo}/${valor}`;
        if (cpfAluno != null) {
            rota = `${this.route.consultaCpf}/${cpfAluno}/${tipo}/${valor}`;
        }
      
        rota += edicao ? '?edicao=true' : '?edicao=false'
        return (await window.axios.get(rota)).data.data
    }

    async consultaCandidato (tipo, dado, nascimento = null) {
        let rota = `${this.route.consultaCandidato}/${tipo}/${dado}/${nascimento}`;
        return (await window.axios.get(rota)).data.data
    }

    async protocolo (protocolo) {
        return (await window.axios.get(`${this.route.protocolo}/${protocolo}`)).data.data.body.path
    }

    async excluir (protocolo) {
        return (await window.axios.get(`${this.route.excluir}/${protocolo}`)).data.data
    }
}
