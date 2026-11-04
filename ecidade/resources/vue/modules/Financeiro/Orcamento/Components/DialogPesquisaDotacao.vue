<script setup>
import {computed, onMounted, ref, watch} from "vue";
import ModalLoading from "../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
import {formatCurrency} from "../../../../utils/Strings";
import AutoCompleteNaturezaDespesa from "./AutoCompleteNaturezaDespesa";

const toast = useToast();
const props = defineProps(['modelValue', 'visible', 'pesquisaReduzido', 'exercicio', 'instituicao']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

/**
 * Declaração das computed
 */
const filtrar = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});
// computed "readonly"
const pesquisaReduzido = computed(() => props.pesquisaReduzido);

// variáveis da grid
const dadosGrid = ref({
    "dotacoes": [],
    "selected": null,
    "loadinGrid": false,
    "totalRecords": 0
});

const lazyParams = ref({
    page: 1,
    rows: 15,
    sortField: null,
    sortOrder: null
});

const reduzido = ref();
const elemento = ref();

const rota = 'v4/api/financeiro/orcamento/despesa/dotacoes';

const buscar = async () => {
    const parameters = {
        page: 1,
        rows: 15,
    }

    await loadLazyData(parameters);
};

const loadLazyData = async (parameters) => {
    parameters = {
        ...parameters,
        exercicio: props.exercicio,
        instituicao: props.instituicao
    }

    if (elemento.value) {
        parameters.elemento = elemento.value.codigo
    }

    if (reduzido.value) {
        parameters.reduzido = reduzido.value
    }

    dadosGrid.value.loadinGrid = true;
    await window.axios.get(rota, {'params': parameters}).then(response => {
        dadosGrid.value.totalRecords = response.data.data.totalRegistros;
        dadosGrid.value.dotacoes = response.data.data.dotacoes;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        dadosGrid.value.loadinGrid = false;
    });
};


const onRowSelect = (event) => {
    filtrar.value = event.data;
    emit('update:visible', false);
};

const onSort = (event) => {
    lazyParams.value = event;
    lazyParams.value.sortOrder = event.sortOrder === 1 ? 'asc' : 'desc';
    loadLazyData({...lazyParams.value});
};

const onPage = (event) => {
    event.page += 1;
    lazyParams.value = event;
    loadLazyData({...lazyParams.value});
};

const executaPesquisa = async (pesquisaReduzido) => {
    reduzido.value = pesquisaReduzido
    await buscar();
    if (!dadosGrid.value.dotacoes.length) {
        filtrar.value = {};
        return
    }

    filtrar.value = {...dadosGrid.value.dotacoes[0]};
};

function limparCampos() {
    reduzido.value = null;
    elemento.value = null;
}

watch(pesquisaReduzido, async (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
        await executaPesquisa(newValue);
    }
});

onMounted(async() => {
    if (pesquisaReduzido.value) {
        await executaPesquisa(pesquisaReduzido.value);
    }
});
</script>

<template>
    <Dialog :visible="visible" modal header="Pesquisa de Dotações"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized" :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full gap-2">
            <section class="flex justify-content-center">
                <Panel header="Filtros" class="w-full md:w-11 lg:w-8 xl:w-6 ">
                    <div class="formgrid grid mt-4 row-gap-2">

                        <div class="field col-12 ">
                            <AutoCompleteNaturezaDespesa v-model="elemento" :exercicio="exercicio" :apenasComDotacao="true" />
                        </div>
                        <div class="field col-12 md:col-4 lg:col-6 xl:col-3 ">
                            <div class="p-float-label">
                                  <InputNumber id="input-reduzido" v-model="reduzido" :useGrouping="false"
                                               class="w-6rem" :pt="{input:{class: 'w-6rem'}}"/>
                                <label for="input-reduzido">Reduzido</label>
                             </div>
                        </div>

                        <div class="field col-12 md:col-8 lg:col-6 xl:col-9 flex sm:justify-content-end justify-content-center">
                            <Button class="m-1 p-button-success shadow-4" icon="pi pi-search" @click="buscar"/>
                            <Button class="m-1 shadow-4" icon="pi pi-undo" @click="limparCampos"/>
                        </div>
                    </div>
                </Panel>
            </section>
        </section>
        <section class="flex justify-content-center mt-1">
            <DataTable v-model:selection="dadosGrid.selected" :value="dadosGrid.dotacoes" dataKey="coddot"
                       selectionMode="single"
                       @rowSelect="onRowSelect"
                       lazy paginator
                       :rows="15"
                       @sort="onSort($event)"
                       :totalRecords="dadosGrid.totalRecords"
                       @page="onPage($event)"
                       :loading="dadosGrid.loadinGrid"
                       :metaKeySelection="false" tableStyle="min-width: 50rem" class="w-full">
                <template #empty>
                    Para carregar as dotações, clique em Buscar.
                </template>
                <template #loading>Buscando dotações...</template>

                <Column field="funcionalProgramatica" header="Funcional Programática"></Column>
                <Column field="reduzido" header="Reduzido" sortable></Column>
                <Column field="projeto" header="Projeto" sortable>
                    <template #body="slotProps">
                        {{ `${slotProps.data.projeto.codigo} - ${slotProps.data.projeto.descricao}` }}
                    </template>
                </Column>
                <Column field="elemento" header="Elemento" sortable>
                    <template #body="slotProps">
                        {{ `${slotProps.data.elemento.elemento} - ${slotProps.data.elemento.descricao}` }}
                    </template>
                </Column>
                <Column field="recurso" header="Recurso" sortable>
                    <template #body="slotProps">
                        {{ slotProps.data.recurso.apresentacao }}
                    </template>
                </Column>
                <Column field="saldoInicial" header="Saldo Inicial" sortable>
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.saldoInicial) }}
                    </template>
                </Column>
            </DataTable>
        </section>
        <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    </Dialog>
</template>


<style scoped>

.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}
</style>
