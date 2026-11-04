<script setup>
import {computed, ref, watch} from "vue";
import DialogPesquisaRecurso from "./DialogPesquisaRecurso.vue";

const props = defineProps(['modelValue', 'pesquisaCodigo', 'exercicio', 'data', 'label']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

/**
 * retorno ou modelValue é o objeto que terá os dados selecionados
 * @type {WritableComputedRef<*>}
 */
const retorno = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const pesquisaCodigo = computed(() => props.pesquisaCodigo);

/**
 * Código pesquisar
 */
const codigoPesquisar = ref(props.pesquisaCodigo)
const visibleDialog = ref(false);

watch(pesquisaCodigo, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        codigoPesquisar.value = newValue;
    }
})
</script>

<template>
    <section>
        <div class="inputGroup gap-1">
            <Button icon="pi pi-search" severity="primary" @click="visibleDialog = true"
                    style="min-width: 35px"/>
            <div class="p-float-label w-12">
                <InputText id="input-recurso" v-model="retorno.apresentacao" disabled class="w-full"/>
                <label for="input-recurso">{{ label }} Siconfi - Subrecurso - Complemento - Descrição</label>
            </div>
        </div>
    </section>
    <section>
        <DialogPesquisaRecurso v-model="retorno" :pesquisaCodigo="codigoPesquisar" v-model:visible="visibleDialog"
                               :exercicio="exercicio" :data="data"/>
    </section>
</template>

<style scoped>
.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}
</style>
