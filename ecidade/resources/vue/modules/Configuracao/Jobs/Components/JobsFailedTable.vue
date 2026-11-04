<script setup>

import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Button from 'primevue/button';

const props = defineProps({
    jobsFailed: Array,
    selectJobs: Array,
    showModal: Function,
    formatDate: Function,
    loading: Boolean
});

const emit = defineEmits(["update:selectJobs", "onSort"]);

const updateSelection = (newSelection) => {
    emit('update:selectJobs', newSelection);
}

const sort = (event) => {
    emit("onSort", event);
}

</script>

<template>
    <DataTable
        v-if="jobsFailed.length !== 0"
        :value="jobsFailed"
        :selection="selectJobs"
        :loading="loading"
        @update:selection="updateSelection"
        @sort="sort"
    >
        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>

        <Column field="id" header="ID" sortable></Column>

        <Column field="uuid" header="UUID" sortable>
            <template #body="{ data }">
                {{ JSON.parse(data.payload).uuid }}
            </template>
        </Column>

        <Column field="failed_at" header="Falhado em" sortable>
            <template #body="{ data }">
                {{ formatDate(data.failed_at) }}
            </template>
        </Column>

        <Column field="class" header="Class" sortable="">
            <template #body="{ data }">
                {{ JSON.parse(data.payload).displayName }}
            </template>
        </Column>

        <Column field="connection" header="Connection" sortable></Column>

        <Column field="queue" header="Queue" sortable></Column>

        <Column field="payload" header="Payload" sortable>
            <template #body="{ data }">
                <Button @click="showModal('Payload', data.payload)" icon="pi pi-eye"></Button>
            </template>
        </Column>

        <Column field="exception" header="Exception" sortable>
            <template #body="slotProps">
                <div class="exception-container">
                    <Button @click="showModal('Exception', slotProps.data.exception)" icon="pi pi-eye"></Button>
                    <p class="m-0">
                        {{ slotProps.data.exception.split("Stack trace")[0] }}
                    </p>
                </div>
            </template>
        </Column>
    </DataTable>
</template>

<style scoped>
.exception-container {
    display: flex;
    align-items: center;
    gap: 5px;
}
</style>
