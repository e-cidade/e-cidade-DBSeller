<script setup>
import {computed, ref} from "vue";
import {useToast} from "primevue/usetoast";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import MultiDownload from "@modules/Components/MultiDownload.vue";
import MultiSelectInstituicao from "@modules/Configuracao/Components/MultiSelectInstituicao.vue";
import Calendar from "primevue/calendar";

const toast = useToast();
const props = defineProps(['modelValue', 'dataSistema']);
const emit = defineEmits(['update:modelValue']);

const filtro = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const loading = ref(false);
const multiDownload = ref(null);

const dataSistema = new Date(`${props.dataSistema}T00:00:00`);
const minDate = ref(new Date(`${filtro.value.exercicio}-01-01T00:00:00`));
const maxDate = ref(new Date(`${filtro.value.exercicio}-12-31T00:00:00`));

filtro.value.dataInicial = minDate;
filtro.value.dataFinal = maxDate.value;

const apresentarRecursos = ref([
    {name: 'Apenas Siconfi', code:1},
    {name: 'Siconfi + CO', code:2},
    {name: 'Siconfi + Subrecurso + CO', code:3}
]);

const rota = 'v4/api/financeiro/contabilidade/relatorio/superavit-por-recurso';

function valida() {
    try {
        if (filtro.value.instituicoes.length === 0) {
            throw 'Selecione ao menos uma instituição!'
        }
        if (filtro.value.dataInicial > filtro.value.dataFinal) {
            throw 'Data inicial não pode ser maior que a data final.'
        }
        if (filtro.value.apresentarRecurso === null) {
            throw 'Selecione a forma que deseja agrupar valores.'
        }
    } catch (e) {
        toast.add({severity: 'warn', detail: e, summary: 'Aviso'});
        return false;
    }
    return true;
}

async function imprimir() {
    if (!valida()) {
        return;
    }

    const parametros = {
        dataInicial: filtro.value.dataInicial.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: filtro.value.dataFinal.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'}),
        apresentarRecurso: filtro.value.apresentarRecurso,
        instituicoes: filtro.value.instituicoes.map(value => value.code),
        recursos: filtro.value.recursos.map(recurso => recurso.o15_codigo),
    }

    loading.value = true;
    await window.axios.post(rota, parametros).then(response => {
        multiDownload.value.addFile(
            `${response.data.data.xlsLinkExterno}`,
            'Superávit Financeiro por Recurso'
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
            <Panel header="Filtros" class="w-full md:w-11 lg:w-8 xl:w-5">
                <div class="formgrid grid mt-4 row-gap-2">

                    <div class="field col-12 md:col-6 lg:col-5 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="filtro.dataInicial" dateFormat="dd/mm/yy"
                                      :minDate="minDate" :maxDate="maxDate"
                                      showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 150px;"/>
                            <label for="lb-periodo">Período de</label>
                        </div>
                    </div>

                    <div class="field col-12  md:col-6 lg:col-7 xl:col-9">
                        <div class="p-float-label">
                            <Calendar v-model="filtro.dataFinal" dateFormat="dd/mm/yy"
                                      :minDate="filtro.dataInicial" :maxDate="maxDate"
                                      showIcon placeholder="Data Final" showButtonBar :manualInput=true
                                      inputId="lb-periodo-ate"
                                      style="width: 150px;"/>
                            <label for="lb-periodo-ate"> Até</label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label">
                            <MultiSelectInstituicao v-model="filtro.instituicoes" inputId="dd-instituicao"
                                                    class="w-full"></MultiSelectInstituicao>
                            <label for="dd-instituicao">Instituições</label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label">
                            <Dropdown v-model="filtro.apresentarRecurso" inputId="dd-apresentar" class="w-full"
                                      :options="apresentarRecursos" optionLabel="name" option-value="code" />
                            <label for="dd-apresentar">Agrupar valores por</label>
                        </div>
                    </div>
                </div>
            </Panel>
        </section>

        <section class="flex justify-content-center flex-wrap card-container pt-1 gap-2">
            <Button type="button" label="Imprimir" icon="pi pi-print" @click="imprimir"/>
        </section>
    </section>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    <MultiDownload ref="multiDownload" :header="'Relatórios'" :position="'center'"></MultiDownload>
</template>

<style scoped>

</style>
