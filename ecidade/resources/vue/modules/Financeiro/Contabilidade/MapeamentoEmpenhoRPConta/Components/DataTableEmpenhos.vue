<script setup>
import {computed, onBeforeUnmount, onMounted, ref, watch} from 'vue';
import {useToast} from 'primevue/usetoast';
import EmpenhoService from '../Services/EmpenhoService';
import {FilterMatchMode} from 'primevue/api';
import BadgeSaldo from './BadgeSaldo.vue';

// service
const toast = useToast();
const filtersTable = ref({
    global: {value: null, matchMode: FilterMatchMode.CONTAINS}
});

// props
const props = defineProps(['conta']);

// emits
const emit = defineEmits(['select']);

const contas = ['5317', '6317'];
const partEstrutural = props.conta.estrutural.substr(0, 4);

// data
const empenhos = ref([]);
const isLoadingEmpenhos = ref(false);
const selectedEmpenhos = ref([]);
const tipoLiquidacao = ref([
    {label: 'Saldo total', value: 1},
    {label: 'Saldo a liquidar', value: 3},
    {label: 'Saldo em liquidcao', value: 4}
]);
const selectedTipoLiquidacao = ref(1);
const showTipoLiquidacao = ref(true);

if ((props.conta.estrutural.substr(3, 1) != 2) || contas.includes(partEstrutural)) {
    showTipoLiquidacao.value = false;
}
const selectedTipoBusca = ref(0);

const tipoBuscaOptions = [
    {label: 'Por Lançamento', value: 0},
    {label: 'Por RP', value: 1},
];



// methods
const getListaEmpenhos = async () => {
    const exercicio = props.conta.exercicio;
    const reduzido = props.conta.reduzido;

    try {
        isLoadingEmpenhos.value = true;
        empenhos.value = await EmpenhoService.getEmpenhos(
            exercicio,
            reduzido,
            selectedTipoLiquidacao.value,
            selectedTipoBusca.value
        );
    } catch (error) {
        let msg = 'Erro interno';
        if (error.response.data.message) {
            msg = error.response.data.message
        }

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro buscar empenhos: ' + msg
        });
    } finally {
        isLoadingEmpenhos.value = false;
    }
}

// computed
const saldoTotal = computed(() => {
    if (selectedEmpenhos.value.length) {
        return selectedEmpenhos.value
            .reduce((total, emp) => total + Number(emp.saldo), 0)
            .toFixed(2)
    }

    return 0;
});

const saldoDisponivel = computed(() => {
    return (Number(props.conta.saldo_conta) - Number(props.conta.saldo_empenhos)).toFixed(2);
});

const isDisableToAdd = computed(() => {
    let disabled = true;

    if (selectedEmpenhos.value.length && saldoTotal.value <= saldoDisponivel.value) {
        disabled = false;
    }

    return disabled;
});

// hooks
onMounted(() => {
    getListaEmpenhos();
});

onBeforeUnmount(() => {
    empenhos.value = [];
    selectedEmpenhos.value = [];
});

watch(selectedTipoBusca, (newValue) => {


    if (newValue) {
        selectedTipoBusca.value = 1; // Por RP
    } else {
        selectedTipoBusca.value = 0; // Por Lançamento
    }

    getListaEmpenhos(); // Chama a função para buscar os empenhos quando mudar o tipo de busca
});

</script>

<template>
    <div class="fixed-header-table flex justify-content-between	align-items-center fixed bg-white z-1 py-3">
        <InputText v-model="filtersTable['global'].value" placeholder="Pesquisar"/>

        <Dropdown v-model="selectedTipoLiquidacao"
                  v-if="showTipoLiquidacao"
                  :options="tipoLiquidacao"
                  optionLabel="label"
                  optionValue="value"
                  placeholder="Saldo"
                  @change="getListaEmpenhos"
        />
        <Dropdown v-model="selectedTipoBusca" :options="tipoBuscaOptions" optionLabel="label"
        optionValue="value"
        placeholder="Selecione o tipo de busca"
        class="w-full md:w-12rem"
        @change="getListaEmpenhos"
        />

        <div class="flex align-items-center gap-3">
            <BadgeSaldo label="Disponivel" :saldo="saldoDisponivel"/>
            <BadgeSaldo label="Total adicionado" :saldo="saldoTotal"/>


            <Button
                label="Confirmar"
                severity="success"
                icon="pi pi-save"
                @click="emit('select', selectedEmpenhos)"
                :disabled="isDisableToAdd"
            />
        </div>
    </div>
    <DataTable
        class="mt-8"
        tableStyle="min-width: 60rem"
        :value="empenhos"
        :loading="isLoadingEmpenhos"
        v-model:selection="selectedEmpenhos"
        v-model:filters="filtersTable"
        dataKey="e60_numemp"
        :globalFilterFields="['e60_numemp', 'e60_codemp', 'z01_nome']"
    >
        <template #empty>
            Nenhum Empenho disponível para mapeamento
        </template>
        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
        <Column field="e60_numemp" header="Sequencial"></Column>
        <Column field="e60_codemp" header="Numero"></Column>
        <Column field="z01_nome" header="Credor"></Column>
        <Column field="saldo" header="Saldo">
            <template #body="slotProps">
                R$ {{ Number(slotProps.data.saldo).toLocaleString('pt-BR') }}
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
.fixed-header-table {
    font-size: 90%;
    box-shadow: 0px 10px 7px -3px rgba(0, 0, 0, 0.1);
    width: calc(100% - 3rem);
}
</style>
