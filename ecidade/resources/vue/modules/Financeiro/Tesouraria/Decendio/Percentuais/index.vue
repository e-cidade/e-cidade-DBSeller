<script setup>
import {ref, watch} from "vue";
import {useToast} from "primevue/usetoast";

import LookupRecurso from "@modules/Financeiro/Orcamento/Components/LookupRecurso.vue";
import ModalLoading from "@modules/Components/ModalLoading.vue";

const props = defineProps(['exercicio']);
const toast = useToast();

const loading = ref(false);
const mensagemLoad = ref('');
const mde = ref(0);
const asps = ref(0);
const fundeb = ref(0);
const visible = ref(false);
const msdDialog = ref('');

const selectedOrigem = ref();
const tipoDesdobramento = ref([
    {"id": 1, "descricao": "Impostos"},
    {"id": 2, "descricao": "Transferências"},
]);

const tipoDesdobramentoAtivo = ref('');
const recursoPesquisar = ref({});
const recursos = ref([]);
const recursosMde = ref([]);
const recursosAsps = ref([]);
const recursosFundeb = ref([]);

const rota = 'v4/api/financeiro/tesouraria/decendio/tipo-desdobramento';

async function buscaDadosDesdobramento() {
    mde.value = 0;
    asps.value = 0;
    fundeb.value = 0;
    recursosMde.value = [];
    recursosAsps.value = [];
    recursosFundeb.value = [];

    const parameters = {origem: selectedOrigem.value};

    loading.value = true;
    mensagemLoad.value = 'Aguarde, carregando dados';
    await window.axios.get(rota, {'params': parameters}).then(response => {
        response.data.data.forEach((tipoDesdobramento) => {
            if (tipoDesdobramento.descricao === 'MDE') {
                mde.value = tipoDesdobramento.percentual;
                recursosMde.value = tipoDesdobramento.recursos;
            }
            if (tipoDesdobramento.descricao === 'ASPS') {
                asps.value = tipoDesdobramento.percentual;
                recursosAsps.value = tipoDesdobramento.recursos;
            }
            if (tipoDesdobramento.descricao === 'FUNDEB') {
                fundeb.value = tipoDesdobramento.percentual;
                recursosFundeb.value = tipoDesdobramento.recursos;
            }
        });
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    });
    loading.value = false;
}

function configureRecursos(tipo) {
    msdDialog.value = 'Configure os recursos ';
    switch (tipo) {
        case 'mde':
            msdDialog.value += 'do MDE';
            recursos.value = recursosMde.value.map((dado) => {
                return dado;
            });
            break;
        case 'asps':
            msdDialog.value += 'do ASPS';
            recursos.value = recursosAsps.value.map((dado) => {
                return dado;
            });
            break;
        case 'fundeb':
            msdDialog.value += 'do FUNDEB';
            recursos.value = recursosFundeb.value.map((dado) => {
                return dado;
            });
            break;
    }
    tipoDesdobramentoAtivo.value = tipo;
    visible.value = true;
}

function fechandoDialog() {
    recursos.value = []
}


function removerRecurso(codigo) {
    recursos.value = recursos.value.filter((data) => {
        return data.orctiporec_id !== codigo;
    });

    switch (tipoDesdobramentoAtivo.value) {
        case 'mde':
            recursosMde.value = recursosMde.value.filter((data) => {
                return data.orctiporec_id !== codigo;
            });
            break;
        case 'asps':
            recursosAsps.value = recursosAsps.value.filter((data) => {
                return data.orctiporec_id !== codigo;
            });
            break;
        case 'fundeb':
            recursosFundeb.value = recursosFundeb.value.filter((data) => {
                return data.orctiporec_id !== codigo;
            });
            break;
    }
}

function validar() {
    try {
        if (mde.value === 0) {
            throw 'Informe um valor percentual maior que zero para o MDE.'
        }
        if (recursosMde.value.length === 0) {
            throw 'Você deve selecionar ao menos um recurso para o MDE'
        }
        if (asps.value === 0) {
            throw 'Informe um valor percentual maior que zero para o ASPS.'
        }
        if (recursosAsps.value.length === 0) {
            throw 'Você deve selecionar ao menos um recurso para o ASPS.'
        }
        if (fundeb.value !== 0 && recursosFundeb.value.length === 0) {
            throw 'Você deve selecionar ao menos um recurso para o FUNDEB.'
        }
    } catch (e) {
        toast.add({severity: 'error', detail: e, summary: 'Erro'});
        return false;
    }
    return true;
}

function salvar() {
    if (!validar()) {
        return
    }

    const param = {
        origem: selectedOrigem.value,
        desdobramentos: {
            mde: {
                codigo: 1,
                percentual: mde.value,
                recursos: recursosMde.value.map((data) => data.orctiporec_id)
            },
            asps: {
                codigo: 2,
                percentual: asps.value,
                recursos: recursosAsps.value.map((data) => data.orctiporec_id)
            },
            fundeb: {
                codigo: 3,
                percentual: fundeb.value,
                recursos: recursosFundeb.value.map((data) => data.orctiporec_id)
            }
        }
    }

    window.axios.post(rota, param).then(response => {
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    });
}

