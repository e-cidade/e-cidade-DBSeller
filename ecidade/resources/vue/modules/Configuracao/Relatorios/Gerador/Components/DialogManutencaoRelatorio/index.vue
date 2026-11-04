<script setup>

import { nextTick, onMounted, ref, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from "primevue/confirmdialog";

import AbaCampos from "./AbaCampos";
import AbaOrdem from "./AbaOrdem";
import AbaFiltros from "./AbaFiltros";
import AbaLayout from "./AbaLayout";
import AbaFinalizar from "./AbaFinalizar";
import AbaVariaveis from "./AbaVariaveis";
import AbaSQL from "./AbaSQL";
import ModalLoading from "../../../../../Components/ModalLoading";

const props = defineProps({
    relatorio: { type: [ Number, null ]  }
});

const emit = defineEmits(['callbackSalvar']);

const novoRelatorio = ref(false);

const toast = useToast();
const confirm = useConfirm();

let avisaDadosNaoSalvos = false;
const visible = ref(false);
const isLoading = ref(false);

const visao = ref(null);
const visoes = ref([]);

const relatorio = ref({
    codigo: null,
    grupo: null,
    tipo: null,
    origem: '1',
    template: null,
    campos: [],
    ordem: [],
    filtros: [],
    layout: {},
    variaveis: [],
    sql: '',
    visao: ''
});

const indexAbaAtiva = ref(0);
const abasDesabilitadas = ref(true);

async function buildTemplate(path, name) {
    const response = await axios.get(path, { responseType: 'blob' });
    return new File([response.data], name, { type: response.headers['content-type'] });
}

async function iniciar(_relatorio) {
    relatorio.value.tipo = _relatorio.tipo;
    relatorio.value.grupo = _relatorio.grupo;
    relatorio.value.origem = _relatorio.origem.toString();
    relatorio.value.campos = [_relatorio.campos.naoConfigurados, _relatorio.campos.configurados];
    relatorio.value.ordem = [_relatorio.ordem.naoConfigurados, _relatorio.ordem.configurados];
    relatorio.value.filtros = _relatorio.filtros;
    relatorio.value.layout = _relatorio.layout;
    relatorio.value.variaveis = _relatorio.variaveis;
    relatorio.value.sql = _relatorio.sql;

    if (_relatorio.tipo?.codigo === 2 && _relatorio.template) {
        relatorio.value.template = await buildTemplate(_relatorio.template.path, _relatorio.template.name);
    }

    abasDesabilitadas.value = false;
    visible.value = true;
}

async function getInfoRelatorio() {
    isLoading.value = true;
    try {
        const response = await axios.get(`v4/api/configuracao/gerador/relatorios/${props.relatorio}`);
        await iniciar(response.data.data);
        await nextTick();
        avisaDadosNaoSalvos = false;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar dados do relatório',
            detail: e.response.data.message
        });
    }
    isLoading.value = false;
}

function confirmar() {
    if (relatorio.value.origem === '1') {
        novoRelatorio.value = true;
        return;
    }

    novo(null);
}

