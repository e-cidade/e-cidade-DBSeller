<template>
    <div class="card flex justify-content-center">
        <Button label="Contratação/Edital/Aviso" @click="visible = true" />
        <Dialog v-model:visible="visible" maximizable modal header="Contratação/Edital/Aviso" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '80vw'}">
            <br>
            <div class="card">
                <DataTable :value="products" selectionMode="single"
                           @rowSelect="onRowSelect" responsiveLayout="scroll" paginator :rows="10" showGridlines>
                    <Column field="pn03_codigo" sortable header="Sequencial Publicações PNCP"></Column>
                    <Column field="pn03_liclicita" sortable header="Código licitacao da Compra"></Column>
                    <Column field="l20_numero" sortable header="Numeração"></Column>
                    <Column field="pn03_ano" sortable header="Ano da Compra"></Column>
                    <Column field="l03_descr" sortable header="Descrição do tipo de Compra"></Column>
                    <Column field="l08_descr" sortable header="Situação da Licitação"></Column>
                    <Column field="pn03_numero" sortable header="Código PNCP"></Column>
                    <Column field="pn02_nome" sortable header="Nome da Unidade"></Column>
                    <Column field="pn03_datapublicacao" sortable header="Data Publicacao PNCP" dataType="date">
                        <template #body="{ data }">
                            {{ formatDate(data.pn03_datapublicacao) }}
                        </template>
                    </Column>
                </DataTable>
                <Toast/>
            </div>
        </Dialog>
    </div>
</template>

<script setup>
import {onMounted, ref} from "vue";
import {useToast} from 'primevue/usetoast';

const visible = ref(false);

onMounted(async () => {
    const { data } = await axios.post("v4/api/patrimonial/pncp/compraEditalAviso/buscarCompras");
    products.value = data.data
});

const formatDate = (value) => {
   const date = new Date(value);
    return date.toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const emits = defineEmits(['sequencialPNCP'])
const products = ref();
const toast = useToast();
const onRowSelect = (event) => {
    emits('sequencialPNCP', {numero: event.data.pn03_numero, ano: event.data.pn03_ano});
    visible.value = false;
};

</script>

