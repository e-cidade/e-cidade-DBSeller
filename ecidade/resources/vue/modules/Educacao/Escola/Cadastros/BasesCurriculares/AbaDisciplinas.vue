<script setup>
import { ref, onMounted } from "vue";
import { useToast } from 'primevue/usetoast';
import { useConfirm } from "primevue/useconfirm";
import DialogPesquisaEducacao from "../../../Components/DialogPesquisaEducacao.vue";
import ModalLoading from "../../../../Components/ModalLoading.vue";
import ConfirmDialog from "primevue/confirmdialog";

const toast = useToast();
const confirm = useConfirm();
const exibeModalUnidades = ref(false)
const exibeModalReplica = ref(false)
const props = defineProps(['base'])
const routes = {
    etapas: `v4/api/educacao/escola/etapas/etapas-intervalo`,
    tiposBase: `v4/api/educacao/secretaria/tipo-base/buscarTodos`,
    unidadesCurriculares: `v4/api/educacao/censo/tabelas-censo/unidades-curriculares`,
    disciplinas: props.base !== null ? `v4/api/educacao/secretaria/bases-curriculares/${props.base.codigo}/etapas/{etapa}/disciplinas` : '',
    diciplinasBase: props.base !== null ? `v4/api/educacao/escola/bases-curriculares/${props.base.codigo}/etapas/{etapa}/disciplinas`: '',
    salvarDiciplinaBase: `v4/api/educacao/escola/bases-curriculares/disciplinas/salvar`,
    excluirDiciplinaBase: `v4/api/educacao/escola/bases-curriculares/disciplinas/{codigo}/excluir`
}
const renderDialog = ref(false)
const disabledLancaDoc = ref(false)
const parametrosDialogPesquisa = ref({
    titulo: null,
    rota: null,
    camposExibir: null,
    callback: null
})
const funcPesquisa = ref()
const loading = ref(false)
const etapas = ref()
const unidadesCurriculares = ref();
const optionsTiposBase = ref();
const optionsMatricula = ref([
    {
        name: 'Obrigatória',
        code: 'OB'
    },
    {
        name: 'Opcional',
        code: 'OP'
    }
])
const form = ref({
    codigo: null,
    disciplina: {
        disciplinaEnsino: null,
        codigo: {
            value: null,
            disabled: false,
            requiered: true,
            label: 'Código'
        },
        nome: {
            value: null,
            disabled: true,
            required: true,
            label: 'Disciplina'
        }
    },
    areaConhecimento: {
        codigo: {
            value: null,
            disabled: true,
            requiered: true,
            label: 'Código'
        },
        nome: {
            value: null,
            disabled: true,
            required: true,
            label: 'Área de Conhecimento'
        }
    },
    slctdTipoBase: {
        data: null,
        disabled: false,
        required: true,
        label: 'Tipo de Base'
    },
    slctdMatricula: {
        data: null,
        disabled: false,
        required: true,
        label: 'Matrícula'
    },
    horasAula: {
        value: null,
        disabled: false,
        required: true,
        label: 'Horas Aula'
    },
    checks: [],
    etapasReplicar: {
        etapasExibir: [],
        disciplina: null,
        etapas: []
    },
    unidadeCurricular: null,
    ordenacao: null
})
const buscaEtapas = async () => {
    if (props.base !== null) {
        let parametros = {};
        parametros.inicial = props.base.etapaInicial.codigo
        parametros.final = props.base.etapaFinal.codigo
        try {
            loading.value = true
            etapas.value = (await window.axios.post(routes.etapas, parametros)).data.data;
            for (const etapa of etapas.value) {
                etapa.disciplinas = await buscaDisciplinasEtapa(etapa.codigo)
            }
            loading.value = false
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: e.response.data.message,
                life: 5000
            });
        }
    }
}

const buscaDisciplinasEtapa = async (etapa) => {
    if (props.base !== null) {
        let rota = routes.diciplinasBase.replace('{etapa}', etapa);
        let disciplinas = (await window.axios.get(rota)).data.data
        return disciplinas;
    }
}

