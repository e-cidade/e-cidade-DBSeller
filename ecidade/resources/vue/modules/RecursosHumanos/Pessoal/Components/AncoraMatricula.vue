<template>
    <tr id="trMatricula">
        <td>
            <label class="font-bold" @click="openDialog()" :class="`dbancora`">Matrícula: </label>
        </td>
        <td>
            <td>
                <InputText
                    type="text"
                    @change="busca"
                    placeholder="Código"
                    v-model="codigoMatricula"
                    v-mask="'##########'" 
                    style="width:85px; margin-left: -3px; overflow: hidden; margin-right: 2px;"
                    aria-readonly="bloqueiaBusca"
                    inputId="codigoMatricula"
                    :useGrouping="false"
                /> 
                <InputText
                    type="text"
                    placeholder="Nome"
                    v-model="descricaoMatricula"
                    style="width: 250px;"
                    id="nomeServidor"
                    readonly
                />
            </td>
        </td>
    </tr>
    <Dialog
        :header="`Consulta de Matrículas`"
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
                    :value="matriculas"
                    showGridlines
                    responsiveLayout="scroll"
                    :loading="loading"
                    @rowSelect="selectItemMatricula"
                    selectionMode="single"
                    :scrollable="true"
                    scrollHeight="flex"
                    :paginator="true"
                    :rowsPerPageOptions="[10, 20, 50, 100, 1000, 10000]"
                    :rows="10"
                    v-model:filters="filtersMatricula"
                    :globalFilterFields="['rh01_regist', 'z01_nome']"
                >
                    <template #header>
                        <div class="flex justify-content-between">
                            <h2 class="m-0">Matrículas</h2>
                            <span class="p-input-icon-left">
                                <i class="pi pi-search" />
                                <InputText v-model="filtersMatricula['global'].value" placeholder="Procurar" />
                            </span>
                        </div>
                    </template>
                    <template #empty>Matrícula não encontrada.</template>
                    <template #loading> Buscando Informações. Aguarde. </template>
                    <Column sortable field="rh01_regist" class="text-center" header="Matrícula"/>
                    <Column sortable field="z01_nome" class="text-center" header="Nome"/>
                </DataTable>
                
            </div>
        </div>
    </Dialog>
</template>
<script setup>
    import { FilterMatchMode } from 'primevue/api';
    import { ref } from 'vue';

    const emit = defineEmits(['alteraMatricula', 'semMatricula']);
    const props = defineProps({exibeRescindido:Boolean});

    const filtersMatricula = ref(
        {
            global: {value: null, matchMode: FilterMatchMode.CONTAINS},
            rh01_regist: {value: null, matchMode: FilterMatchMode.EQUALS},
            z01_nome: {value: null, matchMode: FilterMatchMode.STARTS_WITH}
        } 
    );

    const urlApi = 'v4/api/recursos-humanos/pessoal/rhpessoal';
    const codigoMatricula = ref(null);
    const descricaoMatricula = ref(null);
    const showDialog = ref(false);
    const loading = ref(false);
    const matriculas = ref([]);
    const updated = ref(false);
    const refresh = ref(true);
    const exibeRescindido = ref(false);
    
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
    
    const selectItemMatricula = (e) => {
        codigoMatricula.value = e.data.rh01_regist;
        descricaoMatricula.value = e.data.z01_nome;
        emit('alteraMatricula', codigoMatricula.value);
        closeDialog(false);
    };

    const inicializa = async () => {
        if (updated.value == false) {
            matriculas.value = [];
            loading.value = true;
            try {
                let resp = await window.axios.post(
                    urlApi + '/list', {
                        rescindidos : exibeRescindido.value
                    }
                );
                matriculas.value = resp.data.data;
            } catch (e) {
                matriculas.value = [];
            }
            if (refresh.value == false) {
                updated.value = true;
            }
            loading.value = false;
        }
    };
    const busca = async () => {
        try {
            if (codigoMatricula.value != null) {
                const resp = await window.axios.post(
                    urlApi + '/list', {
                        rh01_regist : codigoMatricula.value,
                        rescindidos : exibeRescindido.value
                    }
                );
                if (resp.data.data[0] === undefined || resp.data.data[0].rh01_regist == null) {
                    codigoMatricula.value = null;
                    descricaoMatricula.value = "Matrícula não encontrada."
                    emit('semMatricula', true);
                } else {
                    emit('alteraMatricula', codigoMatricula.value);
                    descricaoMatricula.value = resp.data.data[0].z01_nome;
                }
            }
        } catch (e) {
            matriculas.value = [];
        }
    }
    defineExpose({
        codigoMatricula
    })
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