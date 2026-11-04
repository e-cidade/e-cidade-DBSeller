<template>
    <Dialog :header="`Consulta de Cargos`" :maximizable="true" :modal="true" :style="{ width: '1000px' }"
        :visible="showDialod" :closable="true" @update:visible="closeDialog">
        <div class="mt-6">
                <div class="flex justify-content-center flex-wrap">
                    <div style="margin-right:10px">
                        <b>Cargo:</b><br />
                        <InputText v-model="inputsFiltros.codigo" />
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
                <DataTable :value="cargos" showGridlines responsiveLayout="scroll" :loading="loading"
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
    rh37_funcao: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rh37_descr: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const filters = ref(filtrosDefault);

const inputsFiltrosDefault = function () {
    this.codigo = '';
    this.descricao = '';
};
const inputsFiltros = ref(new inputsFiltrosDefault());

const showDialod = ref(false);
const loading = ref(false);
const cargos = ref([]);
const tableColumns = [
    {
        field: 'rh37_funcao',
        header: 'Código',
    },
    {
        field: 'rh37_descr',
        header: 'Descrição',
    },
    {
        field: 'rh37_instit',
        header: 'Instituição',
    }
];

const getCargos = async () => {

    loading.value = true;
    try {
        const resp = await window.axios.post(
            'v4/api/recursos-humanos/pessoal/rhfuncao/list', {}
        );
        console.log(resp);
        const data = resp.data.data;
        cargos.value = data;
    } catch (e) {
        cargos.value = [];
        console.log(e);
    }
    loading.value = false;
}

const filtrar = () => {
    filters.value.rh37_funcao.value = inputsFiltros.value.codigo
    filters.value.rh37_descr.value = inputsFiltros.value.descricao
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
    getCargos();
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
