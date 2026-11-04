
<script setup>

import DialogRubricaServidor from "../Components/DialogRubricaServidor";
import ModalLoading from "../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
import {onMounted, ref} from "vue";

const routes = {
    salvar: `v4/api/recursos-humanos/pessoal/ajudacusto/config-salvar`,
    buscar: `v4/api/recursos-humanos/pessoal/ajudacusto/config-buscar/${props.instituicao}`
}
const props = defineProps(["instituicao"]);
const toast = useToast();
const loading = ref(false)

const data = ref({
    id: null,
    valor_limite: 0,
    idade_maxima: 0,
    maximo_dependente: 0,
    percentual: 0,
    habilitaDependente: false,
    selectedParentesco : [],
    permiteServidorDependente: false,
    rubrica: '',
    rubricaDescricao: '',
    rubricaDependente: '',
    rubricaDependenteDescricao: ''
});

const parentesco = ref([
    { code : 'C', name : 'Cônjuge' },
    { code : 'F', name : 'Filho' },
    { code : 'P', name : 'Pai' },
    { code : 'M', name : 'Mãe' },
    { code : 'A', name : 'Avó' },
    { code : 'O', name : 'Outros'}
]);

const callback = ref();
const dialogRubrica = ref();

function openDialogRubrica() {
    callback.value = getRubrica;
    dialogRubrica.value.openDialog();
}

function openDialogRubricaDependente() {
    callback.value = getRubricaDependente;
    dialogRubrica.value.openDialog();
}

function getRubrica(response) {
    data.value.rubrica = response.rh27_rubric
    data.value.rubricaDescricao = `${response.rh27_rubric} - ${response.rh27_descr}`;
}

function getRubricaDependente(response) {
    data.value.rubricaDependente = response.rh27_rubric
    data.value.rubricaDependenteDescricao = `${response.rh27_rubric} - ${response.rh27_descr}`;
}