watch(recursoPesquisar, (newValue, oldValue) => {

    if (newValue?.id) {
        recursos.value.push(newValue);
        recursoPesquisar.value = {};

        switch (tipoDesdobramentoAtivo.value) {
            case 'mde':
                recursosMde.value.push(newValue);
                break;
            case 'asps':
                recursosAsps.value.push(newValue);
                break;
            case 'fundeb':
                recursosFundeb.value.push(newValue);
                break;
        }
    }
});
</script>

<template>
    <Message severity="info" :closable="false" :style="{marginTop:0}">
        <div style='font-size: 10pt; '>
            Informe os percentuais para cada desdobramento.<br>
            No botão "Recursos" ao lado de cada desdobramento, informe quais recursos pertencem.
        </div>
    </Message>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Configure os Desdobramentos" class="w-full sm:w-10 md:w-7 lg:w-6 xl:w-4">
                <div class="formgrid grid mt-2 row-gap-2">
                    <div class="field col-12 ">
                        <div class="p-float-label ">
                            <Dropdown v-model="selectedOrigem" :options="tipoDesdobramento"
                                      optionLabel="descricao" optionValue="id"
                                      class="w-full" id="dd-tipo" @change="buscaDadosDesdobramento"/>
                            <label for="dd-tipo">Selecione a origem de contas</label>
                        </div>
                    </div>
                    <div class="field col-12 sm:col-2">
                        <label for="mde" class="ml-2 font-bold">MDE:</label>
                    </div>
                    <div class="field col-12 sm:col-2">
                        <InputNumber v-model="mde" inputId="mde" suffix="%" :min="0" :max="100"
                                     style="width: 5rem" :pt="{input:{style: 'width:5rem'}}"/>
                    </div>
                    <div class="field col-12 sm:col-8">
                        <Button icon="pi pi-list" aria-label="Filter" label="Recursos"
                                @click="configureRecursos('mde')"/>
                    </div>

                    <div class="field col-12 sm:col-2">
                        <label for="mde" class="ml-2 font-bold">ASPS:</label>
                    </div>
                    <div class="field col-12 sm:col-2">
                        <InputNumber v-model="asps" inputId="asps" suffix="%" :min="0" :max="100"
                                     style="width: 5rem" :pt="{input:{style: 'width:5rem'}}"/>
                    </div>
                    <div class="field col-12 sm:col-8">
                        <Button icon="pi pi-list" aria-label="Filter" label="Recursos"
                                @click="configureRecursos('asps')"/>
                    </div>

                    <div class="field col-12 sm:col-2">
                        <label for="fundeb" class="ml-2 font-bold">FUNDEB:</label>
                    </div>

                    <div class="field col-12 sm:col-2">
                        <InputNumber v-model="fundeb" inputId="asps" suffix="%" :min="0" :max="100"
                                     style="width: 5rem" :pt="{input:{style: 'width:5rem'}}"/>
                    </div>
                    <div class="field col-12 sm:col-8">
                        <Button icon="pi pi-list" aria-label="Filter" label="Recursos"
                                @click="configureRecursos('fundeb')"/>
                    </div>
                </div>
            </Panel>
        </section>
    </section>

    <section class="flex justify-content-center mt-1 column-gap-2">
        <Button class="shadow-4" icon="pi pi-save" @click="salvar" label="Salvar"/>
    </section>

    <Dialog v-model:visible="visible" modal :header="msdDialog" class="w-full md:w-11 lg:w-10 xl:w-9 "
            @hide="fechandoDialog"
            :style="{ height: '600px' }">

        <Panel header="Selecione o Recurso para adicionar" class="w-full mb-1">
            <div class="field col-12 ">
                <LookupRecurso v-model="recursoPesquisar" :pesquisaCodigo="recursoPesquisar.codigo"
                               :exercicio="exercicio"/>
            </div>
        </Panel>

        <Panel header="Recursos Adicionados" class="w-full" :style="{ height: '500px' }">
            <DataTable :value="recursos" dataKey="codigo"
                       class="w-full" tableStyle="min-width: 50rem;" scrollable scrollHeight="500px">
                <Column field="siconfi" header="Siconfi"/>
                <Column field="gestao" header="Gestao"/>
                <Column field="subrecurso" header="Subrecurso"/>
                <Column field="complemento.codigo" header="Complemento"/>
                <Column field="descricao" header="Descricao"/>
                <Column field="acao" header="Ação">
                    <template #body="{ data }">
                        <Button icon="pi pi-trash" text rounded aria-label="Filter"
                                @click="removerRecurso(data.orctiporec_id)"/>
                    </template>
                </Column>
            </DataTable>
        </Panel>
    </Dialog>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
</template>
