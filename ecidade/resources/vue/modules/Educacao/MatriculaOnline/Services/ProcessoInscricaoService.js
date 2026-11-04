import axios from "axios";
import {Fase} from "../Models/Fase.js";
import {Inscricao} from "../Models/Inscricao.js";
export class ProcessoInscricaoService {
	constructor () {
		this.rotas = {
			fases: 'v4/api/educacao/central-de-matriculas/processo-inscricao/fases/{data}',
			fasesCadastroInterno: 'v4/api/educacao/central-de-matriculas/processo-inscricao/fases-interno/{data}',
			temFaseAberta: 'v4/api/educacao/central-de-matriculas/processo-inscricao/tem-fase-abeta',
			redes: 'processo-inscricao/redes-origem',
			camposOpcionais: 'configuracoes/campos-opcionais',
			escolasOrigem: 'v4/api/educacao/central-de-matriculas/processo-inscricao/escolas-origem',
			necessidades: 'v4/api/educacao/central-de-matriculas/processo-inscricao/necessidades-especiais',
			bairros: 'v4/api/educacao/central-de-matriculas/processo-inscricao/bairros',
			tiposRua: 'processo-inscricao/tipos-ruas',
			escolasDisponiveis: 'v4/api/educacao/central-de-matriculas/processo-inscricao/fases/{fase}/etapas/{etapa}/escolas-disponiveis',
			enviarInscricao: 'processo-inscricao/inscricao',
			zonas: 'processo-inscricao/zonas',
			rendaFamiliar: 'processo-inscricao/renda-familiar',
			aluno: 'v4/api/educacao/central-de-matriculas/processo-inscricao/aluno/cpf',
            alunoVisto: 'api/processamento/ecidade/aluno/visto',
            alunoRne: 'api/processamento/ecidade/aluno/rne',
			alunoescola: 'v4/api/educacao/central-de-matriculas/processo-inscricao/aluno/{escola}/cpf',
			candidato: 'v4/api/educacao/central-de-matriculas/processo-inscricao/fases/{fase}/candidato/cpf/{cpf}',
            candidatoVisto: 'api/processamento/ecidade/fases/{fase}/candidato/visto/{visto}',
            candidatoRne: 'api/processamento/ecidade/fases/{fase}/candidato/rne/{rne}',
			dadosPrefeitura: 'v4/api/educacao/central-de-matriculas/processo-inscricao/processo-inscricao/dados-prefeitura',
			geral: 'configuracoes/configuracoes-gerais',
			profissoes: 'v4/api/educacao/central-de-matriculas/processo-inscricao/profissoes',
			corRaca: 'v4/api/educacao/central-de-matriculas/processo-inscricao/cor-raca'
		}

		this.camposString = [
			'dataNascimento', 'protocolo', 'cpf', 'nome', 'nomeSocial', 'visto', 'rne',
			'livro', 'folha', 'termo', 'dataCertidao', 'nomeCartorio', 'certidaoNumero', 'identidade',
            'cartaoSus', 'bolsaFamilia', 'cep', 'logradouro',
			'numero', 'complemento', 'filiacao1', 'cpfFiliacao1', 'filiacao2', 'cpfFiliacao2',
			'tipoResponsavel', 'responsavelLegalNome', 'responsavelLegalRG',
            'responsavelLegalCpf', 'responsavelLegalVisto',
			'responsavelLegalRNE', 'responsavelLegalCelular', 'responsavelLegalEmail',
			'email', 'contatoPrincipal', 'contatoSecundario', 'matriculaServidor'
		]

		this.camposBool = [
			'irmaoGemeo', 'cadeirante', 'responsavelLegalTrabalhador', 'maeVitima', 'responsavelLegalCelularWhatsApp'
		]

		this.camposObjeto = [
			'etapaEnsino', 'redeOrigem', 'escolaOrigem', 'nomeSocial', 'nacionalidade', 'sexo',
            'estadoCertidao', 'municipioCertidao',
			'profissaoFiliacao1','profissaoFiliacao2','responsavelLegalProfissao',
			'estadoCivil', 'cor', 'estadoNascimento', 'municipioNascimento', 'paisNascimento',
			'certidaoTipo', 'certidaoModelo', 'tipoLogradouro', 'bairro', 'zonaResidencia',
			'rendaFamiliar','responsavelLegalOrgaoRG', 'orgaoIdentidade'
		]

		this.camposArray = [
			'necessidadesEspeciais', 'opcoesEscola'
		]

	}

