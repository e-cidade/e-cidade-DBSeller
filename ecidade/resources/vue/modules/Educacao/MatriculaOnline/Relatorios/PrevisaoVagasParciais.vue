<template>
    <div v-show="showBackground" class="background">
    </div>
    <div class="container-wrapper">
        <div v-if="fases.length > 1">
            <Fieldset style="width: 50vw; margin: 0 auto 1rem;" legend="Filtros">
                <div class="card fase-wrapper">
                    <!-- Dropdown para seleção de fase -->
                    <Dropdown
                        filter
                        id="slctFase"
                        v-model="form.slctFase.data"
                        :options="fases"
                        option-label="name"
                        placeholder="Selecione uma Fase"
                        @change="buscarEscolas">
                    </Dropdown>
                </div>
                <div v-if="escolas.data.length > 0">
                    <Fieldset style="width: 50vw; margin: 0 auto 1rem;" legend="Escolas">
                        <div class="select-escolas">
                            <!-- Multiselect para seleção de escolas -->
                            <MultiSelect
                                id="selectEtapas"
                                v-model="form.slctEscola.data"
                                :options="escolas.data"
                                filter
                                option-label="name"
                                placeholder="Selecione as Escolas"
                                :maxSelectedLabels="1"
                                @change="verificaButtons"
                            >
                                <template #value>
                                    <span v-if="form.slctEscola.data.length <= 0">Selecione as Escolas</span>
                                    <span v-else>
                                    <b>{{ form.slctEscola.data.length }}</b>
                                    escola{{ form.slctEscola.data.length > 1 ? 's' : '' }}
                                    selecionada{{ form.slctEscola.data.length > 1 ? 's' : '' }}
                                </span>
                                </template>
                            </MultiSelect>
                        </div>
                        <!-- Botão para buscar etapas -->
                        <div style="text-align: center; margin-top: 1rem">
                            <Button icon="pi pi-search" label="Buscar Etapas" @click="buscarEtapas" :disabled="btnEtapas"/>
                        </div>
                    </Fieldset>
                </div>
                <div v-if="etapas.data.length > 0">
                    <Fieldset style="width: 50vw; margin: 0 auto 1rem;" legend="Etapas">
                        <div class="select-etapas">
                            <!-- Multiselect para seleção de etapas -->
                            <MultiSelect
                                id="selectEtapas"
                                v-model="form.slctEtapa.data"
                                :options="etapas.data"
                                filter
                                option-label="name"
                                placeholder="Selecione as Etapas"
                                :maxSelectedLabels="1"
                                @change="verificaButtons">
                                <template #value>
                                    <span v-if="form.slctEtapa.data.length <= 0">Selecione as Etapas</span>
                                    <span v-else>
                                    <b>{{ form.slctEtapa.data.length }}</b>
                                    etapa{{ form.slctEtapa.data.length > 1 ? 's' : '' }}
                                    selecionada{{ form.slctEtapa.data.length > 1 ? 's' : '' }}
                                </span>
                                </template>
                            </MultiSelect>
                        </div>
                    </Fieldset>
                    <!-- Botão para emitir o relatório -->
                    <div class="button-wrap">
                        <Button icon="pi pi-file-pdf" label="Emitir Relatório em PDF" @click="emitirRelatorio(true)" :disabled="btnEmitir" raised severity="danger"/>
                        <Button icon="pi pi-file-excel" label="Emitir Relatório em CSV" @click="emitirRelatorio(false)" :disabled="btnEmitir" raised severity="success"/>
                    </div>
                </div>
            </Fieldset>
        </div>
        <div v-else>
            <div class="card">
                <Image :src="imgSrc" alt="Imagem em desenho de uma mulher sentada em cima de um livro lendo outro livro e um homem em pé com uma lupa enorme nas mãos!" width="500"/>
            </div>
        </div>
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
    <ModalLoading :is-loading="loading" :message="mensagemModal"/>
</template>
<script setup>

