<script setup>
/**
 * O Componente monta um Chips para informar uma lista de estrutural/numeros
 */
import {computed} from "vue";

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const modelValue = computed({
    get() {
        return props.modelValue
    },
    set(values) {
        emit('update:modelValue', sanatizeNumber(values))
    }
});

/**
 * trata os valores inputados para retornar apenas números
 * @param valores
 * @returns {[]}
 */
 const sanatizeNumber = (valores) => {
     let valueFilter = [];
     for (const v of valores) {
         // aplico o slip por quebra de linha pois o suporte costuma colar vários estruturais
         let items = v.split('\n').filter(function (v) {
             return RegExp('^[0-9]+$').test(v)
         });

         valueFilter = valueFilter.concat(items);
     }
     return valueFilter
}

</script>

<template>
    <div class="p-float-label w-full">
        <Chips v-model="modelValue" separator=","
               :pt="{root:{class:['w-full']}, container:{class:['w-full']}}"
               id="lb-estruturais"/>
        <label for="lb-estruturais">Informe o(s) estrutural(is) e pressione Enter (virgula no teclado numérico)</label>
    </div>
</template>
