<template>
    <div>
        <Button label="Acordo: " link  @click="buscarContratos"  :loading="loading" />
        <Dialog v-model:visible="visible" header="Acordos" >
            <div>
                <DataTable
                    :value="tableData"
                    v-model:filters="filters"



                    showGridlines
                    paginator
                    :rows="25"
                    :rowsPerPageOptions="[10,15,25, 50]"
                    paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                    currentPageReportTemplate="{first} até {last} de {totalRecords}"

                    sortMode="multiple"

                    selectionMode="single"
                    @rowSelect="selecionarAcordo"

                    :tableStyle="{ 'height': '100%', 'width': '100%', 'table-layout': 'fixed','padding': '15px' }"
                >
                    <template #header>
                        <div class="flex justify-content-center">
                            <fieldset>
                                <legend>Filtros</legend>
                                <table class="form-container">
                                    <thead>
                                    <tr>
                                        <th scope="col">Acordo</th>
                                        <th scope="col">Número do Contrato</th>
                                        <th scope="col">Objeto</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <InputText class="p-inputtext-sm md:w-12rem" v-model="filters['ac16_sequencial'].value"/>
                                        </td>
                                        <td>
                                            <InputText class="p-inputtext-sm md:w-12rem" v-model="filters['ac16_numeroacordo'].value"/>
                                        </td>
                                        <td>
                                            <InputText class="p-inputtext-sm md:w-12rem" v-model="filters['ac16_resumoobjeto'].value"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <table class="form-container">
                                    <thead>
                                    <tr>
                                        <th scope="col">Origem</th>
                                        <th scope="col">Contratado</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <Dropdown class="p-inputtext-sm md:w-12rem"
                                                      :options="origens"
                                                      showClear
                                                      placeholder="Selecione a Origem"
                                                      v-model="filters['ac28_descricao'].value"

                                            />
                                        </td>
                                        <td>
                                            <InputText class="p-inputtext-sm md:w-12rem" v-model="filters['z01_nome'].value" style="lengt"/>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                           </fieldset>
                        </div>
                    </template>
                    <Column field="ac16_sequencial" sortable header="Acordo" style="width: 8%"></Column>
                    <Column field="ac16_numeroacordo" sortable header="Número do Acordo" style="width: 11%"></Column>
                    <Column field="ac16_resumoobjeto" sortable header="Resumo do Objeto" style="width: 15%">></Column>
                    <Column field="ac17_descricao" sortable header="Situação" style="width: 10%"></Column>
                    <Column field="z01_nome" sortable header="Nome/Razão Social" style="width: 15%"></Column>
                    <Column field="descrdepto" sortable header="Departamento" style="width: 10%"></Column>
                    <Column field="ac16_dataassinatura" sortable header="Data Assinatura" style="width: 8%"></Column>
                    <Column field="ac16_datainicio" sortable header="Data Inicio" style="width: 8%"></Column>
                    <Column field="ac16_datafim" sortable header="Data Fim" style="width: 7%"></Column>
                    <Column field="ac28_descricao" sortable header="Origem" style="width: 8%"></Column >
                </DataTable>
            </div>
        </Dialog>
    </div>
</template>
<script>
import {ref,watch} from 'vue';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Dropdown from 'primevue/dropdown';
import {FilterMatchMode} from 'primevue/api';

export default {
    emits: ['alteraValor'],
    name: 'PesquisarAcordo',
    components: {
        Button,
        InputNumber,
        DataTable,
        Dropdown
    },
    setup( props,{emit} ) {
        const loading = ref(false);
        const acordoNum = ref(null);
        const origens = ref(null);
        const visible = ref(false);
        const tableData = ref([]);
        const filters = ref({
            'ac16_resumoobjeto': {value: null, matchMode: FilterMatchMode.CONTAINS},
            'ac16_numeroacordo': {value: null, matchMode: FilterMatchMode.STARTS_WITH},
            'ac16_sequencial': {value: null, matchMode: FilterMatchMode.EQUALS},
            'ac28_descricao': {value: null, matchMode: FilterMatchMode.EQUALS},
            'z01_nome': {value: null, matchMode: FilterMatchMode.CONTAINS},
        });

        async function buscarContratos() {
            loading.value = true;
            const dados = await window.axios.post(`v4/api/patrimonial/contratos/consulta/buscar-todos-acordos`);
            await buscarOrigem();
            dados.data.data.forEach(obj => {
                if(obj.ac16_dataassinatura) {
                    obj.ac16_dataassinatura = dataFormatter(obj.ac16_dataassinatura)
                }
                obj.ac16_datainicio = dataFormatter(obj.ac16_datainicio)
                obj.ac16_datafim = dataFormatter(obj.ac16_datafim)
            })
            tableData.value = dados.data.data;
            visible.value = true;
            loading.value = false;
        }

        function selecionarAcordo(key) {
            acordoNum.value = key.data.ac16_sequencial;
            visible.value = false;
        }

        async function buscarOrigem() {
           const dados = await window.axios.get(`v4/api/patrimonial/contratos/consulta/buscar-descricao-origens`);
           origens.value = Object.values(dados.data.data);
        }

        function alterarValor(event) {
            acordoNum.value = event.value
        }

        watch (acordoNum, (novoValor) => {
            emit('alteraValor',novoValor);
        })

        function dataFormatter (data) {
            const [ano, mes, dia] = data.split('-')
            return `${dia}/${mes}/${ano}`;
        }


        return {
            visible,
            tableData,
            buscarContratos,
            selecionarAcordo,
            alterarValor,
            acordoNum,
            filters,
            origens,
            loading,
        };
    }
};
</script>

