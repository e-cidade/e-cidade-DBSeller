<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import {buildDate, formatCurrency, formateDate} from "../../../../utils/Strings";

const toast = useToast();
const props = defineProps(['modelValue', 'visible', 'codigo']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

/**
 * Declaração das computed
 */
const filtrarUnificacoes = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const codigoUnificacao = computed(() => props.codigo);

function onVisibleChange(newValue, oldValue) {
    if(newValue == true){
        buscar();
    }
}

watch(() => props.visible, (newValue, oldValue) => {
    onVisibleChange(newValue, oldValue);
});

// declaração das ref
const dadosGrid = ref({
    "unificacoes": [],
    "selected": null,
    "loadingGrid": false,
    "totalRecords": 0
});

// filtros
const inputCgsCorreto = ref()

let rota = 'v4/api/saude/ambulatorial/procedimento/unificacao-cgs/listar-unificacoes';

const buscar = async () => {
    
    const parameters = {
        page: 1,
        rows: 15
    }

    if(inputCgsCorreto.value != null){
        parameters.cgs_correto = inputCgsCorreto.value;
    }
    parameters.unificado = true;

    await loadLazyData(parameters);
}

async function loadLazyData(parameters) {

    dadosGrid.value.loadingGrid = true;
    try {
        const response = await axios.post(
            rota, 
            {
                'cgs_correto': parameters.cgs_correto,
                'unificado': parameters.unificado,
                'page':parameters.page,
                'rows':parameters.rows
            }
        );

        dadosGrid.value.totalRecords = response.data.data.totalRegistros;
        dadosGrid.value.unificacoes = response.data.data.data;
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }
    dadosGrid.value.loadingGrid = false;
}

async function executaPesquisa(codigo) {

    await buscar();
    if (!dadosGrid.value.unificacoes.length) {
        filtrarUnificacoes.value = {};
    }

    filtrarUnificacoes.value = {...dadosGrid.value.unificacoes[0]};
}

/**
 * Evento de seleção da linha da grid.
 * @param event
 */
const onRowSelect = (event) => {
    filtrarUnificacoes.value = event.data;
    emit('update:visible', false);
};


const onPage = (event) => {
    event.page += 1;
    event.unificado = true;
    loadLazyData({...event});
};

/**
 * Limpa os dados do formulário de pesquisa formulário
 */
function limpar() {
    inputCgsCorreto.value = null;
}

/**
 * Implementa a busca pela digitação do código
 */
watch(codigoUnificacao, async (newValue, oldValue) => {    
    if ((newValue === undefined) || !newValue || (newValue === oldValue)) {
        return;
    }  
    await executaPesquisa(codigoUnificacao.value);
})

onMounted(async () => {
    if (codigoUnificacao.value !== undefined) {
        await executaPesquisa(codigoUnificacao.value);
    }
})
</script>

<template>
    <Dialog :visible="visible" modal header="Consulta dos CGSs unificados"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized" :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full gap-2">

            <section class="flex justify-content-center">

                <Panel header="Filtros" class="w-full md:w-11 lg:w-8 xl:w-6 gap-0">
                    <div class="formgrid grid mt-4">
                        <div class="field col-12 sm:col-9 md:col-6 lg:col-2 xl:col-2">
                             <span class="p-float-label">
                                <InputNumber id="input-codigo" v-model="inputCgsCorreto" :useGrouping="false"
                                             style="width:100px" :pt="{input:{style: 'width:100px'}}"/>
                                <label for="input-codigo">CGS</label>
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
            <DataTable v-model:selection="dadosGrid.selected" :value="dadosGrid.unificacoes"
                       dataKey="codigo" selectionMode="single"
                       @rowSelect="onRowSelect"
                       lazy paginator :rows="15"                    
                       :totalRecords="dadosGrid.totalRecords"
                       @page="onPage($event)"
                       :loading="dadosGrid.loadingGrid"
                       tableStyle="min-width: 50rem" class="w-full" :metaKeySelection="false">
                <template #empty>
                    Para carregar os unificações, clique em Buscar.
                </template>
                <template #loading>Buscando unificações...</template>
                <Column field="codigo" header="Código" class="w-1"></Column>
                <Column field="cgs" header="CGS Correto" class="w-2"></Column>
                <Column field="nome" header="Nome Correto" class="w-5"></Column>
                <Column field="data_processamento" header="Data Processamento" class="w-2">
                       <template #body="slotProps">                        
                            {{ slotProps.data.data_processamento ? formateDate(slotProps.data.data_processamento) : ''}}
                        </template> 
                </Column>              
                <Column field="hora_processamento" header="Hora Processamento" class="w-1"></Column>
                <Column field="login_processamento" header="Login Processamento" class="w-2"></Column>
                <Column field="processado" header="Processado" class="w-2"></Column>
                <Column field="instituicao" header="Instituição" class="w-2"></Column>

            </DataTable>
        </section>
    </Dialog>
</template>