import ModalLoading from "@modules/Components/ModalLoading.vue"
import {onMounted, ref} from 'vue';
import { useToast } from "primevue/usetoast";
import Dropdown from 'primevue/dropdown';
import SelectButton from 'primevue/selectbutton';
import MultiSelect from 'primevue/multiselect';
import MultiDownload from "@modules/Components/MultiDownload.vue";
import Image from 'primevue/image';

const btnEtapas = ref(true);
const btnEmitir = ref(true);
const imgSrc = ref(null);
const display = ref(false);
const files = ref([]);
const form = ref({
    slctFase: {
        label: 'Fase',
        data: []
    },
    slctEtapa: {
        data: []
    },
    slctEscola: {
        data: []
    },
    slctTurno: {
        data: []
    },
});
const escolas = ref({
    data: []
});
const etapas = ref({
    data: []
});
const fases = ref([{
    name: 'Selecione uma Fase',
    code: null
}]);
const turnos = ref({
    data: []
});
const loading = ref(false);
const mensagens = {
    fases: 'Buscando Fases...',
    etapas: 'Buscando Etapas...',
    escolas: 'Buscando Escolas...',
    turnos: 'Verificando Turnos Parciais...',
    relatorio: 'Emitindo Relatório...',
};
const mensagemModal = ref(null);
const typeIsPdf = ref(null);
const routes = {
    fases: `v4/api/educacao/matricula-online/vagas/fases-abertas`,
    etapas: `v4/api/educacao/matricula-online/vagas/etapas-fases-abertas`,
    escolas: `v4/api/educacao/matricula-online/vagas/escolas-fases-abertas`,
    turnos: `v4/api/educacao/matricula-online/vagas/turnos-fases-abertas`,
    emissao: `v4/api/educacao/matricula-online/vagas/relatorio-vagas-parciais`,
};
const showBackground = ref(true);
const toast = useToast();

