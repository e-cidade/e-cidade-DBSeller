<script setup>

import { computed, onMounted, ref } from "vue";

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const relatorio = computed({
    get() { return props.modelValue },
    set(value) { emit('update:modelValue', value) }
});

const margem = ref({
    direita: 0,
    esquerda: 0,
    superior: 0,
    inferior: 0
});

/** @todo buscar informações na API? */
const tiposSaida = ref([
    { value: 'pdf', label: 'PDF'},
    { value: 'csv', label: 'CSV'},
    { value: 'txt', label: 'TXT'}
]);
const tiposPagina = ref(['A4']);
const tiposOrientacoes = ref([
    { value: 'portrait', label: 'Retrato' },
    { value: 'landscape', label: 'Paisagem' }
]);

onMounted(() => {
    if (relatorio.value.margem === undefined) {
        return;
    }
    const { margem: oMargem } = relatorio.value;
    margem.value = oMargem;
});

</script>

<template>
    <section class="flex flex-column gap-2">
        <section class="flex justify-content-center">
            <Panel header="Dados do relatório" class="w-5">
                <div class="formgrid grid row-gap-2 mt-2">
                    <div class="field col-12">
                        <span class="p-float-label">
                            <InputText id="nome" type="text" v-model="relatorio.nome" class="w-full"/>
                            <label for="nome">Nome</label>
                        </span>
                    </div>
                    <div class="field col-12">
                        <span class="p-float-label">
                            <Dropdown inputId="tipoSaida"
                                      v-model="relatorio.tipoSaida"
                                      :options="tiposSaida"
                                      optionLabel="label"
                                      optionValue="value"
                                      class="w-full"></Dropdown>
                            <label for="tipoSaida">Formato de Saída</label>
                        </span>
                    </div>
                    <template v-if="relatorio.tipoSaida === 'csv'">
                        <div class="field col-12">
                            <div class="flex align-items-center">
                                <Checkbox inputId="delimitar-texto" :binary="true" v-model="relatorio.delimitarTexto"></Checkbox>
                                <label class="ml-2" for="delimitar-texto">Delimitar textos complexos(aplica aspas duplas em volta do texto).</label>
                            </div>
                        </div>
                        <div class="field col-12">
                            <div class="flex align-items-center">
                                <Checkbox inputId="imprimir-cabecalho" :binary="true" v-model="relatorio.imprimirCabecalho"></Checkbox>
                                <label class="ml-2" for="imprimir-cabecalho">Imprimir cabeçalho.</label>
                            </div>
                        </div>
                    </template>
                </div>
            </Panel>
        </section>
        <template v-if="relatorio.tipoSaida === 'pdf'">
            <section class="flex justify-content-center">
                <Panel header="Configurações da página" class="w-5">
                    <div class="formgrid grid row-gap-2 mt-2">
                        <div class="field col-12">
                            <span class="p-float-label">
                                <Dropdown inputId="formato"
                                          v-model="relatorio.formato"
                                          :options="tiposPagina"
                                          class="w-full"></Dropdown>
                                <label for="formato">Formato</label>
                            </span>
                        </div>
                        <div class="field col-12">
                            <span class="p-float-label">
                                <Dropdown inputId="orientacao"
                                          v-model="relatorio.orientacao"
                                          :options="tiposOrientacoes"
                                          optionLabel="label"
                                          optionValue="value"
                                          class="w-full"></Dropdown>
                                <label for="orientacao">Orientação</label>
                            </span>
                        </div>
                    </div>
                </Panel>
            </section>
            <section class="flex justify-content-center">
                <Panel header="Margens" class="w-5">
                    <div class="formgrid grid row-gap-2 mt-2">
                        <div class="field col-12 md:col-6">
                            <span class="p-float-label">
                                <InputText id="direita" type="text" v-model="margem.direita" class="w-full"/>
                                <label for="direita">Direita</label>
                            </span>
                        </div>
                        <div class="field col-12 md:col-6">
                            <span class="p-float-label">
                                <InputText id="esquerda" type="text" v-model="margem.esquerda" class="w-full"/>
                                <label for="esquerda">Esquerda</label>
                            </span>
                        </div>
                        <div class="field col-12 md:col-6">
                            <span class="p-float-label">
                                <InputText id="inferior" type="text" v-model="margem.inferior" class="w-full"/>
                                <label for="inferior">Inferior</label>
                            </span>
                        </div>
                        <div class="field col-12 md:col-6">
                            <span class="p-float-label">
                                <InputText id="superior" type="text" v-model="margem.superior" class="w-full"/>
                                <label for="superior">Superior</label>
                            </span>
                        </div>
                    </div>
                </Panel>
            </section>
        </template>
    </section>
</template>

<style scoped>

</style>
