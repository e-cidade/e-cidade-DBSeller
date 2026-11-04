<script setup>
import {computed, ref} from "vue";
import Calendar from "primevue/calendar";
import {useToast} from "primevue/usetoast";
import MultiSelectInstituicao from "../../../../../Configuracao/Components/MultiSelectInstituicao.vue";
import ModalLoading from "../../../../../Components/ModalLoading.vue";
import MultiDownload from "../../../../../Components/MultiDownload.vue";
import ChipsEstruturais from "@modules/Financeiro/Contabilidade/Components/ChipsEstruturais.vue";
import CheckboxApenasContaComMovimento
    from "@modules/Financeiro/Contabilidade/Components/CheckboxApenasContaComMovimento.vue";

const toast = useToast();
const props = defineProps(['modelValue', 'exercicio', 'dataSistema']);
const emit = defineEmits(['update:modelValue'])

const filtro = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const rotas = {
    ecidade: 'v4/api/financeiro/contabilidade/relatorio/balancete-receita-por-complemento',
    plano_padrao: 'v4/api/financeiro/contabilidade/relatorio/balancete-receita-plano-padrao'
};

const loading = ref(false);
const mensagemLoad = ref(null);
const multiDownload = ref(null);
const instituicoes = ref([]);
const estruturais = ref([]);

const minDate = ref(new Date(`${props.exercicio}-01-01T00:00:00`));
const maxDate = ref(new Date(`${props.exercicio}-12-31T00:00:00`));

filtro.value.obrigatorio.dataInicial = minDate.value;
filtro.value.obrigatorio.dataFinal = props.dataSistema

const tipoPlanoOpcoes = ref([
    {name: 'Plano e-Cidade', code: 'ecidade'},
    {name: 'Plano União/Federação', code: 'uniao'},
    {name: 'Plano Estadual/Regional', code: 'estadual'},
]);

const tipoAgrupamento = ref([
    {name: 'Lista Deduções Grupo 9', code: 0},
    {name: 'Deduções no Mesmo Grupo', code: 2},
]);

const apresentarFonteRecurso = ref([
    {name: 'Fonte Recurso', code: 3},
    {name: 'Subrecurso', code: 9},
    {name: 'Código Siconfi', code: 7},
]);

function validaForm() {
    try {
        if (filtro.value.obrigatorio.instituicoes.length === 0) {
            throw 'Selecione ao menos uma instituição!'
        }
        if (filtro.value.obrigatorio.dataInicial > filtro.value.obrigatorio.dataFinal) {
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
    mensagemLoad.value = 'Emitindo balancete da receita. aguarde.';

    const parameters = {
        exercicio: props.exercicio,
        instituicoes: JSON.stringify(filtro.value.obrigatorio.instituicoes.map(function (value) {
            return { "codigo": value.code, "nome": value.name }
        })),
        dataInicio: filtro.value.obrigatorio.dataInicial.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: filtro.value.obrigatorio.dataFinal.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        ementario: filtro.value.obrigatorio.tipoPlano,
        // opicionais
        natureza: filtro.value.opcionais.estruturais.map(value => value).join(','),
        nivelAgrupar: filtro.value.opcionais.tipoAgrupamento,
        apenasComMovimentacao: filtro.value.opcionais.contasComMovimento,
        apresentar:filtro.value.opcionais.apresentarFonteRecurso,
        //recursos
        recursos: filtro.value.recursos.map(recurso => recurso.o15_codigo)
    }

    let rota = rotas.ecidade;
    if (parameters.ementario !== 'ecidade') {
        rota = rotas.plano_padrao
    }

    await window.axios.post(rota, parameters).then(response => {

        multiDownload.value.addFile(
            `${response.data.data.pdfLinkExterno}`,
            'Balancete da Receita - PDF'
        );

        multiDownload.value.addFile(
            `${response.data.data.csvLinkExterno}`,
            'Balancete de Receita - CSV'
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
                            <Calendar v-model="filtro.obrigatorio.dataInicial" dateFormat="dd/mm/yy"
                                      :minDate="minDate" :maxDate="filtro.obrigatorio.dataFinal"
                                      showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 150px;"/>
                            <label for="lb-periodo">Período de</label>
                        </div>
                    </div>

                    <div class="field col-12  md:col-6 lg:col-7 xl:col-9">
                        <div class="p-float-label">
                            <Calendar v-model="filtro.obrigatorio.dataFinal" dateFormat="dd/mm/yy"
                                      :minDate="filtro.obrigatorio.dataInicial" :maxDate="maxDate"
                                      showIcon placeholder="Data Final" showButtonBar :manualInput=true
                                      inputId="lb-periodo-ate"
                                      style="width: 150px;"/>
                            <label for="lb-periodo-ate"> Até</label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label">
                            <MultiSelectInstituicao v-model="filtro.obrigatorio.instituicoes" inputId="dd-instituicao"
                                                    class="w-full"></MultiSelectInstituicao>
                            <label for="dd-instituicao">Instituições</label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label">
                            <Dropdown v-model="filtro.obrigatorio.tipoPlano" inputId="dd-tipo-plano"
                                      :options="tipoPlanoOpcoes" optionLabel="name" option-value="code" class="w-full"/>
                            <label for="dd-tipo-plano">Selecione o plano que deseja agrupar os dados</label>
                        </div>
                    </div>

                </div>
            </Panel>
        </section>

        <section class="flex justify-content-center">
            <Panel header="Filtros Opcionais" class="w-full md:w-11 lg:w-8 xl:w-6">
                <div class="formgrid grid mt-2 row-gap-2">
                    <div class="field col-12">
                        <div class="p-float-label">
                            <ChipsEstruturais v-model="filtro.opcionais.estruturais"></ChipsEstruturais>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label">
                            <Dropdown v-model="filtro.opcionais.tipoAgrupamento" inputId="dd-tipo-agrupamento"
                                      :options="tipoAgrupamento" optionLabel="name" option-value="code" class="w-full"/>
                            <label for="dd-tipo-agrupamento">Tipo de Agrupamento das Deduções: </label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label">
                            <Dropdown v-model="filtro.opcionais.apresentarFonteRecurso" inputId="dd-fr"
                                      :options="apresentarFonteRecurso" optionLabel="name" option-value="code" class="w-full"/>
                            <label for="dd-fr">Apresentar Fonte Recurso</label>
                        </div>
                    </div>


                    <div class="field-checkbox col-12 md:col-6 ">
                        <CheckboxApenasContaComMovimento v-model="filtro.opcionais.contasComMovimento" />
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
