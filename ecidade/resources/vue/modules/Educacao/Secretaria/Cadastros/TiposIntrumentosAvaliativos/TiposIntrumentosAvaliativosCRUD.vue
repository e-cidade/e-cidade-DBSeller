<script setup>
import ModalLoading from "../../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import {ref} from "vue";
import ConfirmPopup from "primevue/confirmpopup";
const form = ref({
    codigo: null,
    nome: {
        value: null,
        label: 'Descrição',
        disabled: false
    },
    ensinos: {
        value: [],
        label: 'Ensinos',
        disabled: true
    },
    ativo: {
        value: true,
        label: 'Ativo',
        disabled: true
    },
    btnSalvar: {
        label: 'Salvar',
        disabled: true
    }
})

const confirm = useConfirm();
const toast = useToast()
const loading = ref(false)
const routes = {
    ensinos: `v4/api/educacao/secretaria/ensinos`,
    instrumentos: `v4/api/educacao/secretaria/tipos-insturmentos-avaliativos`,
    salvar: `v4/api/educacao/secretaria/tipos-insturmentos-avaliativos/salvar`,
    excluir: `v4/api/educacao/secretaria/tipos-insturmentos-avaliativos/{codigo}/excluir`
}
const instrumentos = ref();
const ensinos = ref();

const getTiposInstrumentosAvaliativos = async () => {
    try {
        instrumentos.value = (await window.axios.get(routes.instrumentos)).data.data
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

const getEnsinos = async () => {
    try {
        ensinos.value = (await window.axios.get(routes.ensinos)).data.data
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
    form.value.nome.value = data.nome
    form.value.ensinos.value = data.ensinos.map(ensino => ensino.codigo);
    form.value.ativo.value = data.ativo
    liberaCampos()
}

const excluir = async (codigo) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Deseja mesmo excluir?',
        icon: 'pi pi-exclamation-triangle',
        accept: async () => {
            try {
                loading.value = true
                let rota = routes.excluir.replace('{codigo}', codigo);
                await window.axios.delete(rota)
                loading.value = false
                toast.add({
                    severity: 'success',
                    summary: 'Sucesso!',
                    detail: 'Excluído com Sucesso!',
                    life: 5000
                });
                await getTiposInstrumentosAvaliativos()
            } catch (e) {
                loading.value = false
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: e.response.data.message,
                    life: 5000
                });
            }
        },
        reject: () => {
            return
        }
    });
}

const salvar = async() => {
    let parametros = {}
    if (form.value.codigo !== null) {
        parametros.codigo = form.value.codigo
    }
    parametros.nome = form.value.nome.value
    parametros.ensinos = form.value.ensinos.value
    parametros.ativo = form.value.ativo.value
    try {
        loading.value = true
        await window.axios.post(routes.salvar, parametros)
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Salvo com Sucesso!',
            life: 5000
        });
        await getTiposInstrumentosAvaliativos()
        limpaCampos()
        liberaCampos()
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

const limpaCampos = () => {
    form.value.codigo = null
    form.value.nome.value = null
    form.value.ensinos.value.length = 0
    form.value.ativo.value = false
}
const liberaCampos = () => {
    form.value.ensinos.disabled = form.value.nome.value === null
    form.value.ativo.disabled = form.value.ensinos.value.length === 0
    form.value.btnSalvar.disabled = form.value.nome.disabled || form.value.ativo.disabled || form.value.ensinos.disabled
}

getEnsinos();
getTiposInstrumentosAvaliativos()
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container">
        <Panel header="Tipos de Intrumentos Avaliativos" style="width: 800px; margin: 0 auto">
            <br/>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-8 col-offset-2" >
                    <span class="p-float-label">
                        <InputText
                            id="nomeArea"
                            v-model="form.nome.value"
                            :disabled="form.nome.disabled"
                            @update:modelValue="liberaCampos"
                        />
                        <label for="">{{ form.nome.label }}</label>
                    </span>
                </div>
            </div>
            <Fieldset legend="Ensinos">
                <div class="card">
                    <div class="flex flex-wrap card-container blue-container">
                        <div v-for="ensino in ensinos" class="flex field" style="width: 33%">
                            <div class="flex">
                                <Checkbox @update:modelValue="liberaCampos" v-model="form.ensinos.value" :value="ensino.codigo" :disabled="form.ensinos.disabled" @change="liberaCampos"/>
                                <label for="ingredient1" class="ml-2">{{ ensino.descricao }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </Fieldset>
            <br>
            <div class="flex flex-column align-items-center justify-content-center">
                <label class="mb-2" for="">{{ form.ativo.label }}</label>
                <InputSwitch v-model="form.ativo.value" @update:modelValue="liberaCampos" :disabled="form.ativo.disabled"/>
            </div>
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4 col-offset-4">
                    <Button class="p-button" :label="form.btnSalvar.label" icon="pi pi-save" :disabled="form.btnSalvar.disabled"
                            @click="salvar"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>
    <div style="width: 40%; margin: 0 auto;">
        <div class="p-fluid grid">
            <DataTable :value="instrumentos" paginator :rows="10" style="width: 100%">
                <Column field="codigo" header="Código"></Column>
                <Column field="nome" header="Nome"></Column>
                <Column field="ensinos" header="Ensinos">
                    <template #body="slotProps">
                        <li v-for="ensino in slotProps.data.ensinos">
                            {{ ensino.descricao }}
                        </li>
                    </template>
                </Column>
                <Column field="ativo" header="Ativo">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.ativo, 'pi pi-times text-red-600': !slotProps.data.ativo }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column header="Ações" style="width: 10%">
                    <template #body="{ data }">
                        <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)"></i>
                        <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(data['codigo'])"></i>
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
:deep(.p-fieldset-legend) {
    padding: 0!important;
    border: none;
    background: transparent!important;
}

:deep(.p-fieldset-legend-text) {
    color: #a19f9d!important;
}

:deep(.p-fieldset) {
    background: #e1dede;
    padding: 0;
}
</style>
