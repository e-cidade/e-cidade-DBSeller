<script setup>
import {computed, ref} from "vue";
import {useToast} from "primevue/usetoast";

const toast = useToast();

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);
/**
 * Declaração das computed
 */
const selectedComplemento = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const suggestionsComplementos = ref([]);
const mensagemLoad = ref(null);
const loading = ref(false);

const rout = 'v4/api/financeiro/orcamento/recursos/complementos';

const searchComplementos = ({query}) => {
    const parameters = {
        autocomplete: query
    }

    return window.axios.get(rout, {'params': parameters}).then(response => {
        suggestionsComplementos.value = response.data.data;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    })
}

</script>

<template>
    <div class="p-float-label w-full">
        <AutoComplete v-model="selectedComplemento" :suggestions="suggestionsComplementos"
                      optionLabel="apresentar" @complete="searchComplementos" forceSelection
                      class="w-full" :pt="{input:{class:'w-full'}}"/>

        <label for="dd-complemento">Complemento</label>
    </div>
</template>
