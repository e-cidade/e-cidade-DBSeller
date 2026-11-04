<template>
    <tr id="trSelecao">
        <td>
            <label class="font-bold" @click="openDialog()" :class="`dbancora`">Seleção: </label>
        </td>
        <td>
            <td>
                <InputNumber
                    type="text"
                    :onChange="busca()"
                    placeholder="Código"
                    v-model="codigoSelecao" 
                    inputStyle="width:62px"
                    style="margin-left: -3px; overflow: hidden; margin-right: 2px;"
                    aria-readonly="bloqueiaBusca"
                    inputId="codigoSelecao"
                    :useGrouping="false"
                /> 
                <InputText
                    type="text"
                    placeholder="Descrição"
                    v-model="descricaoSelecao"
                    style="width: 150px;"
                    id="descricaoSelecao"
                    readonly
                />
            </td>
        </td>
    </tr>
    <Dialog
        :header="`Consulta de Seleções`"
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
                    :value="selecoes"
                    showGridlines
                    responsiveLayout="scroll"
                    :loading="loading"
                    @rowSelect="selectItemSelecao"
                    selectionMode="single"
                    :scrollable="true"
                    scrollHeight="flex"
                    :paginator="true"
                    :rowsPerPageOptions="[10, 20, 50, 100]"
                    :rows="10"
                    v-model:filters="filtersSelecao"
                    :globalFilterFields="['codigo', 'descricao']"
                >
                    <template #header>
                        <div class="flex justify-content-between">
                            <h2 class="m-0">Seleções</h2>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filtersSelecao['global'].value" placeholder="Procurar" />
                            </span>
                        </div>
                    </template>
                    <template #empty> Seleção não encontrada. </template>
                    <template #loading> Buscando Informações. Aguarde. </template>
                    <Column field="codigo" class="text-center" header="Código"/>
                    <Column field="descricao" class="text-center" header="Descrição"/>
                </DataTable>
                
            </div>
        </div>
    </Dialog>
</template>
<script setup>
    import { FilterMatchMode } from 'primevue/api';
    import { ref } from 'vue';

    const filtersSelecao = ref(
        {
            global: {value: null, matchMode: FilterMatchMode.CONTAINS},
            codigo: {value: null, matchMode: FilterMatchMode.EQUALS},
            descricao: {value: null, matchMode: FilterMatchMode.STARTS_WITH}
        } 
    );

    const codigoSelecao = ref(null);
    const descricaoSelecao = ref(null);
    const showDialog = ref(false);
    const loading = ref(false);
    const selecoes = ref([]);
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
    
    const selectItemSelecao = (e) => {
        codigoSelecao.value = e.data.codigo;
        descricaoSelecao.value = e.data.descricao;
        closeDialog(false);
    };

    const inicializa = async () => {
        if (updated.value == false) {
            selecoes.value = [];
            loading.value = true;
            try {
                let resp = await window.axios.get(
                    'v4/api/recursos-humanos/pessoal/selecao/list', {}
                );
                selecoes.value = resp.data.data;
            } catch (e) {
                selecoes.value = [];
            }
            if (refresh.value == false) {
                updated.value = true;
            }
            loading.value = false;
        }
    };
    const busca = async () => {
        try {
            if (codigoSelecao.value != null) {
                const resp = await window.axios.get(
                    'v4/api/recursos-humanos/pessoal/selecao/find/' + codigoSelecao.value, {}
                );
                if (resp.data.data.codigo == null) {
                    codigoSelecao.value = null;
                }
                descricaoSelecao.value = resp.data.data.descricao;
            }
        } catch (e) {
            selecoes.value = [];
        }
    }
    defineExpose({
        codigoSelecao,
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