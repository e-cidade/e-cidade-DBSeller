<script setup>
import { ref } from "vue";
import { useToast } from "primevue/usetoast"
import ModalLoading from "../../../../Components/ModalLoading.vue";
import MultiDownload from "../../../../Components/MultiDownload.vue";
import DialogPesquisaEducacao from "../../../Components/DialogPesquisaEducacao.vue";
import Textarea from "primevue/textarea";
const props = defineProps(['escola'])
const windowUrl = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g)[0]
const routes = {
    calendarios: `v4/api/educacao/escola/${props.escola}/calendario`,
    diretores: `v4/api/educacao/escola/${props.escola}/diretores`,
    turmas: `v4/api/educacao/escola/turmas-por-calendario`,
    disciplinas: `v4/api/educacao/escola/turma`,
    alunos: `v4/api/educacao/escola/alunos/alunos-por-turma`,
    profissionais: `v4/api/educacao/escola/${props.escola}/profissionais`,
    secretarios: `v4/api/educacao/escola/secretarios/${props.escola}`,
    emissao: `${windowUrl}/edu2_fichamatricula002.php?`
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
    slctTurmas: {
        data: null,
        label: 'Turmas',
        required: false
    },
    slctDiretores: {
        data: null,
        label: 'Diretor(a)',
        required: false
    },
    dataEmissao: {
        data: null,
        label: 'Data de Deferimento',
        required: false
    },
    checkAssDiretor: {
        label: 'Exibe Nome do Diretor',
        required: false
    },
    checkAssResponsavel: {
        label: 'Exibe Nome do Responsável',
        required: false
    },
    checkImpRematricula: {
        label: 'Imprime Página de Rematrícula',
        required: false
    },
    textAreaObservacao: {
        label: 'Observação Geral',
        data: '',
        maxCaracteres: 800
    },
    checks: null,
    alunos: [[], []],
    dataLabel: {
        label: 'Inserir data de emissão:'
    }
});

var caracteresRestantes = form.value.textAreaObservacao.maxCaracteres;
/********************** MaxLength do TextArea **********************/
/* O maxLenght do TextArea é definido com o valor da propriedade   */
/* maxCaracteres do form.value.textAreaObservacao. No entanto, ao  */
/* deixar o valor fixo no maxLength do TextArea ele não responde   */
/* conforme o cálculo do totalLength da função limitChars(). Pois  */
/* ele não espera que cada quebra de linha corresponda a -30 chars */
/* conforme implementado na função. Para fazer ele corresponder a  */
/* esse cálculo, é criado uma variável maxCaracText, que irá       */
/* iniciar com o valor da propriedade maxCaracteres e atualizar seu*/
/* valor, quando o cálculo de totalLength chegar ao atribuido na   */
/* variável maxCaracteres.                                         */
/*******************************************************************/
var maxCaracText = form.value.textAreaObservacao.maxCaracteres;
function limitChars() {
    maxCaracText = form.value.textAreaObservacao.maxCaracteres;
    const maxLength = form.value.textAreaObservacao.maxCaracteres;
    let currentText = form.value.textAreaObservacao.data;
    let lineBreak = 0;

    for (let i = 0; i <= currentText.length; i++) {
        if (currentText[i] === '\n') {
            lineBreak ++;
        }
    }

    let totalLength = currentText.length + lineBreak * 30;
    caracteresRestantes = maxLength - totalLength;

    if (totalLength >= maxLength) {
        form.value.textAreaObservacao.data = currentText.slice(0, maxLength - lineBreak * 30);
        caracteresRestantes = 0;
        maxCaracText = currentText.length;
    }
}

const optionsCalendarios = ref(null)
const optionsTurmas = ref(null)
const optionsDiretores = ref(null)

