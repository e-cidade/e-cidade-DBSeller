<template>
    <tr id="trLotacao">
        <td>
            <label class="font-bold" @click="openDialog()" :class="`dbancora`">Lotação: </label>
        </td>
        <td>
            <td>
                <InputNumber
                    type="text"
                    :onChange="busca()"
                    placeholder="Código"
                    v-model="codigoLotacao" 
                    inputStyle="width:62px"
                    style="margin-left: -3px; overflow: hidden; margin-right: 2px;"
                    aria-readonly="bloqueiaBusca"
                    inputId="codigoLotacao"
                    :useGrouping="false"
                /> 
                <InputText
                    type="text"
                    placeholder="Descrição"
                    v-model="descricaoLotacao" 
                    style="width: 150px;"
                    readonly
                    id="descricaoLotacao"
                />
            </td>
        </td>
    </tr>
    <Dialog
        :header="`Consulta de Lotações`"
        :maximizable="true"
        :modal="true"
        :style="{ width: '1000px' }"
        :visible="showDialog"
        :closable="true"
        min-height="400px"
        @update:visible="closeDialog"
    >
        <div class="mt-6">
            <div class="card"
            >
                <DataTable 
                    :value="lotacoes"
                    showGridlines
                    responsiveLayout="scroll"
                    :loading="loading"
                    @rowSelect="selectItemLotacao"
                    selectionMode="single"
                    :scrollable="true"
                    scrollHeight="flex"
                    :paginator="true"
                    :rowsPerPageOptions="[10, 20, 50, 100]"
                    :rows="10"
                    v-model:filters="filtersLotacao"
                    :globalFilterFields="['codigo', 'descricao', 'estrutural']"
                >
                    <template #header>
                        <div class="flex justify-content-between">
                            <h2 class="m-0">Lotações</h2>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filtersLotacao['global'].value" placeholder="Procurar" />
                            </span>
                        </div>
                    </template>
                    <template #empty> Lotação não encontrada. </template>
                    <template #loading> Buscando Informações. Aguarde. </template>
                    <Column field="codigo" class="text-center" header="Código"/>
                    <Column field="estrutural" class="text-center" header="Estrutural"/>
                    <Column field="descricao" class="text-center" header="Descrição"/>
                </DataTable>
            </div>
        </div>
    </Dialog>
</template>
<script setup>
    import { FilterMatchMode } from 'primevue/api';
    import { ref } from 'vue';

    const filtersLotacao = ref(
        {
            global: {value: null, matchMode: FilterMatchMode.CONTAINS},
            codigo: {value: null, matchMode: FilterMatchMode.EQUALS},
            descricao: {value: null, matchMode: FilterMatchMode.STARTS_WITH},
            estrutural: {value: null, matchMode: FilterMatchMode.STARTS_WITH}
        } 
    );

    const codigoLotacao = ref(null);
    const descricaoLotacao = ref(null);
    const showDialog = ref(false);
    const loading = ref(false);
    const lotacoes = ref([]);
    const updated = ref(false);
    const refresh = ref(true);
    
    const closeDialog = (e) => {
        showDialog.value = false;
    };
    
    const openDialog = () => {
        showDialog.value = true;
        if (refresh.value != true) {
            refresh.value = false;
        }
        inicializa();
    };
    
    const selectItemLotacao = (e) => {
        codigoLotacao.value = e.data.codigo;
        descricaoLotacao.value = e.data.descricao;
        closeDialog(false);
    };

    const inicializa = async () => {
        if (updated.value == false) {
            lotacoes.value = [];
            loading.value = true;
            try {
                let resp = await window.axios.get(
                    'v4/api/recursos-humanos/pessoal/lotacao/list', {}
                );
                lotacoes.value = resp.data.data;
            } catch (e) {
                lotacoes.value = [];
            }
            if (refresh.value == false) {
                updated.value = true;
            }
            loading.value = false;
        }
    };
    const busca = async () => {
        try {
            if (codigoLotacao.value != null) {
                const resp = await window.axios.get(
                    'v4/api/recursos-humanos/pessoal/lotacao/find/' + codigoLotacao.value, {}
                );
                if (resp.data.data.codigo == null) {
                    codigoLotacao.value = null;
                }
                descricaoLotacao.value = resp.data.data.descricao;
            }
        } catch (e) {
            lotacoes.value = [];
        }
    }
    defineExpose({
        codigoLotacao,
    });
</script>
<style scoped>
    .dbancora {
        text-decoration: underline;
        color: blue;
    }

    .dbancora:hover {
        cursor: pointer;
    }
</style>
