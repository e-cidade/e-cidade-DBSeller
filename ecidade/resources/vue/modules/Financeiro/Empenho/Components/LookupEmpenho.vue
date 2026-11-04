<script setup>
import {computed, ref, watch} from "vue";
import DialogPesquisaEmpenho from "./DialogPesquisaEmpenho.vue";

const props = defineProps(['modelValue', 'pesquisaCodigo', 'pesquisaNumero', 'exercicio', 'instituicao', 'dataSistema']);
const emit = defineEmits(['update:modelValue', 'update:visible']);

const retorno = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const pesquisaNumero = computed(() => props.pesquisaNumero);

const visibleDialog = ref(false);
const pesquisaNumeroEmpenho = ref(props.pesquisaNumero)

const blurPesquisa = ({value}) => {
    retorno.value.numeroEmpenho = event.target.value
    pesquisaNumeroEmpenho.value = retorno.value.numeroEmpenho;
};

watch(pesquisaNumero => (newValue, oldValue) => {
    if (newValue !== oldValue) {
        pesquisaNumeroEmpenho.value = newValue;
    }
})

</script>

<template>
    <section>
        <div class="inputGroup gap-1">
            <Button icon="pi pi-search" severity="primary" @click="visibleDialog = true"/>
            <div class="p-float-label">
                <InputText id="input-empenho" :modelValue="retorno.numeroEmpenho"
                           @change="blurPesquisa"
                           style="width:150px" :pt="{input:{style: 'width:150px'}}"/>
                <label for="input-empenho">Empenho</label>
            </div>
        </div>
    </section>
    <section>
        <DialogPesquisaEmpenho v-model="retorno" v-model:visible="visibleDialog"
                               :pesquisaNumero="pesquisaNumeroEmpenho" :pesquisaCodigo="pesquisaCodigo"
                               :exercicio="exercicio" :instituicao="instituicao" :dataSistema="dataSistema"/>
    </section>
</template>

<style scoped>
.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}
</style>
