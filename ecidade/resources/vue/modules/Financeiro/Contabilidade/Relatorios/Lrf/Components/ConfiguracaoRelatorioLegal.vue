<script setup>

import {ref, watch} from 'vue';
import {formatCurrency} from '@utils/Strings';
import {useToast} from "primevue/usetoast";
import ModalLoading from "@modules/Components/ModalLoading.vue";

const props = defineProps(['tipo', 'exercicio', 'instituicao', 'relatorio', 'periodo']);
const toast = useToast();

/**
 *
 *  ----------------------------------------------------------------------------
 *
 *   Buscar pelos parâmetros:  código do relatório, exercício e período as configurações das linhas
 *   -- apresentar,
 *   -- salvar a edição.
 *   -- não precisa deixar reativo
 *
 *  ----------------------------------------------------------------------------
 *
 */
const metaKey = ref(true);
const loading = ref(false);
const deleteDialog = ref(false);

const linhas = ref([]);
const linhaSelecionada = ref([]);
const colunas = ref([]);
const colunaSelecionada = ref([]);
const disabledBtnAdicionar = ref(true);

const teste = ref()

// da grid
const valores = ref([]);
const linhasEditar = ref([]);
const linhaDeletar = ref();

const exercicioManual = ref();
const mesManual = ref();
const valorManual = ref(0);

const meses = ref([
    {label: 'Janeiro', value: 1},
    {label: 'Fevereiro', value: 2},
    {label: 'Março', value: 3},
    {label: 'Abril', value: 4},
    {label: 'Maio', value: 5},
    {label: 'Junho', value: 6},
    {label: 'Julho', value: 7},
    {label: 'Agosto', value: 8},
    {label: 'Setembro', value: 9},
    {label: 'Outubro', value: 10},
    {label: 'Novembro', value: 11},
    {label: 'Dezembro', value: 12},
])

const rotas = {
    "linhas": 'v4/api/financeiro/contabilidade/relatorio-legal/linhas-manuais',
    "valoresColuna": 'v4/api/financeiro/contabilidade/relatorio-legal/linha/valor-manual',
    "deletar": 'v4/api/financeiro/contabilidade/relatorio-legal/linha/valor-manual',
    "salvar": 'v4/api/financeiro/contabilidade/relatorio-legal/linha/valor-manual',
}

//
async function buscaConfiguracao(codigo) {
    await window.axios.get(`${rotas.linhas}/${codigo}/${props.tipo}`).then(response => {
        for (const dado of response.data.data) {
            linhas.value.push(dado);
        }
    });
}

/**
 * Quando seleciona/muda a linha no Dropdown
 * @param e
 */
function changeLinha(e) {
    disabledBtnAdicionar.value = true;
    colunas.value = e.value.colunas;
}

/**
 * Quando seleciona/muda a coluna no Dropdown
 * @param e
 */
function changeColuna(e) {
    disabledBtnAdicionar.value = false;
    buscarValoresColuna(e);
}

