<script setup>
import {ref} from "vue";
import Calendar from "primevue/calendar";
import {useToast} from "primevue/usetoast";
import MultiSelectInstituicao from "../../../../../Configuracao/Components/MultiSelectInstituicao.vue";
import ModalLoading from "../../../../../Components/ModalLoading.vue";
import MultiDownload from "../../../../../Components/MultiDownload.vue";
import ChipsEstruturais from "@modules/Financeiro/Contabilidade/Components/ChipsEstruturais.vue";
import DropdownIndicadorSuperavit from "@modules/Financeiro/Contabilidade/Components/DropdownIndicadorSuperavit.vue";
import DropdownSistemaContas from "@modules/Financeiro/Contabilidade/Components/DropdownSistemaContas.vue";
import CheckboxEncerramento from "@modules/Financeiro/Contabilidade/Components/CheckboxEncerramento.vue";
import CheckboxApenasContaComMovimento
    from "@modules/Financeiro/Contabilidade/Components/CheckboxApenasContaComMovimento.vue";

const toast = useToast();
const props = defineProps(['exercicio', 'dataSistema']);


const rota = 'v4/api/financeiro/contabilidade/relatorio/balancete/verificacao/informacao-complementar';

const loading = ref(false);
const mensagemLoad = ref(null);
const multiDownload = ref(null);
const instituicoes = ref([]);
//opcionais
const estruturais = ref([]);
const indicadorSuperavit = ref('T');
const sistemaContas = ref('99');
const comEncerramento = ref(false);
const contasComMovimento = ref(true);

const minDate = ref(new Date(`${props.exercicio}-01-01T00:00:00`));
const maxDate = ref(new Date(`${props.exercicio}-12-31T00:00:00`));

const dataInicial = ref(minDate.value);
const dataFinal = ref(maxDate.value);

function validaForm() {
    try {
        if (instituicoes.value.length === 0) {
            throw 'Selecione ao menos uma instituição!'
        }
        if (dataInicial.value > dataFinal.value) {
            throw 'Data inicial não pode ser maior que a data final.'
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

const imprimir = async () => {

    if (!validaForm()) {
        return false;
    }

    loading.value = true;
    mensagemLoad.value = 'Emitindo balancete de verificação por informação complementar, aguarde.';

    const parameters = {
        exercicio: props.exercicio,
        instituicoes: instituicoes.value.map(value => value.code),
        dataInicial: dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        comEncerramento: comEncerramento.value,
        estruturais: estruturais.value,
        indicadorSuperavit: indicadorSuperavit.value,
        sistemaContas: sistemaContas.value,
        contasComMovimento: contasComMovimento.value,
    }

    await window.axios.post(rota, parameters).then(response => {
        multiDownload.value.addFile(
            `${response.data.data.csvLinkExterno}`,
            'Balancete de Verificação - CSV'
        );

        multiDownload.value.openModal();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
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
                            <MultiSelectInstituicao v-model="instituicoes" inputId="dd-instituicao" class="w-full"/>
                            <label for="dd-instituicao">Instituições</label>
                        </div>
                    </div>


                </div>
            </Panel>
        </section>

        <section class="flex justify-content-center">
            <Panel header="Filtros Opcionais" class="w-full md:w-11 lg:w-8 xl:w-6">
                <div class="formgrid grid mt-2 row-gap-2">
                    <div class="field col-12">
                        <ChipsEstruturais v-model="estruturais" />
                    </div>

                     <div class="field col-12 md:col-6">
                         <DropdownIndicadorSuperavit v-model="indicadorSuperavit" />
                     </div>

                    <div class="field col-12 md:col-6">
                        <DropdownSistemaContas v-model="sistemaContas" />
                    </div>

                    <div class="field col-12 md:col-6">
                        <CheckboxEncerramento v-model="comEncerramento" />
                    </div>
                    <div class="field col-12 md:col-6">
                        <CheckboxApenasContaComMovimento v-model="contasComMovimento" />
                    </div>
                </div>
            </Panel>
        </section>

        <section class="flex justify-content-center flex-wrap card-container pt-5">
            <Button type="button" label="Imprimir" icon="pi pi-print" @click="imprimir"/>
        </section>
        <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
        <MultiDownload ref="multiDownload" :header="'Relatórios'" :position="'center'"></MultiDownload>
    </section>
</template>

<style scoped>

</style>
