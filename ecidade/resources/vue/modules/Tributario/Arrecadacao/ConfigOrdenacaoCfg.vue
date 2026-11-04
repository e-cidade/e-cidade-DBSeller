<template>
    <Toast position="center" group="bc">
        <template #message="slotProps">
            <div class="flex flex-column" style="flex: 1">
                <i class="m-auto pi pi-exclamation-triangle" style="font-size: 3rem"></i>
                <p class="font-bold text-xl my-3 text-center">
                    {{ slotProps.message.summary }}
                </p>
                <p class="text-xl my-3 text-center">
                    {{ slotProps.message.detail1 }}
                </p>
            </div>
        </template>
    </Toast>
    <ModalLoading :is-loading="data.loading" />
    <Fieldset class="w-max ml-auto mr-auto mt-3 mb-3 p-3 shadow-4" legend="Pesquisar grupo de débitos">
        <form class="mb-3 w-max">
            <table class="w-max">
                <tr>
                    <td>
                        <label>
                            <a href="javascript:void(0)" @click="abrirConsultaGrupoDebitos">Grupo de débitos:</a>
                        </label>
                    </td>
                    <td class="w-auto flex">
                        <InputText class="p-inputtext-sm inputSearch m-auto shadow-4 w-2"
                            v-model="data.form.grupoDebitos" @change="pesquisaGrupoDebitos" />
                        <InputText class="p-inputtext-sm inputResult m-auto shadow-4 w-9" disabled
                            v-model="data.form.resultadoPesquisaGrupoDebitos" />
                    </td>
                </tr>
            </table>
        </form>
    </Fieldset>

    <Fieldset class="w-max ml-auto mr-auto mt-3 mb-3 p-3 shadow-4" legend="Ordenação do grupo de débitos" v-if="data.mostrarQuadroOrdenacao &&
            data.form.resultadoPesquisaGrupoDebitos != '' &&
            data.form.grupoDebitos != ''
            ">
        <form class="mb-3 w-max">
            <table class="w-max">
                <tr>
                    <td>
                        <label>
                            <a href="javascript:void(0)" @click="abrirConsultaTiposOrdenacao">Tipo de ordenação:</a>
                        </label>
                    </td>
                    <td class="w-auto flex">
                        <InputText class="p-inputtext-sm inputSearch m-auto shadow-4 w-2"
                            v-model="data.form.tipoOrdenacao" @change="pesquisaTipoOrdenacao" />
                        <InputText class="p-inputtext-sm inputResult m-auto shadow-4 w-9" disabled
                            v-model="data.form.resultadoPesquisaTipoOrdenacao" />
                    </td>
                </tr>
            </table>

            <div class="flex justify-content-left flex-wrap card-container indigo-container m-3">
                <Button class="m-auto shadow-4 bg-green-700" @click="adicionarTipoOrdenacao"><i
                        class="pi pi-plus-circle">
                        Adicionar </i></Button>
            </div>
        </form>
        <br />

        <OrderList v-model="data.tiposOrdenacao" listStyle="height:auto" dataKey="sequencial">
            <template #item="slotProps">
                <div class="p-card p-3 shadow-4 m-auto flex justify-content-evenly w-auto">
                    <span class="font-bold text-primary m-auto text-danger">
                        {{ slotProps.item.sequencial }}
                    </span>
                    <span class="text-secondary m-auto" style="width: 300px">{{
            slotProps.item.descricao
        }}</span>
                    <Button class="m-auto shadow-4 bg-red-700" @click="() => {
                removerTipoOrdenacao(slotProps.item.sequencial);
            }
            "><i class="pi pi-trash"></i></Button>
                </div>
            </template>
        </OrderList>

        <div class="flex justify-content-left flex-wrap card-container indigo-container m-3">
            <Button class="m-auto shadow-4" @click="salvarOrdenacaoDebitos"><i class="pi pi-check-square">
                    Enviar</i></Button>
        </div>
    </Fieldset>

    <ConfirmDialog />
    <DialogConsultaGrupoDebitos ref="dialogConsultaGrupoDebitos" @selectRow="selecionaGrupoDebitos" />
    <DialogConsultaTiposOrdenacaoCgf ref="dialogConsultaTiposOrdenacaoCgf" @selectRow="selecionaTiposOrdenacaoCgf" />
</template>

<script setup>
import { reactive, ref } from "vue";
import ConfirmDialog from "primevue/confirmdialog";
import ModalLoading from "../../Components/ModalLoading.vue";
import DialogConsultaGrupoDebitos from "./components/DialogConsultaGrupoDebitos.vue";
import DialogConsultaTiposOrdenacaoCgf from "./components/DialogConsultaTiposOrdenacaoCgf.vue";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";

const toast = useToast();
const dialogConsultaGrupoDebitos = ref(null);
const dialogConsultaTiposOrdenacaoCgf = ref(null);

const data = reactive({
    loading: false,
    dialogConsultaGrupoDebitos: false,
    form: {
        grupoDebitos: "",
        resultadoPesquisaGrupoDebitos: "",
        tipoOrdenacao: "",
        resultadoPesquisaTipoOrdenacao: "",
    },
    mostrarQuadroOrdenacao: false,
    tiposOrdenacao: [],
});

function setResultadoPesquisaGrupoDebito(resultado, erro = false) {
    if (erro && resultado.trim() !== "") {
        data.form.resultadoPesquisaGrupoDebitos = `Código (${resultado}) não encontrado`;
        data.mostrarQuadroOrdenacao = false;
    } else {
        data.form.resultadoPesquisaGrupoDebitos = resultado;
    }
}

