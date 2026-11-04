<script setup>

import {computed} from "vue";

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
</script>

<template>
    <div class="field-checkbox ">
        <Checkbox v-model="modelValue" inputId="encerramento" :binary="true"/>
        <label for="encerramento">Encerramento de Exercício </label>
    </div>
</template>

