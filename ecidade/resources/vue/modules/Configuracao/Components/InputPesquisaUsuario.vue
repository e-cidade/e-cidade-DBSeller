<template>
    <DialogUsuario
        ref="dialogUsuarios"
        @select="selecaoUsuarios"
        :instituicao_sessao="props.instituicao_sessao"
    />
    <b>{{ props.label ? props.label : 'Usuário' }}:</b><br>
    <div class="p-inputgroup"
         ref="containerSelecaoUsuario"
    >
    <span class="p-inputgroup-addon"
          @click="openDialogUsuarios"
    >
        <i class="pi pi-search"></i>
    </span>
        <AutoComplete
            v-model="selectUsuario"
            :placeholder="props.label ? props.label : 'Usuário'"
            optionLabel="usuario_label"
            :suggestions="suggestionsUsuario"
            :dropdown="true"
            forceSelection
            @complete="pesquisarUsuarios($event)"
            :multiple="multiple"
        />
    </div>
</template>

<script setup>
import {
    ref,
    watch
} from 'vue';

const props = defineProps([
    "instituicao_sessao",
    "label",
    "multiple"
]);

const emit = defineEmits(
    ['select']
);

import DialogUsuario from "./DialogUsuario";

const dialogUsuarios = ref(null);
let selectUsuario = ref(null);
const suggestionsUsuario = ref([]);

const openDialogUsuarios = () => {
    dialogUsuarios.value.openDialog();
}

const pesquisarUsuarios = async ({query} = {query: ''}) => {
    try {

        const filtros = {
            label: query,
            instituicao_sessao: props.instituicao_sessao
        }
        const urlParams = new URLSearchParams(filtros);
        const resp = await window.axios.get(
            `v4/api/configuracao/usuario/search?${urlParams.toString()}`
        );
        suggestionsUsuario.value = resp.data.data.data;
    } catch (e) {
        suggestionsUsuario.value = [];
    }
}

const selecaoUsuarios = (usuario) => {
    if (props.multiple === true) {
        if (!(selectUsuario.value instanceof Array)) {
            selectUsuario.value = [];
        }
        selectUsuario.value.push(usuario);
        return;
    }
    selectUsuario.value = usuario;
}

watch(selectUsuario, (newSelecUsuario, oldSelectUsuario) => {
    emit('select', newSelecUsuario);
});

</script>

<style scoped>

</style>
