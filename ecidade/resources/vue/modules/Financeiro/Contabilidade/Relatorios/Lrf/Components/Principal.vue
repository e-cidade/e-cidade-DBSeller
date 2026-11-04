<script setup>

import {computed, onMounted, ref} from "vue";
import {useToast} from "primevue/usetoast";
import DropdownVersoesRelatorioLegal
    from "@modules/Financeiro/Contabilidade/Relatorios/Lrf/Components/DropdownVersoesRelatorioLegal.vue";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import MultiDownload from "@modules/Components/MultiDownload.vue";
import MultiSelectInstituicao from "@modules/Configuracao/Components/MultiSelectInstituicao.vue";
import Emissoes from "@modules/Financeiro/Contabilidade/Relatorios/Lrf/Components/Emissoes.vue";

const toast = useToast();
const props = defineProps(['modelValue', 'tipo', 'anexo', 'exercicio', 'login', 'consolidado']);
const emit = defineEmits(['update:modelValue'])

const filtro = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const isAdmin = ref(props.login === 'dbseller');
const isConsolidado = ref(props.consolidado == 1);

const loading = ref(false);
const multiDownload = ref(null);

const relatorioSelecionado = ref(null);
const periodoSelecionado = ref(null);

const versoes = ref([]);
const periodos = ref([]);

const rotas = {
    versoes:'v4/api/financeiro/contabilidade/relatorio-legal/versoes/anexo',
    emitirAgendamento: 'v4/api/financeiro/contabilidade/relatorio-legal/emitir',
    emitirAgora: 'v4/api/financeiro/contabilidade/relatorio-legal/emitir-agora',
}

function changeVersao(e) {
    let versao = versoes.value.find((versao) => {
        return versao.codigo === e.value;
    });

    filtro.value.relatorio = versao;
    filtro.value.abasDisabilitada = true;
    periodoSelecionado.value = filtro.value.periodo = null;
    periodos.value = versao.periodos;
}

function changePeriodo(e) {
    const periodo = periodos.value.find((periodo) => {
        return periodo.codigo === e.value;
    })

    filtro.value.periodo = periodo;
    filtro.value.abasDisabilitada = false;
}

function valida() {
    try {
        if (filtro.value.relatorio === null) {
            throw 'Você deve selecionar a versão do relatório antes.';
        }
        if (filtro.value.periodo === null) {
            throw 'Você deve selecionar o período antes.';
        }
    } catch (e) {
        toast.add({severity: 'warn', detail: e, summary: 'Aviso'});
        return false;
    }

    return true;
}

async function buscaVersoesAnexo() {
    loading.value = true;
    await window.axios.get(`${rotas.versoes}/${props.anexo}/${props.tipo}`).then(response => {
        for (const dado of response.data.data) {
            dado.descricao = `${dado.codigo} - ${dado.descricao}`
            versoes.value.push(dado);
            loading.value = false;
        }
    }).catch(e => {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

function imprimirAgendamento() {
    imprimir(rotas.emitirAgendamento, false);
}

function imprimirAgora() {
    imprimir(rotas.emitirAgora, true);
}

function imprimir(rota, opemModalDowload) {

    if (!valida()) {
        return
    }
    const parametros = {
        tipo: props.tipo,
        anexo: props.anexo,
        relatorio : filtro.value.relatorio.codigo,
        periodo : filtro.value.periodo.codigo,
        consolidado: props.consolidado,
        instituicoes: filtro.value.instituicoes.map(value => value.code),
    }

    loading.value = true;
    window.axios.post(rota, parametros).then(async response => {
        loading.value = false;
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
        if (opemModalDowload) {
            multiDownload.value.addFile(
                `${response.data.data.pdfLinkExterno}`,
                `${response.data.message} - PDF`
            );

            multiDownload.value.addFile(
                `${response.data.data.xlsLinkExterno}`,
                `${response.data.message} - XLS`
            );

            multiDownload.value.openModal();
        }
    }).catch(e => {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}


onMounted(() => {
    buscaVersoesAnexo()
});

</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Filtros para impressão" class="w-full md:w-11 lg:w-8 xl:w-6">
                <div class="formgrid grid mt-2 row-gap-2">
                    <div class="field col-12">
                        <div class="p-float-label w-full">
                            <Dropdown v-model="relatorioSelecionado" inputId="dd-versoes-relatorio-legal"
                                      :options="versoes"
                                      optionLabel="descricao" optionValue="codigo" @change="changeVersao"
                                      class="w-full "/>
                            <label for="dd-versoes-relatorio-legal">Versão</label>
                        </div>
                    </div>
                    <div class="field col-12">
                        <div class="p-float-label w-full">
                            <Dropdown v-model="periodoSelecionado" inputId="dd-periodos"
                                      :options="periodos"
                                      optionLabel="descricao" optionValue="codigo"
                                      class="w-full" @change="changePeriodo"/>
                            <label for="dd-periodos">Período</label>
                        </div>
                    </div>
                    <div class="field col-12" v-if="!isConsolidado">
                        <div class="p-float-label">
                            <MultiSelectInstituicao v-model="filtro.instituicoes" inputId="dd-instituicao"
                                                    class="w-full"></MultiSelectInstituicao>
                            <label for="dd-instituicao">Instituições</label>
                        </div>
                    </div>
                </div>
            </Panel>
        </section>
        <section class="flex justify-content-center flex-wrap card-container pt-1 gap-2">
            <Button type="button" label="Imprimir" icon="pi pi-print" @click="imprimirAgendamento"/>
            <Button type="button" label="Imprimir Agora" icon="pi pi-print" severity="info"
                    v-if="isAdmin" @click="imprimirAgora"/>
        </section>

        <Emissoes v-model="filtro" :periodo="filtro.periodo"></Emissoes>
    </section>

    <ModalLoading :isLoading="loading"/>
    <MultiDownload ref="multiDownload" :header="'Relatórios'" :position="'center'"></MultiDownload>
</template>

<style scoped>

</style>
