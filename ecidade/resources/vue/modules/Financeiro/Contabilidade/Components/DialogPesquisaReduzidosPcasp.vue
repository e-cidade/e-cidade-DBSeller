<script setup>
import {computed, onMounted, ref, watch} from "vue";
import ModalLoading from "../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";

const toast = useToast();
const props = defineProps(['modelValue', 'visible', 'instituicao', 'exercicio', 'reduzido', 'estrutural', 'excluirContasBancarias']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

const filtrarConta = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

// computed "readonly"
const reduzido = computed(() => props.reduzido);
const estrutural = computed(() => props.estrutural);

const inputReduzido = ref(null);
const inputEstrutural = ref(null);

const mensagemLoad = ref(null);
const loading = ref(false);

// variáveis da grid
const dadosGrid = ref({
    "reduzidos": [],
    "selected": null,
    "loadinGrid": false,
    "totalRecords": 0
});

let rota = 'v4/api/financeiro/contabilidade/reduzidos/pcasp';

const buscar = async () => {
    const parameters = {
        page: 1,
        rows: 15,
    }

    await loadLazyData(parameters);
}

async function loadLazyData(parameters) {
    parameters = {
        ...parameters,
        instituicao: props.instituicao,
        exercicio: props.exercicio,
    }
    if (props.excluirContasBancarias) {
        parameters.excluirContasBancarias = true;
    }
    if (inputReduzido.value) {
        parameters.reduzido = inputReduzido.value;
    }

    if (inputEstrutural.value) {
        parameters.estrutural = inputEstrutural.value;
    }

    dadosGrid.value.loadinGrid = true;

    await window.axios.get(rota, {'params': parameters}).then(response => {
        dadosGrid.value.totalRecords = response.data.data.totalRegistros;
        dadosGrid.value.reduzidos = response.data.data.reduzidos;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        dadosGrid.value.loadinGrid = false;
    });
}

const onRowSelect = (event) => {
    filtrarConta.value = {...event.data};
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

const executaPesquisa = async (reduzido, estrutural) => {
    inputReduzido.value = reduzido;
    inputEstrutural.value = estrutural;

    await buscar();

    filtrarConta.value = {};
    if (dadosGrid.value.reduzidos.length) {
        filtrarConta.value = {...dadosGrid.value.reduzidos[0]};
    }
}

function limpar() {
    inputReduzido.value = null
    inputEstrutural.value = null
}

onMounted(() => {
    let vReduzido = null;
    let vEstrutural = null;
    if (reduzido.value) {
        vReduzido = reduzido.value
    }
    if (estrutural.value) {
        vEstrutural = estrutural.value
    }

    if (reduzido.value || estrutural.value) {
        executaPesquisa(vReduzido, vEstrutural)
    }
});

/**
 * Como estou observando 2 variáveis, newValue e oldValue se tornam um array onde a primeira posição refere-se
 * a primeira variável observada.
 * A opção "deep" deve ser informada, pois essas propriedades estão sendo alteradas em outro componente
 */
watch([reduzido, estrutural], async (newValue, oldValue) => {
    if ((reduzido.value && estrutural.value) || (!reduzido.value && !estrutural.value)) {
        return;
    }

    // Alterou o reduzido, disparando assim o change
    if (newValue[0] && reduzido.value === newValue[0] && newValue[0] !== oldValue[0]) {
        await executaPesquisa(newValue[0], null)
    }

    // Alterou o estrutural, disparando assim o change
    if (newValue[1] && estrutural.value === newValue[1] && newValue[1] !== oldValue[1]) {
        await executaPesquisa(null, newValue[1]);
    }
}, {deep: true});
</script>

<template>
    <Dialog :visible="visible" modal header="Pesquisa de Lançamentos contábeis"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized" :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full gap-2">

            <section class="flex justify-content-center">

                <Panel header="Filtros" class="w-full md:w-11 lg:w-8 xl:w-6 gap-0">
                    <div class="formgrid grid mt-4">
                        <div class="field col-12 sm:col-6 md:col-3 lg:col-2 xl:col-2">
                             <span class="p-float-label">
                                <InputNumber id="input-reduzido" v-model="inputReduzido" :useGrouping="false"
                                             style="width:150px" :pt="{input:{style: 'width:150px'}}"/>
                                <label for="input-reduzido">Conta Crédito</label>
                             </span>
                        </div>

                        <div class="field col-12 sm:col-6 md:col-4 lg:col-3 xl:col-3 ml-3">
                            <span class="p-float-label">
                                 <InputText id="input-estrutural" type="text" v-model="inputEstrutural"
                                            maxlength="15"/>
                                 <label for="input-estrutural">Estrutural</label>
                            </span>
                        </div>
                        <div class="field col-12 md:col-4 lg:col-6 xl:col-6 flex md:justify-content-end justify-content-center">
                            <Button class="m-1 p-button-success shadow-4" icon="pi pi-search" @click="buscar"/>
                            <Button class="m-1 shadow-4" icon="pi pi-undo" @click="limpar"/>
                        </div>
                    </div>
                </Panel>

            </section>
        </section>
        <section class="flex justify-content-center mt-1">
            <DataTable v-model:selection="dadosGrid.selected" :value="dadosGrid.reduzidos" dataKey="reduzido"
                       selectionMode="single"
                       @rowSelect="onRowSelect"
                       lazy paginator
                       :rows="15"
                       @sort="onSort($event)"
                       :totalRecords="dadosGrid.totalRecords"
                       @page="onPage($event)"
                       :loading="dadosGrid.loadinGrid"
                       tableStyle="min-width: 50rem" class="w-full"
                       :metaKeySelection="false">
                <template #empty>
                    Para carregar os reduzidos, clique em Buscar.
                </template>
                <template #loading>Buscando reduzidos...</template>
                <Column field="reduzido" header="Reduzido" sortable=""></Column>
                <Column field="estrutural" header="Estrutural" sortable=""></Column>
                <Column field="descricao" header="Conta" sortable></Column>
                <Column field="instituicao" header="Instituição"></Column>
            </DataTable>
        </section>

        <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    </Dialog>
</template>
