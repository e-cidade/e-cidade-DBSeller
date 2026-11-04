<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import {formatCurrency} from "../../../../utils/Strings";
import AutoCompleteNaturezaDespesa from "./AutoCompleteNaturezaDespesa";
import AutoCompleteNaturezaReceita from "./AutoCompleteNaturezaReceita.vue";

const toast = useToast();
const props = defineProps(['modelValue', 'visible', 'pesquisaReduzido', 'exercicio', 'instituicao']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

/**
 * Declaração das computed
 */
const retorno = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});
// computed "readonly"
const pesquisaReduzido = computed(() => props.pesquisaReduzido);

const validaErro = ref({
    estrutural: false
})

// variáveis da grid
const dadosGrid = ref({
    "itens": [],
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
const estrutural = ref();
const naturezaReceita = ref();

const rota = 'v4/api/financeiro/orcamento/receita/receitas';

async function buscar() {

    validaErro.value.estrutural = false;
    if (estrutural.value && !['4', '9'].includes(estrutural.value.toString().substring(0, 1))) {
        validaErro.value.estrutural = true;
        estrutural.value = null
        return false;
    }

    const parameters = {
        page: 1,
        rows: 15,
    }

    await loadLazyData(parameters);
}

async function loadLazyData(parameters) {
    parameters = {
        ...parameters,
        exercicio: props.exercicio,
        instituicao: props.instituicao
    }

    if (reduzido.value) {
        parameters.reduzido = reduzido.value;
    }
    if (estrutural.value) {
        parameters.estrutural = estrutural.value;
    }
    if (naturezaReceita.value) {
        parameters.codigoNaturezaReceita = naturezaReceita.value?.codigo
    }

    dadosGrid.value.loadinGrid = true;
    await window.axios.get(rota, {'params': parameters}).then(response => {
        dadosGrid.value.totalRecords = response.data.data.totalRegistros;
        dadosGrid.value.receitas = response.data.data.receitas;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        dadosGrid.value.loadinGrid = false;
    });
}

const onRowSelect = (event) => {
    retorno.value = event.data;
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

async function executaPesquisa(pesquisaReduzido) {
    reduzido.value = pesquisaReduzido
    await buscar();
    if (!dadosGrid.value.receitas.length) {
        retorno.value = {};
        return
    }

    retorno.value = {...dadosGrid.value.receitas[0]};
}

function limparCampos() {
    reduzido.value = null;
    estrutural.value = null;
    naturezaReceita.value = null;
}

watch(pesquisaReduzido, async (newValue, oldValue) => {

    if (newValue && newValue !== oldValue) {
        await executaPesquisa(newValue);
    }
});

onMounted(async () => {
    if (pesquisaReduzido.value) {
        await executaPesquisa(pesquisaReduzido.value);
    }
});
</script>

<template>
    <Dialog :visible="visible" modal header="Pesquisa de Receitas"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized" :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full gap-2">
            <section class="flex justify-content-center">
                <Panel header="Filtros" class="w-full md:w-11 lg:w-8 xl:w-6 ">
                    <div class="formgrid grid mt-4 row-gap-2">
                        <div class="field col-12 md:col-4 lg:col-6 xl:col-3 ">
                            <div class="p-float-label">
                                <InputNumber id="input-reduzido" v-model="reduzido" :useGrouping="false"
                                             style="width:100px" :pt="{input:{style: 'width:100px'}}"/>
                                <label for="input-reduzido">Reduzido</label>
                            </div>
                        </div>
                        <div class="field col-12 md:col-4 lg:col-6 xl:col-3 ">
                            <div class="p-float-label">
                                <InputNumber id="input-estrutural" v-model="estrutural" :useGrouping="false"
                                             :inputClass="{ 'p-invalid': validaErro.estrutural }"
                                             style="width:250px" :pt="{input:{style: 'width:250px'}}"/>
                                <label for="input-estrutural">Estrutural Natureza Receita</label>
                            </div>
                            <small v-if="validaErro.estrutural" class="p-error" id="grupo-error">Estrutural deve iniciar com
                                4 ou 9</small>
                        </div>
                        <div class="field col-12 ">
                            <AutoCompleteNaturezaReceita v-model="naturezaReceita" :exercicio="exercicio"
                                                         :apenasComReceita="true"/>
                        </div>
                        <div class="field col-12 ">

                            <div class="m-auto flex justify-content-center flex-wrap">
                                <Button class="m-1 p-button-success shadow-4" icon="pi pi-search" @click="buscar"/>
                                <Button class="m-1 shadow-4" icon="pi pi-undo" @click="limparCampos"/>
                            </div>
                        </div>
                    </div>
                </Panel>
            </section>
        </section>
        <section class="flex justify-content-center mt-1">
            <DataTable v-model:selection="dadosGrid.selected" :value="dadosGrid.receitas" dataKey="coddot"
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
                <template #loading>Buscando receitas...</template>

                <Column field="reduzido" header="Reduzido" sortable></Column>

                <Column field="naturezaReceita" header="Natureza da Receita" sortable>
                    <template #body="slotProps">
                        {{ `${slotProps.data.naturezaReceita.estrutural} - ${slotProps.data.naturezaReceita.descricao}` }}
                    </template>
                </Column>
                <Column field="cp" header="Caracteristica Peculiar" sortable>
                    <template #body="slotProps">
                        {{ `${slotProps.data.cp.codigo} - ${slotProps.data.cp.descricao}` }}
                    </template>
                </Column>
                <Column field="recurso" header="Recurso Peculiar">
                    <template #body="slotProps">
                        {{ slotProps.data.recurso.apresentacao }}
                    </template>
                </Column>
                <Column field="esferaOrcamentaria" header="Esfera Orçamentária">
                    <template #body="slotProps">
                        {{ slotProps.data.esferaOrcamentaria.descricao }}
                    </template>
                </Column>
                <Column field="saldoInicial" header="Previsto" sortable>
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.saldoInicial) }}
                    </template>
                </Column>
            </DataTable>
        </section>
    </Dialog>
</template>

<style scoped>

</style>
