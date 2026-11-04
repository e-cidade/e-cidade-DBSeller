<script setup>
import { onBeforeMount, ref } from 'vue';

// data
const contribuintes = ref([]);
const resp = ref({});
const cgm  = ref(0);
const isLoadingContribuintes = ref(false);
const isLoadingSendForm = ref(false);
const isLoadingResp = ref(false);

// methods
const getContribuinte = async () => {
    isLoadingContribuintes.value = true;

    try {
        const url = 'v4/api/integracoes/efd-reinf/r4099responsavel/get-contribuinte';
        const response = await axios.get(url);
        const { data: responseData } = response;

        contribuintes.value = responseData.data.contribuinte;
    } catch (error) {
        alert('Erro ao buscar contruibuinte');
    } finally {
        isLoadingContribuintes.value = false;
    }
}

const getResp = async () => {
    isLoadingResp.value = true;

    try {
        if (!cgm.value) {
            return false;
        }

        const url = 'v4/api/integracoes/efd-reinf/r4099responsavel/get';
        const params = {cgm: cgm.value}
        const response = await axios.get(url, {params});
        const { data: responseData } = response;

        resp.value = responseData.data.resp ?? [];
    } catch (error) {
        alert('Erro ao buscar dados do responsavel');
    } finally {
        isLoadingResp.value = false;
    }
}

const sendForm = async () => {
    isLoadingSendForm.value = true;

    try {
        if (!validateForm()) {
            return false;
        }

        const url = 'v4/api/integracoes/efd-reinf/r4099responsavel/save';
        const params = {cgm: cgm.value, ...resp.value}
        const response = await axios.post(url, params);
        const { data: responseData } = response;

        if (responseData.error) {
            alert("Erro ao salvar dados do responsavel:\n" + responseData.message);
            return false;
        }

        alert('Dados Salvos com Sucesso.');

    } catch (error) {
        alert('Erro ao salvar dados do responsavel.');
    } finally {
        isLoadingSendForm.value = false;
    }
}

const validateForm = () => {
    if (!cgm.value) {
        alert('Deve-se informar o contribuinte');
        return false;
    }

    if (!resp.value.efd10_nome) {
        alert('Nome deve ser informado');
        return false;
    }

    if (!resp.value.efd10_email) {
        alert('Email deve ser informado');
        return false;
    }

    if (!resp.value.efd10_telefone) {
        alert('Telefone deve ser informado');
        return false;
    }

    if (!resp.value.efd10_cpf) {
        alert('CPF deve ser informado');
        return false;
    }

    return true;
}

// hooks
onBeforeMount(async () => {
    await getContribuinte();
});

</script>

<template>
    <div class="container mt-5">
        <Panel header="Contribuinte">
            <Dropdown
                :loading="isLoadingContribuintes"
                v-model="cgm"
                :options="contribuintes"
                optionLabel="descricao"
                optionValue="cgm"
                placeholder="Selecione Contribuinte"
                @change="getResp"
            />
        </Panel>

        <Divider/>

        <Panel header="Dados do Responsálvel">
            <div class="flex justify-content-center" v-if="isLoadingResp || !contribuintes.length">
                <ProgressSpinner style="width: 40px; margin: 0 auto;" />
            </div>
            <div class="grid gap-3" v-else>
                <!-- nome -->
                <div class="col-4 flex flex-column gap-2">
                    <label for="nome" >Nome: </label>
                    <InputText id="nome" type="text" v-model="resp.efd10_nome" required/>
                </div>

                <!-- cpf -->
                <div class="col-4 flex flex-column gap-2">
                    <label for="cpf" >CPF: </label>
                    <InputText id="cpf" type="text" v-model="resp.efd10_cpf" required/>
                </div>

                <!-- telefone -->
                <div class="col-4 flex flex-column gap-2">
                    <label for="telefone" >Telefone: </label>
                    <InputText id="telefone" type="text" v-model="resp.efd10_telefone" required/>
                </div>

                <!-- email -->
                <div class="col-4 flex flex-column gap-2">
                    <label for="email" >Email: </label>
                    <InputText id="email" type="text" v-model="resp.efd10_email" required/>
                </div>
            </div>
        </Panel>

        <Divider/>

        <Button
            :label="isLoadingSendForm ? 'Salvando' : 'Salvar'"
            :loading="isLoadingSendForm"
            @click="sendForm"
        />
    </div>
</template>
