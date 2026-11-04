<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import ModalLoading from "../../../Components/ModalLoading.vue";

const toast = useToast();
const routes = {
    getCores: `v4/api/educacao/matricula-online/configuracoes/cores-personalizadas/`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/cores-personalizadas/salvar`,
    excluir: `v4/api/educacao/matricula-online/configuracoes/cores-personalizadas/{codigo}/excluir`,
}

const loading = ref(false)
const cores = ref()
const form = ref({
    codigo: null,
    slctdItens: {
        data: null,
        disabled: false,
        label: 'Item'
    },
    cor: null,
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    }
})

const limpaCampos = () => {
    form.value.codigo = null
    form.value.slctdItens.data = null
    form.value.cor = null
}
const salvar = async () => {
    const formData = new FormData()
    let cor = `#${form.value.cor}`;
    formData.append('cor', cor);

    if (form.value.codigo != null) {
        formData.append('codigo', form.value.codigo);
    }
    try{
        loading.value = true
        await window.axios.post(routes.salvar, formData)
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Sucesso ao Salvar!',
            life: 5000
        });
        loading.value = false
        await buscarCores()
        limpaCampos()
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
const buscarCores = async () => {
    try {
        loading.value = true
        cores.value = (await window.axios.get(routes.getCores)).data.data
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
const setaCor = () => {
    form.value.codigo = form.value.slctdItens.data.id
    form.value.cor = form.value.slctdItens.data.cor
}
const editar = async (data) => {
    form.value.codigo = data.codigo
    form.value.cor = data.cor
    const item = cores.value.filter(item => item.codigo == data.item.codigo).map(it => {
        delete it.disabled
        return it
    })
    form.value.slctdItens.data = item[0]

}
onMounted(async () => {
    await buscarCores()
})
</script>
<template>
    <section class="container">
        <Panel header="Cores Personalizadas" style="width: 500px; margin: 0 auto">
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6 col-offset-3">
                    <span class="p-float-label">
                          <Dropdown id="tipoBase" :options="cores"
                                optionLabel="tipo.name"
                                v-model="form.slctdItens.data"
                                :disabled="form.slctdItens.disabled"
                                    @update:model-value="setaCor"
                          />
                         <label for="">{{ form.slctdItens.label }}</label>
                    </span>
                </div>
                <div class="field col flex justify-content-center">
                    <ColorPicker v-model="form.cor" inline :disabled="form.slctdItens.data === null" />
                </div>
                <div class="field col-12 md:col-6 col-offset-3">
                   <span class="p-float-label">
                        <InputText
                            v-model="form.cor"
                            :disabled="form.slctdItens.data === null"
                        />
                        <label for="">Código Hexa-Decimal</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2 col-offset-5">
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
