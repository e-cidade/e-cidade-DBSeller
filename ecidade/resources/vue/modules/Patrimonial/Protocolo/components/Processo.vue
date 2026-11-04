<template>
    <Dialog header="Consulta Lista" :maximizable="true" :modal="true" :style="{ width: '1000px' }" position="top"
        v-model:visible="data.dialogAberto" class="shadow-4">
        <section style="width: 100%" class="m-auto">
            <DataTable :value="data.data" responsiveLayout="scroll" :rowHover="true" :rows="8" showGridlines
                :paginator="false" filterDisplay="menu" :loading="data.loading" v-model::selection="data.select"
                selectionMode="single" dataKey="p58_codproc" @rowSelect="onRowSelect" class="shadow-4">
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading>
                    <ProgressSpinner />
                </template>
                <template #header>
                    <form class="flex flex-row m-auto flex-wrap" ref="form">
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="p58_codproc">{{
                                data.labels.p58_codproc
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.p58_codproc" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="p58_requer">{{ data.labels.p58_requer }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.p58_requer" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="p58_obs">{{
                                data.labels.p58_obs
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.p58_obs" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="p58_dtproc">{{
                                data.labels.p58_dtproc
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.p58_dtproc" />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="p58_ano">{{
                                data.labels.p58_ano
                            }}</label>
                            <InputText :useGrouping="false" placeholder="" class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.p58_ano" />
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
                <Column field="p58_codproc" :header="data.labels.p58_codproc" />
                <Column field="p58_requer" :header="data.labels.p58_requer" />
                <Column field="p58_obs" :header="data.labels.p58_obs" />
                <Column field="p58_dtproc" :header="data.labels.p58_dtproc" />
                <Column field="p58_ano" :header="data.labels.p58_ano" />
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
        p58_codproc: "",
        p58_requer: "",
        p58_obs: "",
        p58_dtproc: "",
        p58_ano: ''
    },
    labels: {
        p58_codproc: "",
        p58_requer: "",
        p58_obs: "",
        p58_dtproc: "",
        p58_ano: ""
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
        "v4/api/patrimonial/protocolo/processo/rotulos"
    );
    data.labels = labels.data.data;
}

function pesquisaGeralLista(body) {
    return window.axios.post(
        "v4/api/patrimonial/protocolo/processo/getprocesso",
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
    data.form.p58_codproc = "";
    data.form.p58_dtproc = "";
    data.form.p58_requer = "";
    data.form.p58_ano = "";
    data.form.p58_obs = "";
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

    const p58_codproc = data.form.p58_codproc;
    const p58_dtproc = data.form.p58_dtproc;
    const p58_requer = data.form.p58_requer;
    const p58_ano = data.form.p58_ano;
    const p58_obs = data.form.p58_obs;

    const requestBody = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        p58_codproc,
        p58_dtproc,
        p58_ano,
        p58_obs,
        p58_requer,
    };

    if (p58_codproc.trim() === "") {
        delete requestBody.p58_codproc;
    }
    if (p58_dtproc.trim() === "") {
        delete requestBody.p58_dtproc;
    }
    if (p58_requer.trim() === "") {
        delete requestBody.p58_requer;
    }
    if (p58_ano.trim() === "") {
        delete requestBody.p58_ano;
    }
    if (p58_obs.trim() === "") {
        delete requestBody.p58_obs;
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
