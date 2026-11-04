<script setup>
import { ref, onMounted } from "vue";
import ConfirmPopup from "primevue/confirmpopup";

import Trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.min.css';
import 'trumbowyg/dist/plugins/colors/trumbowyg.colors.min';
import 'trumbowyg/dist/plugins/history/trumbowyg.history.min';

import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import ModalLoading from "../../../Components/ModalLoading.vue";
import Message from "primevue/message";

$.trumbowyg.svgPath = window.ECIDADE_PATH + 'public/trumbowyg/ui/icons.svg';
const toast = useToast();
const confirm = useConfirm();
const ajuda = ref("Selecione um tipo de mensagem.")
const routes = {
    getMensagens: `v4/api/educacao/matricula-online/configuracoes/mensagens-personalizadas`,
    salvar: `v4/api/educacao/matricula-online/configuracoes/mensagens-personalizadas/salvar`,
    excluir: `v4/api/educacao/matricula-online/configuracoes/mensagens-personalizadas/{codigo}/excluir`,
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
const mensagens = ref()
const form = ref({
    codigo: null,
    slctdTipo: {
        data: null,
        disabled: false,
        label: 'Tipo da Mensagem'
    },
    conteudo: {
        value: "",
        disabled: true
    },
    btnSalvar: {
        disabled: false,
        label: 'Salvar'
    }
})

const limpaCampos = () => {
    form.value.codigo = null
    form.value.slctdTipo.data = null
    form.value.conteudo.value = ""
}
const salvar = async () => {
    let parametros = {
        tipo: form.value.slctdTipo.data.codigo,
        conteudo: form.value.conteudo.value
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
        await bucarMensagens()
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
const bucarMensagens = async () => {
    try {
        loading.value = true
        mensagens.value = (await window.axios.get(routes.getMensagens)).data.data
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
const preencheMensagem = async () => {
    ajuda.value = form.value.slctdTipo.data.ajuda
    form.value.codigo = form.value.slctdTipo.data.id
    form.value.conteudo.value = form.value.slctdTipo.data.conteudo
}
onMounted(async () => {
    await bucarMensagens()
})
</script>
<template>
    <ConfirmPopup></ConfirmPopup>
    <section class="container">
        <Panel header="Mensagens Personalizadas">
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6 col-offset-3">
                    <span class="p-float-label">
                          <Dropdown id="tipoBase" :options="mensagens"
                                    optionLabel="tipo.name"
                                    v-model="form.slctdTipo.data"
                                    :disabled="form.slctdTipo.disabled"
                                    @change="preencheMensagem"
                          />
                         <label for="">{{ form.slctdTipo.label }}</label>
                    </span>
                </div>
            </div>
            <Message severity="info" :closable="false">
                <h3>{{ ajuda }}</h3>
            </Message>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-12">
                    <Fieldset
                        legend="Mensagem"
                        :toggleable="false"
                    >
                        <Textarea
                            v-if="form.slctdTipo.data != null && form.slctdTipo.data.tipo.value !== 11 && form.slctdTipo.data.tipo.value !== 2"
                            v-model="form.conteudo.value"
                            :disabled="form.slctdTipo.data === null">
                        >
                        </Textarea>

                        <trumbowyg
                            v-else
                            v-model="form.conteudo.value"
                            :config="editor.config"
                            name="content"
                            :disabled="form.slctdTipo.data === null">
                        </trumbowyg>
                    </Fieldset>
                </div>
            </div>

            <div class="p-fluid grid">
                <div class="field col-12 md:col-2 col-offset-5">
                    <Button class="p-button" :label="form.btnSalvar.label"
                            :disabled="form.conteudo.value === ''"
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
