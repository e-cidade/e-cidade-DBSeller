<template>
    <Dialog header="Consulta de Usuários"
            :maximizable="true"
            :modal="true"
            :style="{ width: '1000px' }"
            :visible="showDialod"
            :closable="true"
            @update:visible="closeDialog">
        <div class="mt-6">
            <Fieldset legend="Pesquisar Por">
                <div class="flex justify-content-center flex-wrap">
                    <div style="margin-right:10px">
                        <b>CÓDIGO:</b><br/>
                        <InputText v-model="filtro.codigo"/>
                    </div>
                    <div style="margin-right:10px">
                        <b>LOGIN:</b><br/>
                        <InputText v-model="filtro.login"/>
                    </div>
                    <div style="margin-right:10px">
                        <b>NOME:</b><br/>
                        <InputText v-model="filtro.nome"/>
                    </div>
                </div>

                <div class="flex justify-content-center flex-wrap">
                    <Button style="margin:5px"
                            label="Limpar Filtros"
                            icon="pi pi-trash"
                            class="p-button-secondary"
                            @click="limparFiltros"/>
                    <Button label="Pesquisar"
                            style="margin:5px"
                            icon="pi pi-search"
                            @click="getusuarios({page:0})"/>
                </div>
            </Fieldset>
            <div class="card"
                 style="height: calc(100vh - 143px)">
                <DataTable
                    :value="usuarios"
                    showGridlines
                    responsiveLayout="scroll"
                    @rowSelect="selectItem"
                    selectionMode="single"
                    :scrollable="true"
                    scrollHeight="flex"
                    :loading="loading"
                >
                    <Column v-for="col of tableColumns"
                            :field="col.field"
                            :header="col.header"
                            :key="col.field"></Column>
                </DataTable>
                <Paginator
                    ref="paginator"
                    :rows="paginate.perpage"
                    :totalRecords="paginate.total"
                    v-model:first="paginate.offset"
                    :rowsPerPageOptions="[10, 20, 30]"
                    @page="getusuarios($event)"
                />
            </div>
        </div>
    </Dialog>
</template>
<script setup>

import {ref} from 'vue';

const props = defineProps(["instituicao_sessao"]);
const emit = defineEmits(['select']);

const defaultPaginate = function () {
    this.page = 0;
    this.total = 0;
    this.perpage = 10;
    this.offset = 0;
};

const defaultFiltros = function () {
    this.codigo = '';
    this.login = '';
    this.nome = '';
    this.instituicao_sessao = false;
};

const paginate = ref(defaultPaginate);
const filtro = ref(defaultFiltros);
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

const getusuarios = async ({page, rows} = {page: 0, rows: 10}) => {


    loading.value = true;

    try {

        paginate.value.page = ++page;

        if (rows) {
            if (paginate.value.perpage !== parseInt(rows)) {
                paginate.value.page = 1;
                paginate.value.offset = 0;
            }
            paginate.value.perpage = parseInt(rows);
        }

        const urlParams = new URLSearchParams({
            ...filtro.value,
            ...paginate.value,
            instituicao_sessao: props.instituicao_sessao
        });

        const resp = await window.axios.get(
            `v4/api/configuracao/usuario/search?${urlParams.toString()}`
        );
        const data = resp.data.data.data;
        const {current_page, total, per_page} = resp.data.data;
        paginate.value = {
            page: current_page,
            offset: current_page * per_page - 1,
            total,
            perpage: parseInt(per_page)
        };

        usuarios.value = data;
    } catch (e) {
        usuarios.value = [];
    }
    loading.value = false;
}

const limparFiltros = () => {
    filtro.value = new defaultFiltros();
    getusuarios();
}

const closeDialog = (e) => {
    showDialod.value = false;
}

const openDialog = (e) => {
    showDialod.value = true;
    limparFiltros();
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
