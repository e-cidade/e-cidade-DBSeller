<template>
    <Toast position="center" />

    <Fieldset
        legend="Simples Nacional - Agendamento da atualização de cadastros"
        class="m-auto mt-5 w-max shadow-4"
    >
        <form @submit="confirmaHorario">
            <tr>
                <td>
                    <p for="dropdownFrequencia" class="text-right mr-3">
                        Frequência das atualizações:
                    </p>
                </td>
                <Dropdown
                    @change="mostraCampos"
                    name="dropdownFrequencia"
                    v-model="dados.formulario.frequenciaSelecionada"
                    :options="dados.frequencias"
                    optionLabel="nome"
                    :placeholder="dados.formulario.dropdownFrequencia"
                    class="w-full md:w-14rem shadow-4"
                />
            </tr>
            <tr v-if="dados.campos.frequencia.mensal">
                <td>
                    <p for="dropdownDiaDoMes" class="text-right mr-3">
                        Dia do mês:
                    </p>
                </td>
                <Dropdown
                    name="dropdownDiaDoMes"
                    v-model="dados.formulario.diaDoMesSelecionado"
                    :options="dados.diasDoMes"
                    optionLabel="nome"
                    :placeholder="dados.formulario.dropDownDiaDoMes"
                    class="w-full md:w-14rem shadow-4"
                />
            </tr>
            <tr v-if="dados.campos.frequencia.semanal">
                <td>
                    <p for="dropdownDiaDaSemana" class="text-right mr-3">
                        Dia da semana:
                    </p>
                </td>
                <Dropdown
                    name="dropdownDiaDaSemana"
                    v-model="dados.formulario.diaDaSemanaSelecionado"
                    :options="dados.diasDaSemana"
                    optionLabel="nome"
                    :placeholder="dados.formulario.dropDownDiaDaSemana"
                    class="w-full md:w-14rem shadow-4"
                />
            </tr>
            <tr v-if="dados.campos.frequenciaSelecionada">
                <td>
                    <p for="dropdownDiaDaSemana" class="text-right mr-3 mt-2">
                        Horário das atualizações:
                    </p>
                </td>
                <td>
                    <input
                        required
                        v-model="dados.formulario.horarioSelecionado"
                        class="h-2rem text-center w-max mt-2 shadow-4"
                        type="time"
                    />
                </td>
            </tr>
            <div class="flex">
                <Button
                    :disabled="dados.formulario.botaoBloqueado"
                    type="submit"
                    label="Confirmar"
                    class="m-auto mt-3 shadow-4"
                />
            </div>
        </form>
    </Fieldset>
</template>

<script setup>
import { useToast } from "primevue/usetoast";
import { onMounted, reactive } from "vue";

const toast = useToast();
const props = defineProps(["usuario"]);
const dados = reactive({
    campos: {
        frequenciaSelecionada: false,
        frequencia: {
            diario: false,
            semanal: false,
            mensal: false,
        },
    },
    formulario: {
        dropdownFrequencia: "Frequência",
        dropDownDiaDaSemana: "Dia da semana",
        dropDownDiaDoMes: "Dia do mês",
        horarioSelecionado: "",
        diaDoMesSelecionado: null,
        diaDaSemanaSelecionado: null,
        frequenciaSelecionada: null,
        botaoBloqueado: true,
    },
    diasDoMes: [],
    diasDaSemana: [
        { nome: "Segunda-feira", valor: 1 },
        { nome: "Terça-feira", valor: 2 },
        { nome: "Quarta-feira", valor: 3 },
        { nome: "Quinta-feira", valor: 4 },
        { nome: "Sexta-feira", valor: 5 },
        { nome: "Sábado", valor: 6 },
        { nome: "Domingo", valor: 7 },
    ],
    frequencias: [
        { nome: "Diário", valor: 1 },
        { nome: "Semanal", valor: 2 },
        { nome: "Mensal", valor: 3 },
    ],
});

onMounted(() => {
    buscaDadosApi();
    geraDiasDaSemana();
    console.log(props);
});

function geraDiasDaSemana() {
    for (let i = 1; i <= 28; i++) {
        dados.diasDoMes.push({ nome: i.toString(), valor: i });
    }
}

