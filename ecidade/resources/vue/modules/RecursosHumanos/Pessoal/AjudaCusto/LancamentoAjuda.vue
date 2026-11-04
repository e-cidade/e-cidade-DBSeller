<script setup>

import DialogMatricula from "../Components/DialogMatricula.vue"
import ModalLoading from "../../../Components/ModalLoading.vue";
import DialogConsultaCgm from "../../../../modules/Patrimonial/Protocolo/Components/DialogConsultaCgm.vue";

import {useToast} from "primevue/usetoast";
import {onMounted, ref} from "vue";
import {FilterMatchMode} from "primevue/api";

const props = defineProps(["instituicao"]);

const routes = {
    lancamento: `v4/api/recursos-humanos/pessoal/ajudacusto/lancamento`,
    lancamentos: `v4/api/recursos-humanos/pessoal/ajudacusto/lancamentos`,
    buscar: `v4/api/recursos-humanos/pessoal/ajudacusto/config-buscar/${props.instituicao}`,
    processamento: `v4/api/recursos-humanos/pessoal/ajudacusto/processamento`,
    depententes : `v4/api/recursos-humanos/pessoal/rhdepend/dependentes`,
}

const confirmDelete = ref(false);
const toast = useToast();
const loading = ref(false)
const callback = ref();
const dialogServidor = ref();
const dialogConsultaCgm = ref(null);
const visible = ref(false);
const timeLine = ref(false);
const result = ref([]);
const selecionado = ref([]);
const dependentes = ref([]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});


const data = ref({
    id: null,
    matricula: null,
    servidor: null,
    dependente: null,
    local: '',
    especializacao: '',
    graduacao: '',
    unidade_ensino: '',
    codigo_unidade : null,
    competencia: '',
    valor: 0,
    quantidade: 0,
    bolsa_publica: false,
    habilitaDependente: false
});

const events = ref([]);

const config = ref({
    idade_maxima: 0,
    valor_limite: 0,
    grau_dependentes : []
});

function openDialogServidor() {
    callback.value = getServidor;
    dialogServidor.value.openModal();
}

function abrirConsultaCgm() {
    dialogConsultaCgm.value.toggleDialog();
}

function selecionaCgm(result) {
    data.value.unidade_ensino = result.nome;
    data.value.codigo_unidade = result.numcgm;
}

async function getServidor(response) {
    data.value.matricula = response.rh01_regist
    data.value.servidor = `${response.rh01_regist} - ${response.z01_nome}`;
    dependentes.value = [];
    await getDependente();
}

async function getDependente() {
    loading.value = true
    await window.axios
        .get(routes.depententes + `/${data.value.matricula}`)
        .then((response) => {
            let rDependentes = response.data.data;
            if (config.value.grau_dependentes.length > 0) {
                rDependentes = rDependentes.filter(d => config.value.grau_dependentes.find(grau => {
                    return grau == d.rh31_gparen;
                }));
            }

            dependentes.value = rDependentes.map(dependente => {
                return {code: dependente.rh31_codigo, name: dependente.rh31_nome}
            });
            loading.value = false
        });
}

async function salvar () {

    let parametros = {}
    parametros.matricula = data.value.matricula;
    parametros.servidor = data.value.servidor;
    parametros.codigo_dependente = data.value.dependente?.code;
    parametros.local = data.value.local;
    parametros.especializacao = data.value.especializacao;
    parametros.graduacao = data.value.graduacao;
    parametros.unidade_ensino = data.value.codigo_unidade;
    parametros.competencia = data.value.competencia;
    parametros.valor = data.value.valor;
    parametros.quantidade = data.value.quantidade;
    parametros.bolsa_publica = data.value.bolsa_publica;
    parametros.habilitaDependente = data.value.habilitaDependente;
    parametros.id = data.value.id;

    try {
        loading.value = true
        await window.axios.post(routes.lancamento, parametros)
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Salvo com sucesso`,
            life: 5000
        });
        visible.value = false;
        limparCampos();
        await buscaDados();

    } catch (e) {
        loading.value = false
        let message = e.response ? e.response.data.message : e.message;
        if (e.response.status === 422) {
            message = Object.values(e.response.data).join("\n")
        }
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: message,
            life: 5000
        });
    }
}

async function deletar () {

    try {
        loading.value = true;
        confirmDelete.value = false;
        await window.axios.delete(routes.lancamento+`/${selecionado.value.id}`)
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Excluído com sucesso`,
            life: 5000
        });

        limparCampos();
        await buscaDados();
        visible.value = false;

    } catch (e) {
        loading.value = false;
        confirmDelete.value = false;

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response ? e.response.data.message : e.message,
            life: 5000
        });
    }
}

