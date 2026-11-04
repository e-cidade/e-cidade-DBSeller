<script setup>
import { ref, onMounted } from "vue"
import ModalLoading from "../../../Components/ModalLoading.vue";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from "primevue/confirmdialog";

import { ProcessoInscricaoService } from "../Services/ProcessoInscricaoService.js"
import { Inscricao } from "../Models/Inscricao.js";
import { InscricaoService } from "../Services/InscricaoService.js";
import { Utils } from "../Utils/Utils.js";

const confirm = useConfirm();
const mensagens = ref();
const emits = defineEmits(['campos-preenchidos', 'limpar-formulario'])
const campos = ref()
const toast = useToast()
const loading = ref(false)
const mensagensValidacao = ref([])

const inscricao = new Inscricao()
const inscricaoService = new ProcessoInscricaoService()
const consulta = new InscricaoService()

const optionsNacionalidade = ref([
	{label: 'BRASILEIRA', value: 1},
	{label: 'BRAS. NASC. EXTERIOR', value: 2},
	{label: 'ESTRANGEIRO', value: 3}
])

const optionsRedesOrigem = ref()
const optionsEscolaOrigem = ref()
const optionsEtapas = ref(null)
const exibeEscolaOrigem = ref(false)
const fase = ref(null)

const form = ref({
	dataNascimento: {
		label: 'Data de Nascimento',
		data: null,
		required: true
	},
	nacionalidade: {
		label: 'Nacionalidade',
		data: null,
		required: true
	},
	cpf: {
		label: 'CPF',
		data: null,
		required: true
	},
	visto: {
		label: 'Visto',
		data: null,
		required: [val => true || 'Campo Obrigatório']
	},
	rne: {
		label: 'Registro Nacional de Estrangeiros (RNE/RNM)',
		data: null,
		required: [val => true || 'Campo Obrigatório']
	},
	nome: {
		label: 'Nome do Candidato',
		data: null,
		required: true
	},
	nomeSocial: {
		label: 'Nome Social',
		data: null,
        required: false

	},
	etapaEnsino: {
		label: 'Ano Ensino/Série',
		data: null,
		required: true
	},
	redeOrigem: {
		label: 'Rede de Origem',
		data: null,
		required: true
	},
	escolaOrigem: {
		label: 'Escola de Origem',
		data: null,
		required: [val => true || 'Campo Obrigatório']
	}
})