async function buscarValoresColuna() {
    const parametros = {
        relatorio: props.relatorio.codigo,
        instituicao: props.instituicao,
        linha: linhaSelecionada.value.ordem,
        coluna: colunaSelecionada.value
    }

    valores.value = [];
    loading.value = true
    await window.axios.get(rotas.valoresColuna, {'params': parametros}).then(response => {
        for (const valor of response.data.data) {
            const index = meses.value.findIndex(mes => mes.value == valor.mes);
            valor.id = uniqid();
            valor.mes = meses.value[index];
            valores.value.push(valor);
        }
    }).catch(e => {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

function validaExercicio(exercicio) {
    if (!exercicio || exercicio.toString().length < 4) {
        return false;
    }

    return true;
}

function validaLinhaAdicionada(exercicio, mes, indexGrid) {
    const hash = `${exercicio}#${mes}`;
    const teste = valores.value.find((dado, indexDados) => {
        // se index dos dados for igual siguinifica que esta editando a própria linha
        if (indexGrid !== undefined && indexDados === indexGrid) {
            return false;
        }
        const hashExistente = `${dado.exercicio}#${dado.mes.value}`;
        return hash === hashExistente;
    });

    // tem conflito
    if (teste != undefined) {
        return false;
    }

    return true;
}

/**
 * Valida edição na linha da grid
 * @param exercicio
 * @param mes
 * @param indexGrid
 * @returns {boolean}
 */
function valida(exercicio, mes, valor, indexGrid) {

    try {
        if (!validaExercicio(exercicio)) {
            throw 'Você deve informar o exercício, e este deve ter 4 digitos.' ;
        }

        if (!validaLinhaAdicionada(exercicio, mes, indexGrid)) {
            throw 'Você já informou uma linha com o exercício e mês informado.';
        }

        if (Number(valor) === 0) {
            throw 'Valor informado não pode ser Zero(0).';
        }
    } catch (e) {
        toast.add({severity: 'error', detail: e, summary: 'Erro'});
        return false;
    }
    return true;
}

/**
 * Edita linha da grid persistindo o valor
 * @param event
 * @returns {boolean}
 */
const onRowEditSave = (event) => {
    let {newData, index} = event;

    if (!valida(newData.exercicio, newData.mes.value, newData.valor, index)) {
        return false;
    }
    salvar(newData, index)
};

/**
 * Persiste a edição da linha da grid
 * @param dados
 * @param index
 * @returns {Promise<void>}
 */
async function salvar(dados, index) {
    const parametros = {
        relatorio: props.relatorio.codigo,
        instituicao: props.instituicao,
        linha: linhaSelecionada.value.ordem,
        coluna: colunaSelecionada.value,
        codigo: dados.codigo,
        exercicio: dados.exercicio,
        mes: dados.mes.value,
        valor: dados.valor
    }

    loading.value = true;
    window.axios.post(rotas.salvar, parametros).then(async response => {
        loading.value = false;
        dados.codigo = response.data.data;
        atualizaGrid(dados, index);
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
    }).catch(e => {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

function atualizaGrid(dados, index) {
    if (index === undefined) {
        valores.value.push(dados);
        return;
    }

    valores.value[index] = dados;
}

function confirmarDelecao(data) {
    linhaDeletar.value = data;
    deleteDialog.value = true;
}

function removeLinhaGrid(linhaDeletar) {
    let index = valores.value.findIndex(obj => obj.id == linhaDeletar.value.id);

    if (index >= 0) {
        valores.value.splice(index, 1)
    }
    deleteDialog.value = false;
    loading.value = false;
}

async function deletar() {
    if (linhaDeletar.value.codigo === null) {
        removeLinhaGrid(linhaDeletar)
        deleteDialog.value = false;
        return;
    }
    loading.value = true;
    await window.axios.delete(`${rotas.deletar}/${linhaDeletar.value.codigo}`).then(async response => {
        removeLinhaGrid(linhaDeletar);
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

function adicionarLinha() {
    if (mesManual.value === undefined) {
        toast.add({severity: 'error', detail: 'Informe os valores antes de adicionar.', summary: 'Erro'});
        return
    }
    if (!valida(exercicioManual.value, mesManual.value.value, valorManual.value, undefined)) {
        return
    }

    salvar({
        id: uniqid(),
        codigo: null,
        exercicio: exercicioManual.value,
        mes: mesManual.value,
        valor: valorManual.value
    });
}

watch(() => props.relatorio, (item) => {
    if (item !== null) {
        buscaConfiguracao(item.codigo);
    }
})
</script>

<template>
    <section class="flex flex-column w-full gap-2">

        <section class="flex justify-content-center">
            <Panel class="w-full " :pt="{header:{style: 'padding: 5px'}}">
                <template #header>Anexo: {{ relatorio?.descricao }}</template>

                <div class="formgrid grid mt-2 row-gap-2">
                    <div class="field col-12">
                        <div class="p-float-label w-full">
                            <Dropdown v-model="linhaSelecionada" inputId="dd-linha"
                                      :options="linhas"
                                      optionLabel="descricao" @change="changeLinha"
                                      class="w-full "/>
                            <label for="dd-linha">Linha</label>
                        </div>
                    </div>

                    <div class="field col-12">
                        <div class="p-float-label w-full">
                            <Dropdown v-model="colunaSelecionada" inputId="dd-coluna"
                                      :options="colunas"
                                      optionLabel="descricao" optionValue="coluna" @change="changeColuna"
                                      class="w-full "/>
                            <label for="dd-coluna">Coluna</label>
                        </div>
                    </div>
                </div>
            </Panel>
        </section>
        <section class="flex justify-content-center">
            <DataTable v-model:editingRows="linhasEditar" :value="valores" editMode="row" dataKey="id"
                       @row-edit-save="onRowEditSave"
                       :pt="{
                          table: { style: 'min-width: 50rem' },
                          column: {
                                  bodycell: ({ state }) => ({
                                      style:  state['d_editing']&&'padding-top: 0.6rem; padding-bottom: 0.6rem'
                                  })
                              }
                          }"
            >
                <template #header>
                    <div class="w-full gap-2 pt-3">
                        <div class="flex justify-content-between">
                            <div class="p-float-label ">
                                <InputText v-model="exercicioManual"/>
                                <label for="dd-coluna">Exercício</label>
                            </div>



                            <div class="p-float-label">
                                <Dropdown v-model="mesManual" :options="meses" optionLabel="label"
                                          placeholder="Mês">
                                </Dropdown>
                                <label for="dd-coluna">Mês</label>
                            </div>
                            <div class="p-float-label ">
                                <InputNumber v-model="valorManual" mode="currency" currency="BRL" locale="pt-BR"/>
                                <label for="dd-coluna">Valor</label>
                            </div>
                            <Button icon="pi pi-plus w-1" label="Adicionar Linha" raised
                                    :disabled="disabledBtnAdicionar" @click="adicionarLinha"/>
                        </div>
                    </div>
                </template>
                <Column field="exercicio" header="Exercício">
                    <template #editor="{ data, field }">
                        <InputText v-model="data[field]"/>
                    </template>
                </Column>
                <Column field="mes" header="Mês">
                    <template #editor="{ data, field }">
                        <Dropdown v-model="data[field]" :options="meses" optionLabel="label"
                                  placeholder="Mês">
                        </Dropdown>
                    </template>
                    <template #body="{ data, field }">
                        {{ data.mes.label }}
                    </template>
                </Column>
                <Column field="valor" header="Valor">
                    <template #editor="{ data, field }">
                        <InputNumber v-model="data[field]" mode="currency" currency="BRL" locale="pt-BR"/>
                    </template>
                    <template #body="{ data, field }">
                        {{ formatCurrency(data[field]) }}
                    </template>
                </Column>
                <Column :rowEditor="true" style="width: 8%; min-width: 8rem" bodyStyle="text-align:center"></Column>
                <Column :exportable="false" style="min-width:8rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-trash" outlined rounded severity="danger"
                                @click="confirmarDelecao(slotProps.data)"/>
                    </template>
                </Column>
            </DataTable>
        </section>
    </section>

    <ModalLoading :isLoading="loading"/>

    <Dialog v-model:visible="deleteDialog" :style="{width: '450px'}" header="Confirme" :modal="true">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem"/>
            <span v-if="linhaDeletar">Tem certeza que deseja deletar esse registro?</span>
        </div>
        <template #footer>
            <Button label="Não" icon="pi pi-times" text @click="deleteDialog = false"/>
            <Button label="Sim" icon="pi pi-check" text @click="deletar"/>
        </template>
    </Dialog>
</template>

<style scoped>

</style>
