<script setup>

import Button from 'primevue/button';

const props = defineProps({
    activeIndex: Number,
    selectJobs: Array,
    retry: Function,
    destroy: Function
});

</script>

<template>
    <div class="action-buttons-container">
        <Button
            v-if="selectJobs.length && activeIndex === 1"
            raised rounded
            class="m-2"
            icon="pi pi-refresh"
            label="Reprocessar"
            title="Reprocessar"
            @click="retry">
        </Button>

        <Button
            v-if="selectJobs.length"
            raised rounded
            class="m-2"
            icon="pi pi-trash"
            label="Excluir"
            title="Excluir"
            @click="destroy">
        </Button>
    </div>
</template>

<style scoped>
.action-buttons-container {
    display: flex;
    justify-content: center;
}
</style>
