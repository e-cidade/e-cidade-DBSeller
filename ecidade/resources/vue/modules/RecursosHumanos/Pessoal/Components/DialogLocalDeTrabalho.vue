<template>
    <Dialog :header="`Consulta de Locais de Trabalho`" :maximizable="true" :modal="true" :style="{ width: '1000px' }"
        :visible="showDialod" :closable="true" @update:visible="closeDialog">
        <div class="mt-6">
                <div class="flex justify-content-center flex-wrap">
                    <div style="margin-right:10px">
                        <b>Códigos:</b><br />
                        <InputText v-model="inputsFiltros.codigo" />
                    </div>
                    <div style="margin-right:10px">
                        <b>Estrutural:</b><br />
                        <InputText v-model="inputsFiltros.estrutural" />
                    </div>
                    <div style="margin-right:10px">
                        <b>Descrição:</b><br />
                        <InputText v-model="inputsFiltros.descricao" />
                    </div>
                </div>
                <div class="flex justify-content-center flex-wrap">
                    <Button label="Pesquisar" class="p-button-sm m-3" style="margin:5px" icon="pi pi-search" @click="filtrar()" />
                    <Button label="Limpar" class="p-button-sm p-button-info m-3" style="margin:5px" icon="pi pi-trash" @click="limparFiltro" />
                </div>
            <div class="card" style="height: calc(100vh - 143px)">
                <DataTable :value="locais" showGridlines responsiveLayout="scroll" :loading="loading"
                    @rowSelect="selectItem" selectionMode="single" :scrollable="true" scrollHeight="flex" :paginator="true"
                    :rowsPerPageOptions="[10, 20, 50, 100]" :rows="10" v-model:filters="filters">
                    <Column v-for="col of tableColumns" :field="col.field" :header="col.header" :key="col.field" />
                    <template #empty>
                        Nenhum registro encontrado
                    </template>
                </DataTable>
            </div>
        </div>
    </Dialog>
</template>
<script setup>

import { ref } from 'vue';
import { FilterMatchMode } from 'primevue/api';

const emit = defineEmits(['select']);

const filtrosDefault = {
    rh55_codigo: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rh55_estrut: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rh55_descr: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const filters = ref(filtrosDefault);

const inputsFiltrosDefault = function () {
    this.codigo = '';
    this.estrutural = '';
    this.descricao = '';
};
const inputsFiltros = ref(new inputsFiltrosDefault());

const showDialod = ref(false);
const loading = ref(false);
const locais = ref([]);
const tableColumns = [
    {
        field: 'rh55_codigo',
        header: 'Código',
    },
    {
        field: 'rh55_estrut',
        header: 'Estrutural',
    },
    {
        field: 'rh55_descr',
        header: 'Descrição',
    },
    {
        field: 'rh55_instit',
        header: 'Instituição',
    }
];

const getLocais = async () => {

    loading.value = true;
    try {
        const resp = await window.axios.post(
            'v4/api/recursos-humanos/pessoal/rhlocaltrab/list',{}
        );
        console.log(resp);
        const data = resp.data.data;
        locais.value = data;
    } catch (e) {
        locais.value = [];
        console.log(e);
    }
    loading.value = false;
}

const filtrar = () => {
    filters.value.rh55_codigo.value = inputsFiltros.value.codigo
    filters.value.rh55_estrut.value = inputsFiltros.value.estrutural
    filters.value.rh55_descr.value = inputsFiltros.value.descricao
}

const limparFiltro = () => {
    Object.keys(filters.value).forEach(key => {
        filters.value[key].value = '';
    });
    inputsFiltros.value = new inputsFiltrosDefault();
}

const closeDialog = (e) => {
    showDialod.value = false;
}

const openDialog = (e) => {
    showDialod.value = true;
    limparFiltro();
    getLocais();
}

const selectItem = (e) => {
    emit("select", e.data);
    closeDialog(false);
}

defineExpose({
    closeDialog,
    openDialog
});

</script>
<style scoped></style>
