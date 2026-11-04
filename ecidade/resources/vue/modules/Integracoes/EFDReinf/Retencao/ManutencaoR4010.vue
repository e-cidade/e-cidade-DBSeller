<script setup>
import { computed, onMounted, ref } from 'vue';
import ModalLoading from '../../../Components/ModalLoading.vue';
import DataTableBaseCalculo4010 from './Components/DataTableBaseCalculo4010.vue';

// data
const retencao = ref({});
const naturezaRendimentos = ref([]);
const selectedNatureza = ref(false);
const subcontratados = ref(false);
const isLoadingNaturezas = ref(false);
const isLoading = ref(false);
const isLoadingSendForm = ref(false);
const retencao_valor_bruto = computed(() => {
    let total = Number(retencao.value.valor_base) + Number(retencao.value.deducao);
    return total.toFixed(2);
});
const showComposicao = ref(false);


// methods
const getDataFromSession = async () => {
    retencao.value = JSON.parse(sessionStorage.getItem('retencao'));

    if (retencao.value.naturezarendimento) {
        selectedNatureza.value = retencao.value.naturezarendimento;
    }

    if (retencao.value.subcontratados) {
        subcontratados.value = JSON.parse(retencao.value.subcontratados);
    }
}

const getNaturezaRendimentos = async () => {
    const api = 'v4/api/financeiro/empenho/naturezarendimentos/naturezas';

    isLoadingNaturezas.value = true;
    selectedNatureza.value = false;

    try {
        const params= {declarante: 'PF'};
        const response = await axios.get(api, {params});
        const { data: responseData } = response;

        if (responseData.error == true) {
            alert(responseData.message);
            return false;
        }

        if (responseData.data.naturezas) {
            naturezaRendimentos.value = responseData.data.naturezas;
        }
    } catch (error) {
        alert('Erro ao buscar naturezas de rendimento');
        console.error(error);
    }

    isLoadingNaturezas.value = false;
}

const saveRetencao = async () => {
    isLoadingSendForm.value = true;

    if (!validateForm()) {
        return false;
    }

    try {
        const api = 'v4/api/integracoes/efd-reinf/retencao/save-retencao';
        const params = {
            'retencaoreceitas': retencao.value.retencaoreceitas,
            'naturezarendimento': selectedNatureza.value,
            'evento': 'r4010'
        }

        const response = await axios.post(api, params);
        const { data: responseData } = response;

        if (responseData.error == true) {
            alert(responseData.message);
            isLoadingSendForm.value = false;
            return false;
        }

        parent.js_getRetencoes();
        alert('Os dados da retenção foram salvos com sucesso');

    } catch (error) {
        alert(`Erro ao salvar Retenção`);
        console.error('An error occurred:', error.message);
    }

    isLoadingSendForm.value = false;
}

const validateForm = () => {
    if (!selectedNatureza.value) {
        alert('Deve selecionar a Natureza');
        return false;
    }

    return true;
}

// hooks
onMounted(async () => {
    isLoading.value = true;
    await getNaturezaRendimentos();
    await getDataFromSession();
    isLoading.value = false;
});
</script>