	async buildFases (data) {
        /* let rota = this.rotas.fases.replace('{data}', data); */
        let rota = this.rotas.fasesCadastroInterno.replace('{data}', data);
		await window.axios.get(rota).then(response => {
			this.setStorageFases(response.data.data)
		})
	}

	setStorageFases(value) {
		localStorage.setItem('fase', JSON.stringify(value))
	}
	getStorageFases () {
		if (localStorage.getItem('fase') === null || localStorage.getItem('fase') === 'null') {
			return null
		}
		return new Fase(JSON.parse(localStorage.getItem('fase')))
	}

	async buildConfigGeral () {
		await axios.get(this.rotas.geral).then(response => {
			this.setStorageConfigGeral(response.data.data)
		})
	}
	setStorageConfigGeral(value) {
		localStorage.setItem('config-geral', JSON.stringify(value))
	}
	getStorageConfigGeral () {
		return JSON.parse(localStorage.getItem('config-geral'))
	}


	async getEtapas(dataNascimento, fase) {
		let rota = this.rotas.etapas.replace('{fase}', fase).replace('{data}', dataNascimento);
		await axios.get(rota).then(response => {
			this.setStorageEtapas(response.data.data)
		})
	}

	async getCorRaca() {
		let cores = (await window.axios.get(this.rotas.corRaca)).data.data.map(elemento => {
			return {value: elemento.id, label: elemento.cor, legenda: elemento.descricao, padrao: elemento.padrao}
		})
		return cores
	}

	async buildEscolasOrigem () {
		let escolas = (await window.axios(this.rotas.escolasOrigem)).data.data.map(escola => {
			return {label: escola.nome, value: escola.codigo}
		})
		this.setStorageEscolasOrigem(escolas)
	}
	setStorageEscolasOrigem(value) {
		localStorage.setItem('escolas-origem', JSON.stringify(value))
	}
	getStorageEscolasOrigem () {
		return JSON.parse(localStorage.getItem('escolas-origem'));
	}
	async getProfissoes() {
		let profissoes = (await window.axios.get(this.rotas.profissoes)).data.data.map(profissoes => {
			return {label: profissoes.nome, value: profissoes.codigo}
		});
		this.setStorageProfissoes(profissoes);
	}
	
	setStorageProfissoes(value) {
		localStorage.setItem('profissoes', JSON.stringify(value))
	}

	getStorageProfissoes(){
		return JSON.parse(localStorage.getItem('profissoes'));
	}

	setStorageApresentaProfissoes(value){
		localStorage.setItem('apresentaProfissoes', value)
	}

	getStorageApresentaProfissoes(){
		return localStorage.getItem('apresentaProfissoes')
	}	

    async salvarFormularioStorageEdicao(dados) {
        for (const key of Object.keys(dados)) {
            if (key === 'fase') {
                this.setStorageFases(dados[key])
            }
			if (key === 'atestadoNecessidadeEspecial') {
				localStorage.setItem(key, dados[key])
			}
            if (this.camposArray.includes(key)) {
                if (key === 'opcoesEscola') {
                    const escolas = dados[key].filter(opcao => opcao.escola.nome !== null)
                    localStorage.setItem(key, JSON.stringify(escolas))
                } else {
                    let valor =  dados[key] === 'null' || dados[key] === null ? '' : dados[key]
                    localStorage.setItem(key, valor === '' ? valor : JSON.stringify(valor))
                }
            }
            if (this.camposObjeto.includes(key)) {
                let valor =  dados[key] === 'null' || dados[key] === null ? '' : dados[key]
                localStorage.setItem(key, valor === '' ? valor : JSON.stringify(valor))
            }
            if (this.camposString.includes(key)) {
                let valor =  dados[key] === 'null' || dados[key] === null ? '' : dados[key]
                localStorage.setItem(key, valor)
            }
            if (this.camposBool.includes(key)) {
                localStorage.setItem(key, dados[key])
            }
        }
    }
	salvarFormularioStorage(dados) {
		Object.keys(dados).forEach(key => {
			if (this.camposArray.includes(key)) {
				if (key === 'opcoesEscola') {
					const escolas = dados[key].data.filter(opcao => opcao.escola.nome !== null)
					localStorage.setItem(key, JSON.stringify(escolas))
				} else {
					let valor =  dados[key].data === 'null' || dados[key].data === null ? '' : dados[key].data
					localStorage.setItem(key, valor === '' ? valor : JSON.stringify(valor))
				}
			}
			if (this.camposObjeto.includes(key)) {
				let valor =  dados[key].data === 'null' || dados[key].data === null ? '' : dados[key].data
				localStorage.setItem(key, valor === '' ? valor : JSON.stringify(valor))
			}
			if (this.camposString.includes(key)) {
				let valor =  dados[key].data === 'null' || dados[key].data === null ? '' : dados[key].data
				localStorage.setItem(key, valor)
			}
			if (this.camposBool.includes(key)) {
				localStorage.setItem(key, dados[key].data)
			}
		})
	}

