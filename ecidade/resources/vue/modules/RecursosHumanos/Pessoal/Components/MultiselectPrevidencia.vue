<script setup>
import { useToast } from "primevue/usetoast";

import { ref, onMounted } from 'vue'
import { FilterMatchMode } from 'primevue/api';
import Checkbox from 'primevue/checkbox';


const apiUrl = 'v4/api/recursos-humanos/pessoal/tabelaprevidencia/'
const loading = ref(false);
const toast = useToast();
const selectedPrevidencia = ref(false);
const previdencia = ref([]);
const previdenciaSelecionada = ref([])

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
    const action = apiUrl + 'list';
    loading.value = true;
    try {
        const response = await window.axios.get(action);
        previdencia.value = response.data

    } catch (error) {
        console.log(error);
        // toast.add({ severity: 'warn', summary: 'Aviso', detail: 'NÃ£o foi possÃ­vel recuperar os dados.', life: 5000 });
    }

    loading.value = false;
}


const alteraCheckBoxPrevidencia = (elemento, campo) => {
    elemento[campo] = !elemento[campo];
    const index = previdenciaSelecionada.value.indexOf(elemento.codigo)
    if (index > -1) {
        previdenciaSelecionada.value.splice(index, 1);
    } else {
        previdenciaSelecionada.value.push(elemento.codigo);
    }
}

const selecionaTodos = () => {
    let estado = !selectedPrevidencia.value
    previdencia.value.data.forEach((el, index) => {
        el.selecionado = estado
        if (!estado) {
            while (previdenciaSelecionada.value.length) {
                previdenciaSelecionada.value.pop();
            }
        } else {
            previdenciaSelecionada.value.push(el.codigo);
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
    previdenciaSelecionada
});
</script>


<template>
    <DataTable v-model:filters="filters" :value="previdencia.data" showGridlines style="width: 100%" dataKey="id"
        id="datatablePrevidencia" :scrollable="true" scrollHeight="flex" :paginator="true"
        :rowsPerPageOptions="[10, 20, 50, 100]" :rows="5" :globalFilterFields="['descricao']">
        <template #header>
            <div class="flex justify-content-between">
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="filters['global'].value" placeholder="Procurar" />
                </span>
                <span class="p-input-icon-right">
                    Todas:
                    <Checkbox @click="selecionaTodos()" v-model="selectedPrevidencia" class="p-invalid" binary />
                </span>
            </div>
        </template>
        <Column field="codigo" class="text-center" hidden header="Código">
        </Column>
        <Column field="selecionado" class="text-center" header="Selecionados" style="width: 30px;">
            <template #body="{ data, field }">
                <i @click="alteraCheckBoxPrevidencia(data, field)" disabled class="pi"
                    :class="{ 'pi-check-circle text-green-500': data.selecionado, 'pi-times-circle text-red-400': !data.selecionado }"></i>
            </template>
        </Column>
        <Column field="descricao" class="text-center" header="Descrição" />
    </DataTable>
</template>
