<template>
    <div class="card flex justify-content-center">
        <Button label="Contratos" @click="visible = true" />
        <Dialog v-model:visible="visible" maximizable modal header="Contratos" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '80vw'}">
            <br>
            <div class="card">
                <DataTable :value="contratos" selectionMode="single"
                           @rowSelect="onRowSelect" responsiveLayout="scroll" paginator :rows="10" showGridlines>
                    <Column field="pn04_acordo" sortable header="Acordo"></Column>
                    <Column field="pn04_numero" sortable header="Número"></Column>
                    <Column field="pn04_ano" sortable header="Ano"></Column>
                    <Column field="pn04_codigo" sortable header="Código PNCP"></Column>
                    <Column field="unidade_compradora.pn02_nome" sortable header="Unidade Compradora"></Column>
                    <Column field="pn04_datapublicacao" sortable header="Data Publicacao" dataType="date">
                        <template #body="{ data }">
                            {{ formatDate(data.pn04_datapublicacao) }}
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
    const { data } = await axios.post("v4/api/patrimonial/pncp/contratos/buscarContratos");
    contratos.value = data.data
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
const contratos = ref();
const toast = useToast();
const onRowSelect = (event) => {
    emits('sequencialPNCP', {numero: event.data.pn04_numero, ano: event.data.pn04_ano});
    visible.value = false;
};

</script>

