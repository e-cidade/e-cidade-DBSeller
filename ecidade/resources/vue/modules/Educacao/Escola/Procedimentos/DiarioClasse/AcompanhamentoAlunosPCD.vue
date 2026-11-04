<script setup>

    import { ref, reactive } from "vue";
    import { useToast } from "primevue/usetoast"
    import ModalLoading from "../../../../Components/ModalLoading.vue";
    import { useConfirm } from "primevue/useconfirm";
    import ConfirmDialog from "primevue/confirmdialog";

    const props = defineProps(['escola', 'modulo'])
    const confirm = useConfirm();
    const isSecretaria = ref(null)
    isSecretaria.value = props.modulo == 7159

    const routes = {
        calendarios: `v4/api/educacao/escola/calendarioaee`,
        escolas: `v4/api/educacao/escola/`,
        turmas: `v4/api/educacao/escola/turmasEspeciaisaee`,
        alunos: `v4/api/educacao/escola/alunos/alunos-por-turma-especial`,
        aluno: `v4/api/educacao/escola/alunos/detalhes-aluno-especial`,
        recursos: 'v4/api/educacao/escola/alunoAtendimentosEspecial/recursos',
        saveAtendimento: `v4/api/educacao/escola/alunoAtendimentosEspecial/save`,
        buscarRecursos: `v4/api/educacao/escola/alunoAtendimentosEspecial/busca-recursos`,
        atendimentos: `v4/api/educacao/escola/alunoAtendimentosEspecial/atendimentos`,
        excluir: `v4/api/educacao/escola/alunoAtendimentosEspecial/excluir`,
        atualizar: `v4/api/educacao/escola/alunoAtendimentosEspecial/atualizar`,
        deficiencias : `v4/api/educacao/escola/alunos/deficiencias`,
        atendimentosTipos: `v4/api/educacao/escola/tiposAtendimentos`
    }

    const form = ref({
        slctEscolas: {
            data: null,
            label: 'Escolas',
            required: true,
            show: false
        },
        slctCalendarios: {
            data: null,
            label: 'Calendários',
            required: true
        },
        slctTurmas: {
            data: null,
            label: 'Turmas',
            required: true
        },
        slctAlunos: {
            data: null,
            label: 'Alunos',
            required: true
        },
        codigo: {
            data: null,
            label: 'Código',
            required: true
        },
        dataEmissao: {
            data: null,
            label: 'Data de Registro',
            required: true
        },
        slctAtendimentos: {
            label: 'Atendimentos',
            data: null,
            required: true

        },
        slctRecursos:{
            label: 'Recursos Utilizados',
            data: null,
            required: true
        },
        textAreaParecer: {
            label: 'Parecer',
            data: null
        },
        checks: null,
        alunos: [[],[]]
    })

    const loading = ref(false)
    const optionsCalendarios = ref(null)
    const optionsTurmas = ref(null)
    const optionsAlunos = ref(null)
    const expandedRows = ref([])
    const optionsDados = ref([])
    const optionsDadosDeficiencia = ref([])
    const optionsAtendimentos = ref([])
    const optionsTodosRecursos = ref([
        {
            descricao: null,
            codigo: null
        }
    ])
    const optionsRecursos = ref([null])
    const optionsAtendimentosAluno = reactive({
        value: []
    })
    const toast = useToast()
    const showInfo = ref(false)
    const showSalvar = ref(true)
    const optionsEscolas = ref([])
    const optionsSecretarios = ref([{
        name: 'Selecione',
        code: null
    }])

    let tiposAtendimentos = []


    function buscaCalendarios() {
        try {
            form.value.textAreaParecer.data = []
            form.value.slctAtendimentos.data = []
            form.value.dataEmissao.data = null
            form.value.slctRecursos.data = []
            form.value.slctCalendarios.data = []
            form.value.slctTurmas.data = []
            form.value.slctAlunos.data = []
            optionsAtendimentosAluno.value = []
            showInfo.value = false
            loading.value = false
            loading.value = true
            let escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code

            window.axios.get(`${routes.calendarios}/${escola}`).then(retorno => {
                optionsCalendarios.value = retorno.data.data.map(calendario => {
                    return {
                        name: calendario.descricao,
                        code: calendario.id
                    }
                })
                loading.value = false
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    function buscaEscolas() {
        try {
            loading.value = true
            window.axios.get(routes.escolas).then(retorno => {
                optionsEscolas.value = retorno.data.data.map(escola => {
                    return {
                        name: escola.ed18_c_nome,
                        code: escola.ed18_i_codigo
                    }
                })
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    function buscarRecursos() {

        try {
            loading.value = true
            window.axios.get(routes.buscarRecursos).then(retorno => {
                optionsTodosRecursos.value = retorno.data.data.map(recurso => {
                    return {
                        codigo: recurso.ed198_codigo,
                        descricao: recurso.ed198_descricao
                    }
                })
                loading.value = false
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    function buscaTurmas() {
        form.value.textAreaParecer.data = []
        form.value.slctAtendimentos.data = []
        form.value.dataEmissao.data = null
        form.value.slctRecursos.data = []
        form.value.slctTurmas.data = []
        form.value.slctAlunos.data = []
        optionsAtendimentosAluno.value = []
        showInfo.value = false

        try {
            loading.value = true
            let parametros = {};
            parametros.calendario = form.value.slctCalendarios.data.code;
            parametros.escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code;
            window.axios.post(`${routes.turmas}`, parametros).then(retorno => {
                optionsTurmas.value = retorno.data.data.map(turma => {
                    return {
                        name: turma.descricao,
                        code: turma.id,
                        atendimentos: turma.atendimentos
                    }
                })
                loading.value = false
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    async function buscaTiposAtendimentos() {

        try {
            loading.value = true

            tiposAtendimentos = (await window.axios.get(`${routes.atendimentosTipos}`)).data.data
            loading.value = false

        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }

    }

    function buscaAlunos() {

        form.value.textAreaParecer.data = []
        form.value.slctAtendimentos.data = []
        form.value.dataEmissao.data = null
        form.value.slctRecursos.data = []
        form.value.slctAlunos.data = []
        optionsAtendimentosAluno.value = []
        form.value.alunos = []
        showInfo.value = false

        defineAtendimentos()

        try {
            loading.value = true
            window.axios.get(`${routes.alunos}/${form.value.slctTurmas.data.code}`).then(retorno => {
                optionsAlunos.value = retorno.data.data.map(aluno => {
                    return {
                        name: aluno.nome,
                        code: aluno.codigo
                    }
                })
                loading.value = false
                form.value.alunos.push(retorno.data.data)
                form.value.alunos.push([])
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    function buscaDadosAlunos() {

        form.value.textAreaParecer.data = []
        form.value.slctAtendimentos.data = []
        form.value.dataEmissao.data = null
        form.value.slctRecursos.data = []
        optionsAtendimentosAluno.value = []
        showInfo.value = false
        buscaAtendimentosAluno()
        buscaRecursosAluno()
        buscaDeficiencias()
        try {
            loading.value = true
            window.axios.get(`${routes.aluno}/${form.value.slctAlunos.data.code}`).
                then(retorno => {
                let aluno = retorno.data.data;
                var oIdade = js_idade(  aluno[0].dataNascimento.split('-')[2],
                                        aluno[0].dataNascimento.split('-')[1],
                                        aluno[0].dataNascimento.split('-')[0]);
                optionsDados.value =  {
                                                'dataNascimento': aluno[0].dataNascimento,
                                                'idade': oIdade.string,
                                                'etapa': aluno[0].etapa,
                                                'cadeirante': aluno[0].cadeirante
                                            };
                loading.value = false
                showInfo.value = true;
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    function buscaDeficiencias() {

        try {
            loading.value = true
            window.axios.get(`${routes.deficiencias}/${form.value.slctAlunos.data.code}`).
                then(retorno => {
                optionsDadosDeficiencia.value = retorno.data.data.map(aluno => {
                    return {
                        deficiencia: aluno.deficiencia,
                        subdivisao: aluno.subdivisao,
                    }
                })
                loading.value = false
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }

    }

    function defineAtendimentos() {

        optionsAtendimentos.value = []
        tiposAtendimentos.forEach( (element, id) => {

            if (form.value.slctTurmas.data.atendimentos.charAt(id) == '1' &&
                element != '') {
                optionsAtendimentos.value.push({
                       name: element,
                       code: id
                    })
            }
        });

    }

    const salvar = async () => {
        try {
            loading.value = true
            let parametros = {};
            parametros.aluno = form.value.slctAlunos.data.code === null ? '':form.value.slctAlunos.data.code;
            parametros.parecer = form.value.textAreaParecer.data === null ? '':form.value.textAreaParecer.data;
            parametros.atendimentos = form.value.slctAtendimentos.data === null ? '':form.value.slctAtendimentos.data;
            parametros.data = form.value.dataEmissao.data === null ? '':form.value.dataEmissao.data.toLocaleDateString()
            parametros.recursos = form.value.slctRecursos.data === null ? '':form.value.slctRecursos.data;

            let resposta = (await window.axios.post(`${routes.saveAtendimento}`, parametros));

            form.value.textAreaParecer.data = []
            form.value.slctAtendimentos.data = []
            form.value.dataEmissao.data = null
            form.value.slctRecursos.data = []
            buscaAtendimentosAluno()
            buscaRecursosAluno()
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
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }

    }

    function cancelar() {
        try {
            form.value.textAreaParecer.data = []
            form.value.slctAtendimentos.data = []
            form.value.dataEmissao.data = null
            form.value.slctRecursos.data = []
            showSalvar.value = true;

        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }

    }

    function buscaAtendimentosAluno() {

        try {
            loading.value = true
            window.axios.get(`${routes.atendimentos}/${form.value.slctAlunos.data.code}`).then(retorno => {
                optionsAtendimentosAluno.value = retorno.data.data.map(atendimento => {
                    return {
                                'atendimentos': atendimento.atendimentos.map( natendimento => {
                                    return {
                                        'natendimento' : tiposAtendimentos[natendimento],
                                    }
                                }),
                                'atendimentosCode' : atendimento.atendimentos,
                                'data': atendimento.data,
                                'parecerCompleto': atendimento.parecer,
                                'parecer': atendimento.parecer.slice(0, 200),
                                'aluno': atendimento.aluno,
                                'codigo': atendimento.codigo
                            };
                })
                loading.value = false
            })
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    function buscaRecursosAluno() {

        try {
            loading.value = true
            window.axios.get(`${routes.recursos}/${form.value.slctAlunos.data.code}`).then(retorno => {
                optionsRecursos.value = retorno.data.data.map(recurso => {
                    return {
                                'descricao': recurso.ed198_descricao,
                                'codigoRecursoAtendimento': recurso.ed198_codigo,
                                'codigoRecurso': recurso.ed199_codigo,
                                'codigoAtendimento': recurso.ed197_codigo
                            };
                })
                loading.value = false
            })

        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    const excluirRegistro = async (data) => {

        confirm.require({
            message: "Excluir este registro?",
            header: "Confirmação",
            icon: "pi pi-exclamation-triangle",
            accept: async () => {
                loading.value = true
                try {
                    let resposta = (await window.axios.delete(`${routes.excluir}/${data.codigo}`));
                    buscaAtendimentosAluno();
                    buscaRecursosAluno();
                    loading.value = false;
                    toast.add({
                                severity: 'success',
                                summary: 'Sucesso',
                                detail: `Sucesso ao Excluir`,
                                life: 5000
                            });
                    return true;
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

    function editarRegistro (data) {
        form.value.codigo.data = data.codigo;
        form.value.slctAlunos.data.code = data.aluno;
        form.value.textAreaParecer.data = data.parecerCompleto;
        form.value.slctAtendimentos.data = data.atendimentosCode;
        form.value.dataEmissao.data = (new Date(data.data)).toLocaleDateString('pt-BR', {timeZone: 'UTC'});
        let recursos = filtrarRecursos(data.codigo)
        let codigos = recursos.map( (a) => a.codigoRecursoAtendimento)
        form.value.slctRecursos.data = codigos;
        showSalvar.value = false;
    }

    const editar = async () => {

        try {
            loading.value = true
            let parametros = {};

            parametros.codigo = form.value.codigo.data === null ? '': form.value.codigo.data;
            parametros.aluno = form.value.slctAlunos.data.code === null ? '':form.value.slctAlunos.data.code;
            parametros.parecer = form.value.textAreaParecer.data === null ? '':form.value.textAreaParecer.data;
            parametros.atendimentos = form.value.slctAtendimentos.data === null ? '':form.value.slctAtendimentos.data;
            parametros.data = form.value.dataEmissao.data === null ? '':form.value.dataEmissao.data
            parametros.recursos = form.value.slctRecursos.data === null ? '':form.value.slctRecursos.data;
            let recursos = filtrarRecursos(parametros.codigo)
            let recursoAtendimento = recursos.map( (a) => a.codigoRecurso)
            parametros.recursoAtendimento = recursoAtendimento === null ? '':recursoAtendimento;

            let resposta = (await window.axios.post(`${routes.atualizar}`, parametros));

            form.value.textAreaParecer.data = []
            form.value.slctAtendimentos.data = []
            form.value.dataEmissao.data = null
            form.value.slctRecursos.data = []
            showSalvar.value = true;
            buscaAtendimentosAluno()
            buscaRecursosAluno()
            loading.value = false
            toast.add({
                        severity: 'success',
                        summary: 'Sucesso',
                        detail: `Sucesso ao Editar`,
                        life: 5000
                    });
        } catch (e) {
            loading.value = false
            toast.add({
                severity: 'error',
                summary: 'Erro',
                detail: `${e.response.data.message}`,
                life: 5000
            });
        }
    }

    const checkDisable = () => {
        return (form.value.slctAtendimentos.data === null || form.value.slctAtendimentos.data.length === 0) ||
                (form.value.slctRecursos.data === null || form.value.slctRecursos.data.length === 0) ||
                (form.value.textAreaParecer.data === null || form.value.textAreaParecer.data.length === 0) ||
                (form.value.dataEmissao.data === null || form.value.dataEmissao.data.length === 0) ||
                (form.value.slctAlunos.data === null || form.value.slctAlunos.data.length  === 0)
    }

    if (isSecretaria.value) {
        buscaEscolas()
    } else {
        buscaCalendarios()
    }

    buscaTiposAtendimentos()
    buscarRecursos()

    function filtrarRecursos(codigoRecurso) {
        return optionsRecursos.value.filter( ({codigoAtendimento}) => {
           return codigoAtendimento == codigoRecurso
        })
    }
</script>

<template>
    <section class="container">
        <Panel header="Acompanhamento de Alunos PCD">
            <Fieldset legend="Filtros de busca">
                <div class="card">
                    <br/>
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-4"></div>
                        <div class="field col-12 md:col-4" >
                            <span class="p-float-label" v-show="isSecretaria">
                                <Dropdown id="dropdown" v-model="form.slctEscolas.data"
                                        :options="optionsEscolas" optionLabel="name" @change="buscaCalendarios"/>
                                <label for="dropdown">{{ form.slctEscolas.label }}</label>
                            </span>
                            <br/>
                        </div>
                    </div>
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-4">
                            <span class="p-float-label">
                                <Dropdown id="dropdown" v-model="form.slctCalendarios.data" :options="optionsCalendarios"
                                            optionLabel="name" @change="buscaTurmas" required/>
                                <label for="dropdown">{{ form.slctCalendarios.label }}</label>
                            </span>
                        </div>
                        <div class="field col-12 md:col-4">
                            <span class="p-float-label">
                                <Dropdown id="dropdown" v-model="form.slctTurmas.data"
                                        :options="optionsTurmas" optionLabel="name" @change="buscaAlunos" required/>
                                <label for="dropdown">{{ form.slctTurmas.label }}</label>
                            </span>
                        </div>
                        <div class="field col-12 md:col-4">
                            <span class="p-float-label">
                                <Dropdown id="dropdown" v-model="form.slctAlunos.data" :options="optionsAlunos"
                                            optionLabel="name" @change="buscaDadosAlunos" required/>
                                <label for="dropdown">{{ form.slctAlunos.label }}</label>
                            </span>
                        </div>
                    </div>
                </div>
            </Fieldset>
            <br/>
            <Fieldset legend="Dados do aluno">
                <div class="card" v-show="showInfo">
                    <br>
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-3">
                            <label for="username"><strong>Nascimento: </strong></label>
                            {{ (new Date(optionsDados.dataNascimento)).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) }}
                        </div>
                        <div class="field col-12 md:col-4">
                            <label for="username"><strong>Idade: </strong></label>
                            {{ optionsDados.idade }}
                        </div>
                        <div class="field col-12 md:col-3">
                            <label for="username"><strong>Etapa da matricula: </strong> </label>
                            {{ optionsDados.etapa }}
                        </div>
                        <div class="field col-12 md:col-2">
                            <label for="username"><strong>Cadeirante: </strong> </label>
                            {{ optionsDados.cadeirante == false ? "Não" : "Sim"}}
                        </div>
                    </div>
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-2">
                            <label for="username"><strong>PcD/Altas Habilidades : </strong> </label>
                        </div>
                            <ls>
                                <li
                                    v-for="(category, index) in optionsDadosDeficiencia"
                                    :key="index"
                                >
                                    <div class="mr-2 w-30rem mt-2">{{ category.deficiencia }}.</div>
                                    <div v-if="category.subdivisao != ''" class="ml-4 w-30rem mt-2">{{ category.subdivisao }}.</div>
                                </li>
                            </ls>
                    </div>
                </div>
            </Fieldset>
            <br/>
            <Fieldset legend="Adicione atendimento(s) ao aluno" >
                <div v-for="category of optionsAtendimentos" :key="category.code" class="flex align-items-center mt-3">
                    <Checkbox v-model="form.slctAtendimentos.data" :inputId="category.code" name="category"
                                :value="category.code" required/>
                    <label :for="category.key">{{ category.name }}</label>
                </div>
                <br>
                <br>

                <MultiSelect v-model="form.slctRecursos.data" display="chip" :options="optionsTodosRecursos" optionLabel="descricao"
                            optionValue="codigo"
                            placeholder="Selecione Recursos Utilizados"
                            :maxSelectedLabels="3" class="w-full md:w-20rem" />
                <br>
                <br>
                <br>
                <div class="card">
                    <div class="p-fluid grid">
                        <div class="field col-12 md:col-12">
                            <span class="p-float-label">
                                <Textarea v-model="form.textAreaParecer.data" rows="5" cols="5" />
                                <label>{{ form.textAreaParecer.label }}</label>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Calendar inputId="dateformat"
                                  showIcon
                                  v-model="form.dataEmissao.data"
                                  dateFormat="dd/mm/yy"
                                  required/>
                        <label for="dateformat">{{ form.dataEmissao.label }}</label>
                    </span>
                </div>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-4"></div>
                    <div class="field col-12 md:col-2">
                        <Button v-show="showSalvar" class="p-button" icon="pi pi-check" label="Salvar" type="submit"
                                @click="salvar" :disabled="checkDisable()"></Button>
                        <Button v-show="!showSalvar" class="p-button" icon="pi pi-check" label="Salvar"
                                @click="editar()" :disabled="checkDisable()"></Button>
                    </div>
                    <div class="field col-12 md:col-2">
                        <Button class="p-button" icon="pi pi-times" label="Cancelar"
                                @click="cancelar"></Button>
                    </div>
                    <div class="field col-12 md:col-4"></div>
                </div>
            </Fieldset>
        </Panel>
        <br>
        <Fieldset legend="Registros" :toggleable="true" style="width: auto; margin: 0 auto; height: auto">
            <div class="p-fluid grid">
                <DataTable  v-model:expandedRows="expandedRows" :value="optionsAtendimentosAluno.value"
                            style="width: 100%" :loading="loading">

                    <template #empty>
                        Nenhum registro foi encontrado
                    </template>
                    <Column field="data" header="Data" style="min-width:8rem">
                        <template #body="slotProps">
                            {{ (new Date(slotProps.data.data)).toLocaleDateString('pt-BR', {timeZone: 'UTC'})}}
                        </template>
                    </Column>
                    <Column field="parecer" header="Parecer" style="min-width:28rem">
                        <template #body="slotProps">
                            {{ slotProps.data.parecer}}
                        </template>
                    </Column>
                    <Column header="Ações"  style="min-width:10rem">
                        <template #body="slotProps">
                            <i class="pi pi-pencil text-blue-600" style="font-size: 1rem; margin-right: 1.5rem;" @click="editarRegistro(slotProps.data)"></i>
                            <i class="pi pi-trash text-red-600" style="font-size: 1rem" @click="excluirRegistro(slotProps.data)"></i>
                        </template>
                    </Column>
                    <Column header="Atendimentos" expander style="width: 5rem" />
                    <template #expansion="slotProps">
                        <strong>Parecer: </strong>{{ slotProps.data.parecerCompleto}}
                        <div class="p-3">
                            <DataTable :value="slotProps.data.atendimentos">
                                <Column field="natendimento" header="Atendimentos do aluno"/>
                            </DataTable>
                        </div>
                        <div class="p-3" v-if="filtrarRecursos(slotProps.data.codigo).length > 0">
                            <DataTable :value="filtrarRecursos(slotProps.data.codigo)">
                                <Column field="descricao" header="Recursos utilizados"/>
                            </DataTable>
                        </div>
                    </template>
                </DataTable>
            </div>
        </Fieldset>
        <ConfirmDialog/>
        <ModalLoading :isLoading="loading"/>
    </section>
</template>

<style scoped>
    :deep(.p-fieldset-legend) {
        padding: 0!important;
        border: none;
        background: transparent;
    }
    :deep(.p-fieldset){
        background: #e1dede;
    }

    .tableDeficiencia:deep(.p-datatable-thead > tr > th) {
        background: transparent;
        border: 1px solid #eee;
        color: #000;
    }

    .tableDeficiencia :deep(.p-datatable-tbody > tr > td) {
        background: transparent;
        border: 1px solid #eee;
        color: #000;
    }

    .tableDeficiencia :deep(.p-datatable-tbody > tr) {
        background: transparent;
        color: #000;
    }

</style>
