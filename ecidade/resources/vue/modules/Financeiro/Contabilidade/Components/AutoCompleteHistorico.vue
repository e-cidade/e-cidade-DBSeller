<script setup>
import {computed, ref, watch} from "vue";
import {useToast} from "primevue/usetoast";

const toast = useToast();
const props = defineProps(['modelValue', 'exercicio', 'apenasComDotacao']);
const emit = defineEmits(['update:modelValue']);

const modelValue = computed( {
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const suggestions = ref([]);
const mensagemLoad = ref(null);
const loading = ref(false);

let rota = 'v4/api/financeiro/contabilidade/historico';

const selecionado = ref();

const search = ({query}) => {
    const parameters = {
        exercicio: props.exercicio,
        autocomplete: query,
        rows: 15
    }

    if (props.apenasComDotacao) {
        parameters.apenasComDotacao = props.apenasComDotacao;
    }

    return window.axios.get(rota, {'params': parameters}).then(response => {
        suggestions.value = response.data.data.historicos;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    })
}

function retornaSelecionado() {
    if (typeof selecionado.value === "object") {
        emit("update:modelValue", selecionado.value)
    } else {
        emit("update:modelValue", null)
    }
}

watch(modelValue, () => {

    if (!modelValue.value) {
        selecionado.value = null;
        suggestions.value = [];
        return
    }
    selecionado.value = {... modelValue.value}
})
</script>

<template>
    <div class="p-float-label w-full">
        <AutoComplete v-model="selecionado" :suggestions="suggestions" @update:modelValue="retornaSelecionado"
                      optionLabel="apresentar" @complete="search" forceSelection
                      class="w-full" :pt="{input:{class:'w-full'}}"/>
        <label for="dd-complemento">Histórico</label>
    </div>
</template>

<style scoped>

</style>
