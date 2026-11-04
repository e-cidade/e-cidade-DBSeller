<script setup>

import { computed, onBeforeMount, ref } from "vue";
import { useToast } from "primevue/usetoast";

import Formulario from "./Formulario";
import ModalLoading from "../../../../../Components/ModalLoading.vue";

const props = defineProps({
    modelValue: { type: Object },
    codigoRelatorio: { type: Number },
    modal: { type: Boolean, default: false },
    visible: { type: Boolean, default: false }
});

const toast = useToast();

const dialogPassThrough = {
    content: { style: 'background: #e1dede;' }
};

const isLoading = ref(false);

let relatorio = computed(() => props.modelValue);

const relatoriosIsSetted = computed(() => {
    return relatorio.value?.layout !== undefined && relatorio.value?.variaveis !== undefined;
});

async function getDadosRelatorio() {
    isLoading.value = true;
    try {
        const response = await axios.get(`v4/api/configuracao/gerador/relatorios/${props.codigoRelatorio}`);
        build(response.data.data);
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar dados do relatório',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

function build(dados) {
    relatorio.value.codigo = props.codigoRelatorio;
    relatorio.value.grupo = dados.grupo.codigo;
    relatorio.value.tipo = dados.tipo.codigo;
    relatorio.value.origem = dados.origem;
    relatorio.value.campos = dados.campos.configurados;
    relatorio.value.ordem = dados.ordem.configurados;
    relatorio.value.layout = dados.layout;
    relatorio.value.variaveis = dados.variaveis;
    relatorio.value.filtros = dados.filtros;
    relatorio.value.tipoVisualizacao = dados.tipoVisualizacao;

    if (dados.origem == 1) {
        relatorio.value.sql =  dados.sql;
    } else {
        relatorio.value.visao =  dados.visao;
    }
}

onBeforeMount(async () => {
    if (relatorio.value === undefined && props.codigoRelatorio) {
        relatorio = ref({});
        return getDadosRelatorio();
    }
});

</script>

<template>
    <template v-if="relatoriosIsSetted">
        <Dialog v-if="modal"
                :closeOnEscape="false"
                :header="relatorio.layout.nome"
                :modal="true"
                :pt="dialogPassThrough"
                :visible="visible"
                @update:visible="value => $emit('update:visible', value)">
            <Formulario :modelValue="relatorio"></Formulario>
        </Dialog>
        <section v-else class="flex flex-column w-full mt-4 gap-2">
            <section class="flex justify-content-center">
                <Panel :header="relatorio.layout.nome" class="w-full lg:w-5">
                    <Formulario :modelValue="relatorio"></Formulario>
                </Panel>
            </section>
        </section>

        <ModalLoading :isLoading="isLoading"/>
    </template>
</template>

<style scoped>

</style>
