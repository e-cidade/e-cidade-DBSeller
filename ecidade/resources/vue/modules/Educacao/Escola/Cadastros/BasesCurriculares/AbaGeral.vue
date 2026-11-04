<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import DialogPesquisaEducacao from "../../../Components/DialogPesquisaEducacao.vue";
import ModalLoading from "../../../../Components/ModalLoading.vue";

const toast = useToast();
const props = defineProps(['escola', 'secretaria', 'base', 'bases'])
const emits = defineEmits(['save'])
const visible = ref(false)
const funcPesquisa = ref();
const renderDialog = ref(false);
const loading = ref(false)
const parametrosDialogPesquisa = ref({
    titulo: null,
    rota: null,
    camposExibir: null,
    callback: null
})
const dialogReplica = ref(false)
const form = ref({
    codigoBaseImportada: null,
    curso : {
        nome: {
            value: null,
            required: true,
            disabled: true,
            label: 'Nome do Curso'
        },
        codigo: {
            value: null,
            required: true,
            disabled: true,
            label: 'Código'
        },
        atos: [],
        cursoEscola: null,
        ensino: null
    },
    nome: {
        value: null,
        required: true,
        disabled: false,
        label: 'Nome da Base'
    },
    codigo: {
        value: null,
        required: false,
        disabled: true,
        label: 'Código'
    },
    observacao: {
        value: null,
        required: true,
        disabled: true,
        label: 'Observação'
    },
    slctdTurno: {
        data: null,
        required: true,
        disabled: true,
        label: 'Turno'
    },
    regimeMatricula: {
        nome: {
            value: null,
            required: true,
            disabled: true,
            label: 'Regime de Matrícula'
        },
        codigo: {
            value: null,
            required: true,
            disabled: true,
            label: 'Código'
        },
        divisoes: []
    },
    etapaInicial: {
        nome: {
            value: null,
            required: true,
            disabled: true,
            label: 'Etapa Inicial'
        },
        codigo: {
            value: null,
            required: true,
            disabled: true,
            label: 'Código'
        }
    },
    etapaFinal: {
        nome: {
            value: null,
            required: true,
            disabled: true,
            label: 'Etapa Final'
        },
        codigo: {
            value: null,
            required: true,
            disabled: true,
            label: 'Código'
        }
    },
    slctdFrequencia: {
        data: null,
        required: true,
        disabled: true,
        label: 'Frequência'
    },
    slctdControleFrequencia: {
        data: null,
        required: true,
        disabled: true,
        label: 'Controle de Frequência'
    }
})
const optionsTurno = [
    {name: 'DIURNO', code: null},
    {name: 'NOTURNO', code: null},
    {name: 'DIURNO E NOTURNO', code: null},
]
const optionsControleFrequencia = [
    {name: 'GLOBAL', code: 'G'},
    {name: 'INDIVIDUAL', code: 'I'}
]
const optionsFrequencia = [
    {name: 'DIA LETIVO', code: 'D'},
    {name: 'PERIODO', code: 'P'}
]
const checks = ref([])
const divisoesRegimeMatricula = ref([])
const routes = {
    cursos: props.secretaria ? `v4/api/educacao/secretaria/cursos/` : `v4/api/educacao/escola/${props.escola}/cursos/`,
    regimesMatricula: `v4/api/educacao/escola/${props.escola}/regimes-matricula/`,
    etapa: `v4/api/educacao/escola/${props.escola}/etapas/`,
    salvar: `v4/api/educacao/escola/bases-curriculares/salvar/`,
    bases: `v4/api/educacao/secretaria/bases-curriculares/`
}

const pesquisaCursos = async () => {
    parametrosDialogPesquisa.value.titulo = 'Cursos'
    parametrosDialogPesquisa.value.rota = routes.cursos;
    parametrosDialogPesquisa.value.callback = retornoPesquisaCursos;
    parametrosDialogPesquisa.value.camposExibir = [
        {
            field: 'codigo',
            dataType: 'int',
            label: 'Codigo',
            columnSize: 15
        },
        {
            field: 'nome',
            dataType: 'string',
            label: 'Descrição',
            columnSize: 15
        },
        {
            field: 'ensino.nome',
            dataType: 'string',
            label: 'Nível de Ensino',
            columnSize: 15
        },
        {
            field: 'incluiNoHistorico',
            dataType: 'boolean',
            label: 'Incluir no Historico',
            columnSize: 15
        }
    ]
    renderDialog.value = true
    loading.value = true
    await openDialog()
    loading.value = false
}

