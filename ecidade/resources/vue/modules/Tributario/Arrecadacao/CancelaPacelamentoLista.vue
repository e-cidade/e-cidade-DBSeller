
<template>
    <ModalLoading :isLoading="data.loading" />
    <Toast />
    <section class="container">
        <Panel header="Anulação de Parcelamento por Lista">
            <ConfirmDialog></ConfirmDialog>
            <div class="flex align-items-center justify-content-center m-1" style="width: 100%;">
                <div class="m-auto">
                    <form class="mb-3 w-max">
                        <table>
                            <tr>
                                <td>
                                    <label>
                                        <a href="javascript:void(0)" @click="abrirConsultaLista">Lista:</a>
                                    </label>
                                </td>
                                <td>
                                    <InputText class="p-inputtext-sm inputSearch m-auto shadow-4"
                                        v-model="data.form.k60_codigo" @change="pesquisaLista" />
                                    <Button class="mx-6 shadow-4" @click="carregaResultados"
                                        :disabled=data.desabilitaPesquisa><i class="pi pi-search">
                                            Pesquisar Débitos</i></Button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p>Motivo</p>
                                </td>
                                <td>
                                    <Textarea v-model="data.form.motivo" rows="5" cols="50" />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:void(0)" @click="abrirConsultaProcesso">Processo:</a>
                                </td>
                                <td>
                                    <InputText class="p-inputtext-sm inputSearch m-auto shadow-4"
                                        v-model="data.form.p58_codproc" @change="pesquisaProcesso" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
            <div v-if="data.exibir" class="card">
                <DataTable :value="data.data" :loading="loading" :rowHover="true" selectionMode="single"
                    responsiveLayout="scroll" :paginator="false">
                    <Column field="parcelamento" header="Parcelamento" />
                    <Column field="numpre" header="Numpre" />
                    <Column field="tipo" header="Tipo" />
                    <Column field="qtd_parcelas_vencidas" header="QTD parcelas vencidas" />
                    <Column field="situacao" header="Situação" />
                    <template #empty>
                        Nenhum registro encontrado
                    </template>
                </DataTable>
                <div class="flex justify-content-center">
                    <Paginator ref="teste" :rows="data.informacoesData.porPagina" v-model:first="data.informacoesData.pagina"
                        :totalRecords="data.informacoesData.numeroRegistros" @page="paginacao" :alwaysShow="true">
                    </Paginator>
                    <select class="h-3rem mt-auto mb-auto ml-2 shadow-4" v-model="data.informacoesData.porPagina">
                        <option selected="true" value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>

            <div class="flex align-items-center justify-content-center m-1" style="width: 100%;">
                <div class="m-auto">
                    <div class="grid mt-5">
                        <div class="col flex align-items-center justify-content-center">
                            <Button icon="pi pi-check" label="Secondary" id="btemit" :disabled=data.desabilitaProcessamento
                                class="p-button-sm  m-3 p-button-success" @click="showTemplate()">Processar Cancelamento</Button>
                        </div>
                    </div>
                </div>
            </div>
        </Panel>
        <Panel v-if="data.processando" class="mt-5">
            <template #header>
                <b>Processamento</b>
            </template>
            <div class="flex align-items-center justify-content-center m-1" style="width: 100%;">
                <div class="m-auto">
                    <div class="field col-12 md:col-4">
                        <Knob v-model="data.valueprocessando" size="150" readonly valueTemplate="{value}%" />
                    </div>
                </div>
            </div>
        </Panel>
    </section>

    <DialogLista ref="dialogConsultaLista" @selectRow="selecionaLista" />
    <DialogProcesso ref="dialogConsultaProcesso" @selectRow="selecionaProcesso" />
</template>

