<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import Calendar from "primevue/calendar";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import MultiDownload from "@modules/Components/MultiDownload.vue";
import DropdownInstituicao from "@modules/Configuracao/Instituicao/Components/DropdownInstituicao.vue";

const props = defineProps(['exercicio', 'dataSistema', 'instituicao']);
const toast = useToast();

const dataSistema = new Date(`${props.dataSistema}T00:00:00`);
const minDate = ref(new Date(`${props.exercicio}-01-01T00:00:00`));
const maxDate = ref(new Date(`${props.exercicio}-12-31T00:00:00`));
const dataInicial = ref(dataSistema);
const dataFinal = ref(dataSistema);
const tipoConta = ref([]);
const multiDownload = ref(null);
const loading = ref(false);

const instituicao = ref(props.instituicao);

const rota = 'v4/api/financeiro/contabilidade/deliberacao/285/modelo-5';

function validaForm() {
    try {
        if (dataInicial.value > dataFinal.value) {
            throw 'Data inicial não pode ser maior que a data final.'
        }
        if (tipoConta.value.length === 0) {
            throw 'Informe ao menos um tipo de conta. (Fundeb ou MDE)'
        }
    } catch (e) {
        toast.add({
            severity: 'warn',
            detail: e,
            summary: 'Aviso'
        });
        return false
    }
    return true
}

async function imprimir() {
    if (!validaForm()) {
        return false;
    }

    loading.value = true;

    const parameters = {
        exercicio: props.exercicio,
        instituicao: instituicao.value,
        dataInicial: dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        tipoConta: tipoConta.value
    }

    await window.axios.post(rota, parameters).then(response => {
        multiDownload.value.addFile(
            `${response.data.data.pdfLinkExterno}`,
            'Deliberação 285 - Modelo 5 - PDF'
        );
        multiDownload.value.addFile(
            `${response.data.data.csvLinkExterno}`,
            'Deliberação 285 - Modelo 5 - CSV'
        );

        multiDownload.value.openModal();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
}


const selectInstit = (data) => {
    instituicao.value = data.codigo;
}
</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Filtros Obrigatórios" class="w-full md:w-11 lg:w-8 xl:w-6">
                <div class="formgrid grid mt-4 row-gap-2">

                    <div class="field col-12 md:col-6 lg:col-5 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dataInicial" dateFormat="dd/mm/yy"
                                      :minDate="minDate" :maxDate="dataFinal"
                                      showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 150px;"/>
                            <label for="lb-periodo">Período de</label>
                        </div>
                    </div>

                    <div class="field col-12  md:col-6 lg:col-7 xl:col-9">
                        <div class="p-float-label">
                            <Calendar v-model="dataFinal" dateFormat="dd/mm/yy"
                                      :minDate="dataInicial" :maxDate="maxDate"
                                      showIcon placeholder="Data Final" showButtonBar :manualInput=true
                                      inputId="lb-periodo-ate"
                                      style="width: 150px;"/>
                            <label for="lb-periodo-ate"> Até</label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label">
                            <DropdownInstituicao @change="selectInstit" :instit="instituicao"/>
                            <label for="instit">Instituição: </label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="card flex flex-wrap justify-content-center gap-3">
                            <div class="flex align-items-center">
                                <Checkbox v-model="tipoConta" inputId="fundeb" name="tipoConta" value="fundeb"/>
                                <label for="fundeb" class="ml-2"> FUNDEB </label>
                            </div>
                            <div class="flex align-items-center">
                                <Checkbox v-model="tipoConta" inputId="mde" name="tipoConta" value="mde"/>
                                <label for="mde" class="ml-2"> MDE </label>
                            </div>
                        </div>
                    </div>
                </div>
            </Panel>
        </section>
        <section class="flex justify-content-center flex-wrap card-container pt-5">
            <Button type="button" label="Imprimir" icon="pi pi-print" @click="imprimir"/>
        </section>

        <ModalLoading :isLoading="loading" message="Emitindo documento, aguarde."/>
        <MultiDownload ref="multiDownload" :header="'Relatórios'" :position="'center'"></MultiDownload>
    </section>
</template>

<style scoped>

</style>