const pesquisaRegimesMatricula = async () => {
    parametrosDialogPesquisa.value.titulo = 'Regimes de Matrícula'
    parametrosDialogPesquisa.value.rota = routes.regimesMatricula + form.value.curso.ensino.codigo
    parametrosDialogPesquisa.value.callback = retornoPesquisaRegimesMatricula;
    parametrosDialogPesquisa.value.camposExibir = [
        {
            field: 'codigo',
            dataType: 'int',
            label: 'Codigo',
            columnSize: 15
        },
        {
            field: 'nome',
            dataType: 'string',
            label: 'Descrição',
            columnSize: 15
        },
        {
            field: 'abreviatura',
            dataType: 'string',
            label: 'Abreviatura',
            columnSize: 15
        },
        {
            field: 'divisao',
            dataType: 'string',
            label: 'Divisão',
            columnSize: 15
        }
    ]
    renderDialog.value = true
    loading.value = true
    await openDialog()
    loading.value = false
}
const replicarBase = () => {
    form.value.codigoBaseImportada = props.base.codigo
    form.value.codigo.value = null
    form.value.nome.value = null
    form.value.regimeMatricula.codigo.value = null
    form.value.regimeMatricula.codigo.disabled = false
    form.value.regimeMatricula.nome.value = null
    dialogReplica.value = true
}

const cancelaReplica = () => {
    form.value.codigoBaseImportada = null
    form.value.codigo.value = props.base.codigo
    form.value.nome.value = props.base.nome
    form.value.regimeMatricula.codigo.value = props.base.regimeMatricula.codigo
    form.value.regimeMatricula.nome.value = props.base.regimeMatricula.nome
    form.value.regimeMatricula.codigo.disabled = true
    dialogReplica.value = false
}
const pesquisaBasesSecretaria = async () => {
    parametrosDialogPesquisa.value.titulo = 'Bases Curriculares'
    parametrosDialogPesquisa.value.rota = routes.bases;
    parametrosDialogPesquisa.value.callback = retornoBasesSecretaria;
    parametrosDialogPesquisa.value.camposExibir = [
        {
            field: 'codigo',
            dataType: 'int',
            label: 'Codigo',
            columnSize: 15
        },
        {
            field: 'descricao',
            dataType: 'string',
            label: 'Descrição',
            columnSize: 15
        },
        {
            field: 'etapaInicial.nome',
            dataType: 'string',
            label: 'Etapa Inicial',
            columnSize: 15
        },
        {
            field: 'etapaFinal.nome',
            dataType: 'string',
            label: 'Etapa Final',
            columnSize: 15
        },
        {
            field: 'turno',
            dataType: 'string',
            label: 'Turno',
            columnSize: 15
        },
        {
            field: 'controleFrequencia',
            dataType: 'string',
            label: 'Controle de Frequência',
            columnSize: 15
        },
        {
            field: 'medidaFrequencia',
            dataType: 'string',
            label: 'Medida de Frequência',
            columnSize: 15
        }
    ]
    renderDialog.value = true
    loading.value = true
    await openDialog()
    loading.value = false
}
const pesquisaEtapas = async (callback) => {
    parametrosDialogPesquisa.value.titulo = 'Etapas'
    parametrosDialogPesquisa.value.rota = `${routes.regimesMatricula}${form.value.regimeMatricula.codigo.value}/etapas`;
    parametrosDialogPesquisa.value.callback = callback;
    parametrosDialogPesquisa.value.camposExibir = [
        {
            field: 'codigo',
            dataType: 'int',
            label: 'Codigo',
            columnSize: 15
        },
        {
            field: 'nome',
            dataType: 'string',
            label: 'Descrição',
            columnSize: 15
        },
        {
            field: 'abreviatura',
            dataType: 'string',
            label: 'Abreviatura',
            columnSize: 15
        },
        {
            field: 'nomeRegimeMatricula',
            dataType: 'string',
            label: 'Regimde Matricula',
            columnSize: 15
        },
        {
            field: 'codigoCenso',
            dataType: 'int',
            label: 'Codigo Censo',
            columnSize: 15
        }
    ]
    renderDialog.value = true
    loading.value = true
    await openDialog()
    loading.value = false
}

