<script setup>
import { ref, onMounted } from "vue";
import ConfirmPopup from "primevue/confirmpopup";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";
const toast = useToast();
const confirm = useConfirm();
const routes = {
    ciclos: `v4/api/educacao/matricula-online/ciclos`,
    ensinos: `v4/api/educacao/secretaria/ensinos`,
    salvar: `v4/api/educacao/matricula-online/ciclos/salvar`,
    excluir: `v4/api/educacao/matricula-online/ciclos/{codigo}/excluir`,
}

const loading = ref(false)
const ensinos = ref()
const ciclos = ref()
const form = ref({
    codigo: null,
    nome: {
        value: null,
        disabled: false,
        label: 'Descrição'
    },
    sigla: {
        value: null,
        disabled: false,
        label: 'Sigla'
    },
    dataCadastro: {
        value: null,
        disabled: false,
        label: 'Data do Cadastro'
    },
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    },
    checksEnsinos: [],
    isEJA: {
        value: [false],
        label: 'EJA',
        disabled: false
    },
    isAtivo: {
        value: [true],
        label: 'Ativo',
        disabled: false
    }
})

const liberaCampos = () => {
    form.value.sigla.disabled = form.value.nome.value === null || form.value.nome.value === ''
    form.value.dataCadastro.disabled = form.value.sigla.value === null || form.value.sigla.value === ''
    form.value.isEJA.disabled = form.value.dataCadastro.value === null || form.value.dataCadastro.value === ''
    form.value.isAtivo.disabled = form.value.dataCadastro.value === null || form.value.dataCadastro.value === ''

    form.value.btnSalvar.disabled =
        form.value.sigla.disabled ||
        form.value.dataCadastro.disabled ||
        form.value.checksEnsinos.length === 0
}

const limpaCampos = () => {
    form.value.nome.value = null
    form.value.sigla.value = null
    form.value.dataCadastro.value = null
    form.value.checksEnsinos.length = 0

    form.value.isAtivo.value = [false]
    form.value.isEJA.value = [false]
    liberaCampos()
}

