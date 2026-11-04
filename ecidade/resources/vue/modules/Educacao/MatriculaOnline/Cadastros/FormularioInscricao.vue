<script setup>
import { ref, onMounted } from "vue"
import ModalLoading from "../../../Components/ModalLoading.vue";
import { useToast } from 'primevue/usetoast';
import Stepper from 'primevue/stepper';

import { ProcessoInscricaoService } from "../Services/ProcessoInscricaoService.js";
import { Inscricao } from "../Models/Inscricao.js";
import StepperPanel from 'primevue/stepperpanel';
import PrimeiraEtapa from "../Components/PrimeiraEtapa.vue";
import DadosCandidato from "../Components/DadosCandidato.vue";
import Documentos from "../Components/Documentos.vue";
import Endereco from "../Components/Endereco.vue";
import OpcoesEscola from "../Components/OpcoesEscola.vue";
import DadosResponsavel from "../Components/DadosResponsavel.vue";
import Confirmacao from "../Components/Confirmacao.vue";
import Protocolo from "../Components/Protocolo.vue";

const inscricaoService = new ProcessoInscricaoService()

const temFaseAberta = ref(null)
const stepper = ref()
const step = ref()
const loading = ref(false)
const toast = useToast()

const parametros = ref()
const liberaProximo = ref(false)
const liberaVoltar = ref(false)
const dadosProtocolo = ref(null)

const salvarFormulario = (dados) => {
    liberaProximo.value = !dados.camposPreenchidos
    if (dados.camposPreenchidos) {
        inscricaoService.salvarFormularioStorage(dados)
    }
}

const salvarInscricao = async () => {
    try{
        loading.value = true
        const inscricao = new Inscricao()
        let res = await window.axios.post('v4/api/educacao/central-de-matriculas/processo-inscricao/inscricao', inscricao.toRequest())
        loading.value = false
        dadosProtocolo.value = res.data.data;
    } catch (e) {
        console.log(e)
        loading.value = false
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Contate um administrador! '+e.message, life: 5000 });
    }
}

const limpaFormulario = () => {
    inscricaoService.limparFormularioStorage()
}

onMounted(async () => {
    parametros.value = (await window.axios.get('v4/api/educacao/files/parametros')).data
    if (inscricaoService.getCampoFormularioStorage('protocolo') === null) {
        inscricaoService.limparFormularioStorage()
    }
    await inscricaoService.buildTemFaseAberta()
    temFaseAberta.value = inscricaoService.getStorageTemFaseAberta()
    if (inscricaoService.getCampoFormularioStorage('protocolo') !== null) {
        temFaseAberta.value = true
    }
    
})
</script>
<template>
    <section class="container">
        <div class="card mt-3">
            <Protocolo @fechar-inscricao="() => dadosProtocolo = null" :dados="dadosProtocolo" v-if="dadosProtocolo"/>
            <Stepper linear v-model="step" v-if="!dadosProtocolo">
                <StepperPanel header="Etapa 1">
                    <template #content="{ nextCallback }">
                        {{ activeStep }}
                        <div class="flex flex-column">
                            <PrimeiraEtapa @limpar-formulario="limpaFormulario" @campos-preenchidos="(val) => salvarFormulario(val)" />
                        </div>
                        <div class="flex pt-4 justify-content-end">
                            <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right" @click="nextCallback" :disabled="liberaProximo" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Dados do Candidato">
                    <template #content="{ active, prevCallback, nextCallback }">
                        <div class="flex flex-column">
                            <DadosCandidato v-if="active" @campos-preenchidos="(val) => salvarFormulario(val)" />
                        </div>
                        <div class="flex pt-4 justify-content-between">
                            <Button label="Voltar" severity="secondary" icon="pi pi-arrow-left" @click="prevCallback" />
                            <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right" @click="nextCallback" :disabled="liberaProximo" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Documentos">
                    <template #content="{ active, prevCallback, nextCallback }">
                        <div class="flex flex-column">
                            <Documentos v-if="active" @campos-preenchidos="(val) => salvarFormulario(val)" />
                        </div>
                        <div class="flex pt-4 justify-content-between">
                            <Button label="Voltar" severity="secondary" icon="pi pi-arrow-left" @click="prevCallback" />
                            <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right" @click="nextCallback" :disabled="liberaProximo" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Endereço">
                    <template #content="{ active, prevCallback, nextCallback }">
                        <div class="flex flex-column">
                            <Endereco v-if="active" @campos-preenchidos="(val) => salvarFormulario(val)" />
                        </div>
                        <div class="flex pt-4 justify-content-between">
                            <Button label="Voltar" severity="secondary" icon="pi pi-arrow-left" @click="prevCallback" />
                            <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right" @click="nextCallback" :disabled="liberaProximo" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Opções de Escola">
                    <template #content="{ active, prevCallback, nextCallback }">
                        <div class="flex flex-column">
                            <OpcoesEscola v-if="active" @campos-preenchidos="(val) => salvarFormulario(val)" />
                        </div>
                        <div class="flex pt-4 justify-content-between">
                            <Button label="Voltar" severity="secondary" icon="pi pi-arrow-left" @click="prevCallback" />
                            <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right" @click="nextCallback" :disabled="liberaProximo" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Dados do Responsável">
                    <template #content="{ active, prevCallback, nextCallback }">
                        <div class="flex flex-column">
                            <DadosResponsavel v-if="active" @campos-preenchidos="(val) => salvarFormulario(val)" />
                        </div>
                        <div class="flex pt-4 justify-content-between">
                            <Button label="Voltar" severity="secondary" icon="pi pi-arrow-left" @click="prevCallback" />
                            <Button label="Próximo" icon="pi pi-arrow-right" iconPos="right" @click="nextCallback" :disabled="liberaProximo" />
                        </div>
                    </template>
                </StepperPanel>
                <StepperPanel header="Confirmação">
                    <template #content="{ active, prevCallback }">
                        <div class="flex flex-column">
                            <Confirmacao v-if="active" />
                        </div>
                        <div class="flex pt-4 justify-content-between">
                            <Button label="Voltar" severity="secondary" icon="pi pi-arrow-left" @click="prevCallback" />
                            <Button label="Confirmar" icon="pi pi-check" iconPos="right" @click="salvarInscricao" />
                        </div>
                    </template>
                </StepperPanel>
            </Stepper>
        </div>
        <ModalLoading :isLoading="loading"/>
    </section>
</template>

<style scoped>
    section.container { max-width: 1400px;}
</style>