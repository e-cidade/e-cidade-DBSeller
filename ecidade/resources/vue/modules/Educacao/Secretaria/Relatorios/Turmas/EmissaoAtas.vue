<script setup>
    import { ref } from "vue";
    import { useToast } from "primevue/usetoast"
    import ModalLoading from "../../../../Components/ModalLoading.vue";
    import MultiDownload from "../../../../Components/MultiDownload.vue";
    import DialogPesquisaEducacao from "../../../Components/DialogPesquisaEducacao.vue";
    const props = defineProps(['escola', 'modulo'])
    const MODULO_SECRETARIA = 7159;
    const TIPO_MODELO_ATA = 7;
    const CODIGO_ATA_CLASSIFICACAO = 1;
    const CODIGO_ATA_RECLASSIFICACAO = 2;
    const CODIGO_ATA_GERAL = 3;
    const CODIGO_ATA_AVANCO = 4;
    const windowUrl = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g)[0]
    const routes = {
        calendarios: `v4/api/educacao/escola`,
        turmas: `v4/api/educacao/escola/turmas-por-calendario`,
        alunosPorTurma: `v4/api/educacao/escola/alunos/alunos-por-turma`,
        alunosClassificacao: `v4/api/educacao/secretaria/emissao-atas/getAlunosClassificacao`,
        alunosReclassificacao: `v4/api/educacao/secretaria/emissao-atas/getAlunosReclassificacao`,
        alunosAvanco: `v4/api/educacao/secretaria/emissao-atas/getAlunosAvanco`,
        profissionais: `v4/api/educacao/escola/#codigoescola#/profissionais`,
        secretarios: `v4/api/educacao/escola/secretarios`,
        caminihoAtaClassificacao: `${windowUrl}/edu2_ataclassificacao002.php?`,
        caminihoAtaReclassificacao: `${windowUrl}/edu2_atareclassificacoes002.php?`,
        caminihoAtaGeral: `${windowUrl}/edu2_atageral002.php?`,
        diretores: `v4/api/educacao/escola`,
        escolas: `v4/api/educacao/escola`,
        modelos_atas: `v4/api/educacao/secretaria/modelos-relatorio/getModelosRelatoriosPorTipo/${TIPO_MODELO_ATA}`
    }
    const isSecretaria = ref(null)
    isSecretaria.value = props.modulo == MODULO_SECRETARIA
    const download = ref(null)
    const loading = ref(false)
    const toast = useToast()
    const funcPesquisa = ref(null)
    const definicoesModeloAta = ref([])
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
        slctModelosAta: {
            label: 'Modelo de Ata',
            data: null
        },
        slctTipos: {
            label: 'Tipo',
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
        alunos: [[],[]],
        dataEmissao: {
            data: null,
            label: 'Data de Emissão',
            required: false
        },
        texto: {
            label: 'Texto',
            data: null
        }
    })

    const definicoesModelo = ref({
        exibir_assinatura_secretario: {
            data: true
        },
        exibir_assinatura_adicional: {
            data: true
        },
        exibir_grade_alunos: {
            data: true
        },
        exibir_coluna_disciplinas: {
            data: true
        },
        exibir_coluna_resultado_final: {
            data: true
        },
        exibir_brasao: {
            data: true
        },
        disposicao_brasao: {
            data: 'LADO'
        },
        disposicao_cabecalho: {
            data: 'ESQUERDA'
        },
        texto_cabecalho: {
            data: ''
        },
        texto_rodape: {
            data: ''
        },
        texto_obs: {
            data: ''
        },
         

    })

    const optionsAtividades = ref(null)
    const openFuncPesquisa = ref(false)
    const optionsCalendarios = ref(null)
    const optionsTurmas = ref(null)
    const optionsEscolas = ref([{
        name: 'Todos',
        code: null
    }])
    const optionsModelosAta = ref([])
    const optionsTipos = ref([
        {name: `Classificação`, code: CODIGO_ATA_CLASSIFICACAO},
        {name: `Avanço`, code: CODIGO_ATA_AVANCO},
        {name: `Reclassificação`, code: CODIGO_ATA_RECLASSIFICACAO},
        {name: `Geral`, code: CODIGO_ATA_GERAL}
    ])
    const optionsSecretarios = ref([
        {code: '', name: ''}
    ])
    const optionsDiretores = ref([
        {code: '', name: ''}
    ])

    function buscaCalendarios() 
    {
        try {
            limpaAlunos()
            loading.value = true
            let escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code
            routes.profissionais = routes.profissionais.replace('#codigoescola#', escola)
            window.axios.get(`${routes.calendarios}/${escola}/calendario`).then(retorno => {
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
            console.log('Erro '+e.message)
        }
    }
    function buscaProfissional() 
    {
        let cgm = form.value.assinaturaAdicional.codigo.value
        if (cgm !== null) {
            loading.value = true
            let escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code
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
    function buscaTurmas() 
    {
        try {
            limpaAlunos()
            loading.value = true
            const calendario = form.value.slctCalendarios.data.code
            window.axios.get(`${routes.turmas}/${calendario}`).then(retorno => {
                optionsTurmas.value = retorno.data.data.map(turma => {
                    return {
                        name: turma.ed57_c_descr,
                        code: turma.ed57_i_codigo
                    }
                })
                loading.value = false
            })
        } catch (e) {
            loading.value = false
            console.log('Erro '+e.message)
        }
    }
    function buscaSecretarios() 
    {
        try {
            loading.value = true
            let escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code
            window.axios.get(`${routes.secretarios}/${escola}`).then(retorno => {
                optionsSecretarios.value = []
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
            console.log('Erro '+e.message)
        }
    }
    function buscaDiretores() 
    {
        try {
            loading.value = true
            let escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code
            window.axios.get(`${routes.diretores}/${escola}/diretores`).then(retorno => {
                optionsDiretores.value = []
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
            console.log('Erro '+e.message)
        }
    }
    function buscaEscolas() 
    {
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
            console.log('Erro '+e.message)
        }
    }
    function buscaAlunos() 
    {
        form.value.alunos = []
        try {
            loading.value = true
            const escola = !isSecretaria.value == true ? props.escola : form.value.slctEscolas.data.code
            const calendario = form.value.slctCalendarios.data.code
            const turma = form.value.slctTurmas.data.code
            let msg = 'Nenhum aluno foi classificado na turma informada.'
            let rota_alunos = `${routes.alunosPorTurma}/${form.value.slctTurmas.data.code}`
            if (form.value.slctTipos.data.code == CODIGO_ATA_CLASSIFICACAO) {
                rota_alunos = `${routes.alunosClassificacao}/${calendario}/${turma}`
                msg = 'Nenhum aluno foi classificado no calendário e turma informados.'
            } else if (form.value.slctTipos.data.code == CODIGO_ATA_RECLASSIFICACAO) {
                rota_alunos = `${routes.alunosReclassificacao}/${escola}/${calendario}/${turma}`
                msg = 'Nenhum aluno foi (Re)classificado na escola, calendário e turma informados.'
            } else if (form.value.slctTipos.data.code == CODIGO_ATA_AVANCO) {
                rota_alunos = `${routes.alunosAvanco}/${calendario}/${turma}`
                msg = 'Nenhum aluno localizado na escola, calendário e turma informados.'
            }
            window.axios.get(rota_alunos).then(retorno => {
                if (retorno.data.data.length == 0) {
                    toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: msg,
                        life: 5000
                    });
                    loading.value = false
                    return false
                }
                form.value.alunos.push(retorno.data.data)
                form.value.alunos.push([])
                loading.value = false
                buscaSecretarios()
            })
        } catch (e) {
            loading.value = false
            console.log('Erro '+e.message)
        }
    }
    function limpaAlunos() 
    {
        form.value.alunos = []
        form.value.slctTipos.data = null
    }
    function selecionaProfissional(e) 
    {
        form.value.assinaturaAdicional.nome.value = e.nome
        optionsAtividades.value = e.atividades.map(atividade => {
            return {code: atividade.codigo, name: atividade.nome}
        })
        form.value.assinaturaAdicional.codigo.value = e.profissional.codigo
    }
    function openModal() 
    {
        toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: `Nenhum Aluno encontrado para esses filtros.`,
            life: 5000
        });
    }
    function buscaModelosRelatorioPorTipo() 
    {
        try {
            loading.value = true;
            window.axios.get(`${routes.modelos_atas}`).then(retorno => {
                retorno.data.data.forEach(modelo => {
                    optionsModelosAta.value.push({
                       name: `${modelo.ed217_c_nome}`,
                       code: modelo.ed217_i_codigo
                    })

                    const obj = {}
                    obj.codigo = modelo.ed217_i_codigo
                    obj.nome = modelo.ed217_c_nome.trim()
                    obj.exibir_assinatura_secretario = modelo.ed217_exibir_assinatura_secretario
                    obj.exibir_assinatura_adicional = modelo.ed217_exibir_assinatura_adicional
                    obj.exibir_grade_alunos = modelo.ed217_exibir_grade_alunos
                    obj.exibir_coluna_disciplinas = modelo.ed217_exibir_coluna_disciplinas
                    obj.exibir_coluna_resultado_final = modelo.ed217_exibir_coluna_resultado_final
                    obj.exibir_brasao = modelo.ed217_exibir_brasao
                    obj.disposicao_brasao = modelo.ed217_disposicao_brasao.trim()
                    obj.disposicao_cabecalho = modelo.ed217_disposicao_cabecalho.trim()
                    obj.brasao_cabecalho = modelo.ed217_brasao
                    definicoesModeloAta.value.push(obj) 
               })

               if (definicoesModeloAta.value.length == 1) {
                form.value.slctModelosAta.data = optionsModelosAta.value[0]
                setTimeout(function(){ aplicaDefinicoesAta(); }, 500)
               }
            })
            loading.value = false;
            
        } catch (e) {
            console.log('Erro '+e.message)
            loading.value = false;
        }
    }
    function aplicaDefinicoesAta() 
    {
        try {
            const definicoesAtaCorrente = definicoesModeloAta.value.filter((def) => def.codigo == form.value.slctModelosAta.data.code)
            if (definicoesAtaCorrente.length != 1) {
                return false;
            }

            definicoesModelo.value.exibir_assinatura_secretario = definicoesAtaCorrente[0].exibir_assinatura_secretario
            definicoesModelo.value.exibir_assinatura_adicional = definicoesAtaCorrente[0].exibir_assinatura_adicional
            definicoesModelo.value.exibir_grade_alunos = definicoesAtaCorrente[0].exibir_grade_alunos
            definicoesModelo.value.exibir_coluna_disciplinas = definicoesAtaCorrente[0].exibir_coluna_disciplinas
            definicoesModelo.value.exibir_coluna_resultado_final = definicoesAtaCorrente[0].exibir_coluna_resultado_final
            definicoesModelo.value.exibir_brasao = definicoesAtaCorrente[0].exibir_brasao
            definicoesModelo.value.disposicao_brasao = definicoesAtaCorrente[0].disposicao_brasao
            definicoesModelo.value.disposicao_cabecalho = definicoesAtaCorrente[0].disposicao_cabecalho            
            definicoesModelo.value.brasao_cabecalho = definicoesAtaCorrente[0].brasao_cabecalho           
        } catch (e) {
            console.log('Erro '+e.message)
        }
    }
    function validaFiltros() 
    {
        if (form.value.slctTipos.data.code == CODIGO_ATA_GERAL 
            && form.value.texto.data == null
        ) {
            toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: `Para a Ata Geral é preciso preencher o campo texto.`,
            life: 5000
            });
            return false
        }

        if (form.value.dataEmissao.data == null) {
            toast.add({
            severity: 'warn',
            summary: 'Atenção',
            detail: `A data de emissão está vazia.`,
            life: 5000
            });
            return false
        }
        return true
    }
    function emitir() 
    {
        if (!validaFiltros()) {
            return false
        }

        let turma = form.value.slctTurmas.data.code
        let turmaNome = form.value.slctTurmas.data.name === null ? '' : btoa(form.value.slctTurmas.data.name)
        let alunos = form.value.alunos[1].map(aluno => aluno.codigo)
        alunos = alunos.join()
        let observacao = form.value.textAreaObservacao.data === null ? '' : encodeURIComponent(form.value.textAreaObservacao.data)
        let escola = form.value.slctEscolas.data == null ? props.escola : form.value.slctEscolas.data.code
        let escolaNome = '';
        if (!isSecretaria.value) {
            escolaNome = btoa(props.nome_escola);
        } else {
            escolaNome = btoa(form.value.slctEscolas.data.name);
        }
        let assinaturaAdicional = form.value.assinaturaAdicional.codigo.value === null ?
            "" :
            form.value.assinaturaAdicional.codigo.value
        let secretarioNome =  form.value.slctSecretarios.data === undefined
            ? ''
            : btoa(form.value.slctSecretarios.data.name)

        let diretorNome =  form.value.slctDiretores.data === undefined
            ? ''
            : btoa(form.value.slctDiretores.data.name)
        
        let atividade = form.value.slctAtividades.data === null ? '' : form.value.slctAtividades.data.code
        let dataEmissao = form.value.dataEmissao.data === null 
            ? (new Date()).toLocaleDateString('pt-BR', {timeZone: 'UTC'}) 
            : form.value.dataEmissao.data

        let exibirAssinaturaSecretario = definicoesModelo.value.exibir_assinatura_secretario
        let exibirAssinaturaAdicional = definicoesModelo.value.exibir_assinatura_adicional == true ? 't' : 'f'
        const isSecreataria = isSecretaria.value == true ? 't' : 'f'
        const texto = form.value.texto.data === null ? '' : encodeURIComponent(form.value.texto.data)
        let parametros = [
            `calendario=${form.value.slctCalendarios.data.code}`,
            `escola=${escola}`,
            `turma=${turma}`,
            `turmaNome=${turmaNome}`,
            `alunos=${alunos}`,
            `exibirAssinaturaSecretario=${exibirAssinaturaSecretario}`,
            `exibirAssinaturaAdicional=${exibirAssinaturaAdicional}`,
            `secretarioNome=${secretarioNome}`,
            `diretorNome=${diretorNome}`,
            `iAtividade=${atividade}`,
            `iAssinaturaAdicional=${assinaturaAdicional}`,
            `sObs=${observacao}`,
            `dataEmissao=${dataEmissao}`,
            `modeloAtaId=${form.value.slctModelosAta.data.code}`,
            `moduloSecretaria=${isSecreataria}`,
            `textoRelatorio=${texto}`
        ]

        let rota = ''
        let nomeRelatorio = ''
        if (form.value.slctTipos.data.code == CODIGO_ATA_GERAL) {
            rota = routes.caminihoAtaGeral
            nomeRelatorio = 'Ata Geral'
        } else if (form.value.slctTipos.data.code == CODIGO_ATA_CLASSIFICACAO 
            || form.value.slctTipos.data.code == CODIGO_ATA_AVANCO
        ) {
            rota = routes.caminihoAtaClassificacao
            nomeRelatorio = 'Ata de Classificação'
        } else {
            rota = routes.caminihoAtaReclassificacao
            nomeRelatorio = 'Ata de Reclassificação'
        }
        download.value.addFile(
            `${rota}${parametros.join('&')}`,
            nomeRelatorio
        )
        download.value.openModal();
    }
    
    form.value.slctSecretarios.data = optionsSecretarios.value[0]
    form.value.slctDiretores.data = optionsDiretores.value[0]
    buscaModelosRelatorioPorTipo()
    props.modulo == MODULO_SECRETARIA && buscaEscolas()
    props.modulo != MODULO_SECRETARIA && buscaCalendarios()

    form.value.dataEmissao.data = (new Date()).toLocaleDateString('pt-BR', {timeZone: 'UTC'})
</script>

<template>
    <section class="container">
        <Panel header="Relatório - Turmas - Emissão de Atas">
            <div class="card">
                <br>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-3">
                    </div>
                    <div class="field col-12 md:col-6">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctModelosAta.data"
                            :options="optionsModelosAta" optionLabel="name" @change="aplicaDefinicoesAta()" />
                            <label for="dropdown">{{ form.slctModelosAta.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-3">
                    </div>
                </div>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-3" v-show="isSecretaria">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctEscolas.data" filter
                            :options="optionsEscolas" optionLabel="name" @change="buscaCalendarios" />
                            <label for="dropdown">{{ form.slctEscolas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-3">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctCalendarios.data"
                            :disabled="form.slctModelosAta.data == null || (form.slctEscolas.data == null && isSecretaria.value)"
                            :options="optionsCalendarios" optionLabel="name" @change="buscaTurmas"/>
                            <label for="dropdown">{{ form.slctCalendarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-6 md:col-3">
                        <span class="p-float-label">
                            <Dropdown id="dropdown" v-model="form.slctTurmas.data" :options="optionsTurmas"
                            :disabled="form.slctModelosAta.data == null || (form.slctEscolas.data == null && isSecretaria.value) || form.slctCalendarios.data == null"
                            @change="limpaAlunos"
                            optionLabel="name" />
                            <label for="dropdown">{{ form.slctTurmas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-3">
                        <span class="p-float-label">
                            <Dropdown id="modelos"  :options="optionsTipos"
                            optionLabel="name" @change="buscaAlunos"
                            :disabled="form.slctModelosAta.data == null || (form.slctEscolas.data == null && isSecretaria.value) || form.slctCalendarios.data == null || form.slctTurmas.data == null"
                            v-model="form.slctTipos.data" />
                            <label for="">{{ form.slctTipos.label }}</label>
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
                    <div class="field col-12 md:col-3">
                          <span class="p-float-label">
                            <Dropdown id="secretarios"  :options="optionsSecretarios"
                                      optionLabel="name" v-show="definicoesModelo.exibir_assinatura_secretario"
                                      v-model="form.slctSecretarios.data" />
                             <label for="" v-show="definicoesModelo.exibir_assinatura_secretario">{{ form.slctSecretarios.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-3">
                          <span class="p-float-label">
                            <Dropdown id="diretores"  :options="optionsDiretores"
                                      optionLabel="name"
                                      v-model="form.slctDiretores.data" />
                             <label for="">{{ form.slctDiretores.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <div class="p-inputgroup flex-1" v-show="definicoesModelo.exibir_assinatura_adicional">
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
                    <div class="field col-12 md:col-2">
                        <span class="p-float-label" v-show="definicoesModelo.exibir_assinatura_adicional">
                            <Dropdown id="atividades"  :options="optionsAtividades"
                                    optionLabel="name"
                                    v-model="form.slctAtividades.data" />
                            <label for="">{{ form.slctAtividades.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-12">
                        <span class="p-float-label">
                        <Textarea v-model="form.texto.data" rows="5" cols="5" :placeholder="form.texto.label"
                        :disabled="form.slctTipos.data != null && form.slctTipos.data.code != CODIGO_ATA_GERAL" />
                            <label>{{ form.texto.label }}</label>
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
                    <div class="field col-12 md:col-3">
                    </div>
                    <div class="field col-12 md:col-1">
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Calendar inputId="dateformat"
                                      v-model="form.dataEmissao.data"
                                      dateFormat="dd/mm/yy"/>
                            <label for="dateformat">{{ form.dataEmissao.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-1">
                    </div>
                    <div class="field col-12 md:col-3">
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