async function buscaConfiguracao () {
    loading.value = true
    await window.axios
        .get(routes.buscar)
        .then((response) => {
            let configuracao = response.data.data.configuracao;
            config.value.idade_maxima = configuracao.rh311_idademax;
            config.value.valor_limite = configuracao.rh311_valormax;
            config.value.grau_dependentes = configuracao.rh311_grauparentesco ? configuracao.rh311_grauparentesco : [];
            loading.value = false
        });
}

async function buscaDados () {
    loading.value = true
    await window.axios
        .get(routes.lancamento)
        .then((response) => {
            result.value = response.data.data;
            loading.value = false;
        });
}

async function visualizarLancamento(lancamento) {

    try {
        loading.value = true;
        events.value = [];

        await window.axios
            .get(routes.lancamentos+`/${lancamento.id}`)
            .then(async (response) => {
                events.value = response.data.data.competencias;

                timeLine.value = true;
                loading.value = false;
            });

    } catch (e) {
        loading.value = false
        console.log(e);
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response ? e.response.data.message : e.message,
            life: 5000
        });
    }
}

async function edit (obj) {
    try {
        loading.value = true
        await window.axios
            .get(routes.lancamento+`/${obj.id}`)
            .then(async (response) => {
                data.value = response.data.data;
                await getDependente();

                let optionDependente = dependentes.value.filter(opt =>
                    opt.code === data.value.codigo_dependente
                ).shift();

                data.value.dependente = optionDependente;
                visible.value = true;
                loading.value = false;
            });

    } catch (e) {
        loading.value = false
        console.log(e);
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response ? e.response.data.message : e.message,
            life: 5000
        });
    }
}

async function lancarValores () {

    try {
        loading.value = true
        await window.axios
            .post(routes.processamento)
            .then(async (response) => {

                limparCampos();
                await buscaDados();
                visible.value = false;
                confirmDelete.value = false;

                toast.add({
                    severity: 'success',
                    summary: 'Concluido',
                    detail:  response ? response.data.message : message,
                    life: 5000
                });
            });

    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response ? e.response.data.message : e.message,
            life: 5000
        });
    }
}

function novoCadastro () {
    limparCampos();
    visible.value = true;
}

function formatCurrency (value) {
    return new Intl.NumberFormat(
        'pt-BR',
        {
            style: 'currency',
            currency: 'BRL'
        }
    ).format(value);
}

function confirmDeleteAjuda (data) {
    selecionado.value   = data;
    confirmDelete.value = true;
};

function limparCampos () {
    data.value.id = null;
    data.value.matricula = null;
    data.value.servidor = null;
    data.value.dependente = [];
    data.value.local = '';
    data.value.especializacao = '';
    data.value.graduacao = '';
    data.value.unidade_ensino = '';
    data.value.codigo_unidade = null;
    data.value.competencia = '';
    data.value.valor = 0;
    data.value.quantidade = 0;
    data.value.bolsa_publica = false;
    data.value.habilitaDependente = false;
}

onMounted(() => {
    buscaConfiguracao();
    buscaDados();
});

