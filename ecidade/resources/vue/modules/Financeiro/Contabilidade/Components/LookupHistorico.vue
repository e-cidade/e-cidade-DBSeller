<script setup>
import {computed, ref, watch} from "vue";
import DialogPesquisaHistorico from "./DialogPesquisaHistorico.vue";
import AutoCompleteHistorico from "./AutoCompleteHistorico.vue";

const props = defineProps(['modelValue', 'pesquisaCodigo', 'exercicio', 'instituicao']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

const retorno = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

// computed "readonly"
const pesquisaCodigo = computed(() => props.pesquisaCodigo);

const codigo = ref(props.pesquisaCodigo)
const visibleDialog = ref(false);

watch(pesquisaCodigo, (newValue, oldValue) => {

    if (newValue === undefined || !newValue) {
        return;
    }
    if (newValue !== oldValue) {
        codigo.value = newValue;
    }
    console.log('watch LookupHistorico', newValue, oldValue)
});
</script>

<template>
    <section>
        <div class="inputGroup gap-1">
            <Button icon="pi pi-search" severity="primary" @click="visibleDialog = true"
                    style="min-width: 35px"/>

            <AutoCompleteHistorico v-model="retorno" />
        </div>
    </section>

    <section>
        <DialogPesquisaHistorico v-model="retorno" :codigo="codigo" v-model:visible="visibleDialog"/>
    </section>
</template>

<style scoped>
.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}
</style>
