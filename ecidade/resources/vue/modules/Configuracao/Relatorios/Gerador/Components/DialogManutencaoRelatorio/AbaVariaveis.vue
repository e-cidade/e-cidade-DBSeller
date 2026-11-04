<script setup>

import { computed, ref } from "vue";

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue'])

const variaveis = computed({
    get() { return props.modelValue },
    set(value) { emit('update:modelValue', value) },
});

const variavel = ref({
    nome: '',
    label: '',
    tipo: '',
    default: '',
    sql: ''
});
const erro = ref({
    nome: false,
    label: false,
    tipo: false,
    sql: false
});
/**
 * @todo BUSCAR INFORMAÇÕES DA API?
 */
const tipos = ref([
    {value: 'varchar', label: 'Texto Livre'},
    {value: 'int4', label: 'Número sem Decimais'},
    {value: 'float8', label: 'Número com Decimais'},
    {value: 'date', label: 'Data'},
    {value: 'bool', label: 'Lógico'},
    {value: 'select', label: 'Select'}
]);

function editar(nome) {
    variavel.value = { ...variaveis.value.find(variavel => variavel.nome === nome) };
}

function excluir(nome) {
    variaveis.value = variaveis.value.filter(variavel => variavel.nome !== nome);
}

function hasError() {
    let hasError = false
    erro.value = {
        nome: false,
        label: false,
        tipo: false,
        sql: false
    };

    if (!variavel.value.nome) {
        erro.value.nome = true;
        hasError = true;
    }
    if (!variavel.value.label) {
        erro.value.label = true;
        hasError = true;
    }
    if (!variavel.value.tipo) {
        erro.value.tipo = true;
        hasError = true;
    }

    if (variavel.value.tipo === 'select' && !variavel.value.sql) {
        erro.value.sql = true;
        hasError = true
    }

    return hasError;
}
function salvar() {
    if (hasError()) {
        return;
    }

    const index = variaveis.value.findIndex(_variavel => _variavel.nome === variavel.value.nome);
    if (index > -1) {
        variaveis.value[index] = variavel.value;
        limpar();
        return;
    }

    variaveis.value.push(variavel.value);
    limpar();
}

function limpar() {
    variavel.value = {
        nome: '',
        label: '',
        tipo: '',
        default: '',
        sql: ''
    };
}

/**
 * Garante que o nome começara com $
 * @param value
 */
function updateNome(value) {
    value = value.replaceAll(' ', '');
    if (value === '' || value.substring(0, 1) === '$') {
        variavel.value.nome = value;
        return;
    }

    variavel.value.nome = `$${value}`;
}

</script>

<template>
    <section class="flex flex-column gap-2">
        <section class="flex justify-content-center">
            <Panel header="Cadastrar" class="w-4">
                <div class="formgrid grid row-gap-2 mt-2">
                    <div class="field col-12 md:col-6">
                        <span class="p-float-label">
                            <InputText id="nome"
                                       type="text"
                                       v-model="variavel.nome"
                                       :class="['w-full', { 'p-invalid': erro.nome }]"
                                       @update:modelValue="updateNome"/>
                            <label for="nome">Nome</label>
                        </span>
                        <small v-if="erro.nome" class="p-error" id="nome-error">Informe este campo.</small>
                    </div>
                    <div class="field col-12 md:col-6">
                        <span class="p-float-label">
                            <InputText id="descricao" type="text" v-model="variavel.label" :class="['w-full', { 'p-invalid': erro.label }]"/>
                            <label for="descricao">Descrição</label>
                        </span>
                        <small v-if="erro.label" class="p-error" id="descricao-error">Informe este campo.</small>
                    </div>
                    <div class="field col-12 md:col-6">
                        <span class="p-float-label">
                            <Dropdown input-id="tipo"
                                      v-model="variavel.tipo"
                                      :options="tipos"
                                      :class="['w-full', { 'p-invalid': erro.tipo }]"
                                      optionValue="value"
                                      optionLabel="label"></Dropdown>
                            <label for="tipo">Tipo</label>
                        </span>
                        <small v-if="erro.tipo" class="p-error" id="tipo-error">Informe este campo.</small>
                    </div>
                    <div class="field col-12 md:col-6">
                        <span class="p-float-label">
                            <InputText id="default" type="text" v-model="variavel.default" class="w-full"/>
                            <label for="default">Valor Padrão</label>
                        </span>
                    </div>
                    <div class="field col-12">
                        <span class="p-float-label">
                            <InputText id="sql" v-model="variavel.sql" :class="['w-full', { 'p-invalid': erro.sql }]" type="text"/>
                            <label for="sql">SQL</label>
                        </span>
                        <small v-if="erro.sql" class="p-error" id="tipo-error">Informe este campo.</small>
                    </div>
                </div>
            </Panel>
        </section>
        <section class="flex justify-content-center gap-1">
            <Button icon="pi pi-save" label="Salvar" @click="salvar"></Button>
            <Button icon="pi pi-erase" label="Limpar" @click="limpar"></Button>
        </section>
        <section class="flex justify-content-center">
            <div class="w-full xl:w-5">
                <DataTable :value="variaveis">
                    <Column field="nome" style="width: 50px" header="Nome"></Column>
                    <Column field="label" header="Label"></Column>
                    <Column field="options" header="Opções">
                        <template #body="slotProps">
                            <Button class="mx-1" icon="pi pi-pencil" title="Editar" @click="editar(slotProps.data.nome)"
                                    rounded raised></Button>
                            <Button class="mx-1" icon="pi pi-trash" title="Excluir" severity="danger"
                                    @click="excluir(slotProps.data.nome)" rounded raised></Button>
                        </template>
                    </Column>

                    <template #empty>
                        <div class="flex justify-content-center">
                            <p>Nenhuma variável cadastrada.</p>
                        </div>
                    </template>
                </DataTable>
            </div>
        </section>
    </section>
</template>

<style scoped>

</style>
