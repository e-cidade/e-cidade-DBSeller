<script setup>
import { ref } from 'vue';
import FiltroDividas from './components/FiltroDividas';
import DataTableDividas from './components/DataTableDividas';
import { useToast } from 'primevue/usetoast';

//data
const isFiltering = ref(false);
const isLoadingDividas = ref(false);
const filters = ref({});
const dividas = ref({});
const exercicios = ref({});
const selectedDividas = ref([]);
const showDataTableDividas = ref(false);
const clearSelectedDividas = ref(false);
const clearFilterExercicio = ref(false);

//services
const toast = useToast();

//methods
const setFilters = (data) => {
    filters.value.exercicio = [];
    clearFilterExercicio.value = true;
    selectedDividas.value = [];
    isFiltering.value = true;
    filters.value = data;
    filters.value.page = 1;
    getDividasExercicio();
    getDividas();
}

const setFilterExercicio = (data) => {
    selectedDividas.value = [];
    filters.value.exercicio = data;
    filters.value.page = 1;
    getDividas();
}

const setFilterPage = (data) => {
    filters.value.page = data + 1;
    getDividas();
}

const getDividas = async() => {
    isLoadingDividas.value = true;
    showDataTableDividas.value = true;
    const url = 'v4/api/tributario/divida-ativa/lancamento-historico/dividas';

    try {
        const response = await axios.get(url, {
            params: filters.value
        });
        const { data: resp } = response;
        dividas.value = resp.data ? resp.data : {data: null};
    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro ao buscar dívidas', detail: e.message, life: 5000 })
    } finally {
        isFiltering.value = false;
        isLoadingDividas.value = false;
        clearSelectedDividas.value = false;
        clearFilterExercicio.value = false;
    }
}

const getDividasExercicio = async() => {
    const urlExercicios = 'v4/api/tributario/divida-ativa/lancamento-historico/dividas-exercicios';

    try {
        const response = await axios.get(urlExercicios, {
            params: filters.value
        });
        const { data: resp } = response;
        exercicios.value = resp.data ? resp.data : {data: null};
    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro ao buscar exercícios', detail: e.message, life: 5000 })
    }
}

const getAllDividasToSelect = async() => {
    isLoadingDividas.value = true;
    const url = 'v4/api/tributario/divida-ativa/lancamento-historico/selecionar-todas-dividas';
    try {
        const response = await axios.get(url, {
            params: filters.value
        });
        const { data: resp } = response;
        selectedDividas.value = resp.data ? resp.data : {data: null};
    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro ao selecionar dívidas', detail: e.message, life: 5000 })
    } finally {
        isLoadingDividas.value = false;
    }
}

</script>

<template>
    <div class="container mt-5 p-0" style="max-width: 1200px;">
        <FiltroDividas
            @filter="setFilters"
            :isFiltering="isFiltering"
        />

        <Divider v-show="showDataTableDividas"/>

        <DataTableDividas
            v-model:selectedAllDividas="selectedDividas"
            :dividas="dividas"
            :exercicios="exercicios"
            v-if="showDataTableDividas"
            @filterExercicio="setFilterExercicio"
            @selectAllDividas="getAllDividasToSelect"
            :isLoading="isLoadingDividas"
            :filters="filters"
            @page="setFilterPage"
            @refresh="getDividas"
            :clearFilterExercicio="clearFilterExercicio"
        />
    </div>
</template>
