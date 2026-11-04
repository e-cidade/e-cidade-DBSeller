<script setup>
import { onMounted, ref } from 'vue';
import ModalLoading from '../../../Components/ModalLoading.vue';

// data
const retencao = ref({});
const naturezaRendimentos = ref([]);
const naturezaGrupos = ref([]);
const subcontratados = ref(false);
const selectedNatureza = ref(false);
const selectedNaturezaGrupo = ref(false);
const isLoadingNaturezas = ref(false);
const isLoading = ref(false);
const isLoadingSendForm = ref(false);

// methods
const getDataFromSession = async () => {
    retencao.value = JSON.parse(sessionStorage.getItem('retencao'));

    if (retencao.value.naturezarendimento) {
        selectedNaturezaGrupo.value = retencao.value.codnatureza_rendimento.substring(0,2);
        await getNaturezaRendimentos();
        selectedNatureza.value = retencao.value.naturezarendimento;
    }

    if (retencao.value.subcontratados) {
        subcontratados.value = JSON.parse(retencao.value.subcontratados);
    }
}

const getNaturezaRendimentos = async () => {
    const api = 'v4/api/financeiro/empenho/naturezarendimentos/grupo';
    const grupo = selectedNaturezaGrupo.value;
    const params = { grupo }

    isLoadingNaturezas.value = true;
    selectedNatureza.value = false;

    try {
        const response = await axios.get(api, { params: params });
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

const getNaturezaGrupos = async () => {
    const api = 'v4/api/financeiro/empenho/naturezarendimentos/grupos';

    try {
        const response = await axios.get(api);
        const { data: responseData } = response;

        if (responseData.error == true) {
            alert(responseData.message);
            return false;
        }

        if (responseData.data.grupos) {
            naturezaGrupos.value = responseData.data.grupos;
        }
    } catch (error) {
        alert('Erro ao buscar grupos da naturezas de rendimento');
        console.error(error);
    }
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
            'evento': 'r4020'
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

    await getNaturezaGrupos();
    await getDataFromSession();

    isLoading.value = false;
});
</script>

<template>
    <ModalLoading :is-loading="isLoading" />
    <div class="container mt-5" v-show="isLoading == false">
        <!-- Empenho -->
        <Panel header="Dados do Empenho">
            <div class="grid gap-3">
                <!-- credor -->
                <div class="col-6 flex flex-column gap-2">
                    <label for="" >Credor</label>
                    <InputText type="text" v-model="retencao.benef" readonly />
                </div>

                <!-- CNPJ -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="cnpj" >CNPJ</label>
                    <InputMask id="cnpj" mask="99.999.999/9999-99" type="text" v-model="retencao.cnpjbenef" readonly />
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
            </div>

        </Panel>

        <Divider />

        <!-- Retencao -->
        <Panel header="Dados da Retencao">
            <div class="grid gap-3">

                <!-- Tipo -->
                <div class="col-4 flex flex-column gap-2">
                    <label for="tiporet" >Tipo</label>
                    <InputText id="tiporet" v-model="retencao.tipo_retencao" readonly />
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
                    <label for="vltbruto" >Valor Bruto</label>
                    <InputNumber
                        id="vltbruto"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model.number="retencao.valor_bruto"
                        readonly
                        style="width: 100px"
                    />
                </div>

                <!-- Valor base -->
                <div class="col-3 flex flex-column gap-2">
                    <label for="vlrbase" >Valor Base</label>
                    <InputNumber
                        id="vlrbase"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao.valor_base"
                        readonly
                        style="width: 100px"
                    />
                </div>

                <!-- Valor retido -->
                <div class="col-3 flex flex-column gap-2">
                    <label for="vlrreti" >Valor Retido</label>
                    <InputNumber
                        id="vlrreti"
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
                        <label for="" >Grupo / Natureza Rendimento</label>
                        <div class="flex gap-2">
                            <!-- grupo -->
                            <Dropdown
                                v-model="selectedNaturezaGrupo"
                                :options="naturezaGrupos"
                                optionLabel="grupo"
                                optionValue="grupo"
                                placeholder="Grupo"
                                @change="getNaturezaRendimentos"
                            />

                            <!-- natureza -->
                            <Dropdown
                                v-model="selectedNatureza"
                                :options="naturezaRendimentos"
                                optionLabel="descricao"
                                optionValue="e167_sequencial"
                                placeholder="Selecione Natureza"
                                :disabled="selectedNaturezaGrupo == false || isLoadingNaturezas"
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

        <Button
            :label="isLoadingSendForm ? 'Salvando' : 'Salvar'"
            :loading="isLoadingSendForm"
            @click="saveRetencao"
        />
    </div>
</template>

<style>
    .p-dropdown .p-dropdown-label {
        white-space: unset;
    }
</style>
