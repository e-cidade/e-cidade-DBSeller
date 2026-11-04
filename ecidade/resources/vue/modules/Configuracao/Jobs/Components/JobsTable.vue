<script setup>

import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Button from 'primevue/button';

const props = defineProps({
    jobs: Array,
    selectJobs: {
        type: Array,
        default: () => []
    },
    showModal: Function,
    formatDate: Function,
    loading: Boolean
});

const emit = defineEmits(["update:selectJobs", "onSort"]);

const updateSelection = (newSelection) => {
    emit("update:selectJobs", newSelection);
}

const sort = (event) => {
    emit("onSort", event);
}

const tempoExecucao = (reservedAt) => {
    if (Number.isInteger(reservedAt)) {
        const reservedAtInSeconds = reservedAt * 1000;
        const currentDateTimeInSeconds = Date.now();

        const roundedTime = Math.floor((currentDateTimeInSeconds - reservedAtInSeconds) / 1000);

        return formatTime(roundedTime);
    }

    return "Aguardando execução";
}

const formatTime = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    const formattedHours = String(hours).padStart(2, "0") + ":";
    const formattedMinutes = String(minutes).padStart(2, "0") + ":";
    const formattedSeconds = String(secs).padStart(2, "0");

    return formattedHours + formattedMinutes + formattedSeconds;
}

</script>

<template>
    <DataTable
        v-if="jobs.length !== 0"
        :value="jobs"
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

        <Column field="class" header="Class" sortable>
            <template #body="{ data }">
                {{ JSON.parse(data.payload).displayName }}
            </template>
        </Column>

        <Column field="created_at" header="Criado em" sortable>
            <template #body="{ data }">
                {{ formatDate(data.created_at) }}
            </template>
        </Column>

        <Column field="available_at" header="Disponível em" sortable>
            <template #body="{ data }">
                {{ formatDate(data.available_at) }}
            </template>
        </Column>

        <Column field="reserved_at" header="Executado em" sortable>
            <template #body="{ data }">
                {{ data.reserved_at !== null ? formatDate(data.reserved_at) : "Aguardando execução" }}
            </template>
        </Column>

        <Column field="attempts" header="Tentativas" sortable></Column>

        <Column header="Tempo em execução" sortable>
            <template #body="{ data }">
                {{ tempoExecucao(data.reserved_at) }}
            </template>
        </Column>

        <Column field="queue" header="Queue" sortable></Column>

        <Column field="payload" header="Payload" sortable>
            <template #body="{ data }">
                <Button @click="showModal('Payload',data.payload)" icon="pi pi-eye"></Button>
            </template>
        </Column>
    </DataTable>
</template>
