<template>
    <Toast position="center" />

    <Fieldset legend="Grupo de Taxas" class="m-auto mt-5 w-max shadow-4">
        <form>
            <table>
                <tr id="opcaoAcao">
                    <td>
                        <p class="text-right m-auto mr-3" style="padding: 10px">
                            Opção:
                        </p>
                    </td>
                    <Dropdown
                        optionLabel="name"
                        class="w-full md:w-14rem shadow-4"
                        :options="novoGrupo"
                        v-model="dadosFormulario.novoGrupo"
                        @change="toggleOpcao"
                    />
                </tr>

                <tr id="sequencialGrupo">
                    <td
                        class="flex justify-content-end h-auto"
                        style="padding: 10px"
                    >
                        <a
                            class="mt-2 mr-3"
                            href="javascript:void(0)"
                            @click="abrirConsultaGrupoTaxas"
                            >Código:</a
                        >
                    </td>
                    <td>
                        <InputText
                            class="p-inputtext-sm inputSearch m-auto shadow-4 w-2"
                            v-model="dadosFormulario.sequencial"
                            @change="pesquisaGrupoTaxas"
                        />
                        <InputText
                            class="p-inputtext-sm inputSearch m-auto shadow-4"
                            v-model="dadosFormulario.descricaoGrupoTaxas"
                            disabled="true"
                        />
                    </td>
                </tr>

                <tr id="sequencialProcedencia">
                    <td class="flex justify-content-end" style="padding: 4px">
                        <a
                            class="mr-3 mt-2"
                            href="javascript:void(0)"
                            @click="abrirConsultaProcedencia"
                            >Procedência principal:</a
                        >
                    </td>
                    <td>
                        <InputText
                            class="p-inputtext-sm inputSearch m-auto shadow-4 w-2"
                            v-model="dadosFormulario.procedencia"
                            @change="pesquisaProcedencia"
                        />
                        <InputText
                            class="p-inputtext-sm inputSearch m-auto shadow-4"
                            v-model="dadosFormulario.descricaoProcedencia"
                            disabled="true"
                        />
                    </td>
                </tr>

                <tr id="descricaoGrupo">
                    <td>
                        <p class="text-right mr-3">Descrição:</p>
                    </td>
                    <td>
                        <InputText
                            :useGrouping="false"
                            placeholder=""
                            class="padding-custom p-inputtext-sm shadow-4 w-full md:w-14rem shadow-4"
                            v-model="dadosFormulario.descricao"
                        />
                    </td>
                </tr>

                <tr id="origemGrupo">
                    <td>
                        <p class="text-right m-auto mr-3">Origem:</p>
                    </td>
                    <Dropdown
                        optionLabel="name"
                        class="w-full md:w-14rem shadow-4"
                        :options="origens"
                        v-model="dadosFormulario.origem"
                    />
                </tr>

                <tr id="dataLimite">
                    <td>
                        <p class="text-right mr-3">Data limite:</p>
                    </td>
                    <td>
                        <Calendar
                            placeholder=""
                            :useGrouping="false"
                            class="padding-custom p-inputtext-sm shadow-4 w-full md:w-14rem shadow-4"
                            v-model="dadosFormulario.dataLimite"
                        />
                    </td>
                </tr>
            </table>
            <div class="flex">
                <Button
                    id="botaoAdicionar"
                    type="button"
                    label="Incluir"
                    class="m-auto mt-3 shadow-4"
                    @click="enviarFormulario"
                />
                <Button
                    id="botaoAlterar"
                    type="button"
                    label="Alterar"
                    class="m-auto mt-3 shadow-4"
                    @click="enviarFormulario"
                />
            </div>
        </form>
    </Fieldset>

    <DialogConsultaProcedencia
        ref="dialogConsultaProcedencia"
        @selectRow="selecionaProcedencia"
    />

    <DialogConsultaGrupoTaxas
        ref="dialogConsultaGrupoTaxas"
        @selectRow="selecionaGrupoTaxas"
    />
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import DialogConsultaProcedencia from "./components/DialogConsultaProcedencia.vue";
import DialogConsultaGrupoTaxas from "./components/DialogConsultaGrupoTaxas.vue";

