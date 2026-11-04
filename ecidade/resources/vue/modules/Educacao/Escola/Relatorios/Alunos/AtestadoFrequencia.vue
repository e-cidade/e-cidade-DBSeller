<script setup>
import { ref } from "vue";
import { useToast } from "primevue/usetoast"
import ModalLoading from "../../../../Components/ModalLoading.vue";
import MultiDownload from "../../../../Components/MultiDownload.vue";
const props = defineProps(['departamento', 'modulo'])
const windowUrl = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g)[0]

const isSecretaria = ref(null)
isSecretaria.value = props.modulo == 7159
const routes = {
    escolas: `v4/api/educacao/escola/`,
    calendarios: `v4/api/educacao/escola/{escola}/calendario`,
    turmas: `v4/api/educacao/escola/turmas-por-calendario`,
    disciplinas: `v4/api/educacao/escola/turma`,
    periodos: `v4/api/educacao/escola/periodos-por-calendario`,
    alunos: `v4/api/educacao/escola/alunos/alunos-por-turma`,
    secretarios: `v4/api/educacao/escola/secretarios/{escola}`,
    diretores: `v4/api/educacao/escola/{escola}/diretores`,
    emissao: `${windowUrl}/edu2_atestadofrequencia002.php?`
}
const download = ref(null)
const loading = ref(false)
const toast = useToast()

const form = ref({
    slctCalendarios: {
        data: null,
        label: 'Calendário',
        required: false
    },
    slctEscolas: {
        data: null,
        label: 'Escola',
        required: false
    },
    slctTurmas: {
        data: null,
        label: 'Turmas',
        required: false
    },
    slctEmissores: {
        label: 'Emissor',
        required: false,
        data: null
    },
    slctExibeGrade: {
        label: 'Exibe Grade de Horários',
        data: null,
        required: true
    },
    slctExibePercentualFrequencia: {
        label: 'Exibe % Frequência',
        data: null,
        required: true
    },
    slctPeriodos: {
        label: 'Períodos',
        required: false,
        data: null
    },
    textAreaObservacao: {
        label: 'Observação Geral',
        data: null,
        required: false
    },
    alunos: [[], []]
})

const optionsCalendarios = ref(null)
const optionsEscolas = ref(null)
const optionsTurmas = ref(null)
const optionsEmissores = ref([])
const optionsExibeGrade = ref([
    { name: 'SIM', code: 'S' },
    { name: 'NÃO', code: 'N' }
])
const optionsExibePercentualFrequencia = ref([
    { name: 'NÃO', code: 'N' },
    { name: 'SIM', code: 'S' }
])
const optionsPeriodos = ref([])


