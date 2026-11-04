<script setup>
import { ref, onMounted } from "vue"
import ModalLoading from "../../../Components/ModalLoading.vue";
import { useToast } from 'primevue/usetoast';
import axios from "axios";

import { Utils } from "../Utils/Utils.js";
import { ProcessoInscricaoService } from "../Services/ProcessoInscricaoService.js";
import { Inscricao } from "../Models/Inscricao.js";

const emits = defineEmits(['campos-preenchidos'])

const toast = useToast()

const inscricaoService = new ProcessoInscricaoService()
const inscricao = new Inscricao()

const loading = ref(false)
const optionsBairro = ref()
const optionsZonaResidencia = ref()

const form = ref({
	cep: {
		label: 'CEP',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	logradouro: {
		label: 'Logradouro (Endereço)',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	numero: {
		label: 'Número',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	complemento: {
		label: 'Complemento',
		data: null,
		required: [val => true || 'Campo Obrigatório']
	},
	bairro: {
		label: 'Bairro',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	},
	zonaResidencia: {
		label: 'Zona de Residência',
		data: null,
		required: [val => !!val || 'Campo Obrigatório']
	}
})

const buscaCep = () => {
	let cep = form.value.cep.data
	if (Utils.validaTamanhoCampoCEP(cep)) {
		axios.get(`https://viacep.com.br/ws/${cep}/json/`).then(response => {
			if (response.status === 200) {
				if (response.data.logradouro !== '') {
					form.value.logradouro.data = response.data.logradouro.toUpperCase()
					validaCampos()
				}
			}
		})
	}
	validaCampos()
}
const validaCampos = () => {
	let camposObrigatorios = [
		form.value.cep.data !== null && form.value.cep.data !== '',
		form.value.logradouro.data !== null && form.value.logradouro.data !== '' ,
		form.value.numero.data !== null && form.value.numero.data !== '',
		form.value.bairro.data !== null && form.value.bairro.data !== '',
		form.value.zonaResidencia.data !== null && form.value.zonaResidencia.data !== '',
	]
	form.value.camposPreenchidos = camposObrigatorios.indexOf(false) == -1
	emits('campos-preenchidos', form.value)
}

const preencheCamposStorage = async () => {
	if (inscricao.cep.value !== null) {
		form.value.cep.data = inscricao.cep.value
		form.value.numero.data = inscricao.numero.value
		form.value.complemento.data = inscricao.complemento.value
		form.value.logradouro.data = inscricao.logradouro.value
		const bairroStorage = inscricao.bairro.value
		let opcaoSelecionada = null;
		optionsBairro.value.map( (bairro) => {
			if (bairro.value == bairroStorage.value) {
				opcaoSelecionada = {label: bairro.label, value: bairro.value, uf: bairro.uf, munic: bairro.munic}
			}
		})
		form.value.bairro.data = opcaoSelecionada
		form.value.zonaResidencia.data = inscricao.zonaResidencia.value
	}
}

const limitaCampo = (length, value) => {
    value.data = value.data.slice(0, length)
    validaCampos()
}

onMounted( async () => {
	try {
		loading.value = true
		await inscricaoService.buildBairros()
		optionsBairro.value = inscricaoService.getStorageBairros()
		optionsZonaResidencia.value = (await window.axios.get('v4/api/educacao/files/zonas')).data
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
            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.cep.data" 
                    :placeholder="form.cep.label" v-mask="'#####-###'"
                    @update:model-value="buscaCep" />
                    <label for="">{{ form.cep.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.logradouro.data" 
                    :placeholder="form.logradouro.label"
                    @update:model-value="limitaCampo(100, form.logradouro)" />
                    <label for="">{{ form.logradouro.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.numero.data" 
                    :placeholder="form.numero.label" v-mask="'#####'"
                    @update:model-value="validaCampos" />
                    <label for="">{{ form.numero.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.complemento.data" 
                    :placeholder="form.complemento.label"
                    @update:model-value="val => limitaCampo(20, form.complemento)" />
                    <label for="">{{ form.complemento.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <Dropdown v-model="form.bairro.data"
                    :options="optionsBairro" optionLabel="label"
                    @update:model-value="validaCampos" />
                    <label for="">{{ form.bairro.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <Dropdown v-model="form.zonaResidencia.data"
                    :options="optionsZonaResidencia" optionLabel="label" 
                    @update:model-value="validaCampos" />
                    <label for="">{{ form.zonaResidencia.label }}</label>
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