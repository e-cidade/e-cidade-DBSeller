<script setup>
import { onMounted, ref } from 'vue';
import ModalLoading from '../../../Components/ModalLoading.vue';

// data
const retencao = ref({});
const naturezaRendimentos = ref([]);
const naturezaGrupos = ref([]);
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

    try {
        if (!validateForm()) {
            return false;
        }

        const api = 'v4/api/integracoes/efd-reinf/retencao/save-retencao';
        const params = {
            'retencaoreceitas': retencao.value.retencaoreceitas,
            'naturezarendimento': selectedNatureza.value,
            'evento': 'r4040'
        }

        const response = await axios.post(api, params);
        const { data: responseData } = response;

        if (responseData.error == true) {
            alert(responseData.message);
            return false;
        }

        parent.js_getRetencoes();
        alert('Os dados da retenção foram salvos com sucesso');

    } catch (error) {
        alert(`Erro ao salvar Retenção`);
        console.error('An error occurred:', error.message);
    } finally {
        isLoadingSendForm.value = false;
    }
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
                <div class="col-4 flex flex-column gap-2">
                    <label for="" >CNPJ</label>
                    <InputText type="text" v-model="retencao.cnpjbenef" readonly />
                </div>

                <!-- Numero Emepenho -->
                <div class="col-2 flex flex-column gap-2">
                    <label for="" >Empenho</label>
                    <InputText type="text" v-model="retencao.empenho" readonly />
                </div>
            </div>
        </Panel>

        <Divider />

        <!-- Retencao -->
        <Panel header="Dados da Retencao">
            <div class="grid gap-3">

                <!-- Tipo -->
                <div class="col-12 flex flex-column gap-2">
                    <label for="" >Tipo</label>
                    <InputText id="username" v-model="retencao.tipo_retencao" class="w-6" readonly />
                </div>

                <!-- Valor bruto -->
                <div class="col-3 flex flex-column gap-2">
                    <label for="" >Valor Bruto</label>
                    <InputNumber
                        id="username"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao.valor_bruto"
                        readonly
                        style="width: 100px"
                    />
                </div>

                <!-- Valor base -->
                <div class="col-3 flex flex-column gap-2">
                    <label for="" >Valor Base</label>
                    <InputNumber
                        id="username"
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
                    <label for="" >Valor Retido</label>
                    <InputNumber
                        id="username"
                        mode="currency"
                        currency="BRL"
                        locale="pt-BR"
                        v-model="retencao.valor_retencao"
                        readonly
                        style="width: 100px"
                    />
                </div>

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

        </Panel>

        <Divider />

        <Button
            :label="isLoadingSendForm ? 'Salvando' : 'Salvar'"
            :loading="isLoadingSendForm"
            @click="saveRetencao"
        />
    </div>
</template>
