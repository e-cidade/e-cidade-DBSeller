<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import MultiDownload from "@modules/Components/MultiDownload.vue";

const toast = useToast();
const props = defineProps(['exercicio', 'instituicao']);

const EXERCICIO_MINIMO_EMISSAO = 2024;

const loading = ref(false);
const mensagemLoad = ref(null);
const multiDownload = ref(null);
const mesSelecionado = ref();
const consistencia = ref();
const exercicioEmissao = ref(Number(props.exercicio));

const meses = ref([
    {name: 'Janeiro', code: '01'},
    {name: 'Fevereiro', code: '02'},
    {name: 'Março', code: '03'},
    {name: 'Abril', code: '04'},
    {name: 'Maio', code: '05'},
    {name: 'Junho', code: '06'},
    {name: 'Julho', code: '07'},
    {name: 'Agosto', code: '08'},
    {name: 'Setembro', code: '09'},
    {name: 'Outubro', code: '10'},
    {name: 'Novembro', code: '11'},
    {name: 'Dezembo', code: '12'},
    {name: 'Encerramento', code: '13'},
]);

const rotas = {
    msc: 'v4/api/financeiro/contabilidade/msc/emitir',
    consistencia: {
        pcasp: 'v4/api/financeiro/contabilidade/plano-contas/emitir/pcasp/mapeamento',
        despesa: 'v4/api/financeiro/contabilidade/plano-contas/emitir/orcamentario/despesa/mapeamento',
        receita: 'v4/api/financeiro/contabilidade/plano-contas/emitir/orcamentario/receita/mapeamento',
        atributoFP: 'v4/api/financeiro/contabilidade/msc/consistencia/atributoFP'
    }
}

const consistencias = [{
    items: [
        {
            label: 'Atributo FP',
            command: atributoFP
        },
        {
            label: 'Mapeamento PCASP',
            command: mapeamentoPcasp
        },
        {
            label: 'Mapeamento Despesa',
            command: mapeamentoDespesa
        },
        {
            label: 'Mapeamento Receita',
            command: mapeamentoReceita
        }
    ]
}];


function validaForm() {
    try {
        if (mesSelecionado.value === undefined) {
            throw 'Selecione o mês da emissão.'
        }

        if (exercicioEmissao.value < EXERCICIO_MINIMO_EMISSAO) {
            throw 'Essa rotina emite a matriz a partir do exercício de 2024.'
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
    mensagemLoad.value = 'Emitindo MSC, aguarde.';

    const parameters = {
        exercicio: exercicioEmissao.value,
        mes: mesSelecionado.value.code
    }

    try {
        const response = await window.axios.post(rotas.msc, parameters)
        console.log(response.data.data.path);
        multiDownload.value.addFile(
            response.data.data.linkExterno,
            'Matriz de Saldos Contábeis'
        );

        multiDownload.value.openModal();
        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro', life: 6000});
    }
}

async function mapeamentoPcasp() {
    imprimirMapeamento(rotas.consistencia.pcasp)
}

async function mapeamentoDespesa() {
    imprimirMapeamento(rotas.consistencia.despesa)
}

async function mapeamentoReceita() {
    imprimirMapeamento(rotas.consistencia.receita)
}

async function imprimirMapeamento(rota) {
    const formData = new FormData();
    formData.append('exercicio', exercicioEmissao.value);
    formData.append('tipoPlano', 'uniao');

    loading.value = true;
    mensagemLoad.value = 'Aguarde, processando consistência'
    window.axios.post(rota, formData).then(response => {
        multiDownload.value.addFile(
            `${response.data.data.csvLinkExterno}`,
            response.data.message
        );
        multiDownload.value.openModal();
    }).catch(e => {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro', life: 6000});
    }).finally(() => loading.value = 0);
}

async function atributoFP() {
    const formData = new FormData();
    formData.append('exercicio', exercicioEmissao.value);

    loading.value = true;
    mensagemLoad.value = 'Aguarde, processando consistência';
    window.axios.post(`${rotas.consistencia.atributoFP}/${exercicioEmissao.value}`).then(response => {
        multiDownload.value.addFile(
            `${response.data.data.pdfLinkExterno}`,
            response.data.message
        );
        multiDownload.value.openModal();
    }).catch(e => {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro', life: 6000});
    }).finally(() => loading.value = 0);
}


const toggle = (event) => {
    consistencia.value.toggle(event);
};

</script>

<template>

    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Filtros Obrigatórios" class="w-full md:w-11 lg:w-8 xl:w-5">
                <div class="formgrid grid mt-2 row-gap-2">
                    <div class="card ">
                        <div class="flex justify-content-start flex-wrap card-container">
                            <div class="flex align-items-center md:w-10rem ">
                                <label class="font-bold">Competência:</label>
                            </div>
                            <div class="flex align-items-center pl-2">
                                <Dropdown v-model="mesSelecionado" :options="meses" optionLabel="name"
                                          placeholder="Selecione o mês"
                                          checkmark :highlightOnSelect="false" class="w-full md:w-14rem"/>
                            </div>
                            <div class="flex align-items-center pl-2">
                                <InputNumber v-model="exercicioEmissao" inputId="withoutgrouping" :useGrouping="false"/>
                            </div>
                        </div>
                    </div>
                </div>
            </Panel>
        </section>
    </section>

    <section class="flex justify-content-center flex-wrap card-container pt-5 gap-2">
        <Button
            type="button"
            icon="pi pi-ellipsis-v"
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_menu"
            label="Consistências"
        />
        <Menu ref="consistencia" id="overlay_menu" :model="consistencias" :popup="true"/>

        <Button type="button" label="Imprimir" icon="pi pi-print" @click="imprimir"/>
    </section>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    <MultiDownload ref="multiDownload" :header="'Relatórios'" :position="'center'"></MultiDownload>
</template>
