<script setup>
import { ref, onMounted } from "vue"
import ModalLoading from "../../../Components/ModalLoading.vue";
import { useToast } from 'primevue/usetoast';

import { ProcessoInscricaoService } from "../Services/ProcessoInscricaoService.js";
import { CensoLocalidadesService } from "../Services/CensoLocalidadesService.js";
import { Inscricao } from "../Models/Inscricao.js";

const toast = useToast()

const emits = defineEmits(['campos-preenchidos'])
const campos = ref()
const loading = ref(false)

const inscricaoService = new ProcessoInscricaoService()
const censo = new CensoLocalidadesService()
const inscricao = new Inscricao()

const temNecessidadeEspecial = ref(false)
const nacionalidade = ref(inscricao.nacionalidade.value.value)
const fase = ref(inscricao.fase)
const mensagensValidacao = ref([])

const optionsSexo = ref([
	{label: 'FEMININO', value: 'F'},
	{label: 'MASCULINO', value: 'M'}
])
const optionsEstadoCivil =  ref([
	{label: 'SOLTEIRO', value: 1},
	{label: 'CASADO', value: 2},
	{label: 'VIÚVO', value: 3},
	{label: 'DIVORCIADO', value: 4}
])

const DEFICIENCIA_FISICA = 106;

const optionsCor =  ref([])
const optionsNecessidades = ref()
const optionsEstados = ref()
const optionsMunicipios = ref()
const laudoNecessidadeEspecial = ref(null)
const optionsPaises = ref()

const form = ref({
	sexo: {
		label: 'Sexo',
		data: null,
		required: true
	},
	estadoCivil: {
		label: 'Estado Civil',
		data: optionsEstadoCivil.value[0]
	},
	cor: {
		label: 'Cor/Raça',
		data: null,
	},
	estadoNascimento: {
		label: 'UF de Nascimento',
		data: null,
		required: true
	},
	municipioNascimento: {
		label: 'Município de Nascimento',
		data: null,
		required: true
	},
	paisNascimento: {
		label: 'País de Nascimento',
			data: null,
			required: false
	},
	irmaoGemeo: {
		label: 'Candidato Tem Irmão Gêmeo',
		data: false,
		required: false
	},
	necessidadesEspeciais: {
		label: 'Necessidades Especiais?',
		data: [],
		required: false
	},
	cadeirante: {
		label: 'Cadeirante',
		data: false
	},
	laudoNecessidadeEspecial: {
		label: 'Clique para adicionar/remover o laudo comprovante da necessidade especial (imagens JPG/PNG de até 1MB)'
	}
})

const buscaMunicipios = async () => {
	try {
        loading.value = true
		optionsMunicipios.value = await censo.getMunicipios(form.value.estadoNascimento.data.value)
		loading.value = false
		validaCampos()
	} 
	catch(e) {
		loading.value = false
	}
}

const delay = ms => new Promise(res => setTimeout(res, ms));

const buscaCorRaca = async () => {
	const cores = await inscricaoService.getCorRaca()
	optionsCor.value = cores
}

const validaCampos = () => {
	mensagensValidacao.value = []
	let camposObrigatorios = [
		form.value.sexo.data !== null && form.value.sexo.data !== ''
	]

	if (nacionalidade.value !== 3 && nacionalidade.value !== 2) {
		form.value.paisNascimento.required = [val => true || 'Campo Obrigatório']
		camposObrigatorios.push(form.value.municipioNascimento.data !== null && form.value.municipioNascimento.data !== '')
		camposObrigatorios.push(form.value.estadoNascimento.data !== null && form.value.estadoNascimento.data !== '')
	} else {
		form.value.estadoNascimento.required = [val => true || 'Campo Obrigatório']
		form.value.municipioNascimento.required = [val => true || 'Campo Obrigatório']
        if (nacionalidade.value === 3) {
            camposObrigatorios.push(form.value.paisNascimento.data !== null && form.value.paisNascimento.data !== '')
        }
	}

	if (campos.value.cor_raca.obrigatorio) {
		camposObrigatorios.push(form.value.cor.data !== null && form.value.cor.data !== '')
	}

	if (campos.value.estado_civil.obrigatorio) {
		camposObrigatorios.push(form.value.estadoCivil.data !== null && form.value.estadoCivil.data !== '')
	}

	if (fase.value.publicosAlvo.indexOf(1) !== -1) {
		camposObrigatorios.push(form.value.necessidadesEspeciais.data !== null && form.value.necessidadesEspeciais.data.length > 0)
		let boo = form.value.necessidadesEspeciais.data !== null && form.value.necessidadesEspeciais.data.length > 0
		form.value.necessidadesEspeciais.required = [val => boo || 'Pelo menos uma necessidade deve ser informada']
	}

	temNecessidadeEspecial.value = false;
	if(form.value.necessidadesEspeciais.data != null && form.value.necessidadesEspeciais.data.length > 0){
		temNecessidadeEspecial.value = true;
	}

	if (form.value.necessidadesEspeciais.data.length > 0 && (laudoNecessidadeEspecial.value == null || laudoNecessidadeEspecial.value == '')) { 
		camposObrigatorios.push(false)
		mensagensValidacao.value.push('É preciso anexar o laudo de necessidade especial') 
	}

	form.value.camposPreenchidos = camposObrigatorios.indexOf(false) == -1
	emits('campos-preenchidos', form.value)
}

