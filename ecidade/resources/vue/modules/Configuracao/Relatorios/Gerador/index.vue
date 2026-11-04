<script setup>

import { nextTick, onMounted, ref, watch } from "vue";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import { FilterMatchMode } from "primevue/api";
import ConfirmDialog from "primevue/confirmdialog";

import ModalLoading from "../../../Components/ModalLoading";
import DialogTelaDinamica from "./Components/DialogTelaDinamica";
import DialogManutencaoRelatorio from "./Components/DialogManutencaoRelatorio";
import DialogMenuRelatorio from "./Components/DialogMenuRelatorio.vue";
import DialogImportarRelatorio from "./Components/DialogImportarRelatorio.vue";

const props = defineProps({
    usuario: { type: String, required: true },
    departamento: { type: String, required: true }
});

const toast = useToast();
const confirm = useConfirm();

const isLoading = ref(false);

const tiposVisualizacao = ref([
    { value: '1', label: 'Usuário' },
    { value: '2', label: 'Departamento' },
    { value: '3', label: 'Público' },
    { value: '4', label: 'Cubos BI' }
]);
const tipoVisualizacao = ref('3');

const codigoRelatorio = ref(null);
const relatorio = ref(null);
const relatorios = ref([]);
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const isImprimirVisible = ref(false);
const isCriarMenu = ref(false);
const isImportar = ref(false);

/**
 * @type {Ref<DialogManutencaoRelatorio>}
 */
const dialogManutencaoRelatorio = ref();

async function getRelatorios() {
    isLoading.value = true;
    try {
        const response = await axios.get(`v4/api/configuracao/gerador/relatorios?tipoVisualizacao=${tipoVisualizacao.value}`);

        relatorios.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar relatórios',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

async function novoRelatorio() {
    codigoRelatorio.value = null;
    await nextTick();
    dialogManutencaoRelatorio.value.open();
}

function imprimirRelatorio(codigo) {
    codigoRelatorio.value = codigo;
    isImprimirVisible.value = true;
}

async function exportarRelatorio(codigo) {
    isLoading.value = true;
    try {
        const response = await axios.get(`v4/api/configuracao/gerador/relatorios/${codigo}/exportar`);

        saveAs(response.data.data.path, response.data.data.name);
    } catch (e) {
        toast.add({
           severity: 'error',
           summary: 'Erro ao exportar relatório',
           detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

function criarMenuRelatorio(codigo) {
    relatorio.value = codigo;
    isCriarMenu.value = true;
}

async function editarRelatorio(codigo) {
    codigoRelatorio.value = codigo;
    /** @todo colocar loading? */
    await nextTick();
    dialogManutencaoRelatorio.value.open();
}

async function excluirRelatorio(codigo) {
    if (!await confirmacaoUsuario()) {
        return;
    }

    isLoading.value = true;
    try {
        await axios.delete(`v4/api/configuracao/gerador/relatorios/${codigo}`);
        relatorios.value = relatorios.value.filter(relatorio => relatorio.codigo !== codigo);

        toast.add({
            severity: 'success',
            summary: 'Relatório apagado com sucesso.',
            life: 3000
        });
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao apagar relatório',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false
}

async function confirmacaoUsuario() {
    return new Promise(resolve => {
        confirm.require({
            header: 'Deseja prosseguir?',
            message: 'Todos os dados serão perdidos!',
            icon: 'pi pi-exclamation-triangle',
            acceptClass: 'p-button-danger',
            accept: () => {
                resolve(true);
            },
            reject: () => {
                resolve(false);
            }
        });
    });
}

watch(tipoVisualizacao, (newValue, oldValue) => {
    if (oldValue === null) {
        return;
    }
    if (newValue === null) {
        tipoVisualizacao.value = oldValue;
        return;
    }
    getRelatorios();
});

onMounted(() => {
    getRelatorios();
});

</script>

<template>
    <section class="flex flex-column w-full mt-4 gap-2">
        <section class="flex justify-content-center">
            <Panel header="Dados do Usuário" class="w-full lg:w-6 xl:w-5">
                <div class="formgrid grid mt-4">
                    <div class="field col-6">
                        <span class="p-float-label">
                            <InputText id="usuario" type="text" :modelValue="usuario" class="w-full" disabled />
                            <label for="usuario">Usuário</label>
                        </span>
                    </div>
                    <div class="field col-6">
                        <span class="p-float-label">
                            <InputText id="departamento" type="text" :modelValue="departamento" class="w-full" disabled />
                            <label for="departamento">Departamento</label>
                        </span>
                    </div>
                </div>
            </Panel>
        </section>
        <section class="flex justify-content-center">
            <DataTable :filters="filters" :value="relatorios" class="w-full lg:w-8 xl:w-6" showGridlines scrollable scrollHeight="400px">
                <template #header>
                    <div class="flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filters.global.value" placeholder="Pesquisar" />
                             </span>
                        </div>
                        <div class="formgrid grid">
                            <SelectButton :options="tiposVisualizacao"
                                          v-model="tipoVisualizacao"
                                          optionValue="value"
                                          optionLabel="label"></SelectButton>
                        </div>
                    </div>
                </template>
                <Column field="codigo" style="width: 50px" header="Código"></Column>
                <Column field="nome" header="Nome"></Column>
                <Column style="width: 230px" header="Opções">
                    <template #body="slotProps">
                        <Button class="mx-1" icon="pi pi-print" title="Imprimir" @click="imprimirRelatorio(slotProps.data.codigo)" rounded raised></Button>
                        <Button class="mx-1" icon="pi pi-file-export" title="Exportar" @click="exportarRelatorio(slotProps.data.codigo)" rounded raised></Button>
                        <Button class="mx-1" icon="pi pi-send" title="Lançar Menu" @click="criarMenuRelatorio(slotProps.data)" rounded raised></Button>
                        <Button class="mx-1" icon="pi pi-file-edit" title="Editar" @click="editarRelatorio(slotProps.data.codigo)" rounded raised></Button>
                        <Button class="mx-1" icon="pi pi-trash" title="Excluir" severity="danger" @click="excluirRelatorio(slotProps.data.codigo)" rounded raised></Button>
                    </template>
                </Column>
            </DataTable>
        </section>
        <section class="flex justify-content-center gap-1">
            <Button icon="pi pi-file" label="Novo" @click="novoRelatorio"></Button>
            <Button icon="pi pi-file-import" label="Importar" @click="isImportar = true;"></Button>
        </section>
    </section>
    <DialogManutencaoRelatorio ref="dialogManutencaoRelatorio"
                               :relatorio="codigoRelatorio"
                               @callbackSalvar="getRelatorios"></DialogManutencaoRelatorio>
    <DialogTelaDinamica v-if="codigoRelatorio"
                        :codigoRelatorio="codigoRelatorio"
                        :modal="true"
                        :visible="isImprimirVisible"
                        @update:visible="isImprimirVisible = false; codigoRelatorio = null"></DialogTelaDinamica>
    <DialogMenuRelatorio v-if="relatorio"
                         :relatorio="relatorio"
                         :visible="isCriarMenu"
                         @update:visible="isCriarMenu = false; relatorio = null"></DialogMenuRelatorio>
    <DialogImportarRelatorio v-model:visible="isImportar" @sucesso="isImportar = false; getRelatorios()"></DialogImportarRelatorio>
    <ModalLoading :is-loading="isLoading"/>

    <ConfirmDialog></ConfirmDialog>
</template>

<style scoped>

</style>
