<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";
const toast = useToast();
const confirm = useConfirm();
const routes = {
    getParametros: `v4/api/educacao/matricula-online/configuracoes/parametros`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/parametros/salvar`
}
const parametros = ref()
const loading = ref(false)
const form = ref({
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    }
})

const salvar = async (event) => {
    try{
        loading.value = true
        let dados = {
            parametros:  parametros.value
        }
        await window.axios.post(routes.salvar, dados)
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
        parametros.value = (await window.axios.get(routes.getParametros)).data.data
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
const teste = ref(false)
onMounted(async () => {
    await buscarParametros()
})
</script>
<template>
    <section class="container">
        <Panel header="Parametros" style="width: 500px; margin: 0 auto">
            <div class="p-fluid grid mt-5">
                <div class="field col-12 md:col-12 mt-2" v-for="param in parametros">
                    <div class="flex align-items-center" v-if="param.componente === 'Checkbox'">
                        <label for="ingredient1" class="mr-5"> <b>{{ param.label}}</b> </label>
                        <Checkbox v-model="param.data" :binary="true" />

                    </div>
                    <span class="p-float-label" v-if="param.componente === 'Dropdown'">
                        <Dropdown id="tipoBase" :options="param.options"
                                    optionLabel="label"
                                    v-model="param.data"
                                    :disabled="false"
                          />
                         <label for="">{{ param.label }}</label>
                    </span>
                    <span class="p-float-label" v-if="param.componente === 'InputText'">
                        <InputText v-model="param.data"
                                  :disabled="false"
                        />
                         <label for="">{{ param.label }}</label>
                    </span>
                    <span class="p-float-label" v-if="param.componente === 'InputNumber'">
                        <InputNumber v-model="param.data"
                                   :disabled="false"
                        />
                         <label for="">{{ param.label }}</label>
                    </span>
                    <hr>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2 col-offset-5 mt-2">
                    <Button class="p-button" :label="form.btnSalvar.label"
                            @click="salvar"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>

.container {
    width: 1000px
}

i {
    cursor: pointer
}
</style>
