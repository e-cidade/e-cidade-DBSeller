<script setup>
import { ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import DialogConsultaCgm from '@modules/Patrimonial/Protocolo/Components/DialogConsultaCgm.vue';
import DialogConsultaMatriculaImovel from '@modules/Tributario/Arrecadacao/components/DialogConsultaMatriculaImovel.vue';
import DialogConsultaInscricaoMunicipal from '@modules/Tributario/Arrecadacao/components/DialogConsultaInscricaoMunicipal.vue';
import InputSearchMatricula from '@modules/Tributario/Cadastro/components/InputSearchMatricula.vue';
import InputSearchCgm from '@modules/Patrimonial/Protocolo/Components/InputSearchCgm.vue';
import InputSearchInscricao from '@modules/Tributario/Issqn/Components/InputSearchInscricao.vue';

// props
const props = defineProps(['isFiltering']);

// emits
const emits = defineEmits(['filter']);

//services
const toast = useToast();

// data
const filters = ref({numcgm: null, matricula: null, inscricao: null, codDivida: null});
const cgmLabel = ref('');
const matriculaLabel = ref('');
const inscricaLabel = ref('');

// methods
const filter = () => {
    if (!validate()) {
        toast.add({severity: 'warn', summary: 'Informe um filtro para pesquisar', life: 5000 });
        return;
    }
    emits('filter', filters.value);
}

const validate = () => {
    const filtersArray = [filters.value.numcgm, filters.value.matricula, filters.value.inscricao, filters.value.codDivida];
    return filtersArray.some(dado => dado !== '' && dado !== null);
}

const clearFilter = () => {
    filters.value = {numcgm: null, matricula: null, inscricao: null, codDivida: null};
    cgmLabel.value = '';
    matriculaLabel.value = '';
    inscricaLabel.value = '';
}

const selectCgm = (data) => {
    cgmLabel.value = data.nome;
    filters.value.numcgm = data.numcgm;
}

const selectMatricula = (data) => {
    matriculaLabel.value = data.nome;
    filters.value.matricula = data.matricula;
}

const selectInscricao = (data) => {
    inscricaLabel.value = data.nome;
    filters.value.inscricao = data.inscricao;
}
</script>

<template>
    <Panel header="Filtro" class="w-6 mx-auto">
        <div class="grid">
            <!-- cgm -->
            <div class="col-12 flex flex-column gap-2 align-items-start">
                <Button label="CGM" @click="$refs.dialogConsultaCgm.toggleDialog()" link/>
                <InputSearchCgm v-model:id="filters.numcgm" v-model:description="cgmLabel" />
                <DialogConsultaCgm ref="dialogConsultaCgm" @selectRow="selectCgm"/>
            </div>

            <!-- matricula -->
            <div class="col-12 flex flex-column gap-2 align-items-start">
                <Button label="Matricula" @click="$refs.dialogConsultaMatricula.toggleDialog()" link/>
                <InputSearchMatricula v-model:id="filters.matricula" v-model:description="matriculaLabel" />
                <DialogConsultaMatriculaImovel ref="dialogConsultaMatricula" @selectRow="selectMatricula"/>
            </div>

            <!-- inscricao -->
            <div class="col-12 flex flex-column gap-2 align-items-start">
                <Button label="Inscricao" @click="$refs.dialogConsultaInscricao.toggleDialog()" link/>
                <InputSearchInscricao v-model:id="filters.inscricao" v-model:description="inscricaLabel" />
                <DialogConsultaInscricaoMunicipal ref="dialogConsultaInscricao" @selectRow="selectInscricao"/>
            </div>

            <div class="col-12 flex flex-column gap-2">
                <label for="codDivida">Código da Divida </label>
                <InputText v-model="filters.codDivida"/>
            </div>

            <div class="col-12">
                <Button
                    label="Pesquisar"
                    icon="pi pi-search"
                    :loading="isFiltering"
                    @click="filter"
                    class="mr-2"
                />
                <Button
                    outlined
                    label="Limpar"
                    icon="pi pi-trash"
                    @click="clearFilter"
                />
            </div>
        </div>
    </Panel>
</template>

<style scoped>
    .p-button-link {
        padding: 0px;
        background-color: transparent;
        color: #4a789c;
    }
</style>
