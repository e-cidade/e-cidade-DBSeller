<script setup>
import { ref, onMounted } from "vue"
import { useToast } from 'primevue/usetoast';
import ModalLoading from "../../../Components/ModalLoading.vue";
import {Utils} from "../Utils/Utils.js";

import { ProcessoInscricaoService } from "../Services/ProcessoInscricaoService.js";
import { Inscricao } from "../Models/Inscricao.js";
import SelecaoEscolas from "./SelecaoEscolas.vue";

const emits = defineEmits(['campos-preenchidos'])

const loading = ref(false)
const inscricaoService = new ProcessoInscricaoService()
const inscricao = new Inscricao()
const toast = useToast()

const mensagem = ref()
const fase = ref(inscricao.fase)
const form = ref({
	opcoesEscola: {
		data: []
	}
})
for (let i = 1; i <= fase.value.opcoesEscolha; i++) {
	form.value.opcoesEscola.data[i] = {
		escola: {
			nome: null,
			bairro: null,
			codigo: null,
			turno: {
				codigo: null,
				descricao: null
			},
			index: null,
			bairrosAtendidos: null,
			escolaLonge: false
		},
		temIrmao: false,
		nomeIrmao: null,
		cpfIrmao: null
	}
}

const validaCampos = () => {
	const opcoes = form.value.opcoesEscola.data.filter(opcao => opcao.escola.nome !== null)
	let irmaosPreenchidos = opcoes.map(op => {
		if (op.temIrmao) {
			if (op.nomeIrmao === '' || op.nomeIrmao === null) {
				return false
			}
			return true
		}
		return true
	})

	const escolasRestantes = verificaQtdOpcoesEscolaObrigatorias(form.value)

	form.value.camposPreenchidos = opcoes.length > 0 
		&& irmaosPreenchidos.indexOf(false) == -1
		&& escolasRestantes <= 0

	if (opcoes.length > 0 && escolasRestantes > 0) {
		inscricaoService.salvarFormularioStorage(form.value)
	}
	emits('campos-preenchidos', form.value)
}

const verificaQtdOpcoesEscolaObrigatorias = (dados) => {
	const fase = inscricao.fase;
    let count = 0;
    let escolas = dados.opcoesEscola.data;

    escolas.forEach(escola => {
        if (escola.escola.nome !== null) {
            count++;
        }
    });

    let escolasRestantes = fase.opcoesObrigatoria - count;
    return escolasRestantes
}

const preencheCamposStorage = async () => {
	if (inscricaoService.getCampoFormularioStorage('opcoesEscola') !== null && inscricaoService.getCampoFormularioStorage('opcoesEscola') !== 'null') {
		let op = inscricaoService.getCampoFormularioStorage('opcoesEscola')
		op.map((opcao, key) => {
			form.value.opcoesEscola.data[opcao.escola.index] = opcao
		})
	}
}
const validaBairro = (e) => {
	if(e != undefined) {
		let bairroAluno = inscricaoService.getCampoFormularioStorage('bairro').value
		if (e.bairrosAtendidos.indexOf(parseInt(bairroAluno)) == -1) {
			form.value.opcoesEscola.data[e.index].escola.escolaLonge = true
			let mensagem = "ATENÇÃO! - A opção escolhida está localizada em área distante do endereço residencial do pretendente à vaga."
			toast.add({ severity: 'error', summary: 'Atenção', detail: mensagem, life: 5000 });
		} else {
			form.value.opcoesEscola.data[e.index].escola.escolaLonge = false
		}
	}
	validaCampos()
}

const mensagemEscolasRestantes = (escolasRestantes) => {
	const mensagem = `Escolas obrigatórias restantes: ${escolasRestantes}!`
	toast.add({ severity: 'error', summary: 'Atenção', detail: mensagem, life: 5000 });
}

const selecionaEscola = (e) => {
	form.value.opcoesEscola.data[e.index].escola = e
	validaBairro(e)
	validaCampos()
	const escolasRestantes = verificaQtdOpcoesEscolaObrigatorias(form.value)
	if (escolasRestantes > 0) {
		mensagemEscolasRestantes(escolasRestantes)
	}
}

const limparEscola = (e) => {
	form.value.opcoesEscola.data[e] = {
		escola: {
			nome: null,
			bairro: null,
			codigo: null,
			turno: {
				codigo: null,
				descricao: null
			},
			index: null,
			bairrosAtendidos: null,
			escolaLonge: false
		},
		temIrmao: false,
		nomeIrmao: null
	}
	localStorage.removeItem('opcoesEscola')
	inscricaoService.salvarFormularioStorage(form)
	validaCampos()
	const escolasRestantes = verificaQtdOpcoesEscolaObrigatorias(form.value)
	if (escolasRestantes > 0) {
		mensagemEscolasRestantes(escolasRestantes)
	}
}

