<script setup>
import { ref, onMounted } from "vue";
import ConfirmPopup from "primevue/confirmpopup";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";
import ConfirmDialog from "primevue/confirmdialog";

const toast = useToast();
const confirm = useConfirm();
const routes = {
    ciclos: `v4/api/educacao/matricula-online/ciclos`,
    fases: `v4/api/educacao/matricula-online/fases`,
    salvar: `v4/api/educacao/matricula-online/fases/salvar`,
    excluir: `v4/api/educacao/matricula-online/fases/{codigo}/excluir`,
}

const loading = ref(false)
const optCiclos = ref()
const fases = ref()
const form = ref({
    codigo: null,
    descricao: {
        value: null,
        disabled: false,
        label: 'Descrição'
    },
    dataCorte: {
        value: null,
        disabled: false,
        label: 'Data de Corte'
    },
    dataInicio: {
        value: null,
        disabled: false,
        label: 'Data Início'
    },
    horaInicio: {
        value: null,
        disabled: false,
        label: 'Hora Início'
    },
    dataFim: {
        value: null,
        disabled: false,
        label: 'Data Fim'
    },
    horaFim: {
        value: null,
        disabled: false,
        label: 'Hora Fim'
    },
    slctdCiclos: {
        data: null,
        disabled: false,
        label: 'Ciclo'
    },
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    },
    exibeEscolaOrigem: {
        value: false,
        label: 'Exibe Escola Origem',
        disabled: true
    },
    opcoesEscolha: {
        value: null,
        label: 'Nº Opções Escolha',
        disabled: true
    },
    opcoesObrigatoria: {
        value: null,
        label: 'Nº Opções Obrigatórias',
        disabled: true
    },
    checksPublicoAlvo: []
})

const liberaCampos = () => {
    form.value.dataCorte.disabled = form.value.descricao.value === null || form.value.descricao.value === ''
    form.value.opcoesEscolha.disabled = form.value.dataCorte.value === null || form.value.dataCorte.value === ''
    form.value.opcoesObrigatoria.disabled = form.value.opcoesEscolha.value === null || form.value.opcoesEscolha.value === ''
    form.value.slctdCiclos.disabled = form.value.opcoesEscolha.value === null || form.value.opcoesEscolha.value === ''
    form.value.dataInicio.disabled = form.value.slctdCiclos.data === null || form.value.slctdCiclos.data === ''
    form.value.horaInicio.disabled = form.value.dataInicio.value === null || form.value.dataInicio.value === ''
    form.value.dataFim.disabled = form.value.dataInicio.value === null || form.value.dataInicio.value === ''
    form.value.horaFim.disabled = form.value.dataFim.value === null || form.value.dataFim.value === ''
    form.value.exibeEscolaOrigem.disabled = form.value.dataFim.value === null || form.value.dataFim.value === ''

    form.value.btnSalvar.disabled =
        form.value.dataCorte.disabled ||
        form.value.dataInicio.disabled ||
        form.value.dataFim.disabled ||
        form.value.slctdCiclos.disabled ||
        form.value.exibeEscolaOrigem.disabled
        form.value.checksPublicoAlvo.length === 0
}

const limpaCampos = () => {
    form.value.codigo = null
    form.value.descricao.value = null
    form.value.dataCorte.value = null
    form.value.dataInicio.value = null
    form.value.dataFim.value = null
    form.value.slctdCiclos.data = null
    form.value.horaFim.value = null
    form.value.horaInicio.value = null
    form.value.exibeEscolaOrigem.value = null
    form.value.opcoesEscolha.value = null
    form.value.opcoesObrigatoria.value = null
    form.value.checksPublicoAlvo.length = 0
    liberaCampos()
}

