<script setup>
import { ref, onMounted } from "vue";
import { useToast } from "primevue/usetoast";
import { useConfirm } from "primevue/useconfirm";
import ConfirmDialog from "primevue/confirmdialog";
import ModalLoading from "../../../../Components/ModalLoading.vue";
import DialogPesquisaEducacao from "../../../Components/DialogPesquisaEducacao.vue";

const confirm = useConfirm();
const toast = useToast();
const funcPesquisa = ref();
const cursos = ref();
const renderDialog = ref(false);
const loading = ref(false)

const parametrosDialogPesquisa = ref({
    titulo: null,
    rota: null,
    camposExibir: null,
    callback: null
})

const routes = {
    cursos: `v4/api/educacao/secretaria/cursos/`,
    ensinos: `v4/api/educacao/secretaria/ensinos/`,
    cursosProf: `v4/api/educacao/censo/tabelas-censo/cursos-profissionalizantes/`,
    salvar: `v4/api/educacao/secretaria/cursos/salvar`,
    excluir: `v4/api/educacao/secretaria/cursos/excluir`
}

const form = ref({
    nomeCurso: {
        value: null,
        label: 'Nome do Curso',
        required: true,
        disabled: false
    },
    codigoCurso: {
        value: null,
        label: 'Codigo do Curso',
        required: false,
        disabled: true
    },
    nivelEnsino: {
        codigo: {
            value: null,
            label: 'Código',
            required: true,
            disabled: false
        },
        nome: {
            value: null,
            label: 'Nivel de Ensino',
            required: true,
            disabled: true
        },
        tipo: null
    },
    cursoProfissional: {
        codigo: {
            value: null,
            label: 'Código',
            required: true,
            disabled: false
        },
        nome: {
            value: null,
            label: 'Curso Profissionalizante',
            required: false,
            disabled: true
        }
    }
})

const checks = ref([])

const pesquisaNiveisEnsino = async () => {
    parametrosDialogPesquisa.value.titulo = 'Niveis de Ensino'
    parametrosDialogPesquisa.value.rota = routes.ensinos;
    parametrosDialogPesquisa.value.callback = retornoPesquisaEnsino;
    parametrosDialogPesquisa.value.camposExibir = [
        {
            field: 'codigo',
            dataType: 'int',
            label: 'Codigo',
            columnSize: 15
        },
        {
            field: 'descricao',
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

const openDialog = async () => {
    setTimeout(async () => {
        await funcPesquisa.value.open();
    }, 100)
}

const pesquisaCursos = async () => {
    parametrosDialogPesquisa.value.titulo = 'Cursos Profissionalizantes'
    parametrosDialogPesquisa.value.rota = routes.cursosProf;
    parametrosDialogPesquisa.value.callback = retornoPesquisaCursosProfiss;
    parametrosDialogPesquisa.value.camposExibir = [
        {
            field: 'ed247_i_codigo',
            dataType: 'int',
            label: 'Codigo',
            columnSize: 15
        },
        {
            field: 'ed247_c_descr',
            dataType: 'string',
            label: 'Descrição',
            columnSize: 15
        },
        {
            field: 'tipo',
            dataType: 'string',
            label: 'Tipo',
            columnSize: 15
        }
    ]
    renderDialog.value = true
    loading.value = true
    await openDialog()
    loading.value = false
}

const excluir = async (codigo) => {
    confirm.require({
        message: "Excluir este registro?",
        header: "Confirmação",
        icon: "pi pi-exclamation-triangle",
        accept: async () => {
            loading.value = true
            try {
                await window.axios.delete(`${routes.excluir}/${codigo}`)
                cursos.value = (await window.axios.get(routes.cursos)).data.data;
            } catch (e) {
                loading.value = false
                toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
            }
            loading.value = false
        },
    });
}

const editar = (data) => {
    checks.value.length = 0
    form.value.codigoCurso.value = data.codigo;
    form.value.nomeCurso.value = data.nome;
    form.value.cursoProfissional.nome.value = data.cursoProfissionalizante === null ? null : data.cursoProfissionalizante.ed247_c_descr;
    form.value.cursoProfissional.codigo.value =
        data.cursoProfissionalizante === null  ? null : data.cursoProfissionalizante.ed247_i_codigo;
    form.value.nivelEnsino.codigo.value = data.ensino.codigo
    form.value.nivelEnsino.nome.value = data.ensino.descricao.trim()
    form.value.nivelEnsino.tipo = data.ensino.tipoBncc
    form.value.nivelEnsino.codigo.disabled = true
    form.value.nivelEnsino.nome.disabled = true
    if (data.habilitaAprovParcial) {
        checks.value.push('aprovParcial');
    }
    if (data.incluiNoHistorico) {
        checks.value.push('incluiHistorico');
    }
    if (data.isAtivo) {
        checks.value.push('isAtivo');
    }
}

const salvar = async () => {
    let parametros = {}
    parametros.nomeCurso = form.value.nomeCurso.value
    parametros.nivelEnsino = form.value.nivelEnsino.codigo.value
    parametros.cursoProfissional = form.value.cursoProfissional.codigo.value
    parametros.incluiHistorico = checks.value.includes('incluiHistorico')
    parametros.aprovParcial = checks.value.includes('aprovParcial')
    parametros.isAtivo = checks.value.includes('isAtivo')

    if (form.value.codigoCurso.value !== null) {
        parametros.codigo = form.value.codigoCurso.value
    }

    try {
        loading.value = true
        await window.axios.post(`${routes.salvar}`, parametros)
        loading.value = false
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: `Salvo com sucesso1`,
            life: 5000
        });
        loading.value = true
        cursos.value = (await window.axios.get(routes.cursos)).data.data;
        loading.value = false
        limpaCampos();
    } catch (e) {
        loading.value = false
        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: `Falha ao salvar!`,
            life: 5000
        });
    }
}

