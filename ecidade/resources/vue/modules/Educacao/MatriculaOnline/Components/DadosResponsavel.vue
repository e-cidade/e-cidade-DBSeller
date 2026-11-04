<script setup>
import { ref, onMounted } from "vue"
import ModalLoading from "../../../Components/ModalLoading.vue";
import { useToast } from 'primevue/usetoast';

import { Utils } from "../Utils/Utils.js";
import { Inscricao } from "../Models/Inscricao.js";

const toast = useToast()
const emits = defineEmits(['campos-preenchidos'])
const campos = ref()

const inscricao = new Inscricao()

const loading = ref(false)
const naoFiliacao1 = ref(false)
const naoFiliacao2 = ref(false)

const rendaFamiliar = ref()
const maeVitima = ref()
const matriculaServidor = ref()
const nacionalidade = ref(inscricao.nacionalidade.value.value)
const exibeMaisContato = ref(false)

const optionRendaFamiliar = ref()
const optionsOrgao = ref()

const adicionarContatos = ref(false)
const mensagensValidacao = ref([])

const profissoes = ref()
const profissaoFiliacao1 = ref();
const profissaoFiliacao2 = ref();
const responsavelLegalProfissao = ref();

const form = ref({
	filiacao1: {
		label: 'Filiação 1',
		data: null,
		required: [val => true || 'Campo Obrigatório'],
		disabled: false
	},
	cpfFiliacao1: {
		label: 'CPF Filiação 1',
		data: null,
		required: [val => true || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF Inválido'],
		disabled: false
	},
	profissaoFiliacao1:{
		label:'Profissão 1',
		data:null,
		required: [val => true || 'Campo Obrigatório'],
		disabled:false
	},
	filiacao2: {
		label: 'Filiação 2',
		data: null,
		required: [val => true || 'Campo Obrigatório'],
		disabled: false
	},
	cpfFiliacao2: {
		label: 'CPF Filiação 2',
		data: null,
		required: [val => true || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF Inválido'],
		disabled: false
	},
	profissaoFiliacao2:{
		label:'Profissão 2',
		data:null,
		required: [val => true || 'Campo Obrigatório'],
		disabled:false
	},
	responsavelLegalNome: {
		label: 'Nome do Responsável Legal',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	responsavelLegalRG: {
		label: 'Identidade do Responsável Legal(RG)',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	responsavelLegalOrgaoRG: {
		label: 'Órgão Emissor',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	responsavelLegalCpf: {
		label: 'CPF',
		data: null,
		required: [val => !!val || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF Inválido']
	},
	responsavelLegalCelular: {
		label: 'Celular (com DDD)',
		data: null,
		required: [val => !!val || 'Campo Obrigatório', val => Utils.validaTamanhoCampoTelefone(val) || 'Deve conter 11 carceteres.']
	},
	responsavelLegalCelularWhatsApp: {
		label: 'Celular é WhatsApp',
		data: false,
		required: [val => !!val || 'Campo Obrigatório']
	},
	responsavelLegalEmail: {
		label: 'E-mail',
		data: null,
		required: [val => true || 'Campo Obrigatório']
	},
	responsavelLegalRNE: {
		label: 'RNE',
			data: null,
			required: [val => true || 'Campo Obrigatório']
	},
	responsavelLegalVisto: {
		label: 'Visto',
		data: null,
		required: [val => true || 'Campo Obrigatório']
	},
	responsavelLegalTrabalhador: {
		label: 'Mãe Economicamente Ativa',
		data: false,
		required: [val => !!val || 'Campo Obrigatório']
	},
	responsavelLegalProfissao:{
		label:'Profissão do Responsável Legal',
		data:null,
		required: [val => true || 'Campo Obrigatório'],
		disabled:false
	},
	maeVitima: {
		label: 'Candidato encaminhado pelo Ministério Público, Defensoria Pública, Promotoria de Justiça, Conselho Tutelar, em Acolhimento Institucional e/ou mãe vítima de violência doméstica/familiar',
		data: false,
		required: [val => maeVitima.value.obrigatorio ? !!val : true || 'Campo Obrigatório']
	},
	matriculaServidor: {
		label: 'Matrícula do Servidor Público',
		data: null,
		required: [val => matriculaServidor.value.obrigatorio ? !!val : true || 'Campo Obrigatório']
	},
	rendaFamiliar: {
		label: 'Renda Familiar',
		data: null,
		required: [val => rendaFamiliar.value.obrigatorio ? !!val : true || 'Campo Obrigatório']
	},
	tipoResponsavel: {
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	email: {
		label: 'Email Secundario',
		data: null,
		required: [true || 'Campo Obrigatório']
	},
	contatoPrincipal: {
		label: 'Contato Principal (com DDD)',
		data: null,
		required: [val => true || 'Campo Obrigatório', val => Utils.validaTamanhoCampoTelefone(val) || 'Deve conter 11 carceteres.'],
	},
	contatoSecundario: {
		label: 'Contato Secundário (com DDD)',
		data: null,
		required: [val => true || 'Campo Obrigatório', val => Utils.validaTamanhoCampoTelefone(val) || 'Deve conter 11 carceteres.']
	}
})

const changeResponsavel = () => {
	switch (form.value.tipoResponsavel.data) {
		case '1':
			form.value.responsavelLegalRG.data = inscricao.responsavelLegal.value.rg
		    form.value.responsavelLegalOrgaoRG.data = inscricao.responsavelLegal.value.orgaoRG
		    form.value.responsavelLegalEmail.data = inscricao.responsavelLegal.value.email
		    form.value.responsavelLegalCelular.data = inscricao.responsavelLegal.value.celular		
			form.value.responsavelLegalNome.data = form.value.filiacao1.data
			form.value.responsavelLegalCpf.data = form.value.cpfFiliacao1.data
			form.value.responsavelLegalProfissao.data = form.value.profissaoFiliacao1.data
			break;
		case '2':
			form.value.responsavelLegalRG.data = inscricao.responsavelLegal.value.rg
		    form.value.responsavelLegalOrgaoRG.data = inscricao.responsavelLegal.value.orgaoRG
		    form.value.responsavelLegalEmail.data = inscricao.responsavelLegal.value.email
		    form.value.responsavelLegalCelular.data = inscricao.responsavelLegal.value.celular			
			form.value.responsavelLegalNome.data = form.value.filiacao2.data
			form.value.responsavelLegalCpf.data = form.value.cpfFiliacao2.data
			form.value.responsavelLegalProfissao.data = form.value.profissaoFiliacao2.data
			break;
		case '4':
			form.value.responsavelLegalNome.data = null;
		    form.value.responsavelLegalProfissao.data   = null;
			form.value.responsavelLegalOrgaoRG.data = null;
			form.value.responsavelLegalRG.data = null;
			form.value.responsavelLegalCpf.data = null;
			form.value.responsavelLegalCelular.data = null;
		    form.value.responsavelLegalEmail.data = null;

			break;
	}
	verificaCpf()
	validaCampos()
}

const handleAdicionarContatos = () => {
    exibeMaisContato.value = !exibeMaisContato.value
}

const invalidaFiliacao1 = () => {
	if (naoFiliacao1.value) {
		form.value.filiacao1.data = 'NÃO INFORMADO'
		form.value.filiacao1.disabled = true
		form.value.cpfFiliacao1.data = null
		form.value.cpfFiliacao1.disabled = true
		profissaoFiliacao1.value = null
		form.value.profissaoFiliacao1.disabled = true
	} else if (!naoFiliacao1.value) {
		form.value.filiacao1.data = null
		form.value.filiacao1.disabled = false
		form.value.cpfFiliacao1.disabled = false
		form.value.profissaoFiliacao1.disabled = false
	}
	validaCampos()
}
const invalidaFiliacao2 = () => {
	if (naoFiliacao2.value) {
		form.value.filiacao2.data = 'NÃO INFORMADO'
		form.value.filiacao2.disabled = true
		form.value.cpfFiliacao2.data = null
		form.value.cpfFiliacao2.disabled = true
		profissaoFiliacao2.value = null
		form.value.profissaoFiliacao2.disabled = true
	} else if (!naoFiliacao2.value) {
		form.value.filiacao2.data = null
		form.value.filiacao2.disabled = false
		form.value.cpfFiliacao2.disabled = false
		form.value.profissaoFiliacao2.disabled = false
	}
}

const validaCampos = () => {
	mensagensValidacao.value = []

	let camposObrigatorios = [
		form.value.responsavelLegalNome.data !== null && form.value.responsavelLegalNome.data !== '',
		form.value.responsavelLegalCelular.data !== null && form.value.responsavelLegalCelular.data !== '' && Utils.validaTamanhoCampoTelefone(form.value.responsavelLegalCelular.data),
		form.value.responsavelLegalTrabalhador.data !== null && form.value.responsavelLegalTrabalhador.data !== '',
	]

	if (nacionalidade.value !== 3) {
		form.value.responsavelLegalCpf.required = [val => !!val || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF Inválido']
		camposObrigatorios.push(form.value.responsavelLegalCpf.data !== null && form.value.responsavelLegalCpf.data !== '' && Utils.validaCPF(form.value.responsavelLegalCpf.data))
		camposObrigatorios.push(form.value.responsavelLegalRG.data !== null && form.value.responsavelLegalRG.data !== '')
		camposObrigatorios.push(form.value.responsavelLegalOrgaoRG.data !== null && form.value.responsavelLegalOrgaoRG.data !== '')

		if (form.value. cpfFiliacao1.data !== null && form.value. cpfFiliacao1.data !== '' && !Utils.validaCPF(form.value. cpfFiliacao1.data)) { mensagensValidacao.value.push(' CPF da Filiação 1 inválido') }
		if (form.value. cpfFiliacao2.data !== null && form.value. cpfFiliacao2.data !== '' && !Utils.validaCPF(form.value. cpfFiliacao2.data)) { mensagensValidacao.value.push(' CPF da Filiação 2 inválido') }
		if (form.value. responsavelLegalCpf.data !== null && form.value. responsavelLegalCpf.data !== '' && !Utils.validaCPF(form.value. responsavelLegalCpf.data)) { mensagensValidacao.value.push(' CPF do Responsável Legal inválido') }
	}

	if (nacionalidade.value != null && nacionalidade.value == 3) {
		form.value.responsavelLegalRNE.required = [val => !!val || 'Campo Obrigatório']
		form.value.responsavelLegalVisto.required = [val => !!val || 'Campo Obrigatório']
		form.value.responsavelLegalCpf.required = [val => !!val || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF Inválido']

		if (form.value.responsavelLegalVisto.data !== null && form.value.responsavelLegalVisto.data !== '') {
			form.value.responsavelLegalRNE.required = [val => true || 'Campo Obrigatório']
			form.value.responsavelLegalCpf.required = [val => true || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF Inválido']
		}

		if (form.value.responsavelLegalRNE.data !== null && form.value.responsavelLegalRNE.data !== '') {
			form.value.responsavelLegalVisto.required = [val => true || 'Campo Obrigatório']
			form.value.responsavelLegalCpf.required = [val => true || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF Inválido']
		}

		if (form.value.responsavelLegalCpf.data !== null && form.value.responsavelLegalCpf.data !== '' && Utils.validaCPF(form.value.responsavelLegalCpf.data)) {
			form.value.responsavelLegalVisto.required = [val => true || 'Campo Obrigatório']
			form.value.responsavelLegalRNE.required = [val => true || 'Campo Obrigatório']
		}

		camposObrigatorios.push((
			(form.value.responsavelLegalVisto.data !== null && form.value.responsavelLegalVisto.data !== '') ||
			(form.value.responsavelLegalRNE.data !== null && form.value.responsavelLegalRNE.data !== '') ||
			(form.value.responsavelLegalCpf.data !== null && form.value.responsavelLegalCpf.data !== '' && Utils.validaCPF(form.value.responsavelLegalCpf.data))
		))
	}


	if (campos.value.renda_familiar.obrigatorio) {
		camposObrigatorios.push(form.value.rendaFamiliar.data !== null && form.value.rendaFamiliar.data !== '')
	}

	if (campos.value.mae_vitima_violencia.obrigatorio) {
		camposObrigatorios.push(form.value.maeVitima.data !== null && form.value.maeVitima.data !== '')
	}
	form.value.camposPreenchidos = camposObrigatorios.indexOf(false) == -1
	emits('campos-preenchidos', form.value)
}

const preencheCamposStorage = async () => {
	if (inscricao.responsavelLegal.value.nome !== null) {
		form.value.filiacao1.data = inscricao.filiacao1.value.nome
		let maeVitima = false
		if (inscricao.maeVitima !== undefined) {
			if (inscricao.maeVitima.value !== null) {
				maeVitima = inscricao.maeVitima.value
			}
		}
		form.value.maeVitima.data = maeVitima
		form.value.tipoResponsavel.data = inscricao.tipoResponsavel.value
		form.value.filiacao2.data = inscricao.filiacao2.value.nome
		form.value.responsavelLegalNome.data = inscricao.responsavelLegal.value.nome
		form.value.responsavelLegalRG.data = inscricao.responsavelLegal.value.rg
		let orgaoIdentidadeSelecionada = []
		const orgaoIdentidadeLocalStorage = inscricao.responsavelLegal.value.orgaoRG
		orgaoIdentidadeSelecionada = optionsOrgao.value.filter((f) => f.label == orgaoIdentidadeLocalStorage.label)
		form.value.responsavelLegalOrgaoRG.data = null
		if (orgaoIdentidadeSelecionada.length > 0) {
			form.value.responsavelLegalOrgaoRG.data = orgaoIdentidadeSelecionada[0]
		}
		form.value.responsavelLegalCpf.data = inscricao.responsavelLegal.value.cpf
		form.value.responsavelLegalEmail.data = inscricao.responsavelLegal.value.email
		form.value.responsavelLegalCelular.data = inscricao.responsavelLegal.value.celular
		form.value.responsavelLegalCelularWhatsApp.data = inscricao.responsavelLegal.value.celularWhatsApp
		form.value.cpfFiliacao1.data = inscricao.filiacao1.value.cpf
		form.value.cpfFiliacao2.data = inscricao.filiacao2.value.cpf
		form.value.rendaFamiliar.data = inscricao.rendaFamiliar.value
		form.value.email.data = inscricao.email.value
		form.value.contatoPrincipal.data = inscricao.contatoPrincipal.value
		form.value.contatoSecundario.data = inscricao.contatoSecundario.value
		naoFiliacao1.value = form.value.filiacao1.data === 'NÃO INFORMADO'
		naoFiliacao2.value = form.value.filiacao2.data === 'NÃO INFORMADO'
        if (inscricao.nacionalidade.value.value === 3) {
            form.value.responsavelLegalRNE.data = inscricao.responsavelLegal.value.rne
            form.value.responsavelLegalVisto.data = inscricao.responsavelLegal.value.visto
        }
		if (naoFiliacao1.value) {
			invalidaFiliacao1()
		}
		if (naoFiliacao2.value) {
			invalidaFiliacao2()
		}
	}
}
const verificaCpf = () => {
	let cpf = Utils.removeCaracteres(form.value.responsavelLegalCpf.data)
	let cpfCandidato = Utils.removeCaracteres(inscricao.cpf.value)
	if (Utils.validaCPF(cpfCandidato)) {
		if (cpf === cpfCandidato) {
			toast.add({ severity: 'error', summary: 'Erro', detail: 'O CPF do Responsável não pode ser o mesmo do candidato!', life: 5000 });
			form.value.responsavelLegalCpf.data = null
		}
	}
	validaCampos()
}

const limitaCampo = (length, value) => {
    value.data = value.data.slice(0, length)
    validaCampos()
}

onMounted( async () => {
    try {
        loading.value = true
		let fase = inscricao.service.getStorageFases()
        campos.value = (await axios.get('v4/api/educacao/files/campos-opcionais')).data
        optionRendaFamiliar.value = (await axios.get('v4/api/educacao/files/rendas')).data
        optionsOrgao.value = (await axios.get('v4/api/educacao/files/orgaos')).data

        form.value.matriculaServidor.show = campos.value.matricula_servidor_publico.apresenta
        form.value.maeVitima.show = campos.value.mae_vitima_violencia.apresenta
        form.value.rendaFamiliar.show = campos.value.renda_familiar.apresenta

        form.value.matriculaServidor.required =[val =>campos.value.matricula_servidor_publico.obrigatorio ? !!val : true || 'Campo Obrigatório']
        form.value.maeVitima.required =  [val => campos.value.mae_vitima_violencia.obrigatorio ? !!val : true || 'Campo Obrigatório']
        form.value.rendaFamiliar.required = [val => campos.value.renda_familiar.obrigatorio ? !!val : true || 'Campo Obrigatório']

		form.value.profissaoFiliacao1.show = false
    	form.value.profissaoFiliacao2.show = false
		form.value.responsavelLegalProfissao.show = false
    	profissoes.value = inscricao.service.getStorageProfissoes();
		inscricao.service.setStorageApresentaProfissoes(false);

        await preencheCamposStorage()
        validaCampos()
        loading.value = false
    } catch(e) {
		console.log('erro ', e.message)
        loading.value = false
    }
})
</script>
<template>
    <div class="border-2 border-dashed surface-border border-round surface-ground flex-auto flex justify-content-center align-items-center font-medium">
        <div class="p-fluid grid mt-3" style="max-width: 450px;">
			<div class="card mb-4 pl-4 pr-4 flex flex-wrap align-items-center justify-content-center gap-3 " v-if="mensagensValidacao.length > 0">
                <InlineMessage severity="error" v-for="mensagem in mensagensValidacao">{{ mensagem }}</InlineMessage>
            </div>

            <div class="field col-12 md:col-12">
                <InputSwitch v-model="naoFiliacao1" aria-labelledby="naoFiliacao1" @update:model-value="invalidaFiliacao1" />
                <span class="ml-2" id="naoFiliacao1=">Filiação 1 não informada</span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.filiacao1.data" 
                    :placeholder="form.filiacao1.label"
                    :disabled="form.filiacao1.disabled" @update:model-value="limitaCampo(70, form.filiacao1)" />
                    <label for="">{{ form.filiacao1.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.cpfFiliacao1.data" 
                    :placeholder="form.cpfFiliacao1.label"
                    :disabled="form.cpfFiliacao1.disabled"
                    v-mask="'###.###.###-##'" @update:model-value="validaCampos" />
                    <label for="">{{ form.cpfFiliacao1.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12" v-show ="form.profissaoFiliacao1.show">
                <span class="p-float-label">
                    <Dropdown v-model="form.profissaoFiliacao1.data"
					:disabled="form.profissaoFiliacao1.disabled"
					:label="form.profissaoFiliacao1.label"
                    :options="profissoes" optionLabel="label" @update:model-value="validaCampos" />
                    <label for="" v-show ="form.profissaoFiliacao1.show">{{ form.profissaoFiliacao1.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <InputSwitch v-model="naoFiliacao2" aria-labelledby="naoFiliacao2" @update:model-value="invalidaFiliacao2" />
                <span class="ml-2" id="naoFiliacao2">Filiação 2 não informada</span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.filiacao2.data" 
                    :placeholder="form.filiacao2.label"
                    :disabled="form.filiacao2.disabled" @update:model-value="limitaCampo(70, form.filiacao2)" />
                    <label for="">{{ form.filiacao2.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.cpfFiliacao2.data" 
                    :placeholder="form.cpfFiliacao2.label"
                    :disabled="form.cpfFiliacao2.disabled"
                    v-mask="'###.###.###-##'" @update:model-value="validaCampos" />
                    <label for="">{{ form.cpfFiliacao2.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12" v-show ="form.profissaoFiliacao2.show">
                <span class="p-float-label">
                    <Dropdown v-model="form.profissaoFiliacao2.data"
					:disabled="form.profissaoFiliacao2.disabled"
					:label="form.profissaoFiliacao2.label"
                    :options="profissoes" optionLabel="label" @update:model-value="validaCampos" />
                    <label for="" v-show ="form.profissaoFiliacao2.show">{{ form.profissaoFiliacao2.label }}</label>
                </span>
            </div>

            <div class="flex flex-wrap gap-3 pl-3 col-12 md:col-12">
                <div class="flex align-items-center">
                    <RadioButton v-model="form.tipoResponsavel.data" :disable="naoFiliacao1" value="1" @update:model-value="changeResponsavel" />
                    <label for="ingredient1" class="ml-2">Filição 1</label>
                </div>
                <div class="flex align-items-center">
                    <RadioButton v-model="form.tipoResponsavel.data" :disable="naoFiliacao2" value="2" @update:model-value="changeResponsavel" />
                    <label for="ingredient2" class="ml-2">Filiação 2</label>
                </div>
                <div class="flex align-items-center">
                    <RadioButton v-model="form.tipoResponsavel.data" value="4" @update:model-value="changeResponsavel" />
                    <label for="ingredient3" class="ml-2">Outro</label>
                </div>
            </div>

            <div class="field col-12 md:col-12 mt-4">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.responsavelLegalNome.data" 
					:placeholder="form.responsavelLegalNome.label"
					@update:model-value="limitaCampo(70, form.responsavelLegalNome)" />
                    <label for="">{{ form.responsavelLegalNome.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12" v-show ="form.responsavelLegalProfissao.show">
                <span class="p-float-label">
                    <Dropdown v-model="form.responsavelLegalProfissao.data"
					:disabled="form.responsavelLegalProfissao.disabled"
					:label="form.responsavelLegalProfissao.label"
                    :options="profissoes" optionLabel="label" @update:model-value="validaCampos" />
                    <label for="" v-show ="form.responsavelLegalProfissao.show">{{ form.responsavelLegalProfissao.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="nacionalidade != 3">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.responsavelLegalRG.data" 
					:placeholder="form.responsavelLegalRG.label"
					@update:model-value="limitaCampo(20, form.responsavelLegalRG)" />
                    <label for="" v-show="nacionalidade != 3">{{ form.responsavelLegalRG.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="nacionalidade != 3">
                <span class="p-float-label">
                    <Dropdown v-model="form.responsavelLegalOrgaoRG.data"
                    :options="optionsOrgao" optionLabel="label" @update:model-value="validaCampos" />
                    <label for="" v-show="nacionalidade != 3">{{ form.responsavelLegalOrgaoRG.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.responsavelLegalCpf.data" 
					v-mask="'###.###.###-##'" :placeholder="form.responsavelLegalCpf.label"
					@update:model-value="verificaCpf" />
                    <label for="">{{ form.responsavelLegalCpf.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12" v-show="nacionalidade == 3">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.responsavelLegalVisto.data" 
					:placeholder="form.responsavelLegalVisto.label"
					@update:model-value="limitaCampo(20, form.responsavelLegalVisto)" />
                    <label for="" v-show="nacionalidade == 3">{{ form.responsavelLegalVisto.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12" v-show="nacionalidade == 3">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.responsavelLegalRNE.data" 
					:placeholder="form.responsavelLegalRNE.label"
					@update:model-value="limitaCampo(20, form.responsavelLegalRNE)" />
                    <label for="" v-show="nacionalidade == 3">{{ form.responsavelLegalRNE.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.responsavelLegalCelular.data" 
					v-mask="'(##) # ####-####'" 
					:placeholder="form.responsavelLegalCelular.label"
					@update:model-value="validaCampos" />
                    <label for="">{{ form.responsavelLegalCelular.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <InputSwitch v-model="form.responsavelLegalCelularWhatsApp.data" aria-labelledby="celular" 
				@update:model-value="validaCampos" />
                <span class="ml-2" id="celular">{{ form.responsavelLegalCelularWhatsApp.label }}</span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.responsavelLegalEmail.data" 
					:placeholder="form.responsavelLegalEmail.label"
					@update:model-value="limitaCampo(50, form.responsavelLegalEmail)" />
                    <label for="">{{ form.responsavelLegalEmail.label }}</label>
                </span>
            </div>
            <div>
                <div class="field col-12 md:col-12">
                    <span class="p-float-label">
                        <Button class="" type="button" label="Adicionar/Remover mais contatos" @click="handleAdicionarContatos" />
                    </span>
                </div>
            </div>

            <div class="field col-12 md:col-12 mt-2" v-if="exibeMaisContato">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.email.data" 
                    :placeholder="form.email.label"
                    v-if="exibeMaisContato" @update:model-value="limitaCampo(50, form.email)" />
                    <label for="">{{ form.email.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-if="exibeMaisContato">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.contatoPrincipal.data"  
                    v-mask="'(##) # ####-####'" 
                    :placeholder="form.contatoPrincipal.label"
                    v-if="exibeMaisContato" @update:model-value="validaCampos" />
                    <label for="">{{ form.contatoPrincipal.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-if="exibeMaisContato">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.contatoSecundario.data"  
                    v-mask="'(##) # ####-####'" 
                    :placeholder="form.contatoSecundario.label"
                    v-if="exibeMaisContato" @update:model-value="validaCampos" />
                    <label for="">{{ form.contatoSecundario.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <InputSwitch v-model="form.responsavelLegalTrabalhador.data" 
				aria-labelledby="responsavelLegalTrabalhador" @update:model-value="validaCampos" />
                <span class="ml-2" id="responsavelLegalTrabalhador">{{ form.responsavelLegalTrabalhador.label }}</span>
            </div>

            <div class="field col-12 md:col-12" v-show="form.maeVitima.show">
                <InputSwitch v-show="form.maeVitima.show" 
				v-model="form.maeVitima.data" aria-labelledby="maeVitima" 
				@update:model-value="validaCampos" />
                <span class="ml-2" id="maeVitima">{{ form.maeVitima.label }}</span>
            </div>

            <div class="field col-12 md:col-12" v-show="form.rendaFamiliar.show">
                <span class="p-float-label">
                    <Dropdown v-show="form.rendaFamiliar.show" 
					v-model="form.rendaFamiliar.data"
                    :options="optionRendaFamiliar" optionLabel="label" @update:model-value="validaCampos" />
                    <label for="">{{ form.rendaFamiliar.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12" v-if="exibeMaisContato">
                <span class="p-float-label">
                    <InputText type="text" v-show="form.matriculaServidor.show"
					v-model="form.matriculaServidor.data"  
                    v-mask="'##########'"
                    :placeholder="form.matriculaServidor.label" @update:model-value="validaCampos" />
                    <label for="" v-show="form.matriculaServidor.show">{{ form.matriculaServidor.label }}</label>
                </span>
            </div>
        </div>
    </div>
    <ModalLoading :isLoading="loading"/>
</template>
<style scoped>
    .p-inputtext{ background-color: #fff; border: 1px solid rgba(0, 0, 0, 0.12);}
    .p-dropdown{ border: 1px solid rgba(0, 0, 0, 0.12);}
    .border-2,.border-dashed {padding: 10px 5px !important;}
</style>