const retornoEtapaInicial = (e) => {
    form.value.etapaInicial.codigo.value = e.codigo
    form.value.etapaInicial.nome.value = e.nome
    form.value.etapaFinal.codigo.disabled = false

    liberaCampos()
}
const retornoEtapaFinal = (e) => {
    form.value.etapaFinal.codigo.value = e.codigo
    form.value.etapaFinal.nome.value = e.nome
    form.value.slctdFrequencia.disabled = false

    liberaCampos()
}
const retornoPesquisaRegimesMatricula = (e) => {
    form.value.regimeMatricula.codigo.value = e.codigo;
    form.value.regimeMatricula.nome.value = e.nome;
    form.value.regimeMatricula.divisoes = e.divisoes;
    form.value.etapaInicial.codigo.disabled = false

    liberaCampos()
}

const retornoPesquisaCursos = (e) => {
    form.value.curso.codigo.value = e.codigo;
    form.value.curso.nome.value = e.nome;
    form.value.curso.atos = e.atos;
    form.value.curso.cursoEscola = e.cursoEscola
    form.value.curso.ensino = e.ensino
    form.value.nome.disabled = false

    liberaCampos()
}

const limpaCampos = () => {
    form.value.codigo.value = null
    form.value.nome.value = null
    form.value.curso.codigo.value = null
    form.value.curso.nome.value = null
    form.value.regimeMatricula.codigo.value = null
    form.value.regimeMatricula.nome.value = null
    form.value.regimeMatricula.divisoes = null
    form.value.etapaInicial.codigo.value = null
    form.value.etapaInicial.nome.value = null
    form.value.etapaFinal.codigo.value = null
    form.value.etapaFinal.nome.value = null
    form.value.observacao.value = null
    form.value.slctdTurno.data = null
    form.value.slctdFrequencia.data = null
    form.value.slctdControleFrequencia.data = null
    checks.value.length = 0
    divisoesRegimeMatricula.value.length = 0
}

