<script setup>
import { computed, ref } from 'vue';
import DemostrativoCalculoService from '../Services/DemostrativoCalculoService';
import { useToast } from 'primevue/usetoast';

// toast
const toast = useToast();

// emits
const emit = defineEmits(['filter']);

// props
const props = defineProps(['numcgm']);

// data
const anosCalculados = ref([]);
const filtroAno = ref(null);

const isLoadingAnos = ref(false);
const isLoadingTiposVistorias = ref(false);

const tiposVistorias = ref([{id: 0, name:'Todos'}]);
const filtroTipoVistoria = ref(0);

const isFilterDisabled = computed(() => {
    if (!filtroAno.value) {
        return true;
    }
    return false;
});

// methods
const filter = () => {
    emit('filter', {
        exercicio: filtroAno.value,
        tipoVistoria: filtroTipoVistoria.value
    });
}

const getExercicios = async () => {
    isLoadingAnos.value = true;
    try {
        const params = {
            numcgm: props.numcgm
        }
        anosCalculados.value = await DemostrativoCalculoService.getExerciciosCalculados(params);
        if (anosCalculados.value.length > 0) {
            filtroAno.value = anosCalculados.value[0].y123_anousu;
        } else {
            toast.add({
                severity: 'warn',
                summary: 'Aviso',
                detail: 'Não há cálculos de vistoria para o cgm ' + props.numcgm,
                position: 'top'
            });
            filtroTipoVistoria.value = null;
        }
    } catch (error) {
        alert('Erro ao buscar exercícios.');
        console.error(error);
    }

    isLoadingAnos.value = false;
}

const getTiposVistorias = async () => {
    isLoadingTiposVistorias.value = true;
    try {
        const params = {
            numcgm: props.numcgm
        }
        const tiposVistoriasCalculados = await DemostrativoCalculoService.getTiposVistorias(params);
        if (tiposVistoriasCalculados.length > 0) {
            tiposVistorias.value = tiposVistorias.value.concat(tiposVistoriasCalculados);
        }
    } catch (error) {
        alert('Erro ao buscar tipos de vistoria.');
        console.error(error);
    }
    isLoadingTiposVistorias.value = false;
}

const init = async () => {
    await getExercicios();
    if (filtroAno.value) filter();
    await getTiposVistorias();
}
init();
</script>

<template>
    <Panel header="Filtro" class="w-6">
        <div class="grid">
            <Toast />
            <!-- instit -->
            <div class="col-12 flex flex-column gap-2">
                <label for="instit">Exercícios Calculados: </label>
                <Dropdown
                    v-model="filtroAno"
                    :loading="isLoadingAnos"
                    :options="anosCalculados"
                    :disabled="isFilterDisabled"
                    option-label="y123_anousu"
                    option-value="y123_anousu"/>
            </div>

            <!-- Exercicio -->
            <div class="col-12 flex flex-column gap-2">
                <label for="exercicio">Tipo de Vistoria Calculados: </label>
                <Dropdown
                    v-model="filtroTipoVistoria"
                    :options="tiposVistorias"
                    :loading="isLoadingTiposVistorias"
                    :disabled="isFilterDisabled"
                    option-label="name"
                    option-value="id"
                />
            </div>

            <div class="col-12">
                <Button label="Pesquisar"
                    icon="pi pi-search"
                    :disabled="isFilterDisabled"
                    @click="filter"
                />
            </div>
        </div>
    </Panel>
</template>