</script>
<template>
    <Toast position="center" />
    <Dialog v-model:visible="visible" maximizable header="Cadastrar" :style="{ width: '80vw' }">
        <section class="flex flex-column w-full mt-4 gap-2">
            <section class="flex justify-content-center">
                <Panel header="Dados Servidor" class="w-full xl:w-7">
                    <div class="formgrid grid mt-4">
                        <div class="field col-6">
                            <div class="p-inputgroup flex-1">
                                <span class="p-float-label">
                                    <AutoComplete v-model="data.servidor" inputId="servidor" disabled required />
                                    <label for="servidor">Servidor para lançamento</label>
                                    <span class="p-inputgroup-addon" @click="openDialogServidor">
                                        <i class="pi pi-search"></i>
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="field col-6 justify-content-center">
                            <div>Lançar para dependentes? </div>
                            <InputSwitch class="mt-2" v-model="data.habilitaDependente" inputId="habilitaDependente" />
                        </div>
                    </div>
                    <div class="formgrid grid mt-1" v-if="data.habilitaDependente">
                        <div class="field col-6">
                            <div class="p-inputgroup flex-1">
                                <span class="p-float-label">
                                    <Dropdown
                                        v-model="data.dependente"
                                        inputId="dependente"
                                        editable
                                        optionLabel="name"
                                        :options="dependentes"
                                        placeholder="Selecione"
                                    />
                                    <label for="dependente">Dependente para lançamento</label>
                                </span>
                            </div>
                        </div>
                    </div>
                </Panel>
            </section>
        </section>
        <section class="flex flex-column w-full mt-4 gap-2">
            <section class="flex justify-content-center">
                <Panel header="Questionário" class="w-full lg:w-6 xl:w-7" toggleable>
                    <div class="formgrid grid mt-2">
                        <div class="field col-6">
                            <div class="flex flex-column">
                                <label for="local">Local</label>
                                <InputText type="text" inputId="local" v-model="data.local" />
                            </div>
                        </div>
                        <div class="field col-6 ">
                            <div class="flex flex-column">
                                <label for="especializacao">Especialização</label>
                                <InputText type="text" inputId="especializacao" v-model="data.especializacao" />
                            </div>
                        </div>
                    </div>

                    <div class="formgrid grid mt-2">
                        <div class="field col-6">
                            <div class="flex flex-column">
                                <label for="graducao">Graduação</label>
                                <InputText type="text" inputId="graducao" v-model="data.graduacao" />
                            </div>
                        </div>
                        <div class="field col-6 ">
                            <div class="flex flex-column">
                                <label for="unidade_ensino">Unidade de ensino</label>
                                <div class="p-inputgroup flex-1">
                                    <AutoComplete v-model="data.unidade_ensino" inputId="unidade_ensino" disabled required />
                                     <span class="p-inputgroup-addon" @click="abrirConsultaCgm">
                                        <i class="pi pi-search"></i>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="formgrid grid mt-3">
                        <div class="field col">
                            <div class="flex flex-column">
                                <label for="valor">Valor</label>
                                <InputNumber inputId="valor" v-model="data.valor" mode="currency" currency="BRL" showButtons
                                    required />
                            </div>
                        </div>
                        <div class="field col">
                            <div class="flex flex-column">
                                <label for="quantidade">Quantidade de Parcelas</label>
                                <InputNumber inputId="quantidade" v-model="data.quantidade" showButtons mode="decimal"
                                    required />
                            </div>
                        </div>
                        <div class="field col">
                            <div class="flex flex-column">
                                <label for="competencia">Competencia Referência</label>
                                <InputMask inputId="competencia" v-model="data.competencia" mask="99/9999"
                                    placeholder="MM/AAAA" required />
                            </div>
                        </div>
                    </div>
                    <div class="field col-6 justify-content-center">
                        <div>Possui Bolsa Pública? </div>
                        <InputSwitch class="mt-2" v-model="data.bolsa_publica" />
                    </div>
                </Panel>
            </section>
            <div class="card pt-2 ">
                <div class="flex justify-content-center flex-wrap card-container">
                    <Button type="button" label="Salvar" icon="pi pi-save"  @click="salvar" />
                </div>
            </div>
            <DialogMatricula ref="dialogServidor" v-on:selectRow="callback" />
            <DialogConsultaCgm ref="dialogConsultaCgm" @selectRow="selecionaCgm" />
            <ModalLoading :isLoading="loading" />
        </section>
    </Dialog>

    <section class="flex flex-column w-full mt-4 gap-2">
        <section class="flex justify-content-center">
            <Panel header="Ajuda de custo" class="w-full lg:w-6 xl:w-7" toggleable>

                <DataTable
                    v-model:filters="filters"
                    :value="result"
                    paginator :rows="5"
                    :rowsPerPageOptions="[5, 10, 20, 50]"
                    filterDisplay="row"
                    :loading="loading"
                    :globalFilterFields="['nome', 'matricula', 'dependente', 'valor', 'competencia']">
                    <template #header>
                        <div class="flex justify-content-between flex-wrap">
                            <div class="flex align-items-center justify-content-center">
                                <Button label="Cadastrar"
                                        icon="pi pi-user-plus"
                                        v-tooltip.top="'Cadastrar uma nova ajuda para servidor / dependente'"
                                        @click="novoCadastro"/>
                                <Button label="Lançar no ponto"
                                        severity="success"
                                        class="ml-2"
                                        icon="pi pi-dollar"
                                        v-tooltip.top="'Irá Lançar os valores correspondente a ajuda da competência da folha'"
                                        @click="lancarValores"
                                />
                            </div>
                            <div class="flex align-items-center justify-content-center">
                                <span class="p-input-icon-left">
                                    <i class="pi pi-search" />
                                    <InputText placeholder="Pesquisar" v-model="filters['global'].value"/>
                                </span>
                            </div>
                        </div>
                    </template>
                    <Column field="matricula" header="Matricula" />
                    <Column field="nome" header="Servidor" />
                    <Column field="dependente" header="Nome Dependente" />
                    <Column field="valor" header="Valor">
                        <template #body="{ data, field }">
                            {{ formatCurrency(data[field]) }}
                        </template>
                    </Column>
                    <Column field="competencia" header="Referência" />
                    <Column header="Ação">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="edit(slotProps.data)" />
                            <Button icon="pi pi-trash"  outlined rounded severity="danger" @click="confirmDeleteAjuda(slotProps.data)" />
                            <Button icon="pi pi-eye"    outlined rounded class="ml-2" severity="info" @click="visualizarLancamento(slotProps.data)" />
                        </template>
                    </Column>
                    <template #empty>
                        Nenhum registro encontrado
                    </template>
                </DataTable>
                <Dialog v-model:visible="confirmDelete" :style="{width: '450px'}" header="Confirmação" :modal="true">
                    <div class="confirmation-content mt-2">
                        <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem;" />
                        <span>
                            Confirma a exclusão da ajuda de custo para o servidor {{selecionado.nome}}
                            no valor de {{formatCurrency(selecionado.valor)}}?
                        </span>
                    </div>
                    <div class="flex justify-content-end flex-wrap mt-4">
                        <Button label="Não" severity="secondary" icon="pi pi-times" text @click="confirmDelete = false"/>
                        <Button label="Sim" icon="pi pi-check" text @click="deletar" />
                    </div>
                </Dialog>

                <Dialog v-model:visible="timeLine" modal header="Lançamentos" :style="{ width: '40rem' }">
                    <Timeline :value="events" class="mt-4">
                        <template #opposite="slotProps">
                            <small> {{slotProps.item.referencia}} </small>
                        </template>
                        <template #content="slotProps">
                            <InlineMessage v-if="slotProps.item.processado" severity="success">
                                Pago {{formatCurrency(slotProps.item.valor)}}
                            </InlineMessage>

                            <InlineMessage v-if="!slotProps.item.processado" severity="warn">
                                Aguardando ...
                            </InlineMessage>
                        </template>
                    </Timeline>
                </Dialog>
            </Panel>
        </section>
    </section>
</template>

<style scoped>
.p-inputgroup-addon {
    cursor: pointer !important;
}

</style>
