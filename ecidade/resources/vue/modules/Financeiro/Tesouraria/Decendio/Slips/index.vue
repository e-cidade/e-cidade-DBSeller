<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import ConfirmPopup from 'primevue/confirmpopup';
import Calendar from "primevue/calendar";
import Dialog from "primevue/dialog";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import {formatCurrency} from "../../../../../utils/Strings";

const props = defineProps(['exercicio', 'instituicao', 'dataSistema']);
const toast = useToast();
const confirm = useConfirm();

const loading = ref(false);
const mensagemLoad = ref(null);
const visibleDialogSucess = ref(false);
const visibleDialogInfo = ref(false);
const mensagemDialog = ref();
const valores = ref([]);
const detalhamentoSlip = ref([]);
const itensSelecionados = ref([]);
const slipsGerados = ref([]);
const selectAll = ref(false);

// datas filtro período
const dataSistema = new Date(`${props.dataSistema}T00:00:00`);
const dataInicial = ref(dataSistema);
const minDate = ref(new Date(`${props.exercicio}-01-01T00:00:00`));
const maxDate = ref(new Date(`${props.exercicio}-12-31T00:00:00`));
const dataFinal = ref(maxDate.value);

const rotas = {
    get: 'v4/api/financeiro/tesouraria/decendio/lancamentos',
    detalhamento: 'v4/api/financeiro/tesouraria/decendio/detalhamento',
    gerarSlips: 'v4/api/financeiro/tesouraria/decendio/gerar-slips',
}

function validaPesquisa() {
    try {
        if (dataInicial.value > dataFinal.value) {
            throw 'Data inicial não pode ser maior que a data final.'
        }
    } catch (e) {
        toast.add({severity: 'warn', detail: e, summary: 'Aviso'});
        return false
    }
    return true
}

