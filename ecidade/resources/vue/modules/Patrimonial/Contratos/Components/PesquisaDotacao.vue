<template>
  <ModalLoading :isLoading="loadingSaldo" :message="'Calculando saldo da dotação.'"/>

  <Button label="Dotação: " @click="buscaDotacoes" link :loading="loading"/>
  <Dialog v-model:visible="visible" header="Acordos">
    <DataTable
        :value="dotacoes"
        v-model:filters="filters"

        showGridlines
        paginator
        :rows="25"
        :rowsPerPageOptions="[10,15,25, 50]"
        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
        currentPageReportTemplate="{first} até {last} de {totalRecords}"

        selectionMode="single"
        @rowSelect="selecionarDotacao"

        :tableStyle="{ 'height': '100%', 'width': '100%', 'table-layout': 'fixed','padding': '15px' }">
      <template #header>
        <div class="flex justify-content-center">
          <fieldset>
            <legend>Filtros</legend>
            <table class="form-container">
              <thead>
              <tr>
                <th scope="col">Reduzido</th>
                <th scope="col">Programa</th>
                <th scope="col">Secretaria</th>
                <th scope="col">Departamento</th>
              </tr>
              </thead>
              <tbody>
              <tr>
                <td>
                  <InputText class="p-inputtext-sm md:w-12rem" v-model="filters['o58_coddot'].value"/>
                </td>
                <td>
                  <Dropdown :options="programas"
                            optionLabel="descricao"
                            optionValue="o54_programa"
                            showClear
                            filter
                            v-model="filters['o58_programa'].value"
                            class="p-inputtext-sm md:w-12rem"
                  />
                </td>
                <td>
                  <Dropdown :options="orgaos"
                            optionLabel="o40_descr"
                            optionValue="o40_orgao"
                            showClear
                            filter
                            v-model="filters['o58_orgao'].value"
                            class="p-inputtext-sm md:w-12rem"
                  />
                </td>
                <td>
                    <Dropdown :options="departamentos"
                              optionLabel="descricao"
                              optionValue="db01_unidade"
                              showClear
                              filter
                              v-model="filters['o58_unidade'].value"
                              class="p-inputtext-sm md:w-12rem"
                    />
                </td>
              </tr>
              </tbody>
            </table>
          </fieldset>
        </div>
      </template>
      <Column field="o50_estrutdespesa" sortable style="width: 35%" header="Estrutural Despesa"/>
      <Column field="o58_coddot" sortable header="Reduzido"/>
      <Column field="o41_descr" sortable header="Descrição Unidade"/>
      <Column field="o55_descr" sortable header="Descrição"/>
        <Column field="o55_finali" sortable header="Finalidade">
            <template #body="{data,field}">
                <span v-tooltip="{value:data[field],class:'tooltip-class'}">{{ data[field].substr(0, 50) }}</span>
            </template>
        </Column>
      <Column field="o56_descr" sortable="" header="Descrição "/>


    </DataTable>
  </Dialog>
</template>
<script>
import ModalLoading from '../../../Components/ModalLoading'
import Button from 'primevue/button'
import InputNumber from 'primevue/inputnumber'
import Dropdown from 'primevue/dropdown';
import Tooltip from 'primevue/tooltip';
import {ref, computed} from "vue";
import {FilterMatchMode} from "primevue/api";

export default {
  emits: ['enviaDotacao'],
  directives: {
      'tooltip': Tooltip
  },
  components: {
    Button,
    InputNumber,
    ModalLoading,
    Dropdown
  },
  props: {
    elemento: {
      type: Number,
      required: true
    }
  },
  setup(props, {emit}) {
    const loadingSaldo = ref(false);
    const valor = ref(null);
    const filters = ref({
      'o58_coddot': {value: null, matchMode: FilterMatchMode.EQUALS},
      'o58_programa': {value:null, matchMode: FilterMatchMode.EQUALS},
      'o58_orgao': {value:null, matchMode: FilterMatchMode.EQUALS},
      'o58_unidade': {value:null, matchMode: FilterMatchMode.EQUALS}
    })
    const visible = ref(false);
    const dotacoes = ref(null);
    const dotacao = ref(null);
    const anoDotacao = ref(null);
    const loading = ref(false);
    const programas = ref(null);
    const orgaos = ref(null);
    const departamentos = ref(null);

    const elemento = computed(() => {
      return props.elemento
    })

    async function buscaDotacoes() {
      loading.value = true;
      const dados = await window.axios.get('v4/api/financeiro/orcamento/buscar-dotacoes/' + elemento.value);
      const dadosFiltros = await window.axios.get('v4/api/financeiro/orcamento/buscar-filtros-dotacoes');
      programas.value = dadosFiltros.data.data.programas;
      orgaos.value = dadosFiltros.data.data.orgaos;
      departamentos.value = dadosFiltros.data.data.departamentos;
      filters.value['o58_orgao'].value = dadosFiltros.data.data.orgaoSelecionado.o40_orgao

      dotacoes.value = dados.data.data;
      visible.value = true;
      loading.value = false;
    }

    async function selecionarDotacao(key) {
      dotacao.value = key.data.o58_coddot;
      anoDotacao.value = key.data.o58_anousu;
      visible.value = false;
      loadingSaldo.value = true;
      const dados = await window.axios.get('v4/api/financeiro/orcamento/buscar-saldo-dotacao/' + dotacao.value);
      loadingSaldo.value = false;
      valor.value = dados.data.data
      emit('enviaDotacao', {
        'codigo': dotacao.value,
        'saldoDotacao': valor.value,
        'anoDotacao': anoDotacao.value
      })
    }

    return {
      valor,
      visible,
      dotacoes,
      buscaDotacoes,
      loading,
      selecionarDotacao,
      dotacao,
      anoDotacao,
      elemento,
      filters,
      loadingSaldo,
      programas,
      orgaos,
      departamentos
    }
  }
}
</script>
<style>
.p-tooltip > *, .p-tooltip {
    display: block!important;
    max-width: 50em;
}
.tooltip-class {
    border: 1px solid rgb(255, 221, 0);
    background-color: #FFFFCC;
}
</style>
