<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import Message from 'primevue/message';
import FileUpload from 'primevue/fileupload';
import Toast from 'primevue/toast';
import ModalLoading from "../../../../Components/ModalLoading.vue";
import MultiDownload from "../../../../Components/MultiDownload.vue";

const toast = useToast();
const props = defineProps(['exercicio']);
const loading = ref(false);
const mensagemLoad = ref(null);
const fileUpload = ref(null);
const multiDownload = ref(null);

async function upload(event) {

    loading.value = true;
    mensagemLoad.value = 'Realizando upload do arquivo.';
    const formData = new FormData();

    for (let file of event.files) {
        formData.append('file', file);
    }

    formData.append('exercicio', props.exercicio);
    try {
        const response = await window.axios.post(
            "v4/api/financeiro/contabilidade/conta-corrente/implantacao/ddr",
            formData
        );

        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso', life: 4000});
    } catch (e) {
        (e.response.data.message)
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro', life: 6000});
    }

    fileUpload.value.clear();
    fileUpload.value.uploadedFileCount = 0;

    loading.value = false;
}

async function dowloadTemplate() {
    loading.value = true;
    mensagemLoad.value = '';
    try {
        const response = await window.axios.get(
            "v4/api/financeiro/contabilidade/conta-corrente/implantacao/ddr/template"
        );

        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso', life: 4000});

        multiDownload.value.addFile(
            `${response.data.data.csvLinkExterno}`,
            response.data.message
        );
        multiDownload.value.openModal();
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro', life: 6000});
    }
    loading.value = false;
}


</script>

<template>
    <section class="container">
        <Message severity="info" :closable="false">
            <div style='font-size: 10pt'>
                Importe uma planilha no formato CSV.<br>
                Padrões da planilha:<br>
                <ul style="margin: 0;">
                    <li><strong>Codificação: </strong>Europa Ocidental (ISO-8859-1) ou Latin1</li>
                    <li><strong>Delimitador de campo:</strong> ; (ponto e vírgula)</li>
                    <li><strong>Delimitador de texto:</strong> " (aspas duplas)</li>
                </ul>
                <div>
                    Exemplo de como deve ser a estrutura da planilha
                    <table class="table-exemplo">
                        <tr>
                            <td class="bold">Estrutural</td>
                            <td class="bold">Reduzido</td>
                            <td class="bold">Instituição</td>
                            <td class="bold">Fonte Gestão</td>
                            <td class="bold">Subrecurso</td>
                            <td class="bold">Complemento</td>
                            <td class="bold">Saldo</td>
                            <td class="bold">Natureza</td>
                        </tr>
                        <tr>
                            <td>821110100000000</td>
                            <td>17081</td>
                            <td>1</td>
                            <td>1500</td>
                            <td>0001</td>
                            <td>0</td>
                            <td>50000,00</td>
                            <td>D</td>
                        </tr>
                        <tr>
                            <td>821110100000000</td>
                            <td>17081</td>
                            <td>1</td>
                            <td>1500</td>
                            <td>0001</td>
                            <td>3160</td>
                            <td>2000,00</td>
                            <td>D</td>
                        </tr>
                        <tr>
                            <td>821110100000000</td>
                            <td>17081</td>
                            <td>1</td>
                            <td>1500</td>
                            <td>0020</td>
                            <td>0</td>
                            <td>500,00</td>
                            <td>D</td>
                        </tr>
                        <tr>
                            <td>821110100000000</td>
                            <td>18346</td>
                            <td>2</td>
                            <td>1500</td>
                            <td>0001</td>
                            <td>3160</td>
                            <td>1000,00</td>
                            <td>D</td>
                        </tr>
                    </table>
                </div>
            </div>
        </Message>

        <Panel header="Importar saldo inicial">
            <FileUpload ref="fileUpload" name="planilha[]" :file-limit="1" :customUpload="true" @uploader="upload"
                        invalidFileTypeMessage="{0}: Tipo de arquivo inválido, tipos de arquivo permitidos: {1}"
                        accept="text/csv">
                <template #header="{ chooseCallback, uploadCallback, clearCallback, files }">
                    <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                        <div class="flex gap-2">
                            <Button @click="chooseCallback()" icon="pi pi-file" label="Selecione"></Button>
                            <Button @click="uploadCallback()" icon="pi pi-upload" class="p-button-success"
                                    :disabled="!files || files.length === 0"></Button>
                            <Button @click="clearCallback()" icon="pi pi-times" class="p-button-danger"
                                    :disabled="!files || files.length === 0"></Button>
                        </div>
                        <Button @click="dowloadTemplate()" icon="pi pi-download" label="Template" ></Button>
                    </div>

                </template>
                <template #empty>
                    <p>Arraste e solte os arquivos aqui para fazer o upload ou selecione o arquivo clicando no botão
                        <b>Escolha</b>.
                    </p>
                </template>
            </FileUpload>
        </Panel>
    </section>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    <MultiDownload ref="multiDownload" :header="'Arquivos para Download'" :position="'center'"></MultiDownload>

    <Toast/>
</template>

<style scoped>
.p-message .p-message-text {
    font-size: 100% !important;
}

.bold {
    font-weight: bold;
}

table.table-exemplo {
    border-collapse: collapse;
    background-color: #FFF;
    color: #0a0a0a;
}

table.table-exemplo td {
    border: 1px solid black;
    padding: 0 2px;
}


</style>
