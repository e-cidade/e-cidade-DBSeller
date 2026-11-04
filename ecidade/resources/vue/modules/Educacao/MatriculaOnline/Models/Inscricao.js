import {ProcessoInscricaoService} from "../Services/ProcessoInscricaoService.js";
import {Utils} from "../Utils/Utils.js";
import axios from "axios";

export class Inscricao {
	constructor() {
		this.service = new ProcessoInscricaoService()
        this.protocolo = {label: 'Protocolo', value: this.service.getCampoFormularioStorage('protocolo')}
		this.fase = this.service.getStorageFases()
		this.nome = {label: 'Nome', value: this.service.getCampoFormularioStorage('nome')}
		this.cpf = {label: 'CPF', value: this.service.getCampoFormularioStorage('cpf')}
		this.identidade = {label: 'Identidade', value: this.service.getCampoFormularioStorage('identidade')}
		this.orgaoIdentidade = {label: 'Órgão Identidade', value: this.service.getCampoFormularioStorage('orgaoIdentidade')}

		this.redeOrigem = {
			label: 'Rede de Origem',
			value: this.service.getCampoFormularioStorage('redeOrigem')
		}
		this.bolsaFamilia = {
			label: 'Bolsa Famíia',
			value: this.service.getCampoFormularioStorage('bolsaFamilia')
		}
		this.escolaOrigem = {
			label: 'Escola de Origem',
			value: this.service.getCampoFormularioStorage('escolaOrigem')
		}
		this.dataNascimento = {label: 'Data de Nascimento', value: this.service.getCampoFormularioStorage('dataNascimento')}
		this.nacionalidade = {
			label: 'Nacionalidade',
			value: this.service.getCampoFormularioStorage('nacionalidade')
		}
		this.nomeSocial = {label: 'Nome Social', value: this.service.getCampoFormularioStorage('nomeSocial')}
		this.etapaEnsino = {
			label: 'Etapa Selecionada',
			value: this.service.getCampoFormularioStorage('etapaEnsino')
		}
		this.estadoCertidao = {
			label: 'Estado Certidão',
			value: this.service.getCampoFormularioStorage('estadoCertidao')
		}
		this.municipioCertidao = {
			label: 'Municipio Certidão',
			value: this.service.getCampoFormularioStorage('municipioCertidao')
		}
		this.rne = {label: 'RNE', value: this.service.getCampoFormularioStorage('rne')}
		this.visto = {label: 'Visto', value: this.service.getCampoFormularioStorage('visto')}
		this.nomeCartorio = {label: 'Nome Cartório', value: this.service.getCampoFormularioStorage('nomeCartorio')}
		this.dataCertidao = {label: 'Data Cartório', value: this.service.getCampoFormularioStorage('dataCertidao')}
		this.livro = {label: 'Livro', value: this.service.getCampoFormularioStorage('livro')}
		this.termo = {label: 'Termo', value: this.service.getCampoFormularioStorage('termo')}
		this.folha = {label: 'Folha', value: this.service.getCampoFormularioStorage('folha')}
		this.sexo = {label: 'Sexo', value: this.service.getCampoFormularioStorage('sexo')}
		this.estadoCivil = {label: 'Estado Civil', value: this.service.getCampoFormularioStorage('estadoCivil')}
		this.cor = {
			label: 'Cor/Raça',
			value: this.service.getCampoFormularioStorage('cor')
		}
		this.estadoNascimento = {
			label: 'UF de Nascimento',
			value: this.service.getCampoFormularioStorage('estadoNascimento')
		}
		this.paisNascimento = {
			label: 'País de Nascimento',
			value: this.service.getCampoFormularioStorage('paisNascimento')
		}
		this.municipioNascimento = {
			label: 'Município de Nascimento',
			value: this.service.getCampoFormularioStorage('municipioNascimento')
		}
		this.irmaoGemeo = {label: 'Tem Irmão Gêmeo', value: this.service.getCampoFormularioStorage('irmaoGemeo')}
		this.necessidadesEspeciais = {
			label: 'Necessidades Especiais',
			value:  this.service.getCampoFormularioStorage('necessidadesEspeciais') === null ? null : this.service.getCampoFormularioStorage('necessidadesEspeciais').map(necessidade => {
				let necess = {
					label: necessidade.label,
					value: necessidade.value,
					subdivisoes: necessidade.subdivisoes.filter(sub => sub.selecionada)
				}
				return necess
			})
		}
		this.atestadoNecessidadeEspecial = {
			label: 'Laudo anexado',
			value:  this.service.getCampoFile('atestadoNecessidadeEspecial')
		}		
		this.cadeirante = {label: 'Cadeirante', value: this.service.getCampoFormularioStorage('cadeirante')}
		this.certidaoTipo = {
			label: 'Tipo de Certidão',
			value: this.service.getCampoFormularioStorage('certidaoTipo')
		}
		this.certidaoModelo = {
			label: 'Modelo de Certidão',
			value: this.service.getCampoFormularioStorage('certidaoModelo')
		}

		this.certidaoNumero = {label: 'Matrícula de Certidão', value: this.service.getCampoFormularioStorage('certidaoNumero')}
		this.cartaoSus = {label: 'Cartão SUS', value: this.service.getCampoFormularioStorage('cartaoSus')}
		this.email = {label: 'Email', value: this.service.getCampoFormularioStorage('email')}
		this.contatoPrincipal = {label: 'Contato Principal', value: this.service.getCampoFormularioStorage('contatoPrincipal')}
		this.contatoSecundario = {label: 'Contato Secundário', value: this.service.getCampoFormularioStorage('contatoSecundario')}
		this.cep = {label: 'CEP', value: this.service.getCampoFormularioStorage('cep')}

		this.logradouro = {label: 'Logradouro', value: this.service.getCampoFormularioStorage('logradouro')}
		this.numero = {label: 'Número', value: this.service.getCampoFormularioStorage('numero')}
		this.complemento = {label: 'Complemento', value: this.service.getCampoFormularioStorage('complemento')}
		this.bairro = {
			label: 'Bairro',
			value: this.service.getCampoFormularioStorage('bairro')
		}
		this.zonaResidencia = {
			label: 'Zona de Residência',
			value: this.service.getCampoFormularioStorage('zonaResidencia')
		}
		this.rendaFamiliar = {
			label: 'Renda Familiar',
			value: this.service.getCampoFormularioStorage('rendaFamiliar')
		}

		this.opcoesEscola = {
			label: 'Opções de Escola',
			value: this.service.getCampoFormularioStorage('opcoesEscola') === null ? null : this.service.getCampoFormularioStorage('opcoesEscola').map(opcao => {
				let op = {
					label: `${opcao.escola.nome} - ${opcao.escola.bairro} - ${opcao.escola.turno.descricao}`,
					value: opcao.escola,
					temIrmao: opcao.temIrmao,
					nomeIrmao: opcao.nomeIrmao,
				}
				return op
			})
		}
		this.filiacao1 = {
			label: 'Filiação 1',
			value: {
				nome: this.service.getCampoFormularioStorage('filiacao1'),
				cpf: this.service.getCampoFormularioStorage('cpfFiliacao1')
			}
		}
		this.filiacao2 = {
			label: 'Filiação 2',
			value: {
				nome: this.service.getCampoFormularioStorage('filiacao2'),
				cpf: this.service.getCampoFormularioStorage('cpfFiliacao2')
			}
		}
		this.profissaoFiliacao1 = {label:'Profissão 1',value:this.service.getCampoFormularioStorage('profissaoFiliacao1')}
		this.profissaoFiliacao2 = {label:'Profissão 2',value:this.service.getCampoFormularioStorage('profissaoFiliacao2')}
		this.tipoResponsavel = {label: 'Tipo Responsável', value: this.service.getCampoFormularioStorage('tipoResponsavel')}
		this.responsavelLegal = {
			label: 'Responsável Legal',
			value: {
				nome: this.service.getCampoFormularioStorage('responsavelLegalNome'),
				cpf: this.service.getCampoFormularioStorage('responsavelLegalCpf'),
				rg: this.service.getCampoFormularioStorage('responsavelLegalRG'),
				orgaoRG: this.service.getCampoFormularioStorage('responsavelLegalOrgaoRG'),
				email: this.service.getCampoFormularioStorage('responsavelLegalEmail'),
				rne: this.service.getCampoFormularioStorage('responsavelLegalRNE'),
				celular: this.service.getCampoFormularioStorage('responsavelLegalCelular'),
				celularWhatsApp: this.service.getCampoFormularioStorage('responsavelLegalCelularWhatsApp'),
				visto: this.service.getCampoFormularioStorage('responsavelLegalVisto'),
				trabalhador: this.service.getCampoFormularioStorage('responsavelLegalTrabalhador'),
				profissao:this.service.getCampoFormularioStorage('responsavelLegalProfissao')
			}
		}
		this.maeVitima = {
			label: 'Candidato Encaminhado por Poder Público, Em Acolhimento Institucional e/ou Mãe Vítima de violência',
			value: this.service.getCampoFormularioStorage('maeVitima')
		}
		this.matriculaServidor = {label: 'Matrícula Servidor Público', value: this.service.getCampoFormularioStorage('matriculaServidor')}
	}