function abrirConsultaGrupoDebitos() {
    dialogConsultaGrupoDebitos.value.toggleDialog();
}

function pesquisaGrupoDebitos() {
    data.loading = true;
    dialogConsultaGrupoDebitos.value
        .pesquisaGeralGrupoDebitos()
        .then((response) => {
            const grupos = response.filter(
                (item) => item.sequencial == data.form.grupoDebitos
            );
            data.loading = false;
            setResultadoPesquisaGrupoDebito(grupos[0].descricao);
            setOrdenacoes();
        })
        .catch((error) => {
            data.loading = false;
            setResultadoPesquisaGrupoDebito(data.form.grupoDebitos, true);
            data.form.grupoDebitos = "";
        });
}

function selecionaGrupoDebitos(result) {
    const { sequencial, descricao } = result;
    setResultadoPesquisaGrupoDebito(descricao);
    data.form.grupoDebitos = sequencial;
    setOrdenacoes();
}

function setResultadoPesquisaTipoOrdenacao(resultado, erro = false) {
    if (erro && resultado.trim() !== "") {
        data.form.resultadoPesquisaTipoOrdenacao = `Código (${resultado}) não encontrado`;
    } else {
        data.form.resultadoPesquisaTipoOrdenacao = resultado;
    }
}

function abrirConsultaTiposOrdenacao() {
    dialogConsultaTiposOrdenacaoCgf.value.toggleDialog();
}

function pesquisaTipoOrdenacao() {
    data.loading = true;
    dialogConsultaTiposOrdenacaoCgf.value
        .pesquisaGeralTiposOrdenacao()
        .then((response) => {
            const ordenacoes = response.filter(
                (item) => item.sequencial == data.form.tipoOrdenacao
            );
            data.loading = false;
            setResultadoPesquisaTipoOrdenacao(ordenacoes[0].descricao);
        })
        .catch((error) => {
            data.loading = false;
            setResultadoPesquisaTipoOrdenacao(data.form.tipoOrdenacao, true);
            data.form.tipoOrdenacao = "";
        });
}

function selecionaTiposOrdenacaoCgf(result) {
    const { sequencial, descricao } = result;
    setResultadoPesquisaTipoOrdenacao(descricao);
    data.form.tipoOrdenacao = sequencial;
}

async function setOrdenacoes() {
    data.loading = true;
    const response = (await buscaOrdenacoesPorGrupo()).data.data;
    data.tiposOrdenacao = await Promise.all(
        response.map(async (item) => {
            return await montaInformacoesOrdenacao(item.tipo);
        })
    );
    data.mostrarQuadroOrdenacao = true;
    data.loading = false;
}

function adicionarTipoOrdenacao() {
    if (verificarSeTipoSeOrdenacaoExiste(data.form.tipoOrdenacao)) {
        return;
    }
    dialogConsultaTiposOrdenacaoCgf.value
        .pesquisaGeralTiposOrdenacao()
        .then(async (response) => {
            const ordenacaoSelecionada = response.filter((item) => {
                return item.sequencial == data.form.tipoOrdenacao;
            })[0];
            data.tiposOrdenacao = [
                ...data.tiposOrdenacao,
                await montaInformacoesOrdenacao(
                    ordenacaoSelecionada.sequencial
                ),
            ];
        })
        .catch((error) => {
            toast.add({
                severity: "error",
                summary: "Erro",
                detail1: `Erro ao adicionar tipo de ordenação`,
                group: "bc",
            });
        });
}

function verificarSeTipoSeOrdenacaoExiste(sequencial) {
    const tiposEncontrados = data.tiposOrdenacao.filter(
        (item) => item.sequencial == sequencial
    );
    return tiposEncontrados.length >= 1;
}

function removerTipoOrdenacao(sequencial) {
    data.tiposOrdenacao = data.tiposOrdenacao.filter(
        (item) => item.sequencial != sequencial
    );
}

async function buscaOrdenacoesPorGrupo() {
    return await window.axios.get(
        `v4/api/tributario/arrecadacao/ordenacoes-cgf/buscar?cadtipo=${data.form.grupoDebitos}`
    );
}

async function montaInformacoesOrdenacao(sequencialTipoOrdenacao) {
    let ordenacaoSelecionada = null;
    await Promise.resolve(
        dialogConsultaTiposOrdenacaoCgf.value
            .pesquisaGeralTiposOrdenacao()
            .then((response) => {
                ordenacaoSelecionada = response.filter((item) => {
                    return item.sequencial == sequencialTipoOrdenacao;
                })[0];
            })
            .catch((error) => {
                toast.add({
                    severity: "error",
                    summary: "Erro",
                    detail1: `Erro ao incluir tipo de ordenação`,
                    group: "bc",
                });
            })
    );
    return ordenacaoSelecionada;
}

async function salvarOrdenacaoDebitos() {
    data.loading = true;
    const body = {
        cadtipo: data.form.grupoDebitos,
        ordenacoes: data.tiposOrdenacao.map((item) => item.sequencial),
    };
    await window.axios
        .post(`v4/api/tributario/arrecadacao/ordenacoes-cgf/adicionar`, body)
        .then(() => {
            data.loading = false;
            toast.add({
                severity: "success",
                summary: "Sucesso",
                detail1: `Ordenação salva`,
                group: "bc",
            });
        })
        .catch((error) => {
            data.loading = false;
            toast.add({
                severity: "error",
                summary: "Erro",
                detail1: `Erro ao salvar ordenação de débito`,
                group: "bc",
            });
        });
}
</script>

<style scoped></style>
