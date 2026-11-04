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
    {name: 'Todos', code: '99'},
    {name: 'Não aplicável', code: '0'},
    {name: 'Sistema Orçamentário', code: '1'},
    {name: 'Sistema Patrimonial', code: '2'},
    {name: 'Sistema de Controle', code: '3'},
]);

</script>

<template>
    <div class="p-float-label w-full">
        <Dropdown v-model="modelValue" inputId="dd-sistema-conta"
                  :options="opcoes"
                  optionLabel="name" optionValue="code"
                  class="w-full md:w-14rem"/>
        <label for="dd-sistema-conta"> Sistema de Contas</label>
    </div>
</template>