const toast = useToast();
const dialogConsultaProcedencia = ref(null);
const dialogConsultaGrupoTaxas = ref(null);

const rotasApi = {
    adicionarGrupo: "v4/api/tributario/arrecadacao/grupotaxas/adicionar",
    editarGrupo: "v4/api/tributario/arrecadacao/grupotaxas/editar",
};

const novoGrupo = [
    { name: "Incluir", value: "true" },
    { name: "Alterar", value: "false" },
];

const origens = [
    { name: "Todos", value: 1 },
    { name: "CGM", value: 2 },
    { name: "Matrícula", value: 3 },
    { name: "Inscrição", value: 4 },
];

const dadosFormulario = reactive({
    novoGrupo: null,
    sequencial: "",
    descricao: "",
    procedencia: "",
    origem: "",
    dataLimite: "",
    descricaoProcedencia: "",
    descricaoGrupoTaxas: "",
    dialogConsultaProcedencia: false,
    dialogConsultaGrupoTaxas: false,
    loading: true,
});

onMounted(() => {
    toggleOpcao();
});

function abrirConsultaProcedencia() {
    dialogConsultaProcedencia.value.toggleDialog();
}

function pesquisaProcedencia() {
    dialogConsultaProcedencia.value
        .pesquisaGeralProcedencia({ sequencial: dadosFormulario.procedencia })
        .then((response) => {
            setResultadoPesquisaProcedencia(
                response.data.data.data[0].dv09_descr
            );
        })
        .catch((erro) => {
            setResultadoPesquisaProcedencia("", true);
        });
}

function selecionaProcedencia(resultado) {
    setResultadoPesquisaProcedencia(resultado.dv09_descr);
    dadosFormulario.procedencia = resultado.dv09_procdiver;
}

function setResultadoPesquisaProcedencia(resultado) {
    if (resultado.trim() === "") {
        dadosFormulario.descricaoProcedencia = `Código (${dadosFormulario.procedencia}) não encontrado`;
        dadosFormulario.procedencia = "";
    } else {
        dadosFormulario.descricaoProcedencia = resultado;
    }
}

function abrirConsultaGrupoTaxas() {
    dialogConsultaGrupoTaxas.value.toggleDialog();
}

function pesquisaGrupoTaxas() {
    dialogConsultaGrupoTaxas.value
        .pesquisaGeralGrupoTaxas({ sequencial: dadosFormulario.sequencial })
        .then(async (response) => {
            const {
                ar55_datalimite,
                ar55_descricao,
                ar55_origem,
                ar55_procedenciaprinc,
            } = response.data.data.data[0];

            dadosFormulario.descricao = ar55_descricao;
            dadosFormulario.origem = origens.find(
                (item) => Number(item.value) === Number(ar55_origem)
            );
            dadosFormulario.dataLimite = formataData(ar55_datalimite);
            dadosFormulario.procedencia = ar55_procedenciaprinc;

            pesquisaProcedencia();

            setResultadoPesquisaGrupoTaxas(ar55_descricao);
        })
        .catch((erro) => {
            setResultadoPesquisaGrupoTaxas("", true);
        });
}

function selecionaGrupoTaxas(resultado) {
    const {
        ar55_sequencial,
        ar55_datalimite,
        ar55_descricao,
        ar55_origem,
        ar55_procedenciaprinc,
    } = resultado;

    dadosFormulario.descricao = ar55_descricao;
    dadosFormulario.origem = origens.find(
        (item) => Number(item.value) === Number(ar55_origem)
    );
    dadosFormulario.dataLimite = formataData(ar55_datalimite);
    dadosFormulario.procedencia = ar55_procedenciaprinc;

    pesquisaProcedencia();
    setResultadoPesquisaGrupoTaxas(ar55_descricao);
    dadosFormulario.sequencial = ar55_sequencial;
}

function setResultadoPesquisaGrupoTaxas(resultado) {
    if (resultado.trim() === "") {
        dadosFormulario.descricaoGrupoTaxas = `Código (${dadosFormulario.sequencial}) não encontrado`;
        dadosFormulario.sequencial = "";
    } else {
        dadosFormulario.descricaoGrupoTaxas = resultado;
    }
}

