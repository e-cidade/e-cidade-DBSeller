<template>
    <section class="mt-4 mb-4 w-full flex justify-content-center">
        <Panel class="w-7 xl:w-6" header="Filtros">
            <div class="formgrid grid col-12 align-items-center mb-3">
                <div class="col-8 flex flex-column">
                    <div>
                        <label>Data Atendimento</label>
                    </div>

                    <div class="flex align-items-center">
                        <Calendar class="p-inputtext-sm w-9rem"
                                  v-model="dataInicial"
                                  v-mask="'##/##/####'"
                                  :showIcon="true"
                                  :showOnFocus="false"/>

                        <label class="p-2">Até:</label>

                        <Calendar class="p-inputtext-sm w-9rem"
                                  v-model="dataFinal"
                                  v-mask="'##/##/####'"
                                  :showIcon="true"
                                  :showOnFocus="false"/>
                    </div>
                </div>

                <div class="flex align-items-center column-gap-2 col-3">
                    <Checkbox :binary="true" inputId="atendimentoAberto" v-model="atendimentoAberto"/>
                    <label for="atendimentoAberto"
                           class="mb-0 w-10rem">Atendimento Aberto</label>
                </div>
            </div>
            <Accordion >
                <AccordionTab header="Filtros adicionais">
                    <div class="card">
                        <div class="formgrid grid">
                            <div class="col-6 flex flex-column paddingBCampos">
                                <label>FAA</label>
                                <InputNumber :useGrouping="false"
                                             maxlength="12"
                                             type="text"
                                             class="p-inputtext-sm w-2rem"
                                             v-model="faa"
                                             inputStyle="width:100px;"/>
                            </div>

                            <div class="col-6 flex flex-column align-items-end paddingBCampos">
                                <label>Motivo</label>
                                <Dropdown class="w-11 text-left p-inputtext-sm"
                                          v-model="motivoAtendimentoSelecionado"
                                          :options="motivosDeAtendimento"
                                          optionValue="codigo"
                                          optionLabel="descricao"/>
                            </div>

                            <div class="col-6 flex flex-column paddingBCampos">
                                <label>Paciente</label>

                                <AutoComplete class="w-9"
                                              inputClass="w-full"
                                              inputId="pacienteId"
                                              maxlength="255"
                                              v-model="paciente"
                                              optionLabel="cgsNome"
                                              :suggestions="pacientesFiltrados"
                                              forceSelection
                                              @blur="camposVazios(true)"
                                              @focusout="campoScrollZero"
                                              @complete="buscaPaciente">
                                    <template #option="slotProps">
                                        <div class="flex align-options-center">
                                            <div>{{ slotProps.option.codigo }} - {{ slotProps.option.nome }}</div>
                                        </div>
                                    </template>
                                </AutoComplete>
                            </div>

                            <div class="col-6 text-right flex flex-column align-items-end paddingBCampos">
                                <label>Profissional em Atendimento</label>

                                <AutoComplete class="w-11 text-left p-inputtext-sm"
                                              inputId="medicoAtendendo"
                                              dropdown
                                              optionLabel="nome"
                                              v-model="profissionalAtendimentoSelecionado"
                                              :suggestions="medicosFiltrados"
                                              forceSelection
                                              @focusout="campoScrollZero"
                                              @complete="buscaMedicoAtendimento"/>
                            </div>

                            <div class="col-6 flex flex-column paddingBCampos">
                                <label>Prioridade</label>
                                <Dropdown class="w-6 p-inputtext-sm"
                                          v-model="classificacaoRiscoSelecionado"
                                          :options="classificacoesDeRisco"
                                          optionValue="codigo"
                                          optionLabel="descricao"/>
                            </div>

                            <div class="col-6 text-right flex flex-column align-items-end paddingBCampos">
                                <label>Profissional Encaminhado</label>

                                <AutoComplete class="w-11 text-left p-inputtext-sm"
                                              inputId="medicoEncaminhado"
                                              dropdown
                                              optionLabel="nome"
                                              v-model="profissionalEncaminhadoSelecionado"
                                              :suggestions="medicosFiltrados"
                                              forceSelection
                                              @focusout="campoScrollZero"
                                              @complete="buscaMedicoAtendimento"/>
                            </div>

                            <div class="col-6 flex flex-column paddingBCampos">
                                <label>Setor</label>
                                <Dropdown class="w-10 p-inputtext-sm"
                                          v-model="setorSelecionado"
                                          :options="setores"
                                          optionValue="codigo"
                                          optionLabel="descricao"/>
                            </div>

                            <div class="col-6 flex flex-column align-items-end paddingBCampos">
                                <label>Status Paciente</label>
                                <Dropdown class="w-6 p-inputtext-sm"
                                          v-model="statusPacienteSelecionado"
                                          :options="statusPaciente"
                                          optionValue="code"
                                          optionLabel="descr" @change="situacaoFinalizada()"/>
                            </div>
                        </div>
                    </div>
                </AccordionTab>
            </Accordion>
            <div class="flex justify-content-center column-gap-3 col-12">
                <Button @click="pesquisaPacientes()" label="Pesquisar"/>
                <Button @click="limpaCampos()" label="Limpar"/>
            </div>
        </Panel>
    </section>

    <section class="flex justify-content-center">
        <DataTable class="w-full lg:w-11"
                   :value="dadosPrincipais"
                   showGridlines
                   :paginator="true"
                   :rows="15"
                   paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink
                   LastPageLink RowsPerPageDropdown"
                   :rowsPerPageOptions="[15,20,50]"
                   responsiveLayout="scroll"
                   currentPageReportTemplate="Foram retornados {totalRecords} registros. Mostrando de {first} até {last}"
                   v-model:expandedRows="expandedRows"
                   v-model:filters="filtrosTabela"
                   filterDisplay="menu"
                   :globalFilterFields="[
                         'faa',
                         'data',
                         'hora',
                         'cgs',
                         'nome',
                         'nomeSocial',
                         'idadeCompleta',
                         'prioridade',
                         'motivo',
                         'setor',
                         'medicoAtendendo',
                         'situacao',
                         'medicoEncaminhado'
                   ]">
            <template #paginatorend>
                <div class="flex justify-content-between">
                    <Button type="button" icon="pi pi-filter-slash" label="Limpar" class="p-button-outlined"
                            @click="limpaFiltroTabela()"/>
                    <span class="p-input-icon-left">
                        <i class="pi pi-search"/>
                        <InputText v-model="filtrosTabela['global'].value" placeholder="Indique o Conteúdo"/>
                    </span>
                </div>
            </template>
            <template #empty>
                <h1 class="text-center font-medium">
                    Nenhum Registro Encontrado
                </h1>
            </template>
            <Column :expander="true" headerStyle="width: 3rem"/>
            <Column bodyStyle="text-align:center;" field="faa">
                <template #header>
                    <div class="w-full text-center">
                        <span>FAA</span>
                    </div>
                </template>
            </Column>
            <Column bodyStyle="text-align:center;" field="data">
                <template #header>
                    <div class="w-full text-center">
                        <span>Data Atend</span>
                    </div>
                </template>
            </Column>
            <Column bodyStyle="text-align:center;" field="hora">
                <template #header>
                    <div class="w-full text-center">
                        <span>Hora Atend</span>
                    </div>
                </template>
            </Column>
            <Column bodyStyle="text-align:center;" field="cgs">
                <template #header>
                    <div class="w-full text-center">
                        <span>CGS</span>
                    </div>
                </template>
            </Column>
            <Column field="nome">
                <template #header>
                    <div class="w-full text-center">
                        <span>Nome</span>
                    </div>
                </template>
            </Column>
            <Column field="nomeSocial">
                <template #header>
                    <div class="w-full text-center">
                        <span>Nome Social</span>
                    </div>
                </template>
            </Column>
            <Column bodyStyle="text-align:center;" field="idadeCompleta">
                <template #header>
                    <div class="w-full text-center">
                        <span>Idade</span>
                    </div>
                </template>
            </Column>
            <Column bodyStyle="text-align:center;" field="prioridade">
                <template #header>
                    <div class="w-full text-center">
                        <span>Prioridade</span>
                    </div>
                </template>
                <template #body="slotProps">
                    <div :class="corPrioridade(slotProps.data.corPrioridade)">
                        <span>{{ slotProps.data.prioridade }}</span>
                    </div>
                </template>
            </Column>
            <Column bodyStyle="text-align:center;" field="motivo">
                <template #header>
                    <div class="w-full text-center">
                        <span>Motivo</span>
                    </div>
                </template>
            </Column>
            <Column bodyStyle="text-align:center;" field="setor">
                <template #header>
                    <div class="w-full text-center">
                        <span>Setor Atendimento</span>
                    </div>
                </template>
            </Column>
            <Column field="medicoAtendendo">
                <template #header>
                    <div class="w-full text-center">
                        <span>Profissional em Atendimento</span>
                    </div>
                </template>
            </Column>
            <Column headerStyle="justify-content: center;" bodyStyle="text-align:center;" field="situacao">
                <template #header>
                    <div class="w-full text-center">
                        <span>Status Paciente</span>
                    </div>
                </template>
                <template #body="slotProps">
                    <div :class="corStatus(slotProps.data.codigoSituacao)">
                        {{ slotProps.data.situacao }}
                    </div>
                </template>
            </Column>
            <Column field="medicoEncaminhado">
                <template #header>
                    <div class="w-full text-center">
                        <span>Profissional Encaminhado</span>
                    </div>
                </template>
            </Column>
            <template #expansion="slotProps">
                <div class="orders-subtable -mt-4 mb-5">
                    <h5>HISTÓRICO DE MOVIMENTAÇÃO DE {{ slotProps.data.nome }}</h5>
                    <DataTable class="-mt-3" :value="slotProps.data.historicos" responsiveLayout="scroll">
                        <template #empty>
                            <h1 class="text-center font-medium">
                                Sem histórico de movimentação
                            </h1>
                        </template>
                        <Column headerStyle="color:white; background:#93b4cf" bodyStyle="text-align:center; background:#f3eeee" field="hora">
                            <template #header>
                                <div class="w-full text-center">
                                    <span>Hora</span>
                                </div>
                            </template>
                        </Column>
                        <Column headerStyle="color:white; background:#93b4cf" bodyStyle="text-align:center; background:#f3eeee" field="setor">
                            <template #header>
                                <div class="w-full text-center">
                                    <span>Setor Atendimento</span>
                                </div>
                            </template>
                        </Column>
                        <Column headerStyle="color:white; background:#93b4cf" bodyStyle="background:#f3eeee" field="medicoAtendendo">
                            <template #header>
                                <div class="w-full text-center">
                                    <span>Profissional em Atendimento</span>
                                </div>
                            </template>
                        </Column>
                        <Column headerStyle="color:white; background:#93b4cf" bodyStyle="text-align:center; background:#f3eeee" field="situacao">
                            <template #header>
                                <div class="w-full text-center">
                                    <span>Status Paciente</span>
                                </div>
                            </template>
                            <template #body="slotProps">
                                <div :class="corStatus(slotProps.data.codigoSituacao)">
                                    {{ slotProps.data.situacao }}
                                </div>
                            </template>
                        </Column>
                        <Column headerStyle="color:white; background:#93b4cf" bodyStyle="background:#f3eeee" field="medicoEncaminhado">
                            <template #header>
                                <div class="w-full text-center">
                                    <span>Profisional Encaminhado</span>
                                </div>
                            </template>
                        </Column>
                    </DataTable>
                    <hr class="bg-gray-600" style="padding: 1px;">
                </div>
            </template>
        </DataTable>
    </section>

    <Toast></Toast>

    <ModalLoading :isLoading="carregando"/>
