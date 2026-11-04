<template>
    <Dialog
        header="Consultar Macrozonas"
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
                dataKey="j224_sequencial"
                @rowSelect="onRowSelect"
                class="shadow-4"
            >
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading><ProgressSpinner /></template>
                <template #header>
                    <form class="flex flex-row justify-center m-auto flex-wrap" ref="form">
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j224_sequencial">{{
                                'Código'
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-md shadow-4"
                                v-model="data.form.j224_sequencial"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j224_sigla">{{
                                'Sigla'
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-md shadow-4"
                                v-model="data.form.j224_sigla"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j224_localizacao">{{
                                'Localização'
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-md shadow-4"
                                v-model="data.form.j224_localizacao"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j224_descricao">{{
                                'Descrição'
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-md shadow-4"
                                v-model="data.form.j224_descricao"
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
                    field="j224_sequencial"
                    header="Código"
                />
                <Column
                    field="j224_sigla"
                    header="Sigla"
                />
                <Column
                    field="j224_localizacao"
                    header="Localização"
                />
                <Column
                    field="j224_descricao"
                    header="Descrição"
                />
            </DataTable>
            <div class="flex justify-content-center">
                <Paginator
                    :rows="data.informacoesData.porPagina"
                    :totalRecords="data.informacoesData.numeroRegistros"
                    @page="paginacao($event)"
                    :alwaysShow="true"
                    :first="data.informacoesData.registroInicial"
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
        j224_sequencial: "",
        j224_sigla: "",
        j224_localizacao: "",
        j224_descricao: "",
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

function toggleDialog() {
    data.dialogAberto = !data.dialogAberto;
}

function onRowSelect(event) {
    data.data = [];
    data.dialogAberto = false;
    emit("selectRow", {
        j224_sequencial: event.data.j224_sequencial,
        j224_sigla: event.data.j224_sigla,
        j224_localizacao: event.data.j224_localizacao,
        j224_descricao: event.data.j224_descricao,
    });
}

function paginacao(event) {
    data.informacoesData.pagina = event.page + 1;
    pesquisar();
}

function limparCampos() {
    data.data = [];
    data.form.j224_sequencial = "";
    data.form.j224_sigla = "";
    data.form.j224_localizacao = "";
    data.form.j224_descricao = "";
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

function pesquisaListaMacrozonas(parametros) {
    return window.axios.post(
        "v4/api/tributario/cadastro/get-macrozonas",
        parametros
    );
}

async function pesquisar() {
    await carregaResultados();
}

async function carregaResultados() {
    data.loading = true;
    data.data = [];

    const sequencial = data.form.j224_sequencial;
    const sigla = data.form.j224_sigla;
    const localizacao = data.form.j224_localizacao;
    const descricao = data.form.j224_descricao;

    const parametrosPesquisa = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        sequencial: Number(sequencial),
        sigla,
        localizacao,
        descricao,
    };

    if (sequencial.trim() === "") {
        delete parametrosPesquisa.sequencial;
    }
    if (sigla.trim() === "") {
        delete parametrosPesquisa.sigla;
    }
    if (localizacao.trim() === "") {
        delete parametrosPesquisa.localizacao;
    }
    if (descricao.trim() === "") {
        delete parametrosPesquisa.descricao;
    }

    pesquisaListaMacrozonas(parametrosPesquisa)
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
    pesquisaListaMacrozonas,
});
</script>
