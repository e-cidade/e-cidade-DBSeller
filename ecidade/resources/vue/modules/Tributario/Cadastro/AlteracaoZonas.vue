<template>
    <div class="container">
        <div class="card w-100">
            <Fieldset class="fieldSet ml-auto mr-auto mt-3 mb-3 p-3 shadow-4" legend="Alteração de Zonas">
                <div class="divPesquisa">
                    <a href="javascript:void(0)" @click="abrirConsultaZona">Zona:</a>
                    <InputText type="text" @focusout="buscaZona" v-model="j225_sequencial"
                        class="w-2 ml-2 border-black-alpha-60 border-1" id="j225_sequencial" />
                    <InputText :disabled="true" class="w-7 ml-2 border-black-alpha-60 border-1" id="j225_sigla"
                        v-model="j225_sigla" />
                </div>
                <div class="flex">
                    <Button type="button" id="pesquisar" label="Continuar" class="m-auto mt-3 shadow-4"
                        :disabled="continuarButton" @click="editarZona = true; montaForm();" />
                </div>
            </Fieldset>
        </div>

        <Dialog v-model:visible="editarZona" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
            position="top" header="Editar zona" class="shadow-4">
            <Fieldset class="fieldSet ml-auto mr-auto mt-3 mb-3 p-3 shadow-4" legend="Informações">
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
                            <InputText type="text" v-model="j224_sequencial" @keyup="verificaCaracteres"
                                @focusout="buscaMacrozona" />
                            <InputText class="inputSiglaMacro styledisabled" type="text" v-model="j224_sigla" :disabled="true" />
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
        </Dialog>

        <ModalLoading :isLoading="isLoading"></ModalLoading>
        <DialogConsultaZonas ref="dialogZona" @selectRow="selecionaZona" />
        <DialogConsultaMacrozonas ref="dialogMacrozona" @selectRow="selecionaMacrozona" />
    </div>
</template>

<script setup>
import DialogConsultaMacrozonas from './components/DialogConsultaMacrozonas.vue';
import DialogConsultaZonas from './components/DialogConsultaZonas.vue';
import ModalLoading from '@modules/Components/ModalLoading.vue';
import { useToast } from 'primevue/usetoast';
import Fieldset from 'primevue/fieldset';
import { ref } from 'vue';

const continuarButton = ref(true);
const dialogMacrozona = ref(null);
const buscaAnteriorMacro = ref();
const j224_sequencial = ref();
const editarZona = ref(false);
const j225_sequencial = ref();
const dialogZona = ref(null);
const isLoading = ref(false);
const buscaAnterior = ref();
const toast = useToast();
const j225_sigla = ref();
const j224_sigla = ref();
const descricao = ref();
const retorno = ref();
const codigo = ref();
const sigla = ref();


function throwToast(severity = "warn", summary = "Atenção", detail = "Atenção", life = 3000) {
    toast.add({ severity, summary, detail, life });
}

function abrirConsultaZona() {
    dialogZona.value?.toggleDialog();
}

function abrirConsultaMacrozona() {
    dialogMacrozona.value?.toggleDialog();
}

function selecionaZona(result) {
    j225_sequencial.value = result.j225_sequencial;
    j225_sigla.value = result.j225_sigla;
    if (result.j225_sequencial != null) {
        continuarButton.value = false;
        retorno.value = result;
    }
}

function selecionaMacrozona(result) {
    j224_sequencial.value = result.j224_sequencial;
    j224_sigla.value = result.j224_sigla;
}


