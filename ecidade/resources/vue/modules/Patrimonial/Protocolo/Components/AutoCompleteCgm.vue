<script setup>
import {computed, onMounted, ref} from "vue";
import {useToast} from "primevue/usetoast";
import DialogConsultaCgm from "./DialogConsultaCgm.vue";

const toast = useToast();
const props = defineProps(['modelValue', 'pesquisaCgm']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

const retorno = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const dialogConsultaCgm = ref(null);
const suggestionsCgms = ref([])

const searchCgm = async ({query}) => {
    const requestBody = {
        page: Number(1),
        porPagina: Number(28)
    };

    if (Number.isInteger(Number(query))) {
        requestBody.cgm = Number(query);
    } else {
        requestBody.nome = query;
    }

    try {
        const response = await dialogConsultaCgm.value.pesquisaGeralCgm(requestBody);
        suggestionsCgms.value = response.data.data.data;
    } catch (error) {
        toast.add({severity: 'error', detail: error, summary: 'Erro'});
    }
}

onMounted(async () => {
    if (props.pesquisaCgm !== undefined) {
       await searchCgm({query: props.pesquisaCgm});

       if (!suggestionsCgms.value.length) {
           retorno.value = null;
       }
       retorno.value = suggestionsCgms.value[0]
    }
})
</script>

<template>
<section>
    <div class="inputGroup gap-1">
        <div class="p-float-label w-full">
            <AutoComplete v-model="retorno" forceSelection optionLabel="nome"
                          :suggestions="suggestionsCgms" @complete="searchCgm"
                          class="w-full" :pt="{input:{class:'w-full'}}"/>
            <label for="input-cgm">CGM</label>
        </div>
    </div>
</section>
    <section>

        <DialogConsultaCgm ref="dialogConsultaCgm"/>

    </section>
</template>

<style scoped>

</style>