</template>

<script setup>

import { onMounted, ref, watch } from 'vue';
import ModalLoading from "../../../Components/ModalLoading.vue";
import Toast from 'primevue/toast';
import {useToast} from "primevue/usetoast";
import {FilterMatchMode,FilterOperator} from 'primevue/api';

const faa = ref(null);
const paciente = ref(null);
const classificacoesDeRisco = ref([]);
const classificacaoRiscoSelecionado = ref(null);
const motivosDeAtendimento = ref([]);
const motivoAtendimentoSelecionado = ref(null);
const setores = ref([]);
const setorSelecionado = ref(null);
const listaDeProfissionais = ref([]);
const profissionalAtendimentoSelecionado = ref(null);
const statusPaciente = ref([
    {descr: '', code: null},
    {descr: 'Aguard Atendimento', code: 1},
    {descr: 'Em Atendimento', code: 2},
    {descr: 'Finalizado', code: 3},
]);
const statusPacienteSelecionado = ref(null);
const profissionalEncaminhadoSelecionado = ref(null);
const dataInicial = ref(new Date());
const dataFinal = ref(new Date());
const atendimentoAberto = ref(true);
const dadosPrincipais = ref([]);
const carregando = ref(false);
const parametrosPesquisaIniciais = ref(null);
const pesquisou = ref(true);
const expandedRows = ref([]);
const prontuarios = ref([]);
const toast = useToast();
const filtrosTabela = ref({
    'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
    'faa': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'data': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'hora': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'cgs': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'nome': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'nomeSocial': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'idadeCompleta':{operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'prioridade': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'motivo': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'setor': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'medicoAtendendo': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'situacao': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
    'medicoEncaminhado': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]},
});
const medicosFiltrados = ref(null);
const pacientesFiltrados = ref(null);
const timeoutID = ref(null);
const watchValues = ref([
    faa,
    classificacaoRiscoSelecionado,
    setorSelecionado,
    motivoAtendimentoSelecionado,
    profissionalAtendimentoSelecionado,
    profissionalEncaminhadoSelecionado,
    statusPacienteSelecionado
]);

