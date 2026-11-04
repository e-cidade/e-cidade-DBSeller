<script setup>
import {onMounted, ref} from 'vue';
import ModalLoading from "../../../Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";

const toast = useToast();
const loading = ref(false);
const props = defineProps({
    instituicao: {Type: Number, required: true}
});
const rotas = {
    salvarUsuario: 'v4/api/patrimonial/licitacoes/licitacon/obras/salvar-usuario/',
    buscarUsuario: 'v4/api/patrimonial/licitacoes/licitacon/obras/buscar-usuario/',
}
const form = ref({
    idExterno: {
        label: 'ID Externo',
        disabled: false,
        placeholder: 'Digite o ID Externo',
        value: ref()
    },
    chave: {
        label: 'Chave',
        disabled: false,
        placeholder: 'Digite a chave',
        value: ref()
    },
    botaoSalvar: {
        label: 'Salvar',
        disabled: false,
        required: false,
    }
});

onMounted(async () => {
    await buscarUsuario();
});

function bloquearCampos() {
    const camposFormulario = Object.values(form.value);
    camposFormulario.forEach(el => el.disabled = true);
}

function campoEstaVazio(valor) {
    return valor === '' || valor === undefined || valor === false || valor === null;
}

function validarCamposObrigatorios() {
    const camposFormulario = Object.values(form.value);
    let existeCampoVazio = false;

    for (let campo of camposFormulario) {
        if (campo.required !== false && existeCampoVazio === false) {
            if (campoEstaVazio(campo.value)) {
                toast.add({
                    summary: 'Erro: ',
                    detail: `O campo ${campo.label} é obrigatório.`,
                    severity: "error",
                    life: 4000
                });

                existeCampoVazio = true;
            }
        }
    }

    return existeCampoVazio !== true;
}

async function buscarUsuario() {
    loading.value = true;

    try {
        const response = await window.axios.get(`${rotas.buscarUsuario}${props.instituicao}`);
        const dados = response.data.data;

        if (dados) {
            form.value.idExterno.value = dados.l50_id_externo;
            form.value.chave.value = dados.l50_chave;
        }
    } catch (e) {
        bloquearCampos();
        toast.add({
            summary: 'Erro:',
            detail: 'Falha ao buscar usuário desta instituição.',
            severity: 'error',
            life: 6000
        });
    }

    loading.value = false;
}

async function salvarUsuario() {
    loading.value = true;

    if (!validarCamposObrigatorios()) {
        return;
    }

    try {
        const config = {};
        config.idExterno = form.value.idExterno.value;
        config.chave = form.value.chave.value;

        await window.axios.get(`${rotas.salvarUsuario}${props.instituicao}`, {params: config});
        toast.add({
            summary: 'Sucesso:',
            detail: 'O usuário foi salvo.',
            severity: 'success',
            life: 6000
        });

    } catch (e) {
        bloquearCampos();
        toast.add({
            summary: 'Erro:',
            detail: 'Falha ao salvar o usuário.',
            severity: 'error',
            life: 6000
        });
    }

    loading.value = false;
}
</script>

<template>
    <ModalLoading :is-loading="loading"></ModalLoading>

    <section>
        <Panel class="container" header="Parâmetros">
            <br/>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                                <InputText
                                    id="idExterno"
                                    v-model="form.idExterno.value"
                                    :placeholder="form.idExterno.placeholder"
                                    :disabled="form.idExterno.disabled"
                                />
                            <label for="idExterno" class="required">{{ form.idExterno.label }}</label>
                        </span>
                    </div>
                </div>

                <div class="field col-12 md:col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <InputText
                                id="chave"
                                v-model="form.chave.value"
                                :placeholder="form.chave.placeholder"
                                :disabled="form.chave.disabled"
                            />
                            <label class="required">{{ form.chave.label }}</label>
                        </span>
                    </div>
                </div>
            </div>
        </Panel>

        <div class="container">
            <div class="flex justify-content-end">
                <Button
                    class="p-button" icon="pi pi-save"
                    @click="salvarUsuario"
                    :label="form.botaoSalvar.label"
                    :disabled="form.botaoSalvar.disabled"
                />
            </div>
        </div>
    </section>
</template>
