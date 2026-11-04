<script setup>
import { ref, onMounted } from "vue"
import ModalLoading from "../../../Components/ModalLoading.vue";
import { useToast } from 'primevue/usetoast';

import { Utils } from "../Utils/Utils.js";
import { Inscricao } from "../Models/Inscricao.js";
import { CensoLocalidadesService } from "../Services/CensoLocalidadesService.js";

const toast = useToast()

const campos = ref()
const emits = defineEmits(['campos-preenchidos'])
const loading = ref(false)

const inscricao = new Inscricao()
const censo = new CensoLocalidadesService()

const naoIdentidade = ref(false)
const bolsaFamilia = ref()
const nacionalidade = ref(inscricao.nacionalidade.value.value)

const optionsTipo = ref([
	{label: 'NASCIMENTO', value: 1},
	{label: 'CASAMENTO', value: 2}
])
const optionsModelo =  ref([
	{label: 'MODELO ANTIGO', value: 1},
	{label: 'MODELO NOVO', value: 2}
])

const optionsMunicipio = ref()
const optionsEstado = ref()
const optionsOrgao = ref()

const form = ref({
	certidaoTipo: {
		label: 'Certidão',
		data: nacionalidade.value === 3 ? null : optionsTipo.value[0],
		required: [val => !!val || 'Campo Obrigatório']
	},
	certidaoModelo: {
		label: 'Modelo de Certidão',
		data: nacionalidade.value === 3 ? null : optionsModelo.value[1],
		required: [val => !!val || 'Campo Obrigatório'],
	},
	certidaoNumero: {
		label: 'Matrícula da Certidão',
		data: null,
		required: [val => !!val || 'Campo Obrigatório', val => Utils.validaTamanhoCampoMatriculaCertidao(val) || 'Deve conter 32 Caracteres.']

	},
	identidade: {
		label: 'Identidade do Candidato (RG)',
		data: null,
		required: [val => true || 'Campo Obrigatório'],
		disabled: false
	},
	orgaoIdentidade: {
		label: 'Órgão Emissor',
		data: null,
		required: [val => true || 'Campo Obrigatório'],
		disabled: false
	},
	cartaoSus: {
		label: 'Cartão SUS',
		data: null,
		required: [val => true || 'Campo Obrigatório'],
	},
	livro: {
		label: 'Livro',
		data: null,
		required: [val => !!val || 'Campo Obrigatório'],
		show: false
	},
	folha: {
		label: 'Folha',
		data: null,
		required: [val => !!val || 'Campo Obrigatório'],
		show: false
	},
	termo: {
		label: 'Nº da Certidão ou Termo',
		data: null,
		required: [val => !!val || 'Campo Obrigatório'],
		show: false
	},
	dataCertidao: {
		label: 'Data da Certidão',
		data: null,
		required: [val => !!val || 'Campo Obrigatório'],
		show: false
	},
	estadoCertidao: {
		label: 'UF Certidão',
		data: null,
		required: [val => !!val || 'Campo Obrigatório'],
		show: false
	},
	municipioCertidao: {
		label: 'Município Certidão',
		data: null,
		required: [val => !!val || 'Campo Obrigatório'],
		show: false
	},
	nomeCartorio: {
		label: 'Nome do Cartório',
		data: null,
		required: [val => true || 'Campo Obrigatório'],
		show: false
	},
	bolsaFamilia: {
		label: 'Nº Cartão Bolsa Família',
		data: null
	},
})

const validaModeloCertidao = async () => {
	if (form.value.certidaoModelo.data.value == 1) {
		optionsEstado.value = await censo.getEstados()
	}
	validaCampos()
}

const bloqueiaCampoIdentidade = () => {
	form.value.identidade.data = null
	form.value.orgaoIdentidade.data = null
	form.value.identidade.disabled = naoIdentidade.value
	form.value.orgaoIdentidade.disabled = naoIdentidade.value
	validaCampos()
}

const limitaCampo = (length, value) => {
    value.data = value.data.slice(0, length)
    validaCampos()
}

