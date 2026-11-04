<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../../Components/ModalLoading.vue";

const toast = useToast();
const baseAtos = ref()
const loading = ref(false)
const confirm = useConfirm();
const props = defineProps(['base', 'escola'])
const escola = ref()
const routes = {
    escola: `v4/api/educacao/escola`,
    salvar: `v4/api/educacao/escola/bases-curriculares/baseEscola/atos/salvar`,
    cursoAtos: `v4/api/educacao/escola/${props.escola}/cursos`,
    baseAtos: `v4/api/educacao/escola/bases-curriculares/baseEscola/{codigo}/atos`,
    excluir: `v4/api/educacao/escola/bases-curriculares/baseEscola/atos/{codigo}/excluir`

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
    atoLegal: {
        data: null,
        label: 'Ato Legal',
        disabled: false
    }
})
const optionsAtoLegal = ref()

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

const getBaseAtos = async () => {
    try {
        loading.value = true
        let rota = routes.baseAtos.replace('{codigo}', props.base.baseEscola);
        baseAtos.value = (await window.axios.get(rota)).data.data
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
const getAtosLegais = async () => {
    try {
        loading.value = true
        optionsAtoLegal.value = (
            await window.axios.get(`${routes.cursoAtos}/${props.base.curso.codigo}`)
        ).data.data.atos.map(ato => {
            let nome = `${ato.numero} - ${ato.finalidade} - ${ato.tipoAto} - ${ato.competencia} - ${ato.ano}`
            return {code: ato.codigo, name: nome}
        })
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
const salvar = async () => {
    try {
        let parametros = {}
        parametros.baseEscola = props.base.baseEscola
        parametros.ato = form.value.atoLegal.data.code
        loading.value = true
        await window.axios.post(routes.salvar, parametros);
        loading.value = false
        await getBaseAtos()
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Sucesso ao Salvar`,
            life: 5000
        });
        await getEscola()
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

const excluir = async (codigo) => {
    confirm.require({
        message: "Excluir este registro?",
        header: "Confirmação",
        icon: "pi pi-exclamation-triangle",
        accept: async () => {
            loading.value = true
            try {
                let rota = routes.excluir.replace('{codigo}', codigo);
                await window.axios.delete(rota)
                await getBaseAtos()
            } catch (e) {
                loading.value = false
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: e.response.data.message,
                    life: 5000
                });
            }
            loading.value = false
        },
    });
}

onMounted(async () => {
    if (props.base != null) {
        await getEscola()
        await getAtosLegais()
        await getBaseAtos()
    }
})
</script>
<template>
    <br><br>
    <div style="width: 30%; margin: 0 auto; height: 225px">
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
                        <Dropdown id="tipoBase" :options="optionsAtoLegal"
                                  optionLabel="name"
                                  v-model="form.atoLegal.data"
                                  :disabled="form.atoLegal.disabled"/>
                         <label for="">{{ form.atoLegal.label }}</label>
                    </span>
                </div>

            </div>
        </div>
        <div class="p-fluid grid">
            <div class="field col-12 md:col-4 col-offset-4">
                <Button class="p-button" label="Salvar"
                        @click="salvar"
                ></Button>
            </div>
        </div>
    </div>
    <div style="width: 40%; margin: 0 auto; height: 225px">
        <div class="p-fluid grid">
            <DataTable :value="baseAtos" scrollable scrollHeight="300px" showGridlines style="width: 100%">
                <Column field="numero" header="Número">
                </Column>
                <Column field="finalidade" header="Finalidade">
                </Column>
                <Column field="tipoAto" header="Tipo">
                </Column>
                <Column field="competencia" header="Competência">
                </Column>
                <Column field="ano" class="text-center" header="Ano">
                </Column>
                <Column header="Ações">
                    <template #body="slotProps">
                        <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(slotProps.data.baseAto)"></i>
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