function limpaFiltroTabela() {
    filtrosTabela.value.global = {value: null, matchMode: FilterMatchMode.CONTAINS};
    filtrosTabela.value.faa = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.data = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.hora = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.cgs = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.nome = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.nomeSocial = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.idadeCompleta = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]}
    filtrosTabela.value.prioridade = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.motivo = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.setor = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.medicoAtendendo = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.situacao = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
    filtrosTabela.value.medicoEncaminhado = {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]};
}

async function limpaCampos() {
    faa.value = null;
    paciente.value = null;
    classificacaoRiscoSelecionado.value = null;
    motivoAtendimentoSelecionado.value = null;
    setorSelecionado.value = null;
    profissionalEncaminhadoSelecionado.value = null;
    statusPacienteSelecionado.value = null;
    profissionalAtendimentoSelecionado.value = null;
    dataInicial.value = new Date();
    dataFinal.value = new Date();
    atendimentoAberto.value = true;
    await pesquisaPacientes();
}

async function pesquisaPacientes() {
    carregando.value = true;
    try {
        parametrosPesquisaIniciais.value = {
            faa: faa.value,
            paciente: paciente.value ? paciente.value.codigo : null,
            classificacaoRisco: classificacaoRiscoSelecionado.value,
            motivoAtendimento: motivoAtendimentoSelecionado.value,
            setor: setorSelecionado.value,
            profissionalEncaminhado: profissionalEncaminhadoSelecionado.value ? profissionalEncaminhadoSelecionado.value.codigo : null,
            situacao: statusPacienteSelecionado.value,
            profissionalAtendimento: profissionalAtendimentoSelecionado.value ? profissionalAtendimentoSelecionado.value.codigo : null,
            dataInicial: dataInicial.value.toISOString(),
            dataFinal: dataFinal.value.toISOString(),
            atendimentoAberto: atendimentoAberto.value,
        };
        await buscaRegistros();
    } catch (e) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Ooops! Não foi possível recuperar os dados.',
            life: 3000
        });
    }
    carregando.value = false;
}