	toTable(ignorar) {
		return Object.getOwnPropertyNames(this)
			.filter(prop => ignorar.indexOf(prop) == -1 && this[prop].value !== null).map(prop => {
			let obj = {campo: this[prop].label}
			switch (prop) {
				case 'necessidadesEspeciais':
					obj.valor = this.linhaNecessidades(this[prop].value)
					break;
				case 'atestadoNecessidadeEspecial':
					obj.valor = this.linhaAtestadoNecessidadeEspecial(this[prop].value)
					break;		
				case 'opcoesEscola':
					obj.valor = this.linhaOpcoesEscola(this[prop].value)
					break;
				case 'filiacao1':
					obj.valor = this.linhaFiliacao(this[prop].value)
					break
				case 'filiacao2':
					obj.valor = this.linhaFiliacao(this[prop].value)
					break
				case 'profissaoFiliacao1':
					obj.valor = this.linhaProfissoes(this[prop].value)
					break	
				case 'profissaoFiliacao2':
					obj.valor = this.linhaProfissoes(this[prop].value)
					break
				case 'responsavelLegal':
					obj.valor = this.linhaResponsavelLegal(this[prop].value)
					break
				default:
					if (typeof this[prop].value === 'object' && this[prop].value !== null) {
						obj.valor = this.linhaObjeto(this[prop].value)
					}
					if (typeof this[prop].value === 'string' || typeof this[prop].value === 'boolean') {
						obj.valor = this.linhaDefault(this[prop])
					}
			}
			return obj
		});

	}
	linhaDefault(value) {
		let valor = ''
		switch (value.value) {
			case true:
				valor = 'SIM'
				break
			case false:
				valor = 'NÃO'
				break
			case null:
				valor = 'Não Informado'
				break
			default:
				valor = value.value
		}
		return `<span>${valor}</span>`
	}

