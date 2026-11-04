<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../../Components/ModalLoading.vue";

const toast = useToast();
const baseContinuacao = ref()
const loading = ref(false)
const confirm = useConfirm();
const props = defineProps(['base', 'escola'])
const escola = ref()
const routes = {
    escola: `v4/api/educacao/escola`,
    basesEscola: `v4/api/educacao/escola/{escola}/bases-curriculares`,
    salvar: `v4/api/educacao/escola/bases-curriculares/salvarBaseContinuacao`
}

const form = ref({
    escola: {
        codigo: null,
        nome: null,
        label: 'Escola',
        disabled: true
    },
    base: {
        codigo: null,
        nome: null,
        label: 'Base',
        disabled: true
    },
    baseContinuacao: {
        data: null,
        label: 'Base Continuação',
        disabled: false
    }
})
const optionsBasesEscola = ref()

const getEscola = async () => {
    try {
        loading.value = true
        escola.value = (await window.axios.get(`${routes.escola}/${props.escola}`)).data.data;
        loading.value = false
        form.value.escola.nome = escola.value.ed18_c_nome.trim()
        form.value.base.codigo = props.base.codigo
        form.value.base.nome = props.base.descricao.trim()
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

const getBasesEscola = async () => {
    baseContinuacao.value = props.base.baseContinuacao
    try {
        loading.value = true
        let rota = routes.basesEscola.replace('{escola}', props.escola);
        let resposta = (await window.axios.get(rota)).data.data.filter(base => {
            return base.codigo != props.base.codigo
        })
        loading.value = false
        optionsBasesEscola.value = resposta.map(base => {
            return {code: base.codigo, name: base.descricao}
        })
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

const editar = () => {
    form.value.baseContinuacao.disabled = false
}

const salvar = async () => {
    try {
        let parametros = {}
        parametros.baseEscola = props.base.baseEscola
        parametros.baseContinuacao = form.value.baseContinuacao.data.code
        loading.value = true
        await window.axios.post(routes.salvar, parametros);
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Sucesso ao Salvar`,
            life: 5000
        });
        await getEscola()
        await getBasesEscola()
        baseContinuacao.value = form.value.baseContinuacao.data.code
        form.value.baseContinuacao.disabled = true
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
    if (props.base != null) {
        await getEscola()
        await getBasesEscola()
        if (baseContinuacao.value != null) {
            form.value.baseContinuacao.data = optionsBasesEscola.value.filter(base => {
                return base.code === baseContinuacao.value
            }).shift()

            form.value.baseContinuacao.disabled = true
        }
    }
})
</script>
<template>
    <br><br>
    <div style="width: 30%; margin: 0 auto; height: 550px">
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                <div class="p-inputgroup flex-1">
                    <span class="p-float-label">
                        <InputText
                            id="nomeDisciplina"
                            v-model="form.escola.nome"
                            :disabled="form.escola.disabled"/>
                        <label for="">{{ form.escola.label }}</label>
                    </span>
                </div>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                <span class="p-float-label">
                    <InputText
                        id="nomeArea"
                        v-model="form.base.nome"
                        :disabled="form.base.disabled"/>
                    <label for="">{{ form.base.label }}</label>
                </span>
            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-12">
                <div class="p-inputgroup flex-1">
                    <span class="p-float-label">
                        <Dropdown id="tipoBase" :options="optionsBasesEscola"
                                  optionLabel="name"
                                  v-model="form.baseContinuacao.data"
                                  :disabled="form.baseContinuacao.disabled"/>
                         <label for="">{{ form.baseContinuacao.label }}</label>
                    </span>
                    <Button  @click="editar" icon="pi pi-pencil" aria-label="Filter" v-if="baseContinuacao != null" style="width: 40px"/>
                </div>

            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-4 col-offset-4">
                <Button class="p-button" label="Salvar"
                        :disabled="form.baseContinuacao.data === null || form.baseContinuacao.disabled"
                        @click="salvar"
                ></Button>
            </div>
        </div>
    </div>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>

</style>
