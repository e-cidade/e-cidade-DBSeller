<script setup>

import { computed, onMounted, ref, watch } from "vue";
import { useToast } from "primevue/usetoast";

import ModalLoading from "../../../../../Components/ModalLoading.vue";
import DialogTelaDinamica from "../DialogTelaDinamica";

const props = defineProps({
    dados: { type: Object, required: true },
    grupo: { default: null },
    tipo: { default: null },
    template: { default: null }
});

const emit = defineEmits(['update:grupo', 'update:tipo', 'update:template', 'callbackSalvar']);

const toast = useToast();

const fileUpload = ref();

const isLoading = ref(false);
const dialogVisible = ref(false);

const relatorio = computed(() => props.dados);

const grupo = computed({
    get() { return props.grupo },
    set(value) { emit('update:grupo', value) }
});
const tipo = computed({
    get() { return props.tipo },
    set(value) { emit('update:tipo', value) }
});
const template = computed({
    get() { return props.template },
    set(value) {
        templateSalvo.value = false;
        emit('update:template', value);
    }
});

const grupos = ref([]);
const gruposFiltrados = ref([]);
const tipos = ref([]);
const templateSalvo = ref(false);
const tiposFiltrados = ref([]);
const tipoVisualizacao = ref('3');
const tiposVisualizacao = ref([
    { value: '1', label: 'Usuário' },
    { value: '2', label: 'Departamento' },
    { value: '3', label: 'Público' },
    { value: '4', label: 'Cubos BI' }
]);

const erro = ref({
    grupo: false,
    tipo: false
});

const dadosRelatorio = computed(buildData);

function autoCompleteGrupo({ query }) {
    if (!query.trim().length) {
        gruposFiltrados.value = [ ...grupos.value ];
        return;
    }

    gruposFiltrados.value = grupos.value.filter(grupo => {
       if (Number.isInteger(Number(query))) {
           return grupo.codigo.toString().startsWith(query);
       }

       return grupo.descricao.toLowerCase().startsWith(query.toLowerCase());
    });
}

function autoCompleteTipo({ query }) {
    if (!query.trim().length) {
        tiposFiltrados.value = [ ...tipos.value ];
        return;
    }

    tiposFiltrados.value = tipos.value.filter(tipo => {
        if (Number.isInteger(Number(query))) {
            return tipo.codigo.toString().startsWith(query);
        }

        return tipo.descricao.toLowerCase().startsWith(query.toLowerCase());
    });
}

async function getGrupos() {
    try {
        const response = await axios.get('v4/api/configuracao/gerador/relatorios/grupos');
        grupos.value = response.data.data;
        gruposFiltrados.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar grupos',
            detail: e.response ? e.response.data.message : e.message
        });
    }
}

async function getTipos() {
    try {
        const response = await axios.get('v4/api/configuracao/gerador/relatorios/tipos');
        tipos.value = response.data.data;
        tiposFiltrados.value = response.data.data;
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao buscar tipos',
            detail: e.response ? e.response.data.message : e.message
        });
    }
}

function onSelectFile({ files }) {
    template.value = files[0];
}

function onRemoveFile(callback) {
    template.value = null;
    callback(0);
}

function downloadTemplate() {
    saveAs(template.value);
}

async function uploadTemplate(codigoRelatorio) {
    const formData = new FormData();
    formData.append('template', template.value);
    await axios.post(`v4/api/configuracao/gerador/relatorios/${codigoRelatorio}/template`, formData);
}

/**
 * @todo usar vee-validate?
 */
function hasError() {
    let hasError = false;
    erro.value = {
        grupo: false,
        tipo: false
    };

    if (!dadosRelatorio.value.grupo) {
        erro.value.grupo = true;
        hasError = true;
    }
    if (!dadosRelatorio.value.tipo) {
        erro.value.tipo = true;
        hasError = true;
    }

    if (hasError) {
        return true;
    }

    if (dadosRelatorio.value.tipo === 2 && template.value === null) {
        alert("Para o tipo '2 - Documento' é obrigatório informar um template.");
        return true;
    }
    if (dadosRelatorio.value.campos.length === 0) {
        alert('Necessário informar pelo menos um campo.');
        return true;
    }
    if (!dadosRelatorio.value.layout.nome) {
        alert('Necessário informar um nome para o relatório.');
        return true;
    }

    return false;
}

async function salvar() {
    if (hasError()) {
        return;
    }

    isLoading.value = true;
    try {
        let codigo = relatorio.value.codigo;
        if (codigo) {
            await axios.put(`v4/api/configuracao/gerador/relatorios/${codigo}`, dadosRelatorio.value);
        } else {
            const response = await axios.post('v4/api/configuracao/gerador/relatorios', dadosRelatorio.value);
            codigo = response.data.data;
        }

        if (tipo.value.codigo === 2) {
            await uploadTemplate(codigo);
        }

        toast.add({
            severity: 'success',
            summary: 'Relatório salvo com sucesso',
            life: 3000
        });

        emit('callbackSalvar');
    } catch (e) {
        toast.add({
            severity: 'error',
            summary: 'Erro ao salvar relatório',
            detail: e.response ? e.response.data.message : e.message
        });
    }
    isLoading.value = false;
}

