<script setup>
import {computed, onMounted, ref} from "vue";
import Calendar from "primevue/calendar";
import {useToast} from "primevue/usetoast";

const toast = useToast();
const props = defineProps(['modelValue', 'dataMinima', 'dataSistema']);
const emit = defineEmits(['update:modelValue'])

const dados = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});


const rota = 'v4/api/financeiro/contabilidade/lancamento-manual/proximo-lote';


const validaFormulario = () => {
    try {
        if (!dados.value.lote) {
            throw 'Informe o Lote.';
        }
    } catch (e) {
        toast.add({
            severity: 'warn',
            detail: e,
            summary: 'Aviso'
        });
        return false
    }
    return true;
}

const abaLancamento = () => {

    if (!validaFormulario()) {
        return false;
    }

    dados.value.aba.ativa = 1
}

onMounted(() => {
    dados.value.dataLancamento = props.dataSistema;
    const parameters = {
        exercicio : dados.value.exercicio,
        instituicao : dados.value.instituicao
    }

    window.axios.get(rota, {params:parameters}).then(response => {
        dados.value.lote = response.data.data;
    })
})

</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Informe o lote para prosseguir" class="w-full md:w-11 lg:w-8 xl:w-6">
                <div class="formgrid grid mt-4 row-gap-2">

                    <div class="field col-12 md:col-6 lg:col-5 xl:col-3">
                        <span class="p-float-label">
                            <InputText id="input-lote" v-model="dados.lote" disabled type="text" class="w-full"/>
                            <label for="input-lote">Lote</label>
                        </span>
                    </div>

                    <div class="field col-12 md:col-6 lg:col-5 xl:col-3">
                        <div class="p-float-label">
                            <Calendar v-model="dados.dataLancamento" dateFormat="dd/mm/yy"
                                      :minDate="dataMinima" :maxDate="dataSistema"
                                      showIcon :manualInput="true" placeholder="Data Inicial" inputId="lb-periodo"
                                      style="width: 180px;"/>
                            <label for="lb-periodo">Data do Lançameto</label>
                        </div>
                    </div>

                    <div class="field col-12 md:col-6 lg:col-5 xl:col-3">
                        <Button type="button" label="Próximo" icon="pi pi-chevron-right" iconPos="right"
                                @click="abaLancamento"/>
                    </div>
                </div>
            </Panel>
        </section>

    </section>

</template>

<style scoped>

</style>
