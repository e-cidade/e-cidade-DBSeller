<script setup>

import { nextTick, ref, shallowRef } from "vue";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import CodeEditor from "simple-code-editor";
import ModalLoading from "../../../../../Components/ModalLoading.vue";
import InputDinamico from "../InputDinamico.vue";

const props = defineProps(['modelValue', 'callbackSalvar']);
const emit = defineEmits(['callbackSalvar']);

const sql = shallowRef(props.modelValue);

const toast = useToast();
const confirm = useConfirm();

const dialogVariaveis = ref({
    visible: false,
    execute: false,
    variaveis: [],
    tipos: [
        /**
         * @todo BUSCAR INFORMAÇÕES DA API?
         */
        {value: 'varchar', label: 'Texto Livre'},
        {value: 'int4', label: 'Número sem Decimais'},
        {value: 'float8', label: 'Número com Decimais'},
        {value: 'date', label: 'Data'},
        {value: 'bool', label: 'Lógico'},
        {value: 'select', label: 'Select'}
    ]
});
const dialogDadosRetornados = ref({
    visible: false,
    dados: [],
    colunas: []
});

const isLoading = ref(false);

function consultar() {
    sql.value = props.modelValue;
}
async function preparaSql() {
    dialogVariaveis.value.variaveis = [];
    const nomesVariaveis = sql.value.trim().replace(/(\r\n|\n|\r)/gm, " ").match(/(\$\w+)(?!.*\1)/g);
    if (nomesVariaveis) {
        for (const nomeVariavel of nomesVariaveis) {
            dialogVariaveis.value.variaveis.push({
                nome: nomeVariavel,
                valor: '',
                tipo: 'varchar'
            });
        }
    }


    if (dialogVariaveis.value.variaveis.length > 0) {
        dialogVariaveis.value.visible = true;
        await nextTick();

        const wait = resolve => {
            if (dialogVariaveis.value.visible === false) {
                resolve(false);
                return;
            }
            if (dialogVariaveis.value.execute === false) {
                setTimeout(() => wait(resolve), 50);
                return;
            }
            dialogVariaveis.value.visible = false;
            dialogVariaveis.value.execute = false;
            resolve(true);
        }

        if (!await new Promise(wait)) {
            // Usuário fechou o dialog
            return null;
        }
    }

    const bindings = [];
    let bindedSql = sql.value;
    for (const variavel of dialogVariaveis.value.variaveis) {
        if (['varchar', 'select', 'date'].includes(variavel.tipo)) {
            variavel.valor = `'${variavel.valor}'`;
        }
        /**
         * @todo fazer bind por ?. De forma com que o número de ? fique igual o numero de itens no array.
         */
        bindedSql = bindedSql.replaceAll(variavel.nome, variavel.valor);
    }

    return { sql: bindedSql, bindings: bindings };
}

async function executar() {
    try {
        const bind = await preparaSql();
        if (bind === null || bind.sql === '') {
            return;
        }

        isLoading.value = true;

        const response = await axios.post('v4/api/configuracao/gerador/executar-sql', bind);

        dialogDadosRetornados.value.dados = response.data.data;

        dialogDadosRetornados.value.colunas = [];
        const [ dado ] = dialogDadosRetornados.value.dados;
        for (const property in dado) {
            dialogDadosRetornados.value.colunas.push({
                campo: property,
                descricao: property.charAt(0).toUpperCase() + property.slice(1).replaceAll('_', ' ')
            });
        }

        dialogDadosRetornados.value.visible = true;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao executar SQL',
            detail: e.response ? e.response.data.message : e.message
        });
    }

    isLoading.value = false;
}

async function confirmacaoUsuario() {
    return new Promise(resolve => {
        confirm.require({
            group: 'dialogManutencaoRelatorio',
            header: 'Deseja prosseguir?',
            message: 'Todos os dados configurados serão perdidos!',
            icon: 'pi pi-exclamation-triangle',
            acceptClass: 'p-button-danger',
            accept: () => {
                resolve(true);
            },
            reject: () => {
                resolve(false);
            }
        });
    });
}

async function salvar() {
    if (props.modelValue !== '' && !await confirmacaoUsuario()) {
        return;
    }

    emit('callbackSalvar', sql.value);
}

function apagar() {
    sql.value = '';
}

</script>

<template>
    <section class="flex flex-column gap-2">
        <section class="flex justify-content-center">
            <section class="w-full xl:w-8">
                <CodeEditor v-model="sql"
                            :languages="[['pgsql', 'SQL']]"
                            :line-nums="true"
                            :tab-spaces="4"
                            width="100%"
                            height="600px"
                            theme="github"></CodeEditor>
            </section>
        </section>
        <section class="flex justify-content-center gap-1">
            <Button icon="pi pi-search" label="Consultar" @click="consultar"></Button>
            <Button icon="pi pi-play" label="Executar" @click="executar"></Button>
            <Button icon="pi pi-save" label="Salvar" @click="salvar"></Button>
            <Button icon="pi pi-trash" label="Apagar" severity="danger" @click="apagar"></Button>
        </section>
    </section>

    <Dialog header="Variáveis" v-model:visible="dialogVariaveis.visible" modal>
        <section class="flex flex-column mt-4 gap-2">
            <div class="formgrid grid row-gap-2">
                <template v-for="variavel of dialogVariaveis.variaveis">
                    <div class="field col-6">
                        <InputDinamico :modelValue="variavel" @update:modelValue="value => variavel = value"></InputDinamico>
                    </div>
                    <div class="field col-6">
                        <span class="p-float-label">
                            <Dropdown input-id="tipoVariavel"
                                      v-model="variavel.tipo"
                                      :options="dialogVariaveis.tipos"
                                      optionValue="value"
                                      optionLabel="label"
                                      class="w-full"></Dropdown>
                            <label for="tipoVariavel">Tipo</label>
                        </span>
                    </div>
                </template>
            </div>
            <div class="flex justify-content-center">
                <Button icon="pi pi-play" label="Executar" @click="dialogVariaveis.execute = true"></Button>
            </div>
        </section>
    </Dialog>

    <Dialog header="Dados retornados" v-model:visible="dialogDadosRetornados.visible" :pt="{ content: { style: 'background: #e1dede;'}}" modal>
        <section class="mt-2">
            <DataTable :value="dialogDadosRetornados.dados" showGridlines scrollable scrollHeight="400px" class="w-full">
                <template #empty>
                    <div class="flex justify-content-center">
                        <p>Nenhum registro retornado.</p>
                    </div>
                </template>
                <Column v-for="coluna of dialogDadosRetornados.colunas" :field="coluna.campo" :header="coluna.descricao"></Column>
                <template v-if="dialogDadosRetornados.dados.length" #footer> Total de registros: {{ dialogDadosRetornados.dados.length }} </template>
            </DataTable>
        </section>
    </Dialog>

    <ModalLoading :is-loading="isLoading"></ModalLoading>
</template>

<style scoped>

</style>