	linhaObjeto(valor) {
		return `<span>${valor.label}</span>`
	}

	linhaFiliacao(valor) {
		let cpf = valor.cpf === null ? 'NÃO INFORMADO' : valor.cpf
		return `<ul><li>Nome: ${valor.nome}</li><li>CPF: ${cpf}</li></ul>`
	}

	linhaAtestadoNecessidadeEspecial(valor) {
		if(valor == null || valor == 'null'){
			return false;
		}
		return ''
	}
	
	linhaOpcoesEscola(array) {
		let ul = `<ul>`
		array.forEach(row => {
			let string = row.label
			if (row.temIrmao) {
				string += " - Nome Irmão: " + row.nomeIrmao
			}
			ul += `<li>${string}</li>`
		})
		ul += `</ul>`
		return ul
	}

	linhaResponsavelLegal(value) {
		let table = document.createElement('table')
		table.createTHead()
		table.tHead.insertRow().insertCell()
		table.tHead.rows[0].insertCell()
		table.createTBody()
		table.style = 'font-family: arial, sans-serif; border-collapse: collapse;width: 50%;'
		Object.keys(value).forEach((row, key) => {
			let valor = value[row] === null ? 'Não Informado' : value[row]
			switch (row) {
				case 'nome':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Nome'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'cpf':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'CPF'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'rg':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'RG'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'orgaoRG':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Órgao'
					if (valor === 'null' || valor === 'Não Informado') {
						table.tBodies[0].rows[key].insertCell().innerHTML = 'Não Informado'

					} else {
						table.tBodies[0].rows[key].insertCell().innerHTML = valor.label
					}
					break
				case 'email':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Email'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'rne':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'RNE'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'celular':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Celular'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'celularWhatsApp':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Celular é WhatsApp'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor === true ? 'SIM' : 'NÃO'
				case 'visto':
					if (valor === true || valor === false) { break }
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Visto'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'trabalhador':
					valor = valor === true ? 'SIM' : 'NÃO'
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Mãe Economicamente Ativa'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'bolsaFamilia':
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Bolsa Família'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor
					break
				case 'profissao':
					if(this.service.getStorageApresentaProfissoes() == 'false' ||
						typeof valor.label !== 'string'
					){
						break
					}
					table.tBodies[0].insertRow(key).insertCell().innerHTML = 'Profissão'
					table.tBodies[0].rows[key].insertCell().innerHTML = valor.label
					break
			}
		})
		return table.outerHTML
	}

