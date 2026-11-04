<template>
    <Dialog :header="`Consulta de Departamentos da Instituição`" :maximizable="true" :modal="true"
        :style="{ width: '1000px' }" :visible="showDialod" :closable="true" @update:visible="closeDialog">
        <div class="mt-6">
            <Fieldset legend="Pesquisar Por">
                <div class="flex justify-content-center flex-wrap">
                    <div style="margin-right:10px">
                        <b>Código:</b><br />
                        <InputText v-model="inputsFiltros.codigo" />
                    </div>
                    <div style="margin-right:10px">
                        <b>Descrição:</b><br />
                        <InputText v-model="inputsFiltros.descricao" />
                    </div>
                </div>
                <div class="flex justify-content-center flex-wrap">                    
                    <div class="card">
                        <DataTable :value="departamentos" showGridlines responsiveLayout="scroll" :loading="loading"
                            @rowSelect="selectItem" selectionMode="single" :scrollable="true" scrollHeight="flex"
                            :paginator="true" :rowsPerPageOptions="[10, 20, 50]" :rows="10" v-model:filters="filters">
                            <Column v-for="col of tableColumns" :field="col.field" :header="col.header" :key="col.field" />
                        </DataTable>
                    </div>
                </div>
            </Fieldset>
        </div>
    </Dialog>
</template>
<script setup>
import { ref } from 'vue';
import { FilterMatchMode } from 'primevue/api';

const props = defineProps(['instituicao']);

const emit = defineEmits(['select']);

const filtrosDefault = {
    coddepto: { value: null, matchMode: FilterMatchMode.CONTAINS },
    descrdepto: { value: null, matchMode: FilterMatchMode.CONTAINS },
};

const filters = ref(filtrosDefault);

const inputsFiltrosDefault = function () {
    this.codigo = '';
    this.descricao = '';
};
const inputsFiltros = ref(new inputsFiltrosDefault());

const showDialod = ref(false);
const loading = ref(false);
const departamentos = ref([]);
const tableColumns = [
    {
        field: 'coddepto',
        header: 'Código',
    },
    {
        field: 'descrdepto',
        header: 'Descrição',
    }
];

const getDepartamentos = async () => {

    loading.value = true;
    try {
        const resp = await window.axios.get(
            `v4/api/configuracao/instituicao/${props.instituicao}/departamentos?perpage=1000`
        );
        const data = resp.data.data.data;
        console.log(data);
        departamentos.value = data;
    } catch (e) {
        departamentos.value = [];
        console.log(e);
    }
    loading.value = false;
}

const filtrar = () => {
    filters.value.coddepto.value = inputsFiltros.value.codigo
    filters.value.descrdepto.value = inputsFiltros.value.descricao
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
    getDepartamentos();
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
