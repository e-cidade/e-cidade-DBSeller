<script setup>
import {computed, ref, watch} from "vue";
import DialogPesquisaDotacao from "./DialogPesquisaDotacao.vue";

const props = defineProps(['modelValue', 'pesquisaReduzido', 'exercicio', 'instituicao']);
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
const pesquisaReduzido = computed(() => props.pesquisaReduzido);

const visibleDialog = ref(false);
const pesquisa = ref(props.pesquisaReduzido)

const blurPesquisaDotacao = ({value}) => {

    if (retorno.value.reduzido == value) {
        return
    }

    retorno.value.reduzido = value ? Number(value) : null;
    pesquisa.value = retorno.value.reduzido;
};

watch(pesquisaReduzido, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        pesquisa.value = newValue;
    }
});
</script>

<template>

    <div class="inputGroup gap-1">
        <Button icon="pi pi-search" severity="primary" @click="visibleDialog = true"/>
        <div class="p-float-label">
            <InputNumber id="input-dotacao" :modelValue="retorno.reduzido" :useGrouping="false"
                         @blur="blurPesquisaDotacao"
                         style="width:150px" :pt="{input:{style: 'width:150px'}}"/>
            <label for="input-dotacao">Dotação</label>
        </div>
    </div>

    <section>
        <DialogPesquisaDotacao v-model="retorno" v-model:visible="visibleDialog"
                               :pesquisaReduzido="pesquisa"
                               :exercicio="exercicio" :instituicao="instituicao"/>
    </section>
</template>

<style scoped>
.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}
</style>
