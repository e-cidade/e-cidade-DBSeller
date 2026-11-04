<script setup>
import {ref, shallowRef, nextTick} from "vue";
import {useToast} from "primevue/usetoast";
import Message from 'primevue/message';
import Calendar from 'primevue/calendar';
import AbaEmpenho from "./AbaEmpenho";
import AbaPlanilha from "./AbaPlanilha";
import AbasComponent from "../../../../../Components/AbasComponent.vue";
import AbaTotalizadora from "./AbaTotalizadora.vue";
import ModalLoading from "../../../../../Components/ModalLoading.vue";

const props = defineProps(['exercicio']);
const toast = useToast;

// propriedades para o componente Calendar
const minDate = ref(new Date(`${props.exercicio}-01-01T00:00:00`));
const maxDate = ref(new Date(`${props.exercicio}-12-31T00:00:00`));
const dataInicial = ref(new Date());
const dataFinal = ref();
const abasComponente = ref();
const loading = ref(false);

const contas = ref([]);
const empenhos = ref(null);
const planilhas = ref(null);

const selectedTipoEmpenho = ref(0);
const tiposEmpenho = ref([
    {value: 0, name: 'Todos'},
    {value: 1, name: 'Folha de Pagamento'},
    {value: 2, name: 'Fornecedores'},
]);

const selectedSituacao = ref(0);
const situacoesEmpenho = ref([
    {value: 0, name: 'Ambas'},
    {value: 1, name: 'Apenas apropriação da retenção'},
    {value: 2, name: 'Líquido da OP paga ao credor'},
]);

const routs = {
    buscar: 'v4/api/financeiro/tesouraria/contas-tesouraria',
    gerar: ''
};

/**
 * Busca as contas da tesouraria com os reduzidos e empenho
 * @returns {Promise<void>}
 */
const buscarContas = async () => {
    try {
        const response = await window.axios.get(
            "v4/api/financeiro/tesouraria/contas?comContaExtra=1&comReduzidos=1"
        );
        response.data.data.forEach(conta => contas.value.push(conta));
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }
};
buscarContas();

// Abas
const activeIndex = ref(0);
const render = ref(false);
const abas = shallowRef([
    {
        nome: 'Apropriados via Retenção',
        iconClass: 'pi pi-cog',
        componente: AbaEmpenho,
        props: {
            dataInicial: dataInicial,
            dataFinal: dataFinal,
            contas: contas,
            empenhos: empenhos,
            tipo: selectedTipoEmpenho,
            situacao: selectedSituacao
        },

        eventos: {
            proximo: async () => {
                activeIndex.value = 1
            }
        },
        disabled: false
    },
    {
        nome: 'Apropriados via Planilhas',
        iconClass: 'pi pi-cog',
        componente: AbaPlanilha,
        props: {
            dataInicial: dataInicial,
            dataFinal: dataFinal,
            planilhas: planilhas
        },
        eventos: {
            proximo: async () => {
                activeIndex.value = 2
            }
        },
        disabled: false
    },
    {
        nome: 'Totalização',
        iconClass: 'pi pi-calculator',
        componente: AbaTotalizadora,
        props: {
            dataInicial: dataInicial,
            dataFinal: dataFinal,
            dadosEmpenho: empenhos,
            dadosPlanilha: planilhas,
        },
        eventos: {},
        disabled: false
    }
]);

const buscar = async () => {
    empenhos.value = null;
    planilhas.value = null;
    activeIndex.value = 0
    render.value = true;
    await nextTick();
    loading.value = true;

    await Promise.all([
        abasComponente.value.getComponenteAba('AbaEmpenho').buscarDados(),
        abasComponente.value.getComponenteAba('AbaPlanilha').buscarDados()
    ]);

    loading.value = false;
}


const dialogPassThrough = {content: {style: 'background: #e1dede; padding: 0;'}};
</script>

<template>
    <Message severity="info" :closable="false">
        <div style='font-size: 10pt; '>
            Ao informar o período, serão buscados todos os valores contabilizados a título de apropriações
            extra-orçamentários a recolher.
        </div>
    </Message>
    <section class="container" style="max-width: 555px;">
        <div class="card pt-1">
            <div class="flex justify-content-start flex-wrap card-container">
                <div class="flex align-items-center" style="width: 150px">
                    <label class="font-bold">Período:</label>
                </div>
                <div class="flex align-items-center pl-2">
                    <Calendar v-model="dataInicial" :minDate="minDate" :maxDate="new Date()" dateFormat="dd/mm/yy"
                              showIcon :manualInput="false" placeholder="Data Inicial"
                              style="width: 150px;"/>
                </div>
                <div class="flex align-items-center pl-2">
                    <label class="font-bold">até:</label>
                </div>
                <div class="flex align-items-center pl-2">
                    <Calendar v-model="dataFinal" :minDate="dataInicial" :maxDate="maxDate" dateFormat="dd/mm/yy"
                              showIcon placeholder="Data Final" showButtonBar :manualInput=true
                              style="width: 150px;"/>
                </div>
            </div>
        </div>
        <div class="card pt-1">
            <div class="flex justify-content-start flex-wrap card-container pt-1">
                <div class="flex align-items-center" style="width: 150px">
                    <label class="font-bold">Filtrar Empenhos:</label>
                </div>
                <div class="flex align-items-center pl-2">
                    <Dropdown v-model="selectedTipoEmpenho" :options="tiposEmpenho" optionValue="value"
                              optionLabel="name" placeholder="Selecione" class="w-full md:w-20rem"/>
                </div>
            </div>
        </div>
        <div class="card pt-1">
            <div class="flex justify-content-start flex-wrap card-container pt-1">
                <div class="flex align-items-center" style="width: 150px">
                    <label class="font-bold">Situação do Pagamento:</label>
                </div>
                <div class="flex align-items-center pl-2">
                    <Dropdown v-model="selectedSituacao" :options="situacoesEmpenho" optionValue="value"
                              optionLabel="name" placeholder="Selecione" class="w-full md:w-20rem"/>
                </div>
            </div>
        </div>
        <div class="card pt-1">
            <div class="flex justify-content-center flex-wrap card-container pt-5">
                <Button type="button" label="Buscar" icon="pi pi-search" @click="buscar"/>
            </div>
        </div>
    </section>

    <Dialog v-model:visible="render" class="p-dialog-maximized" :pt="dialogPassThrough"
            header="Mapa de apropriações extra-orçamentárias a transferir">
        <AbasComponent ref="abasComponente" :abas="abas" v-if="render" v-model:active-index="activeIndex"/>
    </Dialog>

    <ModalLoading :isLoading="loading"/>
    <Toast/>
</template>

<style scoped>

</style>
