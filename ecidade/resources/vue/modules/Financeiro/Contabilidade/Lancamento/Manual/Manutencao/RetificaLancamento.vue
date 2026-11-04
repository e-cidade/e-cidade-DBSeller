<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import LookupReduzidosPcasp from "../../../Components/LookupReduzidosPcasp.vue";
import LookupEmpenho from "../../../../Empenho/Components/LookupEmpenho.vue";
import LookupHistorico from "../../../Components/LookupHistorico.vue";
import LookupReceita from "../../../../Orcamento/Components/LookupReceita.vue";
import LookupRecurso from "../../../../Orcamento/Components/LookupRecurso.vue";
import LookupDotacao from "../../../../Orcamento/Components/LookupDotacao.vue";
import AutoCompleteCgm from "../../../../../Patrimonial/Protocolo/Components/AutoCompleteCgm.vue";
import ModalLoading from "../../../../../Components/ModalLoading.vue";
import {formateDateToBD} from "../../../../../../utils/Strings";
import Message from "primevue/message";
import Calendar from "primevue/calendar";

const toast = useToast();
const confirm = useConfirm();
const props = defineProps(['modelValue', 'visible', 'dataSistema']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

const modelValue = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const mensagemLoad = ref(null);
const loading = ref(false);

const contaCredito = ref({});
const contaDebito = ref({});
const valor = ref(null);
const historico = ref(null);
const observacao = ref(null);

// campos que habilita os atributos escondidos
const vincularEmpenho = ref(false);
const vincularDotacao = ref(false);
const vincularReceita = ref(false);
const vincularCgm = ref(false);
const vincularRecurso = ref(false);

const empenho = ref({});
const dotacao = ref({});
const receita = ref({});
const recursoCredito = ref({});
const recursoDebito = ref({});
const cgm = ref(null);

const rota = 'v4/api/financeiro/contabilidade/lancamento-manual/retificar';

const atributoReceita = 'RECEITA'
const atributoDotacao = 'DOTACAO'
const atributoEmpenho = 'EMPENHO'
const atributoCgm = 'CGM'
const atributoRecurso = 'RECURSO'

const liberaAtributosPreenchimento = (atributos) => {

    vincularEmpenho.value = false;
    vincularDotacao.value = false;
    vincularReceita.value = false;
    vincularCgm.value = false;
    vincularRecurso.value = false;

    if (atributos.includes(atributoEmpenho)) {
        vincularEmpenho.value = true;

        return;
    }
    if (atributos.includes(atributoDotacao)) {
        vincularDotacao.value = true;
        return;
    }

    if (atributos.includes(atributoReceita)) {
        vincularReceita.value = true;
        return;
    }
    if (atributos.includes(atributoCgm)) {
        vincularCgm.value = true;
        vincularRecurso.value = true;
        return;
    }
    if (atributos.includes(atributoRecurso)) {
        vincularRecurso.value = true;
        return;
    }
}


const validar = () => {

    try {
        if (!contaCredito.value?.codcon) {
            throw 'Informe a conta a Crédito.';
        }
        if (!contaDebito.value?.codcon) {
            throw 'Informe a conta a Débito.'
        }
        if (contaCredito.value.reduzido === contaDebito.value.reduzido) {
            throw 'As contas crédito e débito não podem ser iguais.'
        }
        if (!historico.value?.codigo) {
            throw 'Informe o histórico do lançamento.'
        }
        if (!valor.value) {
            throw 'Informe o valor.'
        }
        if (vincularEmpenho.value && !empenho.value) {
            throw 'Essa as contas selecionadas exigem seleção de um "Empenho".';
        }
        if (vincularDotacao.value && !dotacao.value) {
            throw 'As contas selecionadas exigem seleção de uma "Dotação".';
        }
        if (vincularReceita.value && !receita.value) {
            throw 'As contas selecionadas exigem seleção de uma "Receita".';
        }
        if (vincularCgm.value && !cgm.value?.numcgm) {
            throw 'As contas selecionadas exigem seleção de um "CGM".';
        }
        if (vincularRecurso.value && (!recursoCredito.value?.orctiporec_id || !recursoDebito.value?.orctiporec_id)) {
            throw 'As contas selecionadas exigem seleção de "Recurso".';
        }
        if (!observacao.value) {
            throw 'Você deve informar o "Texto Complementar".'
        }
    } catch (msg) {
        toast.add({severity: 'error', detail: msg, summary: 'Erro'});
        return false;
    }

    return true;
}

async function confirmaAcao(msgConfirm) {
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

function lancamentoRetificar() {
    const lancamento = {
        lancamento: modelValue.value.lancamento,
        credito: modelValue.value.credito,
        debito: modelValue.value.debito,
        historico: modelValue.value.historico,
        valor: modelValue.value.valor,
        observacao: modelValue.value.observacao,
    };
    if (vincularEmpenho.value) {
        lancamento.empenho = modelValue.value.empenho;
    }
    if (vincularDotacao.value) {
        lancamento.dotacao = modelValue.value.dotacao;
    }
    if (vincularReceita.value) {
        lancamento.receita = modelValue.value.receita;
    }
    if (vincularCgm.value) {
        lancamento.cgm = modelValue.value.cgm.value;
    }
    if (vincularRecurso.value) {
        lancamento.recursoCredito = modelValue.value.recursoCredito;
        lancamento.recursoDebito = modelValue.value.recursoDebito;
    }

    return lancamento;
}

async function salvar() {
    if (!validar()) {
        return
    }

    const msgConfirm = {
        header: 'Alterar Lançamento',
        message: 'Você tem certeza que alterar o lançamento?',
    }
    if (!await confirmaAcao(msgConfirm)) {
        return;
    }

    const novoLancamento = {
        credito: {...contaCredito.value},
        debito: {...contaDebito.value},
        historico: {...historico.value},
        valor: valor.value,
        observacao: observacao.value,
    }
    if (vincularEmpenho.value) {
        novoLancamento.empenho = {...empenho.value};
    }
    if (vincularDotacao.value) {
        novoLancamento.dotacao = {...dotacao.value};
    }
    if (vincularReceita.value) {
        novoLancamento.receita = {...receita.value};
    }
    if (vincularCgm.value) {
        novoLancamento.cgm = {...cgm.value};
    }
    if (vincularRecurso.value) {
        novoLancamento.recursoCredito = {...recursoCredito.value};
        novoLancamento.recursoDebito = {...recursoDebito.value};
    }
    const parameters = {
        data: formateDateToBD(props.dataSistema),
        retificar: lancamentoRetificar(),
        lancamento: novoLancamento,
        instituicao: modelValue.value.instituicao,
        exercicio: modelValue.value.exercicio,
    }

    mensagemLoad.value = 'Aguarde, alterando lançamentos.';
    loading.value = true;
    window.axios.post(rota, parameters).then(async response => {
        loading.value = false;
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
        emit('update:visible', false);
    });
}

watch([contaCredito, contaDebito], () => {
    const atributos = [].concat(contaCredito.value.atributos, contaDebito.value.atributos);
    liberaAtributosPreenchimento(atributos)
});

onMounted(() => {
    valor.value = Number(modelValue.value.valor);
    observacao.value = modelValue.value.observacao;

});

</script>

<template>

    <Dialog :visible="visible" modal header="Alterar Lançamento"
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized " :pt="{content:{style:'background-color:#e0dddd'}}">

        <section class="flex flex-column w-full ">
            <section class="flex justify-content-center">
                <div class="formgrid grid w-full md:w-11 lg:w-8 xl:w-6 row-gap-2">
                    <div class="field col-12">
                        <Message severity="info" :closable="false">
                            <h3>Atenção! O lançamento será executado na data da sessão do sistema.</h3>
                            Confira a data do sistema antes de retificar o lançamento.<br>
                        </Message>
                    </div>
                    <div class="field col-12">
                        <div class="p-float-label">
                            <Calendar v-model="props.dataSistema" dateFormat="dd/mm/yy" disabled
                                       placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 180px;"/>
                            <label for="lb-periodo">Data do Sistema</label>
                        </div>
                    </div>
                    <div class="field col-12 ">
                        <LookupReduzidosPcasp v-model="contaDebito" :pesquisaReduzido="modelValue.debito.reduzido"
                                              :exercicio="modelValue.exercicio" :instituicao="modelValue.instituicao"
                                              label="Débito"/>
                    </div>
                    <div class="field col-12 ">
                        <LookupReduzidosPcasp v-model="contaCredito" :pesquisaReduzido="modelValue.credito.reduzido"
                                              :exercicio="modelValue.exercicio" :instituicao="modelValue.instituicao"
                                              label="Crédito"/>
                    </div>
                    <div class="field col-12 ">
                        <LookupHistorico v-model="historico"
                                         :pesquisaCodigo="modelValue.historico.codigo"></LookupHistorico>
                    </div>

                    <!-- vincular com empenho-->
                    <div class="field col-12 " v-if="vincularEmpenho">
                        <LookupEmpenho v-model="empenho" :pesquisaCodigo="modelValue.empenho.numemp"
                                       :exercicio="modelValue.exercicio"
                                       :instituicao="modelValue.instituicao" :dataSistema="dataSistema"/>
                    </div>

                    <!-- vincular com Dotação-->
                    <div class="field col-12 " v-if="vincularDotacao">
                        <LookupDotacao v-model="dotacao" :pesquisaReduzido="modelValue.dotacao.reduzido"
                                       :exercicio="modelValue.exercicio" :instituicao="modelValue.instituicao"/>
                    </div>

                    <!-- vincular com Receita-->
                    <div class="field col-12 " v-if="vincularReceita">
                        <LookupReceita v-model="receita" :pesquisaReduzido="modelValue.receita.reduzido"
                                       :exercicio="modelValue.exercicio" :instituicao="modelValue.instituicao"/>
                    </div>

                    <!-- vincular com CGM-->
                    <div class="field col-12 " v-if="vincularCgm">
                        <AutoCompleteCgm v-model="cgm" :pesquisaCgm="modelValue.cgm.numcgm"/>
                    </div>

                    <!-- Lookup pesquisa de recurso a Crédito -->
                    <div class="field col-12 " v-if="vincularRecurso">
                        <LookupRecurso v-model="recursoCredito" :pesquisaCodigo="modelValue.credito.recurso"
                                       :exercicio="modelValue.exercicio" :data="modelValue.data" label="Crédito:"/>
                    </div>

                    <!-- Lookup pesquisa de recurso a Débito -->
                    <div class="field col-12 " v-if="vincularRecurso">
                        <LookupRecurso v-model="recursoDebito" :pesquisaCodigo="modelValue.debito.recurso"
                                       :exercicio="modelValue.exercicio" :data="modelValue.data" label="Débito:"/>
                    </div>

                    <!-- Valor do lançamento -->
                    <div class="field col-12 ">
                        <div class="p-float-label">
                            <InputNumber id="input-valor" v-model="valor" :maxFractionDigits="2"
                                         locale="pt-BR"/>
                            <label for="input-valor">Valor</label>
                        </div>
                    </div>
                    <div class="field col-12">
                        <div class="p-float-label">
                            <Textarea v-model="observacao" rows="2" cols="30" class="w-full"/>
                            <label>Texto Complementar</label>
                        </div>
                    </div>
                </div>
            </section>
            <section class="flex justify-content-center column-gap-2">
                <Button type="button" label="Retificar" icon="pi pi-save" severity="success"
                        @click="salvar"/>
            </section>
        </section>
        <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    </Dialog>
</template>

<style scoped>

</style>