function toggleOpcao() {
    const botaoAdicionar = document.getElementById("botaoAdicionar");
    const botaoAlterar = document.getElementById("botaoAlterar");
    const sequencialGrupo = document.getElementById("sequencialGrupo");
    const descricaoGrupo = document.getElementById("descricaoGrupo");
    const origemGrupo = document.getElementById("origemGrupo");
    const dataLimite = document.getElementById("dataLimite");
    const sequencialProcedencia = document.getElementById(
        "sequencialProcedencia"
    );

    if (dadosFormulario.novoGrupo) {
        const adicionar = dadosFormulario.novoGrupo.value == "true";

        if (adicionar) {
            botaoAdicionar.classList.remove(["hidden"]);
            botaoAlterar.classList.add(["hidden"]);
            sequencialGrupo.classList.add(["hidden"]);
            sequencialProcedencia.classList.remove(["hidden"]);
            descricaoGrupo.classList.remove(["hidden"]);
            origemGrupo.classList.remove(["hidden"]);
            dataLimite.classList.remove(["hidden"]);
            return;
        } else {
            botaoAdicionar.classList.add(["hidden"]);
            botaoAlterar.classList.remove(["hidden"]);
            sequencialGrupo.classList.remove(["hidden"]);
            sequencialProcedencia.classList.remove(["hidden"]);
            descricaoGrupo.classList.remove(["hidden"]);
            origemGrupo.classList.remove(["hidden"]);
            dataLimite.classList.remove(["hidden"]);
            return;
        }
    }
    botaoAdicionar.classList.add(["hidden"]);
    botaoAlterar.classList.add(["hidden"]);
    sequencialGrupo.classList.add(["hidden"]);
    sequencialProcedencia.classList.add(["hidden"]);
    descricaoGrupo.classList.add(["hidden"]);
    origemGrupo.classList.add(["hidden"]);
    dataLimite.classList.add(["hidden"]);
    return;
}

async function enviarFormulario() {
    const adicionar = dadosFormulario.novoGrupo.value == "true";

    if (adicionar) {
        await adicionarGrupoTaxa();
    } else {
        await editarGrupoTaxa();
    }
}

async function adicionarGrupoTaxa() {
    if (validarCampos()) {
        const resultado = await window.axios.post(
            rotasApi.adicionarGrupo,
            getDadosGrupo()
        );

        mostraMensagemDeSucesso("Adicionado com sucesso!");

        return resultado;
    }
}

async function editarGrupoTaxa() {
    const resultado = await window.axios.patch(
        rotasApi.editarGrupo + `?sequencial=${dadosFormulario.sequencial}`,
        getDadosGrupo()
    );

    mostraMensagemDeSucesso("Alterado com sucesso!");

    return resultado;
}

function validarCampos() {
    try {
        if (
            !dadosFormulario.descricao ||
            dadosFormulario.descricao.toString().trim() == ""
        ) {
            throw new Error("Descrição");
        }

        if (
            !dadosFormulario.procedencia ||
            dadosFormulario.procedencia.toString().trim() == ""
        ) {
            throw new Error("Procedência");
        }

        if (
            !dadosFormulario.origem ||
            dadosFormulario.origem.toString().trim() == ""
        ) {
            throw new Error("Origem");
        }

        // if (
        //     !dadosFormulario.dataLimite ||
        //     dadosFormulario.dataLimite.toString().trim() == ""
        // ) {
        //     throw new Error("Data limite");
        // }

        return true;
    } catch (erro) {
        mostraErroCampoNaoInformado(erro.message);
        return false;
    }
}

function mostraErroCampoNaoInformado(campo) {
    const mensagem = `Campo ${campo} deve ser informado.`;

    toast.add({
        severity: "error",
        summary: "Erro",
        detail: mensagem,
    });
}

function mostraMensagemDeSucesso(mensagem) {
    toast.add({
        severity: "success",
        summary: "Sucesso",
        detail: mensagem,
    });
}

function getDadosGrupo() {
    return {
        descricao: dadosFormulario.descricao,
        procedenciaprinc: dadosFormulario.procedencia,
        origem: Number(dadosFormulario.origem.value),
        datalimite: dadosFormulario.dataLimite,
    };
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
</script>
