<script setup>
import { onMounted, ref } from "vue";
import {useToast} from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from "primevue/confirmpopup";
import ModalLoading from "../../../../Components/ModalLoading.vue";
const props = defineProps(['escola', 'user'])

const loading = ref(false)
const toast = useToast()
const confirm = useConfirm()
const usuario = ref()
const turmas = ref()
const optionsTurno = ref(null)
const optionsReferencia = ref([])
const optionsEtapa = ref(null)
const optionsRegencia = ref([])
const conteudos = ref([])
const visivel = ref(props.visible)
const form = ref({
    codigo: null,
    slctdTurma: {
        data: null,
        label: 'Turma',
        disabled: false
    },
    slctdEtapa: {
        data: null,
        label: 'Etapa',
        disabled: false
    },
    slctdTipoInstrumento: {
        data: null,
        label: 'Tipo de Insturmento Avaliativo',
        disabled: false
    },
    slctdRegencia: {
        data: null,
        label: 'Regência',
        disabled: false
    },
    slctdTurno: {
        data: null,
        label: 'Turno',
        disabled: false
    },
    slctdReferencia: {
        data: null,
        label: 'Referência',
        disabled: false
    },
    data: {
        disabled: false,
        value: null,
        label: 'Data'
    }
})
const routes = {
    dadosRegente: `v4/api/educacao/escola/diario-classe/registro-aula/get-dados-regente`,
    buscarConteudos: `v4/api/educacao/escola/diario-classe/registro-aula/get-conteudos-regente-escola`,
    salarLote: `v4/api/educacao/escola/diario-classe/registro-aula/update-conteudos-lote`
}

