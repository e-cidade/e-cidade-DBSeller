<script setup>
import { ref, watch } from 'vue';

const props = defineProps(['currentStep', 'disableCheck', 'validaInput', 'periodos', 'patronal', 'btnDisabled', 'selectPeriodoEmit', 'periodosValue', 'forms'])
const emits = defineEmits(['prevStep', 'nextStep', 'salvar', 'showTable'])
const resetStep = ref()
const btnPrev = ref(false)
const btnTeste = ref(true)
const visible = ref(false)

const prevStep = () => {
    emits('prevStep')
    btnPrev.value = true
}

if (props.forms) {
    props.disableCheck = true
    props.validaInput = true
}

const nextStep = () => {
    emits('nextStep')
}

const salvar = () => {
    emits('salvar')
}

const showTable = () => {
    visible.value = true
    emits('showTable', visible)
}

const novo = () => {
    resetStep.value = 0
    emits('novo', resetStep)
}

watch(() => props.btnDisabled, (nw, old) => {
    if (nw != old) {
        btnTeste.value = nw
        // btnTeste.value = false
    }
})

</script>

<template>
    <div class="flex mt-3">
        <div v-if="currentStep == 0">
            <Button id="btn-disabled" label="Salvar" :disabled="btnDisabled" @click.prevent="nextStep" />
            <Button label="Pesquisar" severity="help" @click="showTable" />
        </div>
        <div v-else>
            <Button label="Voltar" class="mr-2" severity="secondary" :disabled="false" @click.prevent="prevStep" />
            <Button label="Novo" class="mr-2" @click="novo" severity="info" />
            <template v-if="currentStep !== 5">
                <Button label="Salvar" @click.prevent="salvar" class="mr-2" severity="success" />
            </template>
            <Button label="Pesquisar" severity="help" @click="showTable" />
            <template v-if="currentStep == 5">
                <Button label="Salvar" class="ml-2" severity="secondary" :disabled="!disableCheck || !validaInput"
                    @click.prevent="salvar"/>
            </template>
            <template v-else>
                <Button label="Próximo" class="ml-2" severity="secondary" :disabled="false"
                    @click.prevent="nextStep" />
            </template>
        </div>
    </div>
</template>
