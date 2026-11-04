<script setup>

import { onBeforeMount, ref } from "vue";
import { useToast } from "primevue/usetoast";

import ModalLoading from "../../../../Components/ModalLoading.vue";

const props = defineProps({
   visible: { type: Boolean, required: true }
});
const emit = defineEmits(['sucesso', 'update:visible']);

const toast = useToast();

const fileUpload = ref();

const isLoading = ref(false);

const grupo = ref(null);
const grupos = ref([]);
const gruposFiltrados = ref([]);
const tipo = ref(null);
const tipos = ref([]);
const tiposFiltrados = ref([]);

const arquivo = ref(null);

const erro = ref({
    grupo: false,
    tipo: false
});

function autoCompleteGrupo({ query }) {
    if (!query.trim().length) {
        gruposFiltrados.value = [ ...grupos.value ];
        return;
    }

    gruposFiltrados.value = grupos.value.filter(grupo => {
        if (Number.isInteger(Number(query))) {
            return grupo.codigo.toString().startsWith(query);
        }

        return grupo.descricao.toLowerCase().startsWith(query.toLowerCase());
    });
}

function autoCompleteTipo({ query }) {
    if (!query.trim().length) {
        tiposFiltrados.value = [ ...tipos.value ];
        return;
    }

    tiposFiltrados.value = tipos.value.filter(tipo => {
        if (Number.isInteger(Number(query))) {
            return tipo.codigo.toString().startsWith(query);
        }

        return tipo.descricao.toLowerCase().startsWith(query.toLowerCase());
    });
}

async function getGrupos() {
    try {
        const response = await axios.get('v4/api/configuracao/gerador/relatorios/grupos');
        grupos.value = response.data.data;
        gruposFiltrados.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar grupos',
            detail: e.response ? e.response.data.message : e.message
        });
    }
}

async function getTipos() {
    try {
        const response = await axios.get('v4/api/configuracao/gerador/relatorios/tipos');
        tipos.value = response.data.data;
        tiposFiltrados.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar tipos',
            detail: e.response ? e.response.data.message : e.message
        });
    }
}

function onRemoveFile(callback) {
    arquivo.value = null;
    callback(0);
}

function onSelectFile({ files }) {
    arquivo.value = files[0];
}

function hasError() {
    let hasError = false;
    if (!grupo.value) {
        erro.grupo = true;
        hasError = true;
    }

    if (!tipo.value) {
        erro.tipo = true;
        hasError = true;
    }

    return hasError;
}

async function importar() {
    if (hasError()) {
        return;
    }

    isLoading.value = true;
    try {
        const formData = new FormData();
        formData.append('grupo', grupo.value.codigo);
        formData.append('tipo', tipo.value.codigo);
        formData.append('arquivo', arquivo.value);

        await axios.post('v4/api/configuracao/gerador/relatorios/importar', formData);

        fileUpload.value.clear();
        fileUpload.value.uploadedFileCount = 0;
        arquivo.value = null;
        grupo.value = null;
        tipo.value = null;

        toast.add({
           severity: 'success',
           summary: 'Relatório importado com sucesso'
        });

        emit('sucesso');
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao importar relatório',
            detail: e.response ? e.response.data.message : e.message
        });
    }

    isLoading.value = false;
}

onBeforeMount(async () => {
    await Promise.all([getGrupos(), getTipos()]);
});

</script>

<template>
    <Dialog :pt="{ content: { style: 'background: #e1dede;' } }"
            :visible="visible"
            header="Importar Relatório"
            modal
            @update:visible="value => $emit('update:visible', value)">
        <section class="flex flex-column w-full mt-4 gap-2">
            <section class="flex justify-content-center">
                <article class="formgrid grid row-gap-2">
                    <section class="field col-6 md:col-12">
                        <span class="p-float-label">
                            <AutoComplete v-model="grupo"
                                          :suggestions="gruposFiltrados"
                                          :inputClass="{ 'p-invalid': erro.grupo }"
                                          class="w-full"
                                          dropdown
                                          forceSelection
                                          inputId="grupo"
                                          :optionLabel="grupo => `${grupo.codigo} - ${grupo.descricao}`"
                                          @complete="autoCompleteGrupo">
                                <template #option="slotProps">
                                    <div>
                                        {{ slotProps.option.codigo }} - {{ slotProps.option.descricao }}
                                    </div>
                                </template>
                            </AutoComplete>
                            <label for="grupo">Grupo</label>
                        </span>
                        <small v-if="erro.grupo" class="p-error" id="grupo-error">Informe este campo.</small>
                    </section>
                    <section class="field col-6 md:col-12">
                        <span class="p-float-label">
                            <AutoComplete v-model="tipo"
                                          :suggestions="tiposFiltrados"
                                          :inputClass="{ 'p-invalid': erro.tipo }"
                                          class="w-full"
                                          dropdown
                                          forceSelection
                                          inputId="tipo"
                                          :optionLabel="tipo => `${tipo.codigo} - ${tipo.descricao}`"
                                          @complete="autoCompleteTipo">
                                <template #option="slotProps">
                                    <div>
                                        {{ slotProps.option.codigo }} - {{ slotProps.option.descricao }}
                                    </div>
                                </template>
                            </AutoComplete>
                            <label for="tipo">Tipo</label>
                        </span>
                        <small v-if="erro.tipo" class="p-error" id="grupo-error">Informe este campo.</small>
                    </section>
                    <section class="field col-12">
                        <FileUpload ref="fileUpload"
                                    :customUpload="true"
                                    :file-limit="1"
                                    accept="text/xml"
                                    invalidFileTypeMessage="{0}: Tipo de arquivo inválido, tipos de arquivo permitidos: {1}"
                                    name="relatorios[]"
                                    @select="onSelectFile">
                            <template #header="{ chooseCallback, files }">
                                <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                                    <div class="flex">
                                        <Button :disabled="files.length" icon="pi pi-file" label="Selecione" @click="chooseCallback()"></Button>
                                    </div>
                                </div>
                            </template>
                            <template #content="{ files, removeFileCallback }">
                                <div v-if="files.length" class="flex flex-row card align-items-center justify-content-between">
                                    <span>{{ files[0].name }}</span>
                                    <Button icon="pi pi-times" @click="onRemoveFile(removeFileCallback)" outlined rounded severity="danger" />
                                </div>
                            </template>
                            <template #empty>
                                <p>Nenhum arquivo selecionado.</p>
                            </template>
                        </FileUpload>
                    </section>
                </article>
            </section>
            <section class="flex justify-content-center">
                <Button :disabled="!arquivo" icon="pi pi-file-import" label="Importar" @click="importar"></Button>
            </section>
        </section>
    </Dialog>

    <ModalLoading :isLoading="isLoading"></ModalLoading>
</template>

<style scoped>

</style>
