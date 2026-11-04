<script setup>

import { ref, watch } from "vue";
import DialogMatricula from "./DialogMatricula.vue"
import DialogCargo from "./DialogCargo.vue"
import DialogLota from "./DialogLota.vue"
import DialogLocalDeTrabalho from "./DialogLocalDeTrabalho.vue"

const filtros = ref([
    { code: 1, name: 'Geral' },
    { code: 2, name: 'Lotação' },
    { code: 3, name: 'Matrícula' },
    { code: 4, name: 'Local de trabalho' },
    { code: 5, name: 'Cargo' },
]);

const funcMatricula = ref();
const funcLotacao = ref();
const funcLocalTrabalho = ref();
const funcCargo = ref();
const callback = ref();

const tipoFiltros = ref([
    { code: 1, name: 'Geral' },
    { code: 2, name: 'Intervalo' },
    { code: 3, name: 'Selecionados' }
]);

const data = ref(
    {
        tipoResumo: null,
        tipoFiltro: null,
        adicionar: {
            codigo : null,
            descricao : null
        },
        inicio: null,
        fim: null,
        registros: []
    }
);

function openDialog() {
    switch (data.value.tipoResumo?.code) {
        case 2:
            funcLotacao.value.openModal();
            callback.value = setRegistroLotacao;
            break;
        case 3:
            funcMatricula.value.openModal();
            callback.value = setRegistroMatricula;
            break;
        case 4:
            funcLocalTrabalho.value.openDialog();
            callback.value = setRegistroLocal;
            break;
        case 5:
            funcCargo.value.openDialog();
            callback.value = setRegistroCargo;
            break;

        default:
            break;
    }
}

function openDialogIntervalo (callbackIntervalo) {
    callback.value = callbackIntervalo;
    switch (data.value.tipoResumo?.code) {
        case 2:
            funcLotacao.value.openModal();
            break;
        case 3:
            funcMatricula.value.openModal();
            break;
        case 4:
            funcLocalTrabalho.value.openDialog();
            break;
        case 5:
            funcCargo.value.openDialog();
            break;
        default:
            break;
    }
}

function setRegistroInicio (response) {
    switch (data.value.tipoResumo?.code) {
        case 2:
            data.value.inicio = response.r70_codigo;
            break;
        case 3:
            data.value.inicio = response.rh01_regist;
            break;
        case 4:
            data.value.inicio = response.rh55_codigo;
            break;
        case 5:
            data.value.inicio = response.rh37_funcao;
            break;
    }
}

function setRegistroFim (response) {
    switch (data.value.tipoResumo?.code) {
        case 2:
            data.value.fim = response.r70_codigo;
            break;
        case 3:
            data.value.fim = response.rh01_regist;
            break;
        case 4:
            data.value.fim = response.rh55_codigo;
            break;
        case 5:
            data.value.fim = response.rh37_funcao;
            break;
    }
}

function setRegistroLotacao(response) {
    data.value.adicionar.descricao = response.r70_descr
    data.value.adicionar.codigo = response.r70_codigo;
}
function setRegistroMatricula(response) {
    data.value.adicionar.descricao = response.z01_nome;
    data.value.adicionar.codigo = response.rh01_regist;
}
function setRegistroLocal(response) {
    data.value.adicionar.descricao = response.rh55_descr;
    data.value.adicionar.codigo = response.rh55_codigo;
}
function setRegistroCargo(response) {
    data.value.adicionar.descricao = response.rh37_descr;
    data.value.adicionar.codigo = response.rh37_funcao;
}

function limparFiltros () {
    data.value.adicionar.codigo = '';
    data.value.adicionar.descricao = '';
    data.value.inicio = null;
    data.value.fim = null;
    data.value.registros = [];
}

