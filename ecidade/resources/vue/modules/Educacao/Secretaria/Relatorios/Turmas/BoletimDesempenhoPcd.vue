<script setup>
    import { ref } from "vue";
    import { useToast } from "primevue/usetoast"
    import ModalLoading from "../../../../Components/ModalLoading.vue";
    import MultiDownload from "../../../../Components/MultiDownload.vue";
    import DialogPesquisaEducacao from "../../../Components/DialogPesquisaEducacao.vue";
    import MultiSelect from 'primevue/multiselect'
    const props = defineProps(['escola', 'modulo'])
    const MODULO_SECRETARIA = 7159;
    const windowUrl = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g)[0]
    const routes = {
        calendarios: `v4/api/educacao/escola/calendarioaee`,
        turmas: `v4/api/educacao/escola/turmasEspeciaisaee`,
        alunos: `v4/api/educacao/escola/alunos/alunos-por-turma-especial`,
        profissionais: `v4/api/educacao/escola/${props.escola}/profissionais`,
        secretarios: `v4/api/educacao/escola/secretarios/${props.escola}`,
        emissao: `${windowUrl}/edu2_boletimdesempenhopcd002.php?`,
        emissao2: `${windowUrl}/edu2_boletimdesempenhopcd003.php?`,
        diretores: `v4/api/educacao/escola/${props.escola}/diretores`,
        escolas: `v4/api/educacao/escola`,
        atendimentos: `v4/api/educacao/escola/alunoAtendimentosespecial/atendimentos`,
        profissionaisEspecializados: `v4/api/educacao/escola/alunoAtendimentosespecial/profissionais-especializados`
    }
    const isSecretaria = ref(null)
    isSecretaria.value = props.modulo == MODULO_SECRETARIA
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
        slctEscolas: {
            data: null,
            label: 'Escola',
            required: false
        },
        slctTurmas: {
            data: null,
            label: 'Turmas',
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
        slctAtividades: {
            label: 'Atividade',
            required: false,
            data: null
        },
        slctPeriodos: {
            label: 'Período',
            required: false,
            data: null
        },
        slctModelos: {
            label: 'Modelo',
            required: false,
            data: null
            
        },
        slctProfissionaisEspecializados: {
            label: 'Profissional Especializado',
            required: false,
            data: null
        },
        slctSecretarios: {
            label: 'Secretário',
            required: false,
            data: null
        },
        slctDiretores: {
            label: 'Diretor',
            required: false,
            data: null
        },
        textAreaObservacao: {
            label: 'Observação Geral',
            data: null
        },
        alunos: [[],[]]
    })

    const optionsAtividades = ref(null)
    const openFuncPesquisa = ref(false)
    const optionsCalendarios = ref(null)
    const optionsTurmas = ref(null)
    const optionsEscolas = ref([{
        name: 'Todos',
        code: null
    }])
    const optionsPeriodos = ref([])
    const optionsModelos = ref([
        {name: `Modelo 1`, code: 1},
        {name: `Modelo 2`, code: 2}
    ])
    const optionsProfissionaisEspecializados = ref([
        {code: '', name: ''}
    ])
    const optionsSecretarios = ref([
        {code: '', name: ''}
    ])
    const optionsDiretores = ref([
        {code: '', name: ''}
    ])

    function buscaCalendarios() {
        try {
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
                const call = props.modulo == MODULO_SECRETARIA && buscaEscolas() ? buscaEscolas() : null;
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
    function buscaProfissionaisEspecializados() {
        loading.value = true
        const turmaId = form.value.slctTurmas.data.code;
        window.axios.get(`${routes.profissionaisEspecializados}/${turmaId}`).then(retorno => {
            optionsProfissionaisEspecializados.value = retorno.data.data.map(profissional => {
                return {
                    name: `${profissional.profissional}`,
                    code: profissional.codigo
                }
            })
            loading.value = false
            buscaSecretarios()
        })
    }
    function buscaTurmas() {
        try {
            loading.value = true
            let parametros = {};
            parametros.calendario = form.value.slctCalendarios.data.code;
            parametros.escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code;
            window.axios.post(`${routes.turmas}`, parametros).then(retorno => {
                optionsTurmas.value = retorno.data.data.map(turma => {
                    return {
                        name: turma.descricao,
                        code: turma.id
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
                buscaDiretores()
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
    function buscaDiretores() {
        try {
            loading.value = true
            window.axios.get(routes.diretores).then(retorno => {
                retorno.data.data.forEach(diretor => {
                    optionsDiretores.value.push({
                       name: `${diretor.nome}`,
                       code: diretor.codigo_rechumano
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
    function buscaEscolas() {
        try {
            loading.value = true
            window.axios.get(routes.escolas).then(retorno => {
                retorno.data.data.forEach(escola => {
                    optionsEscolas.value.push({
                       name: `${escola.ed18_c_nome}`,
                       code: escola.ed18_i_codigo
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
    async function buscaDatasAtendimento(alunos) {
        try {
            loading.value = true
            let parametros = {};
            parametros.alunos = alunos
            window.axios.post(routes.atendimentos, parametros).then(retorno => {
                retorno.data.data.forEach(data => {
                    optionsPeriodos.value.push({
                       label: `${data}`,
                       code: `${data}`
                    })
                })
                loading.value = false
                buscaProfissionaisEspecializados()
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
    function buscaAlunos() {
        form.value.alunos = []
        try {
            loading.value = true
            window.axios.get(`${routes.alunos}/${form.value.slctTurmas.data.code}`).then(retorno => {
                form.value.alunos.push(retorno.data.data)
                form.value.alunos.push([])
                loading.value = false
                buscaDatasAtendimento(retorno.data.data)
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
    function selecionaProfissional(e) {
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
        const periodosSelecionados = form.value.slctPeriodos.data !== null 
            ? form.value.slctPeriodos.data.map(data => data.code) 
            : [];
        let turma = form.value.slctTurmas.data.code
        let turmaNome = form.value.slctTurmas.data.name === null ? '' : btoa(form.value.slctTurmas.data.name)
        let alunos = form.value.alunos[1].map(aluno => aluno.codigo)
        alunos = alunos.join()
        let observacao = form.value.textAreaObservacao.data === null ? '' : btoa(form.value.textAreaObservacao.data)
        let periodos = periodosSelecionados.length == 0 ? '' : periodosSelecionados.join(',')
        let profissionalEspecializado = 
            (form.value.slctProfissionaisEspecializados.data === null 
                || form.value.slctProfissionaisEspecializados.data === undefined
            )
            ? '' 
            : btoa(form.value.slctProfissionaisEspecializados.data.name)
        let escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code
        let assinaturaAdicional = form.value.assinaturaAdicional.codigo.value === null ?
            "" :
            form.value.assinaturaAdicional.codigo.value
        let secretario =  form.value.slctSecretarios.data === undefined
            ? ''
            : btoa(form.value.slctSecretarios.data.name)

        let diretor =  form.value.slctDiretores.data === undefined
            ? ''
            : btoa(form.value.slctDiretores.data.name)
        
        let atividade = form.value.slctAtividades.data === null ? '' : form.value.slctAtividades.data.code

        let parametros = [
            `calendario=${form.value.slctCalendarios.data.code}`,
            `escola=${escola}`,
            `turma=${turma}`,
            `turmaNome=${turmaNome}`,
            `alunos=${alunos}`,
            `periodos=${periodos}`,
            `profissionalEspecializado=${profissionalEspecializado}`,
            `secretario=${secretario}`,
            `diretor=${diretor}`,
            `iAtividade=${atividade}`,
            `iAssinaturaAdicional=${assinaturaAdicional}`,
            `sObs=${observacao}`
        ]

        let rota = form.value.slctModelos.data.code == 1 ? routes.emissao : routes.emissao2;
        download.value.addFile(
            `${rota}${parametros.join('&')}`,
            'Boletim de Desempenho - PCD - Modelo '+form.value.slctModelos.data.code
        )
        download.value.openModal();
    }
    
    form.value.slctSecretarios.data = optionsSecretarios.value[0]
    form.value.slctDiretores.data = optionsDiretores.value[0]
    form.value.slctModelos.data = optionsModelos.value[0]
    buscaCalendarios()
</script>

<template>
    <section class="container">
        <Panel header="Boletim de Desempenho - PCD">
            <div class="card">
                <br>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-1">
                    </div>
                    <div class="field col-12 md:col-3" v-show="isSecretaria">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctEscolas.data"
                                :options="optionsEscolas" optionLabel="name" @change="buscaCalendarios" />
                            <label for="dropdown">{{ form.slctEscolas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-3">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctCalendarios.data"
                                :options="optionsCalendarios" optionLabel="name" @change="buscaTurmas"/>
                            <label for="dropdown">{{ form.slctCalendarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-3">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctTurmas.data" :options="optionsTurmas"
                                optionLabel="name" @change="buscaAlunos" />
                            <label for="dropdown">{{ form.slctTurmas.label }}</label>
                        </span>
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
                    <div class="field col-6 md:col-2">
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-2">
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <MultiSelect 
                                id="periodos"
                                v-model="form.slctPeriodos.data" 
                                :options="optionsPeriodos"
                                optionLabel="label"
                                />
                             <label for="">Períodos</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="modelos"  :options="optionsModelos"
                                      optionLabel="name"
                                      v-model="form.slctModelos.data" />
                             <label for="">{{ form.slctModelos.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-2">
                    </div>                    
                </div>
            </div>
            <div class="card">
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="profissionalEspecializado"  :options="optionsProfissionaisEspecializados"
                                      optionLabel="name"
                                      v-model="form.slctProfissionaisEspecializados.data" />
                             <label for="">{{ form.slctProfissionaisEspecializados.label }}</label>
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
                            <Button 
                                @click="funcPesquisa.open()" 
                                icon="pi pi-search" 
                                aria-label="Filter" 
                                style="width: 200px"
                            />
                        </div>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="atividades"  :options="optionsAtividades"
                                    optionLabel="name"
                                    v-model="form.slctAtividades.data" />
                            <label for="">{{ form.slctAtividades.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                          <span class="p-float-label">
                            <Dropdown id="secretarios"  :options="optionsSecretarios"
                                      optionLabel="name"
                                      v-model="form.slctSecretarios.data" />
                             <label for="">{{ form.slctSecretarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                          <span class="p-float-label">
                            <Dropdown id="diretores"  :options="optionsDiretores"
                                      optionLabel="name"
                                      v-model="form.slctDiretores.data" />
                             <label for="">{{ form.slctDiretores.label }}</label>
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