async function buscaCalendarios() {
    try {
        loading.value = true
        await window.axios.get(routes.calendarios).then(retorno => {
            optionsCalendarios.value = retorno.data.data.map(calendario => {
                return {
                    name: calendario.nome,
                    code: calendario.codigo
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

async function buscaTurmas() {
    try {
        loading.value = true
        await window.axios.get(`${routes.turmas}/${form.value.slctCalendarios.data.code}`).then(retorno => {
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

async function buscaAlunos() {
    form.value.alunos = []
    try {
        loading.value = true
        await window.axios.get(`${routes.alunos}/${form.value.slctTurmas.data.code}`).then(retorno => {
            form.value.alunos.push(retorno.data.data)
            form.value.alunos.push([])
        })
        loading.value = false
        await buscaDiretores()
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

async function buscaDiretores() {
    try {
        loading.value = true
        await window.axios.get(routes.diretores).then(retorno => {
            optionsDiretores.value = retorno.data.data.map(diretor => {
                return {
                    name: diretor.nome,
                    code: diretor.codigo_rechumano
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

function openModal() {
    toast.add({
        severity: 'warn',
        summary: 'Atenção',
        detail: `Nenhum Aluno encontrado para esses filtros.`,
        life: 5000
    });
}

function emitir() {

    if (form.value.slctDiretores.data === null && (form.value.checks !== null && form.value.checks.includes('checkAssDiretor'))) {
        toast.add({
           severity: 'warn',
           summary: 'Atenção',
           detail: 'Por favor, selecione um diretor.',
           life: 10000
        });
        return false;
    }

    let turma = form.value.slctTurmas.data.code
    let alunos = form.value.alunos[1].map(aluno => aluno.matricula)
    alunos = alunos.join()
    let observacao = form.value.textAreaObservacao.data === null ? '' : form.value.textAreaObservacao.data
    observacao = observacao.replace(/\n/g, '<br>');
    let checkAssDiretor = form.value.checks === null ? false : form.value.checks.includes('checkAssDiretor')
    let checkAssResponsavel = form.value.checks === null ? false : form.value.checks.includes('checkAssResponsavel')
    let checkImpRematricula = form.value.checks === null ? false : form.value.checks.includes('checkImpRematricula')
    let dataEmissao = form.value.dataEmissao.data === null ? '' : form.value.dataEmissao.data.toLocaleDateString()

    let parametros = [
        `calendario=${form.value.slctCalendarios.data.code}`,
        `turma=${turma}`,
        `alunos=${alunos}`,
        `diretor= ${form.value.slctDiretores.data === null ? '' : form.value.slctDiretores.data.name}`,
        `lExibeAssinaturaDiretor=${checkAssDiretor}`,
        `lExibeAssinaturaResponsavel=${checkAssResponsavel}`,
        `lImprimeRematricula=${checkImpRematricula}`,
        `iDataEmissao=${dataEmissao}`,
        `sObs=${observacao}`
    ]

    let rota = routes.emissao;
    download.value.addFile(
        `${rota}${parametros.join('&')}`,
        'Ficha de Matrícula'
    )
    download.value.openModal();
}
buscaCalendarios()
</script>

<template>
    <section class="container">
        <Panel header="Ficha de Matrícula">
            <div class="card">
                <br>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2">
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctCalendarios.data"
                                      :options="optionsCalendarios" optionLabel="name" @change="buscaTurmas"/>
                            <label for="dropdown">{{ form.slctCalendarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctTurmas.data" :options="optionsTurmas"
                                      optionLabel="name" @change="buscaAlunos" />
                            <label for="dropdown">{{ form.slctTurmas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-2">
                    </div>
                </div>
            </div>
            <div>
                <PickList id="list" v-model="form.alunos" listStyle="height:200px" dataKey="codigo">
                    <template #item="slotProps">
                        <div class="alunos-item">
                            <div class="alunos-list-detail">
                                <li class="">
                                    <small>
                                    {{ slotProps.item.nome }} - {{ slotProps.item.codigo }}
                                    </small>
                                </li>
                            </div>
                        </div>
                    </template>
                </PickList>
            </div>
            <div class="container">
                <div class="p-fluid grid mt-4 justify-content-evenly align-items-start">
                    <div class="field col-8 md:col-4">
                        <div class="field-checkbox align-items-end">
                            <Checkbox inputId="prof" name="prof" value="checkAssDiretor" v-model="form.checks" />
                            <label for="prof">{{ form.checkAssDiretor.label }}</label>
                        </div>
                        <div class="field-checkbox align-items-end">
                            <Checkbox inputId="resp" name="resp" value="checkAssResponsavel" v-model="form.checks" />
                            <label for="resp">{{ form.checkAssResponsavel.label }}</label>
                        </div>
                        <div class="field-checkbox align-items-end">
                            <Checkbox inputId="resp" name="resp" value="checkImpRematricula" v-model="form.checks" />
                            <label for="resp">{{ form.checkImpRematricula.label }}</label>
                        </div>
                    </div>
                    <div class="p-fluid">
                        <div class="field col-16">
                            <span class="p-float-label mb-5">
                                <Dropdown id="dropdown" v-model="form.slctDiretores.data"
                                          :options="optionsDiretores" optionLabel="name"/>
                                <label for="dropdown">{{ form.slctDiretores.label }}</label>
                            </span>
                            <span class="p-float-label">
                                <Calendar inputId="dateformat"
                                          v-model="form.dataEmissao.data"
                                          dateFormat="dd/mm/yy"/>
                                <label for="dateformat">{{ form.dataEmissao.label }}</label>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-12">
                        <p style="font-size: 12px; display: flex; justify-content: flex-end;">Caracteres restantes: <span style="font-weight: bold">{{ caracteresRestantes }}</span></p>
                        <span class="p-float-label">
                            <Textarea :maxlength="maxCaracText"
                                      v-model="form.textAreaObservacao.data"
                                      @input="limitChars"
                                      rows="7"
                                      cols="30"/>
                            <label>{{ form.textAreaObservacao.label }}</label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-5"></div>
                <div class="field col-12 md:col-2">
                    <Button class="p-button" icon="pi pi-print" label="Imprimir"
                            :disabled="(form.alunos[1] === undefined || form.alunos[1].length === 0) ||
                            form.alunos[1].length === 0" @click="emitir"></Button>
                </div>
                <div class="field col-12 md:col-5"></div>
            </div>
            <div class="text-center">
                Para selecionar mais de um aluno mantenha pressionada a tecla <kbd>CTRL</kbd> e clique sobre o nome dos
                alunos.
            </div>
        </Panel>
        <ModalLoading :isLoading="loading"/>
        <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
    </section>
</template>
<style scoped>
label {
    color: #4e4d4c;
}
i {
    cursor: pointer
}
</style>
