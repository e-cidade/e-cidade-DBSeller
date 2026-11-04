<template>
    <Dialog
        header="Consulta Grupo de Taxas"
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
                                data.labels.ar55_sequencial
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.sequencial"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="descricao">{{
                                data.labels.ar55_descricao
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.descricao"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="origem">{{
                                data.labels.ar55_origem
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.origem"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="dataLimite">{{
                                data.labels.ar55_datalimite
                            }}</label>
                            <Calendar
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4 w-full md:w-14rem shadow-4"
                                v-model="data.form.dataLimite"
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
                <Column
                    field="ar55_sequencial"
                    :header="data.labels.ar55_sequencial"
                />
                <Column
                    field="ar55_descricao"
                    :header="data.labels.ar55_descricao"
                />
                <Column
                    field="origem.ar56_descricao"
                    :header="data.labels.ar55_origem"
                />
                <Column
                    field="ar55_datalimite"
                    :header="data.labels.ar55_datalimite"
                />
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
        origem: "",
        descricao: "",
        dataLimite: "",
    },
    labels: {
        ar55_sequencial: "",
        ar55_descricao: "",
        ar55_origem: "",
        ar55_datalimite: "",
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
        "v4/api/tributario/arrecadacao/grupotaxas/rotulos"
    );
    data.labels = labels.data.data;
}

function pesquisaGeralGrupoTaxas(body) {
    return window.axios.post(
        "v4/api/tributario/arrecadacao/grupotaxas/buscar",
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
    data.form.origem = "";
    data.form.descricao = "";
    data.form.dataLimite = "";
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
    const dataLimite = data.form.dataLimite;
    const origem = data.form.origem;
    const descricao = data.form.descricao;

    const requestBody = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        origem: Number(origem),
        sequencial,
        descricao,
        datalimite:
            dataLimite &&
            dataLimite instanceof Date &&
            dataLimite.toJSON().slice(0, 10).trim() !== ""
                ? dataLimite.toJSON().slice(0, 10).trim()
                : "",
    };

    if (sequencial.trim() === "") {
        delete requestBody.sequencial;
    }
    if (origem.trim() === "") {
        delete requestBody.origem;
    }
    if (descricao.trim() === "") {
        delete requestBody.descricao;
    }
    if (requestBody.datalimite.trim() === "") {
        delete requestBody.datalimite;
    }

    pesquisaGeralGrupoTaxas(requestBody)
        .then((response) => {
            data.data = Object.values(
                response.data.data.data.map((obj) => ({
                    ...obj,
                    ar55_datalimite: formataData(obj.ar55_datalimite),
                }))
            );
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

function formataData(data) {
    if (data) {
        const split = data.toString().split(/\-|\//);
        const divisoria = "/";

        const dataBr = split[2].length === 4;
        const dia = dataBr ? split[0] : split[2];
        const ano = dataBr ? split[2] : split[0];
        const mes = split[1];

        return `${dia}${divisoria}${mes}${divisoria}${ano}`;
    }
}

defineExpose({
    toggleDialog,
    pesquisaGeralGrupoTaxas,
});
</script>
