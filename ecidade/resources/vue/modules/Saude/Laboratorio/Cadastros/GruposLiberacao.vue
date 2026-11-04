<script setup>
"use strict";
import {onMounted, ref} from "vue";
import {useToast} from 'primevue/usetoast';
import ConfirmDialog from "primevue/confirmdialog";
import {useConfirm} from 'primevue/useconfirm';
import {FilterMatchMode, FilterOperator} from 'primevue/api';
import ModalLoading from "../../../Components/ModalLoading.vue";

const toast = useToast();
const confirm = useConfirm();
const loadingPrincipal = ref(false);
const loadingButtonSalvar = ref(false);
const loadingButtonExcluir = ref(false);
const loadingButtonCancelar = ref(false);
const buttonSalvarVisible = ref(true);
const buttonExcluirVisible = ref(false);
const buttonCancelarVisible = ref(false);
const expandedRows = ref([]);

const props = defineProps({
    usuario: {type: String, required: true},
    departamento: {type: String, required: true}
});

const departValido = ref(true);
const criar = ref(true);
const deletarExame = ref(false);
const grupoSendoAtualizado = ref({});

const form = ref({
    laboratorio: {
        id: {
            valor: null,
            disabled: true,
            label: "Código",
        },
        descricao: {
            valor: null,
            disabled: true,
            label: "Laboratório",
        }
    },
    grupo: {
        id: {
            valor: null,
            disabled: true,
            label: "Código",
        },
        descricao: {
            valor: null,
            disabled: false,
            label: "Grupo",
        }
    },
    setor: {
        descricao: {
            disabled: false,
            label: "Setor",
        },
        selecionado: null,
        listaSetores: [],
        setoresFiltrados: null
    },
    exame: {
        descricao: {
            valor: null,
            disabled: true,
            label: "Exame",
        },
        selecionado: null,
        listaExames: [],
        examesFiltrados: null
    },
    showInputs: {
        setorExame: false
    }
});

const grupos = ref([]);
const grupoExames = ref({});
const filtrosTabela = ref({
    grupos: {
        'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
        'descricao': {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]
        },
    },
    grupoExames: {
        'global': {value: null, matchMode: FilterMatchMode.CONTAINS},
        'descricao': {
            operator: FilterOperator.AND,
            constraints: [{value: null, matchMode: FilterMatchMode.STARTS_WITH}]
        },
    }
});
const exibeTabelaGrupos = ref(true);

const routes = {
    pesquisarLaboratorio: `v4/api/saude/laboratorio/consulta/departamentos/${props.departamento}/laboratorios`,
    pesquisarGrupos: `v4/api/saude/laboratorio/consulta/grupos-liberacao/${props.usuario}`,
    salvarGrupo: `v4/api/saude/laboratorio/cadastro/grupos-liberacao`,
    atualizarGrupo: `v4/api/saude/laboratorio/cadastro/grupos-liberacao`
};

const pesquisaGrupos = async () => {
    try {
        if (departValido.value) {
            loadingPrincipal.value = true;
            const getGrupos = (await window.axios.get(routes.pesquisarGrupos)).data.data;

            grupos.value = [];

            if (getGrupos) {
                for (const grupo of getGrupos) {
                    if (form.value.setor.listaSetores.some(setor => setor.id === grupo.setor) || !grupo.setor) {
                        grupos.value.push(grupo);
                    }
                }
            }

            loadingPrincipal.value = false;
        }
    } catch (e) {
        loadingPrincipal.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const pesquisaLaboratorio = async () => {
    try {
        loadingPrincipal.value = true;
        const laboratorio = (await window.axios.get(routes.pesquisarLaboratorio)).data.data;

        if (!laboratorio) {
            loadingPrincipal.value = false;
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: "Departamento não vinculado a nenhum laboratório!",
                life: 5000
            });
            departValido.value = false;
            return departValido.value;
        }
        departValido.value = true;

        form.value.laboratorio.id.valor = laboratorio.id;
        form.value.laboratorio.descricao.valor = laboratorio.descricao;
        loadingPrincipal.value = false;
        return departValido.value;
    } catch (e) {
        loadingPrincipal.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const pesquisaLaboratorioSetores = async (grupoSelecionado = null) => {
    try {
        loadingPrincipal.value = true;
        const setores = (await window.axios.get(
            `v4/api/saude/laboratorio/consulta/laboratorio/${form.value.laboratorio.id.valor}/setores`
        )).data.data;

        form.value.setor.listaSetores = [];
        form.value.setor.listaSetores.push({
            id: '',
            idDescricao: 'Selecione'
        });

        for (const setor of setores) {
            form.value.setor.listaSetores.push({
                descricao: setor.descricao,
                id: setor.id,
                idDescricao: `${setor.id} - ${setor.descricao}`
            });
        }

        if (grupoSelecionado) await editandoGrupo(grupoSelecionado);
        loadingPrincipal.value = false;
    } catch (e) {

        loadingPrincipal.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: "Falha ao recuperar setores!",
            life: 5000
        });
    }
}