const getEnsinos = async () => {
    try {
        loading.value = true
        let ensinos = (await window.axios.get(routes.ensinos)).data.data
        loading.value = false
        return ensinos
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

const getCiclos = async () => {
    try {
        loading.value = true
        let ciclos = (await window.axios.get(routes.ciclos)).data.data
        loading.value = false
        return ciclos
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

const salvar = async () => {
    let parametros = {
        isAtivo: form.value.isAtivo.value.length > 0,
        dataCadastro: form.value.dataCadastro.value.toLocaleDateString(),
        sigla: form.value.sigla.value,
        nome: form.value.nome.value,
        isEJA: form.value.isEJA.value.length > 0,
        ensinos: form.value.checksEnsinos
    }

    if (form.value.codigo != null) {
        parametros.codigo = form.value.codigo
    }
    try{
        loading.value = true
        await window.axios.post(routes.salvar, parametros)
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Sucesso ao Salvar!',
            life: 5000
        });
        loading.value = false
        limpaCampos()
        ciclos.value = await getCiclos();
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
    form.value.dataCadastro.value = new Date(data.dataCadastro)
    form.value.sigla.value = data.sigla
    form.value.isEJA.value = data.isEJA ? [data.isEJA] : []
    form.value.isAtivo.value = data.isAtivo ? [data.isAtivo] : []
    form.value.checksEnsinos = data.ensinos.map(ensino => ensino.codigo);
    liberaCampos()
}

const excluir = async (data) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Deseja mesmo excluir?',
        icon: 'pi pi-exclamation-triangle',
        accept: async () => {
            try {
                let rota = routes.excluir.replace('{codigo}', data.codigo);
                loading.value = true
                await window.axios.delete(rota)
                toast.add({
                    severity: 'success',
                    summary: 'Sucesso!',
                    detail: 'Excluído com sucesso!',
                    life: 5000
                });
                loading.value = false
                ciclos.value = await getCiclos();
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

const formataData = (data) => {
    return new Intl.DateTimeFormat('pt-BR', {timeZone: 'UTC'}).format(new Date(data))
}

onMounted(async () => {
    ensinos.value = await getEnsinos();
    ciclos.value = await getCiclos();
    liberaCampos()
})
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container">
        <Panel header="Cadastros de Ciclos">
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6">
                    <span class="p-float-label">
                        <InputText
                            id="descricao"
                            v-model="form.nome.value"
                            :disabled="form.nome.disabled"
                            @keyup="liberaCampos"/>
                        <label for="">{{ form.nome.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-3">
                    <span class="p-float-label">
                        <InputText
                            id="sigla"
                            v-model="form.sigla.value"
                            :disabled="form.sigla.disabled"
                            @keyup="liberaCampos"/>
                        <label for="">{{ form.sigla.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-3">
                    <span class="p-float-label">
                         <Calendar inputId="dateformat"
                                   v-model="form.dataCadastro.value"
                                   :disabled="form.dataCadastro.disabled"
                                   @dateSelect="liberaCampos"
                                   dateFormat="dd-mm-yy"/>
                        <label for="">{{ form.dataCadastro.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4 col-offset-3">
                    <div class="flex align-items-center">
                        <Checkbox v-model="form.isAtivo.value" inputId="isAtivo" name="isAtivo" :value="true" :disabled="form.isAtivo.disabled"/>
                        <label for="ingredient1" class="ml-2">{{ form.isAtivo.label }}</label>
                    </div>
                </div>
                <div class="field col-12 md:col-3">
                    <div class="flex align-items-center">
                        <Checkbox v-model="form.isEJA.value" inputId="isEJA" name="isEJA" :value="true" :disabled="form.isEJA.disabled"/>
                        <label for="ingredient1" class="ml-2">{{ form.isEJA.label }}</label>
                    </div>
                </div>
            </div>
            <Fieldset legend="Ensinos">
                <div class="card">
                    <div class="flex flex-wrap card-container blue-container">
                        <div v-for="ensino in ensinos" class="flex field" style="width: 33%">
                            <div class="flex">
                                <Checkbox v-model="form.checksEnsinos" inputId="isEJA" name="isEJA" :value="ensino.codigo" @change="liberaCampos"/>
                                <label for="ingredient1" class="ml-2">{{ ensino.descricao }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </Fieldset>
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4 col-offset-4">
                    <Button class="p-button" :disabled="form.btnSalvar.disabled" :label="form.btnSalvar.label"
                            @click="salvar"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>
    <br>
    <div style="width: 90%; margin: 0 auto">
        <div class="p-fluid grid">
            <DataTable :value="ciclos" scrollable scrollHeight="300px" showGridlines style="width: 100%">
                <Column field="nome" header="Descrição">
                </Column>
                <Column field="sigla" header="Sigla">
                </Column>
                <Column field="dataCadastro" header="Data de Cadastro">
                    <template #body="slotProps">
                        {{ formataData(slotProps.data.dataCadastro) }}
                    </template>
                </Column>
                <Column field="isEJA" class="text-center" header="EJA">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.isEJA, 'pi pi-times text-red-600': !slotProps.data.isEJA }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="isAtivo" class="text-center" header="Ativo">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.isAtivo, 'pi pi-times text-red-600': !slotProps.data.isAtivo }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="ensinos" header="Ensinos">
                    <template #body="slotProps">
                        <li v-for="ensino in slotProps.data.ensinos">
                            {{ ensino.descricao }}
                        </li>
                    </template>
                </Column>
                <Column header="Ações">
                    <template #body="{ data }">
                        <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)"></i>
                        <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(data)"></i>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>

.container {
    width: 1000px
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

i {
    cursor: pointer
}
</style>