	async salvarCampoFile(key,file){		
		try{
			if(file != 'null'){
				await this.fileToBase64(file).then(base64File => {
					const jsonValor = {
					  	fileName: file.name,
					  	fileData: base64File
					};
					if (jsonValor != null) {
						localStorage.setItem(key, JSON.stringify(jsonValor));
					}
				});		
			} else {
				localStorage.setItem(key, 'null');
			}
		} catch(error){
			console.error('Erro ao converter arquivo para base64:', error);
		}		
	}

	async fileToBase64(file){
		return new Promise((resolve, reject) => {
			const reader = new FileReader();
			reader.readAsDataURL(file);
			reader.onload = () => resolve(reader.result);
			reader.onerror = error => reject(error);
		});
	}	

	async base64ToFile(base64String, fileName) {
		const response = await fetch(base64String);
		const buffer = await response.arrayBuffer();
		const mimeType = base64String.split(',')[0].match(/:(.*?);/)[1];
		return new File([buffer], fileName, { type: mimeType });
	}	

	async getFileFromCampo(campo){
		let campoFile = this.getCampoFile(campo);
		if(campoFile != 'null'){
			let jsonValor = JSON.parse(this.getCampoFile(campo));
			if (jsonValor != null) {
				return await this.base64ToFile(jsonValor.fileData,jsonValor.fileName);
			}
			return null;
		}
		return null;
	}

	getCampoFile(key){
		return localStorage.getItem(key);
	}

	getCampoFormularioStorage(key) {
		let item =  localStorage.getItem(key) === '' ? null : localStorage.getItem(key)
		if (item === null) {
			return item
		}
		if (this.camposString.includes(key)) {
			return item
		}
		if (this.camposObjeto.includes(key)) {
			return JSON.parse(item)
		}
		if (this.camposArray.includes(key)) {
			return JSON.parse(item)
		}
		if (this.camposBool.includes(key)) {
			return item === 'true'
		}
	}

	async buildNecessidades () {
		await window.axios.get(this.rotas.necessidades).then(response => {
			this.setStorageNecessidades(response.data.data)
		})
	}

	setStorageNecessidades(value) {
		localStorage.setItem('necessidades', JSON.stringify(value))
	}
	getStorageNecessidades () {
		return JSON.parse(localStorage.getItem('necessidades')).map(option => {
			return {
				label: option.nome,
				value: option.codigo,
				subdivisoes: option.subdivisoes.map(sub => {
					return {label: sub.nome, value: sub.codigo, selecionada: false}
				}),
			}
		})
	}

	async buildBairros () {
		await window.axios.get(this.rotas.bairros).then(response => {
			this.setStorageBairros(response.data.data)
		})
	}

	setStorageBairros(value) {
		localStorage.setItem('bairros', JSON.stringify(value))
	}
	getStorageBairros () {
		return JSON.parse(localStorage.getItem('bairros')).map(option => {
			return {label: option.nome, value: option.codigo, uf: option.uf, munic: option.municipio}
		})
	}

	async buildEscolasDisponiveis (fase, etapa) {
		let rota = this.rotas.escolasDisponiveis.replace('{fase}', fase).replace('{etapa}', etapa);
		await window.axios.get(rota).then(response => {
			this.setStorageEscolasDisponiveis(response.data.data)
		})
	}