const limitaCampo = (index, length, value) => {
    value.data[index].nomeIrmao = value.data[index].nomeIrmao.slice(0, length)
    validaCampos()
}

const delay = ms => new Promise(res => setTimeout(res, ms));

const buscaDadosIrmao = async (n) => {
	await delay(10);

	let dados = form.value.opcoesEscola.data[n];
	if (dados.cpfIrmao !== null) {
		if (Utils.validaCPF(dados.cpfIrmao)) {
			let irmao = await inscricaoService.getAlunoEscolaByCpf(dados.escola.codigo, Utils.removeCaracteres(dados.cpfIrmao));
			if (irmao == null) {
				form.value.opcoesEscola.data[n].nomeIrmao = null;
				let mensagem = "ATENÇÃO! - Aluno não encontrado com o cpf informado."
				toast.add({ severity: 'error', summary: 'Erro', detail: mensagem, life: 5000 })
				form.value.opcoesEscola.data[n].temIrmao = false;
				form.value.opcoesEscola.data[n].cpfIrmao = null;
			} else {
				form.value.opcoesEscola.data[n].nomeIrmao = irmao.nome;
				validaCampos();
			}
		} else  {
			form.value.opcoesEscola.data[n].nomeIrmao = null;
			return false;
		}
	} else  {
		form.value.opcoesEscola.data[n].nomeIrmao = null;
		return false;
	}
}

onMounted( async () => {
	try {
		loading.value = true
		mensagem.value = (await window.axios.get('v4/api/educacao/files/mensagens')).data.escolha_escolas.conteudo
		await preencheCamposStorage()
		validaCampos()
		loading.value = false
		const escolasRestantes = verificaQtdOpcoesEscolaObrigatorias(form.value)
		if (escolasRestantes > 0) {
			mensagemEscolasRestantes(escolasRestantes)
		}
	} catch(e) {
		loading.value = false
		console.log('Ocorreu um erro ao carregar dados '+e.message)
	}
})
</script>
<template>
    <div class="border-2 border-dashed surface-border border-round surface-ground flex-auto flex justify-content-center align-items-center font-medium">
        <div class="p-fluid grid mt-3 w-full">
            <div class="field col-12 md:col-12">
                <div class="field col-12 md:col-12" style="font-size: 20px;">
                    <Message v-if="mensagem" severity="warn" :closable="false" :sticky="sticky">
						{{ mensagem }}
					</Message>
                </div>
            </div>

			<template v-for="n in fase.opcoesEscolha">
				<SelecaoEscolas @seleciona-escola="val => selecionaEscola(val)"  :index="n" @limpar="val => limparEscola(val)" />
				<div class="field col-12 md:col-12" v-if="form.opcoesEscola.data[n].escola.nome !== null">
					<InputSwitch v-if="form.opcoesEscola.data[n].escola.nome !== null" 
					v-model="form.opcoesEscola.data[n].temIrmao" 
					:aria-labelledby="`irmao${n}`" :binary="true"
					@update:model-value="validaCampos" />
					<span class="ml-2" :id="`irmao${n}`">{{ `Tem irmão na Escola ${form.opcoesEscola.data[n].escola.nome} ?` }}</span>
				</div>
				<div class="field col-12 md:col-12" v-if="form.opcoesEscola.data[n].temIrmao">
					<InputText type="text" :disabled="true"
					v-if="form.opcoesEscola.data[n].temIrmao"
					v-model="form.opcoesEscola.data[n].nomeIrmao"
					placeholder="Nome do Irmão"
					@update:model-value="limitaCampo(n, 70, form.opcoesEscola)" />
				</div>

				<div class="field col-12 md:col-12" v-if="form.opcoesEscola.data[n].temIrmao">
					<InputText type="text" v-mask="'###.###.###-##'"
					v-if="form.opcoesEscola.data[n].temIrmao"
					v-model="form.opcoesEscola.data[n].cpfIrmao"
					placeholder="CPF do Irmão"
					@update:model-value="buscaDadosIrmao(n)" />
				</div>
			</template>	
    	</div>
    </div>
	<ModalLoading :isLoading="loading"/>
</template>
<style scoped>
    .p-inputtext{ background-color: #fff; border: 1px solid rgba(0, 0, 0, 0.12);}
    .p-dropdown{ border: 1px solid rgba(0, 0, 0, 0.12);}
    .border-2,.border-dashed {padding: 10px 5px !important;}
</style>