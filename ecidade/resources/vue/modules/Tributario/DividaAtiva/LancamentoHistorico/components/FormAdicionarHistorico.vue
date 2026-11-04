<script setup>
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import { FilterMatchMode } from 'primevue/api';

//props
const props = defineProps(['selectedDividas']);

//service
const toast = useToast();

//emits
const emit = defineEmits(['close', 'saved']);

//filters
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }});

//data
const historicoDescr = ref('');
const isLoadingSave = ref(false);
const openDialog = ref(false);
const dataAtual = new Date();

//methods
const save = async() => {
    isLoadingSave.value = true;
    const params = {
        dividas: props.selectedDividas.map(dado => dado.codDivida),
        historico: historicoDescr.value,
        data: getDataAtual()
    }

    const url = 'v4/api/tributario/divida-ativa/lancamento-historico/dividas';

    try {
        await axios.post(url, params);

        toast.add({severity: 'success', summary: 'Sucesso', detail: 'Histórico salvo com sucesso', life: 5000 });
        emit('close');
        emit('saved');

    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro ao salvar histórico', detail: e.message, life: 5000 });
    } finally {
        isLoadingSave.value = false;
    }
}

const getDataAtual = () => {
    const dia = String(dataAtual.getDate()).padStart(2, '0');
    const mes = String(dataAtual.getMonth() + 1).padStart(2, '0');
    const ano = dataAtual.getFullYear();
    return `${dia}/${mes}/${ano}`;
};

</script>

<template>
        <Dialog 
            v-model:visible="openDialog"
            position="top"
            :modal="true"
            :draggable="false"
            header="Dívidas selecionadas"
            style="width: 500px;"
        >

            <DataTable
                :value="selectedDividas"
                scrollable
                class="mt-3"
                style="max-height: 600px;"
                v-model:filters="filters"
                :globalFilterFields="['codDivida','numpre']"
            >

                <div class="flex justify-content-left mb-2">
                    <InputText 
                        v-model="filters['global'].value" 
                        style="border-color: #bdbdbd !important;"
                        placeholder="Pesquisar..." />
                </div>
                <Column field="codDivida" header="Código da Dívida" style="text-align: center"></Column>
                <Column field="numpre" header="Numpre" style="text-align: center"></Column>

                <template #empty>
                    Nenhuma dívida encontrada
                </template>

            </DataTable>
        </Dialog>

    <div class="py-4 px-3">
        <a @click="openDialog = true;"
            style="color: blue; text-decoration-line: underline; cursor: pointer;"
        >
            Dividas selecionadas: {{ selectedDividas.length }}
        </a>

        <div class="flex justify-content-left mt-4">
            <Textarea
                v-model="historicoDescr"
                placeholder="Digite novo histórico..."
                autoResize rows="8" cols="70"
                class="border border-gray-300 rounded-lg w-full"
            />
        </div>
    </div>

    <div class="flex justify-content-center gap-2 mt-1">
        <Button
            :loading="isLoadingSave"
            severity="success"
            icon="pi pi-save"
            :label="isLoadingSave ? 'Salvando' : 'Salvar'"
            @click="save"
        />
        <Button severity="danger" icon="pi pi-times" @click="emit('close')"/>
    </div>
</template>

<style scoped>
:deep(.p-column-header-content) {
    justify-content: center;
}
</style>