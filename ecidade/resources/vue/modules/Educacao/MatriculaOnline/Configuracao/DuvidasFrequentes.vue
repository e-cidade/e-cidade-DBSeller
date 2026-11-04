<script setup>
import { ref, onMounted } from "vue";
import ConfirmPopup from "primevue/confirmpopup";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";
import Trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.min.css';
import 'trumbowyg/dist/plugins/colors/trumbowyg.colors.min';
import 'trumbowyg/dist/plugins/history/trumbowyg.history.min';

$.trumbowyg.svgPath = window.ECIDADE_PATH + 'public/trumbowyg/ui/icons.svg';
const toast = useToast();
const confirm = useConfirm();
const routes = {
    getDuvidas: `v4/api/educacao/matricula-online/configuracoes/duvidas-frequentes`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/duvidas-frequentes/salvar`,
    excluir: `v4/api/educacao/matricula-online/configuracoes/duvidas-frequentes/{codigo}/excluir`,
}
const editor = ref({
    config: {
        btns: [
            [
                'foreColor',
                'backColor',
                'historyUndo',
                'historyRedo',
            ],
            ['strong', 'em', 'del'],
            ['formatting'],
            ['superscript', 'subscript'],
            ['link'],
            ['image'], // Our fresh created dropdown
            ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
            ['unorderedList', 'orderedList'],
            ['horizontalRule'],
            ['removeformat'],
        ]
    },
});
const loading = ref(false)
const duvidas = ref()
const form = ref({
    codigo: null,
    pergunta: {
        value: null,
        disabled: false,
        label: 'Pergunta'
    },
    resposta: {
        value: null,
        disabled: true
    },
    ativa: {
        value: true,
        label: 'Ativa'
    },
    btnSalvar: {
        disabled: false,
        label: 'Salvar Perunta'
    },
    respostas: []
})

const limpaCampos = () => {
    form.value.codigo = null
    form.value.pergunta.value = ''
    form.value.ativa.value = true
    form.value.resposta.value = ''
    form.value.respostas = []
}
const salvar = async () => {
    let parametros = {
        pergunta: form.value.pergunta.value,
        respostas: form.value.respostas.length === 0 ? [form.value.resposta.value] : form.value.respostas.map(resposta => resposta.descricao),
        ativa: form.value.ativa.value,
        ordem: duvidas.value.length + 1
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
        await buscarDuvidas();
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
    form.value.pergunta.value = data.pergunta
    form.value.ativa.value = data.ativa
    form.value.respostas = data.respostas.map(resposta => {
        return {descricao: resposta}
    })
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
                await buscarDuvidas();
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

const onRowReorder = async (event) => {
    form.value.respostas = event.value
};

const onRowReorderPerguntas = async (event) => {
    event.value.map(async (pergunta, key) => {
        let parametros = {}
        parametros.ordem = key + 1;
        parametros.codigo = pergunta.codigo
        parametros.pergunta = pergunta.pergunta
        parametros.respostas = pergunta.respostas
        parametros.ativa = pergunta.ativa
        try{
            loading.value = true
            await window.axios.post(routes.salvar, parametros)
            loading.value = false
            await buscarDuvidas();
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
    })
};

const salvarResposta = () => {
    form.value.respostas.push({descricao: form.value.resposta.value})
    form.value.resposta.value = null
}


const excluirResposta = (index) => {
    form.value.respostas = form.value.respostas.filter((resposta, key) => key !== index)
}

const buscarDuvidas = async () => {
    try {
        loading.value = true
        duvidas.value = (await window.axios.get(routes.getDuvidas)).data.data
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
    await buscarDuvidas()
})
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container">
        <Panel header="Dúvidas Frequentes">
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6 col-offset-3">
                    <span class="p-float-label">
                        <Textarea v-model="form.pergunta.value" rows="2" cols="30" />
                        <label>{{ form.pergunta.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-8 col-offset-2">
                    <label>Resposta</label>
                    <trumbowyg
                        v-model="form.resposta.value"
                        :config="editor.config"
                        name="content"
                        :disabled="form.pergunta.value === null || form.pergunta.value === ''">
                    </trumbowyg>
                </div>
                <div class="field col-12 md:col-2">
                    <Button icon="pi pi-plus" aria-label="Nova Resposta" :disabled="form.resposta.value === null" @click="salvarResposta"/>
                </div>
            </div>
            <Fieldset legend="Respostas" v-if="form.respostas.length > 0">
                <DataTable :value="form.respostas" scrollable scrollHeight="300px" showGridlines @rowReorder="onRowReorder" style="width: 100%">
                    <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" header="Ordenar"/>
                    <Column field="descricao" header="Descrição">
                        <template #body="slotProps">
                            <div v-html="slotProps.data.descricao">
                            </div>
                        </template>
                    </Column>
                    <Column headerStyle="width: 3rem" header="Ações">
                        <template #body="slotProps">
                            <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluirResposta(slotProps.index)"></i>
                        </template>
                    </Column>
                </DataTable>
            </Fieldset>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-3 col-offset-5">
                    <div class="ml-6 mb-2">{{ form.ativa.label }}</div>
                    <InputSwitch inputId="switch1" class="ml-6" v-model="form.ativa.value"/>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4 col-offset-4">
                    <Button class="p-button" :disabled="(form.resposta.value === null || form.resposta.value === '') && form.respostas.length === 0" :label="form.btnSalvar.label"
                            @click="salvar"
                    ></Button>
                </div>
            </div>
        </Panel>
    </section>
    <br>
    <div style="width: 90%; margin: 0 auto">
        <div class="p-fluid grid">
            <DataTable :value="duvidas" scrollable scrollHeight="300px" showGridlines style="width: 100%" @rowReorder="onRowReorderPerguntas">
                <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" header="Ordenar"/>
                <Column field="pergunta" header="Pergunta">
                </Column>
                <Column field="respostas" header="Respostas">
                    <template #body="slotProps">
                        <li v-for="resposta in slotProps.data.respostas">
                            <div v-html="resposta">
                            </div>
                        </li>
                    </template>
                </Column>
                <Column field="ativa" class="text-center" header="Ativa" headerStyle="width: 3rem" >
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.ativa, 'pi pi-times text-red-600': !slotProps.data.ativa }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column header="Ações" headerStyle="width: 6rem" >
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

i {
    cursor: pointer
}
</style>
