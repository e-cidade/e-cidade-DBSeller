<script setup>
import ConfirmPopup from 'primevue/confirmpopup';
import Toast from "primevue/toast";

import {ref, onMounted} from 'vue';
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import {formatCurrency} from "../../../../../utils/Strings";
import ModalLoading from "../../../../Components/ModalLoading.vue";

const confirm = useConfirm();
const toast = useToast();

const props = defineProps(['estornar']);
const telaModoEstorno = props.estornar == 1;

let msgTitulo = telaModoEstorno ? 'Estorno de Apropriação de 13ª salário e férias' : 'Apropriação de 13ª salário e férias';

const exercicio = ref(null);
const mes = ref(null);
const nomeMes = ref(null);
const valoresApropriar = ref([]);
const titulo = ref(msgTitulo);
const loading = ref(false);
const loadingCalculo = ref(false);
const loadingProcedimento = ref(false);

const routs = {
    apropriar: {
        competencia: 'v4/api/financeiro/contabilidade/procedimento/apropriacao/decimo-ferias/apropriar/competencia',
        buscarLancamentos: 'v4/api/financeiro/contabilidade/procedimento/apropriacao/decimo-ferias/apropriar/buscarValores',
        procesar: 'v4/api/financeiro/contabilidade/procedimento/apropriacao/decimo-ferias/apropriar/processar'
    },
    extornar: {
        competencia: 'v4/api/financeiro/contabilidade/procedimento/apropriacao/decimo-ferias/estornar/competencia',
        procesar: 'v4/api/financeiro/contabilidade/procedimento/apropriacao/decimo-ferias/estornar/processar'
    }
}

const getRouts = () => {
    if (telaModoEstorno) {
        return routs.extornar;
    }
    return routs.apropriar;
};


const buscarCompetencia = async () => {
    exercicio.value = null;
    mes.value = null;
    nomeMes.value = null;
    let rout = getRouts();

    try {
        const response = await window.axios.get(rout.competencia);
        exercicio.value = response.data.data.exercicio;
        mes.value = response.data.data.mes;
        nomeMes.value = response.data.data.nomeMes;
    } catch (e) {
        toast.add({severity: 'warn', detail: e.response.data.message, summary: 'Aviso', life: 8000, group: "gt"});
    }
};

onMounted(async () => {
    await buscarCompetencia();
})

const buscarCalculo = async () => {

    loadingCalculo.value = true;
    let rout = getRouts();

    try {
        const formData = new FormData();

        formData.append('exercicio', exercicio.value);
        formData.append('mes', mes.value);
        const response = await window.axios.post(rout.buscarLancamentos, formData);
        valoresApropriar.value = response.data.data;
    } catch (e) {
        alert(e.response.data.message);

        if (e.response.status === 302) {
            await buscarCompetencia();
            buscarCalculo();
        }
    }
    loadingCalculo.value = false;
};

const processarLancamentos = async () => {
    if (valoresApropriar.value.length == 0) {
        alert('Clique em buscar para carregar os valores a serem apropriados. ');
        return
    }

    loadingProcedimento.value = true;
    let rout = getRouts();

    try {
        const formData = new FormData();

        formData.append('exercicio', exercicio.value);
        formData.append('mes', mes.value);
        const response = await window.axios.post(rout.procesar, formData);
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso', life: 8000, group: "gt"});
        valoresApropriar.value = [];
        buscarCompetencia();
    } catch (e) {
        toast.add({severity: 'warn', detail: e.response.data.message, summary: 'Aviso', life: 8000, group: "gt"});
        if (e.response.status === 302) {
            await buscarCompetencia();
            buscarCalculo();
        }
    }

    loadingProcedimento.value = false;
}

const confirmarEstorno = (event) => {

    if (exercicio.value == null) {
        return;
    }
    confirm.require({
        target: event.currentTarget,
        message: 'Tem certeza que deseja processar o estorno?',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
            processarEstorno();
        },
        reject: () => {

        }
    });
}

