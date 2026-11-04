<template>
    <div class="container">
        <div class="card w-100">
            <Fieldset class="fieldSet ml-auto mr-auto mt-3 mb-3 p-3 shadow-4" legend="Cadastro de Zonas">
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
                    </tr>
                    <tr>
                        <td class="titulo" colspan="1">
                            <span>
                                <a href="javascript:void(0)" @click="abrirConsultaMacrozona">Macrozona*:</a>
                            </span>
                        </td>
                        <td colspan="3">
                            <InputText class="codmacrozona" type="text" v-model="macrozonacod"
                                @keyup="verificaCaracteres" @focusout="buscaMacrozona" />
                            <InputText class="macrozonaname styledisabled" type="text" v-model="macrozonaname"
                                :disabled="true" />
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
        <DialogConsultaMacrozonas ref="dialogMacrozona" @selectRow="selecionaMacrozona" />
    </div>
</template>

<script setup>
import DialogConsultaMacrozonas from './components/DialogConsultaMacrozonas.vue';
import ModalLoading from '@modules/Components/ModalLoading.vue';
import { useToast } from 'primevue/usetoast';
import Fieldset from 'primevue/fieldset';
import { param } from 'jquery';
import { ref } from 'vue';

const dialogMacrozona = ref(null)
const isLoading = ref(false);
const macrozonaname = ref();
const macrozonacod = ref();
const reqAnterior = ref();
const toast = useToast();
const descricao = ref();
const codigo = ref();
const sigla = ref();

function throwToast(severity = "warn", summary = "Atenção", detail = "Atenção", life = 3000) {
    toast.add({ severity, summary, detail, life });
}


function verificaCampos() {
    if (!sigla.value || !macrozonacod.value) {
        throwToast('warn', 'Atenção', 'Preencha todos os campos');
        return 'erro';
    }
}


async function getNextId() {
    try {
        isLoading.value = true;
        const response = await window.axios.post('v4/api/tributario/cadastro/next-id-zona');
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
    macrozonacod.value = '';
    macrozonaname.value = '';
    descricao.value = '';
    getNextId();
}

function abrirConsultaMacrozona() {
    dialogMacrozona.value?.toggleDialog();
}

function selecionaMacrozona(result) {
    macrozonacod.value = result.j224_sequencial;
    macrozonaname.value = result.j224_sigla;
}

function verificaCaracteres() {
    if (typeof macrozonacod.value === 'string' && macrozonacod.value.trim() !== '') {
        let semLetras = macrozonacod.value.replace(/[^0-9]/g, '');
        if (semLetras !== macrozonacod.value) {
            macrozonacod.value = semLetras;
            throwToast("warn", "Atenção", "Apenas números são permitidos.", 3000);
        }
    }
}

async function buscaMacrozona() {

    if (macrozonacod.value === reqAnterior.value) {
        return;
    }
    reqAnterior.value = macrozonacod.value;

    if (macrozonacod.value !== '' && macrozonacod.value !== null) {
        try {
            isLoading.value = true;
            const parametros = new FormData();
            parametros.append('sequencial', macrozonacod.value);
            const response = await window.axios.post(
                'v4/api/tributario/cadastro/get-macrozonas-by-id',
                parametros,
                {
                    headers: {
                        'Content-Type': 'multipart/form-data; charset=UTF-8'
                    }
                }
            );
            isLoading.value = false;
            if(response.data === 'semResultado'){
                macrozonaname.value = 'Macrozona não encontrada!';
                macrozonacod.value = '';
                throwToast('warn', 'Atenção', 'Macrozona nao encontrada!', 5000);
            }else if(response.data.j224_sigla){
                macrozonaname.value = response.data.j224_sigla;
            }
        } catch (error) {
            isLoading.value = false;
            macrozonacod.value = '';
            macrozonaname.value = 'Erro ao consultar MacroZona';
            throwToast('error', 'Erro', 'Erro ao consultar MacroZona', 5000);
        }
    } else {
        macrozonaname.value = '';
        macrozonacod.value = '';
    }
}


async function submitForm() {
    if (verificaCampos()) {
        return false;
    }

    try {
        isLoading.value = true;
        const parametros = new FormData();
        parametros.append('sigla', sigla.value);
        parametros.append('sequencial', macrozonacod.value);
        parametros.append('descricao', descricao.value);
        const response = await window.axios.post(
            'v4/api/tributario/cadastro/save-zona',
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
            throwToast('success', 'Sucesso', 'Zona cadastrada com sucesso');
            alert('Zona cadastrada com sucesso! \nCódigo: ' + response.data.sucesso);
        }else{
            limparForm();
            throwToast('error', 'Erro', 'Erro ao cadastrar Zona');
        }
    } catch (error) {
        isLoading.value = false;
        limparForm();
        throwToast('error', 'Erro', 'Erro ao cadastrar Zona');
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
    width: 680px;
}

.titulo {
    width: 100px;
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

.codmacrozona {
    width: 67px;
    margin-right: 3px;
}

.macrozonaname {
    width: 440px;
}

.styledisabled {
    border: solid black 1px;
}
</style>