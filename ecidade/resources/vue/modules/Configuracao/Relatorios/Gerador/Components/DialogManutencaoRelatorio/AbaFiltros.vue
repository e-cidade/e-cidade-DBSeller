<script setup>

import { computed, onMounted, ref } from "vue";

const props = defineProps({
    modelValue: { required: true, type: Array },
    campos: { required: true, type: Array },
    variaveis: { required: true, type: Array }
});
const emit = defineEmits(['update:modelValue']);

const campos = ref([]);
const variaveis = ref([]);

const filtros = computed({
    get() { return props.modelValue },
    set(value) { emit('update:modelValue', value) }
});

const filtro = ref({
    operador: '',
    campo: '',
    condicao: '',
    valor: ''
});
/**
 * @type {Ref<array>}
 */
const operadores = ref([
    { value: '=', label: 'Igual' },
    { value: '!=', label: 'Diferente' },
    { value: '>', label: 'Maior' },
    { value: '<', label: 'Menor' },
    { value: '>=', label: 'Maior ou igual' },
    { value: '<=', label: 'Menor ou igual' },
    { value: 'in', label: 'Contém' },
    { value: 'is null', label: 'Nulo' },
    { value: 'is not null', label: 'Preenchido' }
]);
const condicoes = ref([
    { value: 'and', label: 'e' },
    { value: 'or', label: 'ou'}
]);

function autoCompleteValor({ query }) {
    filtro.value.valor = query;
    variaveis.value = [ ...props.variaveis.map(variavel => variavel.nome) ]
    if (query.trim() === '') {
        return;
    }

    variaveis.value.unshift(query);
    variaveis.value = variaveis.value.filter(variavel => variavel.toString().startsWith(query));
}

function salvar() {
    const index = filtros.value.findIndex(_filtro => _filtro.codigo === filtro.value.codigo);
    if (index > -1) {
        filtros.value[index] = filtro.value;
        limpar();
        return;
    }

    filtro.value.codigo = uuid();
    filtros.value.push(filtro.value);
    limpar();
}

function limpar() {
    filtro.value = {
        codigo: '',
        operador: '',
        campo: '',
        condicao: '',
        valor: ''
    };
}

function editar(codigo) {
    filtro.value = { ...filtros.value.find(_filtro => _filtro.codigo === codigo) };
}

function excluir(codigo) {
    filtros.value = filtros.value.filter(_filtro => _filtro.codigo !== codigo);
}

onMounted(() => {
    for (const campo of props.campos.flat()) {
        campos.value.push({
            value: campo.nome,
            label: campo.alias
        });
    }

    for (const variavel of props.variaveis) {
        variaveis.value.push(variavel.nome);
    }
});

</script>

<template>
    <section class="flex flex-column gap-2">
        <section class="flex justify-content-center">
            <Panel header="Configurar" class="w-4">
                <div class="formgrid grid row-gap-2 mt-2">
                    <div class="field col-12 md:col-2">
                        <span class="p-float-label">
                            <Dropdown input-id="condicao"
                                      v-model="filtro.condicao"
                                      :options="condicoes"
                                      optionValue="value"
                                      optionLabel="label"
                                      class="w-full"></Dropdown>
                            <label for="condicao">Condição</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown input-id="campo"
                                      v-model="filtro.campo"
                                      :options="campos"
                                      optionValue="value"
                                      optionLabel="label"
                                      class="w-full"></Dropdown>
                            <label for="campo">Campo</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-3">
                        <span class="p-float-label">
                            <Dropdown input-id="operador"
                                      v-model="filtro.operador"
                                      :options="operadores"
                                      optionValue="value"
                                      optionLabel="label"
                                      class="w-full"></Dropdown>
                            <label for="operador">Operador</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-3">
                        <span class="p-float-label">
                            <AutoComplete inputId="valor"
                                          v-model="filtro.valor"
                                          :suggestions="variaveis"
                                          @complete="autoCompleteValor"
                                          inputClass="w-full"></AutoComplete>
                            <label for="valor">Valor</label>
                        </span>
                    </div>
                </div>
            </Panel>
        </section>
        <section class="flex justify-content-center gap-1">
            <Button icon="pi pi-save" label="Salvar" @click="salvar"></Button>
            <Button icon="pi pi-erase" label="Limpar" @click="limpar"></Button>
        </section>
        <section class="flex justify-content-center">
            <DataTable :value="filtros">
                <Column field="campo" style="width: 150px" header="Campo"></Column>
                <Column field="operador" style="width: 50px" header="Operador"></Column>
                <Column field="valor" style="width: 50px" header="Valor"></Column>
                <Column field="condicao" style="width: 50px" header="Condição"></Column>
                <Column field="options" style="width: 110px" header="Opções">
                    <template #body="slotProps">
                        <Button class="mx-1" icon="pi pi-pencil" title="Editar" @click="editar(slotProps.data.codigo)" rounded raised></Button>
                        <Button class="mx-1" icon="pi pi-trash" title="Excluir" severity="danger" @click="excluir(slotProps.data.codigo)" rounded raised></Button>
                    </template>
                </Column>

                <template #empty>
                    <div class="flex justify-content-center">
                        <p>Nenhum filtro cadastrado.</p>
                    </div>
                </template>
            </DataTable>
        </section>
    </section>

</template>

<style scoped>

</style>