<script setup>
import ConfirmDialog from "primevue/confirmdialog";
import DialogLista from "../Notificacoes/components/DialogLista.vue";
import DialogProcesso from "../../Patrimonial/Protocolo/components/Processo.vue";
import { useConfirm } from "primevue/useconfirm";
import { reactive, ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import ModalLoading from "../../Components/ModalLoading.vue";

const confirm = useConfirm();
const toast = useToast();
const dialogConsultaLista = ref(null);
const dialogConsultaProcesso = ref(null);

const data = reactive({
    loading: false,
    exibir: false,
    processando: false,
    valueprocessando: 0,
    data: [],
    desabilitaProcessamento: true,
    desabilitaPesquisa: true,
    informacoesData: {
        registroInicial: 0,
        pagina: 1,
        porPagina: 10,
        numeroRegistros: 0,
        paginaFinal: 0,
        de: 0,
        ate: 0,
        from:0
    },
    form: {
        motivo: "",
        p58_codproc: "",
        k60_codigo: "",
        resultadoPesquisa: "",
    },
    labels: {
        k00_numpre: "",
        k00_dtoper: "",
        k00_tipo: "",
        k163_data: "",
        k163_observacao: "",
        k00_numpar: "",
    },
});


onMounted(() => {
    startInterval();
});

function abrirConsultaLista() {
    dialogConsultaLista.value.toggleDialog();
}

function abrirConsultaProcesso() {
    dialogConsultaProcesso.value.toggleDialog();
}

async function startInterval() {
    var intervalo = setInterval(() => {
        window.axios
            .get("v4/api/tributario/arrecadacao/procedimentos/getprocessamentocancelamentoparcel")
            .then((res) => {
                const result = res.data.data
                if (result.processamento) {
                    data.processando = true;
                    data.desabilitaProcessamento = true;
                    data.valueprocessando = result.quantidade;
                    if (result.quantidade >= 100.00) {
                        data.valueprocessando = 100;
                    }
                } else {
                    data.processando = false;
                    data.valueprocessando = 0;
                    clearInterval(intervalo);
                }

            }).catch((error) => {
                toast.add({ severity: 'error', summary: 'Erro', detail: error.response.data.message });
                data.processando = false;
                data.valueprocessando = 0;
                clearInterval(intervalo);
            });
    }, 5000);
}

function pesquisaLista() {
    data.desabilitaProcessamento = true;
    data.data = [];
    data.exibir = false;
    data.loading = true;
    dialogConsultaLista.value
        .pesquisaGeralLista({ k60_codigo: data.form.k60_codigo })
        .then((response) => {
            data.loading = false;
            data.desabilitaPesquisa = false;
        })
        .catch((error) => {
            data.form.k60_codigo = '';
            data.loading = false;
        });
}

function pesquisaProcesso() {
    data.loading = true;
    dialogConsultaProcesso.value
        .pesquisaGeralLista({ p58_codproc: data.form.p58_codproc })
        .then((response) => {
            data.loading = false;
        })
        .catch((error) => {
            data.loading = false;
            data.form.p58_codproc = '';
        });
}

function selecionaLista(result) {
    const { k60_codigo, k60_descr } = result;
    data.form.k60_codigo = k60_codigo
    data.desabilitaPesquisa = false;
    data.desabilitaProcessamento = true;
    data.data = [];
    data.exibir = false;
}

function selecionaProcesso(result) {
    const { p58_codproc, p58_requer } = result;
    data.form.p58_codproc = p58_codproc
}

function paginacao(event) {
    data.informacoesData.pagina = event.page + 1;
    carregaResultados();
}

async function carregaResultados() {
    if (data.form.k60_codigo.toString().trim() !== "") {
        data.loading = true;
        data.data = [];
        data.exibir = false;
        const k60_codigo = data.form.k60_codigo;

        const requestBody = {
            page: Number(data.informacoesData.pagina),
            porPagina: Number(data.informacoesData.porPagina),
            k60_codigo,
        };

        pesquisaInformacoes(requestBody)
            .then((response) => {
                data.data = (response.data.data.data);
                data.informacoesData.numeroRegistros = response.data.data.total;
                data.informacoesData.pagina = response.data.data.from-1;
                if (response.data.data.data.length > 0) {
                    data.exibir = true;
                    if (!data.processando) {
                        data.desabilitaProcessamento = false;
                    }
                } else {
                    toast.add({ severity: 'error', summary: 'Erro', detail: 'Sem Débitos na Lista' });
                }

            })
            .catch((error) => {
                data.form.k60_codigo = '';
                resetInformacoesDataPaginacao();
                toast.add({ severity: 'error', summary: 'Erro', detail: error.response.data.message });
            })
            .finally(() => {
                data.loading = false;
            });
    } else {
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Informe o Codigo da Lista' });
    }
}

async function cancelparcellista() {
    if (data.form.k60_codigo.toString().trim() !== "") {
        data.loading = true;

        const k60_codigo = data.form.k60_codigo;
        const motivo = data.form.motivo;
        const processo = data.form.p58_codproc;

        const requestBody = {
            k60_codigo,
            motivo,
            processo
        };

        processar(requestBody)
            .then((response) => {
                data.data = [];
                data.exibir = false;
                startInterval();
                toast.add({ severity: 'info', summary: 'Confirmado', detail: 'Ação aceita' });
            })
            .catch((error) => {
                toast.add({ severity: 'error', summary: 'Erro', detail: error.response.data.message });
            })
            .finally(() => {
                data.loading = false;
            });
    } else {
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Informe o Codigo da Lista' });
    }
}

function pesquisaInformacoes(body) {
    return window.axios.post(
        "v4/api/tributario/notificacoes/lista/verificalista",
        body
    );
}

function processar(body) {
    return window.axios.post(
        "v4/api/tributario/arrecadacao/procedimentos/cancelparcellista",
        body
    );
}

function resetInformacoesDataPaginacao() {
    data.informacoesData.pagina = 1;
    data.informacoesData.numeroRegistros = 0;
    data.informacoesData.paginaFinal = 0;
    data.informacoesData.de = 0;
    data.informacoesData.ate = 0;
    data.exibir = false;
    data.data = [];
    data.loading = false;
}

const showTemplate = () => {
    if (data.form.k60_codigo.toString().trim() !== "") {
        confirm.require({
            header: 'Atenção',
            message: 'O cancelamento por lista é um procedimento irreversível, Deseja continuar?',
            icon: 'pi pi-exclamation-triangle',
            acceptIcon: 'pi pi-check',
            acceptClass: 'p-button-plain',
            rejectClass: 'p-button-danger',
            rejectIcon: 'pi pi-times',
            accept: () => {
                cancelparcellista();
            },
            reject: () => {
                toast.add({ severity: 'error', summary: 'Rejeitado', detail: 'Acão Rejeitada' });
            }
        });
    } else {
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Informe o Codigo da Lista' });
    }
};

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
