<script setup>
import {onMounted, ref} from "vue";
import {useToast} from 'primevue/usetoast';
import Tag from 'primevue/tag';
import Fieldset from 'primevue/fieldset';
import MultiSelect from 'primevue/multiselect';
import Button from 'primevue/button';
import ModalLoading from "../../../Components/ModalLoading.vue";

const display = ref(false);
const fases = ref();
const files = ref([]);
const loading = ref(false);
const mensagens = {
    fases: 'Buscando Fases...',
    relatorio: 'Emitindo Relatório...'
};
const mensagemModal = ref(null);
const routes = {
    fases: `v4/api/educacao/matricula-online/fases`,
    emissao: `v4/api/educacao/matricula-online/relatorios/geral-inscricoes`,
}
const selectedFase = ref([]);
const toast = useToast();

const getFases = async () => {
    try {
        loading.value = true;
        mensagemModal.value = mensagens.fases;
        return (await window.axios.get(routes.fases)).data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    } finally {
        loading.value = false;
    }
}

async function emitirRelatorio() {
    loading.value = true;
    mensagemModal.value = mensagens.relatorio;

    const params = {
        fases: selectedFase.value
    }

    try {
        const response = await window.axios.post(routes.emissao, params);
        response.data.data.map(relatorio => {
           addFile(
               `${relatorio.pathExterno}`,
               `${relatorio.name}`
           )
        });
        openModal();
    } catch (e) {
        const errorMessage = e.response && e.response.data && e.response.data.message
            ? e.response.data.message
            : 'Ocorreu um erro ao emitir o relatório. Por favor, tente novamente mais tarde.';
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: errorMessage,
            life: 10000
        });
    } finally {
        loading.value = false;
    }
}

function openModal() {
    display.value = true;
}

function closeModal() {
    display.value = false;
    files.value = [];
}

function addFile(url, name, icon = 'pi pi-file-pdf') {
    let extension = url.split('.').pop();
    switch (extension) {
        case 'pdf':
            icon = 'pi pi-file-pdf';
            break;

        case 'doc':
        case 'docx':
        case 'odt':
            icon = 'pi pi-file-word';
            break;

        case 'xlsx':
        case 'xls':
        case 'csv':
        case 'ods':
            icon = 'pi pi-file-excel';
            break;
    }
    files.value.push({
        url: url,
        name: name,
        icon: icon
    });
}

onMounted(async () => {
    fases.value = await getFases();
});
</script>

<template>
    <div class="fase-wrapper">
        <Fieldset legend="Fase" style="background-color: transparent">
            <MultiSelect
                id="multiselect"
                v-model="selectedFase"
                :options="fases"
                optionLabel="descricao"
                placeholder="Selecione uma fase"
                :maxSelectedLabels="3"
                class="w-full md:w-30rem">
                <template #value>
                    <div class="py-1 px-2">
                        <span v-if="selectedFase.length <= 1"><b>{{ selectedFase.length }}</b> item selecionado.</span>
                        <span v-else>
                            <b>{{ selectedFase.length }}</b> iten{{ selectedFase.length > 1 ? 's' : '' }} selecionados.
                        </span>
                    </div>
                </template>
                <template #option="slotProps">
                    <div class="flex items-center gap-2">
                        <span>{{ slotProps.option.descricao }}</span>
                        <Tag
                            :value="slotProps.option.isEncerrada ? 'Encerrada' : 'Aberta'"
                            :severity="slotProps.option.isEncerrada ? 'danger' : 'success'"
                        />
                    </div>
                </template>
            </MultiSelect>
            <!-- Botão para emitir o relatório -->
            <div class="button-wrap">
                <Button icon="pi pi-print" rounded outlined label="Emitir CSV" @click="emitirRelatorio" :disabled="selectedFase.length == 0" raised/>
            </div>
        </Fieldset>
    </div>
    <Dialog header="Arquivos para Download" :modal="true"  closable v-model:visible="display"
            :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '30vw'}"
            @hide="closeModal">
        <div class="card">
            <div class="d-flex flex-column justify-content-center flex-wrap card-container gap-1 pt-1">
                <ul class="list-none p-0">
                    <li v-for="file in files" class="mt-2 text-xl">
                        <a id="link-download" :href="file.url" download>
                            <i :class="file.icon" class="m2" style="font-size: 2rem; color:#4a789c"></i>
                            <span class="font-weight-bold text-secondary">
                                {{ file.name }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </Dialog>
    <ModalLoading :isLoading="loading" :message="mensagemModal"/>
</template>

<style scoped>
.fase-wrapper {
    width: 45vw;
    margin: 2rem auto;
    text-align: center;
}

::v-deep(.p-fieldset-legend) {
    padding: 0.7rem 1rem;
    border-radius: 2rem;
}

::v-deep(.p-fieldset) {
    border-radius: 1rem;
}

.button-wrap {
    display: flex;
    flex-direction: row;
    margin-top: 1rem;
    text-align: center;
    gap: 1rem;
    justify-content: center;
}

</style>
