<script setup>
import {ref} from "vue";
import AbaPrincipal from "./AbaPrincipal.vue";
import AbaRecurso from "./AbaRecurso.vue";
import AbaOutrosFiltros from "./AbaOutrosFiltros.vue";

const props = defineProps(['exercicio', 'dataSistema']);

const dataSistema = new Date(`${props.dataSistema}T00:00:00`);

const filtro = ref({
    obrigatorio: {
        dataInicial: null,
        dataFinal: null,
        instituicoes: [],
        tipoPlano: 'ecidade'
    },
    opcionais: {
        estruturais: [],
        indicadorSuperavit: 'T',
        sistemaContas: '99',
        comEncerramento: false,
        contasComMovimento: true
    },
    outros: {
        tipo: 'A', // analitico / sintetico
        contaBancaria: 'S',
        consolidarPor: 'R',
        subtitulo: null
    },
    recursos: []
});

</script>
<template>
    <TabView class="tabview-custom">
        <TabPanel>
            <template #header>
                <i class="pi pi-filter mr-2"></i>
                <span>Principal</span>
            </template>
            <AbaPrincipal v-model="filtro" :exercicio="exercicio" :data-sistema="dataSistema"></AbaPrincipal>
        </TabPanel>
        <TabPanel>
            <template #header>
                <i class="pi pi-filter mr-2"></i>
                <span>Outros Filtros</span>
            </template>
            <AbaOutrosFiltros v-model="filtro.outros" :exercicio="exercicio"></AbaOutrosFiltros>
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