const buscaUnidadesCurriculares = async () => {
    if (props.base !== null) {
        try {
            loading.value = true
            unidadesCurriculares.value = (await window.axios.get(routes.unidadesCurriculares)).data.data;
            loading.value = false
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: e.response.data.message,
                life: 5000
            });
        }
    }
}

const buscaTipoBase = async () => {
    if (props.base !== null) {
        try {
            loading.value = true
            optionsTiposBase.value = (await window.axios.get(routes.tiposBase)).data.data.map(tipo => {
                return {
                    name: tipo.ed182_descricao,
                    code: tipo.ed182_id,
                    isEnsinoMedio: tipo.ed182_estrutura_curricular.id === 1
                }
            })
            loading.value = false
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: e.response.data.message,
                life: 5000
            });
        }
    }
}

const retornoPesquisaDisciplinas = async (e) => {
    form.value.disciplina.codigo.value = e.codigo
    form.value.disciplina.disciplinaEnsino = e.disciplinaEnsino
    form.value.disciplina.nome.value = e.nome
    form.value.areaConhecimento.codigo.value = e.areaConhecimento.codigo
    form.value.areaConhecimento.nome.value = e.areaConhecimento.nome
}
const pesquisaDisciplinas = async (etapa) => {
    parametrosDialogPesquisa.value.titulo = 'Disciplinas'
    let rota = routes.disciplinas.replace('{etapa}', etapa.codigo);
    parametrosDialogPesquisa.value.rota = rota;
    parametrosDialogPesquisa.value.callback = retornoPesquisaDisciplinas;
    parametrosDialogPesquisa.value.camposExibir = [
        {
            field: 'codigo',
            dataType: 'int',
            label: 'Codigo',
            columnSize: 15
        },
        {
            field: 'nome',
            dataType: 'string',
            label: 'Descrição',
            columnSize: 15
        },
        {
            field: 'abreviatura',
            dataType: 'string',
            label: 'Abreviatura',
            columnSize: 15
        }
    ]
    renderDialog.value = true
    loading.value = true
    await openDialog()
    loading.value = false
}

const buscaDisciplina = async () => {
    let codigo = form.value.disciplina.codigo.value;
    form.value.disciplina.codigo.value = null
    form.value.disciplina.nome.value = null
    form.value.areaConhecimento.codigo.value = null
    form.value.areaConhecimento.nome.value = null
    form.value.disciplina.disciplinaEnsino = null
    if (codigo !== '' && codigo !== null) {
        try {
            loading.value = true
            let disciplina = (await window.axios.get(`${routes.disciplinas}/${codigo}`)).data.data;
            loading.value = false
            form.value.disciplina.codigo.value = disciplina.codigo
            form.value.disciplina.nome.value = disciplina.nome
            form.value.disciplina.disciplinaEnsino = disciplina.disciplinaEnsino
            form.value.areaConhecimento.codigo.value = disciplina.areaConhecimento.codigo
            form.value.areaConhecimento.nome.value = disciplina.areaConhecimento.nome
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: e.response.data.message,
                life: 5000
            });
        }
    }
}
const openDialog = async () => {
    setTimeout(async () => {
        loading.value = true
        await funcPesquisa.value.open();
        loading.value = false
    }, 100)
}
 const verificaExibeModalUnidades = () => {
    if (form.value.slctdTipoBase.data.isEnsinoMedio) {
        exibeModalUnidades.value = true
    } else {
        exibeModalUnidades.value = false
        form.value.unidadeCurricular = null
    }
 }
