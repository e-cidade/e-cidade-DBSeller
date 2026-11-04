<script setup>
/**
 * O Componente monta um Dropdown para selecionar o indicador de superavit
 */
import {computed, ref} from "vue";

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const modelValue = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const opcoes = ref([
    {name: 'Todos', code: 'T'},
    {name: 'N - Não se aplica', code: 'N'},
    {name: 'F - Financeiro', code: 'F'},
    {name: 'P - Permanente', code: 'P'}
]);
</script>

<template>
    <div class="p-float-label w-full">
        <Dropdown v-model="modelValue" inputId="dd-indicador-superavit"
                  :options="opcoes"
                  optionLabel="name" optionValue="code"
                  class="w-full md:w-14rem"/>
        <label for="dd-indicador-superavit"> Indicador de Superávit</label>
    </div>
</template>