async function montaSelects() {
    carregando.value = true;
    try {
        const retornoClassificacoesDeRisco = await window.axios.get(
            `v4/api/saude/ambulatorial/consulta/classificacoesderisco`
        );

        const retornoSetoresAmbulatoriais = await window.axios.get(
            `v4/api/saude/ambulatorial/consulta/setoresambulatoriais`
        );

        const retornoMotivosDeAtendimento = await window.axios.get(
            `v4/api/saude/ambulatorial/consulta/motivosdeatendimento`
        );

        const retornoListaDeProfissionais = await window.axios.get(
            `v4/api/saude/ambulatorial/consulta/especialidadeprofissionais/ativas`
        );

        let campoVazio = {
            codigo: '',
            nome: ''
        };

        classificacoesDeRisco.value = retornoClassificacoesDeRisco.data.data;
        classificacoesDeRisco.value.unshift(campoVazio);

        setores.value = retornoSetoresAmbulatoriais.data.data;
        setores.value .unshift(campoVazio);

        motivosDeAtendimento.value = retornoMotivosDeAtendimento.data.data;
        motivosDeAtendimento.value.unshift(campoVazio);

        listaDeProfissionais.value = retornoListaDeProfissionais.data.data;
        listaDeProfissionais.value.unshift(campoVazio);
    } catch (e) {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Ooops! Houve um erro na hora de carregar os filtros.',
            life: 3000
        });
    }
    carregando.value = false;
}

async function buscaRegistros() {
    const resp = await window.axios.post(
        `v4/api/saude/ambulatorial/consulta/statuspaciente`,
        parametrosPesquisaIniciais.value
    );

    if(JSON.stringify(dadosPrincipais.value) !== JSON.stringify(resp.data.data)) {
        dadosPrincipais.value = resp.data.data;
    }
}

function corStatus(situacao) {
    return [
        {
            'aguardAtendimento': situacao === 1,
            'emAtendimento': situacao === 2,
            'finalizado': situacao === 3,
            'encaminhado': situacao === 5,
        }
    ];
}