const limpaForm = () => {
    form.value.codigo = null
    form.value.disciplina.nome.value = null
    form.value.disciplina.codigo.value = null
    form.value.disciplina.disciplinaEnsino = null
    form.value.areaConhecimento.codigo.value = null
    form.value.areaConhecimento.nome.value = null
    form.value.slctdMatricula.data = null
    form.value.slctdTipoBase.data = null
    form.value.checks.length = 0
    form.value.unidadeCurricular = null
    form.value.horasAula.value = null
    form.value.ordenacao = null
}

 const verificaLancamentoDocumentacao = async () => {
     disabledLancaDoc.value = false
    if (form.value.slctdMatricula.data.code == 'OB') {
        form.value.checks.push('lancarDocumentacao')
        disabledLancaDoc.value = true
    }
 }

const verificaParametros = async (etapa) => {
    let parametros = {}

    if (form.value.codigo != null) {
        parametros.codigo = form.value.codigo
    }
    if (form.value.ordenacao != null) {
        parametros.ordenacao = form.value.ordenacao
    }

    parametros.base = props.base.codigo
    parametros.serie = etapa
    parametros.disciplina = form.value.disciplina.codigo.value
    parametros.disciplinaEnsino = form.value.disciplina.disciplinaEnsino
    parametros.areaConhecimento = form.value.areaConhecimento.codigo.value
    parametros.tipoBase = form.value.slctdTipoBase.data.code
    parametros.matricula = form.value.slctdMatricula.data.code
    parametros.horasAula = form.value.horasAula.value
    parametros.isGlobalizada = form.value.checks.includes('disciplinaGlobal') ? true : false
    parametros.isCaraterReprobatorio = form.value.checks.includes('caraterReprobatorio') ? true : false
    parametros.lancarHistorico = form.value.checks.includes('lancarDocumentacao') ? true : false
    parametros.unidadeCurricular = form.value.unidadeCurricular

    const response = await salvar(parametros)
    confirm.require({
        message: "Deseja replicar essa disciplina para outras etapas?",
        header: "Confirmação",
        icon: "pi pi-exclamation-triangle",
        accept: async () => {
            form.value.etapasReplicar.disciplina = response
            form.value.etapasReplicar.etapasExibir = etapas.value.filter(etp => etp.codigo != etapa)
            exibeModalReplica.value = true
        },
        reject: async () => {
            atualiza()
        }
    });
}