const validaCampos = () => {
    mensagensValidacao.value = []
    
	let camposObrigatorios = [
		form.value.dataNascimento.data !== null && form.value.dataNascimento.data !== '',
		form.value.nacionalidade.data !== null && form.value.nacionalidade.data !== '' ,
		form.value.nome.data !== null && form.value.nome.data !== '' ,
		form.value.etapaEnsino.data !== null && form.value.etapaEnsino.data !== '',
		form.value.redeOrigem.data !== null && form.value.redeOrigem.data !== ''
	]

	if (form.value.nacionalidade.data !== null && form.value.nacionalidade.data.value != 3) {
		form.value.visto.data = null
		form.value.rne.data = null
	}
    if (form.value.redeOrigem.data !== null && form.value.redeOrigem.data.value === 9) {
        if (fase.value.exibeEscolaOrigem) {
            camposObrigatorios.push(form.value.escolaOrigem.data !== null && form.value.escolaOrigem.data !== '')
        }
    }
	if (fase.value !== null && fase.value.publicosAlvo.indexOf(3) == -1) {
		if (form.value.nacionalidade.data != null && form.value.nacionalidade.data.value != 3) {
			camposObrigatorios.push(form.value.cpf.data !== null && form.value.cpf.data !== '' && Utils.validaCPF(form.value.cpf.data))
		}

		if (fase.value.exibeEscolaOrigem) {
			camposObrigatorios.push(!form.value.redeOrigem.data !== null && form.value.redeOrigem.data !== '')
		}
	} else {
		if (form.value.cpf.obrigatorio) {
			if (form.value.nacionalidade.data != null && form.value.nacionalidade.data.value != 3) {
				camposObrigatorios.push(form.value.cpf.data !== null && form.value.cpf.data !== '' && Utils.validaCPF(form.value.cpf.data))
			} else if (form.value.nacionalidade.data != null) {
				form.value.cpf.required = [val => true || 'Campo Obrigatório']
			}
		}
	}

    if (form.value.cpf.data !== null && form.value.cpf.data !== '' && !Utils.validaCPF(form.value.cpf.data)) { mensagensValidacao.value.push('CPF inválido') }

	if (form.value.nacionalidade.data != null && form.value.nacionalidade.data.value == 3) {
		form.value.visto.required = [val => !!val || 'Campo Obrigatório']
		form.value.rne.required = [val => !!val || 'Campo Obrigatório']
		form.value.cpf.required = [val => !!val || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF inválido']

		if (form.value.visto.data !== null && form.value.visto.data !== '') {
			form.value.rne.required = [val => true || 'Campo Obrigatório']
			form.value.cpf.required = [val => true || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF inválido']
		}

		if (form.value.rne.data !== null && form.value.rne.data !== '') {
			form.value.visto.required = [val => true || 'Campo Obrigatório']
			form.value.cpf.required = [val => true || 'Campo Obrigatório', val => Utils.validaCPF(val) || 'CPF inválido']
		}

		if (form.value.cpf.data !== null && form.value.cpf.data !== '' && Utils.validaCPF(form.value.cpf.data)) {
			form.value.visto.required = [val => true || 'Campo Obrigatório']
			form.value.rne.required = [val => true || 'Campo Obrigatório']
		}

		camposObrigatorios.push((
			(form.value.visto.data !== null && form.value.visto.data !== '') ||
			(form.value.rne.data !== null && form.value.rne.data !== '') ||
			(form.value.cpf.data !== null && form.value.cpf.data !== '' && Utils.validaCPF(form.value.cpf.data))
		))
	}
	if (form.value.nomeSocial.obrigatorio) {
		camposObrigatorios.push(form.value.nomeSocial.data !== null && form.value.nomeSocial.data !== '')
	}
	form.value.camposPreenchidos = camposObrigatorios.indexOf(false) == -1
	emits('campos-preenchidos', form.value)
}

const buscaFase = async () => {
	if (form.value.dataNascimento.data === null || form.value.dataNascimento.data === undefined || form.value.dataNascimento.data === '') {
		validaCampos()
        return false
	}

    try {
        loading.value = true
        let data = form.value.dataNascimento.data.split('/')
        validaCampos()
        fase.value = inscricao.fase
        optionsEtapas.value = null
        if (localStorage.getItem('edicao') === null || localStorage.getItem('edicao') === 'proximo') {
            await inscricaoService.buildFases(`${data[2]}-${data[1]}-${data[0]}`)
            fase.value = inscricaoService.getStorageFases()
            if (fase.value === null) {
                form.value.dataNascimento.data = null
                toast.add({ severity: 'error', summary: 'Erro', detail: 'Não há vagas disponíveis para esta faixa etária!', life: 5000 });
                loading.value = false
                return
            }
        }

        optionsEtapas.value = fase.value.etapas.map(etapa => {
            return {label: etapa.nome + ' - ' + etapa.ensino.descricao, value: etapa.codigo}
        });

        if (optionsEtapas.value.length === 0) {
            form.value.dataNascimento.data = null
            toast.add({ severity: 'error', summary: 'Erro', detail: 'Não há vagas disponíveis para esta faixa etária!', life: 5000 });
        }

        optionsEtapas.value.sort((a, b) => {
            const nameA = a.label.toUpperCase()
            const nameB = b.label.toUpperCase()

            if (nameA > nameB) {
                return 1
            }
            if (nameA < nameB) {
                return -1
            }
            return 0
        })

        loading.value = false
        validaCampos()
    } 
    catch(e) {
        loading.value = false
        form.value.dataNascimento.data = null
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Não há vagas disponíveis para esta faixa etária!', life: 5000 });
    }
}

const preencheCamposStorage = async () => {
	if (inscricao.dataNascimento.value !== null) {
		form.value.dataNascimento.data = inscricao.dataNascimento.value
		await buscaFase()
		form.value.cpf.data = inscricao.cpf.value
		form.value.nome.data = inscricao.nome.value
		form.value.nomeSocial.data = inscricao.nomeSocial.value
		form.value.nacionalidade.data = inscricao.nacionalidade.value
		if (form.value.dataNascimento.data !== null) {
			if (fase.value !== null) {
				optionsEtapas.value = fase.value.etapas.map(etapa => {
					return {label: etapa.nome + ' - ' + etapa.ensino.descricao, value: etapa.codigo}
				});
				form.value.etapaEnsino.data = inscricao.etapaEnsino.value
				changeEtapa()
			}
		}
		form.value.rne.data = inscricao.rne.value
		form.value.visto.data = inscricao.visto.value
		form.value.redeOrigem.data = inscricao.redeOrigem.value
		if (fase.value !== null && fase.value.exibeEscolaOrigem) {
			form.value.escolaOrigem.data = inscricao.escolaOrigem.value
			changeRede()
		}
	}
}

const limitaCampo = (length, value) => {
    value.data = value.data.slice(0, length)
    validaCampos()
}

const changeEtapa = async () => {
	inscricaoService.buildEscolasDisponiveis(fase.value.codigo, form.value.etapaEnsino.data.value)
	validaCampos()
}

const changeRede = () => {
	if (fase.value.exibeEscolaOrigem) {
        exibeEscolaOrigem.value = false
		if (form.value.redeOrigem.data.value === 9) {
			exibeEscolaOrigem.value = true
		} else {
            form.value.escolaOrigem.data = null
        }
	}
	validaCampos()
}

const buscaAluno = async () => {
    if (localStorage.getItem('edicao') === null || localStorage.getItem('edicao') === 'proximo') {
        if (Utils.validaCPF(form.value.cpf.data)) {
            if (form.value.cpf.data !== null && form.value.cpf.data !== '') {
                let cpf = Utils.removeCaracteres(form.value.cpf.data)
                let aluno = await inscricaoService.getAlunoByCpf(cpf)
                if (localStorage.getItem('edicao') === null) {
                    let candidato = await inscricaoService.getCandidatoByCpf(fase.value.codigo, cpf)
                    if (candidato !== null) {
                        toast.add({ severity: 'error', summary: 'Erro', detail: 'Já exite um candidato com este cpf!', life: 5000 });
                        form.value.cpf.data = null
                        return
                    }
                }
                if (fase.value.publicosAlvo.indexOf(2) !== -1 && fase.value.publicosAlvo.indexOf(3) == -1) {
                    if (aluno === null) {
                        let msg = mensagens.value.candiato_nao_possui_matricula.conteudo
                        toast.add({ severity: 'error', summary: 'Erro', detail: msg, life: 5000 });
                    } else {
                        form.value.nome.data = aluno.nome
                    }
                }
                if (fase.value.publicosAlvo.indexOf(3) !== -1 && fase.value.publicosAlvo.indexOf(2) == -1) {
                    if (aluno !== null) {
                        let msg = mensagens.value.cadidato_possui_matricula.conteudo
                        toast.add({ severity: 'error', summary: 'Erro', detail: msg, life: 5000 });
                        form.value.nome.data = null
                        form.value.cpf.data = null
                    }
                }
            }
        }
    }
	validaCampos()
}

const buscaAlunoByVisto = async () => {
    if (localStorage.getItem('edicao') === null || localStorage.getItem('edicao') === 'proximo') {
        if (form.value.visto.data !== null && form.value.visto.data !== '') {
            let visto = form.value.visto.data
            let aluno = await inscricaoService.getAlunoByVisto(visto)
            if (localStorage.getItem('edicao') === null) {
                let candidato = await inscricaoService.getCandidatoByVisto(fase.value.codigo, visto)
                if (candidato !== null) {
                    toast.add({ severity: 'error', summary: 'Erro', detail: 'Já exite um candidato com este visto!', life: 5000 });
                    form.value.visto.data = null
                    return
                }
            }
            if (fase.value.publicosAlvo.indexOf(2) !== -1 && fase.value.publicosAlvo.indexOf(3) == -1) {
                if (aluno === null) {
                    let msg = mensagens.value.candiato_nao_possui_matricula.conteudo
                    toast.add({ severity: 'error', summary: 'Erro', detail: msg, life: 5000 });
                } else {
                    form.value.nome.data = aluno.nome
                }
            }
            if (fase.value.publicosAlvo.indexOf(3) !== -1 && fase.value.publicosAlvo.indexOf(2) == -1) {
                if (aluno !== null) {
                    let msg = mensagens.value.cadidato_possui_matricula.conteudo
                    toast.add({ severity: 'error', summary: 'Erro', detail: msg, life: 5000 });
                    form.value.nome.data = null
                    form.value.visto.data = null
                }
            }
        }
    }
    validaCampos()
}

const buscaAlunoByRne = async () => {
    if (localStorage.getItem('edicao') === null || localStorage.getItem('edicao') === 'proximo') {
        if (form.value.rne.data !== null && form.value.rne.data !== '') {
            let rne = form.value.rne.data
            let aluno = await inscricaoService.getAlunoByRne(rne)
            if (localStorage.getItem('edicao') === null) {
                let candidato = await inscricaoService.getCandidatoByRne(fase.value.codigo, rne)
                if (candidato !== null) {
                    toast.add({ severity: 'error', summary: 'Erro', detail: 'Já exite um candidato com este rne!', life: 5000 });
                    form.value.rne.data = null
                    return
                }
            }
            if (fase.value.publicosAlvo.indexOf(2) !== -1 && fase.value.publicosAlvo.indexOf(3) == -1) {
                if (aluno === null) {
                    let msg = mensagens.value.candiato_nao_possui_matricula.conteudo
                    toast.add({ severity: 'error', summary: 'Erro', detail: msg, life: 5000 });
                } else {
                    form.value.nome.data = aluno.nome
                }
            }
            if (fase.value.publicosAlvo.indexOf(3) !== -1 && fase.value.publicosAlvo.indexOf(2) == -1) {
                if (aluno !== null) {
                    let msg = mensagens.value.cadidato_possui_matricula.conteudo
                    toast.add({ severity: 'error', summary: 'Erro', detail: msg, life: 5000 });
                    form.value.nome.data = null
                    form.value.rne.data = null
                }
            }
        }
    }
    validaCampos()
}

const limpaFormulario = () => {
    form.value.dataNascimento.data = null
	form.value.nacionalidade.data = null
	form.value.cpf.data = null
	form.value.visto.data = null
	form.value.rne.data = null
	form.value.nome.data = null
	form.value.nomeSocial.data = null
	form.value.etapaEnsino.data = null
	form.value.redeOrigem.data = null
	form.value.escolaOrigem.data = null
    emits('limpar-formulario')
    validaCampos()
}

const confirmaLimpar = () => {
    confirm.require({
        header: 'Confirmar',
        message: 'Deseja realmente limpar o formulário?',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Confirmar',
        accept: () => {
            limpaFormulario()
        },
        reject: () => {
            return false
        }
    });
};

const buscaDadosCandidato = async () => {
    if (localStorage.getItem('edicao') !== null) { 
        return false;
    }

    try {
        let nacionalidade = form.value.nacionalidade.data;
        let tipo = 'cpf';
        let dadoConsulta = '';
        let validacao = 'CPF';

        if (nacionalidade == null) {
            nacionalidade = 1;
        } else {
            nacionalidade = nacionalidade.value;
        }

        switch (nacionalidade) {
            case 1:
            case 2:
                tipo = 'cpf';
                dadoConsulta = Utils.removeCaracteres(form.value.cpf.data); 
            break;
            case 3:
                validacao = 'CPF, VISTO ou RNE'; 
                if (form.value.visto.data !== null && form.value.visto.data !== '') {
                    tipo = 'visto';
                    dadoConsulta = form.value.visto.data;
                }
                if (form.value.rne.data !== null && form.value.rne.data !== '') {
                    tipo = 'rnm';
                    dadoConsulta = form.value.rne.data;
                }
                if (form.value.cpf.data !== null && form.value.cpf.data !== '') {
                    tipo = 'cpf';
                    dadoConsulta = Utils.removeCaracteres(form.value.cpf.data); 
                }
            break;
        }

        if (dadoConsulta == null || dadoConsulta == '') {
            toast.add({ severity: 'error', summary: 'Erro', detail: `É necessário informar o campo ${validacao}.`, life: 5000 });
            return false;
        }

        if (dadoConsulta == null || dadoConsulta == '' || dadoConsulta == undefined) {
            return false;
        }
        if (tipo == 'cpf') {
            if (!Utils.validaCPF(dadoConsulta)) {
                return false;
            }    
        }

        let data = form.value.dataNascimento.data;
        let dataNascimento = null;
        if (data != null) {
            data = data.split('/');
            if (data.length == 3) {
                dataNascimento = `${data[2]}-${data[1]}-${data[0]}`;
            }
        }
        loading.value = true
        let inscricoes = await consulta.consultaCandidato(tipo, dadoConsulta, dataNascimento);

        if (inscricoes.erro == true) {
            toast.add({ severity: 'error', summary: 'Erro', detail: inscricoes.messagem, life: 5000 });
            form.value.cpf.data = null
            form.value.dataNascimento.data = null
            form.value.nome.data = null
            loading.value = false
            return false;
        } else {
            if (inscricoes.dados != null) {
                inscricoes = inscricoes.dados
                form.value.nome.data = inscricoes.nome
                form.value.nomeSocial.data = inscricoes.nomeSocial
                form.value.nacionalidade.data = inscricoes.nacionalidade
                form.value.cpf.data = inscricoes.cpf
                form.value.rne.data = inscricoes.rne
                form.value.visto.data = inscricoes.visto
                await inscricaoService.salvarFormularioStorageEdicao(inscricoes)
                emits('campos-preenchidos', inscricoes)
                validaCampos()
                loading.value = false
                if (dataNascimento == null) {
                    form.value.dataNascimento.data = inscricoes.dataNascimento
                    buscaFase()
                }
            }
        }
        validaCampos()
        loading.value = false
    }
    catch(e) {
        loading.value = false
    }
}

onMounted(async () => {
    optionsRedesOrigem.value = (await window.axios('v4/api/educacao/files/redes')).data
    campos.value = (await window.axios('v4/api/educacao/files/campos-opcionais')).data
    mensagens.value = (await window.axios('v4/api/educacao/files/mensagens')).data

    form.value.nomeSocial.required = campos.value.nome_social.obrigatorio
    form.value.nomeSocial.show =  campos.value.nome_social.apresenta
    form.value.nomeSocial.obrigatorio = campos.value.nome_social.obrigatorio
    form.value.cpf.show =  campos.value.cpf_aluno.apresenta
    form.value.cpf.obrigatorio = campos.value.cpf_aluno.obrigatorio
   
    await inscricaoService.buildEscolasOrigem()
    await inscricaoService.getProfissoes()
	optionsEscolaOrigem.value = inscricaoService.getStorageEscolasOrigem()
    await preencheCamposStorage()
    validaCampos()
    if (localStorage.getItem('edicao') !== null) {
        localStorage.setItem('edicao', 'proximo')
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
                    <InputText type="text" v-model="form.dataNascimento.data" v-mask="'##/##/####'" 
                    :placeholder="form.dataNascimento.label" @focusout="buscaFase"
                    :class="form.dataNascimento.required ? 'obrigatorio' : ''" />
                    <label for="">{{ form.dataNascimento.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <Dropdown v-model="form.nacionalidade.data"
                    :options="optionsNacionalidade" optionLabel="label" 
                    @update:model-value="validaCampos" :class="form.nacionalidade.required ? 'obrigatorio' : ''" />
                    <label for="">{{ form.nacionalidade.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.cpf.data" v-mask="'###.###.###-##'" 
                    :placeholder="form.cpf.label"
                    @focusout="buscaDadosCandidato"
                    :class="form.cpf.required ? 'obrigatorio' : ''" />
                    <label for="">{{ form.cpf.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.nome.data" :placeholder="form.nome.label" 
                    @update:model-value="limitaCampo(70, form.nome)"
                    :class="form.nome.required ? 'obrigatorio' : ''" />
                    <label for="">{{ form.nome.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText type="text" v-model="form.nomeSocial.data" 
                    :placeholder="form.nomeSocial.label" 
                    :class="form.nomeSocial.required ? 'obrigatorio' : ''"
                    @update:model-value="limitaCampo(70, form.nomeSocial)" />
                    <label for="">{{ form.nomeSocial.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="form.nacionalidade.data != null && form.nacionalidade.data.value == 3">
                <span class="p-float-label">
                    <InputText type="text" v-show="form.nacionalidade.data != null && form.nacionalidade.data.value == 3" 
                    v-model="form.visto.data" 
                    :placeholder="form.visto.label"
                    @update:model-value="limitaCampo(20, form.visto)" 
                    @focusout="buscaDadosCandidato"
                    />
                    <label for="" v-show="form.nacionalidade.data != null && form.nacionalidade.data.value == 3">
                        {{ form.visto.label }}
                    </label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="form.nacionalidade.data != null && form.nacionalidade.data.value == 3">
                <span class="p-float-label">
                    <InputText type="text" v-show="form.nacionalidade.data != null && form.nacionalidade.data.value == 3" 
                    v-model="form.rne.data" 
                    :placeholder="form.rne.label" 
                    @update:model-value="limitaCampo(20, form.rne)" 
                    @focusout="buscaDadosCandidato"
                    />
                    <label for="">
                        {{ form.rne.label }}
                    </label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <Dropdown v-model="form.etapaEnsino.data"
                    :options="optionsEtapas" optionLabel="label" 
                    @update:model-value="changeEtapa"
                    :class="form.etapaEnsino.required ? 'obrigatorio' : ''" />
                    <label for="">{{ form.etapaEnsino.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <Dropdown v-model="form.redeOrigem.data"
                    :options="optionsRedesOrigem" optionLabel="label" 
                    @update:model-value="changeRede"
                    :class="form.redeOrigem.required ? 'obrigatorio' : ''" />
                    <label for="">{{ form.redeOrigem.label }}</label>
                </span>
            </div>

            <div class="field col-12 md:col-12" v-show="exibeEscolaOrigem">
                <span class="p-float-label">
                    <Dropdown v-show="exibeEscolaOrigem" v-model="form.escolaOrigem.data"
                    :options="optionsEscolaOrigem" optionLabel="label" 
                    @update:model-value="validaCampos" />
                    <label for="">{{ form.escolaOrigem.label }}</label>
                </span>
            </div>
            
            <div class="flex field col-12 md:col-12">
                <div class="flex pt-4 justify-content-between">
                    <Button label="Limpar Formulário" severity="danger" icon="pi pi-eraser" @click="confirmaLimpar()" />
                </div>
            </div>
        </div>
    </div>
    <ConfirmDialog/>
    <ModalLoading :isLoading="loading"/>
</template>
<style scoped>
    .p-inputtext{ background-color: #fff; border: 1px solid rgba(0, 0, 0, 0.12);}
    .p-dropdown { border: 1px solid rgba(0, 0, 0, 0.12);}
	.border-2,.border-dashed {padding: 10px 5px !important;}
</style>
