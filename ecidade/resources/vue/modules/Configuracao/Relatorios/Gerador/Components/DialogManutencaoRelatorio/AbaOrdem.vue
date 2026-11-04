<script setup>

import { computed, onMounted, ref, watch } from "vue";

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const campos = computed({
    get() { return props.modelValue },
    set(value) { emit('update:modelValue', value) }
});

const opcoes = ref([
    { value: 'asc', label: 'Crescente' },
    { value: 'desc', label: 'Decrescente' }
]);

/**
 * Ajuste técnico para fazer o PickList imprimir as duas listas de forma diferente
 * @todo Refatorar quando houver alternativa melhor. Até a versão 3.32.0 do PrimeVue, não existia alternativa.
 */
function verificaPosicaoCampo() {
    if (!campos.value.length) {
        return;
    }

    const [disponiveis, selecionados] = campos.value;

    for (const disponivel of disponiveis) {
        disponivel.isTarget = false;
    }

    for (const selecionado of selecionados) {
        selecionado.isTarget = true;
    }
}

watch(campos, verificaPosicaoCampo);
onMounted(verificaPosicaoCampo);

</script>

<template>
    <section class="flex justify-content-center">
        <PickList v-model="campos" listStyle="height: 400px" class="w-8">
            <template #sourceheader> Disponível </template>
            <template #targetheader> Selecionado </template>
            <template #item="slotProps">
                <div class="flex flex-wrap p-2 align-items-center gap-3">
                    <div class="flex-1 flex flex-column gap-2">
                        <span class="font-bold">{{ slotProps.item.nome }}</span>
                        <div class="flex align-items-center gap-2">
                            <i class="pi pi-tag text-sm"></i>
                            <span>{{ slotProps.item.alias }}</span>
                        </div>
                    </div>
                    <SelectButton v-if="slotProps.item.isTarget"
                                  :modelValue="slotProps.item.tipo"
                                  :options="opcoes"
                                  optionLabel="label"
                                  optionValue="value"
                                  @update:modelValue="value => slotProps.item.tipo = value ?? slotProps.item.tipo"></SelectButton>
                </div>
            </template>
        </PickList>
    </section>
</template>

<style scoped>

</style>
