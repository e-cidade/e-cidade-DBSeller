<script setup>

import { onMounted, ref, watch } from "vue";
import { useToast } from "primevue/usetoast";
import ModalLoading from "../../../../Components/ModalLoading.vue";

const props = defineProps({
    relatorio: { type: Object, required: true },
    visible: { type: Boolean, required: true }
});

const toast = useToast();

const isLoading = ref(false);

const modulo = ref(null);
const modulos = ref([]);
const modulosFiltrados = ref([]);

const item = ref(null);
const itens = ref([]);

function autoCompleteModulo({ query }) {
    if (!query.trim().length) {
        modulosFiltrados.value = [ ...modulos.value ];
        return;
    }

    modulosFiltrados.value = modulos.value.filter(modulo => {
        if (Number.isInteger(Number(query))) {
            return modulo.codigo.toString().startsWith(query);
        }

        return modulo.descricao.toLowerCase().startsWith(query.toLowerCase());
    });
}

async function getModulos() {
    isLoading.value = true;
    try {
        const response = await axios.get('v4/api/configuracao/menu/modulos');
        modulos.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar módulos',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

async function getItens() {
    isLoading.value = true;
    try {
        const response = await axios.get(`v4/api/configuracao/menu/modulos/${modulo.value.codigo}/itens`);
        itens.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar itens',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

async function salvar() {
    const [codigoItem] = Object.keys(item.value);

    const data = {
        descricao: props.relatorio.nome,
        ajuda: props.relatorio.nome,
        rota: `web/configuracao/gerador/relatorios/${props.relatorio.codigo}`,
        ativo: true,
        manutencao: true,
        descricaoTecnica: `${props.relatorio.nome}\nGerado automáticamente através do Gerador de Relatórios.`,
        liberadoCliente: true
    };

    try {
        await axios.post(`v4/api/configuracao/menu/modulos/${modulo.value.codigo}/itens/${codigoItem}`, data);

        toast.add({
            severity: 'success',
            summary: 'Menu salvo sucesso'
        });
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao salvar menu',
            detail: e.response ? e.response.data.message : e.message
        });
    }
}

onMounted(getModulos);

</script>

<template>
    <Dialog :pt="{ content: { style: 'background: #e1dede;' } }"
            :visible="visible"
            class="p-dialog-maximized"
            header="Cadastrar Menu"
            modal
            @update:visible="value => $emit('update:visible', value)">
        <section class="flex flex-column w-full mt-4 gap-2">
            <section class="flex justify-content-center">
                <article class="formgrid grid row-gap-2">
                    <section class="field col-6 md:col-12">
                        <span class="p-float-label">
                            <AutoComplete v-model="modulo"
                                          :suggestions="modulosFiltrados"
                                          class="w-full"
                                          dropdown
                                          forceSelection
                                          inputId="modulo"
                                          :optionLabel="modulo => `${modulo.codigo} - ${modulo.descricao}`"
                                          @itemSelect="getItens"
                                          @itemUnselect="item = null"
                                          @complete="autoCompleteModulo">
                                <template #option="slotProps">
                                    <div>
                                        {{ slotProps.option.codigo }} - {{ slotProps.option.descricao }}
                                    </div>
                                </template>
                            </AutoComplete>
                            <label for="modulo">Módulo</label>
                        </span>
                    </section>
                </article>
            </section>
            <section v-if="modulo?.codigo" class="flex justify-content-center">
                <Tree v-model:selectionKeys="item"
                      :filter="true"
                      :value="itens"
                      class="w-full md:w-8"
                      filterMode="strict"
                      selectionMode="single"
                      scrollHeight="400px"></Tree>
            </section>
            <section class="flex justify-content-center">
                <Button :disabled="!item" icon="pi pi-save" label="Salvar" @click="salvar"></Button>
            </section>
        </section>
    </Dialog>

    <ModalLoading :isLoading="isLoading"></ModalLoading>
</template>

<style scoped>

</style>