async function visualizar() {
    if (hasError()) {
        return;
    }

    dialogVisible.value = true;
}

function buildData() {
    const [, camposConfigurados ] = relatorio.value.campos;
    const [, ordemConfigurada ] = relatorio.value.ordem;

    return {
        grupo: relatorio.value.grupo?.codigo,
        tipo: relatorio.value.tipo?.codigo,
        origem: relatorio.value.origem,
        campos: camposConfigurados,
        ordem: ordemConfigurada,
        layout: relatorio.value.layout,
        variaveis: relatorio.value.variaveis,
        filtros: relatorio.value.filtros,
        tipoVisualizacao: tipoVisualizacao.value,
        sql: relatorio.value.sql
    };
}

onMounted(async () => {
    isLoading.value = true;
    await Promise.all([getGrupos(), getTipos()]);
    isLoading.value = false;

    if (props.template) {
        template.value = props.template;
        fileUpload.value.files[0] = props.template;
        templateSalvo.value = true;
    }
});

</script>

<template>
    <section class="flex flex-column gap-2">
        <section class="flex justify-content-center">
            <Panel header="Dados do Relatório" class="w-4">
                <form class="formgrid grid row-gap-2 mt-2">
                    <section class="field col-12">
                        <span class="p-float-label">
                            <AutoComplete v-model="grupo"
                                          :suggestions="gruposFiltrados"
                                          :inputClass="{ 'p-invalid': erro.grupo }"
                                          class="w-full"
                                          dropdown
                                          forceSelection
                                          inputId="grupo"
                                          :optionLabel="grupo => `${grupo.codigo} - ${grupo.descricao}`"
                                          @complete="autoCompleteGrupo">
                                <template #option="slotProps">
                                    <div>
                                        {{ slotProps.option.codigo }} - {{ slotProps.option.descricao }}
                                    </div>
                                </template>
                            </AutoComplete>
                            <label for="grupo">Grupo</label>
                        </span>
                        <small v-if="erro.grupo" class="p-error" id="grupo-error">Informe este campo.</small>
                    </section>
                    <section class="field col-12">
                        <span class="p-float-label">
                            <AutoComplete v-model="tipo"
                                          :suggestions="tiposFiltrados"
                                          :inputClass="{ 'p-invalid': erro.tipo }"
                                          class="w-full"
                                          dropdown
                                          forceSelection
                                          inputId="tipo"
                                          :optionLabel="tipo => `${tipo.codigo} - ${tipo.descricao}`"
                                          @complete="autoCompleteTipo">
                                <template #option="slotProps">
                                    <div>
                                        {{ slotProps.option.codigo }} - {{ slotProps.option.descricao }}
                                    </div>
                                </template>
                            </AutoComplete>
                            <label for="tipo">Tipo</label>
                        </span>
                        <small v-if="erro.tipo" class="p-error" id="tipo-error">Informe este campo.</small>
                    </section>
                    <section v-if="tipo?.codigo === 2" class="field col-12 mb-4">
                        <FileUpload ref="fileUpload"
                                    :customUpload="true"
                                    :fileLimit="1"
                                    accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    invalidFileTypeMessage="{0}: Tipo de arquivo inválido, tipos de arquivo permitidos: {1}"
                                    name="relatorios[]"
                                    @select="onSelectFile">
                            <template #header="{ files, chooseCallback }">
                                <div class="flex flex-wrap justify-content-between align-items-center flex-1 gap-2">
                                    <div class="flex">
                                        <Button :disabled="files.length" icon="pi pi-file" label="Selecione" @click="chooseCallback()"></Button>
                                    </div>
                                </div>
                            </template>
                            <template #content="{ files, removeFileCallback }">
                                <div v-if="files.length" class="flex flex-row card align-items-center justify-content-between">
                                    <div>
                                        <span>{{ files[0].name }}</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <Button icon="pi pi-download" @click="downloadTemplate" outlined rounded></Button>
                                        <Button icon="pi pi-times" @click="onRemoveFile(removeFileCallback)" outlined rounded severity="danger" />
                                    </div>
                                </div>
                            </template>
                            <template #empty>
                                <p>Nenhum arquivo selecionado.</p>
                            </template>
                        </FileUpload>
                    </section>
                    <section class="field col-12">
                        <span class="p-float-label">
                            <Dropdown v-model="tipoVisualizacao"
                                      :options="tiposVisualizacao"
                                      optionValue="value"
                                      optionLabel="label"
                                      inputId="visualizacao"
                                      class="w-full"></Dropdown>
                            <label for="visualizacao">Visualização</label>
                        </span>
                    </section>
                </form>
            </Panel>
        </section>
        <section class="flex justify-content-center gap-1">
            <Button :disabled="tipo?.codigo === 2 && !templateSalvo" icon="pi pi-eye" label="Visualizar" @click="visualizar"></Button>
            <Button icon="pi pi-save" label="Salvar" @click="salvar"></Button>
        </section>
    </section>

    <ModalLoading :isLoading="isLoading"></ModalLoading>
    <DialogTelaDinamica :modal="true" :modelValue="dadosRelatorio" v-model:visible="dialogVisible"></DialogTelaDinamica>
</template>

<style scoped>

</style>
