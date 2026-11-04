<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import Calendar from "primevue/calendar";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import MultiDownload from "@modules/Components/MultiDownload.vue";

// props
const props = defineProps(['exercicio', 'instituicao', 'dataSistema']);
const toast = useToast();

const loading = ref(false);
const mensagemLoad = ref(null);
const download = ref();

// datas filtro período
const dataSistema = new Date(`${props.dataSistema}T00:00:00`);
const dataInicial = ref(dataSistema);
const minDate = ref(new Date(`${props.exercicio}-01-01T00:00:00`));
const maxDate = ref(new Date(`${props.exercicio}-12-31T00:00:00`));
const dataFinal = ref(maxDate.value);

const rota = 'v4/api/financeiro/tesouraria/decendio/relatorio';

async function imprimir() {
    const paramentros = {
        dataInicial: dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        instituicao: props.instituicao
    };

    loading.value = true;
    mensagemLoad.value = 'Emitindo relatório de Decêndio, aguarde.';
    await window.axios.post(rota, paramentros).then(response => {
        download.value.addFile(
            response.data.data.pdfLinkExterno,
            'Relatório de Decêndio'
        )
        download.value.openModal();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
}
</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Filtre o período" class="w-full md:w-11 lg:w-8 xl:w-6">
                <div class="formgrid grid mt-4 row-gap-2">
                    <div class="field col-12 sm:col:4 md:col-4 lg:col-3 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dataInicial" dateFormat="dd/mm/yy"
                                      :minDate="minDate" :maxDate="dataFinal"
                                      showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 150px;"/>
                            <label for="lb-periodo">Período de</label>
                        </div>
                    </div>

                    <div class="field col-12 sm:col:4 md:col-4 lg:col-3 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dataFinal" dateFormat="dd/mm/yy"
                                      :minDate="dataInicial" :maxDate="maxDate"
                                      showIcon placeholder="Data Final" showButtonBar :manualInput=true
                                      inputId="lb-periodo-ate"
                                      style="width: 150px;"/>
                            <label for="lb-periodo-ate"> Até</label>
                        </div>
                    </div>
                    <div class="field col-12 sm:col:4 md:col-4 lg:col-6 xl:col-6">
                        <Button type="button" label="Imprimir" icon="pi pi-print" @click="imprimir"/>
                    </div>
                </div>
            </Panel>
        </section>
    </section>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"></MultiDownload>
</template>