const buscaMunicipios = async () => {
	if (form.value.estadoCertidao.data !== null && form.value.estadoCertidao.data !== 'null') {
		optionsMunicipio.value = await censo.getMunicipios(form.value.estadoCertidao.data.value)
	}
}
const validaCampos = () => {

	let camposObrigatorios = []
	if (nacionalidade.value !== 3) {
		camposObrigatorios.push(form.value.certidaoTipo.data !== null && form.value.certidaoTipo.data !== '')
		camposObrigatorios.push(form.value.certidaoModelo.data !== null && form.value.certidaoModelo.data !== '')

		if (form.value.certidaoModelo.data !== null && form.value.certidaoModelo.data.value == 1) {
            form.value.certidaoNumero.data = null
			camposObrigatorios.push(form.value.livro.data !== null && form.value.livro.data !== '')
			camposObrigatorios.push(form.value.folha.data !== null && form.value.folha.data !== '')
			camposObrigatorios.push(form.value.dataCertidao.data !== null && form.value.dataCertidao.data !== '')
			camposObrigatorios.push(form.value.estadoCertidao.data !== null && form.value.estadoCertidao.data !== '')
			camposObrigatorios.push(form.value.municipioCertidao.data !== null && form.value.municipioCertidao.data !== '')
			camposObrigatorios.push(form.value.nomeCartorio.data !== null && form.value.nomeCartorio.data !== '')
		} else {
			camposObrigatorios.push(form.value.certidaoNumero.data !== null && form.value.certidaoNumero.data !== '' && Utils.validaTamanhoCampoMatriculaCertidao(form.value.certidaoNumero.data))
			form.value.livro.data = null
			form.value.folha.data = null
			form.value.dataCertidao.data = null
			form.value.estadoCertidao.data = null
			form.value.municipioCertidao.data = null
			form.value.nomeCartorio.data = null
		}

		if (!naoIdentidade.value) {
			camposObrigatorios.push(form.value.identidade.data !== null && form.value.identidade.data !== '')
			camposObrigatorios.push(form.value.orgaoIdentidade.data !== null && form.value.orgaoIdentidade.data !== '')
		}
	}

	if (campos.value.cartao_sus.obrigatorio) {
		form.value.cartaoSus.required = [val => !!val || 'Campo Obrigatório'],
		camposObrigatorios.push(form.value.cartaoSus.data !== null && form.value.cartaoSus.data !== '')
	}

	if (campos.value.bolsa_familia.obrigatorio) {
		camposObrigatorios.push(form.value.bolsaFamilia.data !== null && form.value.bolsaFamilia.data !== '')
	}

	form.value.camposPreenchidos = camposObrigatorios.indexOf(false) == -1
	emits('campos-preenchidos', form.value)
}

const preencheCamposStorage = async () => {
	if (inscricao.certidaoTipo.value !== null) {
		form.value.bolsaFamilia.data = inscricao.bolsaFamilia.value
		form.value.certidaoTipo.data = inscricao.certidaoTipo.value
		form.value.certidaoModelo.data = inscricao.certidaoModelo.value
		form.value.certidaoNumero.data =  inscricao.certidaoNumero.value
		optionsEstado.value = await censo.getEstados()
		form.value.livro.data =  inscricao.livro.value
		form.value.folha.data =  inscricao.folha.value
		form.value.termo.data = inscricao.termo.value
		form.value.dataCertidao.data =  inscricao.dataCertidao.value
		form.value.nomeCartorio.data =  inscricao.nomeCartorio.value
		form.value.estadoCertidao.data = inscricao.estadoCertidao.value
		await buscaMunicipios()
		form.value.municipioCertidao.data = inscricao.municipioCertidao.value
		let identidade = false
		if (inscricao.identidade !== undefined) {
			if (
				inscricao.identidade.value !== null && 
				inscricao.identidade.value !== '000000000' && 
				inscricao.identidade.value !== ''
			) {
				identidade = true;
			}
		}
		naoIdentidade.value = identidade === false
		form.value.cartaoSus.data = inscricao.cartaoSus.value
		if (!naoIdentidade.value) {
			form.value.identidade.data = inscricao.identidade.value
			let orgaoIdentidadeSelecionada = []
			const orgaoIdentidadeLocalStorage = inscricao.orgaoIdentidade.value
			orgaoIdentidadeSelecionada = optionsOrgao.value.filter((f) => f.label == orgaoIdentidadeLocalStorage.label)
			form.value.orgaoIdentidade.data = null
			if (orgaoIdentidadeSelecionada.length > 0) {
				form.value.orgaoIdentidade.data = orgaoIdentidadeSelecionada[0]
			}
		} else {
			form.value.identidade.disabled = naoIdentidade.value
			form.value.orgaoIdentidade.disabled = naoIdentidade.value
		}
		validaCampos()
	}
}


