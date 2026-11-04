<script setup>
import {onMounted, ref} from "vue";
import {useToast} from 'primevue/usetoast';
import Tag from 'primevue/tag';
import Fieldset from 'primevue/fieldset';
import MultiSelect from 'primevue/multiselect';
import Button from 'primevue/button';
import ModalLoading from "../../../Components/ModalLoading.vue";

const display = ref(false);
const etapas = ref([]);
const escolas = ref([]);
const fases = ref();
const files = ref([]);
const form = ref({
    slctFase: {
        data: []
    },
    slctEtapa: {
        data: []
    },
    slctEscola: {
        data: []
    }
});
const loading = ref(false);
const mensagens = {
    fases: 'Buscando Fases...',
    relatorio: 'Emitindo Relatório...',
    etapas: 'Buscando Etapas...'
};
const mensagemModal = ref(null);
const routes = {
    fases: `v4/api/educacao/matricula-online/fases`,
    etapas: `v4/api/educacao/matricula-online/etapas-por-fase`,
    escolas: `v4/api/educacao/matricula-online/escolas-por-fase-etapa`,
    emissao: `v4/api/educacao/matricula-online/relatorios/demanda-reprimida`
}
const toast = useToast();

const getFases = async () => {
        form.value.slctFase.data = [];
        fases.value = [];
        form.value.slctEtapa.data = [];
        etapas.value = [];
        form.value.slctEscola.data = [];
        escolas.value = [];
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

async function getEtapas() {
    form.value.slctEscola.data = [];
    escolas.value = [];
    form.value.slctEtapa.data = [];
    etapas.value = [];
    try {
        const params = {
            codFase: form.value.slctFase.data.codigo
        }
        loading.value = true;
        mensagemModal.value = mensagens.etapas;
        const response = (await window.axios.get(`${routes.etapas}/${params.codFase}`)).data.data;

        if (response.length > 0) {
            response.map(etapa => {
                etapas.value.push({
                    codigo: etapa.ed11_i_codigo,
                    descricao: etapa.ed11_c_descr
                });
            });
        } else {
            toast.add({
                severity: 'warn',
                summary: 'Atenção!',
                detail: 'Não existem etapas configuradas paras a fase selecionada!',
                life: 15000
            });
        }
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

async function getEscolas() {
    form.value.slctEscola.data = [];
    escolas.value = [];
    try {
        const params = {
            codFase: form.value.slctFase.data.codigo,
            codEtapa: form.value.slctEtapa.data.codigo
        }
        loading.value = true;
        mensagemModal.value = mensagens.etapas;
        const response = (await window.axios.get(`${routes.escolas}/${params.codFase}/${params.codEtapa}`)).data.data;

        if (response.length > 0) {
            response.map(escola => {
                escolas.value.push({
                    codigo: escola.cod_escola,
                    nome: escola.nome_escola
                });
            });
        } else {
            toast.add({
                severity: 'warn',
                summary: 'Atenção!',
                detail: 'Não existem etapas configuradas paras a fase selecionada!',
                life: 15000
            });
        }
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
        fase: form.value.slctFase.data.codigo,
        etapa: form.value.slctEtapa.data.codigo ? form.value.slctEtapa.data.codigo : null,
        escola: form.value.slctEscola.data.codigo ? form.value.slctEscola.data.codigo : null
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
        <Fieldset legend="Filtros" style="background-color: transparent">
            <Dropdown
                v-model="form.slctFase.data"
                placeholder="Selecione uma Fase"
                :options="fases"
                optionLabel="descricao"
                class="w-full md:w-30rem mb-3"
                @change="getEtapas"
            >
            <template #option="slotProps">
                <div class="flex items-center gap-2">
                    <span>{{ slotProps.option.descricao }}</span>
                    <Tag
                        :value="slotProps.option.isEncerrada ? 'Encerrada' : 'Aberta'"
                        :severity="slotProps.option.isEncerrada ? 'danger' : 'success'"
                    />
                </div>
            </template>
            </Dropdown>
            <Dropdown
                v-show="Object.keys(form.slctFase.data).length > 0"
                v-model="form.slctEtapa.data"
                placeholder="Selecione uma Etapa"
                :options="etapas"
                optionLabel="descricao"
                class="w-full md:w-30rem mb-3"
                @change="getEscolas"
            >
                <template #option="slotProps">
                    <div class="flex items-center gap-2">
                        <span>{{ slotProps.option.descricao }}</span>
                    </div>
                </template>
            </Dropdown>
            <Dropdown
                v-show="Object.keys(form.slctEtapa.data).length > 0"
                v-model="form.slctEscola.data"
                placeholder="Selecione uma Escola"
                :options="escolas"
                optionLabel="nome"
                class="w-full md:w-30rem"
            >
                <template #option="slotProps">
                    <div class="flex items-center gap-2">
                        <span>{{ slotProps.option.nome }}</span>
                    </div>
                </template>
            </Dropdown>
            <div class="button-wrap">
                <Button icon="pi pi-print" rounded outlined label="Emitir CSV" @click="emitirRelatorio" :disabled="Object.keys(form.slctFase.data).length <= 0" raised/>
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
                        <a style="text-align: center;" id="link-download" :href="file.url" download>
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
