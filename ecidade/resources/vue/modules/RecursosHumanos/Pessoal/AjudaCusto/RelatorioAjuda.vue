<script setup>
import FiltrosFolha from "../Components/FiltrosFolha.vue"
import ModalLoading    from "../../../Components/ModalLoading.vue";
import MultiDownload from "../../../Components/MultiDownload.vue";
import DialogConsultaCgm  from "../../../../modules/Patrimonial/Protocolo/Components/DialogConsultaCgm.vue";
import { useToast } from "primevue/usetoast";
import { ref, computed } from "vue";

const props = defineProps(["instituicao"]);
const toast = useToast();
const loading = ref(false);
const filtroFolha = ref(null);
const download = ref(null);
const dialogConsultaCgm = ref(null);

const dataFiltroFolha = computed(() => filtroFolha.value?.data)

const tipoEmissao = ref([
    { code: 1, name: 'PDF' },
    { code: 2, name: 'CSV' }
]);

const data = ref({
    data_inicio : null,
    data_fim: null,
    tipo: { code: 1, name: 'PDF' },
    registros_unidade : [],
    unidade : {
        codigo: '',
        descricao: ''
    }
});

function abrirConsultaCgm() {
    dialogConsultaCgm.value.toggleDialog();
}

function selecionaCgm(result) {
    data.value.unidade.descricao = result.nome;
    data.value.unidade.codigo = result.numcgm;
}

function deletarRegistro (registro) {
    data.value.registros_unidade = data.value.registros_unidade.filter(
        val => val.codigo !== registro.codigo
    );
}

function adicionarRegistro () {

    if (empty(data.value.unidade.codigo)) {
        alert('Selecione uma unidade.');
        return false;
    }

    let found = data.value.registros_unidade.find(
        (linha) => linha.codigo == data.value.unidade.codigo
    );

    if (found) {
        alert('Unidade já lançada.')
        return false;
    }

    data.value.registros_unidade.push({
        codigo: data.value.unidade.codigo,
        descricao : data.value.unidade.descricao,
    });
    data.value.unidade.codigo = '';
    data.value.unidade.descricao = '';
}

async function gerarRelatorio() {
    loading.value = true;

    let parametros = {
        data_inicial: data.value.data_inicio,
        data_final: data.value.data_fim,
        tipo: data.value.tipo?.code,
        tipo_resumo : dataFiltroFolha.value.tipoResumo?.code,
        tipo_filtro : dataFiltroFolha.value.tipoFiltro?.code,
        inicio : dataFiltroFolha.value.inicio,
        fim : dataFiltroFolha.value.fim,
        registros : dataFiltroFolha.value.registros,
        unidades : data.value.registros_unidade
    };

    let messageValidation = [];

    if (!parametros?.data_inicial?.toLocaleDateString() || !parametros?.data_final?.toLocaleDateString()) {
        messageValidation.push('Data inicial e final devem ser informados.')
    }

    if ((parametros?.data_final?.toLocaleDateString() && parametros?.data_inicial?.toLocaleDateString())) {
        if (new Date(parametros?.data_inicial) > new Date(parametros?.data_final)) {
            messageValidation.push('Data inicial nao pode ser superior a Data final.')
        }
    }

    if (parametros.tipo_resumo > 1 && parametros.tipo_filtro > 1) {

        if (parametros.tipo_filtro == 2) {
            if(!parametros.inicio || !parametros.fim) {
                messageValidation.push('Registro inicial e final devem ser informados.')
            }

            if(parametros.inicio > parametros.fim) {
                messageValidation.push('Registro inicial não pode ser maior que o final.')
            }
        }

        if (parametros.tipo_filtro == 3) {
            if (parametros.registros.length == 0) {
                messageValidation.push('Nenhum registro informado.')
            }
        }
    }

    if (messageValidation.length > 0) {

        loading.value = false;
        toast.add({
            severity: 'warn',
            summary: 'Alerta',
            detail: messageValidation.join('\n'),
            life: 5000
        });
        return;
    }

    try {
        loading.value = true
        await window.axios.post('v4/api/recursos-humanos/pessoal/ajudacusto/relatorio', parametros).then(response => {
            loading.value = false
            toast.add({
                severity: 'success',
                summary: 'Sucesso',
                detail: `Gerado com sucesso`,
                life: 5000
            });

            download.value.addFile(
                `${response.data.data.pathExterno}`,
                `${response.data.data.name}`
            );
            download.value.openModal();
        })

    } catch (e) {
        loading.value = false
        let message = e.response ? e.response.data.message : e.message;
        if (e.response.status === 422) {
            message = Object.values(e.response.data).join("\n")
        }
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: message,
            life: 5000
        });
    }
}


</script>
<template>
    <Toast position="center" />
    <section class="flex flex-column w-full mt-4 gap-2">
        <section class="flex justify-content-center">
            <Panel header="Relatório de Ajuda de Custo" class="w-full lg:w-6 xl:w-6 justify-content-center" toggleable>
                <div class="formgrid grid mt-2 justify-content-center">

                    <div class="field col-12 md:col-4 mt-2">
                        <span class="p-float-label">
                            <Calendar inputId="dataInicio" v-model="data.data_inicio" showIcon dateFormat="dd/mm/yy" required />
                            <label for="dataInicio">Data Inicio</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4 mt-2">
                        <span class="p-float-label">
                            <Calendar inputId="dateFim" v-model="data.data_fim"  showIcon dateFormat="dd/mm/yy" required />
                            <label for="dateFim">Data Fim</label>
                        </span>
                    </div>
                    <FiltrosFolha ref="filtroFolha"></FiltrosFolha>
                    <Panel header="Unidades de Ensino" class="w-11 mt-2" toggleable>
                        <div class="field col-12 md:col-12">
                            <div class="p-inputgroup flex-1">
                                <span class="p-float-label">
                                    <AutoComplete v-model="data.unidade.descricao" inputId="adicionar" disabled required />
                                    <span class="p-inputgroup-addon" @click="abrirConsultaCgm">
                                        <i class="pi pi-search"></i>
                                    </span>
                                    <Button type="button" class="ml-1" raised label="Adicionar" icon="pi pi-plus" @click="adicionarRegistro" />
                                </span>
                            </div>
                        </div>
                        <div class="field col w-full mt-2">
                            <DataTable :value="data.registros_unidade" scrollable scrollHeight="350px" tableStyle="min-width: 50rem;">
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
                    </Panel>
                    <div class="field col-12 md:col-11 mt-3 ">
                        <span class="p-float-label mt-3">
                            <Dropdown v-model="data.tipo" :options="tipoEmissao" optionLabel="name"
                                inputId="tipoEmissao"
                                placeholder="Tipo de Emissão" class="w-full" />
                            <label for="tipoEmissao">Tipo de emissão</label>
                        </span>
                    </div>
                    <div class="text-center mt-3">
                        <Button @click="gerarRelatorio()" icon="pi pi-print" label="Imprimir"
                            class="p-button-sm mt-3">
                        </Button>
                    </div>


                </div>
            </Panel>
        </section>
    </section>
    <DialogConsultaCgm ref="dialogConsultaCgm" @selectRow="selecionaCgm" />
    <ModalLoading :isLoading="loading" />
    <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
</template>
<style scoped>
.p-inputgroup-addon {
    cursor: pointer !important;
}
</style>