<template>
    <ModalLoading :is-loading="isLoading" />

    <!-- composicao base 4010 -->
    <Dialog v-model:visible="showComposicao" modal header="Pagamentos" :style="{ minWidth: '50rem' }" position="top">
        <DataTableBaseCalculo4010 :retencao="retencao.retencaoreceitas"/>
    </Dialog>

    <div class="container mt-5" v-show="isLoading == false">
        <!-- Empenho -->
        <Panel header="Dados do Empenho">
            <div class="grid gap-3">
                <!-- credor -->
                <div class="col-4 flex flex-column gap-2">
                    <label for="credor">Beneficiário</label>
                    <InputText id="credor" type="text" v-model="retencao.benef" readonly />
                </div>

                <!-- CNPJ -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="cpf">CPF</label>
                    <InputMask mask="999.999.999-99" id="cpf" type="text" v-model="retencao.cpfbenef" readonly />
                </div>

                <!-- cgm -->
                <div class="col-2 flex flex-column gap-2">
                    <label id="cgm" for="">CGM</label>
                    <InputText id="cgm" type="text" v-model="retencao.cgm" readonly />
                </div>
            </div>

            <div class="grid gap-3 mt-3">

                <!-- Numero Emepenho -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="emp" >Empenho</label>
                    <InputText id="emp" type="text" v-model="retencao.empenho" readonly />
                </div>

                <!-- Nota de liquidacao -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="op" >Ordem de Pagamento</label>
                    <InputText  id="op" type="text" v-model="retencao.ordem_pagamento" readonly />
                </div>

                <!-- Nota fiscal -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="ntfisc" >Nota Fiscal</label>
                    <InputText  id="ntfisc" type="text" v-model="retencao.nota_fiscal" readonly />
                </div>

                <!-- Data da nota -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="dtnota" >Data da Nota</label>
                    <InputText  id="dtnota" v-model="retencao.data_nota" readonly />
                </div>

                <!-- Data da Pagamento -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="fg" >Data do Pagamento</label>
                    <InputText id="fg" :model-value="retencao.data_pag" readonly/>
                </div>

                <!-- Data da Pagamento -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="fg" >Valor Liquidação</label>
                    <InputNumber
                        id="vlrliq"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao.valor_liquidacao"
                        readonly
                    />
                </div>
            </div>
        </Panel>

        <Divider />

        <!-- Retencao -->
        <Panel header="Dados da Retencao">
            <div class="grid gap-3">

                <!-- Tipo -->
                <div class="col-4 flex flex-column gap-2">
                    <label for="retencao" >Tipo</label>
                    <InputText id="retencao" v-model="retencao.tipo_retencao" readonly />
                </div>

                 <!-- Valor retido -->
                 <div class="col-2 flex flex-column gap-2">
                    <label for="aliquota" >Alíquota</label>
                    <InputText id="aliquota" v-model="retencao.aliquota" readonly />
                </div>
            </div>

            <div class="grid gap-3 mt-3">
                <!-- Valor bruto -->
                <div class="col-3 flex flex-column gap-2">
                    <label for="vlrrend" >Valor Bruto</label>
                    <InputNumber
                        id="vlrrend"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao_valor_bruto"
                        readonly
                        style="width: 100px"
                        v-tooltip.top="'Valor tributável + Dedução'"
                    />
                </div>

                <!-- Valor retido -->
                <div class="col-3 flex flex-column gap-2">
                    <label for="vlrdeducao">Valor Dedução</label>
                    <InputNumber
                        id="vlrdeducao"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao.deducao"
                        readonly
                        style="width: 100px"
                    />
                </div>

                <!-- Valor base -->
                <div class="col-3 flex flex-column gap-2">
                    <label for="vlrtri" >Valor Tributável</label>
                    <InputNumber
                        id="vlrtri"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao.valor_base"
                        readonly
                        style="width: 100px"
                        v-tooltip.top="'(Valor liquidação + Pagmentos) - Deduções'"
                        />
                    </div>

                <!-- Valor retido -->
                <div class="col-3 flex flex-column gap-2">
                <label for="vltir" >Valor Retido IRRF</label>
                    <InputNumber
                        id="vltir"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao.valor_retencao"
                        readonly
                        style="width: 100px"
                    />
                </div>
            </div>
            <div class="grid gap-3 mt-3">
                <div class="col-12">
                    <div class="flex flex-column gap-2">
                        <label for="natrend" >Natureza Rendimento</label>
                        <div class="flex gap-2">
                            <!-- natureza -->
                            <Dropdown
                                id="natrend"
                                v-model="selectedNatureza"
                                :options="naturezaRendimentos"
                                optionLabel="descricao"
                                optionValue="e167_sequencial"
                                placeholder="Selecione Natureza"
                                :disabled="isLoadingNaturezas"
                                :loading="isLoadingNaturezas"
                                filter
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid gap-3 mt-3" v-if="subcontratados">
                <div class="col-12">
                    <DataTable :value="subcontratados" tableStyle="min-width: 50rem">
                        <Column field="nome" header="Subcontratado"></Column>
                        <Column field="retencao" header="Retenção"></Column>
                        <Column field="vlrbase" header="Valor base"></Column>
                        <Column field="vlrret" header="Valor Retido"></Column>
                    </DataTable>
                </div>
            </div>
        </Panel>

        <Divider />

        <div class="flex justify-content-center gap-2">
            <Button
                icon="pi pi-save"
                :label="isLoadingSendForm ? 'Salvando' : 'Salvar'"
                :loading="isLoadingSendForm"
                @click="saveRetencao"
            />

            <Button
                icon="pi pi-list"
                label="Composição" outlined
                v-tooltip.top="'Pagamentos adicionados a base de cálculo'"
                @click="showComposicao = true"
            />
        </div>

    </div>
</template>

<style>
    .p-dropdown .p-dropdown-label {
        white-space: unset;
    }
</style>
