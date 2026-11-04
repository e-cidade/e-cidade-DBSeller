<script setup>
import {computed, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import ModalLoading from "../../../../Components/ModalLoading.vue";

import LookupReduzidosPcasp from "../../Components/LookupReduzidosPcasp.vue";


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
const conta = ref({});
const validado = ref(true);

const routes = {
    validaContas: "v4/api/financeiro/contabilidade/conta-corrente/cadastro/verificaExistenciaExeContaCorrente",
    salvaConta: "v4/api/financeiro/contabilidade/conta-corrente/cadastro/configurarSaldoInicialPorRecurso"
};

function limparCampos() 
{
     conta.value = {};    
     validado.value = !validado.value;
}


const salvar  = () => {
    window.axios.post(routes.salvaConta,  conta.value).then(async response =>{
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
        limparCampos();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    })
}


watch(conta, () => {
    if(conta.value.reduzido){
        window.axios.get(routes.validaContas, {'params': conta.value} ).then(async response => {
         toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
            validado.value = !validado.value;
        }).catch(response => {
            toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
        })
    }   
});

</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center mt-5">
            <Panel header="Informe os dados dos lançamentos" class="w-full md:w-11 lg:w-8 xl:w-7">
                <div class="formgrid grid mt-4 row-gap-2">
                    <div class="field col-12 ">
                        <LookupReduzidosPcasp v-model="conta" :pesquisaReduzido="conta.reduzido"
                                              :exercicio="exercicio" :instituicao="instituicao"
                                              />
                    </div>
                </div>
            </Panel>
        </section>
    </section>

    <section class="flex justify-content-center column-gap-2">
        <Button class="shadow-4" icon="pi pi-undo" @click="limparCampos"/>
        <Button class="shadow-4" type="button" label="Salvar" icon="pi pi-chevron-right" iconPos="right" :disabled="validado"
                @click="salvar"/>
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
