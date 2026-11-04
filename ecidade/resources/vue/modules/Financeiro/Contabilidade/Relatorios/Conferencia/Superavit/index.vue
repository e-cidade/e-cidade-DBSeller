<script setup>
import {ref} from "vue";
import AbaPrincipal from "./Components/Principal.vue";
import AbaRecurso from "@modules/Financeiro/Contabilidade/Relatorios/Balancetes/Verificacao/AbaRecurso.vue";

const props = defineProps(['exercicio', 'instituicao', 'dataSistema']);
const dataSistema = new Date(`${props.dataSistema}T00:00:00`);

const filtro = ref({
    exercicio: props.exercicio,
    instituicao: props.instituicao,
    dataInicial: null,
    dataFinal: null,
    apresentarRecurso: null,
    instituicoes: [],
    recursos: [],
});

</script>

<template>
    <TabView class="tabview-custom">
        <TabPanel>
            <template #header>
                <i class="pi pi-filter mr-2"></i>
                <span>Principal</span>
            </template>
            <AbaPrincipal v-model="filtro" :data-sistema="props.dataSistema" ></AbaPrincipal>
        </TabPanel>
        <TabPanel>
            <template #header>
                <i class="pi pi-wallet mr-2"></i>
                <span>Recurso</span>
            </template>
            <AbaRecurso v-model="filtro.recursos" :exercicio="exercicio" :data-sistema="dataSistema"></AbaRecurso>
        </TabPanel>
    </TabView>
</template>

<style scoped>

</style>