function corPrioridade(cor) {
    if (!empty(cor)) {
        return [
            {
                'emergencia': cor === '#EC3136',
                'muitoUrgente': cor === '#F68634',
                'urgente': cor === '#FAD902',
                'poucoUrgente': cor === '#01A85A',
                'naoUrgente': cor === '#0095DF',
            }
        ];
    }
}

function situacaoFinalizada() {
    atendimentoAberto.value = statusPacienteSelecionado.value !== 3;
}

function buscaMedicoAtendimento(event) {
    if (!event.query.trim().length) {
        medicosFiltrados.value = [...listaDeProfissionais.value];
    } else {
        medicosFiltrados.value = listaDeProfissionais.value.filter((medico) => {
            return medico.nome.toLowerCase().includes(event.query.toLowerCase());
        });
    }
}
function buscaPaciente(event) {
    if (event.query.trim().length) {
        let dadoPaciente = event.query.trim();
        const parametros = Object.assign({}, parametrosPesquisaIniciais.value);

        if (!isNaN(dadoPaciente)) {
            parametros.paciente = dadoPaciente;
        } else if (dadoPaciente.length >= 3){
            parametros.pacienteNome = dadoPaciente;
        }

        if (parametros.paciente === null && parametros['pacienteNome'] === undefined) {
            pacientesFiltrados.value = [];
            return;
        }

        clearTimeout(timeoutID.value);
        timeoutID.value = setTimeout(async function() {
            try {
                const resp = await window.axios.post(
                    `v4/api/saude/ambulatorial/consulta/statuspaciente/buscapacienteprontuario`,
                    parametros
                );

                pacientesFiltrados.value = [];
                resp.data.data.forEach(element => {
                    pacientesFiltrados.value.push({
                        nome: element.nome,
                        codigo: element.codigo,
                        cgsNome: `${element.codigo} - ${element.nome}`
                    });
                });
            } catch (e) {
                pacientesFiltrados.value = [];
            }

        }, 3000);
    }
}

function camposVazios(teveEvento) {
    let campoFaa = faa.value;
    let campoPaciente = paciente.value;
    let campoPrioridade = classificacaoRiscoSelecionado.value;
    let campoSetor = setorSelecionado.value;
    let campoMotivo = motivoAtendimentoSelecionado.value;
    let campoAtendendo = profissionalAtendimentoSelecionado.value ? profissionalAtendimentoSelecionado.value.codigo : null;
    let campoEncaminhado = profissionalEncaminhadoSelecionado.value ? profissionalEncaminhadoSelecionado.value.codigo : null;
    let campoStatus = statusPacienteSelecionado.value;

    if(campoPaciente !== '' && teveEvento) {
        campoPaciente = false
    } else if(campoPaciente === '' && teveEvento) {
        campoPaciente = true
    }

    if(empty(campoPaciente) && !teveEvento) {
        campoPaciente = true
    }

    if (
        empty(campoFaa) &&
        campoPaciente &&
        empty(campoPrioridade) &&
        empty(campoSetor) &&
        empty(campoMotivo) &&
        empty(campoAtendendo) &&
        empty(campoEncaminhado) &&
        empty(campoStatus)
    ) {
        atendimentoAberto.value = true;
    }
}

function campoScrollZero(event) {
    let campoValue = event.originalTarget.id;
    let pacienteValue = document.getElementById(campoValue);

    if (pacienteValue !== null) {
        pacienteValue.scrollLeft = 0;
    }
}

onMounted(async () => {
    await montaSelects();
    await pesquisaPacientes();
    setInterval(() => {

        try {
            buscaRegistros()
        } catch (e) {}

    }, 5000);
});

watch(watchValues.value, () => {
    camposVazios(false);
});


</script>

<style scoped>
* {
    font-size: 14px;
}

.aguardAtendimento {
    font-weight: 700;
    color: red;
}

.encaminhado {
    font-weight: 700;
    color: orangered;
}

.emAtendimento {
    font-weight: 700;
    color: green;
}

.finalizado {
    font-weight: 700;
    color: blue;
}

.emergencia {
    background-color: #EC3136;
    text-align: center;
    padding: 10px;
}

.muitoUrgente {
    background-color: #F68634;
    text-align: center;
    padding: 10px;
}

.urgente {
    background-color: #FAD902;
    text-align: center;
    padding: 10px;
}

.poucoUrgente {
    background-color: #01A85A;
    text-align: center;
    padding: 10px;
}

.naoUrgente {
    background-color: #0095DF;
    text-align: center;
    padding: 10px;
}

.largura-panel{
    width: 50rem;
}

.paddingBCampos{
    margin-bottom: 5px;
}

</style>
