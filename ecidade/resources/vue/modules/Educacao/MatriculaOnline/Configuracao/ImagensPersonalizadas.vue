<script setup>
import { ref, onMounted } from "vue";
import ConfirmPopup from "primevue/confirmpopup";

import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";
import FileUpload from 'primevue/fileupload';

const toast = useToast();
const confirm = useConfirm();
const routes = {
    getImagens: `v4/api/educacao/matricula-online/configuracoes/imagens-personalizadas`,
    view: `v4/api/educacao/matricula-online/configuracoes/imagens-personalizadas/{codigo}/view`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/imagens-personalizadas/salvar`,
    excluir: `v4/api/educacao/matricula-online/configuracoes/imagens-personalizadas/{tipo}/`,
}
const imagens = ref({
    tipos: [],
    imagens: []
})
const loading = ref(false)
const form = ref({
    codigo: null,
    slctdTipo: {
        data: null,
        disabled: false,
        label: 'Tipo de Imagens'
    },
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    }
})


const excluir = async (tipo) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Deseja mesmo excluir?',
        icon: 'pi pi-exclamation-triangle',
        accept: async () => {
            try {
                let rota = routes.excluir.replace('{tipo}', tipo);
                loading.value = true
                await window.axios.delete(rota)
                toast.add({
                    severity: 'success',
                    summary: 'Sucesso!',
                    detail: 'Excluído com sucesso!',
                    life: 5000
                });
                loading.value = false
                await buscarImagens();
            } catch (e) {
                loading.value = false
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: e.response.data.message,
                    life: 5000
                });
            }
        },
        reject: () => {
            return
        }
    });
}
const limpaCampos = () => {
    form.value.codigo = null
    form.value.slctdTipo.data = null
}
const salvar = async (event) => {
    const file = event.files[0];
    const formData = new FormData()
    formData.append('imagem', file)
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
        await buscarImagens()
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

const editar = async (data) => {
    imagens.value.tipos = imagens.value.tipos.map(tipo => {
        if (tipo.value === data.value) {
            tipo.disabled = false
        }
        return tipo
    })
    data.disabled = false
    form.value.slctdTipo.data = data
}

const buscarImagens = async () => {
    try {
        loading.value = true
        imagens.value = (await window.axios.get(routes.getImagens)).data.data
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
const view = async (url) => {
    window.open(url)
}
onMounted(async () => {
    await buscarImagens()
})
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container">
        <Panel header="Documentos" style="width: 500px; margin: 0 auto">
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6 col-offset-3">
                    <span class="p-float-label">
                          <Dropdown id="tipoBase" :options="imagens.tipos"
                                    optionLabel="name"
                                    optionDisabled="disabled"
                                    v-model="form.slctdTipo.data"
                          />
                         <label for="">{{ form.slctdTipo.label }}</label>
                    </span>
                </div>
            </div>
            <div class="card flex justify-content-center mb-5">
                <FileUpload mode="basic" customUpload name="imagem" accept=".png" @uploader="salvar" :disabled="form.slctdTipo.data === null"/>
            </div>
        </Panel>
        <br>
        <DataTable :value="imagens.imagens" scrollable scrollHeight="300px" showGridlines style="width: 800px; margin: 0 auto">
            <Column field="tipo.name" header="Tipo de Imagem">
            </Column>
            <Column headerStyle="width: 9rem" header="Ações">
                <template #body="slotProps">
                    <i class="pi pi-eye text-green-600 mr-4" style="font-size: 1.2rem" @click="view(slotProps.data.url)"></i>
                    <i class="pi pi-pencil text-yellow-600 mr-4" style="font-size: 1.2rem" @click="editar(slotProps.data.tipo)"></i>
                    <i class="pi pi-trash text-red-600" style="font-size: 1.2rem" @click="excluir(slotProps.data.tipo.value)"></i>
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
