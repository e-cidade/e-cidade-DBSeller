<script setup>
import {ref, shallowRef} from "vue";
import AbasComponent from "../../../../Components/AbasComponent.vue";
import AbaGeral from "./AbaGeral.vue";
import AbaDisciplinas from "./AbaDisciplinas.vue";
import AbaBaseContinuacao from "./AbaBaseContinuacao.vue";
import AbaLegislacao from "./AbaLegislacao.vue";

const props = defineProps(['escola', 'secretaria', 'base', 'bases'])
const visible = ref(false)
const emits = defineEmits(['save'])

const disabledAbaDisciplina = ref(true)
const disabledAbaBaseContinuacao = ref(true)
const disabledAbaBaseLegislacao = ref(true)
async function close() {
    visible.value = false
}
async function open(dados = null) {
    visible.value = true
    abas.value.forEach(aba => {
        aba.props.base = null
    })
    abas.value[1].disabled = true
    abas.value[2].disabled = true
    abas.value[3].disabled = true
    if (dados) {
        abas.value[0].props.base = dados
        abas.value[1].props.base = dados
        abas.value[1].disabled = false
        if (!props.secretaria) {
            abas.value[2].disabled = false
            abas.value[3].disabled = false
            abas.value[2].props.base = dados
            abas.value[3].props.base = dados
        }
    }
}
defineExpose({ open, close })

const abas = shallowRef([
    {
        nome: 'Geral',
        iconClass: 'pi pi-cog',
        componente: AbaGeral,
        props: {
            escola: props.escola,
            secretaria: props.secretaria,
            base: null
        },
        eventos: {
            save: async (e) => {
                abas.value[1].props.base = e
                abas.value[1].disabled = false
                emits('close', e)
                close();
            }
        },
        disabled: false
    },
    {
        nome: 'Disciplinas',
        iconClass: 'pi pi-book',
        componente: AbaDisciplinas,
        props: {
            base: null
        },
        eventos: {},
        disabled: true
    },
    {
        nome: 'Base Continuação',
        iconClass: 'pi pi-refresh',
        componente: AbaBaseContinuacao,
        props: {
            base: null,
            escola: props.escola
        },
        eventos: {},
        disabled: true
    },
    {
        nome: 'Legislação',
        iconClass: 'pi pi-exclamation-triangle',
        componente: AbaLegislacao,
        props: {
            base: null,
            escola: props.escola
        },
        eventos: {},
        disabled: true
    }
])
</script>

<template>
    <Dialog header="Base Curricular" v-model:visible="visible" class="p-dialog-maximized">
        <AbasComponent :abas="abas" :bases="bases"></AbasComponent>
    </Dialog>
</template>

<style scoped>
    span {
        cursor: pointer
    }
    .check {
        display: flex;
        align-items: center;
    }
</style>
