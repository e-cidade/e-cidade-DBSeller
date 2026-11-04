<script setup>
import { onMounted, ref } from 'vue';
import FormCreateUnidadeGestora from './FormCreateUnidadeGestora';
import { useToast } from 'primevue/usetoast';

// Services
const toast = useToast();

// Data
const unidades = ref([]);
const visibleCreate = ref(false);
const dadosEdit = ref(null);
const isLoading = ref(false);

// Methods
const getUnidades = async () => {
    const url = 'v4/api/financeiro/contabilidade/tce/rj/sigfis/unidadegestora';
    isLoading.value = true;

    try {
        const req = await axios.get(url);
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        unidades.value = resp.data;
    } catch (error) {
        unidades.value = [];
        toast.add({ severity: 'warn', summary: 'Erro', detail: 'Erro ao buscar Unidades Gestoras' });
        log.error(error);
    } finally {
        isLoading.value = false;
    }
}

const openForm = (dados = null) => {
    visibleCreate.value = true;
    dadosEdit.value = dados;
}

const formSaved = () => {
    visibleCreate.value = false;
    getUnidades();
}

// Hooks
onMounted(() => {
    getUnidades();
})

</script>

<template>
    <Dialog v-model:visible="visibleCreate" modal header="Unidade Gestora" :style="{ width: '45rem' }" position="top">
        <FormCreateUnidadeGestora class="mt-5" @saved="formSaved" :dados="dadosEdit" />
    </Dialog>

    <div class="container mt-5">
        <DataTable :value="unidades" :loading="isLoading">
            <template #header>
                <Button icon="pi pi-plus" class="mr-2" label="Novo" @click="openForm" />
            </template>

            <Column field="codigo" header="Código"></Column>
            <Column field="codnomeinstit" header="Instituição"></Column>
            <Column field="cgmnomeordenador" header="Ordenador de Despesa"></Column>
            <Column header="Código Folha">
                <template #body="slotProps">
                    {{ slotProps.data.codigofolha ?? '-' }}
                </template>
            </Column>
            <Column>
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" size="small" label="Editar" @click="openForm(slotProps.data)" />
                </template>
            </Column>
        </DataTable>
    </div>
</template>
