<script setup>
import FiltrosFolha from "../Components/FiltrosFolha.vue";
import AncoraRubrica from "../Components/AncoraRubrica.vue";
import MultiDownload from "../../../Components/MultiDownload.vue";
import ModalLoading from "../../../Components/ModalLoading.vue";
import { useToast } from "primevue/usetoast";
import { ref, computed } from "vue";

const filtroFolha = ref(null);
const mes = ref(null);
const ano = ref(null);
const rubricaRef = ref(null);
const tipo = ref({ code: 1, name: 'PDF' });
const loading = ref(false);
const download = ref(null);
const tipoEmissao = ref([
    { code: 1, name: 'PDF' },
    { code: 2, name: 'CSV' }
]);
const toast = useToast();

const dataFiltroFolha = computed(() => filtroFolha.value?.data);

async function emitir() {

    try {
  
       loading.value = true;

        let parametros = {
            rubrica: rubricaRef.value.codigoRubrica,
            tipo_resumo : dataFiltroFolha.value.tipoResumo?.code,
            tipo_filtro : dataFiltroFolha.value.tipoFiltro?.code,
            inicio : dataFiltroFolha.value.inicio,
            fim : dataFiltroFolha.value.fim,
            registros : dataFiltroFolha.value.registros,
            ano : ano.value,
            mes : mes.value,
            tipo: tipo.value?.code,
        };

        let messageValidation = [];

        if (parametros.tipo_resumo > 1 && parametros.tipo_filtro > 1) {

            if (parametros.tipo_filtro == 2) {
                if(!parametros.inicio || !parametros.fim) {
                    messageValidation.push('Registro inicial e final devem ser informados.')
                }

                if(parametros.inicio > parametros.fim) {
                    messageValidation.push('Registro inicial não pode ser maior que o final.')
                }
            }

            if (parametros.tipo_filtro == 3) {
                if (parametros.registros.length == 0) {
                    messageValidation.push('Nenhum registro informado.')
                }
            }
        }

        if (!parametros.ano || !parametros.mes) {
            messageValidation.push('Competência não informada.');
        }

        if (messageValidation.length > 0) {
            loading.value = false;
            toast.add({
                severity: 'warn',
                summary: 'Alerta',
                detail: messageValidation.join('\n'),
                life: 5000
            });
            return;
        }

        await window.axios.post('v4/api/recursos-humanos/pessoal/rhrubricas/gerar-observacoes', parametros).then(response => {
            loading.value = false;

            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: `Gerado com sucesso`,
                life: 5000
            });

            download.value.addFile(
                `${response.data.data.pathExterno}`,
                `${response.data.data.name}`
            );
            download.value.openModal();
        })
    } catch (e) {
        loading.value = false
        let message = e.response ? e.response.data.message : e.message;
     
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: message,
            life: 5000
        });
    }
}

</script>
<template>
    <ModalLoading :is-loading="loading"/>
    <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
    <section class="container">
        <Panel header="Histórico de Rubrica do Servidor">
            <div class="flex justify-content-center flex-column">
                    <div class="flex justify-content-center align-items-center">
                        <label class="font-bold	mr-2">Competência: </label>
                        <InputNumber placeholder="Mês" v-model="mes" :max="12" :min="1" inputStyle="width: 50px;"
                            :useGrouping="false" />
                        /
                        <InputNumber placeholder="Ano" v-model="ano" inputStyle="width: 70px;" :max="2030" :min="1900"
                            :useGrouping="false" />
                    </div>
                    <div class="flex justify-content-center py-4">
                        <FiltrosFolha ref="filtroFolha"></FiltrosFolha>
                    </div>
                    <div class="flex justify-content-center mt-1">
                        <AncoraRubrica ref="rubricaRef" />
                    </div>
                    <div class="flex justify-content-center mt-4 align-items-center">
                        <label class="font-bold	mr-2" for="tipoEmissao">Tipo de emissão:</label>
                        <Dropdown v-model="tipo" :options="tipoEmissao" optionLabel="name"
                            inputId="tipoEmissao"
                            placeholder="Tipo de Emissão"/>
                            
                    </div>
                </div>
                <div class="flex justify-content-center align-items-center mt-1">
                    <Button 
                        label="Gerar" 
                        class="p-button-sm mt-3"
                        @click="emitir"
                    >
                        Emitir
                    </Button>
                </div>
        </Panel>
    </section>
</template>