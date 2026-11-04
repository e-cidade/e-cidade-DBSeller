<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";

const toast = useToast();
const props = defineProps(['modelValue', 'visible', 'instituicao', 'exercicio', 'codigo']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

/**
 * Declaração das computed
 */
const filtrarHistorico = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});
// computed "readonly"
const codigoHistorico = computed(() => props.codigo);

// declaração das ref
const dadosGrid = ref({
    "historicos": [],
    "selected": null,
    "loadingGrid": false,
    "totalRecords": 0
});

// filtros
const inputCodigo = ref()
const nome = ref()

let rota = 'v4/api/financeiro/contabilidade/historico';

const buscar = async () => {
    const parameters = {
        page: 1,
        rows: 15,
    }

    await loadLazyData(parameters);
}

async function loadLazyData(parameters) {
    if (inputCodigo.value) {
        parameters.codigo = inputCodigo.value;
    }

    if (nome.value) {
        parameters.nome = nome.value;
    }

    dadosGrid.value.loadingGrid = true;
    try {
        const response = await axios.get(rota, {'params': parameters});
        dadosGrid.value.totalRecords = response.data.data.totalRegistros;
        dadosGrid.value.historicos = response.data.data.historicos;
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }
    dadosGrid.value.loadingGrid = false;
}

async function executaPesquisa(codigo) {

    inputCodigo.value = codigo;
    await buscar();
    if (!dadosGrid.value.historicos.length) {
        filtrarHistorico.value = {};
    }

    filtrarHistorico.value = {...dadosGrid.value.historicos[0]};
}

/**
 * Evento de seleção da linha da grid.
 * @param event
 */
const onRowSelect = (event) => {
    filtrarHistorico.value = event.data;
    emit('update:visible', false);
};

const onSort = (event) => {
    event.sortOrder = event.sortOrder === 1 ? 'asc' : 'desc';
    loadLazyData({...event});
};

const onPage = (event) => {
    event.page += 1;
    loadLazyData({...event});
};

/**
 * Limpa os dados do formulário de pesquisa formulário
 */
function limpar() {
    inputCodigo.value = null;
    nome.value = null;
}

/**
 * Implementa a busca pela digitação do código
 */
watch(codigoHistorico, async (newValue, oldValue) => {

    if ((newValue === undefined) || !newValue || (newValue === oldValue)) {
        return;
    }

    console.log('DialogPesquisaHistorico watch histórico ', codigoHistorico.value, newValue, !newValue, oldValue)

    await executaPesquisa(codigoHistorico.value);
})

onMounted(async () => {
    if (codigoHistorico.value !== undefined) {
        await executaPesquisa(codigoHistorico.value);
    }
})
</script>

<template>
    <Dialog :visible="visible" modal header="Pesquisa de Histórico"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized" :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full gap-2">

            <section class="flex justify-content-center">

                <Panel header="Filtros" class="w-full md:w-11 lg:w-8 xl:w-6 gap-0">
                    <div class="formgrid grid mt-4">
                        <div class="field col-12 sm:col-6 md:col-3 lg:col-2 xl:col-2">
                             <span class="p-float-label">
                                <InputNumber id="input-codigo" v-model="inputCodigo" :useGrouping="false"
                                             style="width:100px" :pt="{input:{style: 'width:100px'}}"/>
                                <label for="input-codigo">Histórico</label>
                             </span>
                        </div>

                        <div class="field col-12 sm:col-6 md:col-4 lg:col-3 xl:col-6 ml-3">
                            <span class="p-float-label">
                                 <InputText id="input-nome" type="text" v-model="nome" class="w-full"/>
                                 <label for="input-nome">Descrição</label>
                            </span>
                        </div>
                        <div
                            class="field col-12 md:col-4 lg:col-6 xl:col-3 flex md:justify-content-end justify-content-center">
                            <Button class="m-1 p-button-success shadow-4" icon="pi pi-search" @click="buscar"/>
                            <Button class="m-1 shadow-4" icon="pi pi-undo" @click="limpar"/>
                        </div>
                    </div>
                </Panel>
            </section>
        </section>
        <section class="flex justify-content-center mt-1">
            <DataTable v-model:selection="dadosGrid.selected" :value="dadosGrid.historicos"
                       dataKey="codigo" selectionMode="single"
                       @rowSelect="onRowSelect"
                       lazy paginator :rows="15"
                       @sort="onSort($event)"
                       :totalRecords="dadosGrid.totalRecords"
                       @page="onPage($event)"
                       :loading="dadosGrid.loadingGrid"
                       tableStyle="min-width: 50rem" class="w-full" :metaKeySelection="false">
                <template #empty>
                    Para carregar os históricos, clique em Buscar.
                </template>
                <template #loading>Buscando históricos...</template>
                <Column field="codigo" header="Código" class="w-1" sortable></Column>
                <Column field="nome" header="Descrição" class="w-9" sortable></Column>
            </DataTable>
        </section>
    </Dialog>
</template>
