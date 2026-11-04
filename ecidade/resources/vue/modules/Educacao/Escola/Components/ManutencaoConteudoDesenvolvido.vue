<script setup>
import { ref } from "vue";
import {useToast} from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from "primevue/confirmpopup";
import ModalLoading from "../../../Components/ModalLoading.vue";
const props = defineProps(['visible', 'turmas', 'usuario'])
const emits = defineEmits(['update:visible', 'editar', 'close'])

const loading = ref(false)
const toast = useToast()
const confirm = useConfirm()
const optionsTurno = ref(null)
const optionsReferencia = ref([])
const optionsEtapa = ref(null)
const optionsRegencia = ref([])
const expandedRows = ref([]);
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
    buscarConteudos: `v4/api/educacao/escola/diario-classe/registro-aula/get-conteudos-regente-escola`,
    excluir: `v4/api/educacao/escola/diario-classe/registro-aula/excluir-conteudo/{codigo}`
}
const getConteudosRegenteEscola = async (e, pagina = 1, rows = 5) => {
    let parametros = {}
    parametros.page = pagina
    parametros.items = rows
    parametros.usuario = props.usuario

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
}

const editar = (data) => {
    emits('editar', data)
    visivel.value = false
}

const excluir = async (codigo) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Deseja mesmo excluir?',
        icon: 'pi pi-exclamation-triangle',
        accept: async () => {
            try {
                loading.value = true
                let rota = routes.excluir.replace('{codigo}', codigo);
                await window.axios.delete(rota)
                loading.value = false
                toast.add({
                    severity: 'success',
                    summary: 'Sucesso!',
                    detail: 'Excluído com Sucesso!',
                    life: 5000
                });
                getConteudosRegenteEscola()
            } catch (e) {
                loading.value = false
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: e.response.data.message,
                    life: 5000
                });
            }
        },
        reject: () => {
            return
        }
    });
}

getConteudosRegenteEscola()
</script>

<template>
    <ConfirmPopup></ConfirmPopup>
    <Dialog :visible="visivel" modal header="Manutenção de Conteúdos Desenvolvidos" @update:visible="value => emits('update:visible', value)" @close="value => emits('close', value)" class="p-dialog-maximized">
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

        <DataTable :value="conteudos"
                   class="mt-5"
                   tableStyle="min-width: 50rem"
                   :rows-per-page-options="[5, 10, 15, 20]"
                   lazy
                   v-model:expandedRows="expandedRows"
                   paginator
                   :rows="5"
                   :totalRecords="conteudos[0] === undefined ? 0 : conteudos[0].totalRegistros"
                   @page="({page, rows}) => getConteudosRegenteEscola(event, page + 1, rows)">
            <Column expander header="Abrir Conteúdo" style="width: 10%" />
            <Column field="disciplina.nome" header="Regência" style="width: 20%"></Column>
            <Column field="turma.nome" header="Turma" style="width: 15%"></Column>
            <Column  header="Turno / Referência" style="width: 15%">
                <template #body="{ data }">
                    {{ data.turno.nome }} / {{ data.turnoReferente.name }}
                </template>
            </Column>
            <Column field="tipoInstrumento" header="Tipo Instrumento" style="width: 15%">
                <template #body="{ data }">
                    {{ data.tipoInstrumento == null ? 'Não Aplicável' : data.tipoInstrumento.nome }}
                </template>
            </Column>
            <Column field="data" header="Data" style="width: 15%">
                <template #body="{ data }">
                    {{ (new Date(data.data)).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) }}
                </template>
            </Column>
            <Column header="Ações" style="width: 10%">
                <template #body="{ data }">
                    <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)"></i>
                    <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(data['codigo'])"></i>
                </template>
            </Column>
            <template #expansion="slotProps">
                <div class="p-3">
                    <h3>{{ slotProps.data.conteudo }}</h3>
                </div>
            </template>
        </DataTable>
    </Dialog>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>

</style>