const buscaCurso = async () => {
    let codigo = form.value.cursoProfissional.codigo.value;
    loading.value = true
    let curso = (await window.axios.get(`${routes.cursosProf}${codigo}`)).data.data;
    loading.value = false
    form.value.cursoProfissional.nome.value = curso.ed247_c_descr.trim()
}

const buscaEnsino = async () => {
    let codigo = form.value.nivelEnsino.codigo.value;
    loading.value = true
    let ensino = (await window.axios.get(`${routes.ensinos}${codigo}`)).data.data;
    loading.value = false
    form.value.nivelEnsino.nome.value = ensino.descricao
    form.value.nivelEnsino.tipo = ensino.tipoBncc
}

const retornoPesquisaEnsino = (e) => {
    form.value.nivelEnsino.codigo.value = e.codigo
    form.value.nivelEnsino.nome.value = e.descricao
    form.value.nivelEnsino.tipo = e.tipoBncc
}

const retornoPesquisaCursosProfiss = (e) => {
    form.value.cursoProfissional.codigo.value = e.ed247_i_codigo
    form.value.cursoProfissional.nome.value = e.ed247_c_descr
}

const limpaCampos = () => {
    form.value.codigoCurso.value = null
    form.value.nomeCurso.value = null
    form.value.nivelEnsino.codigo.value = null
    form.value.nivelEnsino.nome.value = null
    form.value.cursoProfissional.codigo.value = null
    form.value.cursoProfissional.nome.value = null
    checks.value.length = 0
}

onMounted(async () => {
    loading.value = true
    cursos.value = (await window.axios.get(routes.cursos)).data.data;
    loading.value = false
})
</script>

