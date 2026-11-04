<template>
    <div class="container">
        <div class="card w-100">
            <Fieldset class="fieldSet ml-auto mr-auto mt-3 mb-3 p-3 shadow-4" legend="Cadastro de Macrozonas">
                <table>
                    <tr>
                        <td class="titulo"><span>Código:</span></td>
                        <td>
                            <InputText class="styledisabled" type="text" v-model="codigo" :disabled="true" />
                        </td>
                    </tr>
                    <tr>
                        <td class="titulo"><span>Sigla*:</span></td>
                        <td>
                            <InputText type="text" v-model="sigla" :maxlength="10" />
                        </td>
                        <td class="titulo"><span>Localização*:</span></td>
                        <td>
                            <Dropdown v-model="selectedLocalizacao" :options="localizacoes" optionLabel="name"
                                placeholder="Selecione" checkmark :highlightOnSelect="false"
                                class="w-full md:w-14rem" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1" class="titulo"><span>Descrição:</span></td>
                        <td colspan="3">
                            <InputText class="descricao" type="text" v-model="descricao" :maxlength="60" />
                        </td>
                    </tr>
                </table>
                <div class="btn-group">
                    <Button type="button" label="Salvar" @click="submitForm()"></Button>
                    <Button type="button" label="Limpar" @click="limparForm()"></Button>
                </div>
            </Fieldset>
        </div>
        <ModalLoading :isLoading="isLoading"></ModalLoading>
    </div>
</template>

<script setup>
import ModalLoading from '@modules/Components/ModalLoading.vue';
import { useToast } from 'primevue/usetoast';
import Fieldset from 'primevue/fieldset';
import { param } from 'jquery';
import { ref } from 'vue';

const localizacoes = ref([
    { name: 'Urbana', code: 'Urbana' },
    { name: 'Rural', code: 'Rural' },
    { name: 'Mista', code: 'Mista' }
]);
const selectedLocalizacao = ref();
const isLoading = ref(false);
const toast = useToast();
const descricao = ref();
const codigo = ref();
const sigla = ref();

function throwToast(severity = "warn", summary = "Atenção", detail = "Atenção", life = 3000) {
    toast.add({ severity, summary, detail, life });
}


function verificaCampos() {
    if (!sigla.value || !selectedLocalizacao.value) {
        throwToast('warn', 'Atenção', 'Preencha todos os campos');
        return 'erro';
    }
}


async function getNextId() {
    try {
        isLoading.value = true;
        const response = await window.axios.post('v4/api/tributario/cadastro/next-id-macrozona');
        isLoading.value = false;
        codigo.value = response.data;
    } catch (error) {
        isLoading.value = false;
        codigo.value = '';
    }
}
getNextId();

function limparForm() {
    sigla.value = '';
    selectedLocalizacao.value = '';
    descricao.value = '';
    getNextId();
}

async function submitForm() {
    if (verificaCampos()) {
        return false;
    }

    try {
        isLoading.value = true;
        const parametros = new FormData();
        parametros.append('sigla', sigla.value);
        parametros.append('localizacao', selectedLocalizacao.value.code);
        parametros.append('descricao', descricao.value);
        const response = await window.axios.post(
            'v4/api/tributario/cadastro/save-macrozona',
            parametros,
            {
                headers: {
                    'Content-Type': 'multipart/form-data; charset=UTF-8'
                }
            }
        );
        isLoading.value = false;
        if(response.data.sucesso){
            limparForm();
            alert('Macrozona cadastrada com sucesso! \nCódigo: ' + response.data.sucesso);
            throwToast('success', 'Sucesso', 'Macrozona cadastrada com sucesso');
        }else{
            limparForm();
            throwToast('error', 'Erro', 'Erro ao cadastrar Macrozona');
        }
    } catch (error) {
        isLoading.value = false;
        limparForm();
        throwToast('error', 'Erro', 'Erro ao cadastrar Macrozona');
    }

}

</script>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    width: 100%;
}

.fieldSet {
    width: 450px;
}

.titulo {
    width: 190px;
}

.btn-group {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.btn-group button {
    margin: 0 10px;
}

.descricao {
    width: 100%;
}

.styledisabled{
    border: solid black 1px;
}
</style>