async function novo(sql) {
    indexAbaAtiva.value = 0;
    isLoading.value = true;
    try {
        const response = await axios.post('v4/api/configuracao/gerador/relatorios/novo', { sql: sql, visao: visao.value });
        iniciar(response.data.data).then(); // ignora a promise pois não havera template
        novoRelatorio.value = true;
        avisaDadosNaoSalvos = true;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao iniciar novo relatório',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
    // se estiver sendo gerado à partir de um SQL, muda a aba ativa para "Campos"
    if (sql) {
        indexAbaAtiva.value = 1;
    }
}

function callbackSalvar() {
    visible.value = false;

    emit('callbackSalvar');
}

function open() {
    indexAbaAtiva.value = 0;
    novoRelatorio.value = false;
    relatorio.value.codigo = null;
    relatorio.value.tipo = null;
    relatorio.value.grupo = null;
    relatorio.value.origem = '1';
    relatorio.value.campos = [];
    relatorio.value.ordem = [];
    relatorio.value.layout = {};
    relatorio.value.variaveis = [];
    relatorio.value.sql = '';
    abasDesabilitadas.value = true;

    if (props.relatorio === null) {
        visible.value = true;
        return;
    }

    relatorio.value.codigo = props.relatorio;
    getInfoRelatorio();
}

async function close() {
    if (!avisaDadosNaoSalvos) {
        visible.value = false;
        return;
    }

    await new Promise(resolve => {
        confirm.require({
            group: 'dialogManutencaoRelatorio',
            header: 'Deseja prosseguir?',
            message: 'Todos os dados não salvos serão perdidos!',
            icon: 'pi pi-exclamation-triangle',
            acceptClass: 'p-button-danger',
            accept: () => {
                visible.value = false;
                resolve(true);
            },
            reject: () => {
                resolve(false);
            }
        });
    });
}

async function getVisoes() {
    isLoading.value = true;
    try {
        const response = await axios.get('v4/api/configuracao/consultas/postgres/views');

        visoes.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar Visões',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

onMounted(() => {
    getVisoes();
});

watch(relatorio, () => {
    avisaDadosNaoSalvos = true;
}, { deep: true });

defineExpose({ open, close });

</script>

<template>
    <Dialog header="Manutenção de Relatório"
            :visible="visible"
            class="p-dialog-maximized"
            :closeOnEscape="false"
            :pt="{
                content: { style: 'background: #e1dede; padding: 0;' },
                closeButton: { onClick: close }
            }">
        <section v-if="relatorio.codigo || novoRelatorio">
            <TabView v-model:activeIndex="indexAbaAtiva">
                <TabPanel v-if="relatorio.origem === '1'">
                    <template #header>
                        <i class="pi pi-database mr-2"></i>
                        <span>SQL</span>
                    </template>
                    <!-- v-if da aba ativa serve pra evitar bug que ocorre no CodeEditor ao deixa-lo como hidden -->
                    <AbaSQL v-if="indexAbaAtiva === 0" :modelValue="relatorio.sql" @callbackSalvar="novo"></AbaSQL>
                </TabPanel>
                <TabPanel :disabled="abasDesabilitadas">
                    <template #header>
                        <i class="pi pi-list mr-2"></i>
                        <span>Campos</span>
                    </template>
                    <AbaCampos v-model="relatorio.campos"></AbaCampos>
                </TabPanel>
                <TabPanel :disabled="abasDesabilitadas">
                    <template #header>
                        <i class="pi pi-sort mr-2"></i>
                        <span>Ordem</span>
                    </template>
                    <AbaOrdem v-model="relatorio.ordem"></AbaOrdem>
                </TabPanel>
                <TabPanel v-if="relatorio.origem === '2'">
                    <template #header>
                        <i class="pi pi-filter mr-2"></i>
                        <span>Filtros</span>
                    </template>
                    <AbaFiltros v-model="relatorio.filtros" :campos="relatorio.campos" :variaveis="relatorio.variaveis"></AbaFiltros>
                </TabPanel>
                <TabPanel :disabled="abasDesabilitadas">
                    <template #header>
                        <i class="pi pi-dollar mr-2"></i>
                        <span>Variáveis</span>
                    </template>
                    <AbaVariaveis v-model="relatorio.variaveis"></AbaVariaveis>
                </TabPanel>
                <TabPanel :disabled="abasDesabilitadas">
                    <template #header>
                        <i class="pi pi-book mr-2"></i>
                        <span>Layout</span>
                    </template>
                    <AbaLayout v-model="relatorio.layout"></AbaLayout>
                </TabPanel>
                <TabPanel :disabled="abasDesabilitadas">
                    <template #header>
                        <i class="pi pi-check mr-2"></i>
                        <span>Finalizar</span>
                    </template>
                    <AbaFinalizar v-model:tipo="relatorio.tipo"
                                  v-model:grupo="relatorio.grupo"
                                  v-model:template="relatorio.template"
                                  :dados="relatorio"
                                  @callbackSalvar="callbackSalvar"></AbaFinalizar>
                </TabPanel>
            </TabView>
        </section>
        <section v-else class="flex flex-column gap-2 mt-2">
            <div class="flex justify-content-center">
                <Panel header="Novo Relatório" class="w-5">
                    <div class="formgrid grid row-rap-2">
                        <div class="col-6">
                            <div class="field-radiobutton">
                                <RadioButton id="tipoSql" name="tipo" value="1" v-model="relatorio.origem" />
                                <label for="tipoSql">À partir de um SQL</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="field-radiobutton">
                                <RadioButton id="tipoDepartamento" name="tipo" value="2" v-model="relatorio.origem" />
                                <label for="tipoDepartamento">À partir de uma visão</label>
                            </div>
                        </div>
                    </div>
                    <div v-if="relatorio.origem === '2'" class="flex justify-content-center field grid">
                        <label for="visao" class="col-1">Visão:</label>
                        <Dropdown inputId="visao"
                                  v-model="visao"
                                  :options="visoes"
                                  optionLabel="nome"
                                  optionValue="nome"
                                  class="col-5"></Dropdown>
                    </div>
                </Panel>
            </div>
            <div class="flex justify-content-center">
                <Button icon="pi pi-check" label="Confirmar" @click="confirmar"></Button>
            </div>
        </section>
    </Dialog>

    <ConfirmDialog group="dialogManutencaoRelatorio"></ConfirmDialog>
    <ModalLoading :is-loading="isLoading"></ModalLoading>
</template>

<style scoped>

</style>