const getDadosRegente = async (escola = props.escola, user = props.user) => {
    try {
        loading.value = true
        usuario.value = (await window.axios.get(`${routes.dadosRegente}/${escola}/${user}?periodos`)).data.data
        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }

    turmas.value = usuario.value.turmas
}
const getConteudosRegenteEscola = async (e, pagina = 1, rows = 10) => {
    let parametros = {}
    parametros.page = pagina
    parametros.items = rows
    parametros.usuario = props.user

    if (form.value.slctdTurma.data !== null) {
        parametros.turma = form.value.slctdTurma.data.codigo
    }
    if (form.value.slctdEtapa.data !== null) {
        parametros.etapa = form.value.slctdEtapa.data.codigo
    }
    if (form.value.slctdRegencia.data !== null) {
        parametros.regencia = form.value.slctdRegencia.data.code
    }
    if (form.value.slctdTurno.data !== null) {
        parametros.turno = form.value.slctdTurno.data.codigo
    }
    if (form.value.slctdReferencia.data !== null) {
        parametros.turnoReferente =  form.value.slctdReferencia.data.codigo
    }
    if (form.value.data.value !== null) {
        parametros.data =  form.value.data.value.toLocaleDateString('en-US', {timeZone: 'UTC'});
    }

    parametros.regenciaAtiva = true
    const urlParams = new URLSearchParams(parametros)
    try {
        loading.value = true
        conteudos.value = (await window.axios.get(`${routes.buscarConteudos}?${urlParams.toString()}`)).data.data
        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}
const setaEtapaTurno = () => {
    optionsEtapa.value = form.value.slctdTurma.data.etapas
    if (optionsEtapa.value.length === 1) {
        form.value.slctdEtapa.data = optionsEtapa.value[0]
        setaRegencia()
    }
    optionsTurno.value = [form.value.slctdTurma.data.turno]
    optionsReferencia.value = form.value.slctdTurma.data.turnosReferentes
}

const salvar = async () => {
    let parametros = {}
    parametros.conteudos = conteudos.value.map(conteudo => {
        return {codigo: conteudo.codigo, aulasDadas: conteudo.aulasDadas}
    })
    try {
        loading.value = true
        let resposta = (await window.axios.post(routes.salarLote, parametros)).data
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: `${resposta.message}`,
            life: 5000
        });
        await getConteudosRegenteEscola()
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const limpaCampos = () => {
    form.value.codigo = null
    form.value.slctdTurma.data = null
    form.value.slctdRegencia.data = null
    form.value.slctdEtapa.data = null
    form.value.slctdTurno.data = null
    form.value.slctdReferencia.data = null
    form.value.data.value = null
}

const setaRegencia = () => {
    optionsRegencia.value = form.value.slctdEtapa.data.regencias.map(regencia => {
        return {code: regencia.codigo, nome: regencia.disciplina.disciplina.nome}
    })

    if (optionsRegencia.value.length === 1) {
        form.value.slctdRegencia.data = optionsRegencia.value[0]
    }
}

const onCellEditComplete = (event) => {
    let { field, newValue, index } = event;
    conteudos.value[index][field] = newValue
}

onMounted(async () => {
    await getDadosRegente()
})

</script>

<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container mt-5">
        <div class="p-fluid grid">
            <div class="field col">
            <span class="p-float-label">
                <Dropdown id="turma" optionLabel="nome" :options="turmas"
                          :disabled="form.slctdTurma.disabled"
                          v-model="form.slctdTurma.data" @change="setaEtapaTurno"
                />
                <label for="">{{ form.slctdTurma.label }}</label>
            </span>
            </div>
            <div class="field col">
                <span class="p-float-label">
                    <Dropdown id="etapa" optionLabel="nome" :options="optionsEtapa"
                              v-model="form.slctdEtapa.data"
                              :disabled="form.slctdEtapa.disabled"
                              @change="setaRegencia"
                    />
                    <label for="">{{ form.slctdEtapa.label }}</label>
                </span>
            </div>
            <div class="field col">
                <span class="p-float-label">
                    <Dropdown id="regencia" optionLabel="nome" :options="optionsRegencia"
                              :disabled="form.slctdRegencia.disabled"
                              v-model="form.slctdRegencia.data"
                    />
                    <label for="">{{ form.slctdRegencia.label }}</label>
                </span>
            </div>
            <div class="field col">
                <span class="p-float-label">
                    <Calendar inputId="dateformat"
                              v-model="form.data.value"
                              :disabled="form.data.disabled"
                              dateFormat="dd-mm-yy"/>
                    <label for="">{{ form.data.label }}</label>
                </span>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-2 col-offset-4">
                <Button class="p-button" label="Buscar" icon="pi pi-search"
                        @click="getConteudosRegenteEscola"
                ></Button>
            </div>
            <div class="field col-12 md:col-2">
                <Button class="p-button" label="Limpar Filtros" icon="pi pi-eraser"
                        @click="limpaCampos"
                ></Button>
            </div>
        </div>
    </section>
    <section style="width: 1000px; margin: 0 auto">
        <Message severity="info" :closable="false" :style="{marginTop:0}">
            <div style='font-size: 10pt; '>
                Para editar as aulas dadas clique sobre o numero de aulas dadas de cada linha na tabela,
                altere o valor e clique enter. Em seguida clique em salvar lista para atualizar todos os registros em exibição.
            </div>
        </Message>
        <DataTable :value="conteudos"
                   editMode="cell"
                   @cell-edit-complete="onCellEditComplete"
                   tableClass="editable-cells-table mt-5"
                   tableStyle="min-width: 50rem"
                   :rows-per-page-options="[5, 10, 15, 20]"
                   lazy
                   paginator
                   :rows="10"
                   :totalRecords="conteudos[0] === undefined ? 0 : conteudos[0].totalRegistros"
                   @page="({page, rows}) => getConteudosRegenteEscola(event, page + 1, rows)">
            <Column field="disciplina.nome" header="Regência" style="width: 20%"></Column>
            <Column field="data" header="Data" style="width: 15%">
                <template #body="{ data }">
                    {{ (new Date(data.data)).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) }}
                </template>
            </Column>
            <Column field="aulasDadas" header="Aulas Dadas" style="width: 15%">
                <template #editor="{ data, field }" data-p-cell-editing="true">
                    <InputNumber v-model="data[field]" autofocus />
                </template>
            </Column>
        </DataTable>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-2 col-offset-5 mt-5">
                <Button class="p-button" label="Salvar Lista" icon="pi pi-save"
                        @click="salvar"
                ></Button>
            </div>
        </div>
    </section>
    <ModalLoading :isLoading="loading"/>
</template>

<style lang="scss" scoped>
    ::v-deep(.editable-cells-table td.p-cell-editing) {
        padding-top: 0;
        padding-bottom: 0;
    }
</style>
