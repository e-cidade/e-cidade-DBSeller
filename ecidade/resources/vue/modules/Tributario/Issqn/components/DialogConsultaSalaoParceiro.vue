<template>
    <Dialog
        header="Consulta Inscrição Municipal"
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
                dataKey="q02_inscr"
                @rowSelect="onRowSelect"
                class="shadow-4"
            >
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading><ProgressSpinner /></template>
                <template #header>
                    <form class="flex flex-row m-auto flex-wrap" ref="form">
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="q02_inscr">{{
                                data.labels.q02_inscr
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.q02_inscr"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="z01_nome">{{
                                data.labels.z01_nome
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.z01_nome"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="cpf">{{
                                data.labels.z01_cgccpf
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.z01_cgccpf"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="q02_inscmu">{{
                                data.labels.q02_inscmu
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.q02_inscmu"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="q177_setorfiscal">{{
                                data.labels.q177_setorfiscal
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.q177_setorfiscal"
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
                    field="q02_inscr"
                    
                    :header="data.labels.q02_inscr"
                />
                <Column
                    field="z01_nome"
                    
                    :header="data.labels.z01_nome"
                />
                <Column
                    field="z01_cgccpf"
                    
                    :header="data.labels.z01_cgccpf"
                />
                <Column
                    field="z01_ender"
                    
                    :header="data.labels.z01_ender"
                />
                <Column
                    field="z01_numero"
                    
                    :header="data.labels.z01_numero"
                />
                <Column
                    field="z01_compl"
                    
                    :header="data.labels.z01_compl"
                />
                <Column
                    field="q02_dtinic"
                    
                    :header="data.labels.q02_dtinic"
                />
                <Column
                    field="q02_dtbaix"
                    
                    :header="data.labels.q02_dtbaix"
                />
                <Column
                    field="q02_inscmu"
                    
                    :header="data.labels.q02_inscmu"
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
        q02_inscr: "",
        z01_nome: "",
        z01_cgccpf: "",
        q02_inscmu: "",
        q177_setorfiscal: "",
    },
    labels: {
        q02_inscr: "",
        z01_nome: "",
        z01_cgccpf: "",
        q02_inscmu: "",
        q177_setorfiscal: "",
        z01_ender: "",
        z01_numero: "",
        z01_compl: "",
        q02_dtinic: "",
        q02_dtbaix: "",
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

function toggleDialog() {
    data.dialogAberto = !data.dialogAberto;
}

function onRowSelect(event) {
    data.data = [];
    data.dialogAberto = false;
    emit("selectRow", {
        inscricao: event.data.q02_inscr,
        nome: event.data.z01_nome,
    });
}

function paginacao(event) {
    data.informacoesData.pagina = event.page + 1;
    pesquisar();
}

function limparCampos() {
    data.data = [];
    data.form.q02_inscr = "";
    data.form.z01_nome = "";
    data.form.z01_cgccpf = "";
    data.form.q02_inscmu = "";
    data.form.q177_setorfiscal = "";
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

async function pesquisaLabels() {
    const labels = await window.axios.get(
        "v4/api/tributario/issqn/buscar-labels-inscricoes"
    );
    data.labels = labels.data.data;
}

function pesquisaListaInscricoes(parametros) {
    return window.axios.post(
        "v4/api/tributario/issqn/get-inscr-dialog",
        parametros
    );
}

async function pesquisar() {
    await carregaResultados();
}

async function carregaResultados() {
    data.loading = true;
    data.data = [];

    const inscricao = data.form.q02_inscr;
    const nome = data.form.z01_nome;
    const inscricaoAnterior = data.form.q02_inscmu;
    const cgcpf = data.form.z01_cgccpf;
    const setorFiscal = data.form.q177_setorfiscal;

    const parametrosPesquisa = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        inscricao: Number(inscricao),
        nome,
        inscricaoAnterior: inscricaoAnterior,
        cgcpf,
        setorFiscal: Number(setorFiscal),
    };

    if (inscricao.trim() === "") {
        delete parametrosPesquisa.inscricao;
    }
    if (nome.trim() === "") {
        delete parametrosPesquisa.nome;
    }
    if (inscricaoAnterior.trim() === "") {
        delete parametrosPesquisa.inscricaoAnterior;
    }
    if (cgcpf.trim() === "") {
        delete parametrosPesquisa.cgcpf;
    }
    if (setorFiscal.trim() === "") {
        delete parametrosPesquisa.setorFiscal;
    }

    pesquisaListaInscricoes(parametrosPesquisa)
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
    pesquisaListaInscricoes,
});
</script>
