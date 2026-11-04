<script setup>
import {onMounted, ref} from 'vue'
import ModalLoading from "../../../Components/ModalLoading";
import {useToast} from "primevue/usetoast";
import Toast from "primevue/toast";
import {FilterMatchMode, FilterOperator} from 'primevue/api';

const props = defineProps(['batch_id']);
const toast = useToast();
const falhas = ref([]);
const dialogVisible = ref(false);
const falhaSelecionada = ref({});
const falhasSelecionadas = ref([]);
const loading = ref(false);
const filters = ref();

const getFalahas = async () => {
  loading.value = true;
  try {
    const resp = await axios.get(
        `v4/api/recursos-humanos/pessoal/contra-cheques/falhas/${props.batch_id}`
    );
    falhas.value = resp.data.data;

  } catch (e) {
    toast.add({severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados', life: 5000})
  }
  loading.value = false;

}


const reprocessarFalha = async (falha) => {
  let jobs = [];
  jobs.push({
    queuedjob_id: falha.id,
    failedjob_id: falha.failed_job ? falha.failed_job.id : null
  });

  await reprocessar(jobs);

}

const reprocessarSelecionados = async () => {
  let jobs = [];
  falhasSelecionadas.value.forEach(el => {
    jobs.push({
      queuedjob_id: el.id,
      failedjob_id: el.failed_job ? el.failed_job.id : null
    })
  });
  await reprocessar(jobs);
}


const reprocessar = async (jobs) => {
  loading.value = true;
  try {
    const resp = await axios.post(
        `v4/api/recursos-humanos/pessoal/contra-cheques/reprocessar/falha`,
        {jobs}
    );

    toast.add({severity: 'success', summary: 'Sucesso', detail: resp.data.data.message, life: 5000})
  } catch (e) {
    toast.add({severity: 'warn', summary: 'Aviso', detail: 'Não foi possível recuperar os dados', life: 5000})
  }
  loading.value = false;

  await getFalahas();

}

const exibirFalha = (falha) => {
  console.log(falha);
  falhaSelecionada.value = falha;
  dialogVisible.value = true;
}

const quebrarLinhas = (exception) => {
  return exception.split("#").join("<br><br>#");
}


const initFilters = () => {
  filters.value = {
    'contracheque.matricula': {
      operator: FilterOperator.AND,
      constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]
    },
    'contracheque.mes': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    'contracheque.ano': {operator: FilterOperator.AND, constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]},
    'contracheque.instituicao': {
      operator: FilterOperator.AND,
      constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]
    },
    'contracheque.folha': {
      operator: FilterOperator.AND,
      constraints: [{value: null, matchMode: FilterMatchMode.EQUALS}]
    },
  };
};

initFilters();
onMounted(() => {
  getFalahas();
});

</script>

<template>
  <ModalLoading :is-loading="loading"/>
  <Toast/>

  <div class="container">
    <h1>Lote {{ props.batch_id }}</h1>
    <Button
        label="Reprocessar  selecionados"
        severity="success"
        v-if="falhasSelecionadas.length"
        @click="reprocessarSelecionados()"
    ></Button>
  </div>

  <div class="container">
    <DataTable :value="falhas"
               dataKey="id"
               v-model:selection="falhasSelecionadas"
               tableStyle="width:100%"
               paginator
               :rows="10"
               v-model:filters="filters"
               filterDisplay="menu"
    >
      <Column field="id" header="ID"  selectionMode="multiple">
      </Column>
      <Column field="contracheque.matricula" header="Matrícula" sortable>
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" class="p-column-filter"
                     placeholder="Pesquisa Por matricula"/>
        </template>
      </Column>
      <Column field="contracheque.mes" sortable header="Mês">
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Pesquisa Por Mês"/>
        </template>
      </Column>
      <Column field="contracheque.ano" sortable header="Ano">
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Pesquisa Por Ano"/>
        </template>
      </Column>
      <Column field="contracheque.instituicao" sortable header="Instituição">
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" class="p-column-filter"
                     placeholder="Pesquisa Por Instituição"/>
        </template>
      </Column>
      <Column field="contracheque.folha" sortable header="Folha">
        <template #filter="{ filterModel }">
          <InputText v-model="filterModel.value" type="text" class="p-column-filter" placeholder="Pesquisa Por Folha"/>
        </template>
      </Column>
      <Column field="failed_at" header="Falhado Em"></Column>
      <Column style="flex: 0 0 4rem">
        <template #body="{ data, falhaRow, index }">
          <Button type="button" icon="pi pi-eye" text size="small" @click="exibirFalha(data)"/>
        </template>
      </Column>
      <Column style="flex: 0 0 4rem">
        <template #body="{ data, falhaRow, index }">
          <Button type="button" icon="pi pi-undo" text size="small" @click="reprocessarFalha(data)"/>
        </template>
      </Column>
    </DataTable>
  </div>
  <Dialog v-model:visible="dialogVisible" header="Falha" :style="{ width: '75vw' }" maximizable modal
          :contentStyle="{ height: '300px' }">
    <div v-html="quebrarLinhas(falhaSelecionada.failed_job ? falhaSelecionada.failed_job.exception : falhaSelecionada.exception  )"></div>
    <template #footer>
      <Button label="Ok" icon="pi pi-check" @click="dialogVisible = false"/>
    </template>
  </Dialog>

</template>

<style scoped>

</style>