const preencheCamposStorage = async () => {
	if (inscricao.sexo.value !== null) {
		form.value.sexo.data = inscricao.sexo.value
		form.value.cadeirante.data = inscricao.cadeirante.value
		form.value.estadoCivil.data = inscricao.estadoCivil.value
		let opcaoSelecionada = optionsCor.value.filter((f) => f.padrao == true)
		await delay(100)
		const corStorage = inscricao.cor.value
		if (corStorage.value != null) 
		{
			opcaoSelecionada = optionsCor.value.filter((f) => f.value == corStorage.value)
		}
		form.value.cor.data = opcaoSelecionada[0]
		form.value.necessidadesEspeciais.data = inscricaoService.getCampoFormularioStorage('necessidadesEspeciais')
		validaCampos()
		let irmaoGemeo = false
		if (inscricao.irmaoGemeo !== undefined) {
			if (inscricao.irmaoGemeo.value !== null) {
				irmaoGemeo = inscricao.irmaoGemeo.value
			}
		}
		form.value.irmaoGemeo.data = irmaoGemeo
		form.value.estadoNascimento.data = inscricao.estadoNascimento.value
		if (form.value.estadoNascimento.data !== null) {
			optionsMunicipios.value = await censo.getMunicipios(form.value.estadoNascimento.data.value)
		}
		form.value.municipioNascimento.data = inscricao.municipioNascimento.value
		form.value.paisNascimento.data = inscricao.paisNascimento.value

		let fileAtestado = await inscricaoService.getFileFromCampo('atestadoNecessidadeEspecial');
		if(fileAtestado instanceof File) {
			form.value.laudoNecessidadeEspecial.label = fileAtestado.name;
			laudoNecessidadeEspecial.value = fileAtestado.name
		}
	}
}

const onFileRemoved = async () => {
	await inscricaoService.salvarCampoFile('atestadoNecessidadeEspecial','null')
	form.value.laudoNecessidadeEspecial.label = 'Clique para adicionar/remover o laudo comprovante da necessidade especial (imagens JPG/PNG de até 1MB)'
	laudoNecessidadeEspecial.value = null
	validaCampos()
	return
};

const onFileAdded = async (file) => {
	await inscricaoService.salvarCampoFile('atestadoNecessidadeEspecial',file.files[0]);
	laudoNecessidadeEspecial.value = file.files[0].name
	validaCampos()
};

