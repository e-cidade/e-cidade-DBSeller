<script setup>
import {computed, onMounted, ref, watch} from "vue";
import ModalLoading from "../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
import {formatCurrency, formateDate, formateDateToBD} from "../../../../utils/Strings";
import Calendar from "primevue/calendar";

const toast = useToast();
const props = defineProps(['modelValue', 'visible', 'exercicio', 'instituicao', 'dataSistema', 'pesquisaNumero', 'pesquisaCodigo']);
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
const pesquisaNumero = computed(() => props.pesquisaNumero);

const rota = 'v4/api/financeiro/empenho/empenhos/dialog-pesquisa';
const mensagemLoad = ref(null);
const loading = ref(false);

// variáveis da grid
const dadosGrid = ref({
    "empenhos": [],
    "selected": null,
    "loadingEmpenho": false,
    "totalRecords": 0
});

const lazyParams = ref({
    page: 1,
    rows: 15,
    sortField: null,
    sortOrder: null
});

// variáveis do form de pesquisa
const codigo = ref()
const numero = ref()
const ano = ref(Number(props.exercicio));
// Se a dataSistema veio como objeto, deve ser um Date... se vir como string, deve ser no formato Y-m-d
const dataSistema = typeof props.dataSistema === 'object' ? props.dataSistema: new Date(`${props.dataSistema}T00:00:00`);
const dataInicial = ref(dataSistema);
const dataFinal = ref(dataSistema);

const informouCodigoEmpenho = ({value}) => {
    if (value) {
        numero.value = null;
    }
}

const informouNumeroEmpenho = ({value}) => {
    if (value) {
        codigo.value = null;
    }
}

const validaPesquisa = () => {
    try {
        if (!codigo.value && !numero.value && !ano.value) {
            throw 'Informe ao menos o exercício para pesquisar os empenhos do exercício.'
        }
        if (numero.value && !ano.value) {
            throw 'Ao informar o número do empenho, você deve informar o exercício.'
        }
    } catch (e) {
        toast.add({severity: 'warn', detail: e, summary: 'Erro'});
        return false;
    }

    return true;
}

const buscar = async (page = 1, rows = 15) => {

    if (!validaPesquisa()) {
        return
    }

    const parameters = {};

    if (codigo.value) {
        parameters.numemp = codigo.value;
    }

    if (!codigo.value && numero.value) {
        parameters.numero = numero.value;
    }

    if (!codigo.value && numero.value && ano.value) {
        parameters.exercicio = ano.value;
    }

    if (!codigo.value && !numero.value && dataInicial.value) {
        parameters.dataInicial = formateDateToBD(dataInicial.value)
    }

    if (!codigo.value &&!numero.value && dataFinal.value) {
        parameters.dataFinal = formateDateToBD(dataFinal.value)
    }

    await loadLazyData(parameters);
};

const loadLazyData = async (parameters) => {
    const params = {
        ...lazyParams.value,
        ...parameters,
        instituicao: props.instituicao
    };

    dadosGrid.value.loadingEmpenho = true;
    await window.axios.get(rota, {'params': params}).then(response => {
        dadosGrid.value.totalRecords = response.data.data.totalRegistros;
        dadosGrid.value.empenhos = response.data.data.empenhos;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        dadosGrid.value.loadingEmpenho = false;
    });
}

/**
 * Evento de seleção da linha da grid.
 * @param event
 */
const onRowSelect = (event) => {
    filtrar.value = event.data;
    emit('update:visible', false);
};

const onPage = (event) => {
    event.page +=1
    lazyParams.value.page = event.page +1;
    lazyParams.value.rows = event.rows;
    lazyParams.value.sortField = null;
    lazyParams.value.sortOrder = null;

    buscar();
};

const onSort = (event) => {
    lazyParams.value.page = 1
    lazyParams.value.rows = 15
    lazyParams.value.sortField = event.sortField;
    lazyParams.value.sortOrder = event.sortOrder === 1 ? 'asc' : 'desc';
    buscar();
}


function limparCampos() {
    codigo.value = null
    numero.value = null
    ano.value = Number(props.exercicio);
}

async function find(parans) {
    await loadLazyData(parans);

    if (!dadosGrid.value.empenhos.length) {
        filtrar.value = {};
        return
    }

    filtrar.value = {... dadosGrid.value.empenhos[0]}
}

watch(pesquisaNumero, async (newValue, oldValue) => {

    if (!newValue || (newValue === oldValue)) {
        return;
    }

    find({numeroEmpenho: newValue});
});

onMounted(async () => {
    if (props.pesquisaCodigo) {
        find({numemp: props.pesquisaCodigo});
    }
})
</script>

