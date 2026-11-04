<template>
    <Dialog
        header="Consulta CGM"
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
                dataKey="numcgm"
                @rowSelect="onRowSelect"
                class="shadow-4"
            >
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading><ProgressSpinner /></template>
                <template #header>
                    <form class="flex flex-row m-auto flex-wrap" ref="form">
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="numcgm">{{
                                data.labels.z01_numcgm
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.numcgm"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="nome">{{ data.labels.z01_nome }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.nome"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="cpfCnpj">{{
                                data.labels.z01_cgccpf
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.cgccpf"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="email">{{
                                data.labels.z01_email
                            }}</label>
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.email"
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
                <Column field="numcgm" :header="data.labels.z01_numcgm" />
                <Column field="nome" :header="data.labels.z01_nome" />
                <Column field="cpfCnpj" :header="data.labels.z01_cgccpf" />
                <Column field="tipo" header="Tipo" />
                <Column field="endereco" :header="data.labels.z01_ender" />
                <Column field="municipio" :header="data.labels.z01_munic" />
                <Column field="uf" :header="data.labels.z01_uf" />
                <Column field="cep" :header="data.labels.z01_cep" />
                <Column field="email" :header="data.labels.z01_email" />
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
        numcgm: "",
        nome: "",
        cgccpf: "",
        email: "",
    },
    labels: {
        z01_numcgm: "",
        z01_cgccpf: "",
        z01_nome: "",
        z01_sexo: "",
        z01_ender: "",
        z01_numero: "",
        z01_compl: "",
        z01_cxpostal: "",
        z01_bairro: "",
        z01_munic: "",
        z01_uf: "",
        z01_cep: "",
        z01_email: "",
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
        "v4/api/patrimonial/protocolo/rotulos-pesquisa-geral-cgm"
    );
    data.labels = labels.data.data;
}

function pesquisaGeralCgm(body) {
    return window.axios.post(
        "v4/api/patrimonial/protocolo/pesquisa-geral-cgm",
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
    data.form.numcgm = "";
    data.form.nome = "";
    data.form.cgccpf = "";
    data.form.email = "";
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

    const cgm = data.form.numcgm;
    const email = data.form.email;
    const nome = data.form.nome;
    const cgcpf = data.form.cgccpf;

    const requestBody = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        cgm,
        email,
        nome,
        cgcpf,
    };

    if (cgm.trim() === "") {
        delete requestBody.cgm;
    }
    if (email.trim() === "") {
        delete requestBody.email;
    }
    if (nome.trim() === "") {
        delete requestBody.nome;
    }
    if (cgcpf.trim() === "") {
        delete requestBody.cgcpf;
    }
    if (email.trim() === "") {
        delete requestBody.email;
    }

    pesquisaGeralCgm(requestBody)
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
    pesquisaGeralCgm,
});
</script>