function adicionarRegistro () {

    if (empty(data.value.adicionar.codigo)) {
        alert('Selecione um registro!');
        return false;
    }

    let found = data.value.registros.find((linha) => linha.codigo == data.value.adicionar.codigo);

    if (found) {
        alert('Registro já lançado.')
        return false;
    }

    data.value.registros.push({
        codigo: data.value.adicionar.codigo,
        descricao : data.value.adicionar.descricao,
    });
    data.value.adicionar.codigo = '';
    data.value.adicionar.descricao = '';
}

function deletarRegistro (registro) {
    data.value.registros = data.value.registros.filter(val => val.codigo !== registro.codigo);
};

watch(() => data.value.tipoFiltro, () => {
    limparFiltros();
});

defineExpose({
    data
});

</script>

<template>
    <Panel header="Filtros Adicionais" class="w-11">
        <div class="formgrid grid mt-2 justify-content-center">
            <div class="field col-12">
                <span class="p-float-label mt-3">
                    <Dropdown v-model="data.tipoResumo" :options="filtros" optionLabel="name"
                        placeholder="Tipo de resumo" class="w-full" />
                    <label>Tipo de Resumo</label>
                </span>
            </div>
            <div class="field col-12">
                <span class="p-float-label mt-3">
                    <Dropdown v-model="data.tipoFiltro" :options="tipoFiltros" optionLabel="name"
                        placeholder="Tipo de Filtro" class="w-full" />
                    <label> Tipo de Filtro</label>
                </span>
            </div>
            <div v-if="data.tipoFiltro?.code == 3 && data.tipoResumo?.code > 1" class="formgrid grid w-full">
                <div class="field col-12 md:col-12">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label">
                            <AutoComplete v-model="data.adicionar.descricao" inputId="adicionar" disabled required />
                            <span class="p-inputgroup-addon" @click="openDialog">
                                <i class="pi pi-search"></i>
                            </span>
                            <Button type="button" class="ml-1" raised label="Adicionar" icon="pi pi-plus" @click="adicionarRegistro" />
                        </span>
                    </div>
                </div>
                <div class="field col w-full">
                    <DataTable :value="data.registros" scrollable scrollHeight="350px" tableStyle="min-width: 50rem;">
                        <Column field="codigo" header="Código"></Column>
                        <Column field="descricao" header="Descrição"></Column>
                        <Column header="Ação" style="width:10%">
                            <template #body="slotProps">
                                <Button icon="pi pi-trash" size="small" outlined rounded severity="danger"
                                    @click="deletarRegistro(slotProps.data)" />
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro selecionado
                        </template>
                    </DataTable>
                </div>
            </div>
            <div v-if="data.tipoFiltro?.code == 2 && data.tipoResumo?.code > 1" class="formgrid grid w-full">
                <div class="field col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label mt-3">
                            <InputNumber
                                v-model="data.inicio"
                                :useGrouping="false"/>
                            <label for="dependente">Registro Inicial</label>
                            <span class="p-inputgroup-addon" @click="openDialogIntervalo(setRegistroInicio)">
                                <i class="pi pi-search"></i>
                            </span>
                        </span>
                    </div>
                </div>
                <div class="field col-6">
                    <div class="p-inputgroup flex-1">
                        <span class="p-float-label mt-3">
                            <InputNumber
                                v-model="data.fim"
                                :useGrouping="false"/>
                            <label for="dependente">Registro Final</label>
                            <span class="p-inputgroup-addon" @click="openDialogIntervalo(setRegistroFim)">
                                <i class="pi pi-search"></i>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </Panel>
    <DialogMatricula        ref="funcMatricula"     v-on:selectRow="callback"/>
    <DialogCargo            ref="funcCargo"         @select="callback" />
    <DialogLota             ref="funcLotacao"       v-on:selectRow="callback"/>
    <DialogLocalDeTrabalho  ref="funcLocalTrabalho" @select="callback"/>
</template>

<style scoped>
.p-inputgroup-addon {
    cursor: pointer !important;
}
</style>
