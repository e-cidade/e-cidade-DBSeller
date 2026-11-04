<script setup>
import { computed, onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from "primevue/confirmdialog";
import DataTableEmpenhos from './DataTableEmpenhos.vue';
import BadgeSaldo from './BadgeSaldo.vue';
import MapeamentoService from '../Services/MapeamentoService';
import EmpenhoService from '../Services/EmpenhoService';

// service
const confirm = useConfirm();
const toast = useToast();

// emit
const emit = defineEmits(['change', 'close']);

// props
const props = defineProps(['conta']);

// data
const empenhos = ref([]);
const isLoadingEmpenhos = ref(false);
const isOpenListEmpenhos = ref(false);
const isLoadingSave = ref(false);
const isLoadingDelete = ref(false);
const isLoadingDeleteAll = ref(false);
const empenhoToDelete = ref(0);
const filterEmpenho = ref('');

// methods
const getListaEmpenhos = async (paginator = null) => {
    try {
        isLoadingEmpenhos.value = true;
        const reduzido = props.conta.reduzido;
        const exercicio = props.conta.exercicio;
        const filters = filterEmpenho.value;
        empenhos.value = await EmpenhoService.getEmpenhosMapeados(
            exercicio,
            reduzido,
            paginator,
            filters
        );
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro buscar empenhos ' + error.message
        });
    } finally {
        isLoadingEmpenhos.value = false;
    }
}

const addEmpenhos = async (empenhos) => {
    isOpenListEmpenhos.value = false;

    if (!empenhos.length) {
        return false;
    }

    const reduzido = props.conta.reduzido;
    const exercicio = props.conta.exercicio;

    try {
        isLoadingSave.value = true;
        let response = await MapeamentoService.save(reduzido, exercicio, empenhos);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Empenhos adicionados com sucesso'
        });

        emit('change', true);
        getListaEmpenhos();
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao adicionar empenhos ' + error.message
        });
    } finally {
        isLoadingSave.value = false;
    }
}

const deleteEmpenho = async (empenho) => {
    try {
        isLoadingDelete.value = true;
        empenhoToDelete.value = empenho.e60_numemp;
        let response = await EmpenhoService.deleteEmpenho(empenho.c151_codigo);
        toast.add({
            severity: 'success',
            summary: 'Empenhos',
            detail: `Empenho ${empenho.e60_codemp} excluido com sucesso`
        });

        emit('change', true);
        await getListaEmpenhos();

        if (!empenhos.value.data.length && !filterEmpenho.value) {
            emit('close', true);
        }
    } catch (error) {
        let msg = 'Erro interno';
        if (error.response.data.message) {
            msg = error.response.data.message
        }

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao excluir empenho: ' + msg
        });
    } finally {
        isLoadingDelete.value = false;
        empenhoToDelete.value = 0;
    }
}

const deleteAllEmpenhos = async () => {
    try {
        isLoadingDeleteAll.value = true;
        const exercicio = props.conta.exercicio;
        const reduzido = props.conta.reduzido;

        let response = await EmpenhoService.deleteAllEmpenhos(exercicio, reduzido);

        toast.add({
            severity: 'success',
            summary: 'Empenhos',
            detail: `Todos os empenhos da conta ${reduzido}/${exercicio} excluidos com sucesso`
        });

        emit('change', true);
        emit('close', true);
    } catch (error) {
        let msg = 'Erro interno';
        if (error.response.data.message) {
            msg = error.response.data.message
        }
    } finally {
        isLoadingDeleteAll.value = false;
    }
}

const confirmDeleteEmpenho = async (empenho) => {
    confirm.require({
        message: `Tem certeza que deseja excluir o empenho ${empenho.e60_codemp}?`,
        header: 'Atenção',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Confirmar',
        accept: () => {
            deleteEmpenho(empenho);
        }
    });
}

const confirmDeleteAllEmpenhos = async () => {
    confirm.require({
        message: `Tem certeza que deseja excluir todos os empenhos?`,
        header: 'Atenção',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Confirmar',
        accept: () => {
            deleteAllEmpenhos();
        }
    });
}

// hooks
onMounted(() => {
    getListaEmpenhos();
});
</script>

<template>
    <ConfirmDialog></ConfirmDialog>
    <!-- dialog emepenho -->
    <Dialog v-model:visible="isOpenListEmpenhos" maximizable position="top" :modal="true" :draggable="true"
        header="Adicionar Empenhos">
        <DataTableEmpenhos :conta="conta" @select="addEmpenhos" />
    </Dialog>

    <DataTable class="mt-2" tableStyle="min-width: 60rem" :value="empenhos.data" :loading="isLoadingEmpenhos">
        <template #header>
            <div class="flex justify-content-between align-items-center">
                <div class="my-1">
                    <p class="m-0">{{ conta.descricao }}</p>
                    <div class="mt-3 font-normal">
                        <BadgeSaldo class="mr-2" :saldo="conta.saldo_conta" label="Saldo Conta"/>
                        <BadgeSaldo :saldo="conta.saldo_empenhos" label="Mapeado"/>
                    </div>
                </div>
            </div>
            <Divider/>
            <div class="flex justify-content-between align-items-center">
                <InputText type="text"
                    placeholder="Pesquisar"
                    v-model="filterEmpenho"
                    @keyup.enter="getListaEmpenhos(null)"
                />

                <div class="flex gap-2">
                    <Button icon="pi pi-plus" raised
                        @click="isOpenListEmpenhos = true"
                        :loading="isLoadingSave"
                        :label="isLoadingSave ? 'Adicionando' : 'Adicionar'"
                    />

                    <Button icon="pi pi-trash"
                        outlined
                        severity="danger"
                        @click="confirmDeleteAllEmpenhos"
                        :loading="isLoadingDeleteAll"
                        :label="isLoadingDeleteAll ? 'Excluindo' : 'Excluir Empenhos'"
                    />
                </div>
            </div>
        </template>
        <Column field="e60_numemp" header="Sequencial">
            <template #filter>
                <input type="text" name="" id="">
            </template>
        </Column>
        <Column field="e60_codemp" header="Numero"></Column>
        <Column field="z01_nome" header="Credor"></Column>
        <Column field="c151_valor" header="Valor">
            <template #body="{ data }">
                R$ {{ Number(data.c151_valor).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
            </template>
        </Column>
        <Column field="c151_natureza" header="Natureza"></Column>
        <Column>
            <template #body="{ data }">
                <Button icon="pi pi-times" severity="danger" rounded outlined aria-label="Cancel"
                    @click="confirmDeleteEmpenho(data)"
                    :loading="isLoadingDelete && data.e60_numemp == empenhoToDelete"
                    :label="isLoadingDelete && data.e60_numemp == empenhoToDelete ? 'Excluindo' : ''"
                />
            </template>
        </Column>
    </DataTable>

    <Paginator v-if="empenhos.total > empenhos.per_page" :rows="empenhos.per_page" :totalRecords="empenhos.total"
        @page="getListaEmpenhos" />
</template>