	setStorageEscolasDisponiveis(value) {
		localStorage.setItem('escolas-disponiveis', JSON.stringify(value))
	}
	getStorageEscolasDisponiveis () {
		return  JSON.parse(localStorage.getItem('escolas-disponiveis')).map(escola => {
			return {
				escola: {nome: escola.nome, codigo: escola.codigo},
				bairrosAtendidos: escola.bairros_atendidos.map(bairro => bairro.mo08_bairro),
				bairro: escola.bairro,
				turnos: escola.turnos.map(turno => {
					return {descricao: turno.descricao, codigo: turno.codigo, selecionado: false}
				}),
				acessibilidade: escola.acessibilidade
			}
		})

	}

	limparFormularioStorage() {
		localStorage.removeItem('protocolo')
		localStorage.removeItem('comprovante')
		let inscricao = new Inscricao()
		const campos = Object.getOwnPropertyNames(inscricao)
		campos.forEach(campo => {
			switch (campo) {
				case 'responsavelLegal':
					localStorage.removeItem('responsavelLegalNome')
					localStorage.removeItem('responsavelLegalCpf')
					localStorage.removeItem('responsavelLegalRG')
					localStorage.removeItem('responsavelLegalOrgaoRG')
					localStorage.removeItem('responsavelLegalEmail')
					localStorage.removeItem('responsavelLegalRNE')
					localStorage.removeItem('responsavelLegalCelular')
					localStorage.removeItem('responsavelLegalCelularWhatsApp')
					localStorage.removeItem('responsavelLegalVisto')
					localStorage.removeItem('responsavelLegalTrabalhador')
					break;
				case 'filiacao1':
					localStorage.removeItem(campo)
					localStorage.removeItem('cpfFiliacao1')
					break;
				case 'filiacao2':
					localStorage.removeItem(campo)
					localStorage.removeItem('cpfFiliacao2')
					break
				default:
					localStorage.removeItem(campo)
					break
			}
		})
	}

	async getAlunoByCpf(cpf) {
		return (await window.axios.get(this.rotas.aluno + `/${cpf}`)).data.data
	}

	async getAlunoEscolaByCpf(escola, cpf) {
		let rota = this.rotas.alunoescola.replace('{escola}', escola) + '/' + cpf;
		return (await window.axios.get(rota)).data.data
	}

    async getAlunoByVisto(visto) {
        return (await window.axios.get(this.rotas.alunoVisto + `/${visto}`)).data.data
    }

    async getAlunoByRne(rne) {
        return (await window.axios.get(this.rotas.alunoRne + `/${rne}`)).data.data
    }

	async getCandidatoByCpf(fase, cpf) {
		let rota = this.rotas.candidato.replace('{fase}', fase).replace('{cpf}', cpf);
		return (await window.axios.get(rota)).data.data
	}

    async getCandidatoByVisto(fase, visto) {
        let rota = this.rotas.candidatoVisto.replace('{fase}', fase).replace('{visto}', visto);
        return (await window.axios.get(rota)).data.data
    }

    async getCandidatoByRne(fase, rne) {
        let rota = this.rotas.candidatoRne.replace('{fase}', fase).replace('{rne}', rne);
        return (await window.axios.get(rota)).data.data
    }

	async buildDadosPrefeitura () {
		let dados = {
			nome: 'PREFEITURA MUNICIPAL DBSeller',
			endereco: 'av. Júlio de Castilhos ,132 / 9º - sala 903 - Centro Histórico ,  Porto Alegre - RS - 90030-130'
		}
		await axios.get(this.rotas.dadosPrefeitura).then(response => {
			this.setStorageDadosPrefeitura(dados)
		})
	}
	setStorageDadosPrefeitura(value) {
		localStorage.setItem('dados-prefeitura', JSON.stringify(value))
	}
	getStorageDadosPrefeitura () {
		return JSON.parse(localStorage.getItem('dados-prefeitura'))
	}

	async buildTemFaseAberta () {
		await window.axios.get(this.rotas.temFaseAberta).then(response => {
            this.setStorageTemFaseAberta(response.data.data)
		})
	}

	setStorageTemFaseAberta(value) {
		localStorage.setItem('tem-fase-aberta', value)
	}
	getStorageTemFaseAberta () {
		return localStorage.getItem('tem-fase-aberta') === 'true'
	}
}
