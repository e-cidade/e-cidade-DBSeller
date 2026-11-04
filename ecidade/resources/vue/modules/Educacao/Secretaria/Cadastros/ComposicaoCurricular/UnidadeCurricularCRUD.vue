<script setup>
import ModalLoading from "../../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
import {ref} from "vue";
const form = ref({
    codigo: null,
    nome: null,
    label: 'Descrição'
})

const toast = useToast()
const loading = ref(false)
const routes = {
    unidades: `v4/api/educacao/censo/tabelas-censo/unidades-curriculares`,
    salvar: `v4/api/educacao/censo/tabelas-censo/unidades-curriculares/salvar`,
    excluir: `v4/api/educacao/censo/tabelas-censo/unidades-curriculares/{codigo}/excluir`
}
const unidades = ref();

const getUnidadesCurriculares = async () => {
    try {
        unidades.value = (await window.axios.get(routes.unidades)).data.data
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

const editar = async (data) => {
    form.value.codigo = data.codigo
    form.value.nome = data.nome
}

const excluir = async (codigo) => {
    try {
        loading.value = true
        let rota = routes.excluir.replace('{codigo}', codigo);
        loading.value = false
        await window.axios.delete(rota)
        await getUnidadesCurriculares()
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

const salvar = async() => {
    let parametros = {}
    if (form.value.codigo !== null) {
        parametros.codigo = form.value.codigo
    }
    parametros.nome = form.value.nome
    try {
        loading.value = true
        await window.axios.post(routes.salvar, parametros)
        loading.value = false
        form.value.codigo = null
        form.value.nome = null
        await getUnidadesCurriculares()
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

const mostraInfo = () => {
    toast.add({
        severity: 'warn',
        summary: 'Aviso',
        detail: 'Não pode ser editado nem excluído pois é padrão do censo!',
        life: 5000
    });
}
getUnidadesCurriculares()
</script>
<template>
    <section class="container">
        <Panel header="Unidades Curriculares" style="width: 600px; margin: 0 auto">
            <br/>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-8 col-offset-2" >
                <span class="p-float-label">
                    <InputText
                        id="nomeArea"
                        v-model="form.nome"
                        :disabled="form.disabled"/>
                    <label for="">{{ form.label }}</label>
                </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4 col-offset-4">
                    <Button class="p-button" label="Salvar" icon="pi pi-save" :disabled="form.nome === null"
                            @click="salvar"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>
    <div style="width: 40%; margin: 0 auto;">
        <div class="p-fluid grid">
            <DataTable :value="unidades" paginator :rows="10" style="width: 100%">
                <Column field="codigo" header="Código" style="width: 20%"></Column>
                <Column field="nome" header="Nome" style="width: 70%"></Column>
                <Column header="Ações" style="width: 10%">
                    <template #body="{ data }">
                        <i class="pi pi-info text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @mouseover="mostraInfo" v-if="data['codigo'] <= 8"></i>
                        <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)" v-if="data['codigo'] > 8"></i>
                        <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(data['codigo'])"  v-if="data['codigo'] > 8"></i>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
    <ModalLoading :isLoading="loading"/>
</template>
<style scoped>
    i {
        cursor: pointer
    }
</style>
