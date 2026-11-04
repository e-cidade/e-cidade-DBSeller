<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import Divider from 'primevue/divider';
import ModalLoading from "../../../Components/ModalLoading.vue";

const toast = useToast();
const routes = {
    getParametros: `v4/api/educacao/matricula-online/configuracoes/parametros-notificacoes/getParametros`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/parametros-notificacoes/salvar`
}
const campos = ref()
const loading = ref(false)
const form = ref({
    checks: null,
    telefoneCentralMatriculas: null
})
const btnSalvar = ref({
    disabled: false,
    label: 'Salvar'
})

const salvar = async (event) => {
    try{
        loading.value = true
        let parametros = {
            campos:  form.value
        }
        await window.axios.post(routes.salvar, parametros)
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Sucesso ao Salvar!',
            life: 5000
        });
        loading.value = false
        await buscarParametros()
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const buscarParametros = async () => {
    try {
        loading.value = true
        form.value = (await window.axios.get(routes.getParametros)).data.data
        loading.value = false
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

onMounted(async () => {
    await buscarParametros()
})
</script>
<template>
    <section class="container">
        <Panel header="Parâmetros do envio de notificações" style="width: 500px; margin: 0 auto">
            <div class="p-fluid grid">
                <div class="field col-12 md:col-12">
                    <span class="p-float-label">
                        <strong>Processar designação:</strong>
                    </span>
                </div>
            </div>
            <div class="card flex flex-wrap justify-content gap-3">
                <div class="flex align-items-center">
                    <Checkbox inputId="processardesignacaoemail" 
                    name="processardesignacaoemail" 
                    value="processardesignacaoemail" v-model="form.checks" 
                    />
                    <label for="processardesignacaoemail" class="ml-2"> E-mail </label>
                </div>
                <div class="flex align-items-center">
                    <Checkbox inputId="processardesignacaosms" 
                    name="processardesignacaosms" 
                    value="processardesignacaosms" v-model="form.checks" 
                    />
                    <label for="processardesignacaosms" class="ml-2"> SMS </label>
                </div>
                <div class="flex align-items-center">
                    <Checkbox inputId="processardesignacaowhatsapp" 
                    name="processardesignacaowhatsapp" 
                    value="processardesignacaowhatsapp" v-model="form.checks" 
                    />
                    <label for="processardesignacaowhatsapp" class="ml-2"> Whatsapp </label>
                </div>
            </div>
            <Divider />
            <div class="p-fluid grid">
                <div class="field col-12 md:col-12">
                    <span class="p-float-label">
                        <strong>Reprocessar designação automática:</strong>
                    </span>
                </div>
            </div>
            <div class="card flex flex-wrap justify-content gap-3">
                <div class="flex align-items-center">
                    <Checkbox inputId="reprocessardesignacaoautomaticaemail" 
                    name="reprocessardesignacaoautomaticaemail" 
                    value="reprocessardesignacaoautomaticaemail" v-model="form.checks" 
                    />
                    <label for="reprocessardesignacaoautomaticaemail" class="ml-2"> E-mail </label>
                </div>
                <div class="flex align-items-center">
                    <Checkbox inputId="reprocessardesignacaoautomaticasms" 
                    name="reprocessardesignacaoautomaticasms" 
                    value="reprocessardesignacaoautomaticasms" v-model="form.checks" 
                    />
                    <label for="reprocessardesignacaoautomaticasms" class="ml-2"> SMS </label>
                </div>
                <div class="flex align-items-center">
                    <Checkbox inputId="reprocessardesignacaoautomaticawhatsapp" 
                    name="reprocessardesignacaoautomaticawhatsapp" 
                    value="reprocessardesignacaoautomaticawhatsapp" v-model="form.checks" 
                    />
                    <label for="reprocessardesignacaoautomaticawhatsapp" class="ml-2"> Whatsapp </label>
                </div>
            </div>
            <Divider />
            <div class="p-fluid grid">
                <div class="field col-12 md:col-12">
                    <span class="p-float-label">
                        <strong>Lista de espera manutenção:</strong>
                    </span>
                </div>
            </div>
            <div class="card flex flex-wrap justify-content gap-3">
                <div class="flex align-items-center">
                    <Checkbox inputId="manutencaolistaesperaemail" 
                    name="manutencaolistaesperaemail" 
                    value="manutencaolistaesperaemail" v-model="form.checks" 
                    />
                    <label for="manutencaolistaesperaemail" class="ml-2"> E-mail </label>
                </div>
                <div class="flex align-items-center">
                    <Checkbox inputId="manutencaolistaesperasms" 
                    name="manutencaolistaesperasms" 
                    value="manutencaolistaesperasms" v-model="form.checks" 
                    />
                    <label for="manutencaolistaesperasms" class="ml-2"> SMS </label>
                </div>
                <div class="flex align-items-center">
                    <Checkbox inputId="manutencaolistaesperawhatsapp" 
                    name="manutencaolistaesperawhatsapp" 
                    value="manutencaolistaesperawhatsapp" v-model="form.checks" 
                    />
                    <label for="manutencaolistaesperawhatsapp" class="ml-2"> Whatsapp </label>
                </div>
            </div>
            <Divider />
            <div class="p-fluid grid">
                <div class="field col-12 md:col-12">
                    <span class="p-float-label">
                        <strong>Telefone da central de matrículas(com DDD):</strong>
                    </span>
                </div>
            </div>
            <FloatLabel>
                <InputText v-model="form.telefoneCentralMatriculas" v-mask="['(##) #####-####','(##) ####-####']" />
            </FloatLabel>
            <Divider />
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2 col-offset-5 mt-2">
                    <Button class="p-button" :label="btnSalvar.label"
                            @click="salvar"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>

</style>
