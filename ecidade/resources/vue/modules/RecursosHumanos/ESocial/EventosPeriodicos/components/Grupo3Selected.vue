<script setup>
import { ref } from 'vue';

defineProps(['label', 'grupo', 'seletectDados'])

const emits = defineEmits('alterarGrupo')

const opcaoSelect = ref()

const alterarGrupo = () => {
   emits('alterarGrupo', opcaoSelect)
}
</script>
<template>
    <label for=""><b>{{ label }}</b></label>
    <select class="selected-customizer w-full mb-3" v-model="opcaoSelect" @change="alterarGrupo">
        <option value="" disabled>-- Selecione uma opção</option>
        <option :selected="seletectDados === '1'? 'selected':''" v-for="c in grupo" :key="c.key" :value="c.key">{{ c.info }}</option>
    </select>
</template>
<style scoped>
.selected-customizer {
    padding: 0.5rem 0.5rem;
    border: 1px solid #605e5c;
    background-color: white;
    appearance: none;
    border-radius: 2px;
}
</style>
