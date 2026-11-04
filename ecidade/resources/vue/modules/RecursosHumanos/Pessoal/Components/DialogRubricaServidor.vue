<template>
    <Dialog :header="`Consulta de Rubricas`" :maximizable="true" :modal="true" :style="{ width: '1000px' }"
        :visible="showDialog" :closable="true" @update:visible="closeDialog">
        <div class="mt-6">
            <div class="flex justify-content-center flex-wrap">
                <div style="margin-right:10px">
                    <b>Rubrica:</b><br />
                    <InputText v-model="inputsFiltros.rubrica" />
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
                <DataTable :value="rubricas" showGridlines responsiveLayout="scroll" :loading="loading"
                    @rowSelect="selectItem" selectionMode="single" :scrollable="true" scrollHeight="flex" :paginator="true"
                    :rowsPerPageOptions="[10, 20, 50, 100]" :rows="10" v-model:filters="filters">
                    <Column v-for="col of tableColumns" :field="col.field" :header="col.header" :key="col.field" />
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
    rh27_rubric: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rh27_descr: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const filters = ref(filtrosDefault);

const inputsFiltrosDefault = function () {
    this.rubrica = '';
    this.descricao = '';
};
const inputsFiltros = ref(new inputsFiltrosDefault());

const showDialog = ref(false);
const loading = ref(false);
const rubricas = ref([]);
const tableColumns = [
    {
        field: 'rh27_rubric',
        header: 'Rubrica',
    },
    {
        field: 'rh27_descr',
        header: 'Descrição',
    },
    {
        field: 'rh27_instit',
        header: 'Instituição',
    }
];

const getRubricas = async () => {

    loading.value = true;
    try {
        const resp = await window.axios.post(
            'v4/api/recursos-humanos/pessoal/rhrubricas/list', {}
        );
        console.log(resp);
        const data = resp.data.data;
        rubricas.value = data;
    } catch (e) {
        rubricas.value = [];
        console.log(e);
    }
    loading.value = false;
}

const filtrar = () => {
    filters.value.rh27_rubric.value = inputsFiltros.value.rubrica
    filters.value.rh27_descr.value = inputsFiltros.value.descricao
}

const limparFiltro = () => {
    Object.keys(filters.value).forEach(key => {
        filters.value[key].value = '';
    });
    inputsFiltros.value = new inputsFiltrosDefault();
}

const closeDialog = (e) => {
    showDialog.value = false;
}

const openDialog = (e) => {
    showDialog.value = true;
    limparFiltro();
    getRubricas();
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