async function buscarFases() {
    limparVariaveis('fases');
    mensagemModal.value = mensagens.fases;
    loading.value = true;
    try {
        const response = await window.axios.get(routes.fases);
        loading.value = false;
        showBackground.value = false;
        if (response.data.data.length === 0) {
            toast.add({
                severity: 'error',
                summary: 'Atenção!',
                detail: 'Não foram encontradas fases abertas.',
                life: 10000
            });
        }
        response.data.data.map(fase => {
           fases.value.push({
               name: fase.descricao,
               code: fase.codigo,
               dataCorte: fase.dataCorte,
               dataFim: fase.dataFim,
               dataInicio: fase.dataInicio,
               ciclo: fase.ciclo,
               isProcessada: fase.isProcessada,
               isEncerrada: fase.isEncerrada,
               publicosAlvo: fase.publicosAlvo,
               exibeEscolaOrigem: fase.exibeEscolaOrigem,
               horaInicio: fase.horaInicio,
               horaFim: fase.horaFim,
               opcoesEscolha: fase.opcoesEscolha
           });
        });
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `${e.response.data.message}`,
            life: 5000
        });
        loading.value = false;
        showBackground.value = false;
    }
}
async function buscarEtapas() {
    const params = {
        fase: form.value.slctFase.data,
        escolas: form.value.slctEscola.data
    }
    limparVariaveis('etapas');
    mensagemModal.value = mensagens.etapas;
    loading.value = true;
    try {
        const response = await window.axios.post(routes.etapas, params);
        const etapasConfiguradas = Object.values(response.data.data.etapasConfiguradas);
        if (etapasConfiguradas.length > 0) {
            etapasConfiguradas.map(etapa => {
                etapas.value.data.push({
                  name: etapa.descricao,
                  code: etapa.codigo,
                });
          });
        } else {
            toast.add({
                severity: 'warn',
                summary: 'Atenção!',
                detail: 'Não existem etapas configuradas paras a(s) escola(s) selecionada(s)!',
                life: 15000
            });
        }
      loading.value = false;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `${e.response.data.message}`,
            life: 5000
        });
        loading.value = false;
    }
}
async function buscarEscolas() {
    const params = {
        fase: form.value.slctFase.data
    }
    limparVariaveis('escolas');
    mensagemModal.value = mensagens.escolas;
    loading.value = true;
    try {
        const response = await window.axios.post(routes.escolas, params);
        if (response.data.data.length > 0) {
            response.data.data.map(escola => {
                escolas.value.data.push({
                    name: escola.nome_escola,
                    code: escola.codigo_escola,
                    abreviatura: escola.abreviatura_escola,
                });
            });
        } else {
            toast.add({
                severity: 'warn',
                summary: 'Atenção!',
                detail: 'Não existem escolas configuradas para a fase selecionada!',
                life: 15000
            });
        }
        loading.value = false;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `${e.response.data.message}`,
            life: 5000
        });
        loading.value = false;
    }
}
async function buscarTurnos() {
    const params = {
        fase: form.value.slctFase.data,
        etapas: form.value.slctEtapa.data,
        escolas: form.value.slctEscola.data
    }
    mensagemModal.value = mensagens.turnos;
    loading.value = true;
    try {
        const response = await window.axios.post(routes.turnos, params);
        const responseTurnos = response.data.data;
        loading.value = false;
        if (responseTurnos.length > 0) {
            responseTurnos.map(turno => {
                turnos.value.data.push({
                    code: turno.codigo_turno,
                    name: turno.nome_turno
                })
            })
            return true;
        } else {
            toast.add({
                severity: 'warn',
                summary: 'Atenção',
                detail: 'Não foram encontrados turnos cadastrados para estas etapas selecionadas!',
                life: 15000
            });
            loading.value = false;
            return false;
        }
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `${e.response.data.message}`,
            life: 5000
        });
        loading.value = false;
        return false;
    }
}
async function emitirRelatorio(isPdf) {
    if (await buscarTurnos()) {
        loading.value = true;
        typeIsPdf.value = isPdf;
        mensagemModal.value = mensagens.relatorio;
        const params = {
            fase: form.value.slctFase.data,
            etapas: form.value.slctEtapa.data,
            escolas: form.value.slctEscola.data,
            turnos: turnos.value.data,
            isPdf: typeIsPdf.value
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
                life: 5000
            });
        } finally {
            loading.value = false;
        }
    } else {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: 'Não foram encontrados turnos parciais para estes filtros selecionados!',
            life: 15000
        });
        loading.value = false;
    }

}
function verificaButtons() {
    btnEtapas.value = form.value.slctEscola.data.length === 0;
    btnEmitir.value = form.value.slctEtapa.data.length === 0;
}
function limparVariaveis($chave) {
    switch ($chave) {
        case 'fases':
            form.value.slctFase.data = [];
            form.value.slctEtapa.data = [];
            form.value.slctEscola.data = [];
            form.value.slctTurno.data = [];
            turnos.value.data = [];
            etapas.value.data = [];
            escolas.value.data = [];
            break;
        case 'escolas':
            form.value.slctEscola.data = [];
            form.value.slctTurno.data = [];
            form.value.slctEtapa.data = [];
            escolas.value.data = [];
            turnos.value.data = [];
            etapas.value.data = [];
            break;
        case 'etapas':
            form.value.slctEtapa.data = [];
            form.value.slctTurno.data = [];
            turnos.value.data = [];
            etapas.value.data = [];
            break;
        case 'turnos':
            form.value.slctTurno.data = [];
            turnos.value.data = [];
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

onMounted(() => {
    showBackground.value = true;
    let srcPage = window.ECIDADE_PATH;
    imgSrc.value = srcPage + 'imagens/educacao/matricula-online/sem_fases_abertas.png';
    buscarFases();
});

</script>
<style lang="scss" scoped>

.container-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #E0E0E0;
    max-width: 70%;
    margin: 2rem auto;
    padding: 2rem;
    box-sizing: border-box;
    border-radius: 0.6rem;
}

.fase-wrapper,
.select-etapas,
.select-escolas,
.select-turnos {
    margin-top: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

#link-download {
    text-decoration: none;
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    justify-content: center;
    text-align: center;
    gap: 1rem;
}

.background {
    background-color: #E0E0E0;
    width: 100vw;
    height: 100vh;
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
