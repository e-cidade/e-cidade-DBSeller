<script setup>
import {ref} from "vue";
import {FilterMatchMode, FilterOperator} from 'primevue/api';
import Calendar from "primevue/calendar";
import ConfirmDialog from "primevue/confirmdialog";
import RetificaLancamento from "./RetificaLancamento.vue";
import {useConfirm} from "primevue/useconfirm";
import {useToast} from "primevue/usetoast";
import AbaLote from "./AbaLote.vue";
import AbaPreparaLancamento from "./AbaPreparaLancamento";
import AbaLancamentosSalvar from "./AbaLancamentosSalvar.vue";
import ModalLoading from "../../../../../Components/ModalLoading.vue";
import {buildDate, formatCurrency, formateDate} from "../../../../../../utils/Strings";

const toast = useToast();
const confirm = useConfirm();
const props = defineProps(['instituicao', 'exercicio', 'dataEncerramento', 'dataSistema']);

const loading = ref(false);
const mensagemLoad = ref(null);
const visibleRetificacao = ref(null);
// variáveis usada na grid
const loadingGrid = ref(false);
const totalRecords = ref(0);
const expandedRows = ref([]);
const filtersGrid = ref();

// variáveis usadas no formulário de pesquisa
const dataSistema = new Date(`${props.dataSistema}T00:00:00`);
const dataInicial = ref(dataSistema);
const dataFinal = ref(dataSistema);

let minDate = ref(new Date(`${dataSistema.getFullYear()}-01-01T00:00:00`));
if (props.dataEncerramento !== '') {
    minDate = new Date(`${props.dataEncerramento}T00:00:00`);
}

let rotas = {
    resource: 'v4/api/financeiro/contabilidade/lancamento-manual',
    imprimir: 'v4/api/financeiro/contabilidade/lancamento-manual/nota-lancamento',
    excluirLote: 'v4/api/financeiro/contabilidade/lancamento-manual/excluir-lote',
}

/**
 * Objeto de controle para iteração reativa dos valores entre as abas.
 */
const dados = ref({
    aba: {ativa: 0},
    apresentaTelaFiltros: true,
    numerLote: null,
    dataLancamento: null,
    dataSistema: dataSistema,
    dataEncerramento: props.dataEncerramento,
    instituicao: props.instituicao,
    exercicio: props.exercicio,
    lancamentos: []
});

const lotes = ref([]);
const lancamentoRetificar = ref();

const initFilters = () => {
    filtersGrid.value = {
        global: {value: null, matchMode: FilterMatchMode.CONTAINS},
        stringfy: {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]}
    };
};

initFilters();

const clearFilter = () => {
    initFilters();
};