const atualiza = async () => {
    await buscaEtapas()
    limpaForm()
}
const onRowReorder = async (event) => {
    let disciplinas = {}
    disciplinas.lote = event.value.map((disciplina, key) => {
        disciplina.ordenacao = key + 1;
        disciplina.disciplina = disciplina.disciplina.codigo
        disciplina.areaConhecimento = disciplina.areaConhecimento.codigo
        disciplina.tipoBase = disciplina.tipoBase.codigo
        disciplina.matricula = disciplina.matricula.codigo

        return disciplina
    })
    await salvar(disciplinas);
    atualiza()
};
const salvar = async (parametros) => {
    var retorno = {}
    try {
        loading.value = true
        retorno = (await window.axios.post(`${routes.salvarDiciplinaBase}`, parametros)).data.data
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Sucesso ao Salvar`,
            life: 5000
        });
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: e.response.data.message,
            life: 5000
        });
    }
    return retorno
}

const editar = async (dados) => {
    form.value.checks.length = 0
    form.value.codigo = dados.codigo
    form.value.disciplina.codigo.value = dados.disciplina.codigo
    form.value.disciplina.nome.value = dados.disciplina.nome
    form.value.disciplina.disciplinaEnsino = dados.disciplinaEnsino
    form.value.areaConhecimento.codigo.value = dados.areaConhecimento.codigo
    form.value.areaConhecimento.nome.value = dados.areaConhecimento.nome
    let optionTipobase = optionsTiposBase.value.filter(opt => opt.code === dados.tipoBase.codigo).shift()
    form.value.slctdTipoBase.data = optionTipobase
    let optionMatricula = optionsMatricula.value.filter(opt => opt.code === dados.matricula.codigo).shift()
    form.value.slctdMatricula.data = optionMatricula;
    verificaLancamentoDocumentacao();
    form.value.horasAula.value = dados.horasAula
    if(dados.isGlobalizada) {
        form.value.checks.push('disciplinaGlobal')
    }
    if(dados.isCaraterReprobatorio) {
        form.value.checks.push('caraterReprobatorio')
    }
    if(dados.lancarHistorico) {
        form.value.checks.push('lancarDocumentacao')
    }
    form.value.unidadeCurricular = dados.unidadeCurricular.codigo
    form.value.ordenacao = dados.ordenacao
}
const excluir = async (codigo) => {
    confirm.require({
        message: "Excluir este registro?",
        header: "Confirmação",
        icon: "pi pi-exclamation-triangle",
        accept: async () => {
            loading.value = true
            try {
                let rota = routes.excluirDiciplinaBase.replace('{codigo}', codigo);
                await window.axios.delete(rota)
                await buscaEtapas()
            } catch (e) {
                loading.value = false
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: e.response.data.message,
                    life: 5000
                });
            }
            loading.value = false
        },
    });
}

const replicaEtapas = async () => {
    let parametros = {}
    parametros.lote = [];
    form.value.etapasReplicar.etapas.forEach(etapa => {
        let disciplina = {}
        if (etapa != form.value.etapasReplicar.disciplina.serie) {
            disciplina.base = props.base.codigo
            disciplina.serie = etapa
            disciplina.disciplina = form.value.etapasReplicar.disciplina.disciplina.codigo
            disciplina.disciplinaEnsino = form.value.etapasReplicar.disciplina.disciplinaEnsino
            disciplina.areaConhecimento = form.value.etapasReplicar.disciplina.areaConhecimento.codigo
            disciplina.tipoBase = form.value.etapasReplicar.disciplina.tipoBase.codigo
            disciplina.matricula = form.value.etapasReplicar.disciplina.matricula.codigo
            disciplina.horasAula = form.value.etapasReplicar.disciplina.horasAula
            disciplina.isGlobalizada = form.value.etapasReplicar.disciplina.isGlobalizada
            disciplina.isCaraterReprobatorio = form.value.etapasReplicar.disciplina.isCaraterReprobatorio
            disciplina.lancarHistorico = form.value.etapasReplicar.disciplina.lancarHistorico
            disciplina.unidadeCurricular = form.value.etapasReplicar.disciplina.unidadeCurricular
        }
        parametros.lote.push(disciplina)
    })

    await salvar(parametros);
    exibeModalReplica.value = false
    atualiza()
}

onMounted(async () => {
    await buscaEtapas()
    await buscaTipoBase()
    await buscaUnidadesCurriculares();
})
</script>
<template>
    <TabView class="tabview-custom" @tabChange="limpaForm">
        <ConfirmDialog/>
        <TabPanel v-for="(etapa, key) in etapas">
            <template #header>
                <i class="pi pi-cog mr-2"></i>
                <span>{{ etapa.nome }}</span>
            </template>
            <br>
            <div style="height: 550px">
                <div style="width: 830px; margin: 0 auto">
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-6 ">
                            <div class="p-inputgroup flex-1">
                                 <span class="p-float-label pr-2">
                                    <InputNumber inputId="codigoDisciplina" :useGrouping="false" @focusout="buscaDisciplina"
                                                 style="width: 20px"
                                                 v-model="form.disciplina.codigo.value"
                                                 :required="form.disciplina.codigo.required"/>
                                    <label for="">{{ form.disciplina.codigo.label }}</label>
                                </span>
                                <span class="p-float-label">
                                    <InputText
                                        style="width: 300px"
                                        id="nomeDisciplina"
                                        v-model="form.disciplina.nome.value"
                                        :required="form.disciplina.nome.required"
                                        :disabled="form.disciplina.nome.disabled"/>
                                    <label for="">{{ form.disciplina.nome.label }}</label>
                                </span>
                                <Button @click="pesquisaDisciplinas(etapa)" icon="pi pi-search" aria-label="Filter" style="width: 150px"/>
                            </div>
                        </div>
                        <div class="field col-12 md:col-6">
                            <span class="p-float-label">
                                <InputText
                                    id="nomeArea"
                                    v-model="form.areaConhecimento.nome.value"
                                    :required="form.areaConhecimento.nome.required"
                                    :disabled="form.areaConhecimento.nome.disabled"/>
                                <label for="">{{ form.areaConhecimento.nome.label }}</label>
                            </span>
                        </div>
                    </div>
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-4">
                            <div class="p-inputgroup flex-1">
                                <span class="p-float-label">
                                    <Dropdown id="tipoBase" :options="optionsTiposBase" @change="verificaExibeModalUnidades"
                                              optionLabel="name"
                                              v-model="form.slctdTipoBase.data"
                                              :disabled="form.slctdTipoBase.disabled"/>
                                     <label for="">{{ form.slctdTipoBase.label }}</label>
                                </span>
                                <Button v-if="form.slctdTipoBase.data !== null && form.slctdTipoBase.data.isEnsinoMedio" @click="verificaExibeModalUnidades" icon="pi pi-window-maximize" aria-label="Filter" style="width: 40px"/>
                            </div>
                        </div>
                        <div class="field col-12 md:col-4">
                            <span class="p-float-label">
                                <Dropdown id="matricula" :options="optionsMatricula" @change="verificaLancamentoDocumentacao"
                                          optionLabel="name"
                                          v-model="form.slctdMatricula.data"
                                          :disabled="form.slctdMatricula.disabled"/>
                                 <label for="">{{ form.slctdMatricula.label }}</label>
                            </span>
                        </div>
                        <div class="field col-12 md:col-4">
                            <span class="p-float-label">
                                <InputNumber id="hoarasAula"
                                          v-model="form.horasAula.value"
                                          :disabled="form.horasAula.disabled"/>
                                 <label for="">{{ form.horasAula.label }}</label>
                            </span>
                        </div>
                    </div>
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-4" v-if="props.base.controleFrequencia === 'G'">
                            <div class="field-checkbox">
                                <Checkbox inputId="disciplinaGlobal" name="disciplinaGlobal" value="disciplinaGlobal" v-model="form.checks"/>
                                <label for="disciplinaGlobal">Disciplina Global</label>
                            </div>
                        </div>
                        <div class="field col-12 md:col-4">
                            <div class="field-checkbox">
                                <Checkbox inputId="caraterReprobatorio" name="caraterReprobatorio" value="caraterReprobatorio" v-model="form.checks"/>
                                <label for="caraterReprobatorio">Possui Caráter Reprobatório</label>
                            </div>
                        </div>
                        <div class="field col-12 md:col-4">
                            <div class="field-checkbox">
                                <Checkbox :disabled="disabledLancaDoc" inputId="lancarDocumentacao" name="lancarDocumentacao" value="lancarDocumentacao" v-model="form.checks"/>
                                <label for="lancarDocumentacao">Lançar na Documentação</label>
                            </div>
                        </div>
                    </div>
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-4 col-offset-4">
                            <Button class="p-button" label="Salvar"
                                :disabled="form.disciplina.codigo.value === null || form.slctdTipoBase.data === null || form.slctdMatricula.data === null || form.horasAula.value == null"
                                    @click="verificaParametros(etapa.codigo)"
                            ></Button>
                        </div>
                    </div>
                </div>
                <div style="width: 90%; margin: 0 auto">
                    <div class="p-fluid grid">
                        <DataTable :value="etapa.disciplinas" scrollable scrollHeight="300px" showGridlines @rowReorder="onRowReorder" style="width: 100%">
                            <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" header="Ordenar"/>
                            <Column field="tipoBase" header="Tipo de Base">
                                <template #body="slotProps">
                                    {{ slotProps.data.tipoBase.nome }}
                                </template>
                            </Column>
                            <Column field="areaConhecimento" header="Área de Conhecimento">
                                <template #body="slotProps">
                                    {{ slotProps.data.areaConhecimento.nome }}
                                </template>
                            </Column>
                            <Column field="disciplina" header="Disciplina">
                                <template #body="slotProps">
                                    {{ slotProps.data.disciplina.nome }}
                                </template>
                            </Column>
                            <Column field="isGlobalizada" class="text-center" header="Disciplina Global">
                                <template #body="slotProps">
                                    <i :class="{'pi pi-check text-green-500': slotProps.data.isGlobalizada, 'pi pi-times text-red-600': !slotProps.data.isGlobalizada }" style="font-size: 1.5rem;"></i>
                                </template>
                            </Column>
                            <Column field="isCaraterReprobatorio" class="text-center" header="Possui Caráter Reprobatório">
                                <template #body="slotProps">
                                    <i :class="{'pi pi-check text-green-500': slotProps.data.isCaraterReprobatorio, 'pi pi-times text-red-600': !slotProps.data.isCaraterReprobatorio }" style="font-size: 1.5rem;"></i>
                                </template>
                            </Column>
                            <Column field="lancarHistorico" class="text-center" header="Lançar na Documentação">
                                <template #body="slotProps">
                                    <i :class="{'pi pi-check text-green-500': slotProps.data.lancarHistorico, 'pi pi-times text-red-600': !slotProps.data.lancarHistorico }" style="font-size: 1.5rem;"></i>
                                </template>
                            </Column>
                            <Column field="horasAula" header="Horas Aula"></Column>
                            <Column field="matricula" header="Matrícula">
                                <template #body="slotProps">
                                    {{ slotProps.data.matricula.nome }}
                                </template>
                            </Column>
                            <Column header="Ações">
                                <template #body="{ data }">
                                    <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)"></i>
                                    <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(data['codigo'])"></i>
                                </template>
                            </Column>
                        </DataTable>
                    </div>
                </div>
            </div>
        </TabPanel>
    </TabView>
    <Dialog header="Unidades Curriculares" :closable="form.unidadeCurricular !== null" v-model:visible="exibeModalUnidades" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '40vw'}">
        <br><br>
        <div class="grid">
            <div class="col-4" v-for="unidade in unidadesCurriculares">
                <div class="flex align-items-center">
                    <RadioButton :inputId="`${unidade.codigo}`" :name="`${unidade.codigo}`" :value="unidade.codigo" v-model="form.unidadeCurricular"/>
                    <label :for="unidade.codigo" class="ml-2">{{ unidade.nome }}</label>
                </div>
            </div>
        </div>
    </Dialog>

    <DialogPesquisaEducacao  v-if="renderDialog" ref="funcPesquisa"
                             v-on:select-line="parametrosDialogPesquisa.callback"
                             :title='parametrosDialogPesquisa.titulo'
                             :route='parametrosDialogPesquisa.rota'
                             :fields-display="parametrosDialogPesquisa.camposExibir"></DialogPesquisaEducacao>

    <ModalLoading :isLoading="loading"/>

    <Dialog header="Etapas" v-model:visible="exibeModalReplica" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '40vw'}" @hide="atualiza">
        <br><br>
        <div class="grid">
            <div class="col-4" v-for="etapa in form.etapasReplicar.etapasExibir">
                <div class="flex align-items-center">
                    <Checkbox :inputId="`${etapa.codigo}`" :name="`${etapa.codigo}`" :value="etapa.codigo" v-model="form.etapasReplicar.etapas"/>
                    <label :for="etapa.codigo" class="ml-2">{{ etapa.nome }}</label>
                </div>
            </div>
        </div>
        <div class="text-center">
            <Button class="p-button" label="Replicar"
                    :disabled="form.etapasReplicar.etapas.length === 0"
                    @click="replicaEtapas"
            ></Button>
        </div>
    </Dialog>

</template>
<style scoped>
:deep(.p-tabview-nav) {
    background: none;
}

i {
    cursor: pointer
}

</style>
