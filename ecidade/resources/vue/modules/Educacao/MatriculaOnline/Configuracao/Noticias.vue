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
import FileUpload from "primevue/fileupload";
import VisualizadorNoticias from "../Components/VisualizadorNoticias.vue";
import Message from "primevue/message";

$.trumbowyg.svgPath = window.ECIDADE_PATH + 'public/trumbowyg/ui/icons.svg';
const toast = useToast();
const confirm = useConfirm();
const routes = {
    salvar: `v4/api/educacao/matricula-online/configuracoes/noticias/salvar`,
    getNoticias: `v4/api/educacao/matricula-online/configuracoes/noticias`,
    excluir: `v4/api/educacao/matricula-online/configuracoes/noticias/{id}/excluir`,
}


const uploader = ref()
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
const noticias = ref()
const form = ref({
    id: null,
    titulo: {
        value: null,
        disabled: false,
        label: 'Título'
    },
    data: {
        value: null,
        disabled: true,
        label: 'Data'
    },
    texto: {
        value: null,
        disabled: false,
        label: 'Texto'
    },
    ativa: {
        value: true,
        label: 'Ativa'
    },
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    },
    imagem: null
})

const exibeNoticia = ref(false);
const noticiaExibir = ref()

const visulaizar = (data) => {
    exibeNoticia.value = true
    noticiaExibir.value = data
}
const limpaCampos = () => {
    form.value.id = null
    form.value.titulo.value = ""
    form.value.data.value =  null
    form.value.ativa.value = true
    form.value.texto.value = ""
    form.value.imagem = null
    uploader.value.clear()
}
const salvar = async () => {
    const formData = new FormData()

    formData.append('titulo', form.value.titulo.value)
    formData.append('texto', form.value.texto.value)
    formData.append('data', new Intl.DateTimeFormat('pt-BR', {timeZone: 'UTC'}).format(form.value.data.value).replaceAll('/', '-'))
    formData.append('ativa', form.value.ativa.value)

    if (form.value.imagem != null) {
        formData.append('imagem', form.value.imagem)
    }

    if (form.value.id != null) {
        formData.append('id', form.value.id);
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
        limpaCampos()
        await buscarNoticias()
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
    form.value.id = data.id
    form.value.titulo.value = data.titulo
    let date = new Date(data.data)
    date = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
    form.value.data.value =  date
    form.value.ativa.value = data.ativa
    form.value.texto.value = data.texto
}

const excluir = async (data) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Deseja mesmo excluir?',
        icon: 'pi pi-exclamation-triangle',
        accept: async () => {
            try {
                let rota = routes.excluir.replace('{id}', data.id);
                loading.value = true
                await window.axios.delete(rota)
                toast.add({
                    severity: 'success',
                    summary: 'Sucesso!',
                    detail: 'Excluído com sucesso!',
                    life: 5000
                });
                loading.value = false
                await buscarNoticias();
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
        },
        reject: () => {
            return
        }
    });
}


const salvarResposta = () => {
    form.value.respostas.push({descricao: form.value.resposta.value})
    form.value.resposta.value = null
}


const excluirResposta = (index) => {
    form.value.respostas = form.value.respostas.filter((resposta, key) => key !== index)
}

const buscarNoticias = async () => {
    try {
        loading.value = true
        noticias.value = (await window.axios.get(routes.getNoticias)).data.data
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
    await buscarNoticias()
})
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section style="width: 90vw; margin: 0 auto">
        <Panel header="Notícias" class="mt-3">
            <br>
            <div class="p-fluid grid">
                <div class="field col-6">
                    <label>{{ form.titulo.label }}</label>
                    <trumbowyg
                        v-model="form.titulo.value"
                        :config="editor.config"
                        name="content"
                    >
                    </trumbowyg>
                </div>
                <div class="field col-6">
                    <label>{{ form.texto.label}}</label>
                    <trumbowyg
                        v-model="form.texto.value"
                        :config="editor.config"
                        name="content"
                    >
                    </trumbowyg>
                </div>
            </div>
            <Message severity="info" :closable="false" v-if="form.id !== null" style="width: 800px; margin: 0 auto">
                <div style='font-size: 10pt; '>
                    Ao carregar uma nova imagem, esta substituirá a anterior. Ao não carrgear nenhuma imagem a anterior será mantida
                </div>
            </Message>
            <div class="p-fluid grid mt-5">
                <div class="field col-12 md:col-2 col-offset-3 mt-3">
                   <span class="p-float-label">
                        <Calendar inputId="dateformat"
                                  v-model="form.data.value"
                                  dateFormat="dd/mm/yy"/>
                        <label for="">{{ form.data.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-2">
                    <div class="card flex justify-content-center">
                        <div class="flex flex-column gap-2">
                            <label>{{ form.ativa.label }}</label>
                            <InputSwitch inputId="switch1" v-model="form.ativa.value"/>
                        </div>
                    </div>
                </div>
                <div class="field col-12 md:col-2 mt-3">
                    <div class="card flex justify-content-center">
                        <FileUpload ref="uploader" chooseLabel="Imagem" mode="basic" customUpload name="imagem" accept=".png, .jpg, .jpeg, .webp" @select="event => form.imagem = event.files[0]" @uploader="event => form.imagem = event.files[0]"/>
                    </div>
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
    <br>
    <div style="width: 90%; margin: 0 auto">
        <div class="p-fluid grid">
            <DataTable :value="noticias" scrollable scrollHeight="300px" showGridlines style="width: 100%">
                <Column field="titulo" header="Título">
                    <template #body="slotProps">
                        <div v-html="slotProps.data.titulo"></div>
                    </template>
                </Column>
                <Column field="data" header="Data"  headerStyle="width: 10rem">
                    <template #body="slotProps">
                        {{ new Intl.DateTimeFormat('pt-BR', {timeZone: 'UTC'}).format(new Date(slotProps.data.data)) }}
                    </template>
                </Column>
                <Column field="ativa" class="text-center" header="Ativa" headerStyle="width: 10rem" >
                    <template #body="slotProps">
                        <i :class="{'pi pi-check text-green-500': slotProps.data.ativa, 'pi pi-times text-red-600': !slotProps.data.ativa }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column header="Ações" headerStyle="width: 10rem" >
                    <template #body="{ data }">
                        <i class="pi pi-eye text-green-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="visulaizar(data)"></i>
                        <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)"></i>
                        <i class="pi pi-trash text-red-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="excluir(data)"></i>
                    </template>
                </Column>
            </DataTable>
        </div>
    </div>
    <VisualizadorNoticias v-model:visible="exibeNoticia" v-if="exibeNoticia"  :noticia="noticiaExibir"></VisualizadorNoticias>
    <ModalLoading :isLoading="loading"/>
</template>

<style scoped>


i {
    cursor: pointer
}
</style>
