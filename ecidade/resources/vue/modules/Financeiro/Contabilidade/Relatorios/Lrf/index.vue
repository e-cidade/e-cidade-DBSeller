<script setup>
import {ref} from "vue";
import AbaPrincipal from "./Components/Principal.vue";
import ConfiguracaoRelatorioLegal
    from "@modules/Financeiro/Contabilidade/Relatorios/Lrf/Components/ConfiguracaoRelatorioLegal.vue";
import NotaExplicativa from "@modules/Financeiro/Contabilidade/Relatorios/Lrf/Components/NotaExplicativa.vue";

const props = defineProps(['tipo', 'anexo', 'exercicio', 'instituicao', 'login', 'consolidado']);

const filtro = ref({
    abasDisabilitada: true,
    exercicio: props.exercicio,
    instituicao: props.instituicao,
    instituicoes: [],
    relatorio: null,
    periodo: null,
});

</script>

<template>
    <TabView class="tabview-custom">
        <TabPanel>
            <template #header>
                <i class="pi pi-filter mr-2"></i>
                <span>Principal</span>
            </template>
            <AbaPrincipal v-model="filtro" :tipo="tipo" :anexo="anexo" :exercicio="exercicio"
                          :login="login" :consolidado="consolidado"></AbaPrincipal>
        </TabPanel>

        <TabPanel :disabled="filtro.abasDisabilitada">
            <template #header>
                <i class="pi pi-filter mr-2"></i>
                <span>Configuração</span>
            </template>
            <ConfiguracaoRelatorioLegal :tipo="tipo" :exercicio="exercicio" :instituicao="instituicao"
                                        :relatorio="filtro.relatorio" :periodo="filtro.periodo">
            </ConfiguracaoRelatorioLegal>
        </TabPanel>
        <TabPanel :disabled="filtro.abasDisabilitada">
            <template #header>
                <i class="pi pi-file-edit mr-2"></i>
                <span>Nota Explicativa/Fonte</span>
            </template>
            <NotaExplicativa :tipo="tipo" :exercicio="exercicio" :instituicao="instituicao"
                             :relatorio="filtro.relatorio" :periodo="filtro.periodo"></NotaExplicativa>
        </TabPanel>
    </TabView>
</template>

<style scoped>

</style>
