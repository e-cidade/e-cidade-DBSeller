<template>
    <Toast position="center" />

    <Fieldset
        legend="Parâmetros - Cemitério"
        class="m-auto mt-5 w-max shadow-4"
    >
        <form @submit="alteraParametros">
            <tr>
                <td>
                    <p for="dropdownFrequencia" class="text-right mr-3">
                        Obrigatoriedade de taxa de Sepultamento:
                    </p>
                </td>
                <Dropdown
                    name="dropdownFrequencia"
                    optionLabel="name"
                    class="w-full md:w-14rem shadow-4"
                    :options="opcoesObrigatoriedadeTaxaSepultamento"
                    v-model="dadosFormulario.obrigatoriedadeTaxaSepultamento"
                />
            </tr>
            <div class="flex">
                <Button
                    type="submit"
                    label="Alterar"
                    class="m-auto mt-3 shadow-4"
                />
            </div>
        </form>
    </Fieldset>
</template>

<script setup>
import { onMounted, reactive } from "vue";
import { useToast } from "primevue/usetoast";

const toast = useToast();
const props = defineProps(["ano"]);

const rotasApi = {
    alteraParametros: "v4/api/tributario/cemiterio/parametros",
};

const opcoesObrigatoriedadeTaxaSepultamento = [
    { name: "Não", value: "false" },
    { name: "Sim", value: "true" },
];

const dadosFormulario = reactive({
    obrigatoriedadeTaxaSepultamento: null,
    ano: props.ano,
});

onMounted(() => {
    buscaParametros();
});

function buscaParametros() {
    window.axios
        .get(rotasApi.alteraParametros + "/busca-parametros?ano=" + props.ano)
        .then((respostaApi) => {
            try {
                if (Number(respostaApi.status) !== 200) {
                    mostraMensagemDeErro(
                        "Erro ao buscar parâmetros, tente novamente mais tarde."
                    );
                    return;
                }

                const corpoRespostaApi = respostaApi.data[0];

                dadosFormulario.obrigatoriedadeTaxaSepultamento =
                    buscaOpcaoObrigatoriedadeTaxaSepultamento(
                        corpoRespostaApi.cem36_obrigatoriedadetaxasepultamento.toString()
                    );
            } catch (erro) {
                mostraMensagemDeErro(
                    "Erro ao buscar parâmetros, tente novamente mais tarde."
                );
                return;
            }
        });
}

function buscaOpcaoObrigatoriedadeTaxaSepultamento(valor) {
    return opcoesObrigatoriedadeTaxaSepultamento.find(
        (item) => item.value.toString() === valor
    );
}

function alteraParametros(event) {
    event.preventDefault();

    const bodyRequisicao = {
        obrigatoriedadeTaxaSepultamento:
            dadosFormulario.obrigatoriedadeTaxaSepultamento?.value,
        ano: dadosFormulario.ano
    };

    window.axios
        .patch(
            rotasApi.alteraParametros + "/alteracao-parametros",
            bodyRequisicao
        )
        .then((respostaApi) => {
            if (Number(respostaApi.status) !== 200) {
                mostraMensagemDeErro("Erro ao alterar parâmetros.");
                return;
            }

            console.log(respostaApi);

            mostraMensagemDeSucesso("Parâmetros alterados.");
        });
}

function mostraMensagemDeSucesso(mensagem) {
    toast.add({
        severity: "success",
        summary: "Sucesso",
        detail: mensagem,
    });
}

function mostraMensagemDeErro(mensagem) {
    toast.add({
        severity: "error",
        summary: "Erro",
        detail: mensagem,
    });
}
</script>
