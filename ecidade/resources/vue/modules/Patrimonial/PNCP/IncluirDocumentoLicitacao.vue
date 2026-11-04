<template>
    <section class="container">
        <Panel header="Inclusão de Documento">
            <div class="p-fluid grid pt-3">
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText type="text" disabled v-model="sequencial"
                                   :class="{'p-invalid': verifica.sequencial}"/>
                        <label for="">Sequencial PNCP</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText type="text" disabled v-model="ano" :class="{'p-invalid': verifica.ano}"/>
                        <label for="">Ano PNCP</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <DialogPesquisaCompras @sequencialPNCP="sequencialPPNCP"/>
                </div>
            </div>
            <div class="p-fluid grid pt-3">
                <div class="field col-12 md:col-12">
                    <span class="p-float-label">
                        <InputText type="text" v-model="tituloDocumento"
                                   :class="{'p-invalid': verifica.tituloDocumento}"/>
                        <label for="">Titulo do Documento</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid pt-2">
                <div class="field col-12 md:col-12">
                        <span class="p-float-label">
                             <Dropdown
                                 v-model="tipoSelecionado"
                                 :options="tipos"
                                 optionLabel="name"
                                 placeholder="Selecione o Tipo"
                                 class="w-full"
                                 :class="{'p-invalid': verifica.tipoSelecionado}"
                             />
                            <label for="">Tipo</label>
                        </span>
                </div>
            </div>
            <FileUpload
                ref="upload"
                name="documento[]"
                :showUploadButton="false"
                url=""
                @upload="()=>{}"
                :fileLimit="1"
                :accept="extencoes"
                :maxFileSize="30000000"
                :invalidFileTypeMessage="tipoArquivoErro"
                :invalidFileSizeMessage="tamanhoArquivo"
                :invalidFileLimitMessage="limiteArquivo"
                @select="onSelectFile"
                @remove="onSelectFile"
                :pt="{details: {style: 'display: none'}}"
            >
                <template #empty>
                    <p>Arraste e solte o arquivo aqui para fazer upload.</p>
                </template>
            </FileUpload>
            <div class="card flex justify-content-center pt-3">
                <Button type="button" label="Incluir" icon="pi pi-check" iconPos="right" :loading="loading"
                        @click="incluir"/>
            </div>
        </Panel>
    </section>
</template>

<script setup>

import {useToast} from "primevue/usetoast";
import {ref, watch} from 'vue';
import DialogPesquisaCompras from "@modules/Patrimonial/PNCP/components/DialogPesquisaCompras.vue";
import FileUpload from "primevue/fileupload";

const toast = useToast();

const tipoSelecionado = ref();
const tamanhoArquivo = ref('O tamanho máximo aceito, por arquivo enviado ao PNCP, é de 30 MB (Megabytes).');
const tipoArquivoErro = ref('O formato do arquivo anexado não é compatível com o PNCP.')
const limiteArquivo = ref('Número máximo de arquivos excedido, o limite é 1 no máximo. ' +
    'Será enviado apenas o primeiro arquivo anexado.');
const tituloDocumento = ref('');
const documentos = ref([]);
const sequencial = ref();
const ano = ref();
const upload = ref(null);
const loading = ref(false);
const tipos = ref([
    {name: 'Aviso de Contratação Direta', code: 1},
    {name: 'Edital', code: 2},
    {name: 'Minuta do Contrato', code: 3},
    {name: 'Termo de Referência', code: 4},
    {name: 'Anteprojeto', code: 5},
    {name: 'Projeto Básico', code: 6},
    {name: 'Estudo Técnico Preliminar', code: 7},
    {name: 'Projeto Executivo', code: 8},
    {name: 'Mapa de Riscos', code: 9},
    {name: 'DFD', code: 10},
    {name: 'Minuta de Ata de Registro de Preços', code: 19},
    {name: 'Ato que autoriza a Contratação Direta', code: 20}
]);

const extencoes = ref(
    'application/pdf,text/plain,application/rtf,' +
    'application/msword,' +
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document,' +
    'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,' +
    'application/vnd.oasis.opendocument.text,' +
    'application/vnd.oasis.opendocument.spreadsheet,' +
    'application/vnd.sun.xml.writer,application/zip,pplication/x-7z-compressed,application/vnd.rar,' +
    'image/vnd.dwg,application/octet-stream,image/vnd.dxf,model/vnd.dwf,model/vnd.dwfx+xml,image/svg+xml,' +
    'application/sldprt,application/sldasm,application/dgn,application/ifc,' +
    'application/skp,image/x-3ds,model/vnd.collada+xml,' +
    'application/octet-stream,application/octet-stream,application/octet-stream');

const sequencialPPNCP = (data) => {
    sequencial.value = data.numero;
    ano.value = data.ano;
}
watch(sequencial, (x, y) => {
    if (y !== undefined) {
        tituloDocumento.value = '';
        tipoSelecionado.value = '';
        upload._value.clear();
    }
})

const verifica = ref({
    tituloDocumento: false,
    tipoSelecionado: false,
    sequencial: false,
    ano: false
});

function onSelectFile({files}) {
    documentos.value = files[0];
}

function verificaCampos() {
    verifica.value.tituloDocumento = false;
    verifica.value.tipoSelecionado = false;
    verifica.value.sequencial = false;
    verifica.value.ano = false;

    if (!sequencial.value) {
        verifica.value.sequencial = true;
        verifica.value.ano = true;
        const message = 'Escolha a Compra/Edital/Aviso';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false
    }

    if (!tituloDocumento.value) {
        verifica.value.tituloDocumento = true;
        const message = 'Preencha o título do documento';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false
    }
    if (tipoSelecionado.value === '0' || !tipoSelecionado.value) {
        verifica.value.tipoSelecionado = true;
        const message = 'Selecione o tipo do documento';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false;
    }

    if (documentos.value.name === undefined) {
        const message = 'Escolha um documento';
        toast.add({severity: 'warn', detail: message, summary: 'Atenção', life: 4000});
        return false
    }

    return true;
}

function limparCampos() {
    sequencial.value = '';
    ano.value = '';
    tituloDocumento.value = '';
    tipoSelecionado.value = '';
    documentos.value = '';
    upload._value.clear();
}

async function incluir() {
    if (!verificaCampos()) {
        return false;
    }
    loading.value = true;

    try {
        const data = new FormData();
        data.append('tituloDocumento', tituloDocumento.value);
        data.append('tipoDocumento', tipoSelecionado.value.code);
        data.append('sequencial', sequencial.value);
        data.append('ano', ano.value);
        data.append('documento', documentos.value);
        await axios.post('v4/api/patrimonial/pncp/compraEditalAviso/incluir-documento', data);
        limparCampos();
        toast.add({severity: 'success', detail: 'Documento incluido com sucesso', summary: 'Atenção', life: 4000});
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Atenção', life: 4000});
    }
    loading.value = false;
}


</script>

<style scoped>
.container {
    width: 800px;
}

</style>
