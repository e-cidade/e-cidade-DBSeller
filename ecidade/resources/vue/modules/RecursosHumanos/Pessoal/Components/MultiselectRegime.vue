<script setup>
import { useToast } from "primevue/usetoast";

import { ref, onMounted } from 'vue'
import { FilterMatchMode } from 'primevue/api';
import Checkbox from 'primevue/checkbox';


const apiUrl = 'v4/api/recursos-humanos/pessoal/rhfuncao/'
const loading = ref(false);
const toast = useToast();
const selectedRegime = ref(false);
const previdenciaRegime = ref([])
const regime = ref([]);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    codigo: { value: null, matchMode: FilterMatchMode.CONTAINS },
    descricao: { value: null, matchMode: FilterMatchMode.CONTAINS },
    estrutural: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
/**
 * Methods
 */
const getTabelaPrevidencia = async () => {
    const action = apiUrl + 'list-regime';
    loading.value = true;
    try {
        const response = await window.axios.get(action);
        regime.value = response.data
    } catch (error) {
        console.log(error);
        // toast.add({ severity: 'warn', summary: 'Aviso', detail: 'NÃ£o foi possÃ­vel recuperar os dados.', life: 5000 });
    }

    loading.value = false;
}


const alteraCheckBoxFuncao = (elemento, campo) => {
    elemento[campo] = !elemento[campo];
    const index = previdenciaRegime.value.indexOf(elemento.rh30_codreg)
    if (index > -1) {
        previdenciaRegime.value.splice(index, 1);
    } else {
        previdenciaRegime.value.push(elemento.rh30_codreg);
    }
}

const selecionaTodos = () => {
    let estado = !selectedRegime.value
    regime.value.data.forEach((el, index) => {
        el.selecionado = estado
        if (!estado) {
            while (previdenciaRegime.value.length) {
                previdenciaRegime.value.pop();
            }
        } else {
            previdenciaRegime.value.push(el.rh30_codreg);
        }
    })
}

/**
 * Hooksl
*/
onMounted(async () => {
    await getTabelaPrevidencia();
});


defineExpose({
    previdenciaRegime
});
</script>


<template>
    <DataTable v-model:filters="filters" :value="regime.data" showGridlines style="width: 100%" dataKey="id"
        id="datatableLotacao" :scrollable="true" scrollHeight="flex" :paginator="true"
        :rowsPerPageOptions="[10, 20, 50, 100]" :rows="5" :globalFilterFields="['rh30_descr']">
        <template #header>
            <div class="flex justify-content-between">
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="filters['global'].value" placeholder="Procurar" />
                </span>
                <span class="p-input-icon-right">
                    Selecionar Todos:
                    <Checkbox @click="selecionaTodos()" v-model="selectedRegime" class="p-invalid" binary />
                </span>
            </div>
        </template>
        <Column field="codigo" class="text-center" hidden header="Código">
        </Column>
        <Column field="selecionado" class="text-center" header="Selecionados" style="width: 30px;">
            <template #body="{ data, field }">
                <i @click="alteraCheckBoxFuncao(data, field)" class="pi"
                    :class="{ 'pi-check-circle text-green-500': data.selecionado, 'pi-times-circle text-red-400': !data.selecionado }"></i>
            </template>
        </Column>
        <Column field="rh30_descr" class="items-center" header="Descrição" />
    </DataTable>
</template>
