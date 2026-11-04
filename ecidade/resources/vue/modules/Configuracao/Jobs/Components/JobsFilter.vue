<script setup>

import { reactive, watch } from 'vue';
import Calendar from 'primevue/calendar';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import JobsActionButtons from './JobsActionButtons.vue';

const props = defineProps({
    filter: Object,
    activeIndex: Number,
    selectJobs: Array,
    getJobs: Function,
    retry: Function,
    destroy: Function
});

const emit = defineEmits(['onSearch', 'clearFilter']);

const localFilter = reactive({ ...props.filter });

watch(props.filter, (newFilters) => {
    Object.assign(localFilter, newFilters);
});

const emitSearch = () => {
    emit('onSearch', localFilter);
};

</script>

<template>
    <div class="filter">
        <div class="filter-inputs">
            <Calendar placeholder="Data início" class="m-2" v-model="filter.data_inicio" dateFormat="dd/mm/yy" showButtonBar />
            <Calendar placeholder="Data Fim" class="m-2" v-model="filter.data_fim" dateFormat="dd/mm/yy" showButtonBar />
            <InputText placeholder="ID" class="m-2" v-model="filter.id"/>
            <InputText placeholder="Class" class="m-2" v-model="filter.entity"/>
            <InputText v-if="activeIndex === 1" placeholder="Exception" class="m-2" v-model="filter.exception"/>
        </div>

        <div class="buttons-container">
            <div class="filter-buttons-container">
                <Button
                    raised rounded icon="pi pi-search"
                    label="Pesquisar"
                    title="Pesquisar"
                    class="m-2"
                    @click="emitSearch"
                ></Button>

                <Button
                    raised rounded icon="pi pi-filter-slash"
                    label="Limpar Filtro"
                    title="Limpar"
                    class="m-2"
                    @click="$emit('clearFilter')"
                ></Button>

                <Button raised rounded icon="pi pi-refresh" class="m-2" @click="getJobs"></Button>
            </div>

            <JobsActionButtons :activeIndex="activeIndex" :selectJobs="selectJobs" :retry="retry" :destroy="destroy" />
        </div>
    </div>
</template>

<style scoped>
.filter {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.filter-inputs {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}

.filter-buttons-container {
    display: flex;
    align-items: center;
}

.buttons-container {
    display: flex;
}
</style>
