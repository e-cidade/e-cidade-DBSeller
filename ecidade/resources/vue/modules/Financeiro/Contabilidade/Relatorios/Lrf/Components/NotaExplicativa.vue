<script setup>
import {ref, watch} from 'vue';
import {useToast} from "primevue/usetoast";
import ModalLoading from "@modules/Components/ModalLoading.vue";

const props = defineProps(['tipo', 'exercicio', 'instituicao', 'relatorio', 'periodo']);
const toast = useToast();

const loading = ref(false);
const deleteDialog = ref(false);
const linhaDeletar = ref();

const periodoSelecionado = ref();
const periodos = ref([]);

const notaExplicativa = ref();
const fonte = ref();
const notaExplicativaSize = ref(6)
const fonteSize = ref(6)

const notasLancadas = ref([]);

const rotas = {
    get: 'v4/api/financeiro/contabilidade/relatorio-legal/notas-explicativas',
    salvar: 'v4/api/financeiro/contabilidade/relatorio-legal/notas-explicativas',
    deletar: 'v4/api/financeiro/contabilidade/relatorio-legal/notas-explicativas',
}

function changePeriodo() {

    let index = notasLancadas.value.findIndex(obj => obj.periodo.codigo == periodoSelecionado.value.codigo);

    if (index >= 0) {
        const notaLancada = notasLancadas[index];
    }
}

async function buscaNotas() {
    const parametros = {
        relatorio: props.relatorio.codigo,
        'scope': ['periodo']
    }

    await window.axios.get(`${rotas.get}`, {'params': parametros}).then(async response => {
        notasLancadas.value = response.data.data;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

function montaPeriodos() {
    periodos.value = props.relatorio.periodos;
}

function confirmarDelecao(data) {
    linhaDeletar.value = data;
    deleteDialog.value = true;
}

async function deletar() {
    loading.value = true;

    await window.axios.delete(`${rotas.deletar}/${linhaDeletar.value.codigo}`).then(async response => {
        removeLinhaGrid(linhaDeletar);
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

function removeLinhaGrid(linhaDeletar) {
    let index = notasLancadas.value.findIndex(obj => obj.codigo == linhaDeletar.value.codigo);

    if (index >= 0) {
        notasLancadas.value.splice(index, 1)
    }
    deleteDialog.value = false;
    loading.value = false;
}

function valida() {
    try {
        if (!notaExplicativa.value) {
            throw 'Você deve informar a nota explicativa.';
        }

        const conflito = notasLancadas.value.find(nota => {
            return nota.periodo_id == periodoSelecionado.value.codigo
        });

        if (conflito !== undefined) {
            throw `Já foi lançado uma nota explicativa para o período ${conflito.periodo.descricao}.`
        }

    } catch (e) {
        toast.add({severity: 'error', detail: e, summary: 'Erro'});
        return false;
    }

    return true;
}

function salvar() {
    if (!valida()) {
        return
    }
    const parametros = {
        relatorio: props.relatorio.codigo,
        exercicio: props.exercicio,
        instituicao: props.instituicao,
        periodo: periodoSelecionado.value.codigo,
        nota: notaExplicativa.value,
        fonte: fonte.value !== undefined ? fonte.value : '',
        notaSize: notaExplicativaSize.value,
        fonteSize: fonteSize.value
    }

    loading.value = true;
    window.axios.post(rotas.salvar, parametros).then(async response => {
        loading.value = false;
        parametros.codigo = response.data.data;
        parametros.periodo = periodoSelecionado.value;
        notasLancadas.value.push(parametros);
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
    }).catch(e => {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

watch(() => props.relatorio, (item) => {
    if (item !== null) {
        montaPeriodos();
        buscaNotas();
    }
})
</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel class="w-full md:w-11 lg:w-8 xl:w-6" :pt="{header:{style: 'padding: 5px'}}">
                <template #header>Anexo: {{ relatorio?.descricao }}</template>
                <div class="formgrid grid mt-2 row-gap-2">
                    <div class="field col-12">
                        <div class="p-float-label w-full">
                            <Dropdown v-model="periodoSelecionado" inputId="dd-perido"
                                      :options="periodos"
                                      optionLabel="descricao" @change="changePeriodo"
                                      class="w-full "/>
                            <label for="dd-perido">Período</label>
                        </div>
                    </div>
                    <div class="field col-10">
                        <div class="p-float-label w-full">
                            <Textarea v-model="notaExplicativa" autoResize rows="3" class="w-full" inputId="dd-nota"/>
                            <label for="dd-nota">Nota Explicativa</label>
                        </div>
                    </div>
                    <div class="field col-2">
                        <InputNumber v-model="notaExplicativaSize" mode="decimal" showButtons :min="5" :max="10"
                                     style="width:30px" />
                    </div>

                    <div class="field col-10">
                        <div class="p-float-label w-full">
                            <Textarea v-model="fonte" autoResize rows="3" class="w-full" inputId="dd-fonte"/>
                            <label for="dd-fonte">Fonte</label>
                        </div>
                    </div>
                    <div class="field col-2">
                        <InputNumber v-model="fonteSize" mode="decimal" showButtons :min="5" :max="10"
                                     style="width:30px" />

                    </div>
                </div>
            </Panel>
        </section>

        <section class="flex justify-content-center flex-wrap card-container pt-1">
            <Button type="button" label="Salvar" icon="pi pi-save" @click="salvar"/>
        </section>

        <section class="flex justify-content-center">
            <Panel header="Notas Lançadas" class="w-full md:w-11 lg:w-8 xl:w-7">
                <DataTable :value="notasLancadas" dataKey="codigo" tableStyle="min-width: 50rem">
                    <Column field="periodo.descricao" header="Período`"></Column>
                    <Column field="nota" header="Nota Explicativa"></Column>
                    <Column field="fonte" header="Fonte"></Column>
                    <Column :exportable="false" style="min-width:8rem">
                        <template #body="slotProps">
                            <Button icon="pi pi-trash" outlined rounded severity="danger"
                                    @click="confirmarDelecao(slotProps.data)"/>
                        </template>
                    </Column>
                </DataTable>
            </Panel>
        </section>
    </section>

    <ModalLoading :isLoading="loading"/>

    <Dialog v-model:visible="deleteDialog" :style="{width: '450px'}" header="Confirme" :modal="true">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem"/>
            <span v-if="linhaDeletar">Tem certeza que deseja deletar esse registro??</span>
        </div>
        <template #footer>
            <Button label="Não" icon="pi pi-times" text @click="deleteDialog = false"/>
            <Button label="Sim" icon="pi pi-check" text @click="deletar"/>
        </template>
    </Dialog>
</template>

<style scoped>
:deep(.p-inputnumber-input) {
    width:30px;
}
</style>
