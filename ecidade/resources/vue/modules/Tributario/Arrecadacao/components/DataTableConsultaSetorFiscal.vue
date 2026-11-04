<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from 'primevue/api';

//services
const toast = useToast();

//emits
const emits = defineEmits(['rowSelected', 'close']);

//data
const setorFiscal = ref();
const filters = ref({
    descricao: '',
    sequencial: ''
});
const isLoading = ref(false);
const filterConteudo = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const selectedFilters = ref(['sequencial', 'descricao']);
const options = [
    {column: "Código", value: "sequencial"},
    {column: "Descrição", value: "descricao"}
];
         
//methods
const pesquisarSetorFiscal = async() => {
    isLoading.value = true;
    const urlSetorFiscal = 'v4/api/tributario/issqn/buscar-setor-fiscal'
    try {
        const response = await axios.get(urlSetorFiscal, {
            params: filters.value
        });
        const { data: resp } = response;
        setorFiscal.value = resp.data ? resp.data : {data: null};
    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro ao buscar setores fiscais.', detail: e.message, life: 5000 })
    } finally {
        isLoading.value = false;
    }
}

const limparCampos = () => {
    filters.value.descricao = "";
    filters.value.sequencial = "";
}

const onRowSelect = (event) => {
    emits('rowSelected', event.data.sequencial);
}

onMounted(() => {
    pesquisarSetorFiscal();
});

</script>

<template>
   <section style="width: 100%" class="m-auto">
       <DataTable
            :value="setorFiscal"
            paginator
            :rows="10"
            :loading="isLoading"
            v-model:filters="filterConteudo"
            :globalFilterFields="selectedFilters"
            stripedRows
            selectionMode="single"
            dataKey="sequencial"
            @row-select="onRowSelect"
        >
           <template #empty> Nenhum resultado foi encontrado </template>
           <template #header>
               <form class="m-auto flex flex-col justify-content-center items-center">
                   <span class="p-input-icon m-2 flex flex-column">
                       <label for="nome">Código do Setor Fiscal:</label>
                       <InputText
                           :useGrouping="false"
                           placeholder=""
                           class="padding-custom p-inputtext-sm shadow-4"
                           v-model="filters.sequencial"
                           type="number"
                           min="1"
                       />
                   </span>

                   <span class="p-input-icon m-2 flex flex-column">
                       <label for="sequencial">Descrição do Setor Fiscal:</label>
                       <InputText
                           :useGrouping="false"
                           placeholder=""
                           class="padding-custom p-inputtext-sm shadow-4"
                           v-model="filters.descricao"
                       />
                   </span>
               </form>

               <div class="m-auto flex justify-content-center flex-wrap">
                   <Button @click="pesquisarSetorFiscal" class="m-2 p-button-success shadow-4">
                       <i class="pi pi-search"/>
                   </Button>

                   <Button @click="limparCampos" class="m-2 shadow-4">
                       <i class="pi pi-undo"/>
                   </Button>

                   <Button @click="emits('close')" class="m-2 p-button-danger shadow-4">
                       <i class="pi pi-times"/>
                   </Button>
               </div>
           </template>

           <Column field="sequencial" header="Código" style="width: 30%;" sortable/>
           <Column field="descricao" header="Descrição" style="width: 40%;" sortable/>
           <Column field="valor" header="Valor" style="width: 30%;" sortable>
                <template #body="slotProps">
                    R$ {{parseFloat(slotProps.data.valor).toFixed(2)}}
                </template>
           </Column>
       </DataTable>

        <div class="m-auto flex flex-col inputPesquisa">
            <InputGroup class="mt-2">
                <MultiSelect
                    v-model="selectedFilters"
                    :options="options"
                    optionLabel="column"
                    optionValue="value"
                    placeholder="Colunas"
                    style="max-width: 7rem; color: white; background-color: #4a789c;"
                />
                <InputText
                    v-model="filterConteudo['global'].value"
                    placeholder="Pesquisar..."
                    style="width: 250px;"
                />
            </InputGroup>
        </div>
   </section>
</template>

<style scoped>
:deep(.inputPesquisa .p-multiselect .p-multiselect-trigger) {
    color: white;
}

:deep(.inputPesquisa .p-placeholder) {
    color: white;
}

:deep(.inputPesquisa .p-multiselect) {
    border-color: #4a789c;
}
</style>