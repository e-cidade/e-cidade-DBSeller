<script setup>

import TabMenu from 'primevue/tabmenu';
import Button from 'primevue/button';

const props = defineProps({
    activeIndex: Number,
    selectJobs: Array,
    retry: Function,
    destroy: Function,
    getJobs: Function
});

const emit = defineEmits(['update:activeIndex', 'clearSelectedJobs']);

const items = [
    { label: 'Jobs', icon: 'pi pi-list', value: 0 },
    { label: 'Jobs falhados', icon: 'pi pi-exclamation-circle', value: 1 }
];

const onTabChange = (event) => {
    emit('update:activeIndex', event.index);
    emit('clearSelectedJobs');
    props.getJobs();
}

</script>

<template>
    <TabMenu :model="items" :activeIndex="activeIndex" @tab-change="onTabChange" />

    <div class="d-flex flex-row flex-wrap justify-content-end align-items-center gap-2 ms-auto">
        <Button
            v-if="selectJobs.length && activeIndex === 1"
            raised rounded
            icon="pi pi-refresh"
            label="Reprocessar"
            title="Reprocessar"
            @click="retry">
        </Button>

        <Button
            v-if="selectJobs.length"
            raised rounded
            icon="pi pi-trash"
            label="Excluir"
            title="Excluir"
            @click="destroy">
        </Button>

        <Button icon="pi pi-refresh" rounded raised @click="getJobs"></Button>
    </div>
</template>
