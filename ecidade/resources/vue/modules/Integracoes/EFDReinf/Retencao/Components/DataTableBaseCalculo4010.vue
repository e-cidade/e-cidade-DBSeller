<script setup>
import { ref } from 'vue';

const props = defineProps({
    retencao: Number
});

const isLoading = ref(false);
const pagamentos = ref([]);

const getPagamentos = async () => {
    try {
        isLoading.value = true;
        const url = 'v4/api/integracoes/efd-reinf/retencao/composicao-base-calculo';
        const params = {
            'retencao': props.retencao,
            'evento': 'R-4010'
        }
        const request = await axios.get(url, {params});
        const { data: response } = request;
        const { data: responseData } = response;
        pagamentos.value = responseData;
    } catch(error) {
        alert('Erro ao buscar pagamentos:' + error.message);
        pagamentos.value = [];
    }
    isLoading.value = false;
}

getPagamentos();
</script>

<template>
    <div class="my-2 p-4 bg-blue-100">
        Pagamentos realizados ao credor no mês, que compõem a base de cálculo
    </div>
    <DataTable :value="pagamentos" tableStyle="min-width: 50rem" class="mt-3" :loading="isLoading">
        <template #empty>
            Nenhum pagamento foi adicionado no momento do cálculo da retenção.
        </template>
        <Column field="empenho" header="Empenho"></Column>
        <Column field="ordem" header="Ordem Pagamento"></Column>
        <Column field="valor" header="Valor"></Column>
    </DataTable>
</template>
