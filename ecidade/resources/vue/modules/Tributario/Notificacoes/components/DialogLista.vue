<template>
    <Dialog header="Consulta Lista" :maximizable="true" :modal="true" :style="{ width: '1000px' }" position="top"
        v-model:visible="data.dialogAberto" class="shadow-4">
        <section style="width: 100%" class="m-auto">
            <DataTable :value="data.data" responsiveLayout="scroll" :rowHover="true" :rows="8" showGridlines
                :paginator="false" filterDisplay="menu" :loading="data.loading" v-model::selection="data.select"
                selectionMode="single" dataKey="numcgm" @rowSelect="onRowSelect" class="shadow-4">
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading>
                    <ProgressSpinner />
                </template>
                <template #header>
                    <form class="flex flex-row m-auto flex-wrap" ref="form">
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="numcgm">{{
                                data.labels.k60_codigo
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.k60_codigo" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="nome">{{ data.labels.k60_descr }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.k60_descr" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="cpfCnpj">{{
                                data.labels.k60_tipo
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.k60_tipo" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="email">{{
                                data.labels.k60_datadeb
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.k60_datadeb" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="email">{{
                                data.labels.k60_filtros
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.k60_filtros" />
                        </span>
                    </form>
                    <div class="m-auto flex justify-content-center flex-wrap">
                        <Button class="m-2 p-button-success shadow-4" @click="botaoPesquisa"><i
                                class="pi pi-search" /></Button>
                        <Button class="m-2 shadow-4" @click="limparCampos"><i class="pi pi-undo" /></Button>
                        <Button class="m-2 p-button-danger shadow-4" @click="toggleDialog"><i
                                class="pi pi-times" /></Button>
                    </div>
                </template>
                <Column field="k60_codigo" :header="data.labels.k60_codigo" />
                <Column field="k60_descr" :header="data.labels.k60_descr" />
                <Column field="k60_tipo" :header="data.labels.k60_tipo" />
                <Column field="k60_datadeb" :header="data.labels.k60_datadeb" />
                <Column field="k60_filtros" :header="data.labels.k60_filtros" />
            </DataTable>
            <div class="flex justify-content-center">
                <Paginator ref="teste" :rows="data.informacoesData.porPagina" v-model:first="data.informacoesData.pagina"
                    :totalRecords="data.informacoesData.numeroRegistros" @page="paginacao" :alwaysShow="true"></Paginator>
                <select class="h-3rem mt-auto mb-auto ml-2 shadow-4" v-model="data.informacoesData.porPagina">
                    <option selected="true" value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                </select>
            </div>
        </section>
    </Dialog>
</template>

<script setup>
import { reactive, onMounted } from "vue";

const emit = defineEmits(["selectRow"]);
const data = reactive({
    dialogAberto: false,
    loading: false,
    select: null,
    form: {
        k60_codigo: "",
        k60_descr: "",
        k60_tipo: "",
        k60_datadeb: "",
        k60_filtros: ''
    },
    labels: {
        k60_codigo: "",
        k60_descr: "",
        k60_tipo: "",
        k60_datadeb: "",
        k60_filtros: ""
    },
    data: [],
    informacoesData: {
        registroInicial: 0,
        pagina: 1,
        porPagina: 10,
        numeroRegistros: 0,
        paginaFinal: 0,
        de: 0,
        ate: 0,
    },
});

onMounted(() => {
    pesquisaLabels();
});

async function pesquisaLabels() {
    const labels = await window.axios.get(
        "v4/api/tributario/notificacoes/lista/rotulos"
    );
    data.labels = labels.data.data;
}

function pesquisaGeralLista(body) {
    return window.axios.post(
        "v4/api/tributario/notificacoes/lista/getlista",
        body
    );
}

function toggleDialog() {
    data.dialogAberto = !data.dialogAberto;
}

function onRowSelect(event) {
    data.data = [];
    data.dialogAberto = false;
    emit("selectRow", event.data);
}

function paginacao(event) {
    data.informacoesData.pagina = event.page + 1;
    pesquisar();
}

function limparCampos() {
    data.data = [];
    data.form.k60_codigo = "";
    data.form.k60_datadeb = "";
    data.form.k60_descr = "";
    data.form.k60_filtros = "";
    data.form.k60_tipo = "";
}

function botaoPesquisa() {
    data.informacoesData.pagina = 1;
    data.informacoesData.numeroRegistros = 0;
    data.informacoesData.paginaFinal = 0;
    data.informacoesData.de = 0;
    data.informacoesData.ate = 0;
    data.informacoesData.registroInicial = 0;
    pesquisar();
}

async function pesquisar() {
    await carregaResultados();
}

async function carregaResultados() {
    data.loading = true;
    data.data = [];

    const k60_codigo = data.form.k60_codigo;
    const k60_datadeb = data.form.k60_datadeb;
    const k60_descr = data.form.k60_descr;
    const k60_filtros = data.form.k60_filtros;
    const k60_tipo = data.form.k60_tipo;

    const requestBody = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        k60_codigo,
        k60_datadeb,
        k60_filtros,
        k60_tipo,
        k60_descr,
    };

    if (k60_codigo.trim() === "") {
        delete requestBody.k60_codigo;
    }
    if (k60_datadeb.trim() === "") {
        delete requestBody.k60_datadeb;
    }
    if (k60_descr.trim() === "") {
        delete requestBody.k60_descr;
    }
    if (k60_filtros.trim() === "") {
        delete requestBody.k60_filtros;
    }
    if (k60_tipo.trim() === "") {
        delete requestBody.k60_tipo;
    }

    pesquisaGeralLista(requestBody)
        .then((response) => {
            data.data = (response.data.data.data);
            data.informacoesData.numeroRegistros = response.data.data.total;
            data.informacoesData.pagina = response.data.data.from-1;
        })
        .catch((error) => {
            data.informacoesData.pagina = 1;
            data.informacoesData.numeroRegistros = 0;
            data.informacoesData.paginaFinal = 0;
            data.informacoesData.de = 0;
            data.informacoesData.ate = 0;
            data.data = [];
            data.loading = false;
        })
        .finally(() => {
            data.loading = false;
        });
}

defineExpose({
    toggleDialog,
    pesquisaGeralLista,
});
</script>
