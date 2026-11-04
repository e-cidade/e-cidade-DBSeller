<script setup>
import {computed, ref} from "vue";
import {useToast} from "primevue/usetoast";

const toast = useToast();
const props = defineProps(['modelValue', 'exercicio', 'apenasComReceita']);
const emit = defineEmits(['update:modelValue']);

/**
 * Declaração das computed
 */
const selected = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const suggestions = ref([]);
const rout = 'v4/api/financeiro/orcamento/receita/natureza-receita';

const search = ({query}) => {
    const parameters = {
        exercicio: props.exercicio,
        autocomplete: query,
        rows: 15
    }

    if (props.apenasComReceita) {
        parameters.apenasComReceita = props.apenasComReceita;
    }

    return window.axios.get(rout, {'params': parameters}).then(response => {
        suggestions.value = response.data.data.naturezas;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    })
}
</script>

<template>

    <div class="p-float-label w-full">
        <AutoComplete v-model="selected" :suggestions="suggestions"
                      optionLabel="apresentar" @complete="search" forceSelection
                      class="w-full" :pt="{input:{class:'w-full'}}"/>

        <label for="dd-complemento">Natureza da Receita</label>
    </div>
</template>