	linhaNecessidades(value) {
		let table = document.createElement('table')
		table.createTHead()
		table.tHead.insertRow().insertCell()
		table.tHead.rows[0].insertCell()
		table.createTBody()
		table.style = 'font-family: arial, sans-serif; border-collapse: collapse;width: 50%;'
		value.forEach((necessidade, key) => {
			table.tBodies[0].insertRow(key).insertCell().innerHTML = necessidade.label
			if (necessidade.subdivisoes.length > 0) {
				let ul = '<ul>'
				necessidade.subdivisoes.forEach(sub => {
					ul += `<li>${sub.label}</li>`
				})
				ul += '</ul>'
				table.tBodies[0].rows[key].insertCell().innerHTML = ul
			} else {
				table.tBodies[0].rows[key].insertCell().innerHTML = ''
			}
		})
		return table.outerHTML

	}

	linhaProfissoes(valor){
		if(this.service.getStorageApresentaProfissoes() == 'true' && 
		   !this.service.getStorageFases().ciclo.isEJA
	    ){
			return valor.label; 
		}
		return false;
	}


	toRequest() {
		return {
			ensino_infantil_creche: null,
			fase: this.fase.codigo,
			idade: null,
			codigo: null,
			protocolo: this.protocolo.value,
			data_nascimento: this.dataNascimento.value,
			hora_inscricao: null,
			etapa: this.etapaEnsino.value.value,
			rede_origem: this.redeOrigem.value.value,
			escola_rede_origem: this.escolaOrigem.value === null ? null : this.escolaOrigem.value.value,
			modelo_certidao: this.certidaoModelo.value === null ? null : this.certidaoModelo.value.value,
			nome: this.nome.value,
			sexo: this.sexo.value.value,
			nacionalidade: this.nacionalidade.value.value,
			estado_civil: this.estadoCivil.value === null ? 1 : this.estadoCivil.value.value,
			email: this.email.value,
			certidao_tipo: this.certidaoTipo.value === null ? null : this.certidaoTipo.value.value,
			certidao_livro: this.livro.value === null ? '00000000' : this.livro.value,
			certidao_folha: this.folha.value === null ? '0000' : this.folha.value,
			certidao_cartorio: this.nomeCartorio.value,
			certidao_numero: this.termo.value === null ? '00000000' : this.termo.value,
			certidao_data: this.dataCertidao.value === null ? '1900-01-01' : this.dataCertidao.value,
			uf_cartorio_certidao: this.estadoCertidao.value === null ? '0' :this.estadoCertidao.value.value,
			municipio_cartorio_certidao: this.municipioCertidao.value === null
				? '0' : this.municipioCertidao.value.value,
			certidao_matricula: this.certidaoNumero.value === null ?
				'0000000000000000000000' :
				Utils.removeCaracteres(this.certidaoNumero.value),
			uf_nascimento: this.estadoNascimento.value === null ? null : this.estadoNascimento.value.value,
			municipio_nascimento: this.municipioNascimento.value === null ? '0' : this.municipioNascimento.value.value,
			identidade: this.identidade.value,
			orgao_identidade: this.orgaoIdentidade.value === null ? null : this.orgaoIdentidade.value.label,
			cartao_sus: this.cartaoSus.value === null ? null : Utils.removeCaracteres(this.cartaoSus.value),
			raca: this.cor.value.label,
			cpf: this.cpf.value === null ? null : Utils.removeCaracteres(this.cpf.value),
			contato_principal: this.contatoPrincipal.value !== null ? Utils.removeCaracteres(this.contatoPrincipal.value) : '',
			contato_secundario: this.contatoSecundario.value !== null ? Utils.removeCaracteres(this.contatoSecundario.value) : '',
			irmao_gemeo: this.irmaoGemeo.value,
			protocolo_anterior: null,
			necessidade: this.necessidadesEspeciais.value,
			atestadoNecessidade: this.service.getCampoFile('atestadoNecessidadeEspecial'),
			zona_residencia: parseInt(this.zonaResidencia.value.value),
			cep: Utils.removeCaracteres(this.cep.value),
			tipo_endereco: 1,
			endereco: this.logradouro.value,
			numero: this.numero.value,
			complemento: this.complemento.value === null || this.complemento.value === '' ? '0' : this.complemento.value,
			uf_endereco: this.service.getStorageBairros()[0].uf,
			municipio_endereco: this.service.getStorageBairros()[0].munic,
			bairro: this.bairro.value.value,
			opcoes: Utils.mapperOpcoesEscola(this.opcoesEscola.value),
			responsavel_trabalhador: this.responsavelLegal.value.trabalhador,
			filiacao_1: this.filiacao1.value.nome,
			filiacao_2: this.filiacao2.value.nome,
			tipo_responsavel: this.tipoResponsavel.value,
			nome_responsavel: this.responsavelLegal.value.nome,
			servidor_publico_municipio: this.matriculaServidor.value !== null && this.matriculaServidor.value !== '',
			matricula_servidor: this.matriculaServidor.value,
			identidade_responsavel: this.responsavelLegal.value.rg,
			orgao_identidade_responsavel: this.responsavelLegal.value.orgaoRG === null ? null : this.responsavelLegal.value.orgaoRG.label,
			cpf_responsavel: Utils.removeCaracteres(this.responsavelLegal.value.cpf),
			telefone_responsavel: Utils.removeCaracteres(this.responsavelLegal.value.celular),
			telefone_responsavel_whatsapp: this.responsavelLegal.value.celularWhatsApp,
			email_responsavel: this.responsavelLegal.value.email,
			cartao_bolsa_familia: this.bolsaFamilia.value === null ? null :  Utils.removeCaracteres(this.bolsaFamilia.value),
			violencia_domestica: this.maeVitima.value,
			data_hora_modificacao: null,
			pais_nascimento: this.paisNascimento.value === null ? null : this.paisNascimento.value.value,
			visto: this.visto.value,
			visto_responsavel: this.responsavelLegal.value.visto === null ? null : this.responsavelLegal.value.visto,
			rnm: this.rne.value,
			rnm_responsavel: this.responsavelLegal.value.rne === null ? null : this.responsavelLegal.value.rne,
			termo_aceito: true,
			time: null,
			data_cadastro: new Date().toISOString().slice(0, 10),
			cadeirante: this.cadeirante.value,
			cpfFiliacao1: Utils.removeCaracteres(this.filiacao1.value.cpf),
			cpfFiliacao2: Utils.removeCaracteres(this.filiacao2.value.cpf),
			nomeSocial: this.nomeSocial.value,
			rendaFamiliar: this.rendaFamiliar.value === null ? null : this.rendaFamiliar.value.value,
			profissao1: this.profissaoFiliacao1.value === null ? null : this.profissaoFiliacao1.value.value,
			profissao2: this.profissaoFiliacao2.value === null ? null : this.profissaoFiliacao2.value.value,
			responsavelLegalProfissao: this.responsavelLegal.value.profissao === null ? null : this.responsavelLegal.value.profissao.value
		}
	}

	async enviarInscricao() {
		return await window.axios.post('v4/api/educacao/central-de-matriculas/processo-inscricao/inscricao', this.toRequest())
	}
}

