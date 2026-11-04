<script setup>

import { useToast } from "primevue/usetoast";
import { ref, shallowRef } from "vue";

import ModalLoading from "../../../../../Components/ModalLoading.vue";
import InputDinamico from "../InputDinamico.vue";

const props = defineProps({
    modelValue: { type: Object, required: true }
});

const toast = useToast();

const isLoading = ref(false);

const relatorio = shallowRef(props.modelValue);

async function imprimir() {
    isLoading.value = true;
    try {
        const response = await axios.post('v4/api/configuracao/gerador/relatorios/imprimir', relatorio.value);

        window.open(response.data.data.path, 'relatorio-gerador-relatorio', 'popup');
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao imprimir relatório',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

</script>

<template>
    <section class="flex flex-column gap-2">
        <section class="flex justify-content-center">
            <div class="form grid row-gap-2 mt-4">
                <template v-for="variavel of relatorio.variaveis">
                    <div class="field col-12">
                        <InputDinamico :modelValue="variavel" @update:modelValue="value => variavel.valor = value.valor"></InputDinamico>
                    </div>
                </template>
            </div>
        </section>
        <section class="flex justify-content-center">
            <Button icon="pi pi-print" label="Imprimir" @click="imprimir"></Button>
        </section>
    </section>

    <ModalLoading :isLoading="isLoading"></ModalLoading>
</template>

<style scoped>

</style>
