<script setup>
import { ref, onMounted } from "vue";
import {useToast} from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmPopup from "primevue/confirmpopup";
import ModalLoading from "../../../Components/ModalLoading.vue";
const props = defineProps(['visible', 'escola'])
const emits = defineEmits(['update:visible', 'selectLine'])

const loading = ref(false)
const toast = useToast()
const confirm = useConfirm()

const visivel = ref(props.visible)
const regentes = ref([])
const disciplinas = ref()
const form = ref({
    nome: {
        value: null,
        label: 'Nome',
        disabled: false
    },
    cgm: {
        value: null,
        disabled: false,
        label: 'CGM'
    },
    cpf: {
        value: null,
        disabled: false,
        label: 'CPF'
    },
    matricula: {
        value: null,
        disabled: false,
        label: 'Matricula'
    },
    slctdDisciplina: {
        data: null,
        disabled: false,
        label: 'Disciplina'
    }
})
const routes = {
    buscaRegentes: `v4/api/educacao/escola/${props.escola}/profissionais`,
    disciplinas: `v4/api/educacao/secretaria/disciplinas`
}
const getRegentesEscola = async (e, pagina = 1, rows = 5) => {
    let parametros = {}
    parametros.page = pagina
    parametros.items = rows
    parametros.regente = true

    if (form.value.nome.value !== null && form.value.nome.value !== '') {
        parametros.nome = form.value.nome.value
    }
    if (form.value.cgm.value !== null && form.value.cgm.value !== '') {
        parametros.cgm = form.value.cgm.value
    }
    if (form.value.cpf.value !== null && form.value.cpf.value !== '') {
        parametros.cpf = form.value.cpf.value
    }
    if (form.value.matricula.value !== null && form.value.matricula.value !== '') {
        parametros.matricula = form.value.matricula.value
    }
    if (form.value.slctdDisciplina.data !== null && form.value.slctdDisciplina.data !== '') {
        parametros.disciplina = form.value.slctdDisciplina.data.codigo
    }

    const urlParams = new URLSearchParams(parametros)
    loading.value = true
    regentes.value = (await window.axios.get(`${routes.buscaRegentes}?${urlParams.toString()}`)).data.data
    loading.value = false
}
const limpaCampos = () => {
    form.value.nome.value = null
    form.value.cgm.value = null
    form.value.cpf.value = null
    form.value.matricula.value = null
    form.value.slctdDisciplina.data = null
}

const getDisciplinas = async () => {
    disciplinas.value = (await window.axios.get(routes.disciplinas)).data.data
}

const select = (data) => {
    emits('selectLine', data)
    visivel.value = false
}

onMounted(async () => {
    getRegentesEscola()
})

getDisciplinas()
</script>

<template>
    <ConfirmPopup></ConfirmPopup>
    <Dialog :visible="visivel" modal header="Pesquisa Regentes" @update:visible="value => emits('update:visible', value)" class="p-dialog-maximized">
        <section class="mt-5" style="width: 70%; margin: 0 auto">
            <div class="p-fluid grid">
                <div class="field col-12 md:col-3">
                    <span class="p-float-label">
                        <InputText :disabled="form.nome.disabled"
                                  v-model="form.nome.value"
                        />
                        <label for="">{{ form.nome.label }}</label>
                    </span>
                </div>
                <div class="field col">
                    <span class="p-float-label">
                       <InputNumber
                           v-model="form.cgm.value"
                       />
                        <label for="">{{ form.cgm.label }}</label>
                    </span>
                </div>
                <div class="field col">
                    <span class="p-float-label">
                        <InputText :disabled="form.cpf.disabled"
                                    v-model="form.cpf.value"
                        />
                        <label for="">{{ form.cpf.label }}</label>
                    </span>
                </div>
                <div class="field col">
                    <span class="p-float-label">
                        <InputText :disabled="form.matricula.disabled"
                                    v-model="form.matricula.value"
                        />
                        <label for="">{{ form.matricula.label }}</label>
                    </span>
                </div>
                <div class="field col">
                    <span class="p-float-label">
                        <Dropdown  optionLabel="nome"
                                   :options="disciplinas"
                                    :disabled="form.slctdDisciplina.disabled"
                                    v-model="form.slctdDisciplina.data"
                        />
                        <label for="">{{ form.slctdDisciplina.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid mt-2">
                <div class="field col-12 md:col-2 col-offset-4">
                    <Button class="p-button" label="Buscar" icon="pi pi-search"
                            @click="getRegentesEscola"
                    ></Button>
                </div>
                <div class="field col-12 md:col-2">
                    <Button class="p-button" label="Limpar Filtros" icon="pi pi-save"
                            @click="limpaCampos"
                    ></Button>
                </div>
            </div>
        </section>
        <DataTable :value="regentes"
                   class="mt-5"
                   tableStyle="width: 70vw; margin: 0 auto"
                   :rows-per-page-options="[5, 10, 15, 20]"
                   lazy
                   paginator
                   :rows="5"
                   :totalRecords="regentes.length === 0 ? 0 : regentes[0].total"
                   @page="({page, rows}) => getRegentesEscola(event, page + 1, rows)">
            <Column field="nome" header="Nome" style="width: 20%">
                <template #body="{ data }">
                    <span @click="select(data)">{{ data.nome }}</span>
                </template>
            </Column>
            <Column field="cpf" header="CPF" style="width: 15%">
                <template #body="{ data }">
                    <span @click="select(data)">{{ data.cpf }}</span>
                </template>
            </Column>
            <Column  field="cgm" header="CGM" style="width: 15%">
                <template #body="{ data }">
                    <span @click="select(data)">{{ data.cgm }}</span>
                </template>
            </Column>
            <Column field="matricula" header="Matrícula" style="width: 15%">
                <template #body="{ data }">
                    <span @click="select(data)">{{ data.matricula }}</span>
                </template>
            </Column>
        </DataTable>
    </Dialog>
    <ModalLoading :isLoading="loading"/>
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
    span {
        cursor: pointer
    }
</style>
