<template>
    <Fieldset
        class="w-max ml-auto mr-auto mt-3 mb-3"
        legend="Informações - data de lançamento de débito"
    >
        <form class="mb-3 w-max">
            <table>
                <tr>
                    <td>
                        <label>
                            <a
                                href="javascript:void(0)"
                                @click="abrirConsultaCgm"
                                >Nome / Razão Social:</a
                            >
                        </label>
                    </td>
                    <td>
                        <InputText
                            class="p-inputtext-sm inputSearch m-auto shadow-4"
                            v-model="data.form.cgm"
                            @change="pesquisaCgm"
                        />
                    </td>
                    <td>
                        <InputText
                            class="p-inputtext-sm inputResult m-auto shadow-4"
                            disabled
                            v-model="data.form.resultadoPesquisa"
                        />
                    </td>
                </tr>
                <tr>
                    <td>
                        <a
                            href="javascript:void(0)"
                            @click="abrirConsultaMatricula"
                            >Matrícula do Imóvel:</a
                        >
                    </td>
                    <td>
                        <InputText
                            class="p-inputtext-sm inputSearch m-auto shadow-4"
                            v-model="data.form.matricula"
                            @change="pesquisaMatricula"
                        />
                    </td>
                </tr>
                <tr>
                    <td>
                        <a
                            href="javascript:void(0)"
                            @click="abrirConsultaInscricao"
                            >Inscrição Municipal:</a
                        >
                    </td>
                    <td>
                        <InputText
                            class="p-inputtext-sm inputSearch m-auto shadow-4"
                            v-model="data.form.inscricao"
                            @change="pesquisaInscricao"
                        />
                    </td>
                </tr>
            </table>

            <div
                class="flex justify-content-left flex-wrap card-container indigo-container m-3"
            >
                <Button class="m-auto shadow-4" @click="buscaRegistros"
                    ><i class="pi pi-search"> Pesquisar</i></Button
                >
            </div>
        </form>
    </Fieldset>

    <div v-if="data.dados.registros.length > 0" class="w-max m-auto">
        <DataTable
            :loading="data.loading"
            :value="data.dados.registros"
            responsiveLayout="scroll"
            :rowHover="true"
            :rows="8"
            showGridlines
            :paginator="false"
            filterDisplay="menu"
            class="shadow-4"
            tableStyle="min-width: 60rem"
            scrollable
            scrollHeight="400px"
        >
            <template #loading><ProgressSpinner /></template>
            <template #empty> Nenhum resultado foi encontrado </template>
            <Column field="numpre" :header="data.labels.k00_numpre" />
            <Column field="data" :header="data.labels.k163_data" />
            <Column field="tipodebito" :header="data.labels.k00_tipo" />
            <Column field="datalancamento" :header="data.labels.k00_dtoper" />
            <Column field="observacao" header="Observação" />
            <Column :exportable="false" field="" header="Ação">
                <template #body="slotProps">
                    <div
                        v-if="
                            (slotProps.data.manutencaoliberada === true &&
                                slotProps.data.debitolancado === false) ||
                            (slotProps.data.manutencaoliberada === true &&
                                slotProps.data.debitolancado === true &&
                                slotProps.data.lancamentomanual === true)
                        "
                    >
                        <div
                            class="buttonDiv"
                            v-if="slotProps.data.debitolancado === true"
                        >
                            <Button
                                icon="pi pi-pencil"
                                class="p-button-rounded p-button-sm border-600 shadow-4"
                                @click="editarDialog(slotProps.data)"
                            />
                            <Button
                                icon="pi pi-trash"
                                class="p-button-rounded ml-1 p-button-danger p-button-sm border-600 shadow-4"
                                @click="excluir(slotProps.data.numpre)"
                            />
                        </div>
                        <div
                            class="buttonDiv"
                            v-if="slotProps.data.debitolancado === false"
                        >
                            <Button
                                icon="pi pi-plus"
                                class="p-button-rounded ml-1 p-button-success p-button-sm border-600 shadow-4"
                                @click="incluirDialog(slotProps.data)"
                            />
                        </div>
                    </div>
                </template>
            </Column>
        </DataTable>
        <div class="flex justify-content-center bg-white">
            <Paginator
                :rows="data.informacoesData.porPagina"
                :totalRecords="data.informacoesData.numeroRegistros"
                @page="paginacao($event)"
                :alwaysShow="true"
                v-model:first="offset"
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
    </div>

    <Dialog
        v-model:visible="data.dialogAberto"
        :style="{ width: 'fit-content' }"
        :modal="true"
        :header="data.nomeAcao + ' Registro'"
        class="p-fluid shadow-4"
    >
        <form class="mt-3">
            <table class="m-3">
                <tr>
                    <td>
                        <a href="#">Data de lançamento:</a>
                    </td>
                    <td>
                        <input
                            type="date"
                            v-model="data.registroAtual.datalancamento"
                            class="shadow-4"
                        />
                    </td>
                </tr>
            </table>
            <div>
                Observação:
                <textarea
                    class="w-full shadow-4"
                    name="textoObservacao"
                    rows="7"
                    v-model="data.registroAtual.observacao"
                />
                <div class="flex justify-content-center mt-2">
                    <Button @click="executaAcao" class="w-auto shadow-4">{{
                        data.nomeAcao
                    }}</Button>
                </div>
            </div>
        </form>
    </Dialog>
    <ConfirmDialog />
    <DialogConsultaCgm ref="dialogConsultaCgm" @selectRow="selecionaCgm" />
    <DialogConsultaMatriculaImovel
        ref="dialogConsultaMatriculaImovel"
        @selectRow="selecionaMatricula"
    />
    <DialogConsultaInscricaoMunicipal
        ref="dialogConsultaInscricaoMunicipal"
        @selectRow="selecionaInscricao"
    />
