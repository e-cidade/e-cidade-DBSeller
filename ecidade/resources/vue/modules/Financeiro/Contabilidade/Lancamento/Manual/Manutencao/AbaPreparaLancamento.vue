<script setup>
import {computed, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import ModalLoading from "../../../../../Components/ModalLoading.vue";
import LookupRecurso from "../../../../Orcamento/Components/LookupRecurso.vue";
import LookupReceita from "../../../../Orcamento/Components/LookupReceita.vue";
import LookupDotacao from "../../../../Orcamento/Components/LookupDotacao.vue";
import LookupEmpenho from "../../../../Empenho/Components/LookupEmpenho.vue";
import AutoCompleteCgm from "../../../../../Patrimonial/Protocolo/Components/AutoCompleteCgm.vue";
import LookupReduzidosPcasp from "../../../Components/LookupReduzidosPcasp.vue";
import LookupHistorico from "../../../Components/LookupHistorico.vue";

const toast = useToast();
const props = defineProps(['modelValue', 'exercicio', 'instituicao']);
const emit = defineEmits(['update:modelValue'])

const dados = computed({
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

function limparCampos() {

     contaCredito.value = {};
     contaDebito.value = {};
     valor.value = null;
     historico.value = null;
     observacao.value = null;

    empenho.value = {};
    dotacao.value = {};
    receita.value = {};
    recursoCredito.value = {};
    recursoDebito.value = {};
    cgm.value = null;

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
        if (vincularEmpenho.value && !empenho.value?.numemp) {
            throw 'Essas contas selecionadas exigem seleção de um "Empenho".';
        }
        if (vincularDotacao.value && !dotacao.value?.reduzido) {
            throw 'As contas selecionadas exigem seleção de uma "Dotação".';
        }
        if (vincularReceita.value && !receita.value?.reduzido) {
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

const adicionarLancamento = () => {
    if (!validar()) {
        return
    }

    const parameters = {
        uniqid: uniqid(),
        credito: {...contaCredito.value},
        debito: {...contaDebito.value},
        historico: {...historico.value},
        valor: valor.value,
        observacao: observacao.value,
    }
    if (vincularEmpenho.value) {
        parameters.empenho = {...empenho.value};
    }
    if (vincularDotacao.value) {
        parameters.dotacao = {...dotacao.value};
    }
    if (vincularReceita.value) {
        parameters.receita = {...receita.value};
    }
    if (vincularCgm.value) {
        parameters.cgm = {...cgm.value};
    }
    if (vincularRecurso.value) {
        parameters.recursoCredito = {...recursoCredito.value};
        parameters.recursoDebito = {...recursoDebito.value};
    }

    dados.value.lancamentos.push(parameters);
    valor.value = null;
};

watch([contaCredito, contaDebito], () => {
    const atributos = [].concat(contaCredito.value.atributos, contaDebito.value.atributos);
    liberaAtributosPreenchimento(atributos)

    if (contaCredito.value?.recurso_id) {
        recursoCredito.value.codigo = contaCredito.value.recurso_id;
    }
    if (contaDebito.value?.recurso_id) {
        recursoDebito.value.codigo = contaDebito.value.recurso_id;
    }
})
</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Informe os dados dos lançamentos" class="w-full md:w-11 lg:w-8 xl:w-7">
                <div class="formgrid grid mt-4 row-gap-2">
                    <div class="field col-12 ">
                        <LookupReduzidosPcasp v-model="contaDebito" :pesquisaReduzido="contaDebito.reduzido"
                                              :exercicio="exercicio" :instituicao="instituicao"
                                              excluirContasBancarias="Sim" label="Débito"/>
                    </div>
                    <div class="field col-12 ">
                        <LookupReduzidosPcasp v-model="contaCredito" :pesquisaReduzido="contaCredito.reduzido"
                                              :exercicio="exercicio" :instituicao="instituicao"
                                              excluirContasBancarias="Sim" label="Crédito"/>
                    </div>
                    <div class="field col-12 ">
                        <LookupHistorico v-model="historico" :pesquisaCodigo="historico?.codigo"></LookupHistorico>
                    </div>

                    <!-- vincular com empenho-->
                    <div class="field col-12 " v-if="vincularEmpenho">
                        <LookupEmpenho v-model="empenho" :pesquisaNumero="empenho.numeroEmpenho" :exercicio="exercicio"
                                       :instituicao="instituicao" :dataSistema="dados.dataSistema"/>
                    </div>

                    <!-- vincular com Dotação-->
                    <div class="field col-12 " v-if="vincularDotacao">
                        <LookupDotacao v-model="dotacao" :pesquisaReduzido="dotacao.reduzido"
                                       :exercicio="exercicio" :instituicao="instituicao"/>
                    </div>

                    <!-- vincular com Receita-->
                    <div class="field col-12 " v-if="vincularReceita">
                        <LookupReceita v-model="receita" :pesquisaReduzido="receita.reduzido"
                                       :exercicio="exercicio" :instituicao="instituicao"/>
                    </div>

                    <!-- vincular com CGM-->
                    <div class="field col-12 " v-if="vincularCgm">
                        <AutoCompleteCgm v-model="cgm"/>
                    </div>

                    <!-- Lookup pesquisa de recurso a Débito -->
                    <div class="field col-12 " v-if="vincularRecurso">
                        <LookupRecurso v-model="recursoDebito" :pesquisaCodigo="recursoDebito.codigo"
                                       :exercicio="exercicio" :data="dados.dataLancamento" label="Débito:"/>
                    </div>

                    <!-- Lookup pesquisa de recurso a Crédito -->
                    <div class="field col-12 " v-if="vincularRecurso">
                        <LookupRecurso v-model="recursoCredito" :pesquisaCodigo="recursoCredito.codigo"
                                       :exercicio="exercicio" :data="dados.dataLancamento" label="Crédito:"/>
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
            </Panel>
        </section>
    </section>

    <section class="flex justify-content-center column-gap-2">
        <Button class="shadow-4" icon="pi pi-undo" @click="limparCampos"/>
        <Button class="shadow-4" type="button" label="Adicionar" icon="pi pi-plus" severity="success"
                @click="adicionarLancamento"/>
        <Button class="shadow-4" type="button" label="Próximo" icon="pi pi-chevron-right" iconPos="right"
                @click="dados.aba.ativa = 2"/>
    </section>
    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
</template>

<style scoped>
.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}

</style>