async function buscaZona() {
    const dialogZonaRef = dialogZona.value;
    if (dialogZonaRef) {
        if (j225_sequencial.value !== undefined && j225_sequencial.value.trim() !== "") {
            if (j225_sequencial.value === buscaAnterior.value) {
                return;
            }

            buscaAnterior.value = j225_sequencial.value;
            isLoading.value = true;
            let response;
            response = await dialogZonaRef.pesquisaListaZonas({
                page: 1,
                porPagina: 10,
                sequencial: j225_sequencial.value
            });
            isLoading.value = false;
            response = response.data.data.data;
            if (response.length > 0) {
                j225_sigla.value = response[0].j225_sigla;
                continuarButton.value = false;
                retorno.value = response[0];
            } else {
                j225_sigla.value = 'Zona nao encontrada!';
                throwToast('warn', 'Atencao', 'Zona nao encontrada!', 5000);
                continuarButton.value = true;
                limpaCampos();
            }
        } else {
            buscaAnterior.value = null;
            j225_sigla.value = '';
            continuarButton.value = true;
        }

    }
}

function montaForm(){
    const zona = retorno.value;
    codigo.value = zona.j225_sequencial;
    sigla.value = zona.j225_sigla;
    j224_sequencial.value = zona.j224_sequencial;
    descricao.value = zona.j225_descricao;
    j224_sigla.value = zona.j224_sigla;
}

async function buscaMacrozona() {

    if (j224_sequencial.value === buscaAnteriorMacro.value) {
        return;
    }
    buscaAnteriorMacro.value = j224_sequencial.value;

    if (j224_sequencial.value !== '' && j224_sequencial.value !== null) {
        try {
            isLoading.value = true;
            const parametros = new FormData();
            parametros.append('sequencial', j224_sequencial.value);
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
            if (response.data === 'semResultado') {
                j224_sigla.value = 'Macrozona não encontrada!';
                j224_sequencial.value = '';
                throwToast('warn', 'Atenção', 'Macrozona nao encontrada!', 5000);
            } else if (response.data.j224_sigla) {
                j224_sigla.value = response.data.j224_sigla;
            }
        } catch (error) {
            isLoading.value = false;
            j224_sequencial.value = '';
            j224_sigla.value = 'Erro ao consultar MacroZona';
            throwToast('error', 'Erro', 'Erro ao consultar MacroZona', 5000);
        }
    } else {
        j224_sigla.value = '';
        j224_sequencial.value = '';
    }
}


function limpaCampos() {
    buscaAnteriorMacro.value = null;
    buscaAnterior.value = null;
    j225_sequencial.value = '';
    j225_sigla.value = '';
    continuarButton.value = true;
}

function limparForm() {
    buscaAnteriorMacro.value = null;
    sigla.value = '';
    descricao.value = '';
    j224_sequencial.value = '';
    j224_sigla.value = '';
}

function verificaCampos() {
    if (!sigla.value || !j224_sequencial.value) {
        throwToast('warn', 'Atenção', 'Preencha todos os campos');
        return 'erro';
    }
}

async function submitForm() {
    if (verificaCampos()) {
        return false;
    }

    try {
        isLoading.value = true;
        const parametros = new FormData();
        parametros.append('sequencial', codigo.value);
        parametros.append('macrozona', j224_sequencial.value);
        parametros.append('sigla', sigla.value);
        parametros.append('descricao', descricao.value);
        const response = await window.axios.post(
            'v4/api/tributario/cadastro/update-zona',
            parametros,
            {
                headers: {
                    'Content-Type': 'multipart/form-data; charset=UTF-8'
                }
            }
        );
        isLoading.value = false;
        if(response.data.sucesso){
            editarZona.value = false;
            limparForm();
            limpaCampos();
            alert('Zona Atualizada com sucesso! \nCódigo: ' + response.data.sucesso);
            throwToast('success', 'Sucesso', 'Zona Atualizada com sucesso');
        }else{
            editarZona.value = false;
            limparForm();
            limpaCampos();
            throwToast('error', 'Erro', 'Erro ao Atualizar Zona', 5000);
        }
    } catch (error) {
        editarZona.value = false;
        isLoading.value = false;
        limparForm();
        limpaCampos();
        throwToast('error', 'Erro', 'Erro ao Atualizar Macrozona', 5000);
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
    width: 900px;
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

.styledisabled {
    border: solid black 1px;
}

.divPesquisa {
    display: flex;
    align-items: center;
    justify-content: center;
}

.inputSiglaMacro {
    width: 400px;
    margin-left: 3px;
}
</style>