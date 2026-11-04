<template>
    <Dialog
        header="Consulta Procedência"
        :maximizable="true"
        :modal="true"
        :style="{ width: '1000px' }"
        position="top"
        v-model:visible="data.dialogAberto"
        class="shadow-4"
    >
        <section style="width: 100%" class="m-auto">
            <DataTable
                :value="data.data"
                responsiveLayout="scroll"
                :rowHover="true"
                :rows="8"
                showGridlines
                :paginator="false"
                filterDisplay="menu"
                :loading="data.loading"
                v-model::selection="data.select"
                selectionMode="single"
                dataKey="sequencial"
                @rowSelect="onRowSelect"
                class="shadow-4"
            >
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading><ProgressSpinner /></template>
                <template #header>
                    <form class="flex flex-row m-auto flex-wrap" ref="form">
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="sequencial">{{
                                data.labels.dv09_procdiver
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.sequencial"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="descricaoabreviada">{{ data.labels.dv09_descra }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.descricaoabreviada"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="descricao">{{
                                data.labels.dv09_descr
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.descricao"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="receita">{{
                                data.labels.dv09_receit
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.receita"
                            />
                        </span>
                    </form>
                    <div class="m-auto flex justify-content-center flex-wrap">
                        <Button
                            class="m-2 p-button-success shadow-4"
                            @click="botaoPesquisa"
                            ><i class="pi pi-search"
                        /></Button>
                        <Button class="m-2 shadow-4" @click="limparCampos"
                            ><i class="pi pi-undo"
                        /></Button>
                        <Button
                            class="m-2 p-button-danger shadow-4"
                            @click="toggleDialog"
                            ><i class="pi pi-times"
                        /></Button>
                    </div>
                </template>
                <Column field="dv09_procdiver" :header="data.labels.dv09_procdiver" />
                <Column field="dv09_descra" :header="data.labels.dv09_descra" />
                <Column field="dv09_descr" :header="data.labels.dv09_descr" />
                <Column field="dv09_receit" :header="data.labels.dv09_receit" />
                <Column field="dv09_hist" :header="data.labels.dv09_hist" />
                <Column field="dv09_proced" :header="data.labels.dv09_proced" />
                <Column field="dv09_tipo" :header="data.labels.dv09_tipo" />
                <Column field="dv09_dtlimite" :header="data.labels.dv09_dtlimite" />
            </DataTable>
            <div class="flex justify-content-center">
                <Paginator
                    ref="teste"
                    :rows="data.informacoesData.porPagina"
                    :totalRecords="data.informacoesData.numeroRegistros"
                    @page="paginacao"
                    :alwaysShow="true"
                ></Paginator>
                <select
                    class="h-3rem mt-auto mb-auto ml-2 shadow-4"
                    v-model="data.informacoesData.porPagina"
                >
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
        sequencial: "",
        descricaoabreviada: "",
        descricao: "",
        receita: "",
    },
    labels: {
        dv09_procdiver: "",
        dv09_descr: "",
        dv09_descra: "",
        dv09_hist: "",
        dv09_proced: "",
        dv09_tipo: "",
        dv09_dtlimite: "",
        dv09_receit: "",
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
        "v4/api/tributario/diversos/rotulos-procedencia"
    );
    data.labels = labels.data.data;
}

function pesquisaGeralProcedencia(body) {
    return window.axios.post(
        "v4/api/tributario/diversos/pesquisa-procedencias",
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
    data.form.sequencial = "";
    data.form.descricaoabreviada = "";
    data.form.descricao = "";
    data.form.receita = "";
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

    const sequencial = data.form.sequencial;
    const receita = data.form.receita;
    const descricaoabreviada = data.form.descricaoabreviada;
    const descricao = data.form.descricao;

    const requestBody = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        filtraTipoCobrancaFalso : true,
        sequencial,
        receita,
        descricaoabreviada,
        descricao,
    };

    if (sequencial.trim() === "") {
        delete requestBody.sequencial;
    }
    if (receita.trim() === "") {
        delete requestBody.receita;
    }
    if (descricaoabreviada.trim() === "") {
        delete requestBody.descricaoabreviada;
    }
    if (descricao.trim() === "") {
        delete requestBody.descricao;
    }

    pesquisaGeralProcedencia(requestBody)
        .then((response) => {
            data.data = Object.values(response.data.data.data);
            data.informacoesData.numeroRegistros = response.data.data.total;
            data.informacoesData.paginaFinal = response.data.data.last_page;
            data.informacoesData.de = response.data.data.from;
            data.informacoesData.ate = response.data.data.to;
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
    pesquisaGeralProcedencia,
});
</script>
