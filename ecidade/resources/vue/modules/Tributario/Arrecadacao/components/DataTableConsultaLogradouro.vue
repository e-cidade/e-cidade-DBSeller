<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from 'primevue/api';

//services
const toast = useToast();

//emits
const emits = defineEmits(['rowSelected', 'close']);

//data
const logradouros = ref();
const filters = ref({
    nome: '',
    sequencial: '',
    cep: ''
});
const isLoading = ref(false);
const filterConteudo = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});
const selectedFilters = ref(['sigla', 'nome', 'sequencial', 'tipo', 'cep']);
const options = [
    {column: "Tipo", value: "sigla"},
    {column: "Nome", value: "nome"},
    {column: "Código", value: "sequencial"},
    {column: "Rua", value: "tipo"},
    {column: "Cep", value: "cep"}
];
         
//methods
const pesquisarLogradouro = async() => {
    isLoading.value = true;
    const urlLogradouros = 'v4/api/tributario/cadastro/buscar-logradouro'
    try {
        const response = await axios.get(urlLogradouros, {
            params: filters.value
        });
        const { data: resp } = response;
        logradouros.value = resp.data ? resp.data : {data: null};
    } catch (e) {
        toast.add({severity: 'error', summary: 'Erro ao buscar logradouros.', detail: e.message, life: 5000 })
    } finally {
        isLoading.value = false;
    }
}

const limparCampos = () => {
    filters.value.nome = "";
    filters.value.sequencial = "";
    filters.value.cep = "";
}

const onRowSelect = (event) => {
    emits('rowSelected', event.data.sequencial);
}

onMounted(() => {
    pesquisarLogradouro();
});

</script>

<template>
   <section style="width: 100%" class="m-auto">
       <DataTable
            :value="logradouros"
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
                       <label for="nome">Nome:</label>
                       <InputText
                           :useGrouping="false"
                           placeholder=""
                           class="padding-custom p-inputtext-sm shadow-4"
                           v-model="filters.nome"
                       />
                   </span>

                   <span class="p-input-icon m-2 flex flex-column">
                       <label for="sequencial">Código Sequencial:</label>
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
                       <label for="sequencial">CEP:</label>
                       <InputText
                           :useGrouping="false"
                           placeholder=""
                           class="padding-custom p-inputtext-sm shadow-4"
                           v-model="filters.cep"
                           type="number"
                           min="1"
                       />
                   </span>
               </form>

               <div class="m-auto flex justify-content-center flex-wrap">
                   <Button @click="pesquisarLogradouro" class="m-2 p-button-success shadow-4">
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

           <Column field="sigla" header="Tipo" style="width: 13%;" sortable/>
           <Column field="nome" header="Nome" style="width: 48%;" sortable/>
           <Column field="sequencial" header="Código" style="width: 13%;" sortable/>
           <Column field="tipo" header="Rua" style="width: 13%;" sortable/>
           <Column field="cep" header="CEP" style="width: 13%;" sortable/>
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