const formataData = (data) => {
    return new Intl.DateTimeFormat('pt-BR', {timeZone: 'UTC'}).format(new Date(data))
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

const getFases = async () => {
    try {
        loading.value = true
        let fases = (await window.axios.get(routes.fases)).data.data
        loading.value = false
        return fases
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
        descricao: form.value.descricao.value,
        dataCorte: form.value.dataCorte.value.toLocaleDateString(),
        dataInicio: form.value.dataInicio.value.toLocaleDateString(),
        dataFim: form.value.dataFim.value.toLocaleDateString(),
        ciclo: form.value.slctdCiclos.data.code,
        publicosAlvo: form.value.checksPublicoAlvo,
        exibeEscolaOrigem: form.value.exibeEscolaOrigem.value,
        opcoesEscolha: form.value.opcoesEscolha.value,
        opcoesObrigatoria: form.value.opcoesObrigatoria.value
    }
    if (form.value.codigo != null) {
        parametros.codigo = form.value.codigo
    }
    if (form.value.horaInicio.value != null) {
        parametros.horaInicio = form.value.horaInicio.value.toLocaleTimeString('pt-BR',  {hour12: false})
    }
    if (form.value.horaFim.value != null) {
        parametros.horaFim = form.value.horaFim.value.toLocaleTimeString('pt-BR', {hour12: false})
    }

    try{
        loading.value = true
        await window.axios.post(routes.salvar, parametros)
        fases.value = await getFases();
        toast.add({
            severity: 'success',
            summary: 'Sucesso!',
            detail: 'Sucesso ao Salvar!',
            life: 5000
        });
        loading.value = false
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

const editar = async (data) => {
    form.value.codigo = data.codigo
    form.value.descricao.value = data.descricao
    form.value.dataCorte.value = new Date(data.dataCorte)
    form.value.dataInicio.value = new Date(data.dataInicio)
    form.value.dataFim.value = new Date(data.dataFim)
    form.value.slctdCiclos.data = optCiclos.value.filter(opt => opt.code == data.ciclo.codigo).shift()
    form.value.checksPublicoAlvo = data.publicosAlvo.map(pub => `${pub.value}`);
    form.value.exibeEscolaOrigem.value = data.exibeEscolaOrigem;
    form.value.opcoesEscolha.value = data.opcoesEscolha;
    form.value.opcoesObrigatoria.value = data.opcoesObrigatoria;
    let horas = data.horaFim.split(':')
    form.value.horaFim.value = new Date(0, 0, 0, horas[0], horas[1], horas[2])
    horas = data.horaInicio.split(':')
    form.value.horaInicio.value =  new Date(0, 0, 0, horas[0], horas[1], horas[2])
    liberaCampos()
}

const excluir = async (data) => {
    if (data.isProcessada || data.isEncerrada) {
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Fases processadas ou encerradas não podem ser excluídas!',
            life: 5000
        });
        return
    }
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
                fases.value = await getFases();
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

onMounted(async () => {
    optCiclos.value = (await getCiclos()).map(ciclo => {
        return {code: ciclo.codigo, name: ciclo.nome}
    })
    fases.value = await getFases();
    liberaCampos()
})
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container">
        <Panel header="Cadastros de Fases">
            <br>
            <div class="p-fluid grid">
                <div class="field col-24 md:col-12">
                    <span class="p-float-label">
                        <InputText
                            id="descricao"
                            v-model="form.descricao.value"
                            :disabled="form.descricao.disabled"
                            @keyup="liberaCampos"/>
                        <label for="">{{ form.descricao.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                         <Calendar inputId="dateformat"
                                   v-model="form.dataCorte.value"
                                   :disabled="form.dataCorte.disabled"
                                   @dateSelect="liberaCampos"
                                   dateFormat="dd-mm-yy"/>
                        <label for="">{{ form.dataCorte.label }}</label>
                    </span>
                </div>
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                        <InputNumber v-model="form.opcoesEscolha.value"
                                     :disabled="form.opcoesEscolha.disabled"
                                     @update:modelValue="liberaCampos"
                                     inputId="minmax-buttons" mode="decimal" showButtons :min="0" :max="5" />
                    <label for="">{{ form.opcoesEscolha.label}}</label>
                    </span>
                </div>
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                        <InputNumber v-model="form.opcoesObrigatoria.value"
                                     :disabled="form.opcoesObrigatoria.disabled"
                                     @update:modelValue="liberaCampos"
                                     inputId="minmax-buttons" mode="decimal" showButtons :min="0" :max="5" />
                    <label for="">{{ form.opcoesObrigatoria.label}}</label>
                    </span>
                </div>
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                         <Dropdown id="slctdCiclos"
                                   v-model="form.slctdCiclos.data"
                                   :options="optCiclos"
                                   optionLabel="name"
                                   @change="liberaCampos"
                                   :disabled="form.slctdCiclos.disabled"/>
                        <label for="">{{ form.slctdCiclos.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                         <Calendar inputId="dateformat"
                                   v-model="form.dataInicio.value"
                                   :disabled="form.dataInicio.disabled"
                                   @dateSelect="liberaCampos"
                                   dateFormat="dd-mm-yy"/>
                        <label for="">{{ form.dataInicio.label }}</label>
                    </span>
                </div>
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                        <Calendar id="calendar-timeonly"
                                  v-model="form.horaInicio.value"
                                  :disabled="form.horaInicio.disabled"
                                  @update:model-value="liberaCampos"
                                  timeOnly />
                        <label for="">{{ form.horaInicio.label }}</label>
                    </span>
                </div>
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                         <Calendar inputId="dateformat"
                                   v-model="form.dataFim.value"
                                   :disabled="form.dataFim.disabled"
                                   @dateSelect="liberaCampos"
                                   dateFormat="dd-mm-yy"/>
                        <label for="">{{ form.dataFim.label }}</label>
                    </span>
                </div>
                <div class="field col-24 md:col-3">
                    <span class="p-float-label">
                        <Calendar id="calendar-timeonly"
                                  v-model="form.horaFim.value"
                                  :disabled="form.horaFim.disabled"
                                  @update:model-value="liberaCampos"
                                  timeOnly />
                        <label for="">{{ form.horaFim.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-24 md:col-3 col-offset-5">
                    <label for="switch1" style="color:#a19f9d !important;">{{ form.exibeEscolaOrigem.label }}</label>
                    <InputSwitch inputId="switch1" class="ml-6" v-model="form.exibeEscolaOrigem.value" :disabled="form.exibeEscolaOrigem.disabled"/>
                </div>
            </div>
            <Fieldset legend="Público Alvo">
                <div class="p-fluid grid">
                    <div class="field col-24 md:col-4">
                        <div class="field-checkbox">
                            <Checkbox inputId="pcd" name="pcd" value="1" @change="liberaCampos" v-model="form.checksPublicoAlvo"/>
                            <label for="pcd">Candidatos PCD</label>
                        </div>
                    </div>
                    <div class="field col-24 md:col-4">
                        <div class="field-checkbox">
                            <Checkbox inputId="transferenciaRede" name="transferenciaRede" @change="liberaCampos" value="2" v-model="form.checksPublicoAlvo"/>
                            <label for="transferenciaRede">Transferência na Rede</label>
                        </div>
                    </div>
                    <div class="field col-24 md:col-4">
                        <div class="field-checkbox">
                            <Checkbox inputId="foraRede" name="foraRede" value="3" @change="liberaCampos" v-model="form.checksPublicoAlvo"/>
                            <label for="foraRede">Candidatos Fora da Rede</label>
                        </div>
                    </div>
                </div>
            </Fieldset>
            <br>
            <div class="p-fluid grid">
                <div class="field col-24 md:col-4 col-offset-4">
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
            <DataTable :value="fases" scrollable scrollHeight="300px" showGridlines style="width: 100%">
                <Column field="codigo" header="Código">
                </Column>
                <Column field="descricao" header="Descrição">
                </Column>
                <Column field="dataCorte" header="Data de Corte">
                    <template #body="slotProps">
                        {{ formataData(slotProps.data.dataCorte )}}
                    </template>
                </Column>
                <Column field="dataInicio" header="Data de Início">
                    <template #body="slotProps">
                        {{ formataData(slotProps.data.dataInicio )}}
                    </template>
                </Column>
                <Column field="horaInicio" header="Hora de Início">
                </Column>
                <Column field="dataFim" header="Data do Fim">
                    <template #body="slotProps">
                        {{ formataData(slotProps.data.dataFim )}}
                    </template>
                </Column>
                <Column field="horaFim" header="Hora Fim">
                </Column>
                <Column field="ciclo.nome" class="text-center" header="Ciclo">
                </Column>
                <Column field="isEncerrada" class="text-center" header="Encerrada">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.isEncerrada, 'pi pi-times text-red-600': !slotProps.data.isEncerrada }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="isProcessada" class="text-center" header="Processada">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.isProcessada, 'pi pi-times text-red-600': !slotProps.data.isProcessada }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="exibeEscolaOrigem" class="text-center" header="Exibe Escola Origem">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.exibeEscolaOrigem, 'pi pi-times text-red-600': !slotProps.data.exibeEscolaOrigem }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="publicosAlvo" header="Publicos Alvo">
                    <template #body="slotProps">
                        <li v-for="publico in slotProps.data.publicosAlvo">
                            {{ publico.name }}
                        </li>
                    </template>
                </Column>
                <Column field="opcoesEscolha" header="Nº Opçoes Escolha">
                </Column>
                <Column field="opcoesObrigatoria" header="Nº Opçoes Obrigatórias">
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
        width: auto;
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