const buscar = async () => {
    const parameters = {
        exercicio: props.exercicio,
        instituicao: props.instituicao,
        aposDataEncerramento: true,
        documentos: [3000, 3001],
        dataInicial: dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
    }

    loading.value = true;
    mensagemLoad.value = 'Buscando lançamentos';

    lotes.value = [];
    await window.axios.get(rotas.resource, {'params': parameters}).then(response => {
        for (const dado of response.data.data) {
            dado.stringfy = JSON.stringify(dado)
            lotes.value.push(dado);
        }
    }).catch(response => {
        toast.add({severity: 'error', detail: response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
};

/**
 * Funções do formulário
 */
function cadastrar() {
    dados.value.apresentaTelaFiltros = false;
}

function editar(dado) {
    lancamentoRetificar.value = dado;
    visibleRetificacao.value = true;
}

const confirmaAcao = (msgConfirm) => {
    return new Promise((accept, reject) => {
        confirm.require({
            header: msgConfirm.header,
            message: msgConfirm.message,
            icon: msgConfirm?.icon ? msgConfirm?.icon : 'pi pi-exclamation-triangle',
            accept,
            reject
        });
    }).then(() => true).catch(() => false);
}

async function excluirLancamento(dado) {

    const msgConfirm = {
        header: 'Excluir Lançamento.',
        message: `O lançamento ${dado.lancamento} será excluído. Deseja prosseguir?`
    }

    if (!await confirmaAcao(msgConfirm)) {
        return;
    }

    loading.value = true;
    mensagemLoad.value = 'Excluindo lançamento, aguarde.';
    exclusao(rotas.resource, dado.lancamento);
}

async function excluirLote(dado) {

    const msgConfirm = {
        header: 'Excluir Lote.',
        message: `O Lote ${dado.lote} será excluído. Todos lançamentos serão excluídos. Deseja prosseguir?`
    }

    if (!await confirmaAcao(msgConfirm)) {
        return;
    }
    loading.value = true;
    mensagemLoad.value = 'Excluindo lote, aguarde.';
    exclusao(rotas.excluirLote, dado.codigo)
}

async function exclusao(rota, id) {
    await window.axios.delete(`${rota}/${id}`).then(response => {
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
        buscar();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
}

function imprimir(lote) {

    window.axios.get(rotas.imprimir, {params: {idLote: lote.codigo}}).then(async response => {
        window.open(response.data.data.pdfLinkExterno)
    })
}

function liberaAcoes(lancamento) {

    if (lancamento.temExtorno) {
        return false;
    }
    if (lancamento.documento.documento === 3001) {
        return false;
    }

    if (props.dataEncerramento !== '') {
        const dataEncerramento = buildDate(props.dataEncerramento);
        const dataLancamento = buildDate(lancamento.data);
        if (dataLancamento <= dataEncerramento) {
            return false
        }
    }

    return true;
}

</script>

<template>

    <section class="flex flex-column w-full gap-2" v-if="dados.apresentaTelaFiltros">
        <section class="flex justify-content-center">
            <Panel header="Busque lançamentos para edição/exclusão" class="w-full md:w-11 lg:w-8 xl:w-6 gap-2"
                   :pt="{header:{style: 'padding: 5px'}}">
                <template #icons>
                    <Button type="button" label="Adicionar Lançamento" icon="pi pi-plus" severity="success"
                            @click="cadastrar"/>
                </template>

                <div class="formgrid grid mt-2 row-gap-2">

                    <div class="field col-12 sm:col-6 md:col-4 lg:col-3 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dataInicial" dateFormat="dd/mm/yy"
                                      :minDate="minDate" :maxDate="dataSistema"
                                      showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 150px;"/>
                            <label for="lb-periodo">Período de</label>
                        </div>
                    </div>

                    <div class="field col-12 sm:col-6 md:col-4 lg:col-3 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dataFinal" dateFormat="dd/mm/yy"
                                      :minDate="minDate" :maxDate="dataSistema"
                                      showIcon placeholder="Data Final" showButtonBar :manualInput=true
                                      inputId="lb-periodo-ate"
                                      style="width: 150px;"/>
                            <label for="lb-periodo-ate"> Até</label>
                        </div>
                    </div>
                    <div
                        class="field col-12 md:col-4 lg:col-6 xl:col-6 flex md:justify-content-end justify-content-center	">
                        <Button type="button" label="Buscar" icon="pi pi-search" @click="buscar"/>
                    </div>
                </div>
            </Panel>
        </section>
        <section class="flex justify-content-center">
            <div class="card w-full md:w-11 lg:w-8 xl:w-6">
                <DataTable v-model:expandedRows="expandedRows" :value="lotes" dataKey="lote"
                           :loading="loadingGrid" v-model:filters="filtersGrid" :globalFilterFields="['stringfy']"
                           paginator showGridlines :rows="10"
                           tableStyle="min-width: 60rem">
                    <template #header>
                        <div class="flex justify-content-between">
                            <Button type="button" icon="pi pi-filter-slash" label="Limpar" outlined
                                    @click="clearFilter()"/>
                            <div class="p-input-icon-left">
                                <i class="pi pi-search"/>
                                <InputText v-model="filtersGrid['global'].value" placeholder="Pesquise um lançamento"/>
                            </div>
                        </div>
                    </template>
                    <template #empty>
                        Para buscar os lançamentos manuais, selecione os filtros e clique em <kbd
                        style="background-color: #e1dede">Buscar</kbd>.
                    </template>
                    <template #loading>Buscando lançamentos</template>
                    <Column expander style="width: 5rem"/>
                    <Column field="lote" header="Lote"></Column>
                    <Column field="data" header="Data">
                        <template #body="slotProps">
                            {{ formateDate(slotProps.data.data)}}
                        </template>
                    </Column>
                    <Column field="valor" header="Valor">
                        <template #body="slotProps">
                            {{ formatCurrency(slotProps.data.valor) }}
                        </template>
                    </Column>
                    <Column field="acao" header="Ação" class="w-2">
                        <template #body="slotProps">
                            <Button icon="pi pi-print" text rounded aria-label="Filter"
                                    @click="imprimir(slotProps.data)"/>
                            <Button icon="pi pi-trash" severity="danger" text rounded
                                    @click="excluirLote(slotProps.data)"/>
                        </template>
                    </Column>
                    <template #expansion="slotProps">
                        <DataTable :value="slotProps.data.lancamentos"
                                   dataKey="codigo" filterDisplay="menu"
                                   class="w-full" tableStyle="min-width: 50rem">

                            <Column field="lancamento" header="Lançamento" class="text-center"></Column>
                            <Column field="data" header="Data">
                                <template #body="slotProps">
                                    {{ formateDate(slotProps.data.data)}}
                                </template>
                            </Column>
                            <Column field="debito" header="Débito">
                                <template #body="slotProps">
                                    {{
                                        `${slotProps.data.debito.reduzido} - ${slotProps.data.debito.estrutural} - ${slotProps.data.debito.descricao}`
                                    }}
                                </template>
                            </Column>
                            <Column field="credito" header="Credito">
                                <template #body="slotProps">
                                    {{
                                        `${slotProps.data.credito.reduzido} - ${slotProps.data.credito.estrutural} - ${slotProps.data.credito.descricao}`
                                    }}
                                </template>
                            </Column>
                            <Column field="documento" header="Documento" class="flex justify-content-center" >
                                <template #body="slotProps">
                                    <Tag :severity="(slotProps.data.documento.documento === 3000? 'success' : 'warning')"
                                         :value="slotProps.data.documento.documento"></Tag>
                                </template>
                            </Column>
                            <Column field="valor" header="Valor">
                                <template #body="slotProps">
                                    {{ formatCurrency(slotProps.data.valor) }}
                                </template>
                            </Column>
                            <Column field="acao" header="Ação" class="w-2">
                                <template #body="slotProps">
                                    <Button icon="pi pi-pencil" text rounded aria-label="Filter"
                                            v-if="liberaAcoes(slotProps.data)" @click="editar(slotProps.data)"/>
                                    <Button icon="pi pi-trash" severity="danger" text rounded
                                            v-if="liberaAcoes(slotProps.data)"
                                            @click="excluirLancamento(slotProps.data)"/>
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </DataTable>
            </div>
        </section>
    </section>

    <section class="flex flex-column w-full gap-2" v-if="!dados.apresentaTelaFiltros">
        <TabView class="tabview-custom" v-model:activeIndex="dados.aba.ativa">
            <TabPanel>
                <template #header>
                    <i class="pi pi-bookmark mr-2"></i>
                    <span>Lote</span>
                </template>
                <AbaLote v-model="dados" :data-minima="minDate" :data-sistema="dataSistema"/>
            </TabPanel>
            <TabPanel>
                <template #header>
                    <i class="pi pi-calculator mr-2"></i>
                    <span>Preparar Lançamento</span>
                </template>
                <AbaPreparaLancamento v-model="dados" :exercicio="exercicio" :instituicao="instituicao"/>
            </TabPanel>
            <TabPanel>
                <template #header>
                    <i class="pi pi-save mr-2"></i>
                    <span>Salvar Lançamentos</span>
                </template>
                <AbaLancamentosSalvar v-model="dados"/>
            </TabPanel>
        </TabView>
    </section>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    <ConfirmDialog></ConfirmDialog>
    <RetificaLancamento v-model="lancamentoRetificar" v-model:visible="visibleRetificacao" v-if="visibleRetificacao"
                        :data-sistema="dataSistema"/>
</template>
