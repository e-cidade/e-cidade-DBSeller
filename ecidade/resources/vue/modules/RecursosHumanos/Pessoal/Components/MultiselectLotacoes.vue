<script setup>
import { useToast } from "primevue/usetoast";

import { ref, onMounted } from 'vue'
import { FilterMatchMode } from 'primevue/api';
import Checkbox from 'primevue/checkbox';

// const apiUrl = 'v4/api/recursos-humanos/pessoal/rhlotacoes/list'
const apiUrl = 'v4/api/recursos-humanos/pessoal/lotacao/'

const loading = ref(false);
const toast = useToast();
const lotacoes = ref([]);
const lotacoesMarcadas = ref(false)
const todasLotacoes = ref([])

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    codigo: { value: null, matchMode: FilterMatchMode.CONTAINS },
    descricao: { value: null, matchMode: FilterMatchMode.CONTAINS },
    estrutural: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

/**
 * Methods
 */
const getFuncoes = async () => {
    const action = apiUrl + 'list-active';
    loading.value = true;
    try {
        const response = await window.axios.get(action);
        lotacoes.value = response.data
        
    } catch (error) {
        console.log(error);
        // toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados.', life: 5000 });
    }

    loading.value = false;
}

const alteraCheckBoxFuncoes = (elemento, campo) => {
    elemento[campo] = !elemento[campo];
    const index = todasLotacoes.value.indexOf(elemento.estrutural)
    if (index > -1) {
        todasLotacoes.value.splice(index, 1);
    } else {
        todasLotacoes.value.push(elemento.estrutural);
    }
}

const selecionaTodos = () => {
    let estado = !lotacoesMarcadas.value
    lotacoes.value.data.forEach((el, i) => {
        el.selecionado = estado
        if (!estado) {
            while (todasLotacoes.value.length) {
                todasLotacoes.value.pop();
            }
        } else {
            todasLotacoes.value.push(el.estrutural);
        }
    })
}

/**
 * Hooksl
*/
onMounted(async () => {
    await getFuncoes();
});


defineExpose({
    todasLotacoes
});
</script>


<template>
    <DataTable v-model:filters="filters" :value="lotacoes.data" showGridlines style="width: 100%" dataKey="id"
        id="datatableFuncoes" :scrollable="true" scrollHeight="flex" :paginator="true"
        :rowsPerPageOptions="[10, 20, 50, 100]" :rows="5" :globalFilterFields="['codigo', 'estrutural', 'descricao']">
        <template #header>
            <div class="flex justify-content-between">
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="filters['global'].value" placeholder="Procurar" />
                </span>
                <span class="p-input-icon-right">
                    Geral:
                    <Checkbox @click="selecionaTodos()" v-model="lotacoesMarcadas" class="p-invalid" binary />
                </span>
            </div>
        </template>
        <Column field="codigo" class="text-center" hidden header="Código">
        </Column>
        <Column field="selecionado" class="text-center" header="Selecionados" style="width: 30px;">
            <template #body="{ data, field }">
                <i @click="alteraCheckBoxFuncoes(data, field)" class="pi"
                    :class="{ 'pi-check-circle text-green-500': data.selecionado, 'pi-times-circle text-red-400': !data.selecionado }"></i>
            </template>
        </Column>
        <Column field="estrutural" class="text-center" header="Estrutural" />
        <Column field="descricao" class="text-center" header="Descrição" />
    </DataTable>
</template>
