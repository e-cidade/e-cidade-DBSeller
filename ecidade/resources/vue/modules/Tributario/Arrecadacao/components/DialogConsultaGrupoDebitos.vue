<template>
    <Dialog header="Consulta Grupo de Débitos" :maximizable="true" :modal="true" :style="{ width: '1000px' }"
        position="top" v-model:visible="data.dialogAberto" class="shadow-4">
        <section style="width: 100%" class="m-auto">
            <DataTable :value="data.data" responsiveLayout="scroll" :rowHover="true" :rows="8" showGridlines
                :paginator="false" filterDisplay="menu" :loading="data.loading" v-model::selection="data.select"
                selectionMode="single" dataKey="sequencial" @rowSelect="onRowSelect" class="shadow-4">
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading>
                    <ProgressSpinner />
                </template>
                <Column field="sequencial" :header="data.labels.k03_tipo" />
                <Column field="descricao" :header="data.labels.k03_descr" />
                <Column field="parelaSomanteAnoAtual" :header="data.labels.k03_parcano" />
                <Column field="isParcelamento" :header="data.labels.k03_parcelamento" />
                <Column field="permiteParcelar" :header="data.labels.k03_permparc" />
            </DataTable>
        </section>
    </Dialog>
</template>

<script setup>
import { reactive, onMounted } from "vue";

const emit = defineEmits(["selectRow"]);
const data = reactive({
    dialogAberto: false,
    loading: false,
    select: null,
    labels: {
        k03_tipo: '',
        k03_descr: '',
        k03_parcano: '',
        k03_parcelamento: '',
        k03_permparc: '',
    },
    data: [],
});

onMounted(() => {
    pesquisaLabels();
});

async function pesquisaLabels() {
    const labels = await window.axios.get(
        "v4/api/tributario/arrecadacao/grupos-debito/rotulos"
    );
    data.labels = labels.data.data;
}

async function pesquisaGeralGrupoDebitos() {
    if (data.data.length > 0) {
        return data.data;
    }
    const response = await window.axios.get(
        "v4/api/tributario/arrecadacao/grupos-debito/buscar"
    );
    data.data = response.data.data.map(item => {
        return {
            ...item,
            parelaSomanteAnoAtual: formataBooleano(item.parelaSomanteAnoAtual),
            isParcelamento: formataBooleano(item.isParcelamento),
            permiteParcelar: formataBooleano(item.permiteParcelar),
        };
    })
    return data.data;
}

function toggleDialog() {
    data.dialogAberto = !data.dialogAberto;
    if (data.data.length == 0) {
        carregaResultados();
    }
}

function onRowSelect(event) {
    data.dialogAberto = false;
    emit("selectRow", event.data);
}

async function carregaResultados() {
    data.loading = true;
    data.data = [];

    pesquisaGeralGrupoDebitos()
        .catch((error) => {
            data.data = [];
            data.loading = false;
        })
        .finally(() => {
            data.loading = false;
        });
}

function formataBooleano(resultado) {
    return resultado === 't' ? 'Sim' : 'Não';
}

defineExpose({
    toggleDialog,
    pesquisaGeralGrupoDebitos
});
</script>
