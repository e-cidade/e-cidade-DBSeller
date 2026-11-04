<template>
    <Dialog header="Consulta de Instituição" :maximizable="true" :modal="true" :style="{ width: '1000px' }"
        :visible="showDialod" :closable="true" @update:visible="closeDialog">
        <div class="mt-6">
            <Fieldset legend="Pesquisar Por">
                <div class="flex justify-content-center flex-wrap">
                    <div style="margin-right:10px">
                        <b>CÓDIGO:</b><br />
                        <InputText v-model="inputsFiltros.codigo" />
                    </div>
                    <div style="margin-right:10px">
                        <b>NOME:</b><br />
                        <InputText v-model="inputsFiltros.instituicao" />
                    </div>
                    <div style="margin-right:10px">
                        <b>CNPJ:</b><br />
                        <InputText v-model="inputsFiltros.cnpj" />
                    </div>
                </div>

                <div class="flex justify-content-center flex-wrap">
                    <Button style="margin:5px" label="Limpar Filtros" icon="pi pi-trash" class="p-button-secondary"
                        @click="limparFiltro" />
                    <Button label="Pesquisar" style="margin:5px" icon="pi pi-search" @click="filtrar()" />
                </div>
            </Fieldset>
            <div class="card">
                <DataTable
                    :value="instituicoes"
                    showGridlines
                    responsiveLayout="scroll"
                    :loading="loading"
                    @rowSelect="selectItem"
                    selectionMode="single"
                    :scrollable="true"
                    scrollHeight="flex"
                    :paginator="true"
                    :rowsPerPageOptions="[10, 20, 50]"
                    :rows="10"
                    v-model:filters="filters"
                  >
                    <Column v-for="col of tableColumns" :field="col.field" :header="col.header" :key="col.field"></Column>
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
    codigo: { value: null, matchMode: FilterMatchMode.CONTAINS },
    nomeinst: { value: null, matchMode: FilterMatchMode.CONTAINS },
    cgc: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const filters = ref(filtrosDefault);

const inputsFiltrosDefault = function () {
    this.codigo = '';
    this.instituicao = '';
    this.cnpj = '';
};
const inputsFiltros = ref(new inputsFiltrosDefault());

const showDialod = ref(false);
const loading = ref(false);
const instituicoes = ref([]);
const tableColumns = [
    {
        field: 'codigo',
        header: 'Código',
    },
    {
        field: 'nomeinst',
        header: 'NOME',
    },
    {
        field: 'cgc',
        header: 'CNPJ',
    }
];

const getInstituicoes = async () => {

    loading.value = true;

    try {
        const resp = await window.axios.get(
            `v4/api/configuracao/instituicao/search`
        );
        const data = resp.data.data.data;
        instituicoes.value = data;
    } catch (e) {
        instituicoes.value = [];
    }
    loading.value = false;
}

const filtrar = () => {
    filters.value.cgc.value = inputsFiltros.value.cnpj
    filters.value.nomeinst.value = inputsFiltros.value.instituicao
    filters.value.codigo.value = inputsFiltros.value.codigo
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
    getInstituicoes();
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
