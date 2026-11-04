<script setup>
import { ref, onMounted } from "vue";
import { FilterMatchMode } from 'primevue/api';
import { toCpf, toCnpj } from "../../../utils/Strings";

const emits = defineEmits(['selectLine'])
const props = defineProps({
    title: {
        type: String,
        required: true
    },
    route: {
        type: String,
        required: true
    },
    fieldsDisplay: {
        type: Array,
        required: false
    }
})

const filters = ref(null)
const globalFilterFields = ref([])
const visible = ref(false)
const data = ref(null)
const fields = ref(null)
const loading = ref(false)
function select(data) {
    close()
    emits('selectLine', data)
}
async function close() {
    visible.value = false
    data.value = null
    fields.value = null
    filters.value = null
}
async function open() {
    data.value = await getData(props.route)
    const duplic = new Set();
    data.value = data.value.filter((item) => {
        const duplicated = duplic.has(Object.values(item)[0]);
        duplic.add(Object.values(item)[0]);
        return !duplicated;
    });

    data.value = data.value.sort((a, b) => {
        if (Object.values(a)[0] < Object.values(b)[0]) return -1;
        if (Object.values(a)[0] > Object.values(b)[0]) return 1;
        return 0;
    })
    fields.value = props.fieldsDisplay !== undefined ? props.fieldsDisplay : await getFields(data.value)
    fields.value.forEach((field, chave) => {
        let array = field.field.split('.')
        if (array.length > 1) {
            data.value.forEach((row, key) => {
                data.value[key][`${array[0]}${array[1]}`] = data.value[key][array[0]][array[1]]
            })
            fields.value[chave].field = array[0] + array[1];
        }
    })
    filters.value = defineFilters(fields.value)
    visible.value = true
}
async function getData(route) {
    loading.value = true
    try {
        let retorno = await window.axios.get(route)
        loading.value = false
        return retorno.data.data
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
async function getFields(data) {
    let retorno = []
    for (const field of Object.keys(data[0])) {
        try {
            await window.axios.get(`v4/api/configuracao/campos/${field}`).then(async response => {
                response.data.data.forEach(campo => {
                    retorno.push({
                        label: campo.rotulo.trim(),
                        field: campo.nomecam.trim(),
                        dataType: 'string'
                    })
                })
            })
        } catch (e) {
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }
    return retorno
}
function formatField(field, dataType) {
    switch (dataType) {
        case 'cpf':
            return toCpf(field)
            break
        case 'cnpj':
            return toCnpj(field)
            break
        case 'data':
            if (field === undefined) {
                return ''
            }
            let date = new Date(field)
            date.setDate(date.getDate() + 1)
            date = Intl.DateTimeFormat('pt-BR').format(date)
            return date
            break
        case 'moeda':
            return `R$ ${field.toLocaleString()}`
            break
        case  'boolean':
            let icon = field ? `<i class="pi pi-check text-green-500"></i>` : `<i class="pi pi-times text-red-600"></i>`
            return icon
            break
        case 'string':
        case 'int':
            return field
            break

    }
}

function defineFilters(fields) {
    let retorno = {}
    fields.forEach(field => {
        globalFilterFields.value.push(field.label)
        retorno[field.field] = {value: null, matchMode: FilterMatchMode.CONTAINS}
    })
    return retorno
}
defineExpose({ getData, open, close })

onMounted(async () => {
    visible.value = false
    data.value = null
    fields.value = null
    filters.value = null
})
</script>

<template>
    <Dialog :header="props.title" v-model:visible="visible" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '80vw'}">
        <br>
        <div>
            <DataTable :value="data" v-model:filters="filters" responsiveLayout="scroll" paginator :rows="10" filterDisplay="row" :loading="loading"
                       :globalFilterFields="globalFilterFields">
                <template #empty> Nenhum registro </template>
                <template #loading> Buscando registros.</template>
                <Column v-for="field in fields" :field="field.field" :header="field.label === undefined ? field.field : field.label" :style="{ width: field.columnSize + '%' }">
                    <template #body="{ data }">
                        <span @click="select(data)" v-html="formatField(data[field.field], field.dataType)"></span>
                    </template>
                    <template #filter="{ filterModel, filterCallback }">
                        <InputText v-model="filterModel.value" type="text" @input="filterCallback()" class="p-column-filter" placeholder="Buscar" />
                    </template>
                </Column>
            </DataTable>
        </div>
    </Dialog>
</template>

<style scoped>
    span {
        cursor: pointer
    }
</style>
