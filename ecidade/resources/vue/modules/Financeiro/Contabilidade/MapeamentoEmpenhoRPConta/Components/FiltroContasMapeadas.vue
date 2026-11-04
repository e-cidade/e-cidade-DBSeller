<script setup>
import { ref } from 'vue';
import DropdownInstituicao from '@modules/Configuracao/Instituicao/Components/DropdownInstituicao.vue';

// props
const props = defineProps(['instit', 'exercicio']);

// emits
const emits = defineEmits(['filter'])

// data
const filteredExercicio = ref(props.exercicio);
const filteredInstit = ref(props.instit);

// methods
const selectInstit = (instit) => {
    filteredInstit.value = instit.codigo;
}

const filter = () => {
    const filters = {
        exercicio: filteredExercicio.value,
        instituicao: filteredInstit.value
    }

    emits('filter', filters)
}
</script>

<template>
    <Panel header="Filtro" class="w-6">
        <div class="grid">
            <!-- instit -->
            <div class="col-12 flex flex-column gap-2">
                <label for="instit">Instituição: </label>
                <DropdownInstituicao @change="selectInstit" :instit="instit"/>
            </div>

            <!-- Exercicio -->
            <div class="col-12 flex flex-column gap-2">
                <label for="exercicio">Exercicio: </label>
                <InputNumber v-model="filteredExercicio" :format="false" showButtons />
            </div>

            <div class="col-12">
                <Button label="Pesquisar"
                    icon="pi pi-search"
                    :loading="isFiltering"
                    @click="filter"
                />
            </div>
        </div>
    </Panel>
</template>