const pesquisaGrupoExames = async (grupoId) => {
    try {
        loadingPrincipal.value = true
        grupoExames.value = (await window.axios.get(
            `v4/api/saude/laboratorio/consulta/grupos-liberacao/${grupoId}/exames`
        )).data.data;

        exibeTabelaGrupos.value = false;
        loadingPrincipal.value = false;
    } catch (e) {
        loadingPrincipal.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: "Falha ao recuperar exames vínculados ao grupo!",
            life: 5000
        });
    }
}

const editarGrupo = async (data, limpar = true) => {
    grupoSendoAtualizado.value = data;
    criar.value = false;

    if (limpar) {
        limpaCampos();
    }
    buttonExcluirVisible.value = false;
    buttonCancelarVisible.value = true;
    buttonSalvarVisible.value = true;

    form.value.grupo.id.valor = data.id;
    form.value.grupo.descricao.valor = data.descricao;

    await pesquisaLaboratorioSetores(data);
    form.value.showInputs.setorExame = true;
    await pesquisaGrupoExames(data.id);
    form.value.setor.descricao.disabled = false;

    if (data.setor && grupoExames.value && grupoExames.value.length > 0) {
        form.value.setor.descricao.disabled = true;
    }
    scrollToTop();
}

const excluirExameEvent = (linhaExame) => {
    buttonSalvarVisible.value = false;
    buttonExcluirVisible.value = true;
    buttonCancelarVisible.value = true;
    deletarExame.value = true;

    let exameSelecionado = null;

    if (form.value.exame.listaExames) {
        for (const exame of form.value.exame.listaExames) {
            if (exame.id === linhaExame.id) {
                exameSelecionado = {
                    idDescricao: exame.idDescricao,
                    id: exame.id
                };
            }
        }
        form.value.exame.selecionado = exameSelecionado;
        form.value.exame.descricao.disabled = true;
    }
    scrollToTop();
}

const excluirGrupoEvent = (linhaGrupo) => {
    buttonSalvarVisible.value = false;
    buttonExcluirVisible.value = true;
    buttonCancelarVisible.value = true;

    form.value.grupo.id.valor = linhaGrupo.id;
    form.value.grupo.descricao.valor = linhaGrupo.descricao;
    scrollToTop();
}

const cancelar = () => {
    loadingButtonCancelar.value = true;

    setTimeout(() => {
        criar.value = true;
        deletarExame.value = false;
        limpaCampos();
    }, 1000);
    loadingButtonCancelar.value = false;
}

const criarOuAtualizar = async () => {
    if (departValido.value) {
        if (criar.value) {
            await salvarGrupo();
            return;
        }
        await atualizarGrupo();
        return;
    }

    toast.add({
        severity: 'error',
        summary: 'Erro!',
        detail: "Departamento não vinculado a nenhum laboratório!",
        life: 5000
    });
}

