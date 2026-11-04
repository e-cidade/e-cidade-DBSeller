<template>
    <div class="container">
        <div class="card w-100">
            <Fieldset class="fieldSet ml-auto mr-auto mt-3 mb-3 p-3 shadow-4" legend="Alteração de Macrozonas">
                <div class="divPesquisa">
                    <a href="javascript:void(0)" @click="abrirConsultaMacrozona">Macrozona:</a>
                    <InputText type="text" @focusout="buscaMacrozona" v-model="j224_sequencial"
                        class="w-2 ml-2 border-black-alpha-60 border-1" id="j224_sequencial" />
                    <InputText :disabled="true" class="w-7 ml-2 border-black-alpha-60 border-1" id="j224_sigla"
                        v-model="j224_sigla" />
                </div>
                <div class="flex">
                    <Button type="button" id="pesquisar" label="Continuar" class="m-auto mt-3 shadow-4"
                        :disabled="continuarButton" @click="editarMacrozona = true; montaForm()" />
                </div>
            </Fieldset>
        </div>
        <ModalLoading :isLoading="isLoading"></ModalLoading>
        <DialogConsultaMacrozonas ref="dialogMacrozona" @selectRow="selecionaMacrozona" />

        <Dialog v-model:visible="editarMacrozona" :maximizable="false" :modal="true" :style="{ width: '1000px' }"
            position="top" header="Editar Macrozona" class="shadow-4">
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
                    <Button type="button" label="Salvar" @click="submitAlteracao()"></Button>
                    <Button type="button" label="Limpar" @click="limparForm()"></Button>
                </div>
            </Fieldset>
        </Dialog>
    </div>
</template>

<script setup>
import DialogConsultaMacrozonas from './components/DialogConsultaMacrozonas.vue';
import ModalLoading from '@modules/Components/ModalLoading.vue';
import { useToast } from 'primevue/usetoast';
import Fieldset from 'primevue/fieldset';
import { ref } from 'vue';


const localizacoes = ref([
    { name: 'Urbana', code: 'Urbana' },
    { name: 'Rural', code: 'Rural' },
    { name: 'Mista', code: 'Mista' }
]);

const editarMacrozona = ref(false);
const dialogMacrozona = ref(null);
const continuarButton = ref(true);
const selectedLocalizacao = ref();
const j224_sequencial = ref();
const isLoading = ref(false);
const buscaAnterior = ref();
const toast = useToast();
const j224_sigla = ref();
const descricao = ref();
const retorno = ref();
const codigo = ref();
const sigla = ref();


function throwToast(severity = "warn", summary = "Atenção", detail = "Atenção", life = 3000) {
    toast.add({ severity, summary, detail, life });
}

function abrirConsultaMacrozona() {
    dialogMacrozona.value?.toggleDialog();
}

function selecionaMacrozona(result) {
    j224_sequencial.value = result.j224_sequencial;
    j224_sigla.value = result.j224_sigla;
    if (result.j224_sequencial != null) {
        continuarButton.value = false;
        retorno.value = result;
    }
}

async function buscaMacrozona() {
    const dialogMacrozonaRef = dialogMacrozona.value;
    if (dialogMacrozonaRef) {
        if (j224_sequencial.value !== undefined && j224_sequencial.value.trim() !== "") {
            if (j224_sequencial.value === buscaAnterior.value) {
                return;
            }

            buscaAnterior.value = j224_sequencial.value;
            isLoading.value = true;
            let response;
            response = await dialogMacrozonaRef.pesquisaListaMacrozonas({
                page: 1,
                porPagina: 10,
                sequencial: j224_sequencial.value
            });
            isLoading.value = false;
            response = response.data.data.data;
            if (response.length > 0) {
                j224_sigla.value = response[0].j224_sigla;
                continuarButton.value = false;
                retorno.value = response[0];
            } else {
                j224_sigla.value = 'Macrozona nao encontrada!';
                throwToast('warn', 'Atencao', 'Macrozona nao encontrada!', 5000);
                continuarButton.value = true;
                limpaCampos();
            }
        } else {
            buscaAnterior.value = null;
            j224_sigla.value = '';
            continuarButton.value = true;
        }

    }
}

function limpaCampos() {
    buscaAnterior.value = null;
    j224_sequencial.value = '';
    j224_sigla.value = '';
    continuarButton.value = true;
    codigo.value = '';
    sigla.value = '';
    descricao.value = '';
    selectedLocalizacao.value = null;
}

function limparForm() {
    sigla.value = '';
    descricao.value = '';
    selectedLocalizacao.value = null;
}

function verificaCampos() {
    if (!sigla.value || !selectedLocalizacao.value) {
        throwToast('warn', 'Atenção', 'Preencha todos os campos');
        return 'erro';
    }
}

function montaForm() {
    const macrozona = retorno.value;
    codigo.value = macrozona.j224_sequencial;
    sigla.value = macrozona.j224_sigla;
    descricao.value = macrozona.j224_descricao;
    selectedLocalizacao.value = localizacoes.value.find(local => local.code === macrozona.j224_localizacao);
}

async function submitAlteracao() {
    if (verificaCampos()) {
        return false;
    }

    try {
        isLoading.value = true;
        const parametros = new FormData();
        parametros.append('sequencial', codigo.value);
        parametros.append('sigla', sigla.value);
        parametros.append('localizacao', selectedLocalizacao.value.code);
        parametros.append('descricao', descricao.value);
        const response = await window.axios.post(
            'v4/api/tributario/cadastro/update-macrozona',
            parametros,
            {
                headers: {
                    'Content-Type': 'multipart/form-data; charset=UTF-8'
                }
            }
        );
        isLoading.value = false;
        if(response.data.sucesso){
            editarMacrozona.value = false;
            limparForm();
            limpaCampos();
            alert('Macrozona Atualizada com sucesso! \nCódigo: ' + response.data.sucesso);
            throwToast('success', 'Sucesso', 'Macrozona Atualizada com sucesso');
        }else{
            editarMacrozona.value = false;
            limparForm();
            limpaCampos();
            throwToast('error', 'Erro', 'Erro ao Atualizar Macrozona', 5000);
        }
    } catch (error) {
        editarMacrozona.value = false;
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
    width: 750px;
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
</style>