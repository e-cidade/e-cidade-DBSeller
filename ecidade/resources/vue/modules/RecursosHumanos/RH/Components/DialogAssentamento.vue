<template>
    <Dialog :header="`Consulta de Assentamentos`" :maximizable="true" :modal="true" :style="{ width: '1000px' }"
        :visible="showDialod" :closable="true" @update:visible="closeDialog">
        <div class="mt-3">
                <div class="flex justify-content-center flex-wrap">
                    <div style="margin-right:5px">
                        <b>Assentamento:</b><br />
                        <InputText v-model="inputsFiltros.assentamento" />
                    </div>
                    <div style="margin-right:5px">
                        <b>Descrição:</b><br />
                        <InputText v-model="inputsFiltros.descricao" />
                    </div>
                </div>
                <div class="flex justify-content-center flex-wrap">
                    <Button label="Pesquisar" class="p-button-sm m-3" style="margin:5px" icon="pi pi-search" @click="filtrar()" />
                    <Button label="Limpar" class="p-button-sm p-button-info m-3" style="margin:5px" icon="pi pi-trash" @click="limparFiltro" />
                </div>
            <div class="card" style="height: calc(100vh - 143px)">
                <DataTable :value="assentamentos" showGridlines responsiveLayout="scroll" :loading="loading"
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
    h12_assent: { value: null, matchMode: FilterMatchMode.CONTAINS },
    h12_descr: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const filters = ref(filtrosDefault);

const inputsFiltrosDefault = function () {
    this.assentamento = '';
    this.descricao = '';
};
const inputsFiltros = ref(new inputsFiltrosDefault());

const showDialod = ref(false);
const loading = ref(false);
const assentamentos = ref([]);
const tableColumns = [
    {
        field: 'h12_codigo',
        header: 'Código',
    },
    {
        field: 'h12_assent',
        header: 'Assentamento',
    },
    {
        field: 'h12_descr',
        header: 'Descrição',
    },

];

const getAssentamentos = async () => {

    loading.value = true;
    try {
        const resp = await window.axios.post(
            'v4/api/recursos-humanos/rh/tipoasse/list',{}
        );
        console.log(resp);
        const data = resp.data.data;
        assentamentos.value = data;
    } catch (e) {
        assentamentos.value = [];
        console.log(e);
    }
    loading.value = false;
}

const filtrar = () => {
    filters.value.h12_assent.value = inputsFiltros.value.assentamento
    filters.value.h12_descr.value = inputsFiltros.value.descricao
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
    getAssentamentos();
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