async function buscar() {
    if (!validaPesquisa()) {
        return false;
    }

    loading.value = true;
    mensagemLoad.value = 'Buscando receitas.';

    const parameters = {
        exercicio: props.exercicio,
        instituicao: props.instituicao,
        dataInicial: dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
    }

    valores.value = [];
    itensSelecionados.value = [];
    await window.axios.get(rotas.get, {'params': parameters}).then(response => {
        response.data.data.forEach((dado) => {
            dado.id = uniqid();
            dado.desdobramento.contasBancarias.forEach(conta => {
                conta.nome = `${conta.conta} - ${conta.nome}`
            });

            const conta = dado.desdobramento.contasBancarias[0];
            dado.contaSelecionada = conta
            dado.apenasDeducao = dado.receita.classes.indexOf('4') === -1;
            dado.bloqueiaLinha = dado.apenasDeducao || (dado.desdobramento.valor < 0);

            valores.value.push(dado);
        });
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
}

const onCellEditComplete = (event) => {
    let {data, newValue, field} = event;

    if (typeof newValue === 'number') {
        data.contaSelecionada = data.desdobramento.contasBancarias.filter(contaBancaria => {
            return contaBancaria.conta === newValue;
        }).shift()
    }
}

function validar() {
    try {
        if (itensSelecionados.value.length === 0) {
            throw 'Selecione ao menos uma receita para realizar o decêndio.';
        }
    } catch (e) {
        toast.add({severity: 'error', detail: e, summary: 'Erro'});
        return false;
    }
    return true;
}

const confirmaAcao = (event) => {
    return new Promise((accept, reject) => {
        confirm.require({
            target: event.currentTarget,
            message: 'Confirma geração dos slips de decêndio?',
            icon: 'pi pi-exclamation-triangle',
            accept,
            reject
        });
    }).then(() => true).catch(() => false);
};

async function informacoesReduzido(dados) {

    const parameters = {
        exercicio: props.exercicio,
        instituicao: props.instituicao,
        reduzidos: dados.receita.reduzidos,
        desdobramento: dados.desdobramento.codigo,
        conta_pagadora: dados.conta_pagadora.reduzido,
        dataInicial: dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
    }

    detalhamentoSlip.value = []
    loading.value = true;
    mensagemLoad.value = 'Buscando informações.';

    await window.axios.get(rotas.detalhamento, {'params': parameters}).then(response => {
        response.data.data.forEach((dado) => {
            dado.id = uniqid();

            detalhamentoSlip.value.push(dado);
        });
        visibleDialogInfo.value = true;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
}

async function gerarSlips(event) {

    if (!validar()) {
        return false;
    }

    if (!await confirmaAcao(event)) {
        return
    }

    const paramentros = {
        dataInicial: dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        instituicao: props.instituicao,
        receitas: [],
    };

    itensSelecionados.value.forEach((valor) => {
        paramentros.receitas.push(valor);
    });

    loading.value = true;
    mensagemLoad.value = 'Gerando recibos, aguarde.';
    await window.axios.post(rotas.gerarSlips, paramentros).then(response => {

        visibleDialogSucess.value = true
        mensagemDialog.value = response.data.message;
        slipsGerados.value = response.data.data;
        buscar();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
}

function imprimirPdf() {
    const urlMatch = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g);
    const locationUrl = urlMatch !== null ? urlMatch[0] : '';
    let lista = slipsGerados.value.join(',')
    window.open(`${locationUrl}/cai1_slip003.php?numslip=${lista}`, '', 'location=0');
}

const onSelectAllChange = (event) => {
    selectAll.value = event.checked;

    itensSelecionados.value = [];
    if (selectAll.value) {
        valores.value.forEach(function (dado) {
            if (!dado.bloqueiaLinha) {
                itensSelecionados.value.push(dado);
            }
        });
    }
};

const disableRow = (row) => {
    return row.bloqueiaLinha ? 'p-disabled' : '';
}

</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Informe o período e busque as arrecadações" class="w-full md:w-11 lg:w-8 xl:w-6">
                <div class="formgrid grid mt-4 row-gap-2">
                    <div class="field col-12 sm:col:4 md:col-4 lg:col-3 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dataInicial" dateFormat="dd/mm/yy"
                                      :minDate="minDate" :maxDate="dataFinal"
                                      showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 150px;"/>
                            <label for="lb-periodo">Período de</label>
                        </div>
                    </div>

                    <div class="field col-12 sm:col:4 md:col-4 lg:col-3 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dataFinal" dateFormat="dd/mm/yy"
                                      :minDate="dataInicial" :maxDate="maxDate"
                                      showIcon placeholder="Data Final" showButtonBar :manualInput=true
                                      inputId="lb-periodo-ate"
                                      style="width: 150px;"/>
                            <label for="lb-periodo-ate"> Até</label>
                        </div>
                    </div>
                    <div class="field col-12 sm:col:4 md:col-4 lg:col-6 xl:col-6">
                        <Button type="button" label="Buscar" icon="pi pi-search" @click="buscar"/>
                    </div>
                </div>
            </Panel>
        </section>

        <Panel header="Receitas realizar o decêndio" class="w-full">
            <DataTable :value="valores" editMode="cell" @cell-edit-complete="onCellEditComplete"
                       scrollable scrollHeight="400px" v-model:selection="itensSelecionados"
                       :selectAll="selectAll" @select-all-change="onSelectAllChange" :rowClass="disableRow"
                       :pt="{
                            table: { style: 'min-width: 50rem' },
                            column: {
                                bodycell: ({ state }) => ({
                                    class: [{ 'pt-0 pb-0': state['d_editing'] }]
                                })
                            }
                        }"
            >
                <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                <Column field="receita.estrutural" header="Estrutural" style="width: 10%"/>
                <Column field="receita.descricao" header="Receita" style="width: 20%"/>
                <Column field="valor" header="Valor" style="width: 10%">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.valor) }}
                    </template>
                </Column>
                <Column field="desdobramento.descricao" header="Desdobramento" style="width: 10%"/>
                <Column field="valor" header="Valor Desdobramento" style="width: 10%">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.desdobramento.valor) }}
                    </template>
                </Column>
                <Column field="contaSelecionada" header="Débito" style="width: 20%">
                    <template #body="slotProps">
                        {{ slotProps.data?.contaSelecionada?.nome }}
                    </template>
                    <template #editor="{ data, field }">
                        <Dropdown v-model="data[field]" :options="data.desdobramento.contasBancarias"
                                  optionLabel="nome" optionValue="conta"
                                  placeholder="Conta débito">
                        </Dropdown>
                    </template>
                </Column>
                <Column field="conta_pagadora" header="Crédito" style="width: 20%">
                    <template #body="slotProps">
                        {{ slotProps.data.conta_pagadora.reduzido }} - {{ slotProps.data.conta_pagadora.descricao }}
                    </template>
                </Column>
                <Column header="Info">
                    <template #body="slotProps">
                        <i class="pi pi-info-circle" style="cursor:pointer; font-size: 2rem;"
                           @click="informacoesReduzido(slotProps.data)"></i>
                    </template>
                </Column>
            </DataTable>
        </Panel>
    </section>

    <section class="flex justify-content-center column-gap-2">
        <Button class="shadow-4" icon="pi pi-save" label="Gerar Slips" @click="gerarSlips($event)"/>
    </section>

    <Dialog v-model:visible="visibleDialogSucess" modal header="Sucesso" :style="{ width: '50vw' }">
        <p>{{ mensagemDialog }}</p>
        <template #footer>
            <Button label="Imprimir Slips" @click="imprimirPdf" icon="pi pi-print" autofocus></Button>
        </template>
    </Dialog>

    <Dialog v-model:visible="visibleDialogInfo" modal header="Detalhamento da arrecadação">
        <p>Lançamentos envolvidos</p>
        <DataTable :value="detalhamentoSlip" tableStyle="min-width: 50rem">
            <Column field="estrutural" header="Estrutural"></Column>
            <Column field="valor" header="Valor">
                <template #body="slotProps">
                    {{ formatCurrency(slotProps.data.valor) }}
                </template>
            </Column>
            <Column field="valor_desdobramento" header="Valor Desdobramento">
                <template #body="slotProps">
                    {{ formatCurrency(slotProps.data.valor_desdobramento) }}
                </template>
            </Column>
        </DataTable>
    </Dialog>

    <ConfirmPopup></ConfirmPopup>
    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
</template>