const salvarGrupo = async () => {
    try {
        let parametros = {};
        parametros.nomeGrupo = form.value.grupo.descricao.valor;
        parametros.usuario = props.usuario;

        loadingButtonSalvar.value = true;
        const response = (await window.axios.post(routes.salvarGrupo, parametros)).data;
        loadingButtonSalvar.value = false;
        if (!response.erro) {
            toast.add({
                severity: 'success',
                summary: 'Sucesso!',
                detail: response.message,
                life: 5000
            });
            limpaCampos();
            await pesquisaGrupos();
        }
    } catch (e) {
        loadingButtonSalvar.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro!',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const atualizarGrupo = async () => {
    try {
        let parametros = {};
        parametros.grupoId = grupoSendoAtualizado.value.id;
        parametros.nomeGrupo = form.value.grupo.descricao.valor;

        if (form.value.setor.selecionado) {
            parametros.setor = form.value.setor.selecionado.id;
        }

        loadingButtonSalvar.value = true;
        const response = (await window.axios.put(routes.atualizarGrupo, parametros)).data;
        loadingButtonSalvar.value = false;

        if (!response.erro) {
            toast.add({
                severity: 'success',
                summary: 'Sucesso!',
                detail: response.message,
                life: 5000
            });

            if (grupoSendoAtualizado.value.descricao !== form.value.grupo.descricao.valor) {
                grupoSendoAtualizado.value.descricao = form.value.grupo.descricao.valor;
            }

            if (form.value.setor.selecionado && form.value.setor.selecionado.id) {
                grupoSendoAtualizado.value.setor = form.value.setor.selecionado.id;
                await adicionarExameNoGrupo();
                await editarGrupo(grupoSendoAtualizado.value, false);
            }
        }
    } catch (e) {
        loadingButtonSalvar.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro!',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const adicionarExameNoGrupo = async () => {
    try {
        if (form.value.exame.selecionado) {
            loadingPrincipal.value = true;
            let parametros = {};
            parametros.grupo = grupoSendoAtualizado.value.id;
            parametros.exame = form.value.exame.selecionado.id;

            loadingButtonSalvar.value = true;
            const response = (await window.axios.post(`v4/api/saude/laboratorio/cadastro/grupos-liberacao/novo-exame`, parametros)).data;
            loadingButtonSalvar.value = false;

            if (!response.erro) {
                toast.add({
                    severity: 'success',
                    summary: 'Sucesso!',
                    detail: response.message,
                    life: 5000
                });
            }
            loadingPrincipal.value = false;
        }
    } catch (e) {
        loadingButtonSalvar.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro!',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const excluirExameDialog = async (event) => {

    confirm.require({
        target: event.currentTarget,
        message: 'Deseja mesmo excluir o exame do grupo?',
        header: 'EXCLUINDO EXAME!',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-info p-button-outlined',
        rejectIcon: 'pi pi-times-circle',
        acceptClass: 'p-button-danger',
        acceptIcon: 'pi pi-trash',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Excluir',
        accept: async () => {
            await excluirExame();
        }
    });
}

const excluirGrupoDialog = async (event) => {

    confirm.require({
        target: event.currentTarget,
        message: 'Deseja mesmo excluir o grupo de liberação?',
        header: 'EXCLUINDO GRUPO DE LIBERAÇÃO!',
        icon: 'pi pi-exclamation-triangle',
        rejectClass: 'p-button-info p-button-outlined',
        rejectIcon: 'pi pi-times-circle',
        acceptClass: 'p-button-danger',
        acceptIcon: 'pi pi-trash',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Excluir',
        accept: async () => {
           await excluirGrupo();
        }
    });
}

const excluirExame = async () => {
    try {
        loadingButtonExcluir.value = true;

        let response = (
            await window.axios.delete(
                `v4/api/saude/laboratorio/cadastro/grupos-liberacao/${grupoSendoAtualizado.value.id}/exame/${form.value.exame.selecionado.id}`
            )
        ).data;

        loadingButtonExcluir.value = false;

        if (!response.erro) {
            toast.add({
                severity: 'success',
                summary: 'Sucesso!',
                detail: response.message,
                life: 5000
            });

            await editarGrupo(grupoSendoAtualizado.value, false);
            form.value.exame.selecionado = null;
        }
        deletarExame.value = false;
    } catch (e) {
        loadingButtonExcluir.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro!',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

const excluirGrupo = async () => {
    try {
        loadingButtonExcluir.value = true;

        let response = (
            await window.axios.delete(
                `v4/api/saude/laboratorio/cadastro/grupos-liberacao/${form.value.grupo.id.valor}`
            )
        ).data;

        loadingButtonExcluir.value = false;

        if (!response.erro) {
            toast.add({
                severity: 'success',
                summary: 'Sucesso!',
                detail: response.message,
                life: 5000
            });
            limpaCampos();
            await pesquisaGrupos();
        }
    } catch (e) {
        loadingButtonExcluir.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro!',
            detail: e.response.data.message,
            life: 5000
        });
    }
}

async function excluirGrupoOuExame(event) {
    if (deletarExame.value) {
        await excluirExameDialog(event);
        return;
    }
    await excluirGrupoDialog(event);
}

const limpaCampos = () => {
    buttonCancelarVisible.value = false;
    buttonExcluirVisible.value = false;
    buttonSalvarVisible.value = true;

    form.value.grupo.id.valor = "";
    form.value.grupo.descricao.valor = "";
    form.value.setor.selecionado = null;
    form.value.showInputs.setorExame = false;
    form.value.exame.listaExames = [];
    exibeTabelaGrupos.value = true;
    form.value.exame.selecionado = null;
}

const scrollToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function listaSetores(event) {
    if (!event.query.trim().length) {
        form.value.setor.setoresFiltrados = [...form.value.setor.listaSetores];
        return;
    }

    form.value.setor.setoresFiltrados = form.value.setor.listaSetores.filter((setor) => {
        return setor.idDescricao.toLowerCase().includes(event.query.toLowerCase());
    });
}

function listaExames(event) {
    if (grupoExames.value) {
        let listagemExames = [];

        for (const exame of form.value.exame.listaExames) {
            let exameNoGrupo = false;

            for (const exameDoGrupo of grupoExames.value) {
                if (exame.id === exameDoGrupo.id) {
                    exameNoGrupo = true;
                }
            }

            if (!exameNoGrupo) {
                listagemExames.push(exame);
            }
        }
        form.value.exame.listaExames = listagemExames;
    }

    if (!event.query.trim().length) {
        form.value.exame.examesFiltrados = [...form.value.exame.listaExames];
        return;
    }

    form.value.exame.examesFiltrados = form.value.exame.listaExames.filter((setor) => {
        return setor.idDescricao.toLowerCase().includes(event.query.toLowerCase());
    });
}

async function buscarExames() {
    try {
        form.value.exame.descricao.disabled = true;
        form.value.exame.selecionado = null;

        if (form.value.setor.selecionado && form.value.setor.selecionado.id) {
            loadingPrincipal.value = true;
            const exames = (await window.axios.get(
                `v4/api/saude/laboratorio/consulta/laboratorio/${form.value.laboratorio.id.valor}/setores/${form.value.setor.selecionado.id}/exames`
            )).data.data;

            form.value.exame.listaExames = [];

            if (!exames) {
                loadingPrincipal.value = false;

                toast.add({
                    severity: 'info',
                    summary: 'Atenção',
                    detail: "Este laboratório não possui exames vínculados ao setor excolhido!",
                    life: 5000
                });
                return;
            }

            for (const exame of exames) {
                form.value.exame.listaExames.push({
                    descricao: exame.descricao,
                    id: exame.id,
                    idDescricao: `${exame.id} - ${exame.descricao}`
                });
            }
            form.value.exame.descricao.disabled = false;
            loadingPrincipal.value = false;
        }
    } catch (e) {

        loadingPrincipal.value = false;
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: "Falha ao recuperar exames!",
            life: 5000
        });
    }
}

async function editandoGrupo(grupoSelecionado) {
    if (grupoSelecionado.setor) {
        let grupoSetor = null;

        if (form.value.setor.listaSetores) {
            for (const setor of form.value.setor.listaSetores) {
                if (setor.id === grupoSelecionado.setor) {
                    grupoSetor = {
                        idDescricao: setor.idDescricao,
                        id: setor.id
                    };
                }
            }
            form.value.setor.selecionado = grupoSetor;
            await buscarExames();
        }
    }
}

onMounted(async () => {
    if (!await pesquisaLaboratorio()) {
        return;
    }
    await pesquisaLaboratorioSetores();
    await pesquisaGrupos();
});

</script>

<template>
    <Toast/>

    <ConfirmDialog/>

    <section class="container w-6">
        <Panel header="Grupo de Liberação de Resultados">
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2">
                    <label for="codigo"><b>{{ form.laboratorio.id.label }}</b></label>

                    <InputText
                        id="codigo"
                        :disabled="form.laboratorio.id.disabled"
                        v-model="form.laboratorio.id.valor"
                    />
                </div>

                <div class="field col-12 md:col-10">
                    <label for="descricao"><b>{{ form.laboratorio.descricao.label }}</b></label>

                    <InputText
                        id="descricao"
                        v-model="form.laboratorio.descricao.valor"
                        :disabled="form.laboratorio.descricao.disabled"
                    />
                </div>
            </div>

            <div class="p-fluid grid">
                <div class="field col-12 md:col-2">
                    <label for="codigoGrupo"><b>{{ form.grupo.id.label }}</b></label>

                    <InputText
                        id="codigoGrupo"
                        :disabled="form.grupo.id.disabled"
                        v-model="form.grupo.id.valor"
                    />
                </div>

                <div class="field col-12 md:col-10">
                    <label for="descricaoGrupo"><b>{{ form.grupo.descricao.label }}</b></label>

                    <InputText
                        id="descricaoGrupo"
                        v-model="form.grupo.descricao.valor"
                        :disabled="form.grupo.descricao.disabled"
                    />
                </div>
            </div>

            <div v-show="form.showInputs.setorExame" class="p-fluid grid">
                <div class="field col-12 md:col-10 w-full">
                    <label for="setorSelect"><b>{{ form.setor.descricao.label }}</b></label>

                    <AutoComplete class="w-full text-left p-inputtext-sm setorSelect"
                                  v-bind:disabled="form.setor.descricao.disabled"
                                  inputId="setorSelect"
                                  dropdown
                                  optionLabel="idDescricao"
                                  v-model="form.setor.selecionado"
                                  :suggestions="form.setor.setoresFiltrados"
                                  forceSelection
                                  @item-select="buscarExames()"
                                  @clear="buscarExames()"
                                  @complete="listaSetores($event)"/>
                </div>
            </div>

            <div v-show="form.showInputs.setorExame" class="p-fluid grid">
                <div class="field col-12 md:col-10 w-full">
                    <label for="descricaoExame"><b>{{ form.exame.descricao.label }}</b></label>

                    <AutoComplete class="w-full text-left p-inputtext-sm descricaoExame"
                                  inputId="descricaoExame"
                                  dropdown
                                  optionLabel="idDescricao"
                                  v-model="form.exame.selecionado"
                                  :suggestions="form.exame.examesFiltrados"
                                  forceSelection
                                  :disabled="form.exame.descricao.disabled"
                                  @complete="listaExames($event)"/>
                </div>
            </div>
        </Panel>

        <br>

        <div class="flex justify-content-center flex-wrap">
            <Button class="m-2" v-show="buttonCancelarVisible" icon="pi pi-times-circle" label="Voltar" outlined
                    :loading="loadingButtonCancelar" @click="cancelar()"/>

            <Button class="m-2" v-show="buttonSalvarVisible" icon="pi pi-save" label="Salvar"
                    :loading="loadingButtonSalvar" @click="criarOuAtualizar()"/>

            <Button class="m-2" v-show="buttonExcluirVisible" icon="pi pi-trash" label="Excluir"
                    :loading="loadingButtonExcluir" severity="danger" @click="excluirGrupoOuExame($event)"/>
        </div>

    </section>

    <section class="container">
        <DataTable
            v-if="exibeTabelaGrupos"
            :value="grupos"
            showGridlines
            :paginator="true"
            :rows="5"
            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink
            LastPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[5,10,15]"
            responsiveLayout="scroll"
            currentPageReportTemplate="Foram retornados {totalRecords} registros. Mostrando de {first} até {last}"
            v-model:expandedRows="expandedRows"
            v-model:filters="filtrosTabela.grupos"
            filterDisplay="menu"
            :globalFilterFields="[
                'descricao'
            ]"
            tableStyle="min-width: 50rem"
        >

            <template #paginatorend>
                <div class="flex justify-content-between">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search"/>
                        <InputText v-model="filtrosTabela.grupos['global'].value" placeholder="Pesquisar o registro"/>
                    </span>
                </div>
            </template>

            <template #empty>
                <h1 class="text-center font-medium">
                    Nenhum Registro Encontrado
                </h1>
            </template>

            <Column field="id" header="Código" class="text-lg"></Column>

            <Column field="descricao" header="Descrição" class="text-lg"></Column>

            <Column field="setor" header="Setor" class="text-lg"></Column>

            <Column field="acoes" header="Ações" class="text-lg">
                <template #body="{data}">
                    <Button
                        v-tooltip.top="{
                            value: 'Editar',
                            pt: {
                                arrow: {
                                    style: {
                                        borderTopColor: 'var(--primary-color)'
                                    }
                                },
                                text: 'bg-primary font-medium'
                            }
                        }"
                        icon="pi pi-pencil"
                        @click="editarGrupo(data)"/>

                    <Button
                        v-tooltip.top="{
                            value: 'Excluir',
                            pt: {
                                arrow: {
                                    style: {
                                        borderTopColor: 'var(--primary-color)'
                                    }
                                },
                                text: 'bg-primary font-medium'
                            }
                        }"
                        severity="danger"
                        icon="pi pi-trash round raised"
                        @click="excluirGrupoEvent(data)"/>
                </template>
            </Column>
        </DataTable>

        <DataTable
            v-else
            :value="grupoExames"
            showGridlines
            :paginator="true"
            :rows="5"
            paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink
            LastPageLink RowsPerPageDropdown"
            :rowsPerPageOptions="[5,10,15]"
            responsiveLayout="scroll"
            currentPageReportTemplate="Foram retornados {totalRecords} registros. Mostrando de {first} até {last}"
            v-model:expandedRows="expandedRows"
            v-model:filters="filtrosTabela.grupoExames"
            filterDisplay="menu"
            :globalFilterFields="[
                'descricao'
            ]"
            tableStyle="min-width: 50rem"
        >

            <template #paginatorend>
                <div class="flex justify-content-between">
                    <span class="p-input-icon-left">
                        <i class="pi pi-search"/>
                        <InputText v-model="filtrosTabela.grupoExames['global'].value"
                                   placeholder="Pesquisar o registro"/>
                    </span>
                </div>
            </template>

            <template #empty>
                <h1 class="text-center font-medium">
                    Nenhum Exame Cadastrado
                </h1>
            </template>

            <Column field="id" header="Código" class="text-lg"></Column>

            <Column field="descricao" header="Descrição" class="text-lg"></Column>

            <Column field="acoes" header="Ações" class="text-lg">
                <template #body="{data}">
                    <Button
                        v-tooltip.top="{
                            value: 'Excluir',
                            pt: {
                                arrow: {
                                    style: {
                                        borderTopColor: 'var(--primary-color)'
                                    }
                                },
                                text: 'bg-primary font-medium'
                            }
                        }"
                        severity="danger"
                        icon="pi pi-trash round raised"
                        @click="excluirExameEvent(data)"/>
                </template>
            </Column>
        </DataTable>
    </section>

    <ModalLoading :isLoading="loadingPrincipal"/>
</template>

<style scoped>
:deep(.setorSelect .p-inputtext:disabled) {
    color: black;
}

:deep(.descricaoExame .p-inputtext:disabled) {
    color: black;
}
</style>