<template>
    <Dialog :visible="visible" modal header="Pesquisa de Empenhos"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized" :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full gap-2">
            <section class="flex justify-content-center">
                <Panel header="Filtros" class="w-full md:w-11 lg:w-8 xl:w-6 ">
                    <div class="formgrid grid mt-4 row-gap-2">
                        <div class="field col-12 sm:col-6 md:col-4 lg:col-4 xl:col-4">
                             <span class="p-float-label">
                                <InputNumber id="input-codigo" v-model="codigo" :useGrouping="false"
                                             @blur="informouCodigoEmpenho"
                                             style="width:150px" :pt="{input:{style: 'width:150px'}}"/>
                                <label for="input-codigo">Seq. Empenho</label>
                             </span>
                        </div>

                        <div class="field col-12 sm:col-6 md:col-8 lg:8 xl:col-8 ">
                            <div class="flex ">
                                <span class="p-float-label">
                                      <InputNumber id="input-numero" v-model="numero" :useGrouping="false"
                                                   @blur="informouNumeroEmpenho"
                                                   style="width:100px" :pt="{input:{style: 'width:100px'}}"/>
                                    <label for="input-numero">Numero</label>
                                </span>

                                <span class="p-float-label">
                                       <InputNumber id="input-numero" v-model="ano" :useGrouping="false"
                                                    style="width:100px" :pt="{input:{style: 'width:100px'}}"/>
                                    <label for="input-numero">Exercicio</label>
                                </span>
                            </div>
                        </div>
                        <div class="field col-12 sm:col-6 md:col-4 lg:col-4 xl:col-4">
                            <div class="p-float-label">
                                <Calendar v-model="dataInicial" dateFormat="dd/mm/yy"
                                          :maxDate="dataSistema" showButtonBar
                                          showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                          style="width: 150px;"/>
                                <label for="lb-periodo">Período de</label>
                            </div>
                        </div>

                        <div class="field col-12 sm:col-6 md:col-4 lg:col-4 xl:col-4">
                            <div class="p-float-label">
                                <Calendar v-model="dataFinal" dateFormat="dd/mm/yy"
                                          :maxDate="dataSistema"
                                          showIcon placeholder="Data Final" showButtonBar :manualInput=true
                                          inputId="lb-periodo-ate"
                                          style="width: 150px;"/>
                                <label for="lb-periodo-ate"> Até</label>
                            </div>
                        </div>

                        <div class="field col-12 md:col-4 lg:col-6 xl:col-3 flex md:justify-content-end justify-content-center">
                            <Button class="m-1 p-button-success shadow-4" icon="pi pi-search" @click="buscar"/>
                            <Button class="m-1 shadow-4" icon="pi pi-undo" @click="limparCampos"/>
                        </div>
                    </div>
                </Panel>
            </section>
        </section>

        <section class="flex justify-content-center mt-1">
            <DataTable v-model:selection="dadosGrid.selected" :value="dadosGrid.empenhos" dataKey="numemp" selectionMode="single"
                       @rowSelect="onRowSelect" tableStyle="min-width: 50rem"
                       lazy paginator
                       :rows="15"
                       @sort="onSort($event)"
                       :totalRecords="dadosGrid.totalRecords"
                       @page="onPage($event)"
                       class="w-full" :loading="dadosGrid.loadingEmpenho"
                       :metaKeySelection="false">
                <template #empty>
                    Para carregar os empenhos, clique em Buscar.
                </template>
                <template #loading>Buscando empenhos...</template>
                <Column field="numemp" header="Código" class="w-1" sortable></Column>
                <Column field="numero" header="Numero" class="w-1" sortable>
                    <template #body="slotProps">
                        {{ `${slotProps.data.numero} - ${slotProps.data.exercicio}` }}
                    </template>
                </Column>
                <Column field="dataEmissao" header="Emissão" class="w-1" sortable>
                    <template #body="slotProps">
                        {{ formateDate(slotProps.data.dataEmissao) }}
                    </template>
                </Column>
                <Column field="credor" header="Credor" class="w-4" sortable>
                    <template #body="slotProps">
                        {{ `${slotProps.data.cgm.codigo} - ${slotProps.data.cgm.nome}` }}
                    </template>
                </Column>
                <Column field="valorEmpenhado" header="Empenhado" class="w-1">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.valorEmpenhado) }}
                    </template>
                </Column>
                <Column field="valorAnulado" header="Anulado" class="w-1">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.valorAnulado) }}
                    </template>
                </Column>
                <Column field="valorLiquidado" header="Liquidado" class="w-1">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.valorLiquidado) }}
                    </template>
                </Column>
                <Column field="saldoLiquido" header="Saldo Liquido" class="w-1">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.saldoLiquido) }}
                    </template>
                </Column>
                <Column field="saldo" header="Saldo" class="w-1">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.saldo) }}
                    </template>
                </Column>
            </DataTable>
        </section>

        <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    </Dialog>
</template>
