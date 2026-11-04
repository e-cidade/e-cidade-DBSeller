<script setup>
import {useToast} from "primevue/usetoast";
import {ref} from "vue";
import ModalLoading from "../../Components/ModalLoading.vue";

const toast = useToast()

const fileUpload = ref()
const isLoading = ref(false)

const date = ref()
const file = ref()

const verifica = ref({
    date: false,
    file: false
})

function onSelectFile({ files }) {
    file.value = files[0]
}

async function importar() {
    if (!verificarCampos()) {
        return false;
    }

    isLoading.value = true

    try {
        const formData = new FormData()
        formData.append('date', date.value)
        formData.append('file', file.value)

        await axios.post('v4/api/patrimonial/material/implantacao/importar-planilha', formData)

        fileUpload.value.clear()
        fileUpload.value.uploadedFileCount = 0
        file.value = null
        date.value = null

        toast.add({
            severity: 'success',
            summary: 'Processo realizado com Sucesso!',
        })
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Não foi possível concluir a operação.',
            detail: e.response ? e.response.data.message : e.message
        })
    }

    isLoading.value = false
}

function verificarCampos() {
    verifica.value.date = false
    verifica.value.file = false

    if (!file.value) {
        verifica.value.file = true
        const message = 'Insira a Planinha'
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000})
        return false
    }

    if (!date.value) {
        verifica.value.date = true
        const message = 'Preencha a Data'
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000})
        return false
    }

    return true
}
</script>

<template>
    <section>
        <Panel class="container" header="Importação de Planilha">
            <div class="p-fluid grid formgrid">
                <div class="field col-12 md:col-6">
                    <label for="file" class="required">Arquivo</label>
                    <Toast />
                    <FileUpload
                        ref="fileUpload"
                        mode="basic"
                        name="file[]"
                        accept="text/csv"
                        @select="onSelectFile"
                        :class="{'p-invalid' : verifica.file}"
                        invalid-file-type-message="Tipo de arquivo inválido. Por favor, forneça um arquivo CSV."
                    />
                </div>
                <div class="field col-12 md:col-6">
                    <label for="date" class="required">Data</label>
                    <Calendar
                        id="date"
                        v-model="date"
                        dateFormat="dd/mm/yy"
                        showIcon
                        :class="{'p-invalid' : verifica.date}"
                    />
                </div>
            </div>
        </Panel>
    </section>

    <section>
        <div class="text-center">
            <Button label="Processar" icon="pi pi-file-import" class="p-button-primary" @click="importar()" />
        </div>
    </section>

    <ModalLoading :is-loading="isLoading" />
</template>

<style scoped>

</style>
