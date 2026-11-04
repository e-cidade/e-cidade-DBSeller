<script setup>
import {ref} from 'vue';
import {formatCurrency} from "../../../../utils/Strings";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
// props
const props = defineProps(['instituicao', 'exercicio']);
const toast = useToast();

const loading = ref(false);
const deleteDialog = ref(false);
const valores = ref([]);
const linhasEditar = ref([]);
const linhaDeletar = ref();

const naturezas = ref([
    {label: 'C', value: 'C'},
    {label: 'D', value: 'D'}
])

const rota = 'v4/api/financeiro/contabilidade/procedimento/mapeamento-empenho-rp-manual';

function valida(dadoAtualizado) {
    try {
        if (!dadoAtualizado.exercicio || dadoAtualizado.exercicio.toString().length < 4) {
            throw 'Você deve informar o exercício, e este deve ter 4 digitos';
        }

        if (dadoAtualizado.reduzido <= 0) {
            throw `Você deve informar o reduzido. O reduzido deve existir em ${dadoAtualizado.exercicio}`;
        }
        if (!dadoAtualizado.ai || dadoAtualizado.exercicio.toString().length < 4) {
            throw 'O Ano de Inscrição de Restos a Pagar (AI) deve ser informado.';
        }
        if (dadoAtualizado.funcao <= 0) {
            throw 'Você deve informar a função.';
        }
        if (dadoAtualizado.subfuncao <= 0) {
            throw 'Você deve informar a subfunção.';
        }
        if (dadoAtualizado.elemento <= 0) {
            throw 'Você deve informar o elemento';
        }
        if (dadoAtualizado.recurso <= 0) {
            throw 'Você deve informar o recurso';
        }
        if (dadoAtualizado.valor <= 0) {
            throw 'Você deve informar o valor';
        }
    } catch (e) {
        toast.add({severity: 'error', detail: e, summary: 'Erro'});
        return false;
    }
    return true;
}

const onRowEditSave = (event) => {
    let {newData, index} = event;

    if (!valida(newData)) {
        return false;
    }

    salvar(newData, index)
};

async function salvar(dados, index) {
    const parameters = dados;

    loading.value = true;
    window.axios.post(rota, parameters).then(async response => {
        loading.value = false;
        dados.codigo =response.data.data
        valores.value[index] = dados;
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

function adicionarLinha() {
    valores.value.push({
        id: uniqid(),
        codigo: null,
        exercicio: props.exercicio,
        reduzido: 0,
        ai: props.exercicio - 1,
        funcao: 0,
        subfuncao: 0,
        elemento: 0,
        recurso: 0,
        valor: 0,
        natureza: 'C',
    })
}

async function buscarDados() {
    const parameters = {exercicio: props.exercicio}
    loading.value = true;
    await window.axios.get(rota, {'params': parameters}).then(async response => {
        response.data.data.forEach((data) => {
            data.id = uniqid();
            valores.value.push(data);
        });
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

buscarDados();

function confirmarDelecao(data) {
    linhaDeletar.value = data;
    deleteDialog.value = true;
}
async function deletar() {
    loading.value = true;
    await window.axios.delete(`${rota}/${linhaDeletar.value.codigo}`).then(async response => {
        valores.value = [];
        deleteDialog.value = false;
        buscarDados();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

</script>

<template>

    <div class="card p-fluid w-11 pt-1" style="margin: 0 auto; ">
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
                <Button icon="pi pi-plus w-1" label="Adicionar Linha" raised @click="adicionarLinha"/>
            </template>
            <Column field="exercicio" header="Exercício" style="width: 9%">
                <template #editor="{ data, field }">
                    <InputText v-model="data[field]"/>
                </template>
            </Column>
            <Column field="reduzido" header="Reduzido" style="width: 9%">
                <template #editor="{ data, field }">
                    <InputText v-model="data[field]"/>
                </template>
            </Column>
            <Column field="ai" header="AI" style="width: 9%">
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" :useGrouping="false"/>
                </template>
            </Column>
            <Column field="funcao" header="Função" style="width: 9%">
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" :useGrouping="false"/>
                </template>
            </Column>
            <Column field="subfuncao" header="Subfunção" style="width: 9%">
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" :useGrouping="false"/>
                </template>
            </Column>
            <Column field="elemento" header="Elemento" style="width: 10%">
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" :useGrouping="false"/>
                </template>
            </Column>

            <Column field="recurso" header="Recurso" style="width: 10%">
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" :useGrouping="false"/>
                </template>
            </Column>
            <Column field="valor" header="Valor" style="width: 20%">
                <template #editor="{ data, field }">
                    <InputNumber v-model="data[field]" mode="currency" currency="BRL" locale="pt-BR"/>
                </template>
                <template #body="{ data, field }">
                    {{ formatCurrency(data[field]) }}
                </template>
            </Column>
            <Column field="natureza" header="Natureza" style="width: 8%">
                <template #editor="{ data, field }">
                    <Dropdown v-model="data[field]" :options="naturezas" optionLabel="label" optionValue="value"
                              placeholder="Natureza">
                    </Dropdown>
                </template>
            </Column>
            <Column :rowEditor="true" style="width: 8%; min-width: 8rem" bodyStyle="text-align:center"></Column>
            <Column :exportable="false" style="min-width:8rem">
                <template #body="slotProps">
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmarDelecao(slotProps.data)" />
                </template>
            </Column>
        </DataTable>
    </div>

    <ModalLoading :isLoading="loading"/>

    <Dialog v-model:visible="deleteDialog" :style="{width: '450px'}" header="Confirme" :modal="true">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
            <span v-if="linhaDeletar">Tem certeza que deseja deletar esse registro??</span>
        </div>
        <template #footer>
            <Button label="No" icon="pi pi-times" text @click="deleteDialog = false"/>
            <Button label="Yes" icon="pi pi-check" text @click="deletar" />
        </template>
    </Dialog>
</template>

<style scoped>

</style>