<template>
    <section class="container">
        <Panel header="Cadastro de Cursos" >
            <br/>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-2" v-show="form.nivelEnsino.tipo !== 3 && form.nivelEnsino.tipo !== 4">
                </div>
                <div class="field col-12 md:col-4">
                    <div class="p-inputgroup flex-1">
                         <span class="p-float-label" v-show="form.codigoCurso.value !== null">
                            <InputNumber inputId="codigoCurso" :useGrouping="false" @focusout="buscaCurso"
                                         style="width: 20px"
                                         v-model="form.codigoCurso.value"
                                         :required="form.codigoCurso.required"
                                         :disabled="form.codigoCurso.disabled"/>
                            <label for="">{{ form.cursoProfissional.codigo.label }}</label>
                        </span>
                        <span class="p-float-label">
                            <InputText
                                style="width: 248px"
                                id="nomeCurso"
                                v-model="form.nomeCurso.value"
                                :required="form.nomeCurso.required"
                                :disabled="form.nomeCurso.disabled"/>
                            <label for="">{{ form.nomeCurso.label }}</label>
                        </span>
                    </div>
                </div>
                <div class="field col-12 md:col-4">
                    <div class="p-inputgroup flex-1">
                             <span class="p-float-label">
                                <InputNumber inputId="nivelEnsinoCodigo" :useGrouping="false" @focusout="buscaEnsino"
                                             style="width: 30px"
                                             v-model="form.nivelEnsino.codigo.value"
                                             :disabled="form.nivelEnsino.codigo.disabled"
                                             :required="form.nivelEnsino.codigo.required"/>
                                <label for="">{{ form.nivelEnsino.codigo.label }}</label>
                            </span>
                        <span class="p-float-label">
                                <InputText id="nivelEnsinoNome" style="width: 200px"
                                           v-model="form.nivelEnsino.nome.value"
                                           :disabled="form.nivelEnsino.nome.disabled"
                                           :required="form.nivelEnsino.nome.required"/>
                                 <label for="">{{ form.nivelEnsino.nome.label }}</label>
                            </span>
                        <Button @click="pesquisaNiveisEnsino" icon="pi pi-search" aria-label="Filter" style="width: 200px"
                                :disabled="form.nivelEnsino.codigo.disabled"/>
                    </div>
                </div>
                <div class="field col-12 md:col-4" v-show="form.nivelEnsino.tipo === 3 || form.nivelEnsino.tipo === 4 ">
                    <div class="p-inputgroup flex-1">
                             <span class="p-float-label">
                                <InputNumber inputId="cursoProfissionaloCodigo" :useGrouping="false" @focusout="buscaCurso"
                                             style="width: 30px"
                                             v-model="form.cursoProfissional.codigo.value"
                                             :disabled="form.cursoProfissional.codigo.disabled"
                                             :required="form.cursoProfissional.codigo.required"/>
                                <label for="">{{ form.cursoProfissional.codigo.label }}</label>
                            </span>
                        <span class="p-float-label">
                                <InputText id="cursoProfissionalNome" style="width: 200px"
                                           v-model="form.cursoProfissional.nome.value"
                                           :disabled="form.cursoProfissional.nome.disabled"
                                           :required="form.cursoProfissional.nome.required"/>
                                 <label for="">{{ form.cursoProfissional.nome.label }}</label>
                            </span>
                        <Button @click="pesquisaCursos" icon="pi pi-search" aria-label="Filter" style="width: 200px"/>
                    </div>
                </div>
                <div class="field col-12 md:col-2" v-show="form.nivelEnsino.tipo !== 3 && form.nivelEnsino.tipo !== 4">
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-3">
                </div>
                <div class="field col-12 md:col-2">
                    <div class="field-checkbox">
                        <Checkbox inputId="incluiHistorico" name="incluiHistorico" value="incluiHistorico" v-model="checks" />
                        <label for="incluiHistorico">Incluir no Histórico</label>
                    </div>
                </div>
                <div class="field col-12 md:col-2">
                    <div class="field-checkbox">
                        <Checkbox inputId="aprovParcial" name="aprovParcial" value="aprovParcial" v-model="checks" />
                        <label for="aprovParcial">Aprovação Parcial</label>
                    </div>
                </div>
                <div class="field col-12 md:col-2">
                    <div class="field-checkbox">
                        <Checkbox inputId="isAtivo" name="isAtivo" value="isAtivo" v-model="checks" />
                        <label for="isAtivo">Ativo</label>
                    </div>
                </div>
                <div class="field col-12 md:col-3">
                </div>
            </div>
            <div class="card">
                <div class="flex justify-content-center flex-wrap">
                    <div class="flex align-items-center justify-content-center m-2">
                        <Button class="p-button" label="Salvar" icon="pi pi-save" style="width: 100px; margin: 0 auto;"
                                @click.prevent="salvar"
                                :disabled="form.nomeCurso.value === null ||
                                form.nivelEnsino.codigo.value === null
                                "></Button>
                    </div>
                    <div class="flex align-items-center justify-content-center m-2" v-if="form.codigoCurso.value !== null">
                        <Button class="p-button" label="Limpar" icon="pi pi-replay" style="width: 100px; margin: 0 auto;"
                                @click.prevent="limpaCampos"
                        ></Button>
                    </div>
                </div>
            </div>
        </Panel>
    </section>
        <br>
        <div class="card p-fluid">
            <DataTable :value="cursos"  style="width: 1200px;margin: 0 auto;" showGridlines paginator :rows="8">
                <Column field="codigo" header="Codigo" class="text-center"></Column>
                <Column field="nome" header="Nome" class="text-center"></Column>
                <Column field="ensino" header="Ensino" class="text-center">
                    <template #body="{ data, field }">
                        {{ data[field].descricao }}
                    </template>
                </Column>
                <Column field="incluiNoHistorico" header="Inclui no Histórico" class="text-center">
                    <template #body="{ data, field }">
                        <i :class="{'pi pi-check text-green-500': data[field], 'pi pi-times text-red-600': !data[field] }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="habilitaAprovParcial" header="Aprovação Parcial" class="text-center">
                    <template #body="{ data, field }">
                        <i :class="{'pi pi-check text-green-500': data[field], 'pi pi-times text-red-600': !data[field] }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="isAtivo" header="Ativo" class="text-center">
                    <template #body="{ data, field }">
                        <i :class="{'pi pi-check text-green-500': data[field], 'pi pi-times text-red-600': !data[field] }" style="font-size: 1.5rem;"></i>
                    </template>
                </Column>
                <Column field="cursoProfissionalizante" header="Curso Profissionalizante" class="text-center">
                    <template #body="{ data, field }">
                        {{ data[field] === null ? 'Não Informado' : data[field].ed247_c_descr }}
                    </template>
                </Column>
                <Column>
                    <template #body="{ data }">
                        <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editar(data)"></i>
                        <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluir(data['codigo'])"></i>
                    </template>
                </Column>
            </DataTable>
        </div>


        <DialogPesquisaEducacao  v-if="renderDialog" ref="funcPesquisa"
                                v-on:select-line="parametrosDialogPesquisa.callback"
                                :title='parametrosDialogPesquisa.titulo'
                                :route='parametrosDialogPesquisa.rota'
                                :fields-display="parametrosDialogPesquisa.camposExibir"></DialogPesquisaEducacao>

    <ConfirmDialog/>
    <ModalLoading :isLoading="loading"/>
</template>

<style lang="scss" scoped>
    label {
        color: #605e5c;
    }
    i {
        cursor: pointer
    }
    ::v-deep(.editable-cells-table td.p-cell-editing) {
        padding-top: 0.6rem;
        padding-bottom: 0.6rem;
    }
</style>
