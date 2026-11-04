<script setup>

import {computed, ref, watch} from 'vue';
import {formateDate} from "@utils/Strings";

import ModalLoading from "@modules/Components/ModalLoading.vue";
import {useToast} from "primevue/usetoast";
import MultiDownload from "@modules/Components/MultiDownload.vue";

const toast = useToast();
const props = defineProps(['modelValue', 'periodo']);
const emit = defineEmits(['update:modelValue'])

const filtro = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const loading = ref(false);
const metaKey = ref(true);
const emissoes = ref([]);

const multiDownload = ref(null);
const dialogDeletarEmissao = ref(false);
const emissaoDeletar = ref();

const rotas = {
    emissoes: 'v4/api/financeiro/contabilidade/relatorio-legal/emissoes',
    publicar: 'v4/api/financeiro/contabilidade/relatorio-legal/emissao/publicar',
    deletar: 'v4/api/financeiro/contabilidade/relatorio-legal/emissao',
    download: 'v4/api/financeiro/contabilidade/relatorio-legal/emissao/download',
}

function getSeverity(status) {
    switch (status) {
        case 'PROCESSANDO':
            return 'warning';

        case 'PROCESSADO':
            return 'success';

        case 'ERRO':
            return 'danger';
    }
}

function confirmeDeletarEmissao(data) {
    dialogDeletarEmissao.value = true
    emissaoDeletar.value = data;
}

function deletarEmissao() {
    loading.value = true;
    window.axios.delete(`${rotas.deletar}/${emissaoDeletar.value.codigo}`).then(async response => {
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
        buscarEmissoes();
    }).catch(e => {
        data.publicado = !data.publicado;
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;

        dialogDeletarEmissao.value = false
        emissaoDeletar.value = null;
    });
}

function publicar(data) {

    const parametros = {"codigo": data.codigo, "publicado": data.publicado};
    loading.value = true;
    window.axios.post(rotas.publicar, parametros).then(async response => {
        toast.add({severity: 'success', detail: response.data.message, summary: 'Sucesso'});
    }).catch(e => {
        data.publicado = !data.publicado;
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }).finally(() => {
        loading.value = false;
    });
}

async function emitir(idStorage, tipo) {
    window.open(`${window.ECIDADE_PATH}${rotas.download}/${idStorage}`);
}

async function buscarEmissoes() {
    const parametros = {
        relatorio: filtro.value.relatorio.codigo,
        periodo: props.periodo.codigo,
        instituicao: filtro.value.instituicao,
        with: ['relatorio', 'periodo', 'usuario']
    }
    emissoes.value = [];
    await window.axios.get(`${rotas.emissoes}`, {'params': parametros}).then(async response => {
        emissoes.value = response.data.data;
    }).catch(response => {
        toast.add({severity: 'error', detail: response.response.data.message, summary: 'Erro'});
    });
}

watch(() => props.periodo, (item) => {
    if (item !== null) {
        buscarEmissoes();
    }
})
</script>

<template>
    <section class="flex justify-content-center flex-wrap card-container pt-5">
        <div class="card">
            <DataTable :value="emissoes" dataKey="codigo" tableStyle="min-width: 50rem">
                <Column sortable field="create_at" header="Data">
                    <template #body="{data}">
                        {{ formateDate(data.create_at) }}
                    </template>
                </Column>
                <Column sortable field="usuario.nome" header="Emissor"></Column>
                <Column sortable field="status" header="Situação">
                    <template #body="{ data }">
                        <Tag :value="data.status" :severity="getSeverity(data.status)"/>
                    </template>
                </Column>
                <Column sortable field="publicado" header="Publicado">
                    <template #body="{ data }">
                        <Checkbox v-model="data.publicado" :disabled="data.status != 'PROCESSADO'"
                                  :binary="true" @change="publicar(data)"/>
                    </template>
                </Column>

                <Column header="Ação" style="width: 10%">
                    <template #body="slotProps">
                        <div class="flex align-items-center gap-2">
                            <Button icon="pi pi-file-excel" severity="success" outlined rounded aria-label="EXCEL"
                                    v-if="slotProps.data.storage?.xls != null"
                                    @click="emitir(slotProps.data.storage?.xls, 'XLS')"/>
                            <Button icon="pi pi-file-pdf"  outlined rounded aria-label="PDF"
                                    v-if="slotProps.data.storage?.pdf != null"
                                    @click="emitir(slotProps.data.storage?.pdf, 'PDF')"/>
                            <Button icon="pi pi-trash" outlined rounded severity="danger" aria-label="Deletar"
                                    @click="confirmeDeletarEmissao(slotProps.data)"/>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </section>

    <Dialog v-model:visible="dialogDeletarEmissao" :style="{width: '450px'}" header="Confirme" :modal="true">
        <div class="confirmation-content">
            <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem"/>
            <span v-if="emissaoDeletar">Tem certeza que deseja deletar essa emissão?</span>
        </div>
        <template #footer>
            <Button label="Não" icon="pi pi-times" text @click="dialogDeletarEmissao = false"/>
            <Button label="Sim" icon="pi pi-check" text @click="deletarEmissao"/>
        </template>
    </Dialog>

    <ModalLoading :isLoading="loading"/>
    <MultiDownload ref="multiDownload" :header="'Relatório'" :position="'center'"></MultiDownload>
</template>

<style scoped>

</style>
