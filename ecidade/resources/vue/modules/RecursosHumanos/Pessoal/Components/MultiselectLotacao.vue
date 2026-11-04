<script setup>
    import { useToast } from "primevue/usetoast";

    import { ref, onMounted  } from 'vue'
    import { FilterMatchMode } from 'primevue/api';
    import Checkbox from 'primevue/checkbox';


    const apiurl = 'v4/api/recursos-humanos/pessoal/lotacao/';
    const loading = ref(false);
    const toast = useToast();
    const lotacoes = ref([]);
    const todasLotacoes = ref(false)

    const filters = ref({
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        codigo: { value: null, matchMode: FilterMatchMode.CONTAINS },
        descricao: { value: null, matchMode: FilterMatchMode.CONTAINS },
        estrutural: { value: null, matchMode: FilterMatchMode.CONTAINS }
    });
    /**
     * Methods
     */
    const getLotacoes = async () => {
        const action = apiurl + 'list-active';
        loading.value = true;
        try {
            const response = await axios.get(action);
            lotacoes.value = response.data
        } catch (error) {
            console.log(error);
            // toast.add({ severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados.', life: 5000 });
        }

        loading.value = false;
    }

    const alteraCheckBoxLotacao = (elemento, campo) => {
        elemento[campo] = !elemento[campo];
    }

    const selecionaTodos = () => {
        let estado = todasLotacoes.value === false ? true : false
        lotacoes.value.data.forEach((el, index)=>{
            el.selecionado = estado
        })
    }

    /**
     * Hooksl
    */
    onMounted(() => {
        getLotacoes();
    });


    defineExpose({
        lotacoes,
    });
</script>


<template>
  <DataTable 
        v-model:filters="filters"
        :value="lotacoes.data"
        showGridlines 
        style="width: 100%"
        dataKey="id" 
        id="datatableLotacao"
        :scrollable="true"
        scrollHeight="flex"
        :paginator="true"
        :rowsPerPageOptions="[10, 20, 50, 100]"
        :rows="10"
        :globalFilterFields="['codigo', 'estrutural', 'descricao']"
    >
        <template #header>
            <div class="flex justify-content-between">
                <span class="p-input-icon-left">
                    <i class="pi pi-search" />
                    <InputText v-model="filters['global'].value" placeholder="Procurar" />
                </span>
                <span class="p-input-icon-right">
                    Selecionar Todos:
                    <Checkbox @click="selecionaTodos()" v-model="todasLotacoes" class="p-invalid" binary />
                </span>
            </div>
        </template>
        <Column field="codigo" class="text-center" hidden header="Código">
        </Column>
        <Column field="selecionado" class="text-center" header="Selecionados" style="width: 30px;">
            <template #body="{ data, field }">
                <i @click="alteraCheckBoxLotacao(data, field)"  class="pi" :class="{ 'pi-check-circle text-green-500': data.selecionado, 'pi-times-circle text-red-400': !data.selecionado }"></i>
            </template>
        </Column>
        <Column field="estrutural" class="text-center" header="Estrutural"/>
        <Column field="descricao" class="text-center" header="Descrição"/>
    </DataTable>
</template>