const retornoBasesSecretaria = (e) => {
    limpaCampos()
    form.value.codigoBaseImportada = e.codigo
    form.value.nome.value = e.descricao
    form.value.curso.codigo.value = e.curso.codigo
    form.value.curso.nome.value = e.curso.nome
    form.value.regimeMatricula.codigo.value = e.regimeMatricula.codigo
    form.value.regimeMatricula.nome.value = e.regimeMatricula.nome
    form.value.regimeMatricula.divisoes = e.regimeMatricula.divisoes
    form.value.etapaInicial.codigo.value = e.etapaInicial.codigo
    form.value.etapaInicial.nome.value = e.etapaInicial.nome
    form.value.etapaFinal.codigo.value = e.etapaFinal.codigo
    form.value.etapaFinal.nome.value = e.etapaFinal.nome
    form.value.observacao.value = e.observacao
    form.value.slctdTurno.data = optionsTurno.filter(option => option.name === e.turno).shift()
    form.value.slctdFrequencia.data =
        optionsFrequencia.filter(frequencia => frequencia.code === e.medidaFrequencia).shift()

    form.value.slctdControleFrequencia.data = 
        optionsControleFrequencia.filter(option => option.code === e.controleFrequencia).shift()
    if (e.isAtiva) {
        checks.value.push('isAtiva')
    }
    if (e.conclusao) {
        checks.value.push('concluiCurso')
    }
    form.value.regimeMatricula.divisoes.forEach(divisaoExistente => {
        e.divisoesRegimeMatricula.forEach(divisaoMarcada => {
            if (divisaoMarcada.codigo === divisaoExistente.codigo) {
                divisoesRegimeMatricula.value.push(`${divisaoExistente.codigo}`)
            }
        })
    })

    liberaCampos()
}
const buscaEtapaI = async () => {
    let codigo = form.value.etapaInicial.codigo.value;
    if (codigo === '' || codigo === null) {
        form.value.etapaInicial.nome.value = null
        form.value.etapaFinal.codigo.value = null
        form.value.etapaFinal.nome.value = null
        return;
    }

    try{
        let etapa = (await window.axios.get(`${routes.etapa}${codigo}`)).data.data;
        if (etapa != null) {
            form.value.etapaInicial.nome.value  = etapa.nome.trim()
        }
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
    liberaCampos()
    loading.value = false
}

const buscaEtapaF = async () => {
    let codigo = form.value.etapaFinal.codigo.value;
    if (codigo === '' || codigo === null) {
        form.value.etapaFinal.nome.value = null
        form.value.slctdFrequencia.data = null
        return;
    }
    try {
        let etapa = (await window.axios.get(`${routes.etapa}${codigo}`)).data.data;
        loading.value = false
        if (etapa != null) {
            form.value.etapaFinal.nome.value  = etapa.nome.trim()
        }
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
    liberaCampos()
}

const buscaCurso = async () => {
    let codigo = form.value.curso.codigo.value;
    if (codigo === '' || codigo === null) {
        form.value.curso.nome.value = null
        form.value.nome.disabled = true
        form.value.nome.value = null
        form.value.slctdTurno.data = null
        form.value.regimeMatricula.codigo.value = null
        form.value.regimeMatricula.nome.value = null
        form.value.etapaFinal.nome.value = null
        form.value.etapaInicial.nome.value = null
        form.value.etapaFinal.codigo.value = null
        form.value.etapaInicial.codigo.value = null
        form.value.slctdControleFrequencia.data = null
        form.value.slctdFrequencia.data = null
        form.value.slctdControleFrequencia.disabled = true
        checks.value.length = 0
        return;
    }
    loading.value = true
    try {
        let curso = (await window.axios.get(`${routes.cursos}${codigo}`)).data.data;
        if (curso != null) {

            form.value.curso.nome.value  = curso.ed29_c_descr.trim()
            form.value.curso.atos = curso.atos;
            form.value.curso.cursoEscola = curso.ed71_i_codigo
            form.value.curso.ensino = curso.ensinos
        }
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
    liberaCampos()
    loading.value = false
}

const buscaRegimeMatricula = async () => {
    let codigo = form.value.regimeMatricula.codigo.value;
    if (codigo === '' || codigo === null) {
        form.value.regimeMatricula.nome.value = null
        form.value.etapaInicial.codigo.value = null
        form.value.etapaInicial.nome.value = null
        form.value.regimeMatricula.divisoes = [];
        return;
    }
    loading.value = true
    let regimeMatricula = (await window.axios.get(`${routes.regimesMatricula}${codigo}`)).data.data;
    loading.value = false
    if (regimeMatricula != null) {
        form.value.regimeMatricula.nome.value  = regimeMatricula.nome.trim()
        form.value.regimeMatricula.divisoes = regimeMatricula.divisoes;
    }
    liberaCampos()
}
const openDialog = async () => {
    setTimeout(async () => {
        loading.value = true
        await funcPesquisa.value.open();
        loading.value = false
    }, 100)
}

const salvar = async () => {
    if (dialogReplica.value) {
        dialogReplica.value = false
    }
    let parametros = {}
    parametros.codigoBaseImportada = form.value.codigoBaseImportada
    parametros.codigo = form.value.codigo.value
    parametros.curso = form.value.curso.codigo.value
    parametros.cursoAtos = form.value.curso.atos
    parametros.cursoEscola = form.value.curso.cursoEscola
    parametros.regimeMatricula = form.value.regimeMatricula.codigo.value
    parametros.divisoesRegimeMatricula = divisoesRegimeMatricula.value.length > 0 ?
        form.value.regimeMatricula.divisoes.filter(divisao => divisoesRegimeMatricula.value.includes(`${divisao.codigo}`)) : []
    parametros.etapaFinal = form.value.etapaFinal.codigo.value
    parametros.etapaInicial = form.value.etapaInicial.codigo.value
    parametros.nome = form.value.nome.value
    parametros.observacao = form.value.observacao.value
    parametros.turno = form.value.slctdTurno.data.name
    parametros.frequencia = form.value.slctdFrequencia.data.code
    parametros.controleFrequencia = form.value.slctdControleFrequencia.data.code
    parametros.concluiCurso = checks.value.includes('concluiCurso')
    parametros.isAtiva = checks.value.includes('isAtiva')

    if (!props.secretaria) {
        parametros.escola = props.escola
    }

    try {
        loading.value = true
        let resposta = (await window.axios.post(routes.salvar, parametros)).data.data;
        if (resposta.editada) {
            props.bases.map((b) => {
                if (b.codigo === resposta.codigo) {
                    b.descricao = resposta.descricao
                    b.turno = resposta.turno
                    b.curso = resposta.curso
                    b.regimeMatricula = resposta.regimeMatricula 
                    b.etapaInicial = resposta.etapaInicial
                    b.etapaFinal = resposta.etapaFinal
                    b.medidaFrequencia = resposta.medidaFrequencia
                    b.controleFrequencia = resposta.controleFrequencia
                    b.conclusao = resposta.conclusao
                    b.isAtiva = resposta.isAtiva
                    b.observacao = resposta.observacao
                }
            })
        } else {
            props.bases.push(resposta)
        }
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Sucesso ao Salvar`,
            life: 5000
        });
        loading.value = false
        emits('save', resposta)
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `${e.response.data.message}`,
            life: 5000
        });
    }
}

const liberaCampos = () => {
    if (props.base === null) {
        form.value.slctdTurno.disabled = form.value.nome.value === null
        form.value.curso.codigo.disabled = form.value.slctdTurno.data === null
        form.value.regimeMatricula.codigo.disabled = form.value.curso.codigo.value === null
        form.value.etapaInicial.codigo.disabled = form.value.regimeMatricula.codigo.value === null
        form.value.etapaFinal.codigo.disabled = form.value.etapaInicial.codigo.value === null
        form.value.slctdFrequencia.disabled = form.value.etapaFinal.codigo.value === null
        form.value.slctdControleFrequencia.disabled = form.value.slctdFrequencia.data === null
        form.value.observacao.disabled = form.value.slctdControleFrequencia.data === null
    }
}
onMounted(() => {
    if (props.base != null) {
        form.value.slctdTurno.disabled = false
        form.value.etapaFinal.codigo.disabled = false
        form.value.etapaInicial.codigo.disabled = false
        form.value.slctdFrequencia.disabled = false
        form.value.slctdControleFrequencia.disabled = false
        form.value.observacao.disabled = false

        form.value.codigo.value = props.base.codigo
        form.value.nome.value = props.base.descricao
        form.value.curso.codigo.value = props.base.curso.codigo
        form.value.curso.nome.value = props.base.curso.nome
        form.value.curso.ensino = props.base.curso.ensino
        form.value.regimeMatricula.codigo.value = props.base.regimeMatricula.codigo
        form.value.regimeMatricula.nome.value = props.base.regimeMatricula.nome
        form.value.regimeMatricula.divisoes = props.base.regimeMatricula.divisoes
        form.value.etapaInicial.codigo.value = props.base.etapaInicial.codigo
        form.value.etapaInicial.nome.value = props.base.etapaInicial.nome
        form.value.etapaFinal.codigo.value = props.base.etapaFinal.codigo
        form.value.etapaFinal.nome.value = props.base.etapaFinal.nome
        form.value.observacao.value = props.base.observacao
        form.value.slctdTurno.data = optionsTurno.filter(option => option.name === props.base.turno).shift()
        form.value.slctdFrequencia.data =
            optionsFrequencia.filter(frequencia => frequencia.code === props.base.medidaFrequencia).shift()

        form.value.slctdControleFrequencia.data = 
            optionsControleFrequencia.filter(option => option.code === props.base.controleFrequencia).shift()

        if (props.base.isAtiva) {
            checks.value.push('isAtiva')
        }
        if (props.base.conclusao) {
            checks.value.push('concluiCurso')
        }
        form.value.regimeMatricula.divisoes.forEach(divisaoExistente => {
            props.base.divisoesRegimeMatricula.forEach(divisaoMarcada => {
                if (divisaoMarcada.codigo === divisaoExistente.codigo) {
                    divisoesRegimeMatricula.value.push(`${divisaoExistente.codigo}`)
                }
            })
        })
    }
})
</script>
<template>
    <div style="width: 800px; height: 670px">
        <div class="p-fluid grid">
            <div class="field col-12 md:col-6">
                <div class="p-inputgroup flex-1">
                     <span class="p-float-label pr-2" v-show="props.base != null">
                        <InputNumber inputId="codigo" :useGrouping="false"
                                     style="width: 30px"
                                     v-model="form.codigo.value"
                                     :required="form.codigo.required"
                                     :disabled="form.codigo.disabled"
                       />
                        <label for="">{{ form.codigo.label }}</label>
                    </span>
                    <span class="p-float-label">
                        <InputText
                            id="nome"
                            style="width: 269px"
                            v-model="form.nome.value"
                            :required="form.nome.required"
                            :disabled="form.nome.disabled"  @keydown="liberaCampos"/>
                        <label for="">{{ form.nome.label }}</label>
                    </span>
                </div>
            </div>
            <div class="field col-12 md:col-6">
                <span class="p-float-label">
                    <Dropdown id="turno"  :options="optionsTurno"
                              optionLabel="name"
                              v-model="form.slctdTurno.data"
                              :disabled="form.slctdTurno.disabled" @change="liberaCampos"/>
                     <label for="">{{ form.slctdTurno.label }}</label>
                </span>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12 ">
                <div class="p-inputgroup flex-1">
                     <span class="p-float-label pr-2">
                        <InputNumber inputId="codigoCurso" :useGrouping="false" @focusout="buscaCurso"
                                     style="width: 20px"
                                     v-model="form.curso.codigo.value"
                                     :required="form.curso.codigo.required"
                                     :disabled="form.curso.codigo.disabled"/>
                        <label for="">{{ form.curso.codigo.label }}</label>
                    </span>
                    <span class="p-float-label">
                        <InputText
                            style="width: 600px"
                            id="nomeCurso"
                            v-model="form.curso.nome.value"
                            :required="form.curso.nome.required"
                            :disabled="form.curso.nome.disabled"/>
                        <label for="">{{ form.curso.nome.label }}</label>
                    </span>
                    <Button @click="pesquisaCursos" icon="pi pi-search" aria-label="Filter" :disabled="form.curso.codigo.disabled" style="width: 400px"/>
                </div>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                <div class="p-inputgroup flex-1">
                     <span class="p-float-label pr-2">
                        <InputNumber inputId="regimeMatricula" :useGrouping="false" @focusout="buscaRegimeMatricula"
                                     style="width: 20px"
                                     v-model="form.regimeMatricula.codigo.value"
                                     :required="form.regimeMatricula.codigo.required"
                                    :disabled="form.regimeMatricula.codigo.disabled"/>
                        <label for="">{{ form.regimeMatricula.codigo.label }}</label>
                    </span>
                    <span class="p-float-label">
                        <InputText
                            style="width: 600px"
                            id="nomeCurso"
                            v-model="form.regimeMatricula.nome.value"
                            :required="form.regimeMatricula.nome.required"
                            :disabled="form.regimeMatricula.nome.disabled"/>
                        <label for="">{{ form.regimeMatricula.nome.label }}</label>
                    </span>
                    <Button @click="pesquisaRegimesMatricula" :disabled="form.regimeMatricula.codigo.disabled" icon="pi pi-search" aria-label="Filter" style="width: 400px"/>
                </div>
            </div>
        </div>
        <div class="p-fluid grid" v-if="form.regimeMatricula.divisoes.length > 0">
            <div class="field col-12 md:col-12">
                <Fieldset legend="Divisões do Regime de Matrícula">
                    <div class="field-checkbox" v-for="item in form.regimeMatricula.divisoes">
                        <Checkbox :inputId="item.nome" :name="item.nome" :value="`${item.codigo}`" v-model="divisoesRegimeMatricula"/>
                        <label :for="item.nome">{{ item.nome }}</label>
                    </div>
                </Fieldset>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                <div class="p-inputgroup flex-1">
                             <span class="p-float-label pr-2">
                                <InputNumber inputId="etapaInicial" :useGrouping="false" @focusout="buscaEtapaI"
                                             style="width: 20px"
                                             v-model="form.etapaInicial.codigo.value"
                                             :required="form.etapaInicial.codigo.required"
                                             :disabled="form.etapaInicial.codigo.disabled"/>
                                <label for="">{{ form.etapaInicial.codigo.label }}</label>
                            </span>
                    <span class="p-float-label">
                                <InputText
                                    style="width: 600px"
                                    id="nomeCurso"
                                    v-model="form.etapaInicial.nome.value"
                                    :required="form.etapaInicial.nome.required"
                                    :disabled="form.etapaInicial.nome.disabled"/>
                                <label for="">{{ form.etapaInicial.nome.label }}</label>
                            </span>
                    <Button @click="pesquisaEtapas(retornoEtapaInicial)"  :disabled="form.etapaInicial.codigo.disabled" icon="pi pi-search" aria-label="Filter" style="width: 400px"/>
                </div>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                <div class="p-inputgroup flex-1">
                     <span class="p-float-label pr-2">
                        <InputNumber inputId="etapaFinal" :useGrouping="false" @focusout="buscaEtapaF"
                                     style="width: 20px"
                                     v-model="form.etapaFinal.codigo.value"
                                     :required="form.etapaFinal.codigo.required"
                                     :disabled="form.etapaFinal.codigo.disabled"/>
                        <label for="">{{ form.etapaFinal.codigo.label }}</label>
                    </span>
                    <span class="p-float-label">
                        <InputText
                            style="width: 600px"
                            id="nomeCurso"
                            v-model="form.etapaFinal.nome.value"
                            :required="form.etapaFinal.nome.required"
                            :disabled="form.etapaFinal.nome.disabled"/>
                        <label for="">{{ form.etapaFinal.nome.label }}</label>
                    </span>
                    <Button @click="pesquisaEtapas(retornoEtapaFinal)" :disabled="form.etapaFinal.codigo.disabled" icon="pi pi-search" aria-label="Filter" style="width: 400px"/>
                </div>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-6">
                <span class="p-float-label">
                    <Dropdown id="frequecnia" :options="optionsFrequencia"
                              optionLabel="name"
                              v-model="form.slctdFrequencia.data"
                              :disabled="form.slctdFrequencia.disabled" @change="liberaCampos"/>
                     <label for="">{{ form.slctdFrequencia.label }}</label>
                </span>
            </div>
            <div class="field col-12 md:col-6">
                <span class="p-float-label">
                    <Dropdown id="controlaFrequecnia"  :options="optionsControleFrequencia"
                              optionLabel="name"
                              v-model="form.slctdControleFrequencia.data"
                              :disabled="form.slctdControleFrequencia.disabled" @change="liberaCampos"/>
                     <label for="">{{ form.slctdControleFrequencia.label }}</label>
                </span>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-6">
                <div class="field-checkbox">
                    <Checkbox inputId="concluiCurso" name="concluiCurso" value="concluiCurso" v-model="checks"
                              :disabled="form.slctdControleFrequencia.data === null"/>
                    <label for="concluiCurso">Conclui Curso</label>
                </div>
            </div>
            <div class="field col-12 md:col-6">
                <div class="field-checkbox">
                    <Checkbox inputId="isAtiva" name="isAtiva" value="isAtiva" v-model="checks"
                              :disabled="form.slctdControleFrequencia.data === null"/>
                    <label for="isAtiva">Ativa</label>
                </div>
            </div>
        </div>

        <br>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                    <span class="p-float-label">
                        <Textarea v-model="form.observacao.value"
                                  :disabled="form.observacao.disabled"
                                  rows="5" cols="5" />
                        <label>{{ form.observacao.label }}</label>
                    </span>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-3">
            </div>
            <div class="field col-12 md:col-3">
                <Button class="p-button" label="Salvar"
                        :disabled="form.curso.nome.value == null ||
                                form.nome.value == null ||
                                form.slctdTurno.data == null ||
                                form.regimeMatricula.nome.value == null ||
                                form.etapaInicial.nome.value == null ||
                                form.etapaFinal.nome.value == null ||
                                form.slctdFrequencia.data == null ||
                                form.slctdControleFrequencia.data == null"
                        @click.prevent="salvar"
                ></Button>
            </div>
            <div class="field col-12 md:col-3" v-if="!props.secretaria && props.base === null">
                <Button class="p-button" label="Importar Base"
                        @click.prevent="pesquisaBasesSecretaria"
                ></Button>
            </div>
            <div class="field col-12 md:col-3" v-if="props.base != null">
                <Button class="p-button" label="Replicar esta Base"
                        @click.prevent="replicarBase"
                ></Button>
            </div>
            <div class="field col-12 md:col-3">
            </div>
        </div>
    </div>
    <Dialog v-model:visible="dialogReplica" :closable="false" modal header="Replicar Base" :style="{ width: '800px' }">
        <br><br>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText
                        id="nome"
                        v-model="form.nome.value"
                        :required="form.nome.required"
                        :disabled="form.nome.disabled"  @keydown="liberaCampos"/>
                    <label for="">{{ form.nome.label }}</label>
                </span>
            </div>
        </div>
        <div class="field col-12 md:col-12">
            <div class="p-inputgroup flex-1">
                     <span class="p-float-label pr-2">
                        <InputNumber inputId="regimeMatricula" :useGrouping="false" @focusout="buscaRegimeMatricula"
                                     style="width: 20px"
                                     v-model="form.regimeMatricula.codigo.value"
                                     :required="form.regimeMatricula.codigo.required"
                                     :disabled="form.regimeMatricula.codigo.disabled"/>
                        <label for="">{{ form.regimeMatricula.codigo.label }}</label>
                    </span>
                <span class="p-float-label">
                        <InputText
                            style="width: 600px"
                            id="nomeCurso"
                            v-model="form.regimeMatricula.nome.value"
                            :required="form.regimeMatricula.nome.required"
                            :disabled="form.regimeMatricula.nome.disabled"/>
                        <label for="">{{ form.regimeMatricula.nome.label }}</label>
                    </span>
                <Button @click="pesquisaRegimesMatricula" :disabled="form.regimeMatricula.codigo.disabled" icon="pi pi-search" aria-label="Filter" style="width: 400px"/>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-4 col-offset-2">
                <Button class="p-button" label="Replicar"
                        :disabled="form.curso.nome.value == null ||
                                form.nome.value == null ||
                                form.slctdTurno.data == null ||
                                form.regimeMatricula.nome.value == null ||
                                form.etapaInicial.nome.value == null ||
                                form.etapaFinal.nome.value == null ||
                                form.slctdFrequencia.data == null ||
                                form.slctdControleFrequencia.data == null"
                        @click.prevent="salvar"
                ></Button>
            </div>
            <div class="field col-12 md:col-4">
                <Button class="p-button" label="Cancelar"
                        @click.prevent="cancelaReplica"
                ></Button>
            </div>
        </div>
    </Dialog>


    <DialogPesquisaEducacao  v-if="renderDialog" ref="funcPesquisa"
                             v-on:select-line="parametrosDialogPesquisa.callback"
                             :title='parametrosDialogPesquisa.titulo'
                             :route='parametrosDialogPesquisa.rota'
                             :fields-display="parametrosDialogPesquisa.camposExibir"></DialogPesquisaEducacao>

    <ModalLoading :isLoading="loading"></ModalLoading>
</template>

<style scoped>
    :deep(.p-fieldset-legend) {
        padding: 0!important;
        border: none;
        background: transparent!important;
    }
    :deep(.p-fieldset-legend-text) {
        color: #a19f9d!important;
    }
    :deep(.p-fieldset) {
        background: #e1dede;
    }
    i {
        cursor: pointer
    }
</style>
