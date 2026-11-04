<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";

const toast = useToast();
const confirm = useConfirm();
const routes = {
    getCampos: `v4/api/educacao/matricula-online/configuracoes/campos-opcionais`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/campos-opcionais/salvar`,
    getZonaResidencias: `v4/api/educacao/matricula-online/configuracoes/zona-residencias`,
    salvarZonasResidencias: `v4/api/educacao/matricula-online/configuracoes/zona-residencias/salvar`
}
const campos = ref()
const camposZonas = ref()
const loading = ref(false)
const form = ref({
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    },
    btnSalvarZona: {
        disabled: false,
        label: 'Salvar'
    }
})

const salvar = async (event) => {
    try{
        loading.value = true
        let parametros = {
            campos:  campos.value
        }
        await window.axios.post(routes.salvar, parametros)
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Sucesso ao Salvar!',
            life: 5000
        });
        loading.value = false
        await buscarCampos()
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

const salvarZonas = async () => {
    try {
        loading.value = true
        let parametros = {
            campos: camposZonas.value
        }
        await window.axios.post(routes.salvarZonasResidencias, parametros)
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Sucesso ao Salvar!',
            life: 5000
        });
        loading.value = false
        await buscasZonas()
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

const buscarCampos = async () => {
    try {
        loading.value = true
        campos.value = (await window.axios.get(routes.getCampos)).data.data
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

const buscasZonas = async () => {
    try {
        loading.value = true
        camposZonas.value = (await window.axios.get(routes.getZonaResidencias)).data.data
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
    await buscarCampos()
    await buscasZonas()
})
</script>
<template>
    <section class="container">
        <Panel header="Campos Opcionais" style="width: 720px; margin: 0 auto">
            <DataTable :value="campos" scrollable scrollHeight="300px" showGridlines>
                <Column headerStyle="width: 12rem" field="tipo.name" header="Campo">
                </Column>
                <Column headerStyle="width: 12rem" header="Texto Personalizado">
                    <template #body="slotProps">
                        <Textarea
                            v-if="slotProps.data.id == 8 || slotProps.data.id == 11"
                            v-model="slotProps.data.apresentacao"
                            autoResize
                            rows="1"
                            type="text"
                        />
                        <Textarea
                            v-else
                            v-model="slotProps.data.apresentacao"
                            disabled
                            autoResize
                            rows="1"
                            type="text"
                        />
                    </template>
                </Column>
                <Column headerStyle="width: 8rem" header="Apresenta">
                    <template #body="slotProps">
                        <Checkbox v-model="slotProps.data.apresenta" :binary="true" />
                    </template>
                </Column>
                <Column headerStyle="width: 8rem" header="Obrigatório">
                    <template #body="slotProps">
                        <Checkbox v-model="slotProps.data.obrigatorio" :binary="true" />
                    </template>
                </Column>
            </DataTable>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2 col-offset-5 mt-2">
                    <Button class="p-button" :label="form.btnSalvar.label"
                            @click="salvar"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>

    <section class="container">
        <Panel header="Zonas Residencias" style="width: 720px; margin: 0 auto">
            <DataTable :value="camposZonas" scrollable scrollHeight="300px" showGridlines>
                <Column headerStyle="width: 2rem" field="id" header="Campo">
                </Column>
                <Column headerStyle="width: 12rem" header="Texto Personalizado">
                    <template #body="slotProps">
                        <Textarea
                            v-model="slotProps.data.descricao"
                            disabled
                            autoResize
                            rows="1"
                            type="text"
                        />
                    </template>
                </Column>
                <Column headerStyle="width: 8rem" header="Apresenta">
                    <template #body="slotProps">
                        <Checkbox v-model="slotProps.data.apresenta" :binary="true" />
                    </template>
                </Column>
            </DataTable>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2 col-offset-5 mt-2">
                    <Button class="p-button" :label="form.btnSalvarZona.label"
                            @click="salvarZonas"
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
