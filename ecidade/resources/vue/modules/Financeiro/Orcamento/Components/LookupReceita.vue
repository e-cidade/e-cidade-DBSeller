<script setup>
import {computed, ref, watch} from "vue";
import DialogPesquisaReceita from "./DialogPesquisaReceita.vue";

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

const pesquisaReduzido = computed(() => props.pesquisaReduzido);

const visibleDialog = ref(false);
const reduzido = ref(props.pesquisaReduzido)
const blurPesquisa = ({value}) => {
    if (retorno.value.reduzido == value) {
        return
    }

    retorno.value.reduzido = value ? Number(value) : null;
};

watch(pesquisaReduzido, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        reduzido.value = newValue;
    }
})
</script>

<template>
    <section>
        <div class="inputGroup gap-1">
            <Button icon="pi pi-search" severity="primary" @click="visibleDialog = true"/>
            <div class="p-float-label">
                <InputNumber id="input-receita" :modelValue="retorno.reduzido" :useGrouping="false"
                             @blur="blurPesquisa"
                             style="width:150px" :pt="{input:{style: 'width:150px'}}"/>
                <label for="input-receita">Receita</label>
            </div>
        </div>
    </section>
    <section>
        <DialogPesquisaReceita v-model="retorno" :pesquisaReduzido="reduzido" v-model:visible="visibleDialog"
                               :exercicio="exercicio"/>
    </section>
</template>

<style scoped>
.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}
</style>