const processarEstorno = async () => {

    if (exercicio.value === '' || mes.value === '') {
        toast.add({
            severity: 'warn',
            detail: 'Sem competência para processar.',
            summary: 'Aviso',
            life: 8000,
            group: "gt"
        });
    }

    let rout = getRouts();
    loading.value = true;

    try {
        const formData = new FormData();

        formData.append('exercicio', exercicio.value);
        formData.append('mes', mes.value);
        const response = await window.axios.post(rout.procesar, formData);
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso', life: 8000, group: "gt"});
        valoresApropriar.value = [];
        buscarCompetencia();
    } catch (e) {
        toast.add({severity: 'warn', detail: e.response.data.message, summary: 'Aviso', life: 8000, group: "gt"});
        if (e.response.status === 302) {
            await buscarCompetencia();
        }
    }

    loading.value = false;
};

const rowStyle = (data) => {
    if (data.valorLancar == 0) {
        return {backgroundColor: '#FFE0E0'};
    }
};

</script>

<template>
    <section class="container">
        <Panel :header="titulo">
            <div class="card">
                <div class="flex justify-content-start flex-wrap card-container">
                    <div class="flex align-items-center md:w-10rem sm:w-10rem">
                        <label class="font-bold">Competência:</label>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <InputMask type="text" v-model="exercicio" mask="9999" class="p-inputtext-sm mr-2 exercicio"
                                   readonly/>
                        /
                        <InputText type="text" v-model="nomeMes" class="p-inputtext-sm ml-2" readonly/>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Button v-if="!telaModoEstorno" type="button" label="Buscar Valores" icon="pi pi-check"
                                :loading="loadingCalculo" @click="buscarCalculo"/>

                        <Button v-if="telaModoEstorno" type="button" label="Processar Estorno" icon="pi pi-cog"
                                @click="confirmarEstorno"/>
                    </div>
                </div>
            </div>
        </Panel>
    </section>
    <ModalLoading :isLoading="loading"/>

    <section style="max-width: 1600px; padding: 0 10px; margin: 0 auto" v-if="!telaModoEstorno">
        <div class="card pt-2">
            <DataTable :value="valoresApropriar" :rowStyle="rowStyle">
                <template #empty>
                    Clique em
                    <Chip icon="pi pi-search" label="Buscar Valores"/>
                </template>
                <Column field="documento" header="Documento">
                    <template #body="slotProps">
                        {{ `${slotProps.data.documento} - ${slotProps.data.documentoDescricao} ` }}
                    </template>
                </Column>
                <Column field="contaDebito" header="CD">
                    <template #body="slotProps">
                        {{ `${slotProps.data.contaDebito} - ${slotProps.data.estruturalContaDebito} - ${slotProps.data.descricaoContaDebito} ` }}
                    </template>
                </Column>
                <Column field="contaCredito" header="CC">
                    <template #body="slotProps">
                        {{
                            `${slotProps.data.contaCredito} - ${slotProps.data.estruturalContaCredito} - ${slotProps.data.descricaoContaCredito} `
                        }}
                    </template>
                </Column>
                <Column field="valorBalanceteVerificacao" header="Vlr Balancete de Verificação" class="text-right">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.valorBalanceteVerificacao) }}
                    </template>
                </Column>
                <Column field="valorLancar" header="Vlr do Lançamento" class="text-right">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.valorLancar) }}
                    </template>
                </Column>
                <Column field="saldo" header="Saldo do balancete" class="text-right">
                    <template #body="slotProps">
                        {{ formatCurrency(slotProps.data.saldo) }}
                    </template>
                </Column>
            </DataTable>
        </div>

        <div class="card pt-2 ">
            <div class="flex justify-content-center flex-wrap card-container">
                <Button type="button" label="Processar" icon="pi pi-check" :loading="loadingProcedimento"
                        @click="processarLancamentos"/>
            </div>
        </div>
    </section>

    <ConfirmPopup></ConfirmPopup>
    <Toast group="gt"/>
</template>

<style scoped>

.exercicio {
    width: 3.3rem;
}
</style>
