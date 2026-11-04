<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import ContaService from '../Services/ContaService';
import { FilterMatchMode } from 'primevue/api';

// service
const toast = useToast();
const filtersTable = ref({
    global: {value: null, matchMode: FilterMatchMode.CONTAINS}
})

// props
const props = defineProps(['filters']);

// emits
const emit = defineEmits(['select'])

// data
const contas = ref([]);
const isLoadingContas = ref(false);
const selectedConta = ref([]);

// methods
const getListaContas = async ()  => {
    const params = {filters: props.filters}

    try {
        isLoadingContas.value = true;
        contas.value = await ContaService.getContas(params);
    } catch (error) {
        let msg = 'Erro interno';
        if (error.response.data.message) {
            msg = error.response.data.message
        }

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro buscar contas ' + msg
        });
    } finally {
        isLoadingContas.value = false;
    }
}

const onRowSelect = () => {
    emit('select', selectedConta.value);
}

// hooks
onMounted(() => {
    getListaContas();
});

</script>

<template>
    <DataTable
        class="mt-2"
        tableStyle="min-width: 60rem"
        :value="contas"
        :loading="isLoadingContas"
        v-model:selection="selectedConta"
        v-model:filters="filtersTable"
        selectionMode="single"
        dataKey="c60_reduz"
        :globalFilterFields="['c60_estrut', 'c61_reduz', 'c61_reduz']"
        @rowSelect="onRowSelect"
    >
        <template #header>
            <InputText v-model="filtersTable['global'].value" placeholder="Pesquisar" />
        </template>
        <template #empty>
            Nenhuma conta disponível para mapeamento
        </template>
        <Column field="c60_estrut" header="Estrutural"></Column>
        <Column field="c61_reduz" header="Reduzido"></Column>
        <Column field="c60_descr" header="Descricao"></Column>
        <Column field="saldo" header="Saldo">
            <template #body="slotProps">
                R$ {{ Number(slotProps.data.saldo).toLocaleString('pt-BR') }}
            </template>
        </Column>
    </DataTable>
</template>
