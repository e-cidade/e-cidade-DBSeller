<script setup>
import { useToast } from "primevue/usetoast";

import { ref, onMounted } from 'vue'
import { FilterMatchMode } from 'primevue/api';
import Checkbox from 'primevue/checkbox';

const loading = ref(false);
const toast = useToast();
const folhas = ref([]);
const todasFolhas = ref(false)
const folhasSelecionadas = ref([])

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
    loading.value = true;
    try {
       
        folhas.value = {'data':[
            {'codigo': 'sa', 'descricao':"SALÁRIO"},
            {'codigo': 'co', 'descricao':"COMPLEMENTAR"},
            {'codigo': 'su', 'descricao':"SUPLEMENTAR"},
            {'codigo': 're', 'descricao':"RESCISÃO"},
            {'codigo': 'd13', 'descricao':"13o. SALÁRIO"},
            {'codigo': 'fe', 'descricao':"FERIAS"}
        ]}
        
    } catch (error) {
        console.log(error);
        // toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados.', life: 5000 });
    }

    loading.value = false;
}

const alteraCheckBoxFuncoes = (elemento, campo) => {
    
    const index = folhasSelecionadas.value.indexOf(elemento.codigo)
    
    if (index > -1) {
        folhasSelecionadas.value.splice(index, 1);
    } else {
        folhasSelecionadas.value.push(elemento.codigo);
    }
    elemento[campo] = !elemento[campo];
}

const selecionaTodos = () => {
    let estado = !todasFolhas.value
    folhas.value.data.forEach((el, i) => {
        el.selecionado = estado
        if (!estado) {
            while (folhasSelecionadas.value.length) {
                folhasSelecionadas.value.pop();
            }
        } else {
            folhasSelecionadas.value.push(el.codigo);
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
    folhasSelecionadas
});
</script>


<template>
    <DataTable v-model:filters="filters" :value="folhas.data" showGridlines style="width: 100%" dataKey="id"
        id="datatableFuncoes" :scrollable="true" scrollHeight="flex" :paginator="true"
        :rowsPerPageOptions="[10, 20, 50, 100]" :rows="5" :globalFilterFields="['codigo', 'descricao']">
        <template #header>
            <div class="flex justify-content-between">
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="filters['global'].value" placeholder="Procurar" />
                </span>
                <span class="p-input-icon-right">
                    Geral:
                    <Checkbox @click="selecionaTodos()" v-model="todasFolhas" class="p-invalid" binary />
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
        <Column field="descricao" class="text-center" header="Descrição" />
    </DataTable>
</template>