function buscaCalendarios() {
        optionsTurmas.value = null;
    buscaEmissores()
    let school = isSecretaria.value ? form.value.slctEscolas.data.code : props.departamento;
    let rota = routes.calendarios.replace('{escola}', school);
        try {
        loading.value = true
        window.axios.get(rota).then(retorno => {
            optionsCalendarios.value = retorno.data.data.map(calendario => {
                return {
                    name: calendario.nome,
                    code: calendario.codigo
                }
            })
        })
        loading.value = false
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

function buscaEscolas() {
    try {
        loading.value = true
        window.axios.get(routes.escolas).then(retorno => {
            optionsEscolas.value = retorno.data.data.map(escola => {
                return {
                    name: escola.ed18_c_nome,
                    code: escola.ed18_i_codigo
                }
            })
        })
        loading.value = false
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


function buscaTurmas() {
    try {
        loading.value = true
        window.axios.get(`${routes.turmas}/${form.value.slctCalendarios.data.code}`).then(retorno => {
            optionsTurmas.value = retorno.data.data.map(turma => {
                return {
                    name: turma.ed57_c_descr,
                    code: turma.ed57_i_codigo,
                    temParecer: turma.temFormaAvaliacaoParecer
                }
            })
            loading.value = false
        })
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

async function buscaEmissores() {
    optionsEmissores.value = [];
    await buscaSecretarios();
    await buscaDiretores();
}

const buscaSecretarios = async () => {
    try {
        loading.value = true
        let school = isSecretaria.value ? form.value.slctEscolas.data.code : props.departamento
        let rota = routes.secretarios.replace('{escola}', school);
        (await window.axios.get(rota)).data.data.forEach(secretario => {
            optionsEmissores.value.push({
                name: `SECRETÁRIO(A) - ${secretario.nome}`,
                code: `SECRETÁRIO(A)|${secretario.nome}`
            })
        })
        loading.value = false
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

const buscaDiretores = async () => {
    try {
        loading.value = true
        let school = isSecretaria.value ? form.value.slctEscolas.data.code : props.departamento
        let rota = routes.diretores.replace('{escola}', school);
        (await window.axios.get(rota)).data.data.forEach(diretor => {
            optionsEmissores.value.push({
                name: `DIRETOR(A) - ${diretor.nome}`,
                code: `DIRETOR(A)|${diretor.nome}|${diretor.descricao_tipo_ato_legal}`
            })
        })
        loading.value = false
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
async function buscaAlunos() {
    form.value.alunos = []
    try {
        loading.value = true
        window.axios.get(`${routes.alunos}/${form.value.slctTurmas.data.code}`).then(retorno => {
            form.value.alunos.push(retorno.data.data)
            form.value.alunos.push([])
        })
        loading.value = false
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

function buscaPeriodos() {
    optionsPeriodos.value = [];
    optionsPeriodos.value.push({ name: 'TODOS', code: '' });
    try {
        loading.value = true
        window.axios.get(`${routes.periodos}/${form.value.slctCalendarios.data.code}`).then(retorno => {
            retorno.data.data.forEach(periodo => {
                optionsPeriodos.value.push({
                    name: periodo.ed09_c_descr,
                    code: periodo.ed09_i_codigo
                })
            })
        })

        loading.value = false
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

function hideMultiDownload() {
    window.location.reload()
}

function mostrarPeriodos() {
    divPeriodo.style = 'display: none';
    if (form.value.slctExibePercentualFrequencia.data.code == 'S') {
        divPeriodo.style = '';
    }
}

function emitir() {
    var unfilledFields = 0;
    Object.values(form.value).map(element => {
        if (element.required) {
            if (element.data == null || element.data.length == 0 || element.data.code == null) {
                unfilledFields++;
                toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: `Campo ${element.label} deve ser preenchido!`,
                    life: 4000
                });
            }
        }
    })
    if (unfilledFields > 0) {
        return;
    }
    let alunos = form.value.alunos[1].map(aluno => {
        return {
            iMatricula: aluno.matricula,
            sAluno: aluno.nome.trim()
        }
    })
    let turma = form.value.slctTurmas.data.code
    let emissor = form.value.slctEmissores.data === null ? '' : form.value.slctEmissores.data.code
    let exibeGrade = form.value.slctExibeGrade.data.code
    let exibePercentualFrequencia = form.value.slctExibePercentualFrequencia.data.code
    let periodo = form.value.slctPeriodos.data == null ? '' : form.value.slctPeriodos.data.code
    let observacao = form.value.textAreaObservacao.data === null ? '' : form.value.textAreaObservacao.data
    let parametros = [
        `aMatriculas=${JSON.stringify(alunos)}`,
        `iTurma=${turma}`,
        `sDiretor=${emissor}`,
        `lExibeGradeAluno=${exibeGrade}`,
        `lExibePercentualFrequencia=${exibePercentualFrequencia}`,
        `calendario=${form.value.slctCalendarios.data.code}`,
        `periodo=${periodo}`,
        `sObservacao=${observacao}`
    ]

    download.value.addFile(
        `${routes.emissao}${parametros.join('&')}`,
        'Atestado de Frequência'
    )
    download.value.openModal();
}

if (isSecretaria.value) {
    buscaEscolas()
} else {
    buscaCalendarios()
}
</script>

<template>
    <section class="container">
        <Panel header="Atestado de Frequência">
            <div class="card">
                <br>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2" :class="{ 'field col-12 md:col-4': isSecretaria }">
                        <span class="p-float-label" v-show="isSecretaria">
                            <Dropdown id="dropdown" v-model="form.slctEscolas.data" :options="optionsEscolas" optionLabel="name" @change="buscaCalendarios" />
                            <label for="dropdown">{{ form.slctEscolas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctCalendarios.data" :options="optionsCalendarios" optionLabel="name" @change="buscaTurmas(); buscaPeriodos()" />
                            <label for="dropdown">{{ form.slctCalendarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctTurmas.data" :options="optionsTurmas" optionLabel="name" @change="buscaAlunos" />
                            <label for="dropdown">{{ form.slctTurmas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-2" v-show="!isSecretaria">
                    </div>
                </div>
            </div>
            <div>
                <PickList id="list" v-model="form.alunos" listStyle="height:200px" dataKey="codigo">
                    <template #item="slotProps">
                        <div class="alunos-item">
                            <div class="alunos-list-detail">
                                <li class=""><small>
                                        {{ slotProps.item.nome }} - {{ slotProps.item.codigo }}
                                    </small></li>
                            </div>
                        </div>
                    </template>
                </PickList>
            </div>
            <br><br>

            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2"></div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="secretarios" :options="optionsEmissores" optionLabel="name" v-model="form.slctEmissores.data" />
                            <label for="">{{ form.slctEmissores.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="grade" :options="optionsExibeGrade" optionLabel="name" v-model="form.slctExibeGrade.data" />
                            <label for="" :class="{ 'required': form.slctExibeGrade.required }">{{ form.slctExibeGrade.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-2"></div>
                </div>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2"></div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="exibePercentualFrequencia" :options="optionsExibePercentualFrequencia" optionLabel="name" v-model="form.slctExibePercentualFrequencia.data"
                                @change="mostrarPeriodos" />
                            <label for="">{{ form.slctExibePercentualFrequencia.label }}</label>
                        </span>
                    </div>

                    <div class="field col-12 md:col-4" id="divPeriodo" style='display: none'>
                        <span class="p-float-label">
                            <Dropdown id="periodos" :options="optionsPeriodos" optionLabel="name" v-model="form.slctPeriodos.data" />
                            <label for="" :class="{ 'required': form.slctPeriodos.required }">{{ form.slctPeriodos.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-2"></div>
                </div>
            </div>

            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-12">
                        <span class="p-float-label">
                            <Textarea v-model="form.textAreaObservacao.data" rows="5" cols="5" />
                            <label>{{ form.textAreaObservacao.label }}</label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-5"></div>
                <div class="field col-12 md:col-2">
                    <Button class="p-button" icon="pi pi-print" label="Imprimir" :disabled="(form.alunos[1] === undefined || form.alunos[1].length === 0) ||
                        form.alunos[1].length === 0
                        " @click="emitir"></Button>
                </div>
                <div class="field col-12 md:col-5"></div>
            </div>
            <div class="text-center">
                Para selecionar mais de um aluno mantenha pressionada a tecla <kbd>CTRL</kbd> e clique sobre o nome dos
                alunos.
            </div>
        </Panel>
        <ModalLoading :isLoading="loading" message="'Carregando Dados...'"/>
        <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download" />
    </section>
</template>
<style scoped>
label {
    color: #605e5c;
}

i {
    cursor: pointer
}
</style>
