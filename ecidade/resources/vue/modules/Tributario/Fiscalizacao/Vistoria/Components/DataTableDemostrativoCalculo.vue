<script setup>
import { ref } from 'vue';
import { formatCurrency, formateDate } from '@utils/Strings';

// props
const props = defineProps(['calculos', 'isLoading']);

// data
const visible = ref(false);
const inDetail = ref([]);

// methods
const showRowDetail = (data) => {
    inDetail.value = data;
    visible.value = true;
};
</script>

<template>
    <Card>
    <template #title>Cálculos</template>
    <template #content>
        <DataTable
            class="mt-2"
            tableStyle="min-width: 60rem"
            :value="calculos"
            :loading="isLoading"
        >
            <template #empty>
                Nenhum Cálculo para os filtros informados
            </template>
            <Column field="y123_codvist" header="Vistoria"></Column>
            <Column field="y123_numpre" header="Numpre"></Column>
            <Column header="Valor">
                <template #body="{ data }">
                    {{ formatCurrency(data.y123_valor) }}
                </template>
            </Column>
            <Column field="tipovistoria" header="Tipo de Vistoria"></Column>
            <Column field="y123_anousu" header="Exercício"></Column>
            <Column>
                <template #body="{ data }">
                    <Button label="Detalhes" icon="pi pi-list" @click="showRowDetail(data)"/>
                </template>
            </Column>
        </DataTable>
    </template>
    </Card>

    <Dialog
        v-model:visible="visible"
        modal
        header="Detalhes"
        :responsive="true"
        position="top"
        :draggable="false"
    >
        <h4 class="bg-gray-200 p-2">Dados</h4>
        <p>Vistoria: <b>{{ inDetail.y123_codvist }}</b></p>

        <span v-if="inDetail.inscricao">
            <p>Inscrição: <b>{{ inDetail.info }}</b></p>
            <p>Data Inscrição: <b>{{ formateDate(inDetail.date) }}</b></p>
        </span>

        <span v-else>
            <p>Sanitario: <b>{{ inDetail.info }}</b></p>
            <p>Data Sanitário: <b>{{ formateDate(inDetail.date) }}</b></p>
        </span>

        <Divider/>
        <h4 class="bg-gray-200 p-2">Débito</h4>
        <p>Numpre: <b>{{ inDetail.y123_numpre }}</b></p>
        <p>Receita: <b>{{ inDetail.receita }}</b></p>

        <Divider/>

        <h4 class="bg-gray-200 p-2">Cálculo</h4>
        <p>Tipo Cálculo: <b>{{ inDetail.tipocalculo }}</b></p>
        <p>Forma Cálculo: <b>{{ inDetail.formacalculo }}</b></p>
        <p>Atividade: <b>{{ inDetail.atividade }}</b></p>

        <Divider/>

        <h4 class="bg-gray-200 p-2">Valores </h4>
        <p>Valor Base: <b>{{ formatCurrency(inDetail.y123_valorbase ?? '0,00') }}</b></p>
        <p>Valor Inflator: <b>{{ formatCurrency(inDetail.y123_valorinflator ?? '0,00') }}</b></p>
        <p>Valor Isencão: <b>{{ formatCurrency(inDetail.y123_valorisencao) ?? '0,00' }}</b></p>
        <p>Valor Final: <b>{{ formatCurrency(inDetail.y123_valor) ?? '0,00' }}</b></p>

        <Divider/>

        <h4 class="bg-gray-200 p-2">Data </h4>
        <p>Data Base Cálculo:  <b>{{ formateDate(inDetail.y123_databasecalculo) }}</b></p>
        <p>Data do Cálculo: <b>{{ formateDate(inDetail.y123_datacalculo) }}</b></p>
    </Dialog>
</template>
