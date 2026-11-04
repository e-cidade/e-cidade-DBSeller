<script setup>
import {computed, onBeforeMount, onMounted, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";


const toast = useToast();
const confirm = useConfirm();
const props = defineProps(['modelValue', 'visible', 'dataSistema']);
const emit = defineEmits(['update:visible']);

const modelValue = computed(() => props.modelValue);

const vincularEmpenho = ref(false);
const vincularDotacao = ref(false);
const vincularReceita = ref(false);
const vincularCgm = ref(false);
const vincularRecurso = ref(false);

onBeforeMount(() => {
    let credito = modelValue.value.credito;
    let debito = modelValue.value.debito;
    let historico = modelValue.value.historico;
    let empenho = modelValue.value?.empenho;
    let dotacao = modelValue.value?.dotacao;
    let receita = modelValue.value?.receita;
    let cgm = modelValue.value?.cgm;
    let recursoCredito = modelValue.value?.recursoCredito;
    let recursoDebito = modelValue.value?.recursoDebito;

    modelValue.value.resumoDebito = `${debito.reduzido} - ${debito.estrutural} - ${debito.descricao}`;
    modelValue.value.resumoCredito = `${credito.reduzido} - ${credito.estrutural} - ${credito.descricao}`;
    modelValue.value.resumoHistorico = `${historico.codigo} - ${historico.nome}`;

    if (empenho) {
        vincularEmpenho.value = true;
        modelValue.value.resumoEmpenho = empenho.numeroEmpenho;
    }

    if (dotacao) {
        vincularDotacao.value = true;
        modelValue.value.resumoDotacao = `${dotacao.reduzido} - ${dotacao.elemento.elemento}  - ${dotacao.elemento.descricao}`;
    }
    if (receita) {
        vincularReceita.value = true;
        modelValue.value.resumoReceita = `${receita.reduzido} - ${receita.naturezaReceita.estrutural} - ${receita.naturezaReceita.descricao}`;
    }
    if (cgm) {
        vincularCgm.value = true;
        modelValue.value.resumoCgm = `${cgm.numcgm} ${cgm.nome}`;
    }
    if (recursoCredito && recursoDebito) {
        vincularRecurso.value = true;
        modelValue.value.resumoRecursoCredito = recursoCredito.apresentacao;
        modelValue.value.resumoRecursoDebito = recursoDebito.apresentacao;
    }

    modelValue.value.resumoValor = formataValorMonetario(modelValue.value.valor);

});

</script>

<template>
    <Dialog :visible="visible" modal header="Resumo do Lançamento a ser criado."
            @update:visible="value => $emit('update:visible', value)"
            class="p-dialog p-component p-dialog-maximized " :pt="{content:{style:'background-color:#e0dddd'}}">
        <section class="flex flex-column w-full ">
            <section class="flex justify-content-center">
                <div class="formgrid grid mt-4 w-full md:w-11 lg:w-8 xl:w-6 row-gap-2">
                    <div class="field col-12 ">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoDebito" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Conta Débito</label>
                        </div>
                    </div>

                    <div class="field col-12 ">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoCredito" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Conta Cérdito</label>
                        </div>
                    </div>

                    <div class="field col-12 ">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoHistorico" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Histórico</label>
                        </div>
                    </div>

                    <div class="field col-12 " v-if="vincularEmpenho">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoEmpenho" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Empenho</label>
                        </div>
                    </div>
                    <div class="field col-12 " v-if="vincularDotacao">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoDotacao" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Dotação</label>
                        </div>
                    </div>

                    <div class="field col-12 " v-if="vincularReceita">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoReceita" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Receita</label>
                        </div>
                    </div>

                    <div class="field col-12 " v-if="vincularCgm">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoCgm" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">CGM</label>
                        </div>
                    </div>
                    <div class="field col-12 " v-if="vincularRecurso">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoRecursoDebito" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Recurso Débito</label>
                        </div>
                    </div>
                    <div class="field col-12 " v-if="vincularRecurso">
                        <div class="p-float-label w-full">
                            <InputText id="input-descricao" type="text" v-model="modelValue.resumoRecursoCredito" class="w-full"
                                       :pt="{input:{class: 'w-full'}}" disabled/>
                            <label for="input-descricao">Recurso Crédito</label>
                        </div>
                    </div>
                    <div class="field col-12">
                        <div class="p-float-label w-full">
                            <InputText id="input-valor" v-model="modelValue.resumoValor" disabled
                                       locale="pt-BR"/>
                            <label for="input-valor">Valor</label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label w-full">
                            <Textarea v-model="modelValue.observacao" rows="2" cols="30" class="w-full" disabled/>
                            <label for="input-observacao">Valor</label>
                        </div>
                    </div>
                </div>
            </section>
        </section>
    </Dialog>
</template>

<style scoped>

</style>
