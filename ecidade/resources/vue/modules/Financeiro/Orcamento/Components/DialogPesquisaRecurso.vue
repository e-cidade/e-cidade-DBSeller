<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import AutoCompleteComplemento from "./AutoCompleteComplemento.vue";
import {formateDateToBD} from "../../../../utils/Strings";

const toast = useToast();
const props = defineProps(['modelValue', 'pesquisaCodigo', 'visible', 'exercicio', 'data']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

/**
 * Declaração das computed
 */
const filtrarRecurso = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const pesquisaCodigo = computed(() => props.pesquisaCodigo);

const mensagemLoad = ref(null);
const loading = ref(false);

// filtros para pesquisa
const codigo = ref(null);
let todosSiconfis = [];
const classificacoesSiconfi = [];

const selectedClassificacoes = ref();
const classificacoes = ref([{"id": 0, "descricao": "Não se aplica"}]);

const selectedSiconfi = ref();
const siconfis = ref([]);

const selectedComplemento = ref();

const selectedMostrarRegistros = ref('ativos');
const mostrarRegistros = ref([
    {name: 'Apenas Ativos', code: 'ativos'},
    {name: 'Apenas Inativos', code: 'inativos'},
    {name: 'Todos', code: 'todos'}
]);

const lazyParams = ref({
    page: 1,
    rows: 15,
    sortField: null,
    sortOrder: null,
});

// da grid
const dadosGrid = ref({
    recursos: [],
    selected: null,
    loadingGrid: false,
    totalRegistros: 0
});

const recursos = ref([]);
const selected = ref(); // linha selecionada

const routs = {
    classificacoes: 'v4/api/financeiro/orcamento/classificacao/com-siconfi',
    recursos: 'v4/api/financeiro/orcamento/recursos/byFilters'
}

const criarOpcoesSiconfi = dados => {
    siconfis.value = [{"codigo_siconfi": 0, "apresentar": "Todos"}];
    for (const dado of dados) {
        siconfis.value.push(dado);
    }
};

const preFiltraSiconfis = () => {
    if (selectedClassificacoes.value === 1) {
        criarOpcoesSiconfi(todosSiconfis);
        return;
    }

    const selecionada = classificacoesSiconfi.filter((classificacao) => {
        return classificacao.id === selectedClassificacoes.value
    }).shift();

    criarOpcoesSiconfi(selecionada.fontes_siconfi);
};

const buscarClassificacoes = () => {
    window.axios.get(routs.classificacoes).then(response => {
        let allSiconfis = [];

        for (const classificacao of response.data.data) {
            classificacoesSiconfi.push(classificacao);
            classificacoes.value.push(classificacao)
            allSiconfis = allSiconfis.concat(classificacao.fontes_siconfi)
        }

        todosSiconfis = allSiconfis;
        criarOpcoesSiconfi(allSiconfis)
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
};

function limparCampos() {
    selectedClassificacoes.value = null;
    selectedSiconfi.value = null;
    selectedComplemento.value = null;
    selectedMostrarRegistros.value = 'ativos';
}

const buscar = async () => {
    const parameters = {
        page: 1,
        rows: 15
    }

    loadLazyData(parameters)
}

const loadLazyData = async (parameters) => {

    parameters = {
        ...parameters,
        exercicio: props.exercicio,
    }

    if (props.data !== undefined) {
        const dataSistema = typeof props.data === 'object' ? props.data : new Date(`${props.data}T00:00:00`);
        parameters.data = formateDateToBD(dataSistema);
    }

    if (selectedClassificacoes.value) {
        parameters.classificacao = selectedClassificacoes.value;
    }

    if (selectedSiconfi.value) {
        parameters.siconfi = selectedSiconfi.value;
    }

    if (selectedComplemento.value) {
        parameters.complemento = selectedComplemento.value.codigo;
    }

    if (selectedMostrarRegistros.value) {
        parameters.mostrarRegistros = selectedMostrarRegistros.value;
    }

    dadosGrid.value.loadingGrid = true;
    await window.axios.get(routs.recursos, {'params': parameters}).then(response => {
        dadosGrid.value.totalRegistros = response.data.data.totalRegistros;
        dadosGrid.value.recursos = response.data.data.recursos;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => dadosGrid.value.loadingGrid = false);
};

/**
 * Evento de seleção da linha da grid.
 * @param event
 */
const onRowSelect = (event) => {
    filtrarRecurso.value = event.data;
    emit('update:visible', false);
};

const onPage = (event) => {
    event.page += 1;
    loadLazyData({...event});
};

watch(pesquisaCodigo, async (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
        await executaPesquisa(newValue);
    }
});

const executaPesquisa = async (pesquisaCodigo) => {

    const parameters = {
        page: 1,
        rows: 15,
        codigo: pesquisaCodigo
    };
    await loadLazyData(parameters);
    if (!dadosGrid.value.recursos.length) {
        filtrarRecurso.value = {};
        return;
    }
    filtrarRecurso.value = {...dadosGrid.value.recursos[0]};
};

onMounted(async () => {

    if (pesquisaCodigo.value != undefined) {
        await executaPesquisa(pesquisaCodigo.value);
    }
    buscarClassificacoes();
})

</script>

<template>
    <Dialog :visible="visible" modal header="Pesquisa de Recursos"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized" :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full gap-2">
            <section class="flex justify-content-center">
                <Panel header="Filtros" class="w-full md:w-11 lg:w-10 xl:w-8">
                    <div class="formgrid grid mt-4 gap-2">
                        <div class="field col-12 ">
                            <div class="p-float-label ">
                                <Dropdown v-model="selectedClassificacoes" :options="classificacoes"
                                          optionLabel="descricao" optionValue="id"
                                          @change="preFiltraSiconfis"
                                          class="w-full" id="dd-classificacoes"/>
                                <label for="dd-classificacoes">Classificação</label>
                            </div>
                        </div>

                        <div class="field col-12 ">
                            <div class="p-float-label w-full">
                                <Dropdown v-model="selectedSiconfi" :options="siconfis"
                                          optionLabel="apresentar" optionValue="codigo_siconfi"
                                          class="w-full" id="dd-siconfi"/>
                                <label for="dd-siconfi">Siconfi</label>
                            </div>
                        </div>

                        <div class="field col-12 ">
                            <AutoCompleteComplemento v-model="selectedComplemento"/>
                        </div>

                        <div class="field col-12 md:col-8">
                            <SelectButton :modelValue="selectedMostrarRegistros" :options="mostrarRegistros"
                                          optionLabel="name" optionValue="code" aria-labelledby="custom"
                                          @update:modelValue="value => selectedMostrarRegistros = value ?? selectedMostrarRegistros"
                            />
                        </div>
                        <div class="field col-12 md:col-3 md:justify-content-end justify-content-center">
                            <Button class="m-1 p-button-success shadow-4" icon="pi pi-search" @click="buscar" />
                            <Button class="m-1 shadow-4" icon="pi pi-undo" @click="limparCampos" />
                        </div>
                    </div>
                </Panel>
            </section>
        </section>


        <section class="flex justify-content-center mt-1">
            <DataTable v-model:selection="dadosGrid.selected" :value="dadosGrid.recursos"
                       dataKey="orctiporec_id" selectionMode="single"
                       @rowSelect="onRowSelect"
                       lazy paginator
                       :rows="15"
                       :totalRecords="dadosGrid.totalRegistros"
                       @page="onPage($event)"
                       :loading="dadosGrid.loadinGrid"
                       tableStyle="min-width: 50rem" class="w-full"
                       :metaKeySelection="false">
                <template #empty>
                    Para carregar os reduzidos, clique em Buscar.
                </template>
                <template #loading>Buscando reduzidos...</template>
                <Column field="siconfi" header="Siconfi" class="w-1"></Column>
                <Column field="gestao" header="Gestão" class="w-1"></Column>
                <Column field="subrecurso" header="Subrecurso" class="w-1"></Column>
                <Column field="complemento" header="Complemento" class="w-4">
                    <template #body="slotProps">
                        {{ `${slotProps.data.complemento.codigo} - ${slotProps.data.complemento.descricao}` }}
                    </template>
                </Column>
                <Column field="descricao" header="Estrutural" class="w-6"></Column>
            </DataTable>
        </section>
    </Dialog>
</template>

<style scoped>

</style>
