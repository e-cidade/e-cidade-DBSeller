<script setup>
import { ref, watch, computed } from 'vue';
import FormAdicionarHistorico from './FormAdicionarHistorico';
import { useToast } from 'primevue/usetoast';

//emits
const emits = defineEmits(['filterExercicio', 'page', 'refresh', 'selectAllDividas', 'update:selectedAllDividas']);

//props
const props = defineProps(['dividas', 'isLoading', 'filters', 'clearFilterExercicio', 'exercicios', 'selectedAllDividas']);

//services
const toast = useToast();

//data
const isOpenFormHistorico = ref(false);
const isOpenFormAddHistorico = ref(false);
const selectedDividaHistorico = ref({historico: null, codDivida: null});
const isLoadingSave = ref(false);
const selectedFirstExercicio = ref('');
const selectedSecondExercicio = ref('');
const currentPage = ref(0);

//computed
const selectedDividas = computed({
    get: () => props.selectedAllDividas,
    set: (value) => {
        emits('update:selectedAllDividas', value);
    }
});

const allSelected = computed(() => {
    return selectedDividas.value.length === props.dividas.total;
});

//methods
const setFilterExercicio = () => {
    if (selectedSecondExercicio.value && selectedFirstExercicio.value > selectedSecondExercicio.value) {
        toast.add({severity: 'warn', summary: 'O período inicial deve ser menor ou igual ao período final', life: 5000 })
        return;
    };
    const exerciciosFiltrados = [selectedFirstExercicio.value, selectedSecondExercicio.value];
    emits('filterExercicio', exerciciosFiltrados);
}

const clearFilterExercicio = () => {
    selectedFirstExercicio.value = '';
    selectedSecondExercicio.value = '';
    emits("filterExercicio", []);
}

const setOrigem = (dadosDividas) => {
    let origem = '';
    if (dadosDividas.numcgm) {
        origem += `C - ${dadosDividas.numcgm}<br>`;
    }
    if (dadosDividas.inscricao) {
        origem += `I - ${dadosDividas.inscricao}<br>`;
    }
    if (dadosDividas.matricula) {
        origem += `M - ${dadosDividas.matricula}<br>`;
    }

    return origem;
}

const removeDelimitadorProcforo = (dadosDividas) => {
    return dadosDividas.procforo.replace(/,/g, '');
}

const changePage = (dadosPagina) => {
    emits("page", dadosPagina.page);
}

const openHistorico = (divida) => {
    selectedDividaHistorico.value.codDivida = divida.codDivida;
    selectedDividaHistorico.value.historico = divida.historico;
    isOpenFormHistorico.value = true;
}

const selectAll = () => {
    if (allSelected.value) {
        selectedDividas.value = [];
        return;
    }

    emits("selectAllDividas");
}

watch(() => props.clearFilterExercicio, (value) => {
    if (value === true) {
        selectedFirstExercicio.value = '';
        selectedSecondExercicio.value = '';
    }
});
</script>

<template>
    <Dialog
        v-model:visible="isOpenFormAddHistorico"
        position="top"
        :modal="true"
        :draggable="false"
        header="Adicionar Histórico de Divida"
    >
        <FormAdicionarHistorico
            :selectedDividas="selectedDividas"
            @close="isOpenFormAddHistorico = false"
            @saved="$emit('refresh')"
            :isLoadingSave="isLoadingSave"
        />
    </Dialog>

    <!-- historico -->
    <Dialog
        v-model:visible="isOpenFormHistorico"
        position="top"
        :modal="true"
        :draggable="false"
        :header="`Histórico Dívida - ${selectedDividaHistorico.codDivida}`"
    >

        <Textarea
            v-model="selectedDividaHistorico.historico"
            autoResize rows="8" cols="70"
            class="mt-5 border border-gray-300 rounded-lg w-full"
            readonly
        />
    </Dialog>

    <!-- dividas -->
    <DataTable
        :value="dividas.data"
        v-model:selection="selectedDividas"
        @select-all-change="selectAll"
        :selectAll="allSelected"
        scrollable
        :loading="isLoading"
        style="width: 1200px;"
        dataKey="codDivida"
    >
        <template #header>
            <div class="flex justify-content-between">
                <Button
                    icon="pi pi-plus"
                    label="Adicionar Histórico"
                    raised
                    :disabled="(selectedDividas.length > 0) ? false : true"
                    @click="isOpenFormAddHistorico = true"
                    style="height: 35px!important;"
                />
                <div class="flex gap-2 relative">
                    <Dropdown
                        v-model="selectedFirstExercicio"
                        :options="exercicios"
                        class="w-full w-10rem"
                        placeholder="A partir de..."
                        @change="setFilterExercicio"
                    />

                    <Dropdown
                        v-model="selectedSecondExercicio"
                        :options="exercicios"
                        class="w-full w-10rem"
                        placeholder="Até..."
                        @change="setFilterExercicio"
                    />

                    <Button
                        icon="pi pi-times"
                        severity="danger"
                        text
                        rounded
                        aria-label="Cancel"
                        @click="clearFilterExercicio"
                        v-if="selectedFirstExercicio != '' || selectedSecondExercicio != ''"
                        class="relative"
                    />
                </div>
            </div>
        </template>
        <Column selectionMode="multiple" headerStyle="width: 3em"></Column>
        <Column field="origem" header="Origem">
            <template #body="slotProps">
                <div v-html="setOrigem(slotProps.data)"/>
            </template>
        </Column>
        <Column field="codDivida" header="Código da Dívida" style="text-align: center"></Column>
        <Column field="exercicio" header="Exercício" style="text-align: center"></Column>
        <Column field="numpre" header="Numpre" style="text-align: center"></Column>
        <Column field="numpar" header="Parcela" style="text-align: center"></Column>
        <Column field="procedencia" header="Procedência" style="text-align: center"></Column>
        <Column field="descricao" header="Descrição"></Column>
        <Column field="cda" header="Nº da CDA" style="text-align: center"></Column>
        <Column field="inicial" header="Nº da Inicial" style="text-align: center"></Column>
        <Column field="procforo" header="Nº Processo" style="text-align: center">
            <template #body="slotProps">
                {{ removeDelimitadorProcforo(slotProps.data) }}
            </template>
        </Column>
        <Column field="historico" header="Histórico" style="text-align: center">
            <template #body="slotProps">
                <Button @click="openHistorico(slotProps.data)" icon="pi pi-eye" rounded outlined/>
            </template>
        </Column>

        <template #empty>
            Nenhum registro encontrado
        </template>

        <template #footer>
            <Paginator
                v-if="dividas.total > dividas.per_page"
                :rows="dividas.per_page"
                :totalRecords="dividas.total"
                @page="changePage"
            />
        </template>
    </DataTable>
</template>

<style scoped>
:deep(.p-dropdown) {
    border: 1px solid #bdbdbd;
    font-size: 0.9rem;
    height: 35px;
}
</style>