async function salvar(){

    let parametros = {}


    parametros.id = data.value.id;
    parametros.idade_maxima = data.value.idade_maxima;
    parametros.valor_limite = data.value.valor_limite;
    parametros.maximo_dependente = data.value.maximo_dependente;
    parametros.percentual = data.value.percentual;
    parametros.habilitaDependente = data.value.habilitaDependente;
    parametros.rubrica = data.value.rubrica;
    parametros.rubricaDependente = data.value.rubricaDependente;
    parametros.tiposParentescos = data.value.selectedParentesco.map(parentesco => parentesco.code);
    parametros.permiteServidorDependente = data.value.permiteServidorDependente;

    try {
        loading.value = true
        await window.axios.post(routes.salvar, parametros)
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Salvo com sucesso`,
            life: 5000
        });
        await buscaConfiguracao();
    } catch (e) {
        loading.value = false

        let message = e.response ? e.response.data.message : e.message;

        if (e.response.status === 422) {
            message = Object.values(e.response.data).join("<\n>")
        }

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: message,
            life: 5000
        });
    }
}


async function buscaConfiguracao () {
    loading.value = true
    await window.axios
        .get(routes.buscar)
        .then((response) => {
            if (response.data.data.configuracao) {
                let configuracao = response.data.data.configuracao;
                data.value.id = configuracao.rh311_sequencial;
                data.value.idade_maxima = configuracao.rh311_idademax;
                data.value.valor_limite = configuracao.rh311_valormax;
                data.value.maximo_dependente = configuracao.rh311_limite;
                data.value.percentual = configuracao.rh311_percentual;
                data.value.habilitaDependente = configuracao.rh311_validadependente;
                data.value.rubrica = configuracao.rubrica;
                data.value.rubricaDescricao = configuracao.descricao_rubrica;
                data.value.rubricaDependente = configuracao.rh311_rubricdepend;
                data.value.rubricaDependenteDescricao = configuracao.rubrica_dependente_descricao;
                data.value.tiposParentescos = configuracao.rh311_grauparentesco;
                let selectedParentesco = [];
                parentesco.value.map(opt => {
                    let retorno = configuracao?.rh311_grauparentesco?.find(grau  => grau == opt.code);
                    if (retorno) {
                        selectedParentesco.push(opt);
                    }
                });
                data.value.selectedParentesco = selectedParentesco;
                data.value.permiteServidorDependente = configuracao.rh311_servidordependente;
            }

            loading.value = false
        });
}

onMounted(() => {
    buscaConfiguracao();
});

</script>
<template>
    <Toast position="center" />
    <section class="flex flex-column w-full mt-4 gap-2">
        <section class="container">
            <Panel header="Configurações | Ajuda de custo" class="w-full">
                <div class="p-fluid grid mt-4">
                    <div class="field col-6 md:col-6 lg:col-3">
                        <span class="p-float-label">
                            <InputNumber inputId="valor_limite" v-model="data.valor_limite" mode="currency" currency="BRL"
                                v-tooltip.top="'Valor limite permitido para ajuda'" required showButtons />
                            <label for="valor_limite">Valor Máximo da Ajuda</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-6 lg:col-3">
                        <span class="p-float-label">
                            <InputNumber inputId="percentual" v-model="data.percentual" showButtons :minFractionDigits="2"
                                :maxFractionDigits="5" :min="0" :max="100" prefix="%"
                                v-tooltip.top="'Percentual adicional quer irá ser adicionado caso o valor seja abaixo do limite estipulado'" />
                            <label for="percentual">Percentual adicional</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-6 lg:col-3">
                        <span class="p-float-label">
                            <InputNumber inputId="maximo_dependente" v-model="data.maximo_dependente" showButtons
                                mode="decimal" required />
                            <label for="maximo_dependente">Limite de Dependente</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-6 lg:col-3">
                        <span class="p-float-label">
                            <InputNumber inputId="idade_maxima" v-model="data.idade_maxima" showButtons mode="decimal"
                                required />
                            <label for="idade_maxima">Idade limite do Dependente</label>
                        </span>
                    </div>

                    <div class="field col-6 md:col-6 lg:col-3 mt-4">
                        <div class="p-float-label">
                            <MultiSelect v-model="data.selectedParentesco"
                                inputId="selectedParentesco"
                                class="w-full"
                                display="chip"
                                :options="parentesco"
                                optionLabel="name"
                                placeholder="Grau parentesco"/>
                            <label for="selectedParentesco">Grau parantesco dependente</label>
                        </div>
                    </div>

                    <div class="field col-6 md:col-6 lg:col-3">
                        <label for="permiteServidorDependente" style="font-weight: bold; font-size: 12px;">Permite cadastro de servidor e dependente? </label>
                        <InputSwitch class="mt-2" v-model="data.permiteServidorDependente" inputId="permiteServidorDependente" />
                    </div>
                </div>
            </Panel>
        </section>
    </section>

    <section class="flex flex-column w-full mt-4 gap-2">
        <section class="container">
            <Panel header="Rubricas" class="w-full" toggleable>
                <div class="p-fluid grid mt-4">
                    <div class="field col-6">
                        <div class="p-inputgroup flex-1">
                            <span class="p-float-label">
                                <AutoComplete v-model="data.rubricaDescricao" inputId="rubricaDescricao" disabled
                                    required />
                                <label for="rubricaDescricao">Rubrica para lançamento no ponto</label>
                                <span class="p-inputgroup-addon" @click="openDialogRubrica">
                                    <i class="pi pi-search"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field col-6 mt-0">
                        <div class="p-inputgroup">
                            <Message severity="info" :closable="false">Rubrica informada será usada para lançamento do valor
                                da ajuda no ponto salário.</Message>
                        </div>
                    </div>
                </div>

                <div class="formgrid grid mt-2">
                    <div class="field col-6">
                        <div style="font-weight: bold; font-size: 12px;">Lançar rubrica para dependentes? </div>
                        <InputSwitch class="mt-2" v-model="data.habilitaDependente" inputId="habilitaDependente" />
                    </div>
                </div>

                <div class="formgrid grid mt-2" v-if="data.habilitaDependente">
                    <div class="field col-6">
                        <div class="p-inputgroup flex-1">
                            <span class="p-float-label">
                                <AutoComplete v-model="data.rubricaDependenteDescricao" disabled
                                    inputId="rubricaDependenteDescricao" />
                                <label for="rubricaDependenteDescricao">Rubrica para lançamento no ponto para os
                                    dependentes</label>
                                <span class="p-inputgroup-addon" @click="openDialogRubricaDependente">
                                    <i class="pi pi-search"></i>
                                </span>
                            </span>
                        </div>
                    </div>
                    <div class="field col-6 mt-0">
                        <div class="p-inputgroup">
                            <Message severity="info" :closable="false">Rubrica informada será usada para lançamento do valor
                                da ajuda no ponto salário com os valores do dependentes.</Message>
                        </div>
                    </div>
                </div>
            </Panel>
        </section>
        <div class="card pt-2 ">
            <div class="flex justify-content-center flex-wrap card-container">
                <Button type="button" label="Salvar" icon="pi pi-save" @click="salvar" />
            </div>
        </div>
        <DialogRubricaServidor ref="dialogRubrica" v-on:select="callback" />
        <ModalLoading :isLoading="loading" />
    </section>
</template>

<style scoped>
.p-inputgroup-addon {
    cursor: pointer !important;
}
</style>
