<script setup>
import { ref, onMounted } from "vue"
import { ProcessoInscricaoService } from "../Services/ProcessoInscricaoService.js";

const props = defineProps(['index'])
const emits = defineEmits(['seleciona-escola', 'limpar'])
const inscricaoService = new ProcessoInscricaoService()
const escolasSelecionadas = ref([])

if (inscricaoService.getCampoFormularioStorage('opcoesEscola') !== null) {
	escolasSelecionadas.value = inscricaoService.getCampoFormularioStorage('opcoesEscola').map(escola => {
		return `${escola.escola.codigo}-${escola.escola.turno.codigo}`
	})
}

const columns = ref([
	{ name: 'escola', field: 'escola', align: 'left' }
])
const columns1 = ref([
	{ name: 'escola', field: 'escola', label: 'Escola', align: 'left' },
	{ name: 'bairro', field: 'bairro', label: 'Bairro', align: 'left' },
	{ name: 'turnos', field: 'turnos', label: 'Turnos', align: 'left', style: 'width: 40%' }
])

const labels = ref([
	'Primeira Opção', 'Segunda Opção', 'Terceira Opção', 'Quarta Opção', 'Quinta Opção'
])

const visible = ref(false);
const form = ref({
	escola: null,
	escolasSelecionadas: null,
})
const dados = ref([])
const bairroAluno = inscricaoService.getCampoFormularioStorage('bairro').value
const escolas = inscricaoService.getStorageEscolasDisponiveis()
let escolasBairro = escolas.filter(escola => escola.bairrosAtendidos.indexOf(bairroAluno) !== -1)
let demaisEscolas = escolas.filter(escola => escola.bairrosAtendidos.indexOf(bairroAluno) == -1)
escolasBairro.sort(function (a, b) {
	if (a.bairro > b.bairro) {
		return 1;
	}
	if (a.bairro < b.bairro) {
		return -1;
	}
	return 0;
});
demaisEscolas.sort(function (a, b) {
	if (a.bairro > b.bairro) {
		return 1;
	}
	if (a.bairro < b.bairro) {
		return -1;
	}
	return 0;
});
escolasBairro.forEach(escola => {
	dados.value.push(escola)
})
demaisEscolas.forEach(escola => {
	dados.value.push(escola)
})

const selecionaEscola = () => {
	let hash = form.value.escolasSelecionadas.split('-')
	let escola = dados.value.filter(escola => escola.escola.codigo === parseInt(hash[0])).map(escola => {
		let school = {
			nome: escola.escola.nome,
			bairro: escola.bairro,
			codigo: escola.escola.codigo,
			turno: {...escola.turnos.filter(turno => turno.codigo === hash[1])[0]},
			bairrosAtendidos: [...escola.bairrosAtendidos],
			index: props.index,
			escolaLonge: false
		}

		return school
	})
	form.value.escola = escola[0].nome
	visible.value = false

	emits('seleciona-escola', escola[0])
}

const preencheCamposStorage = async () => {
	if (inscricaoService.getCampoFormularioStorage('opcoesEscola') !== null && inscricaoService.getCampoFormularioStorage('opcoesEscola') !== 'null') {
		let op = inscricaoService.getCampoFormularioStorage('opcoesEscola')
		op.map((opcao, key) => {
			if (props.index == opcao.escola.index) {
				form.value.escola = opcao.escola.nome
				form.value.escolasSelecionadas = `${opcao.escola.codigo}-${opcao.escola.turno.codigo}`
			}
		})
	} else {
		form.value.escolasSelecionadas = null
	}
}
const abreModal = () => {
	preencheCamposStorage()
	escolasSelecionadas.value = []
	if (inscricaoService.getCampoFormularioStorage('opcoesEscola') !== null) {
		escolasSelecionadas.value = inscricaoService.getCampoFormularioStorage('opcoesEscola').map(escola => {
			return `${escola.escola.codigo}-${escola.escola.turno.codigo}`
		})
	}
	visible.value = true
}

const limpar = () => {
	form.value.escola = null
	form.value.escolasSelecionadas = null
	emits('limpar', props.index)
}
onMounted(async () => {
	preencheCamposStorage()
})

</script>
<template>
    <div class="field col-10 md:col-10">
		<span class="p-float-label">
			<InputText :disabled="true" type="text" v-model="form.escola" />
			<label for="">{{ labels[props.index - 1] }}</label>
		</span>
	</div>

	<div class="field col-2 md:col-2">
		<Button style="width: fit-content;" label="Buscar" @click="abreModal" />
		<Button style="width: fit-content;" class="ml-1" label="Limpar" @click="limpar" />
	</div>

	<Dialog v-model:visible="visible" modal header="Escolas Disponíveis" style="width: 1000px; max-width: 80vw;">
		<div class="card">
			<DataTable :value="dados" tableStyle="min-width: 50rem" class="mt-3">
				<Column field="escola.nome" header="Escola"></Column>
				<Column field="bairro" header="Bairro"></Column>
				<Column field="" header="Turnos">
					<template #body="slotProps">
						<div class="flex flex-wrap gap-3">
							<div class="flex align-items-center" v-for="turno in slotProps.data.turnos" :key="turno.codigo">
								<RadioButton v-model="form.escolasSelecionadas" 
								:disabled="escolasSelecionadas.indexOf(`${slotProps.data.escola.codigo}-${turno.codigo}`) !== -1"
								:inputId="`turno${turno.codigo}`" :value="`${slotProps.data.escola.codigo}-${turno.codigo}`" />
								<label :for="`turno${turno.codigo}`" class="ml-2">{{ turno.descricao }}</label>
							</div>
						</div>
					</template>
				</Column>
			</DataTable>
		</div>

		<div class="flex justify-content-between gap-2 mt-4">
			<Button type="button" label="Cancelar" severity="secondary" @click="visible = false"></Button>
			<Button type="button" label="Salvar" 
			:disable="form.escolasSelecionadas === null" v-close-popup @click="selecionaEscola"></Button>
		</div>
	</Dialog>
</template>
<style scoped>
    .p-inputtext{ background-color: #fff; border: 1px solid rgba(0, 0, 0, 0.12);}
    .p-dropdown{ border: 1px solid rgba(0, 0, 0, 0.12);}
    .border-2,.border-dashed {padding: 10px 5px !important;}
</style>
