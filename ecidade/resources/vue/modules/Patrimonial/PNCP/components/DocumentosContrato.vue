<template>
    <DataTable ref="dt" :value="documentos" v-model:selection="selectedProducts"
               :paginator="true" :rows="10"
               showGridlines
               lazy
               :loading="loading"
               :rowsPerPageOptions="[5,10,25]"
    >
        <Column field="sequencialDocumento" header="Sequencial Doc"></Column>
        <Column field="titulo" header="Titulo Documento"></Column>
        <Column field="sequencialCompra" header="Sequencial Compra"></Column>
        <Column field="tipoDocumentoNome" header="Tipo Documento"></Column>
        <Column :exportable="false" header="Ações" class="card flex justify-content-center">
            <template #body="data">
                <Button icon="pi pi-trash" outlined rounded class="mr-2" severity="danger"
                        @click="confirmDeleteProduct(data)"/>
                <Button icon="pi pi-download" outlined rounded severity="info"
                        @click="downloadArquivo(data)"/>
            </template>
        </Column>
        <template #empty>
            <div class="flex justify-content-center">
                <p>Não existem documentos anexados ao PNCP.</p>
            </div>
        </template>
    </DataTable>

    <Dialog v-model:visible="deleteDocumentoDialog" :style="{width: '450px'}" header="Confirmar" :modal="true">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3 pt-2" style="font-size: 2rem"/>
            <span v-if="documento">Quer excluir o documento <b>{{ confirmMessage }}</b>?</span>
        </div>
        <div class="p-fluid grid formgrid">
            <div class="field col-12 md:col-12 mt-5">
                        <span class="p-float-label">
                            <InputText id="justificativa" v-model="justificativa"/>
                             <label for="">Justificativa</label>
                        </span>
                <p style="color: cornflowerblue">Justificativa obrigatoria.</p>
            </div>
        </div>
        <template #footer>
            <Button label="Não" icon="pi pi-times" text @click="cancelaExclusao"/>
            <Button label="Sim" icon="pi pi-check" text @click="deleteDocumento"/>
        </template>
    </Dialog>
</template>

<script setup>
import {ref, watch} from "vue";
import {useToast} from 'primevue/usetoast';


watch(() => props.sequencialContrato, async (newValue, oldValue) => {
    loading.value = true;
    const formData = new FormData;
    formData.append('ano', props.anoContrato);
    formData.append('sequencial', props.sequencialContrato);
    try {
        const response = await axios.post('v4/api/patrimonial/pncp/contratos/buscar-documentos', formData);
        console.log(response.data.data)
        loading.value = false;
        documentos.value = response.data.data
    } catch (e) {
        loading.value = false;
        toast.add({severity: 'error', summary: 'Erro', detail: e.response.data.message, life: 3000});
    }
})

const props = defineProps(['sequencialContrato', 'anoContrato']);

const documentos = ref();
const justificativa = ref('');
const toast = useToast();
const loading = ref(false);
const dt = ref();
const ativo = ref('Desativado');
const deleteDocumentoDialog = ref(false);
const documento = ref({});
const confirmMessage = ref('')
const selectedProducts = ref();
const confirmDeleteProduct = (data) => {
    documento.value = data.data;
    confirmMessage.value = `${data.data.sequencialDocumento} - ${data.data.titulo}`
    deleteDocumentoDialog.value = true;
};
const deleteDocumento = async () => {
    loading.value = true;
    if (justificativa.value !== '') {
        documento.value.justificativa = justificativa.value;
    }

    if (justificativa.value === '') {
        toast.add({severity: 'error', summary: 'Erro', detail: 'Justificativa Obrigatória', life: 5000});
        loading.value = false;
        return false;
    }

    try {
        deleteDocumentoDialog.value = false;
        await axios.post('v4/api/patrimonial/pncp/contratos/excluir-documento', documento.value)
        documentos.value = documentos.value.filter(val => val.sequencialDocumento !== documento.value.sequencialDocumento);
        loading.value = false;
        justificativa.value = '';
        documento.value = {};
        toast.add({severity: 'success', summary: 'Sucesso', detail: 'Documento excluido com sucesso', life: 3000});
    } catch (e) {
        loading.value = false;
        toast.add({severity: 'error', summary: 'Erro', detail: e.response.data.message, life: 3000});
    }
};
const cancelaExclusao = () => {
    deleteDocumentoDialog.value = false
    justificativa.value = '';
}

const downloadArquivo = (data) => {
    window.open(data.data.uri);
}
const getStatusLabel = (status) => {
    if (status.data.statusAtivo) {
        ativo.value = 'Ativo'
    }
    switch (status.data.statusAtivo) {
        case true:
            return 'success';

        case false:
            return 'danger';

        default:
            return null;
    }
};
</script>
<style scoped>

</style>
