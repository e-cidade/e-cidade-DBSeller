<script setup>
import FiltroDemostrativoCalculo from './Components/FiltroDemostrativoCalculo.vue';
import DataTableDemostrativoCalculo from './Components/DataTableDemostrativoCalculo.vue';
import DemostrativoCalculoService from './Services/DemostrativoCalculoService';
import { ref } from 'vue';

// props
const props = defineProps(['numcgm']);

// data
const calculos = ref([]);
const isLoadingCalculos = ref(false);

// methods
const getCalculos = async (filters) => {
    isLoadingCalculos.value = true;
    try {
        filters.numcgm = props.numcgm;
        calculos.value = await DemostrativoCalculoService.getCalculos(filters);
    } catch (error) {
        alert('Erro ao buscar calculos.');
        console.error(error);
    }
    isLoadingCalculos.value = false;
}
</script>

<template>
    <div class="container mt-5">
        <FiltroDemostrativoCalculo class="mx-auto" @filter="getCalculos" :numcgm="numcgm"/>
        <Divider/>
        <DataTableDemostrativoCalculo :calculos="calculos" :isLoading="isLoadingCalculos"/>
    </div>
</template>
