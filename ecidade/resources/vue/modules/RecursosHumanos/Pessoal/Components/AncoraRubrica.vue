<script setup>
import { FilterMatchMode } from 'primevue/api';
import { ref } from 'vue';

const emit = defineEmits(['alteraRubrica', 'semRubrica'])

const filtersRubrica = ref(
    {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        rh27_rubric: { value: null, matchMode: FilterMatchMode.EQUALS },
        rh27_descr: { value: null, matchMode: FilterMatchMode.STARTS_WITH }
    }
);

const urlApi = 'v4/api/recursos-humanos/pessoal/rhrubricas';
const codigoRubrica = ref(null);
const descricaoRubrica = ref(null);
const showDialog = ref(false);
const loading = ref(false);
const rubricas = ref([]);
const updated = ref(false);
const refresh = ref(true);

const closeDialog = (e) => {
    showDialog.value = false;
};

const openDialog = () => {
    showDialog.value = true;
    if (refresh.value != true) {
        refresh.value = false;
    }
    inicializa();
};

const selectItemRubrica = (e) => {
    codigoRubrica.value = e.data.rh27_rubric;
    descricaoRubrica.value = e.data.rh27_descr;
    emit('alteraRubrica', codigoRubrica.value);
    closeDialog(false);
};

const inicializa = async () => {
    if (updated.value == false) {
        rubricas.value = [];
        loading.value = true;
        try {
            let resp = await window.axios.post(
                urlApi + '/list', {}
            );
            rubricas.value = resp.data.data;
        } catch (e) {
            rubricas.value = [];
        }
        if (refresh.value == false) {
            updated.value = true;
        }
        loading.value = false;
    }
};
const busca = async () => {
    try {
        if (codigoRubrica.value != null) {
            const resp = await window.axios.post(
                urlApi + '/list', {
                rh27_rubric: codigoRubrica.value
            }
            );
            if (resp.data.data[0] === undefined || resp.data.data[0].rh27_rubric == null) {
                codigoRubrica.value = null;
                descricaoRubrica.value = "Rubrica não encontrada."
                emit('semRubrica', true);
            } else {
                emit('alteraRubrica', codigoRubrica.value);
                descricaoRubrica.value = resp.data.data[0].rh27_descr;
            }
        }
    } catch (e) {
        rubricas.value = [];
    }
}
defineExpose({
    codigoRubrica
})
</script>
<template>
    <div class="flex flex-wrap w-10">
        <div class="p-inputgroup flex-1">
            <span class="p-float-label">
                <InputText type="text" @change="busca" placeholder="Rubrica" v-model="codigoRubrica"
                    style="max-width:100px; margin-left: -3px; overflow: hidden; margin-right: 2px;"
                    aria-readonly="bloqueiaBusca" inputId="codigoRubrica" :useGrouping="false" />
                <InputText type="text" placeholder="Nome" v-model="descricaoRubrica"
                    id="rubrica" readonly />
                <label for="dependente">Rubrica:</label>
                <span class="p-inputgroup-addon" @click="openDialog()">
                    <i class="pi pi-search"></i>
                </span>
            </span>
        </div>
    </div>
    <Dialog :header="`Consulta de Rubricas`" :maximizable="true" :modal="true" :style="{ width: '1000px' }"
        :visible="showDialog" :closable="true" min-height="400px" @update:visible="closeDialog">
        <div class="mt-6">
            <div class="card">
                <DataTable :value="rubricas" showGridlines responsiveLayout="scroll" :loading="loading"
                    @rowSelect="selectItemRubrica" selectionMode="single" :scrollable="true" scrollHeight="flex"
                    :paginator="true" :rowsPerPageOptions="[10, 20, 50, 100, 1000, 10000]" :rows="10"
                    v-model:filters="filtersRubrica" :globalFilterFields="['rh27_rubric', 'rh27_descr']">
                    <template #header>
                        <div class="flex justify-content-between">
                            <h2 class="m-0">Rubricas</h2>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filtersRubrica['global'].value" placeholder="Procurar" />
                            </span>
                        </div>
                    </template>
                    <template #empty>Rubrica não encontrada.</template>
                    <template #loading> Buscando Informações. Aguarde. </template>
                    <Column sortable field="rh27_rubric" class="text-center" header="Rubrica" />
                    <Column sortable field="rh27_descr" class="text-center" header="Nome" />
                </DataTable>

            </div>
        </div>
    </Dialog>
</template>
<style scoped>
.p-inputgroup-addon {
    cursor: pointer !important;
}
</style>