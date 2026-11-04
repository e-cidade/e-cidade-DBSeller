<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import {formatCurrency} from "../../../../../../utils/Strings";

const props = defineProps(['dataInicial', 'dataFinal', 'planilhas']);
const toast = useToast();
const emits = defineEmits(['proximo'])
const dataInicial = ref(props.dataInicial);
const dataFinal = ref(props.dataFinal);

const dados = ref(props.planilhas);

const rotas = {
    get: 'v4/api/financeiro/tesouraria/slip/cobertura-recurso-extra/preparados/planilhas'
}

const buscarDados = async () => {
    try {
        let parans = [
            `dataInicial=${dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'})}`
        ];

        if (dataFinal.value !== undefined) {
            parans.push(`dataFinal=${dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'})}`);
        }

        const response = await window.axios.get(`${rotas.get}?${parans.join('&')}`);
        if (response.status === 206) {
            toast.add({severity: 'warn', detail: response.data.message, summary: 'Aviso'});
            dados.value = [];
            return;
        }

        const contas = [];
        response.data.data.forEach(conta => {
            conta.uniqid = uniqid();
            contas.push(conta);
        });
        dados.value = contas;
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }
}

const proximo = () => {
    emits('proximo');
}

defineExpose({buscarDados});
</script>

<template>
    <Message severity="info" :closable="false" :style="{marginTop:0}">
        <div style='font-size: 10pt; '>
            Valores de recebimentos extra orçamentários apropriados via planilhas de lançamentos.
        </div>
    </Message>

    <Button @click="proximo" label="Próxima Aba" class="mb-1"/>

    <DataTable :value="dados" tableStyle="min-width: 50rem">
        <template #empty>
            Sem registros de apropriação por planilha para o período informado.
        </template>
        <Column field="credor" header="Credor">
            <template #body="slotProps">
                {{ `${slotProps.data.cgm} - ${slotProps.data.nome_credor}` }}
            </template>
        </Column>
        <Column field="debito" header="Débito">
            <template #body="slotProps">
                {{ `${slotProps.data.debitar} - ${slotProps.data.debitar_descricao}` }}
            </template>
        </Column>
        <Column field="credito" header="Crédito">
            <template #body="slotProps">
                {{ `${slotProps.data.creditar} - ${slotProps.data.creditar_descricao}` }}
            </template>
        </Column>
        <Column field="siconfi" header="Siconfi"></Column>
        <Column field="subrecurso" header="Subrecurso"></Column>
        <Column field="complemento" header="Complemento"></Column>
        <Column field="planilha" header="Planilha"></Column>
        <Column field="valor" header="Valor">
            <template #body="slotProps">
                {{ formatCurrency(slotProps.data.valor) }}
            </template>
        </Column>
    </DataTable>

</template>

<style scoped>

</style>
