<template>
    <Button link label="Matricula" :loading="loading" @click="buscarMatriculas"/>
    <Dialog v-model:visible="visible" header="Matriculas">
        <DataTable :value="matriculas"
                   showGridlines
                   paginator
                   :rows="25"
                   :rowsPerPageOptions="[10,15,25, 50]"
                   paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                   currentPageReportTemplate="{first} até {last} de {totalRecords}"
                   sortMode="multiple"
                   selectionMode="single"
                   :tableStyle="{ 'height': '100%', 'width': '100%', 'table-layout': 'fixed','padding': '15px' }"

        >
            <Column field="rh01_regist" header="Matricula"></Column>
            <Column field="rh01_numcgm" header="Cgm"></Column>
            <Column field="z01_nome" header="Nome/Razão Social"></Column>
            <Column field="z01_cgccpf" header="CNPJ/CPF "></Column>
            <Column field="rh01_admiss" header="Admissão"></Column>
            <Column field="rh05_recis" header="Rescisão"></Column>
            <Column field="rh37_descr" header="Descrição"></Column>
        </DataTable>

    </Dialog>
</template>
<script setup>
import {ref} from 'vue';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';

const loading = ref(false);
const visible = ref(false);
const matriculas = ref([])
const buscarMatriculas = () => {
    loading.value = true;

    loading.value = false;
    visible.value = true
}
</script>
