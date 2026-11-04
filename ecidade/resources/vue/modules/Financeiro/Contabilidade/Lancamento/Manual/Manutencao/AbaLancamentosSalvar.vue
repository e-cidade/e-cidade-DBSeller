<script setup>
import {computed, ref} from "vue";
import {useConfirm} from "primevue/useconfirm";
import {useToast} from "primevue/usetoast";
import {formatCurrency, formateDateToBD} from "../../../../../../utils/Strings";
import ModalLoading from "../../../../../Components/ModalLoading.vue";
import ResumoLancamento from "./ResumoLancamento.vue";

const confirm = useConfirm();
const toast = useToast();
const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue'])

const dados = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const rotas = {
    criar: 'v4/api/financeiro/contabilidade/lancamento-manual',
    imprimir: 'v4/api/financeiro/contabilidade/lancamento-manual/nota-lancamento',
}
const mensagemLoad = ref(null);
const loading = ref(false);
const gerouLancamentos = ref(false);

const resumoLancamento = ref();
const visibleResumo = ref(false);
/**
 * Remove da grid dos lançamentos a serem criados
 * @param lancamento
 */
const deleteLancamento = (lancamento) => {
    dados.value.lancamentos = dados.value.lancamentos.filter(val => val.uniqid !== lancamento.uniqid);
}

const confirmaAcao = (msgConfirm) => {
    return new Promise((accept, reject) => {
        confirm.require({
            header: msgConfirm.header,
            message: msgConfirm.message,
            icon: msgConfirm?.icon ? msgConfirm?.icon : 'pi pi-exclamation-triangle',
            accept,
            reject
        });
    }).then(() => true).catch(() => false);
}

const salvarLancamentos = async () => {
    if (gerouLancamentos.value) {
        toast.add({severity: 'success', detail: 'Os lançamentos já foram gerados.', summary: 'Erro'});
        return;
    }
    if (dados.value.lancamentos.length === 0) {
        toast.add({
            severity: 'error',
            summary: 'Você deve preparar os dados do Lançamento.',
            detail: 'Volte na aba "Preparar Lançamentos", informe os dados e click em "Adicionar"'
        });
        return;
    }

    const msgConfirm = {
        header: 'Criar Lançamentos',
        message: 'Você tem certeza que deseja criar os lançamentos?',
    }
    if (!await confirmaAcao(msgConfirm)) {
        return
    }

    mensagemLoad.value = 'Aguarde, gerando lançamentos.';
    loading.value = true;

    const parameters = {
        numeroLote: dados.value.lote,
        data: formateDateToBD(dados.value.dataLancamento),
        instituicao: dados.value.instituicao,
        exercicio: dados.value.exercicio,
        lancamentos: dados.value.lancamentos
    }

    window.axios.post(rotas.criar, parameters).then(async response => {
        loading.value = false;
        gerouLancamentos.value = true;
        const msgConfirmImpressao = {
            header: 'Imprimir Lançamentos Criados',
            message: 'Lançamentos gerados com sucesso. \nDeseja emitir o a nota de lançamento manual?',
            icon: 'pi pi-question'
        }

        if (!await confirmaAcao(msgConfirmImpressao)) {
            voltar();
            return;
        }
        imprimirNotaLancamento(response.data.data);
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
};


/**
 * Criar uma pela de resumo do lancamento
 * @param lancamento
 */
const abrirResumoLancamento = (lancamento) => {

    resumoLancamento.value = lancamento;
    visibleResumo.value = true;
}

const imprimirNotaLancamento = dados => {

    mensagemLoad.value = 'Aguarde, imprimindo nota de lançamento.';
    loading.value = true;
    window.axios.get(rotas.imprimir, {params: dados}).then(async response => {
        loading.value = false;
        window.open(response.data.data.pdfLinkExterno)
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
    voltar();
};

const voltar = () => {
    dados.value.lancamento = null;
    dados.value.numerLote = null;
    dados.value.dataLancamento = null;
    dados.value.lancamentos = [];
    dados.value.aba.ativa = 0;
    dados.value.apresentaTelaFiltros = true;
};

</script>

<template>

    <section>
        <DataTable :value="dados.lancamentos" dataKey="uniqid"
                   scrollable scrollHeight="400px" tableStyle="min-width: 50rem"
                   class="w-full" :metaKeySelection="false">
            <template #header>
                <div class="flex justify-content-end column-gap-2">
                    <Button label="Salvar" icon="pi pi-save" severity="success" @click="salvarLancamentos"/>
                    <Button label="Fechar" icon="pi pi-times" severity="danger" @click="voltar"/>
                </div>
            </template>
            <template #empty>
                Para carregar os empenhos, clique em Buscar.
            </template>
            <Column field="debito" header="Debito">
                <template #body="slotProps">
                    {{
                        `${slotProps.data.debito.reduzido} - ${slotProps.data.debito.estrutural} - ${slotProps.data.debito.descricao}`
                    }}
                </template>
            </Column>
            <Column field="credito" header="Cŕedito">
                <template #body="slotProps">
                    {{
                        `${slotProps.data.credito.reduzido} - ${slotProps.data.credito.estrutural} - ${slotProps.data.credito.descricao}`
                    }}
                </template>
            </Column>
            <Column field="valor" header="Valor">
                <template #body="slotProps">
                    {{ formatCurrency(slotProps.data.valor) }}
                </template>
            </Column>

            <Column :exportable="false" style="min-width:8rem">
                <template #body="slotProps">
                    <Button icon="pi pi-info" text rounded severity="info"
                            @click="abrirResumoLancamento(slotProps.data)"/>

                    <Button icon="pi pi-trash" text rounded severity="danger"
                            @click="deleteLancamento(slotProps.data)"/>
                </template>
            </Column>
        </DataTable>
    </section>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    <ResumoLancamento v-model="resumoLancamento" v-model:visible="visibleResumo" v-if="visibleResumo" />
</template>

<style scoped>

</style>
