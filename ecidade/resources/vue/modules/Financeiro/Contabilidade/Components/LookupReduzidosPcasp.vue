<script setup>
import {computed, ref, watch} from "vue";
import DialogPesquisaReduzidosPcasp from "./DialogPesquisaReduzidosPcasp.vue";

const props = defineProps(['modelValue', 'pesquisaReduzido', 'exercicio', 'instituicao', 'excluirContasBancarias', 'label']);
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
const pesquisa = ref(props.pesquisaReduzido)

const blurReduzido = ({value}) => {
    // lógica para simular um change.
    if (retorno.value.reduzido == value) {
        return
    }
    retorno.value.reduzido = value ? Number(value) : null;
    retorno.value.estrutural = null;
    pesquisa.value = retorno.value.reduzido;
}

const blurEstrutural = ({value}) => {
    // lógica para simular um change.
    if (retorno.value.estrutural == value) {
        return
    }

    pesquisa.value = null;
    retorno.value.reduzido = null;
    retorno.value.estrutural = value ? Number(value) : null;
}

watch(pesquisaReduzido, (newValue, oldValue) => {
    if (newValue !== oldValue) {
        pesquisa.value = newValue;
    }
});
</script>

<template>
    <section>
        <div class="inputGroup gap-2">
            <Button icon="pi pi-search" severity="primary" @click="visibleDialog = true"
                    style="min-width: 35px"/>
            <div class="p-float-label w-2">
                <InputNumber id="input-reduzido"
                             :modelValue="retorno.reduzido"
                             :useGrouping="false"
                             class="w-full" :pt="{input:{class: 'w-full'}}"
                             @blur="blurReduzido"
                />
                <label for="input-reduzido">Conta {{ label }}</label>
            </div>
            <div class="p-float-label w-3">
                <InputNumber id="input-estrutural" type="text"
                             :modelValue="retorno.estrutural"
                             :useGrouping="false"
                             @blur="blurEstrutural"
                             class="w-full" :pt="{input:{class: 'w-full'}}"
                />
                <label for="input-estrutural">Estrutural</label>
            </div>
            <div class="p-float-label w-7">
                <InputText id="input-descricao" type="text" v-model="retorno.descricao" class="w-full"
                           :pt="{input:{class: 'w-full'}}" disabled/>
                <label for="input-descricao">Nome</label>
            </div>
        </div>
    </section>
    <section>
        <DialogPesquisaReduzidosPcasp v-model="retorno" v-model:visible="visibleDialog" :exercicio="exercicio"
                                      :reduzido="pesquisa" :estrutural="retorno.estrutural"
                                      :instituicao="instituicao" :excluirContasBancarias="excluirContasBancarias"/>
    </section>
</template>

<style scoped>
.inputGroup {
    display: flex;
    align-items: stretch;
    width: 100%;
}
</style>
