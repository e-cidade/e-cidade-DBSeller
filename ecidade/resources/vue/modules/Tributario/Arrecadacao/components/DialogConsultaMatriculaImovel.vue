<template>
   <Dialog
       v-model:visible="openSearchCondominio"
       position="top"
       :modal="true"
       :draggable="false"
       header="Consulta Código de Condominio"
       style="width: 800px;"
       class="shadow-4"
   >
       <section style="width: 100%" class="m-auto">
           <DataTableConsultaCondominio
                @rowSelected="condominioSelected"
                @close="openSearchCondominio = false"
           />
       </section>
   </Dialog>


   <Dialog
       v-model:visible="openSearchLoteamento"
       position="top"
       :modal="true"
       :draggable="false"
       header="Consulta Código de Loteamento"
       style="width: 800px;"
       class="shadow-4"
   >
       <section style="width: 100%" class="m-auto">
           <DataTableConsultaLoteamento
                @rowSelected="loteamentoSelected"
                @close="openSearchLoteamento = false"
            />
       </section>
   </Dialog>


   <Dialog
       v-model:visible="openSearchLogradouro"
       position="top"
       :modal="true"
       :draggable="false"
       header="Consulta Código de Logradouro"
       style="width: 800px;"
       class="shadow-4"
   >
       <section style="width: 100%" class="m-auto">
           <DataTableConsultaLogradouro
                @rowSelected="logradouroSelected"
                @close="openSearchLogradouro = false"
           />
       </section>
   </Dialog>

    <Dialog
        header="Consulta Matrícula Imóvel"
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
                dataKey="j01_matric"
                @rowSelect="onRowSelect"
                class="shadow-4"
                stripedRows
            >
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading><ProgressSpinner /></template>
                <template #header>
                    <form class="flex flex-row flex-wrap" ref="form">
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j01_matric"
                                >{{ data.labels.j01_matric }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j01_matric"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j108_condominio" @click="openSearchCondominio = true" style="cursor: pointer; color: #4a789c;"
                                >{{ data.labels.j108_condominio }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j108_condominio"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j34_loteam" @click="openSearchLoteamento = true" style="cursor: pointer; color: #4a789c;"
                                >{{ data.labels.j34_loteam }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j34_loteam"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j14_codigo" @click="openSearchLogradouro = true" style="cursor: pointer; color: #4a789c;"
                                >{{ data.labels.j14_codigo }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j14_codigo"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="z01_nome"
                                >{{ data.labels.z01_nome }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.z01_nome"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j34_setor"
                                >{{ data.labels.j34_setor }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j34_setor"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j34_quadra"
                                >{{ data.labels.j34_quadra }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j34_quadra"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j34_lote"
                                >{{ data.labels.j34_lote }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j34_lote"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j05_codigoproprio"
                                >{{ data.labels.j05_codigoproprio }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j05_codigoproprio"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j06_quadraloc"
                                >{{ data.labels.j06_quadraloc }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j06_quadraloc"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j06_lote"
                                >{{ data.labels.j06_lote }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j06_lote"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j40_refant"
                                >{{ data.labels.j40_refant }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j40_refant"
                            />
                        </span>
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j40_registrocartografico"
                                >{{
                                    data.labels.j40_registrocartografico
                                }}:</label
                            >
                            <InputText
                                :useGrouping="false"
                                placeholder=""
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="data.form.j40_registrocartografico"
                            />
                        </span>

                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="j40_registrocartografico"
                                >Exibir Matrículas Baixadas:</label
                            >
                            <Dropdown
                                v-model="data.form.matriculaSelecionada"
                                :options="data.form.opcoesMatriculaSelecionada"
                                class="shadow-4"
                                optionLabel="name"
                                optionValue="code"
                                placeholder="Selecione"
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

                <Column field="j01_matric" :header="data.labels.j01_matric" />
                <Column
                    field="j108_condominio"
                    :header="data.labels.j108_condominio"
                />
                <Column field="j40_refant" :header="data.labels.j40_refant" />
                <Column
                    field="j40_registrocartografico"
                    :header="data.labels.j40_registrocartografico"
                />
                <Column field="z01_nome" :header="data.labels.z01_nome" />
                <Column field="tipo" header="Tipo" />
                <Column field="j14_nome" :header="data.labels.j14_nome" />
                <Column field="j39_numero" :header="data.labels.j39_numero" />
                <Column field="j39_compl" :header="data.labels.j39_compl" />
                <Column field="j34_setor" :header="data.labels.j34_setor" />
                <Column field="j34_quadra" :header="data.labels.j34_quadra" />
                <Column field="j34_lote" :header="data.labels.j34_lote" />
                <Column field="j01_baixa" :header="data.labels.j01_baixa" />
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
import { reactive, onMounted, ref } from "vue";
import DataTableConsultaCondominio from './DataTableConsultaCondominio';
import DataTableConsultaLogradouro from './DataTableConsultaLogradouro';
import DataTableConsultaLoteamento from './DataTableConsultaLoteamento';

const openSearchCondominio = ref(false);
const openSearchLoteamento = ref(false);
const openSearchLogradouro = ref(false);
const emit = defineEmits(["selectRow"]);
const data = reactive({
    dialogAberto: false,
    loading: false,
    select: null,
    form: {
        j01_matric: "",
        j108_condominio: "",
        j34_loteam: "",
        j14_codigo: "",
        z01_nome: "",
        j34_setor: "",
        j34_quadra: "",
        j34_lote: "",
        j05_codigoproprio: "",
        j06_quadraloc: "",
        j06_lote: "",
        j40_refant: "",
        j40_registrocartografico: "",
        matriculaSelecionada: false,
        opcoesMatriculaSelecionada: [
            { name: "NÃO", code: false },
            { name: "SIM", code: true },
        ],
    },
    labels: {
        j01_matric: "",
        j108_condominio: "",
        j34_loteam: "",
        j14_codigo: "",
        z01_nome: "",
        j34_setor: "",
        j34_quadra: "",
        j34_lote: "",
        j05_codigoproprio: "",
        j06_quadraloc: "",
        j06_lote: "",
        j40_refant: "",
        j40_registrocartografico: "",
        j14_nome: "",
        j39_numero: "",
        j39_compl: "",
        j01_baixa: "",
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

function condominioSelected(condominio) {
    data.form.j108_condominio = condominio;
    openSearchCondominio.value = false;
}

function logradouroSelected(logradouro) {
    data.form.j14_codigo = logradouro;
    openSearchLogradouro.value = false;
}

function loteamentoSelected(loteamento) {
    data.form.j34_loteam = loteamento.sequencial;
    data.form.j34_setor = loteamento.setor;
    data.form.j34_quadra = loteamento.quadra;
    data.form.j34_lote = loteamento.lote;
    openSearchLoteamento.value = false;
}

function toggleDialog() {
    data.dialogAberto = !data.dialogAberto;
}

function onRowSelect(event) {
    data.data = [];
    data.dialogAberto = false;
    emit("selectRow", {
        nome: event.data.z01_nome,
        matricula: event.data.j01_matric,
    });
}

function paginacao(event) {
    data.informacoesData.pagina = event.page + 1;
    pesquisar();
}
async function pesquisaLabels() {
    const labels = await window.axios.get(
        "v4/api/tributario/cadastro/buscar-labels-imoveis"
    );
    data.labels = labels.data.data;
}

function pesquisaImoveis(parametros) {
    return window.axios.post(
        "v4/api/tributario/cadastro/buscar-imoveis",
        parametros
    );
}

function limparCampos() {
    data.data = [];
    data.form.j01_matric = "";
    data.form.j108_condominio = "";
    data.form.j34_loteam = "";
    data.form.j14_codigo = "";
    data.form.z01_nome = "";
    data.form.j34_setor = "";
    data.form.j34_quadra = "";
    data.form.j34_lote = "";
    data.form.j05_codigoproprio = "";
    data.form.j06_quadraloc = "";
    data.form.j06_lote = "";
    data.form.j40_refant = "";
    data.form.j40_registrocartografico = "";
    data.form.matriculaSelecionada = false;
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

    const matriculasBaixadas = data.form.matriculaSelecionada ?? "";
    const matricula = data.form.j01_matric;
    const codCondominio = String(data.form.j108_condominio);
    const codLoteamento = String(data.form.j34_loteam);
    const codLogradouro = String(data.form.j14_codigo);
    const nome = data.form.z01_nome;
    const setor = String(data.form.j34_setor);
    const quadra = String(data.form.j34_quadra);
    const lote = String(data.form.j34_lote);
    const setorLocalizacao = data.form.j05_codigoproprio;
    const quadraLocalizacao = data.form.j06_quadraloc;
    const loteLocalizacao = data.form.j06_lote;
    const refAnterior = data.form.j40_refant;
    const registroCartografico = data.form.j40_registrocartografico;

    const parametrosPesquisa = {
        page: Number(data.informacoesData.pagina),
        porPagina: Number(data.informacoesData.porPagina),
        matriculasBaixadas,
        matricula: Number(matricula),
        codCondominio: Number(codCondominio),
        codLoteamento: Number(codLoteamento),
        codLogradouro: Number(codLogradouro),
        nome,
        setor,
        quadra,
        lote,
        setorLocalizacao,
        quadraLocalizacao,
        loteLocalizacao,
        refAnterior,
        registroCartografico,
    };

    if (matricula.trim() === "") {
        delete parametrosPesquisa.matricula;
    }
    if (codCondominio.trim() === "") {
        delete parametrosPesquisa.codCondominio;
    }
    if (codLoteamento.trim() === "") {
        delete parametrosPesquisa.codLoteamento;
    }
    if (codLogradouro.trim() === "") {
        delete parametrosPesquisa.codLogradouro;
    }
    if (nome.trim() === "") {
        delete parametrosPesquisa.nome;
    }
    if (setor.trim() === "") {
        delete parametrosPesquisa.setor;
    }
    if (quadra.trim() === "") {
        delete parametrosPesquisa.quadra;
    }
    if (lote.trim() === "") {
        delete parametrosPesquisa.lote;
    }
    if (setorLocalizacao.trim() === "") {
        delete parametrosPesquisa.setorLocalizacao;
    }
    if (quadraLocalizacao.trim() === "") {
        delete parametrosPesquisa.quadraLocalizacao;
    }
    if (loteLocalizacao.trim() === "") {
        delete parametrosPesquisa.loteLocalizacao;
    }
    if (refAnterior.trim() === "") {
        delete parametrosPesquisa.refAnterior;
    }
    if (registroCartografico.trim() === "") {
        delete parametrosPesquisa.registroCartografico;
    }

    pesquisaImoveis(parametrosPesquisa)
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
    pesquisaImoveis,
    toggleDialog,
});
</script>

<style scoped>
:deep(.p-row-odd) {
    background: #f8f6f6;
}
</style>