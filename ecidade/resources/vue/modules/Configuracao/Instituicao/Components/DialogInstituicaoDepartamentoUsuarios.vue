<template>
    <Dialog
        :header="`Consulta de Usuários / Departamentos Instituição`"
        :maximizable="true"
        :modal="true"
        :style="{ width: '1000px' }"
        :visible="showDialod"
        :closable="true"
        @update:visible="closeDialog"
    >
        <div class="mt-6">
            <Fieldset legend="Pesquisar Por">
                <div class="flex justify-content-center flex-wrap">
                    <div style="margin-right:10px">
                        <b>CÓDIGO:</b><br/>
                        <InputText v-model="inputsFiltros.codigo"/>
                    </div>
                    <div style="margin-right:10px">
                        <b>LOGIN:</b><br/>
                        <InputText v-model="inputsFiltros.login"/>
                    </div>
                    <div style="margin-right:10px">
                        <b>NOME:</b><br/>
                        <InputText v-model="inputsFiltros.nome"/>
                    </div>
                </div>

                <div class="flex justify-content-center flex-wrap">
                    <Button style="margin:5px" label="Limpar Filtros" icon="pi pi-trash" class="p-button-secondary"
                            @click="limparFiltro"/>
                    <Button label="Pesquisar" style="margin:5px" icon="pi pi-search" @click="filtrar()"/>
                </div>
            </Fieldset>
            <div class="card">
                <DataTable
                    :value="usuarios"
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
                    v-model:filters="filters">
                    <Column
                        v-for="col of tableColumns"
                        :field="col.field"
                        :header="col.header"
                        :key="col.field"/>
                </DataTable>
            </div>
        </div>
    </Dialog>
</template>
<script setup>
import {ref} from 'vue';
import {FilterMatchMode} from 'primevue/api';

const props = defineProps([
    'instituicao',
    'departamento'
]);

const emit = defineEmits(['select']);

const filtrosDefault = {
    id_usuario: {value: null, matchMode: FilterMatchMode.CONTAINS},
    login: {value: null, matchMode: FilterMatchMode.CONTAINS},
    nome: {value: null, matchMode: FilterMatchMode.CONTAINS}
};

const filters = ref(filtrosDefault);

const inputsFiltrosDefault = function () {
    this.codigo = '';
    this.login = '';
    this.nome = '';
};

const inputsFiltros = ref(new inputsFiltrosDefault());

const showDialod = ref(false);
const loading = ref(false);
const usuarios = ref([]);

const tableColumns = [
    {
        field: 'id_usuario',
        header: 'Código',
    },
    {
        field: 'login',
        header: 'Login',
    },
    {
        field: 'nome',
        header: 'Nome',
    }
];

const getUsuarios = async () => {

    loading.value = true;
    try {
        const resp = await window.axios.get(
            `v4/api/configuracao/instituicao/${props.instituicao}/departamento/${props.departamento}?perpage=1000`
        );
        usuarios.value = resp.data.data.data;
    } catch (e) {
        usuarios.value = [];
    }
    loading.value = false;
}

const filtrar = () => {
    filters.value.id_usuario.value = inputsFiltros.value.codigo;
    filters.value.login.value = inputsFiltros.value.login;
    filters.value.nome.value = inputsFiltros.value.nome;
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
    getUsuarios();
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