</template>

<script setup>
import ConfirmDialog from "primevue/confirmdialog";
import DialogConsultaCgm from "../../Patrimonial/Protocolo/Components/DialogConsultaCgm.vue";
import DialogConsultaMatriculaImovel from "./components/DialogConsultaMatriculaImovel.vue";
import DialogConsultaInscricaoMunicipal from "./components/DialogConsultaInscricaoMunicipal.vue";
import { useConfirm } from "primevue/useconfirm";
import { reactive, ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";

const confirm = useConfirm();
const toast = useToast();
const dialogConsultaMatriculaImovel = ref(null);
const dialogConsultaCgm = ref(null);
const dialogConsultaInscricaoMunicipal = ref(null);
const offset = ref(0);

const data = reactive({
    loading: false,
    dados: { registros: [] },
    informacoesData: {
        registroInicial: 0,
        pagina: 1,
        porPagina: 10,
        numeroRegistros: 0,
        paginaFinal: 0,
        de: 0,
        ate: 0,
    },
    dialogAberto: false,
    tituloProcesso: "",
    nomeAcao: "Incluir",
    dialogConsultaCgm: false,
    form: {
        cgm: "",
        resultadoPesquisa: "",
        matricula: "",
        inscricao: "",
    },
    labels: {
        k00_numpre: "",
        k00_dtoper: "",
        k00_tipo: "",
        k163_data: "",
        k163_observacao: "",
        k00_numpar: "",
    },
    registroAtual: {
        data: "",
        datalancamento: "",
        debitolancado: "",
        manutencaoliberada: "",
        numpre: "",
        observacao: "",
        tipodebito: "",
    },
});

onMounted(() => {
    pesquisaLabels();
});

function paginacao(event) {
    data.informacoesData.pagina = event.page + 1;
    pesquisar();
}

async function pesquisar() {
    data.informacoesData.registroInicial = 0;
    buscaRegistros();
}

function editarDialog(dados) {
    selecionaRegistro(dados);
    data.nomeAcao = "Alterar";
    mostraDialog();
}

function incluirDialog(dados) {
    selecionaRegistro(dados);
    data.nomeAcao = "Incluir";
    mostraDialog();
}

function executaAcao() {
    const nomeAcao = data.nomeAcao.toLowerCase();
    const body = {
        numpre: data.registroAtual.numpre,
        dataLancamento: data.registroAtual.datalancamento,
        observacao: data.registroAtual.observacao,
    };
    if (nomeAcao === "incluir") {
        incluir(body);
    } else if (nomeAcao === "alterar") {
        editar(body);
    }
}

async function editar(body) {
    data.dados.registros = data.dados.registros.map((item) => {
        if (item.numpre === body.numpre) {
            if (body.dataLancamento.toString().trim() === "") {
                toast.add({
                    severity: "warn",
                    summary: "Atenção",
                    detail: `Data de lançamento deve ser preenchida.`,
                    life: 4000,
                });
            } else {
                window.axios
                    .patch(
                        "v4/api/tributario/arrecadacao/data-lancamento-debito/editar-registro",
                        body
                    )
                    .catch((erro) => {
                        buscaRegistros();
                    });
                mostraDialog();
                return {
                    data: item.data,
                    datalancamento: converteDataBR(body.dataLancamento),
                    debitolancado: true,
                    lancamentomanual: true,
                    manutencaoliberada: true,
                    numpar: item.numpar,
                    numpre: item.numpre,
                    observacao: body.observacao,
                    tipodebito: item.tipodebito,
                };
            }
        }
        return item;
    });
}

async function incluir(body) {
    data.dados.registros = data.dados.registros.map((item) => {
        if (item.numpre === body.numpre) {
            if (body.dataLancamento.toString().trim() === "") {
                toast.add({
                    severity: "warn",
                    summary: "Atenção",
                    detail: `Data de lançamento deve ser preenchida.`,
                    life: 4000,
                });
            } else {
                window.axios
                    .post(
                        "v4/api/tributario/arrecadacao/data-lancamento-debito/incluir-registro",
                        body
                    )
                    .catch((erro) => {
                        buscaRegistros();
                    });
                mostraDialog();
                return {
                    data: item.data,
                    datalancamento: converteDataBR(body.dataLancamento),
                    debitolancado: true,
                    lancamentomanual: true,
                    manutencaoliberada: true,
                    numpar: item.numpar,
                    numpre: item.numpre,
                    observacao: body.observacao,
                    tipodebito: item.tipodebito,
                };
            }
        }
        return item;
    });
}

function excluir(numpre) {
    confirm.require({
        message: "Excluir este registro?",
        header: "Confirmação",
        icon: "pi pi-exclamation-triangle",
        accept: () => {
            data.dados.registros = data.dados.registros.map((item) => {
                if (item.numpre === numpre) {
                    return {
                        data: item.data,
                        datalancamento: "",
                        debitolancado: false,
                        lancamentomanual: item.lancamentomanual,
                        manutencaoliberada: item.manutencaoliberada,
                        numpar: item.numpar,
                        numpre: item.numpre,
                        observacao: "",
                        tipodebito: item.tipodebito,
                    };
                }
                return item;
            });
            window.axios
                .delete(
                    "v4/api/tributario/arrecadacao/data-lancamento-debito/excluir-registro?numpre=" +
                        numpre
                )
                .catch((erro) => {
                    buscaRegistros();
                });
        },
    });
}

function setResultadoPesquisa(resultado, erro = false) {
    if (erro && resultado.trim() !== "") {
        data.form.resultadoPesquisa = `Código (${resultado}) não encontrado`;
    } else {
        data.form.resultadoPesquisa = resultado;
    }
}

function limparCampos(excecao = "nenhuma") {
    if (excecao !== "cgm") {
        data.form.cgm = "";
    }
    if (excecao !== "matricula") {
        data.form.matricula = "";
    }
    if (excecao !== "inscricao") {
        data.form.inscricao = "";
    }
}

function mostraDialog() {
    data.dialogAberto = !data.dialogAberto;
}

function selecionaRegistro(dados) {
    removeregistroAtual();
    data.registroAtual.numpre = dados.numpre ? dados.numpre : "";
    data.registroAtual.datalancamento = dados.datalancamento
        ? converteDataUS(dados.datalancamento)
        : "";
    data.registroAtual.debitolancado = dados.debitolancado
        ? converteDataUS(dados.debitolancado)
        : "";
    data.registroAtual.manutencaoliberada = dados.manutencaoliberada
        ? dados.manutencaoliberada
        : "";
    data.registroAtual.observacao = dados.observacao ? dados.observacao : "";
    data.registroAtual.tipodebito = dados.tipodebito ? dados.tipodebito : "";
}

function removeregistroAtual() {
    data.registroAtual = {
        data: "",
        datalancamento: "",
        debitolancado: "",
        manutencaoliberada: "",
        numpre: "",
        observacao: "",
        tipodebito: "",
    };
}

function abrirConsultaCgm() {
    dialogConsultaCgm.value.toggleDialog();
}

function pesquisaCgm() {
    data.loading = true;
    dialogConsultaCgm.value
        .pesquisaGeralCgm({ cgm: data.form.cgm })
        .then((response) => {
            data.loading = false;
            setResultadoPesquisa(response.data.data.data[0].nome);
            limparCampos("cgm");
        })
        .catch((error) => {
            data.loading = false;
            setResultadoPesquisa(data.form.cgm, true);
            limparCampos();
        });
}

function selecionaCgm(result) {
    const { numcgm, nome } = result;
    setResultadoPesquisa(nome);
    limparCampos("cgm");
    data.form.cgm = numcgm;
}

function abrirConsultaMatricula() {
    dialogConsultaMatriculaImovel.value.toggleDialog();
}

function pesquisaMatricula() {
    data.loading = true;
    dialogConsultaMatriculaImovel.value
        .pesquisaImoveis({ matricula: data.form.matricula })
        .then((response) => {
            data.loading = false;
            limparCampos("matricula");
            setResultadoPesquisa(response.data.data.data[0].z01_nome);
        })
        .catch((error) => {
            data.loading = false;
            setResultadoPesquisa(data.form.matricula, true);
            limparCampos();
        });
}

function selecionaMatricula(result) {
    const { matricula, nome } = result;
    setResultadoPesquisa(nome);
    limparCampos("matricula");
    data.form.matricula = matricula;
}

function abrirConsultaInscricao() {
    dialogConsultaInscricaoMunicipal.value.toggleDialog();
}

function pesquisaInscricao() {
    data.loading = true;
    dialogConsultaInscricaoMunicipal.value
        .pesquisaListaInscricoes({ inscricao: data.form.inscricao })
        .then((response) => {
            data.loading = false;
            limparCampos("inscricao");
            setResultadoPesquisa(response.data.data.data[0].z01_nome);
        })
        .catch((error) => {
            data.loading = false;
            setResultadoPesquisa(data.form.inscricao, true);
            limparCampos();
        });
}

function selecionaInscricao(result) {
    const { inscricao, nome } = result;
    setResultadoPesquisa(nome);
    limparCampos("inscricao");
    data.form.inscricao = inscricao;
}

async function pesquisaLabels() {
    const labels = await window.axios.get(
        "v4/api/tributario/arrecadacao/data-lancamento-debito/labels"
    );
    data.labels = labels.data.data;
}

async function buscaCgm(numeroCgm) {
    const resultado = await window.axios.get(
        `v4/api/tributario/arrecadacao/data-lancamento-debito/busca-cgm?cgm=${numeroCgm}&pagina=${data.informacoesData.pagina}&porPagina=${data.informacoesData.porPagina}`
    );
    return resultado.data.data;
}

async function buscaMatricula(numeroMatricula) {
    const resultado = await window.axios.get(
        `v4/api/tributario/arrecadacao/data-lancamento-debito/busca-matricula?matricula=${numeroMatricula}&pagina=${data.informacoesData.pagina}&porPagina=${data.informacoesData.porPagina}`
    );
    return resultado.data.data;
}

async function buscaInscricao(numeroInscricao) {
    const resultado = await window.axios.get(
        `v4/api/tributario/arrecadacao/data-lancamento-debito/busca-inscricao-municipal?inscricao=${numeroInscricao}&pagina=${data.informacoesData.pagina}&porPagina=${data.informacoesData.porPagina}`
    );
    return resultado.data.data;
}

function buscaRegistros() {
    data.dados.registros = [];
    const numeroCgm = data.form.cgm;
    const numeroMatricula = data.form.matricula;
    const numeroInscricao = data.form.inscricao;

    if (numeroCgm.toString().trim() !== "") {
        data.loading = true;
        buscaCgm(numeroCgm)
            .then((response) => {
                data.dados.registros = converterDataDosResultados(
                    response.data
                );
                data.informacoesData.numeroRegistros = response.total;
                data.informacoesData.paginaFinal = response.last_page;
                data.informacoesData.de = response.from;
                data.informacoesData.ate = response.to;
                data.loading = false;
            })
            .catch((erro) => {
                resetInformacoesDataPaginacao();
            });
    }

    if (numeroMatricula.toString().trim() !== "") {
        data.loading = true;
        buscaMatricula(numeroMatricula)
            .then((response) => {
                data.dados.registros = converterDataDosResultados(
                    response.data
                );
                data.informacoesData.numeroRegistros = response.total;
                data.informacoesData.paginaFinal = response.last_page;
                data.informacoesData.de = response.from;
                data.informacoesData.ate = response.to;
                data.loading = false;
            })
            .catch((erro) => {
                resetInformacoesDataPaginacao();
            });
    }

    if (numeroInscricao.toString().trim() !== "") {
        data.loading = true;
        buscaInscricao(numeroInscricao)
            .then((response) => {
                data.dados.registros = converterDataDosResultados(
                    response.data
                );
                data.informacoesData.numeroRegistros = response.total;
                data.informacoesData.paginaFinal = response.last_page;
                data.informacoesData.de = response.from;
                data.informacoesData.ate = response.to;
                data.loading = false;
            })
            .catch((erro) => {
                resetInformacoesDataPaginacao();
            });
    }
}

function resetInformacoesDataPaginacao() {
    data.informacoesData.pagina = 1;
    data.informacoesData.numeroRegistros = 0;
    data.informacoesData.paginaFinal = 0;
    data.informacoesData.de = 0;
    data.informacoesData.ate = 0;
    data.dados.registros = [];
    data.loading = false;
}

function converterDataDosResultados(resultados) {
    return resultados.map((item) => ({
        ...item,
        data: converteDataBR(item.data),
        datalancamento: converteDataBR(item.datalancamento),
    }));
}

function converteDataBR(data) {
    if (data !== null && data.toString().trim() !== "") {
        return data.toString().split("-").reverse().join("/");
    }
    return "";
}

function converteDataUS(data) {
    if (data !== null && data.toString().trim() !== "") {
        return data.toString().split("/").reverse().join("-");
    }
    return "";
}
</script>

<style scoped>
.buttonDiv {
    width: 100px;
    text-align: center;
}
.inputResult {
    width: 400px;
}
</style>
