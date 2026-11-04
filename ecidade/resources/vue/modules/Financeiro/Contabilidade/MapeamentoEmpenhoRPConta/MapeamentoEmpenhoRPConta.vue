<script setup>
import { ref } from 'vue';
import FiltroContasMapeadas from './Components/FiltroContasMapeadas';
import DataTableContasMapeadas from './Components/DataTableContasMapeadas';

// props
const props = defineProps(['instituicao', 'exercicio']);

// data
const filters = ref({
    exercicio: props.exercicio,
    instituicao: props.instituicao
});

// methods
const setFilters = (filter) => {
    filters.value = filter;
}
</script>

<template>
    <div class="container mt-5">
        <FiltroContasMapeadas
            :instit="instituicao"
            :exercicio="exercicio"
            @filter="setFilters"
            class="mx-auto"
        />
        <Divider/>
        <DataTableContasMapeadas :filters="filters"/>
    </div>
</template>