onMounted( async () => {
	try {
		loading.value = true
		campos.value = (await window.axios.get('v4/api/educacao/files/campos-opcionais')).data
		form.value.estadoCivil.required = [val => campos.value.estado_civil.obrigatorio ? !!val : true || 'Campo Obrigatório']
		form.value.estadoCivil.show = campos.value.estado_civil.apresenta
		
		form.value.cor.required = [val =>campos.value.cor_raca.obrigatorio ? !!val : true || 'Campo Obrigatório']
		form.value.cor.show = campos.value.cor_raca.apresenta


		await inscricaoService.buildNecessidades()
		optionsNecessidades.value = inscricaoService.getStorageNecessidades()	
		if (fase.value.publicosAlvo.indexOf(1) !== -1) {
			form.value.necessidadesEspeciais.label = 'Necessidades Especiais? Selecione pelo menos uma opção!'
			let boo = form.value.necessidadesEspeciais.data.length > 0
			form.value.necessidadesEspeciais.required = [val => boo || 'Pelo menos uma necessidade deve ser informada']
		}	

		if (nacionalidade.value === 3) {
			optionsPaises.value = await censo.getPaises()
		} else {
			optionsEstados.value = await censo.getEstados()
		}
		await buscaCorRaca()
		await preencheCamposStorage()
		validaCampos()
		loading.value = false
	} catch (e) {
		loading.value = false
		console.log('Ocorreu um erro ao carregar dados: '+e.message)
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
                <span class="p-float-label">
                    <Dropdown v-model="form.sexo.data"
                    :options="optionsSexo" optionLabel="label"
					:class="form.sexo.required ? 'obrigatorio' : ''"
					@update:model-value="validaCampos" />
                    <label for="">{{ form.sexo.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="form.estadoCivil.show">
                <span class="p-float-label">
                    <Dropdown v-show="form.estadoCivil.show" 
					v-model="form.estadoCivil.data"
					@update:model-value="validaCampos"
                    :options="optionsEstadoCivil" optionLabel="label" />
                    <label for="">{{ form.estadoCivil.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="form.cor.show">
                <span class="p-float-label">
                    <Dropdown v-show="form.cor.show" v-model="form.cor.data"
                    :options="optionsCor" optionLabel="label"
					@update:model-value="validaCampos" />
                    <label for="">{{ form.cor.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-if="nacionalidade !== 3 && nacionalidade !== 2">
                <span class="p-float-label">
                    <Dropdown v-if="nacionalidade !== 3 && nacionalidade !== 2" 
					v-model="form.estadoNascimento.data"
                    :options="optionsEstados" optionLabel="label"
					:class="form.estadoNascimento.required ? 'obrigatorio' : ''"
					@update:model-value="buscaMunicipios" />
                    <label for="" v-if="nacionalidade !== 3 && nacionalidade !== 2">{{ form.estadoNascimento.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-if="nacionalidade !== 3 && nacionalidade !== 2">
                <span class="p-float-label">
                    <Dropdown v-if="nacionalidade !== 3 && nacionalidade !== 2" 
					v-model="form.municipioNascimento.data"
                    :options="optionsMunicipios" optionLabel="label"
					:class="form.municipioNascimento.required ? 'obrigatorio' : ''"
					@update:model-value="validaCampos" />
                    <label for="" v-if="nacionalidade !== 3 && nacionalidade !== 2">{{ form.municipioNascimento.label }}</label>
                </span>
            </div>

			<div class="field col-12 md:col-12" v-if="nacionalidade === 3">
                <span class="p-float-label">
                    <Dropdown v-if="nacionalidade === 3" v-model="form.paisNascimento.data"
                    :options="optionsPaises" optionLabel="label" 
					@update:model-value="validaCampos" />
                    <label for="">{{ form.paisNascimento.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <InputSwitch v-model="form.irmaoGemeo.data" 
				aria-labelledby="irmaoGemeo"
				@update:model-value="validaCampos" />
                <span class="ml-2" id="irmaoGemeo">{{ form.irmaoGemeo.label }}</span>
            </div>

			<div class="field col-12 md:col-12 mb-3" v-show="form.necessidadesEspeciais.data.length > 0">
				<FileUpload :auto="false"
				mode="basic" name="laudo" v-model="laudoNecessidadeEspecial"
				accept="image/jpg, image/jpeg, image/png" :maxFileSize="1000000" 
				:chooseLabel="form.laudoNecessidadeEspecial.label"
				invalidFileTypeMessage="{0}: Tipo de arquivo inválido. Apenas arquivos JPG/PNG de até 1MB são permitidos."
				invalidFileSizeMessage="Tamanho de arquivo inválido. O arquivo deve ser de até 1MB"
				@select="onFileAdded" @clear="onFileRemoved" :fileLimit=1
				/>
            </div>
			
			<div class="field col-12 md:col-12 mt-2">
                <span class="p-float-label">
                    <MultiSelect v-model="form.necessidadesEspeciais.data" 
					display="chip" :options="optionsNecessidades" 
					optionLabel="label" 
    				class="w-full" @update:model-value="validaCampos" />
                    <label for="">{{ form.necessidadesEspeciais.label }}</label>
                </span>
            </div>
			
			<Divider />

			<div class="field col-12 md:col-12" v-for="(opcao, key)  in form.necessidadesEspeciais.data" :key="key">
				<InputSwitch v-if="opcao.value == DEFICIENCIA_FISICA"
				aria-labelledby="cadeirante"
				:label="form.cadeirante.label"
				v-model="form.cadeirante.data"
				@update:model-value="validaCampos" />
                <span v-if="opcao.value == DEFICIENCIA_FISICA" class="ml-2" id="cadeirante">{{ form.cadeirante.label }}</span>

                <Card v-if="opcao.subdivisoes.length > 0">
					<template #title><h4>{{ opcao.label }}</h4></template>
					<template #content>
						<div class="card flex flex-wrap justify-content-start gap-3 mb-2">Subdivisões:</div>
						<div class="card flex flex-wrap justify-content-center gap-3">
							<div class="flex align-items-center" v-for="(sub, chave) in opcao.subdivisoes">
								<Checkbox v-model="opcao.subdivisoes[chave].selecionada" 
								 :binary="true" inputId="`sub${chave}`" @update:model-value="validaCampos" />
								<label :for="`sub${chave}`" class="ml-2"> {{ sub.label }} </label>
							</div>
						</div>
					</template>
				</Card>
            </div>
        </div>
    </div>
	<ModalLoading :isLoading="loading"/>
</template>
<style scoped>
    .p-inputtext{ background-color: #fff; border: 1px solid rgba(0, 0, 0, 0.12);}
    .p-dropdown{ border: 1px solid rgba(0, 0, 0, 0.12);}
	.border-2,.border-dashed {padding: 10px 5px !important;}
	.p-multiselect-close p-link {display: none !important;}
</style>