<script setup>
import { ref, watch } from 'vue';
import DataTableEmpenhosMapeados from './DataTableEmpenhosMapeados.vue';
import FormCreateMapeamento from './FormCreateMapeamento.vue';
import MapeamentoService from '../Services/MapeamentoService';
import { useToast } from 'primevue/usetoast';

// service
const toast = useToast();

// props
const props = defineProps(['filters']);

// data
const contas = ref([]);
const dataConta = ref(null);
const isOpenListEmpenhos = ref(false);
const isOpenFormMapeamento = ref(false);
const isLoadingContas = ref(false);

// methods
const openListEmpenhos = (data) => {
    isOpenListEmpenhos.value = true;
    setSelectedConta(data);
}

const setSelectedConta = (data) => {
    dataConta.value = {
        exercicio: data.c151_exercicio,
        reduzido: data.c151_reduzido,
        estrutural: data.c60_estrut,
        sinal_saldo: data.sinal_saldo,
        saldo_conta: data.saldo_conta,
        saldo_empenhos: data.saldo_empenhos,
        descricao: data.c60_descr
    }
}

const getMapeamentos = async () => {
    try {
        isLoadingContas.value = true;
        const exercicio = props.filters.exercicio;
        const instituicao = props.filters.instituicao;

        contas.value = await MapeamentoService.getMapeamentos(exercicio, instituicao)
        contas.value.forEach((conta) => {
            conta.sinal_saldo_empenhos = conta.saldo_empenhos < 0 ? 'C' : 'D';
            conta.saldo_empenhos = conta.saldo_empenhos < 0 ? conta.saldo_empenhos *-1 : conta.saldo_empenhos;
        });

        if (dataConta.value && contas.value) {
            let conta = contas.value.find((e) => e.c151_reduzido == dataConta.value.reduzido);
            if (conta) {
                setSelectedConta(conta);
            }
        }
    } catch (error) {
        let msg = 'Erro interno';
        if (error.response.data.message) {
            msg = error.response.data.message
        }

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro buscar contas: ' + msg
        });
    } finally {
        isLoadingContas.value = false;
    }
}

// watch
watch(() => props.filters, (filters) => getMapeamentos(), {immediate: true});

</script>

<template>
    <!-- dialog emepenho -->
    <Dialog v-model:visible="isOpenListEmpenhos" maximizable position="top" :modal="true"
        :draggable="false" header="Empenhos Mapeados">
        <DataTableEmpenhosMapeados
            :conta="dataConta"
            @change="getMapeamentos"
            @close="isOpenListEmpenhos = false"
        />
    </Dialog>

    <!-- dialog mapeamento -->
    <Dialog v-model:visible="isOpenFormMapeamento" position="top" :modal="true"
        :draggable="false" header="Novo Mapeamento">
        <FormCreateMapeamento @save="getMapeamentos" />
    </Dialog>

    <!-- mapeamentos -->
    <DataTable :value="contas" :loading="isLoadingContas">
        <template #header>
            <Button icon="pi pi-plus" label="Novo Mapeamento" raised @click="isOpenFormMapeamento = true" />
        </template>
        <Column field="c60_estrut" header="Estrutural"></Column>
        <Column field="c60_descr" header="Descricao"></Column>
        <Column field="c151_reduzido" header="Reduzido"></Column>
        <Column field="c151_exercicio" header="Exercicio"></Column>
        <Column field="saldo_conta" header="Saldo Conta">
            <template #body="{ data }">
                {{ data.sinal_saldo }} R$ {{ Number(data.saldo_conta).toLocaleString('pt-BR', {minimumFractionDigits: 2}) }}
            </template>
        </Column>
        <Column field="saldo_empenhos" header="Saldo Mapeado">
           <template #body="{ data }">
                {{ data.sinal_saldo_empenhos }} R$ {{ Number(data.saldo_empenhos).toLocaleString('pt-BR', {minimumFractionDigits: 2}) }}
           </template>
        </Column>
        <Column>
            <template #body="{data}">
                <Button icon="pi pi-list" size="small" label="Empenhos" outlined
                    @click="openListEmpenhos(data)" />
            </template>
        </Column>
    </DataTable>
</template>
