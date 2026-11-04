<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import ConfirmPopup from 'primevue/confirmpopup'

const props = defineProps(['exercicio']);
const toast = useToast();
const confirm = useConfirm();

const loading = ref(false);
const mensagemLoad = ref(null);
const estrutural = ref(null);
const receitas = ref([]);
const receitasSelecionadas = ref([]);

const desdobramentoSelecionado = ref();
const desdobramentos = ref([
    {name: 'MDE', code: 1},
    {name: 'ASPS', code: 2},
    {name: 'FUNDEB', code: 3},
]);

const rotas = {
    receitas: 'v4/api/financeiro/tesouraria/decendio/receitas',
    excluir: 'v4/api/financeiro/tesouraria/decendio/receitas/excluir'
}

async function buscarReceitas() {
    if (desdobramentoSelecionado.value === undefined) {
        toast.add({severity: 'warn', detail: 'Informe o desdobramento.', summary: 'Aviso'});
        return;
    }

    const parameters = {
        exercicio: props.exercicio,
        estrutural: estrutural.value,
        desdobramento: desdobramentoSelecionado.value,
    }

    receitas.value = [];
    receitasSelecionadas.value = [];
    loading.value = true;

    await window.axios.get(rotas.receitas, {'params': parameters}).then(response => {
        receitasSelecionadas.value = response.data.data.filter((receita) => {
            return receita.configurada;
        });

        receitas.value = response.data.data;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    });
    loading.value = false;
}

function valida() {
    try {
        if (receitasSelecionadas.value.length === 0) {
            throw 'Você deve selecionar as receitas que deseja configurar';
        }
    } catch (e) {
        toast.add({severity: 'error', detail: e, summary: 'Erro'});
        return false;
    }

    return true
}

function getDesdobramento() {
    console.log(desdobramentoSelecionado.value)
    if (desdobramentoSelecionado.value === 1) {
        return 'mde';
    }
    if (desdobramentoSelecionado.value === 2) {
        return 'asps';
    }
    if (desdobramentoSelecionado.value === 3) {
        return 'fundeb';
    }
}

async function salvar(event) {
    confirm.require({
        group: "page",
        target: event.target,
        message: 'Tem certeza que deseja salvar os selecionados?',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Confirmar',
        accept: () => {
            if (!valida()) {
                return;
            }

            const parameters = {
                exercicio: props.exercicio,
                receitas: [],
                origem: 1,
                desdobramentos: [getDesdobramento()]
            };

            receitasSelecionadas.value.forEach((receita) => {
                parameters.receitas.push(receita.reduzido);
            })

            loading.value = true;

            window.axios.post(rotas.receitas, parameters).then(response => {
                toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
                receitasSelecionadas.value = [];
                receitas.value= [];
                estrutural.value = '';
            }).catch(response => {
                toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
            });
            loading.value = false;
        },
        reject: () => {
            return
        }
    });
}

async function excluirSelecionados(event) {
    confirm.require({
        group: "page",
        target: event.target,
        message: 'Tem certeza que deseja excluir os selecionados?',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-secondary p-button-outlined p-button-sm',
        acceptClass: 'p-button-sm',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Confirmar',
        accept: () => {

            if (receitasSelecionadas.value.length === 0) {
                toast.add({severity: 'error', detail: 'Você deve selecionar as receitas que deseja configurar', summary: 'Erro'});
                return;
            }

            const parameters = {
                exercicio: props.exercicio,
                origem: 1,
                receitas: []
            };

            receitasSelecionadas.value.forEach((receita) => {
                parameters.receitas.push(receita.reduzido);
            })

            loading.value = true;

            window.axios.post(rotas.excluir, parameters).then(response => {
                toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
                receitasSelecionadas.value = [];
                receitas.value= [];
                estrutural.value = '';
                desdobramento.value = false
            }).catch(response => {
                toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
            });
            loading.value = false;
        },
        reject: () => {
            return
        }
    });
}

function alterouDesdobramento() {
    receitas.value = [];
    receitasSelecionadas.value = [];
}
</script>

<template>
    <Message severity="info" :closable="false" :style="{marginTop:0}">
        <div style='font-size: 10pt; '>
            Nessa rotina você informará as receitas que executarão o decêndio.
        </div>
    </Message>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">
            <Panel header="Filtros Obrigatórios" class="w-full md:w-11 lg:w-8 xl:w-7">
                <div class="formgrid grid mt-4 row-gap-2">
                    <div class="field col-12 sm:col-6 md:col-4 lg:col-4 xl:col-3">
                        <div class="p-float-label">
                            <InputText v-model="estrutural" id="lb-estrutural" maxlength="15"/>
                            <label for="lb-estrutural">Estrutural</label>
                        </div>
                    </div>
                    <div class="field col-12 sm:col-6 md:col-5 lg:col-4 xl:col-4">
                        <div class="p-float-label">
                            <Dropdown v-model="desdobramentoSelecionado" :options="desdobramentos"
                                      inputId="dd-desdobramentos" optionLabel="name" option-value="code"
                                      @change="alterouDesdobramento"
                                      placeholder="Desdobramento" class="w-full  md:w-56"/>
                            <label for="dd-desdobramentos">Desdobramento</label>
                        </div>
                    </div>
                    <div class="field col-12 sm:col-6 md:col-3 lg:col-4 xl:col-3">
                        <Button type="button" label="Buscar Receitas" icon="pi pi-search" @click="buscarReceitas"/>
                    </div>
                </div>
            </Panel>
        </section>
    </section>

        <section class="flex flex-column w-full gap-2">
            <Panel header="Configuração das Receitas" class="w-full">
                <div class="card flex flex-wrap justify-content-center gap-3">
                    <div class="flex align-items-center mb-1">
                        <Button type="button" label="Salvar" icon="pi pi-save" @click="salvar"/>
                    </div>
                    <div class="flex align-items-center mb-1">
                        <Button label="Excluir Selecionados" severity="danger" icon="pi pi-trash" @click="excluirSelecionados"/>
                    </div>
                </div>

                <DataTable v-model:selection="receitasSelecionadas" :value="receitas" dataKey="reduzido"
                           class="w-full" tableStyle="min-width: 50rem;" scrollable scrollHeight="500px">
                    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                    <Column field="estrutural" header="Estrutural"/>
                    <Column field="reduzido" header="Reduzido"/>
                    <Column field="descricao" header="Nome"/>
                    <Column field="siconfi" header="Siconfi"/>
                    <Column field="complemento" header="Complemento"/>
                </DataTable>
            </Panel>
        </section>

    <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    <ConfirmPopup group="page"/>
</template>