function mostraCampos() {
    if (dados.formulario?.frequenciaSelecionada?.valor === 1) {
        limpaCampos();
        dados.campos.frequenciaSelecionada = true;
        dados.campos.frequencia.diario = true;
    } else if (dados.formulario?.frequenciaSelecionada?.valor === 2) {
        limpaCampos();
        dados.campos.frequenciaSelecionada = true;
        dados.campos.frequencia.semanal = true;
    } else if (dados.formulario?.frequenciaSelecionada?.valor === 3) {
        limpaCampos();
        dados.campos.frequenciaSelecionada = true;
        dados.campos.frequencia.mensal = true;
    } else {
        limpaCampos();
    }
}

function confirmaHorario(event) {
    event.preventDefault();

    if (validaFormulario()) {
        enviaDadosApi();
    }
}

function limpaCampos() {
    dados.campos.frequenciaSelecionada = false;
    dados.campos.frequencia.diario = false;
    dados.campos.frequencia.semanal = false;
    dados.campos.frequencia.mensal = false;
}

function validaFormulario() {
    const frequencia = dados.campos.frequenciaSelecionada === true;

    if (frequencia) {
        return true;
    }

    return false;
}

async function enviaDadosApi() {
    const body = {};

    if (
        dados.formulario.frequenciaSelecionada?.valor &&
        dados.campos.frequenciaSelecionada
    ) {
        body.frequenciaAtualizacoes =
            dados.formulario.frequenciaSelecionada.valor;
    }

    if (
        dados.formulario.diaDoMesSelecionado?.valor &&
        dados.campos.frequencia.mensal
    ) {
        body.diaDoMes = dados.formulario.diaDoMesSelecionado?.valor;
    }

    if (
        dados.formulario.diaDaSemanaSelecionado?.valor &&
        dados.campos.frequencia.semanal
    ) {
        body.diaDaSemana = dados.formulario.diaDaSemanaSelecionado?.valor;
    }

    if (dados.formulario?.horarioSelecionado) {
        body.horario = dados.formulario?.horarioSelecionado;
    }

    return await window.axios
        .post(
            `v4/api/tributario/issqn/simples-nacional/definir-parametros-atualizacoes?idUsuario=${props.usuario}`,
            body
        )
        .then((data) => {
            dados.loading = false;
            toast.add({
                severity: "success",
                summary: "Sucesso",
                detail: "Agendamento de atualizações atualizado!",
            });
            return data;
        })
        .catch((erro) => {
            dados.loading = false;
            toast.add({
                severity: "error",
                summary: "Erro",
                detail: "Algo deu errado",
            });
        });
}

async function buscaDadosApi() {
    return await window.axios
        .get(
            `v4/api/tributario/issqn/simples-nacional/buscar-parametros-atualizacoes?idUsuario=${props.usuario}`
        )
        .then((response) => {
            mostraResultadoApi(response.data.data);
        })
        .catch((erro) => {
            dados.loading = false;
            toast.add({
                severity: "error",
                summary: "Erro",
                detail: "Algo deu errado",
            });
        });
}

function mostraResultadoApi(resultado) {
    limpaCampos();
    dados.formulario.botaoBloqueado = !resultado.usuarioAdministrador;
    dados.campos.frequenciaSelecionada = true;
    dados.formulario.frequenciaSelecionada = dados.frequencias.find(
        (item) => item.valor === resultado.frequencia
    );
    dados.formulario.diaDaSemanaSelecionado = dados.diasDaSemana.find(
        (item) => item.valor === resultado.diaDaSemana
    );
    dados.formulario.diaDoMesSelecionado = dados.diasDoMes.find(
        (item) => item.valor === resultado.diaDoMes
    );
    dados.formulario.horarioSelecionado = resultado.horario.replace(":00", "");

    switch (resultado.frequencia) {
        case 1:
            dados.campos.frequencia.diario = true;
            break;

        case 2:
            dados.campos.frequencia.semanal = true;
            break;

        case 3:
            dados.campos.frequencia.mensal = true;
            break;
    }
}
</script>
