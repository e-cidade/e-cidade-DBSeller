<script setup>
import { computed } from 'vue';

// props
const props = defineProps({
    saldo: {type: Number, default: 0},
    label: String
});

// computed
const saldoBRL = computed(() => {
    return Number(props.saldo).toLocaleString('pt-BR', {minimumFractionDigits: 2});
})
</script>

<template>
    <span class="badge">
       {{ label }}: <span class="font-semibold">R$ {{ saldoBRL }}</span>
    </span>
</template>

<style scoped>
    .badge {
        font-weight: normal;
        display: inline-block;
        background-color: #eee;
        padding: .5rem;
        border-radius: 2px;
    }
</style>

