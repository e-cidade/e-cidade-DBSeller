<script setup>
    import { ref } from "vue";
    import { useToast } from "primevue/usetoast"
    import ModalLoading from "../../../../Components/ModalLoading.vue";
    import MultiDownload from "../../../../Components/MultiDownload.vue";
    import DialogPesquisaEducacao from "../../../Components/DialogPesquisaEducacao.vue";
    const props = defineProps(['escola', 'usuario'])
    const windowUrl = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g)[0]
    const routes = {
        calendarios: `v4/api/educacao/escola/${props.escola}/calendario`,
        turmas: `v4/api/educacao/escola/turmas-por-calendario`,
        disciplinas: `v4/api/educacao/escola/turma`,
        alunos: `v4/api/educacao/escola/alunos/alunos-por-turma`,
        profissionais: `v4/api/educacao/escola/${props.escola}/profissionais`,
        secretarios: `v4/api/educacao/escola/secretarios/${props.escola}`,
        emissao: `${windowUrl}/edu2_fichaindividualaluno003.php?`,
        emissao2: `${windowUrl}/edu2_fichaindividualaluno002.php?`
    }
    const download = ref(null)
    const loading = ref(false)
    const toast = useToast()
    const funcPesquisa = ref(null)
    const fieldsDisplay = ref([
        {
            field: 'cgm',
            dataType: 'int',
            label: 'CGM',
            columnSize: 15
        },
        {
            field: 'cod_rechumano',
            dataType: 'string',
            label: 'Cod. Rec. Humano',
            columnSize: 15
        },
        {
            field: 'matricula',
            dataType: 'string',
            label: 'Matrícula Folha',
            columnSize: 15
        },
        {
            field: 'nome',
            dataType: 'string',
            label: 'Nome',
            columnSize: 25
        },
        {
            field: 'dataNascimento',
            dataType: 'data',
            label: 'Data de Nascimento',
            columnSize: 15
        },
        {
            field: 'cpf',
            dataType: 'cpf',
            label: 'CPF',
            columnSize: 15
        }
    ])

    const form = ref({
        slctCalendarios: {
            data: null,
            label: 'Calendário',
            required: false
        },
        slctTurmas: {
            data: null,
            label: 'Turmas',
            required: false
        },
        cargaHoraria: {
            data: null,
            label: 'Carga Horária',
            required: false
        },
        dataEmissao: {
            data: null,
            label: 'Data de Emissão',
            required: false
        },
        checkAssProfessor: {
            data: null,
            label: 'Exibe Assinatura do Professor',
            required: false
        },
        checkAssFuncionario: {
            label: 'Exibe Assinatura Adicional de Funcionário',
            required: false
        },
        checkAssResponsavel: {
            label: 'Exibe Assinatura do Responsável',
            required: false
        },
        checkExibeDiasLetivos: {
            label: 'Exibe Campo Dias Letivos',
            required: false
        },
        assinaturaAdicional: {
            codigo: {
                value: null,
                disabled: false,
                label: 'Codigo'
            },
            nome: {
                value: null,
                disabled: true,
                label: 'Assinatura Adicional'
            }
        },
        slctSecretarios: {
            label: 'Secretário',
            required: false,
            data: null
        },
        slctAtividades: {
            label: 'Atividade',
            required: false,
            data: null
        },
        slctDisciplinas: {
            label: 'Disciplinas',
            required: false,
            data: null
        },
        slctDisposicao: {
            label: 'Disposição',
            required: false,
            data: null,
            disabled: true
        },
        textAreaObservacao: {
            label: 'Observação Geral',
            data: null
        },
        checks: null,
        alunos: [[],[]]
    })

    const openFuncPesquisa = ref(false)
    const optionsCalendarios = ref(null)
    const optionsTurmas = ref(null)
    const optionsSecretarios = ref([{
        name: 'Selecione',
        code: null
    }])
    const optionsAtividades = ref(null)

    const optionsDisposicao = ref([
        {
            name: 'Retrato',
            code: 0
        },
        {
            name: 'Paisagem',
            code: 1
        }
    ])
    const optionsDisciplinas = ref(null)

    function buscaCalendarios() {
        try {
            loading.value = true
            window.axios.get(routes.calendarios).then(retorno => {
                optionsCalendarios.value = retorno.data.data.map(calendario => {
                    return {
                        name: calendario.nome,
                        code: calendario.codigo
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

    function buscaProfissional() {
        let cgm = form.value.assinaturaAdicional.codigo.value
        if (cgm !== null) {
            loading.value = true
            funcPesquisa.value.getData(`${routes.profissionais}?cgm=${cgm}`).then(async response => {
                loading.value = false
                let retorno =  Object.values(response)
                if (retorno.length === 0) {
                    toast.add({
                        severity: 'warn',
                        summary: 'Atenção',
                        detail: `Nenhum registro encontrado`,
                        life: 5000
                    });
                } else {
                    form.value.assinaturaAdicional.nome.value = retorno[0].nome
                    form.value.assinaturaAdicional.codigo.value = retorno[0].profissional.codigo
                    optionsAtividades.value = retorno[0].atividades.map(atividade => {
                        return {code: atividade.codigo, name: atividade.nome}
                    })
                }
            })
        } else {
            form.value.assinaturaAdicional.nome.value = ''
            optionsAtividades.value = null
        }
    }

    function buscaTurmas() {
        try {
            loading.value = true
            window.axios.get(`${routes.turmas}/${form.value.slctCalendarios.data.code}?validaUsuario=${props.usuario}`).then(retorno => {
                let turmaResponse = retorno.data.data;
                let responseObj = [];
                turmaResponse.forEach(oTurma => {
                    if (oTurma.etapas !== undefined) {
                        if (oTurma.etapas.length > 1) {
                            let contador = 0;
                            oTurma.etapas.forEach(etapa => {
                                const nome = oTurma.nome + ' - ' + etapa.nome;
                                let parecer = false;
                                if (oTurma.procedimentos[contador].procedimento == "PARECER") {
                                    parecer = true;
                                }
                                responseObj.push({
                                    name: nome,
                                    code: oTurma.codigo,
                                    temParecer: parecer,
                                    etapa: etapa.codigo
                                });
                                contador++;
                            });
                        } else {
                            if (Array.isArray(oTurma.etapas)) {
                                const nome = oTurma.nome + ' - ' + oTurma.etapas[0].nome;
                                let parecer = false;
                                if (oTurma.procedimentos[0].procedimento == "PARECER") {
                                    parecer = true;
                                }
                                responseObj.push({
                                    name: nome,
                                    code: oTurma.codigo,
                                    temParecer: parecer,
                                    etapa: 0
                                });
                            } else {
                                const nome = oTurma.nome + ' - ' + oTurma.etapas.nome;
                                let parecer = false;
                                if (oTurma.procedimentos[0].procedimento == "PARECER") {
                                    parecer = true;
                                }
                                responseObj.push({
                                    name: nome,
                                    code: oTurma.etapa.codigo,
                                    temParecer: parecer,
                                    etapa: 0
                                });
                            }
                        }
                    }
                })
                optionsTurmas.value = responseObj;
                loading.value = false;
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
    function buscaSecretarios() {
        try {
            loading.value = true
            window.axios.get(routes.secretarios).then(retorno => {
                retorno.data.data.forEach(secretario => {
                    optionsSecretarios.value.push({
                       name: `${secretario.nome}`,
                       code: secretario.codigo_rechumano
                    })
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

    async function buscaAlunos() {
        optionsDisciplinas.value = await buscaDisciplinas()
        form.value.alunos = []
        form.value.slctDisposicao.disabled = form.value.slctTurmas.data.temParecer;
        let codigoEtapa = form.value.slctTurmas.data.etapa;
        try {
            loading.value = true
            window.axios.get(`${routes.alunos}/${form.value.slctTurmas.data.code}`).then(retorno => {
                let retornoAlunos = retorno.data.data;
                let responseObj = []
                retornoAlunos.forEach(oAluno => {
                    if (oAluno.codigo_etapa == codigoEtapa || codigoEtapa == 0) {
                        responseObj.push(oAluno)
                    }
                });
                form.value.alunos.push(responseObj)
                form.value.alunos.push([])
            })
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

    async function buscaDisciplinas() {
        let disciplinas = [
            {
                name: 'PARECER ÚNICO',
                code: 'PU'
            }
        ];
        try {
            loading.value = true
            let retorno = await window.axios.get(`${routes.disciplinas}/${form.value.slctTurmas.data.code}/disciplinas`)
            loading.value = false
            retorno.data.data.forEach(disciplina => {
                disciplinas.push({
                    name: disciplina.ed232_c_descr.trim(),
                    code: disciplina.codigoRegenciaNaTurma
                })
            })
            return disciplinas
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

    async function selecionaProfissional(e) {
        form.value.assinaturaAdicional.nome.value = e.nome
        optionsAtividades.value = e.atividades.map(atividade => {
            return {code: atividade.codigo, name: atividade.nome}
        })
        form.value.assinaturaAdicional.codigo.value = e.profissional.codigo
    }

    function openModal() {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: `Nenhum Aluno encontrado para esses filtros.`,
            life: 5000
        });
    }

    function emitir() {
        let codigosDisciplinas = optionsDisciplinas.value
            .map(disciplina => disciplina.code)
            .filter(disciplina => disciplina !== 'PU')

        let disciplinas = form.value.slctDisciplinas.data === null ? 'T' : form.value.slctDisciplinas.data.code
        let parecerUnico = disciplinas === 'PU'? 'yes' : 'no'
        disciplinas = disciplinas === 'T' ? codigosDisciplinas.join() : disciplinas
        disciplinas = parecerUnico === 'yes' ? codigosDisciplinas[0] : disciplinas
        let turma = form.value.slctTurmas.data.code
        let alunos = form.value.alunos[1].map(aluno => aluno.matricula)
        alunos = alunos.join()
        let observacao = form.value.textAreaObservacao.data === null ? '' : form.value.textAreaObservacao.data
        let assinaturaAdicional = form.value.assinaturaAdicional.codigo.value === null ?
            "" :
            form.value.assinaturaAdicional.codigo.value
        let cargaHoraria = form.value.cargaHoraria.data === null ? "" : form.value.cargaHoraria.data
        let checkAssProfessor = form.value.checks === null ? false : form.value.checks.includes('checkAssProfessor')
        let checkAssResponsavel = form.value.checks === null ? false : form.value.checks.includes('checkAssResponsavel')
        let checkExibeDiasLetivos = form.value.checks === null ? false : form.value.checks.includes('checkExibeDiasLetivos')
        let checkAssFuncionario = form.value.checks === null ? false : form.value.checks.includes('checkAssFuncionario')
        let atividade = form.value.slctAtividades.data === null ? '' : form.value.slctAtividades.data.code
        let dataEmissao = form.value.dataEmissao.data === null ? '' : form.value.dataEmissao.data.toLocaleDateString()
        let secretario =  form.value.slctSecretarios.data.code === null || form.value.slctSecretarios.data === null
            ? ''
            : btoa(form.value.slctSecretarios.data.name)

        let parametros = [
            `punico=${parecerUnico}`,
            `calendario=${form.value.slctCalendarios.data.code}`,
            `disciplinas=${disciplinas}`,
            `turma=${turma}`,
            `alunos=${alunos}`,
            `obs1=${observacao}`,
            `iAssinaturaAdicional=${assinaturaAdicional}`,
            `lExibeAssinaturaProfessor=${checkAssProfessor}`,
            `lExibeAssinaturaFuncionario=${checkAssFuncionario}`,
            `lExibeAssinaturaResponsavel=${checkAssResponsavel}`,
            `lExibeDiasLetivos=${checkExibeDiasLetivos}`,
            `sCargaHorariaOpcional=${cargaHoraria}`,
            `secretario=${secretario}`,
            `iAtividade=${atividade}`,
            `iDataEmissao=${dataEmissao}`,
            `iOrientacao=${form.value.slctDisposicao.data.code}`,
            `sObs=${observacao}`,
            `incluirobs`
        ]

        let rota = form.value.slctTurmas.data.temParecer ? routes.emissao : routes.emissao2;
        download.value.addFile(
            `${rota}${parametros.join('&')}`,
            'Ficha Individual do Aluno'
        )
        download.value.openModal();
    }
    form.value.slctDisposicao.data = optionsDisposicao.value[0]
    form.value.slctSecretarios.data = optionsSecretarios.value[0]
    buscaCalendarios()
    buscaSecretarios()
</script>

<template>
    <section class="container">
        <Panel header="Ficha Individual do Aluno">
            <div class="card">
                <br>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2">
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctCalendarios.data"
                                      :options="optionsCalendarios" optionLabel="name" @change="buscaTurmas"/>
                            <label for="dropdown">{{ form.slctCalendarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctTurmas.data" :options="optionsTurmas"
                                      optionLabel="name" @change="buscaAlunos" />
                            <label for="dropdown">{{ form.slctTurmas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-2">
                    </div>
                </div>
            </div>
            <div>
                <PickList id="list" v-model="form.alunos" listStyle="height:200px" dataKey="codigo">
                    <template #item="slotProps">
                        <div class="alunos-item">
                            <div class="alunos-list-detail">
                                <li class=""><small>
                                    {{ slotProps.item.nome }} - {{ slotProps.item.codigo }}
                                </small></li>
                            </div>
                        </div>
                    </template>
                </PickList>
            </div>
            <div class="card">
                <br><br>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2">
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <InputText id="anoLimite"
                                       v-model="form.cargaHoraria.data"/>
                            <label for="">{{ form.cargaHoraria.label }}</label>
                        </span>
                        <small>* Em branco será calculado pelo Sistema.</small>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Calendar inputId="dateformat"
                                      v-model="form.dataEmissao.data"
                                      dateFormat="dd/mm/yy"/>
                            <label for="dateformat">{{ form.dataEmissao.label }}</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-2">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-3">
                        <div class="field-checkbox">
                            <Checkbox inputId="prof" name="prof" value="checkAssProfessor" v-model="form.checks" />
                            <label for="prof">{{ form.checkAssProfessor.label }}</label>
                        </div>
                    </div>
                    <div class="field col-12 md:col-3">
                        <div class="field-checkbox">
                            <Checkbox inputId="func" name="func" value="checkAssFuncionario" v-model="form.checks" />
                            <label for="func">{{ form.checkAssFuncionario.label }}</label>
                        </div>
                    </div>
                    <div class="field col-12 md:col-3">
                        <div class="field-checkbox">
                            <Checkbox inputId="resp" name="resp" value="checkAssResponsavel" v-model="form.checks" />
                            <label for="resp">{{ form.checkAssResponsavel.label }}</label>
                        </div>
                    </div>
                    <div class="field col-12 md:col-3">
                        <div class="field-checkbox">
                            <Checkbox inputId="letivos" name="letivos" value="checkExibeDiasLetivos" v-model="form.checks" />
                            <label for="letivos">{{ form.checkExibeDiasLetivos.label }}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-4">
                          <span class="p-float-label">
                            <Dropdown id="secretarios"  :options="optionsSecretarios"
                                      optionLabel="name"
                                      v-model="form.slctSecretarios.data" />
                             <label for="">{{ form.slctSecretarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <div class="p-inputgroup flex-1">
                             <span class="p-float-label">
                                <InputNumber inputId="anoLimite" :useGrouping="false" @focusout="buscaProfissional"
                                             style="width: 10px"
                                             v-model="form.assinaturaAdicional.codigo.value"/>
                                <label for="">{{ form.assinaturaAdicional.codigo.label }}</label>
                            </span>
                             <span class="p-float-label">
                                <InputText id="anoLimite" style="width: 200px"
                                           v-model="form.assinaturaAdicional.nome.value" disabled/>
                                 <label for="">{{ form.assinaturaAdicional.nome.label }}</label>
                            </span>
                            <Button @click="funcPesquisa.open()" icon="pi pi-search" aria-label="Filter" style="width: 200px"/>
                        </div>
                    </div>
                    <div class="field col-12 md:col-4">
                          <span class="p-float-label">
                            <Dropdown id="secretarios"  :options="optionsAtividades"
                                      optionLabel="name"
                                      v-model="form.slctAtividades.data" />
                             <label for="">{{ form.slctAtividades.label }}</label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-12">
                        <span class="p-float-label">
                            <Textarea v-model="form.textAreaObservacao.data" rows="5" cols="5" />
                            <label>{{ form.textAreaObservacao.label }}</label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2">
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="disciplinas" optionLabel="name" :options="optionsDisciplinas"
                                       v-model="form.slctDisciplinas.data"/>
                            <label for="">{{ form.slctDisciplinas.label }}</label>
                        </span>
                        <small>* Em branco imprime todas.</small>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="disposicao" optionLabel="name" :options="optionsDisposicao"
                                      :disabled="form.slctDisposicao.disabled"
                                      v-model="form.slctDisposicao.data"></Dropdown>
                            <label for="dateformat">{{ form.slctDisposicao.label }}</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-2">
                    </div>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-5"></div>
                <div class="field col-12 md:col-2">
                    <Button class="p-button" icon="pi pi-print" label="Imprimir"
                            :disabled="(form.alunos[1] === undefined || form.alunos[1].length === 0) ||
                            form.alunos[1].length === 0" @click="emitir"></Button>
                </div>
                <div class="field col-12 md:col-5"></div>
            </div>
            <div class="text-center">
                Para selecionar mais de um aluno mantenha pressionada a tecla <kbd>CTRL</kbd> e clique sobre o nome dos
                alunos.
            </div>
        </Panel>
        <ModalLoading :isLoading="loading"/>
        <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
        <DialogPesquisaEducacao ref="funcPesquisa" @select-line="selecionaProfissional" title='Recursos Humanos' :route='routes.profissionais' :fields-display="fieldsDisplay"></DialogPesquisaEducacao>
    </section>
</template>
<style scoped>
    label {
        color: #605e5c;
    }
    i {
        cursor: pointer
    }
</style>