onMounted( async () => {
	try {
		loading.value = true
		campos.value = (await window.axios.get('v4/api/educacao/files/campos-opcionais')).data
		optionsOrgao.value = (await window.axios.get('v4/api/educacao/files/orgaos')).data
		form.value.bolsaFamilia.show = campos.value.bolsa_familia.apresenta
		form.value.bolsaFamilia.required = [val => campos.value.bolsa_familia.obrigatorio ? !!val : true || 'Campo Obrigatório']
		form.value.cartaoSus.show = campos.value.cartao_sus.apresenta
		await preencheCamposStorage()
		validaCampos()
		loading.value = false
	} catch(e) {
		console.log('Ocorreu um erro ao carregar dados: '+e.message)
		loading.value = false
	}
})
</script>
<template>
    <div class="border-2 border-dashed surface-border border-round surface-ground flex-auto flex justify-content-center align-items-center font-medium">
        <div class="p-fluid grid mt-3" style="max-width: 450px;">
			<div class="field col-12 md:col-12" style="max-width: 450px;" v-show="nacionalidade != 3">
				<span class="p-float-label">
					<Dropdown v-show="nacionalidade != 3" v-model="form.certidaoTipo.data"
					:options="optionsTipo" optionLabel="label" @update:model-value="validaCampos" />
					<label for="" v-show="nacionalidade != 3">{{ form.certidaoTipo.label }}</label>
				</span>
			</div>

			<div class="field col-12 md:col-12" v-show="nacionalidade != 3">
				<span class="p-float-label">
					<Dropdown v-show="nacionalidade != 3" v-model="form.certidaoModelo.data"
					:options="optionsModelo" optionLabel="label" @update:model-value="validaModeloCertidao" />
					<label for="" v-show="nacionalidade != 3">{{ form.certidaoModelo.label }}</label>
				</span>
			</div>

			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1">
				<span class="p-float-label">
					<InputText type="text" v-show="nacionalidade != 3" 
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1" 
					v-model="form.livro.data" v-mask="'########'"
					optionLabel="label" 
					@update:model-value="validaCampos" />
					<label for="" v-show="nacionalidade != 3">{{ form.livro.label }}</label>
				</span>
			</div>
			
			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1">
				<span class="p-float-label">
					<InputText type="text" v-show="nacionalidade != 3" v-model="form.folha.data"
					optionLabel="label" v-mask="'####'"
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1"
					@update:model-value="validaCampos" />
					<label for="">{{ form.folha.label }}</label>
				</span>
			</div>
			
			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1">
				<span class="p-float-label">
					<InputText type="text" v-show="nacionalidade != 3" v-model="form.termo.data"
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1"
					optionLabel="label" v-mask="'########'" 
					@update:model-value="validaCampos" />
					<label for="">{{ form.termo.label }}</label>
				</span>
			</div>
			
			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1">
				<span class="p-float-label">
					<InputText v-show="nacionalidade != 3" type="text" 
					v-model="form.dataCertidao.data" 
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1"
					v-mask="'##/##/####'" 
					:class="form.dataCertidao.required ? 'obrigatorio' : ''" />
					<label for="">{{ form.dataCertidao.label }}</label>
				</span>
			</div>
			
			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1" >
				<span class="p-float-label">
					<Dropdown v-show="nacionalidade != 3" 
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1" 
					v-model="form.estadoCertidao.data"
					:options="optionsEstado" optionLabel="label" @update:model-value="buscaMunicipios" />
					<label for="">{{ form.estadoCertidao.label }}</label>
				</span>
			</div>
			
			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1">
				<span class="p-float-label">
					<Dropdown v-show="nacionalidade != 3" 
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1" 
					v-model="form.municipioCertidao.data"
					:options="optionsMunicipio" optionLabel="label" @update:model-value="validaCampos" />
					<label for="">{{ form.municipioCertidao.label }}</label>
				</span>
			</div>
			
			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1">
				<span class="p-float-label">
					<InputText v-show="nacionalidade != 3" type="text" 
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 1"
					v-model="form.nomeCartorio.data" 
					:class="form.nomeCartorio.required ? 'obrigatorio' : ''" 
					@update:model-value="limitaCampo(70, form.nomeCartorio)" />
					<label for="">{{ form.nomeCartorio.label }}</label>
				</span>
			</div>
			
			<div class="field col-12 md:col-12" v-show="nacionalidade != 3" v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 2">
				<span class="p-float-label">
					<InputText v-show="nacionalidade != 3" type="text" 
					v-if="form.certidaoModelo.data !== null && form.certidaoModelo.data.value == 2"
					v-model="form.certidaoNumero.data" 
					v-mask="'######  ##  ##  ####  #  #####  ###  #######  ##'"
					:class="form.certidaoNumero.required ? 'obrigatorio' : ''" 
					@update:model-value="validaCampos" />
					<label for="">{{ form.certidaoNumero.label }}</label>
				</span>
			</div>

			<div class="field col-12 md:col-12" v-show="nacionalidade != 3">
                <InputSwitch v-show="nacionalidade != 3" 
				v-model="naoIdentidade" aria-labelledby="possuiIdentidade" 
				@update:model-value="bloqueiaCampoIdentidade" />
                <span class="ml-2" id="possuiIdentidade=" v-show="nacionalidade != 3">Identidade não informada</span>
            </div>

            <div class="field col-12 md:col-12" v-show="nacionalidade != 3">
                <span class="p-float-label">
                    <InputText v-show="nacionalidade != 3" type="text" :disabled="form.identidade.disabled"
					v-model="form.identidade.data" :placeholder="form.identidade.label"
					@update:model-value="limitaCampo(20, form.identidade)" />
                    <label for="" v-show="nacionalidade != 3">{{ form.identidade.label }}</label>
                </span>
            </div>			

            <div class="field col-12 md:col-12" v-show="nacionalidade != 3">
                <span class="p-float-label">
                    <Dropdown v-show="nacionalidade != 3" v-model="form.orgaoIdentidade.data" :disabled="form.orgaoIdentidade.disabled"
                    :options="optionsOrgao" optionLabel="label" @update:model-value="validaCampos" />
                    <label for="" v-show="nacionalidade != 3">{{ form.orgaoIdentidade.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="form.cartaoSus.show">
                <span class="p-float-label">
                    <InputText v-show="form.cartaoSus.show" type="text" 
					v-model="form.cartaoSus.data" :placeholder="form.cartaoSus.label"
					v-mask="'###  ####  ####  ####'" @update:model-value="validaCampos" />
                    <label for="">{{ form.cartaoSus.label }}</label>
                </span>
            </div>
            
            <div class="field col-12 md:col-12" v-show="form.bolsaFamilia.show">
                <span class="p-float-label">
                    <InputText v-show="form.bolsaFamilia.show" type="text" v-model="form.bolsaFamilia.data" 
					:placeholder="form.bolsaFamilia.label" 
					v-mask="'###########'" @update:model-value="validaCampos" />
                    <label for="">{{ form.bolsaFamilia.label }}</label>
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