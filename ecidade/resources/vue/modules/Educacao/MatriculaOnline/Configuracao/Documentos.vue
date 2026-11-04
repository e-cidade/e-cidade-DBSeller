<script setup>
import { ref, onMounted } from "vue";

import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";
import FileUpload from 'primevue/fileupload';

const toast = useToast();
const confirm = useConfirm();
const routes = {
    getDocumentos: `v4/api/educacao/matricula-online/configuracoes/documentos/`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/documentos/salvar`,
}
const documentos = ref()
const loading = ref(false)
const form = ref({
    codigo: null,
    slctdTipo: {
        data: null,
        disabled: false,
        label: 'Tipo de Documentos'
    },
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    }
})

const limpaCampos = () => {
    form.value.codigo = null
    form.value.slctdTipo.data = null
}
const salvar = async (event) => {
    const file = event.files[0];
    const formData = new FormData()
    formData.append('file', file)
    formData.append('tipo', form.value.slctdTipo.data.value)

    try{
        loading.value = true
        await window.axios.post(routes.salvar, formData)
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Sucesso ao Salvar!',
            life: 5000
        });
        loading.value = false
        limpaCampos()
        await buscarDocumentos()
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}


const buscarDocumentos = async () => {
    try {
        loading.value = true
        documentos.value = (await window.axios.get(routes.getDocumentos)).data.data
        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const editar = (data) => {
    form.value.slctdTipo.data = data
}
const view = async (url) => {
    window.open(url)
}
onMounted(async () => {
    await buscarDocumentos()
})
</script>
<template>
    <section class="container">
        <Panel header="Documentos" style="width: 500px; margin: 0 auto">
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6 col-offset-3">
                    <span class="p-float-label">
                          <Dropdown id="tipoBase" :options="documentos"
                                    optionLabel="name"
                                    v-model="form.slctdTipo.data"
                                    :disabled="true"
                          />
                         <label for="">{{ form.slctdTipo.label }}</label>
                    </span>
                </div>
            </div>
            <div class="card flex justify-content-center mb-5">
                <FileUpload mode="basic" customUpload name="arquivo" accept=".pdf" @uploader="salvar" :disabled="form.slctdTipo.data === null"/>
            </div>
        </Panel>
        <br>
        <DataTable :value="documentos" scrollable scrollHeight="300px" showGridlines style="width: 800px; margin: 0 auto">
            <Column field="name" header="Tipo de Documento">
            </Column>
            <Column headerStyle="width: 9rem" header="Ações">
                <template #body="slotProps">
                    <i class="pi pi-eye text-blue-500 mr-4" style="font-size: 1.2rem" @click="view(slotProps.data.url)"></i>
                    <i class="pi pi-pencil text-red-300 mr-4" style="font-size: 1.2rem" @click="editar(slotProps.data)"></i>
                </template>
            </Column>
        </DataTable>
    </section>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>

.container {
    width: 1000px
}

i {
    cursor: pointer
}
</style>
