<template>
    <section class="container">
        <Panel header="Relatório Histórico Escolar">
            <div class="card">
                <br>
                <div class="p-fluid grid" v-show="aluno !== ''">
                    <div class="field col-12 md:col-4">
                    </div>
                    <div class="field col-12 md:col-4" >
                    <span class="p-float-label">
                        <InputText id="anoLimite"
                                   v-model="form.alunoIndividual.data" disabled />
                        <label for="">{{ form.alunoIndividual.label }}</label>
                    </span>
                    </div>
                    <div class="field col-12 md:col-4">
                    </div>
                </div>
                <div class="p-fluid grid" v-show="isSecretaria">
                    <div class="field col-12 md:col-4">
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label" v-show="aluno === ''">
                            <Dropdown id="slctEscolas"
                                      v-model="form.slctEscolas.data"
                                      :options="escolas"
                                      optionLabel="name"
                                      @change="buscaAlunos"/>
                            <label for="slctEscolas">{{ form.slctEscolas.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                    </div>
                </div>
                <br>
                <div class="p-fluid grid">
                    <div class="field col-12 md:col-4" v-show="aluno === ''">
                        <span class="p-float-label">
                            <Dropdown id="slctTipoVinculo"
                                      v-model="form.slctTipoVinculo.data"
                                      :options="tiposVinculo"
                                      optionLabel="name"
                                      @change="buscaAlunos"/>
                            <label for="slctTipoVinculo">{{ form.slctTipoVinculo.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label" v-show="aluno === ''">
                            <Dropdown id="slctAnosCursos"
                                      v-model="form.slctAnosCursos.data"
                                      :options="anosCursos"
                                      optionLabel="name"
                                      @change="buscaAlunos"/>
                            <label for="slctAnosCursos">{{ form.slctAnosCursos.label }}</label>
                        </span>
                    </div>
                    <div class="field col-12 md:col-4">
                        <span class="p-float-label">
                            <Dropdown id="slctCurso"
                                      v-model="form.slctCurso.data"
                                      :options="cursos"
                                      optionLabel="name"
                                      @change="buscaAlunos"/>
                            <label for="curso">{{ form.slctCurso.label }}</label>
                        </span>
                    </div>
                </div>
            </div>
            <br>
            <div v-show="aluno === ''">
                <PickList id="list" v-model="alunos" listStyle="height:342px" dataKey="ed47_i_codigo">
                    <template #item="slotProps">
                        <div class="alunos-item">
                            <div class="alunos-list-detail">
                                <li class=""><small>
                                    {{ slotProps.item.ed47_v_nome }} - {{ slotProps.item.ed47_i_codigo }}
                                </small></li>
                            </div>
                        </div>
                    </template>
                </PickList>
            </div>

            <br> <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                       <Dropdown id="slctTipoModelo"
                                 v-model="form.slctTipoModelo.data"
                                 :options="modelos"
                                 optionLabel="name"
                                 @change="setaOrientacao"/>
                        <label for="">{{ form.slctTipoModelo.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Dropdown id="slctTipoRegistro"
                                  v-model="form.slctTipoRegistro.data"
                                  :options="tiposRegistro"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctTipoRegistro.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Calendar inputId="dateformat"
                                  v-model="form.dataEmissao.data"
                                  dateFormat="dd-mm-yy"/>
                        <label for="">{{ form.dataEmissao.label }}</label>
                    </span>
                    <small>* Em branco emite a data atual</small>
                </div>
            </div>
            <div class="p-fluid grid" v-show="this.form.slctTipoModelo.data.orientation == 2">
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                       <Dropdown id="slctOrientacao"
                                 v-model="form.slctOrientacao.data"
                                 :options="orientacao"
                                 optionLabel="name"
                                 :disabled="form.slctOrientacao.disabled"/>
                        <label for="">{{ form.slctOrientacao.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Dropdown id="slctEtapas"
                                  v-model="form.slctEtapas.data"
                                  :options="etapas"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctEtapas.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Dropdown id="slctDisposicao"
                                  v-model="form.slctDisposicao.data"
                                  :options="disposicoes"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctDisposicao.label }}</label>
                    </span>
                </div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Dropdown id="slctSecretarios"
                                  v-model="form.slctSecretarios.data"
                                  :options="secretarios"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctSecretarios.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Dropdown id="slctDiretores"
                                  v-model="form.slctDiretores.data"
                                  :options="diretores"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctDiretores.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText id="anoLimite"
                                   v-model="form.anoLimite.data"/>
                        <label for="">{{ form.anoLimite.label }}</label>
                    </span>
                    <small>* Em branco emite todos</small>
                </div>
            </div>
            <div class="p-fluid grid" v-show="this.exibeReclassificacao">
                <div class="field col-12 md:col-4"></div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <Dropdown id="slctReclassificacao"
                                  v-model="form.slctReclassificacao.data"
                                  :options="reclassificao"
                                  optionLabel="name"/>
                        <label for="">{{ form.slctReclassificacao.label }}</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4"></div>
            </div>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4"></div>
                <div class="field col-12 md:col-4">
                    <Button class="p-button" label="Processar" @click.prevent="emitir"></Button>
                </div>
                <div class="field col-12 md:col-4"></div>
            </div>
            <div class="text-center" v-show="aluno === ''">
                Para selecionar mais de um aluno mantenha pressionada a tecla <kbd>CTRL</kbd> e clique sobre o nome dos
                alunos.
            </div>
        </Panel>
    </section>

    <ModalLoading :isLoading="loading"/>
    <MultiDownload :header="'Arquivos para Download'" :position="'center'" ref="download"/>
    <Toast/>
</template>

<script>
import ModalLoading from "../../../../Components/ModalLoading.vue"
import MultiDownload from "../../../../Components/MultiDownload.vue"
import Toast from 'primevue/toast';

export default {
    props: {
        departamento: String,
        modulo: String,
        aluno: String

    },
    components: {
        MultiDownload,
        ModalLoading,
        Toast
    },
    data() {
        return {
            apiEscola: 'v4/api/educacao/escola',
            apiSecretaria: 'v4/api/educacao/secretaria',
            isSecretaria: Boolean,
            escola: String,
            exibeReclassificacao: Boolean,
            windowURL: window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g)[0],
            routes: {},
            form: {
                slctReclassificacao: {
                    label: 'Exibir Reclassificação',
                    required: true,
                    data: []
                },
                slctCurso: {
                    label: 'Curso',
                    required: true,
                    data: []
                },
                slctEscolas: {
                    label: 'Escola',
                    required: true,
                    data: []
                },
                slctTipoVinculo: {
                    label: 'Tipo de Vinculo',
                    required: true,
                    data: []
                },
                slctAnosCursos: {
                    label: 'Ultimo Ano Cursado',
                    required: true,
                    data: []
                },
                slctOrientacao: {
                    label: 'Orientação',
                    required: true,
                    data: [],
                    disabled: true
                },
                slctTipoModelo: {
                    label: 'Tipo de Modelo',
                    required: true,
                    data: []
                },
                slctTipoRegistro: {
                    label: 'Registros',
                    required: true,
                    data: []
                },
                slctDisposicao: {
                    label: 'Disposição do Cabeçalho',
                    required: true,
                    data: []
                },
                slctEtapas: {
                    label: 'Exibir Etapas',
                    required: true,
                    data: []
                },
                slctDiretores: {
                    label: 'Diretor',
                    required: false,
                    data: []
                },
                slctSecretarios: {
                    label: 'Secretário',
                    required: false,
                    data: []
                },
                anoLimite: {
                    label: 'Ano Limite da Emissão',
                    required: false,
                    data: null
                },
                alunoIndividual: {
                    label: 'Aluno',
                    required: false,
                    data: null
                },
                dataEmissao: {
                    label: 'Data Emissão',
                    required: false,
                    data: null
                }
            },
            reclassificao: [
                {name: 'Não', code: 'f'},
                {name: 'Sim', code: 't'}
            ],
            tiposRegistro: [
                {name: 'Selecione', code: null},
                {name: 'Etapas APROVADAS', code: 'A'},
                {name: 'Etapas APROVADAS e REPROVADAS', code: 'AR'},
                {name: 'Listar Último Registro', code: 'U'},
            ],
            disposicoes: [
                {name: 'Selecione', code: null},
                {name: 'Disposição 1', code: 1},
                {name: 'Disposição 2', code: 2}
            ],
            etapas: [
                {name: 'Selecione', code: null},
                {name: 'Somente Etapas Registradas', code: 1},
                {name: 'Todas Etapas do Curso', code: 2}
            ],
            diretores: [{name: 'Selecione', code: null}],
            secretarios: [{name: 'Selecione', code: null}],
            modelos: [{name: 'Selecione', code: null}],
            anosCursos: [{name: 'Todos', code: null}],
            cursos: [{name: 'Todos', code: null}],
            alunos: [],
            tiposVinculo: [{name: 'Selecione', code: null}],
            escolas: [{name: 'Selecione', code: null}],
            orientacao: null,
            loading: false
        }
    },
    methods: {
        async buscaAlunos() {
            if (this.aluno !== '') {
                return;
            }
            this.loading = true;
            this.alunos = [];
            this.escola = this.isSecretaria ? this.form.slctEscolas.data.code : this.departamento;
            await this.buscaParametros();
            this.buscaTiposVinculos();
            await this.buscaCursos();
            await this.buscaAnosCalendario();
            await this.buscaDiretores();
            await this.buscaSecretarios();

            let parametros = {};
            parametros.escola = this.escola;
            if (this.form.slctTipoVinculo.data != null && this.form.slctTipoVinculo.data.code != 0) {
                parametros.tipoVinculo = this.form.slctTipoVinculo.data.code;
            }
            if (this.form.slctAnosCursos.data != null && this.form.slctAnosCursos.data.code != 0) {
                parametros.ano = this.form.slctAnosCursos.data.code;
            }
            if (this.form.slctCurso.data != null && this.form.slctCurso.data.code != 0) {
                parametros.curso = this.form.slctCurso.data.code;
            }

            if (this.form.slctTipoVinculo.data.code == 3) {
                try {
                    const resp = await window.axios.post(
                        `v4/api/educacao/escola/alunos/historicos-alunos-transf-fora`, parametros
                    );
                    this.loading = false;
                    if (resp.data.data.length == 0) {
                        this.$toast.add({
                            severity: 'warn',
                            summary: 'Atenção',
                            detail: `Nenhum Aluno encontrado para esses filtros.`,
                            life: 5000
                        });
                        return;
                    }
                    this.alunos.push(resp.data.data);
                    this.alunos.push([]);
                } catch (e) {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: `${e.response.data.message}`,
                        life: 5000
                    });
                    this.loading = false;
                }
            } else {
                if (this.form.slctTipoVinculo.data.code == null) {
                    this.form.slctAnosCursos.data = this.anosCursos[0];
                    this.form.slctCurso.data = this.cursos[0];
                    return;
                }
                try {
                    const resp = await window.axios.post(
                        `v4/api/educacao/escola/alunos/historicos-por-escola`, parametros
                    );
                    this.loading = false;

                    if (resp.data.data.length == 0) {
                        this.$toast.add({
                            severity: 'warn',
                            summary: 'Atenção',
                            detail: `Nenhum Aluno encontrado para esses filtros.`,
                            life: 5000
                        });
                        return;
                    }
                    this.alunos.push(resp.data.data);
                    this.alunos.push([]);
                } catch (e) {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: `${e.response.data.message}`,
                        life: 5000
                    });
                    this.loading = false;
                }

            }
        },
        async buscaAluno() {
            await this.buscaDiretores();
            await this.buscaSecretarios();
            await this.buscaParametros();
            const resp = await window.axios.get(
                `v4/api/educacao/escola/alunos/${this.aluno}`
            );
            this.loading = false;
            if (resp.data.data.length == 0) {
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: `Nenhum Aluno encontrado para esses filtros.`,
                    life: 5000
                });
                return;
            }
            this.alunos.push([]);
            this.alunos.push([{
                ed47_i_codigo: resp.data.data.ed47_i_codigo,
                ed47_v_nome: resp.data.data.ed47_v_nome
            }]);
            this.form.alunoIndividual.data = resp.data.data.ed47_v_nome;
            this.form.slctTipoVinculo.required = false
            resp.data.data.cursos.map(curso => {
                this.cursos.push({name: curso.ed29_c_descr, code: curso.ed29_i_codigo});
            })
        },
        async buscaAnosCalendario() {
            if (this.anosCursos.length == 1) {
                if (!this.isSecretaria) {
                    this.loading = true;
                    try {
                        const resp = await window.axios.get(
                            `v4/api/educacao/escola/${this.escola}/calendario`
                        );
                        this.loading = false;
                        var anos = resp.data.data.map(calendario => {
                            return calendario.ano;
                        })
                        anos = [...new Set(anos)];
                        anos.map(ano => {
                            this.anosCursos.push({name: ano, code: ano});
                        })
                        return;
                    } catch (e) {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Erro',
                            detail: `${e.response.data.message}`,
                            life: 5000
                        });
                        this.loading = false;
                    }
                }
            }
            if (this.isSecretaria) {
                this.anosCursos = [{name: 'Todos', code: 0}];
                this.loading = true;
                try {
                    const resp = await window.axios.get(
                        `v4/api/educacao/escola/${this.escola}/calendario`
                    );
                    this.loading = false;
                    var anos = resp.data.data.map(calendario => {
                        return calendario.ano;
                    })
                    anos = [...new Set(anos)];
                    anos.map(ano => {
                        this.anosCursos.push({name: ano, code: ano});
                    })
                } catch (e) {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: `${e.response.data.message}`,
                        life: 5000
                    });
                    this.loading = false;
                }

            }
        },
        async buscaCursos() {
            if (this.cursos.length == 1) {
                if (!this.isSecretaria) {
                    this.loading = true;

                    try {
                        const resp = await window.axios.get(
                            `v4/api/educacao/escola/${this.escola}/cursos`
                        );
                        this.loading = false;
                        resp.data.data.map(curso => {
                            this.cursos.push({name: curso.nome, code: curso.codigo});
                        })
                        return;
                    } catch (e) {
                        this.$toast.add({
                            severity: 'error',
                            summary: 'Erro',
                            detail: `${e.response.data.message}`,
                            life: 5000
                        });
                        this.loading = false;
                    }
                }
            }
            if (this.isSecretaria) {
                this.cursos = [{name: 'Todos', code: 0}];
                this.loading = true;

                try {
                    const resp = await window.axios.get(
                        `v4/api/educacao/escola/${this.escola}/cursos`
                    );
                    this.loading = false;
                    resp.data.data.map(curso => {
                        this.cursos.push({name: curso.ed29_c_descr, code: curso.ed29_i_codigo});
                    })
                } catch (e) {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: `${e.response.data.message}`,
                        life: 5000
                    });
                    this.loading = false;
                }
            }
        },
        async buscaEscolas() {
            this.loading = true;

            try {
                const resp = await window.axios.get(
                    `v4/api/educacao/escola/`
                );
                this.loading = false;
                resp.data.data.map(escola => {
                    this.escolas.push({name: escola.ed18_c_nome, code: escola.ed18_i_codigo});
                })
            } catch (e) {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
                this.loading = false;
            }
        },
        buscaTiposVinculos() {
            this.tiposVinculo = [{name: 'Selecione', code: null}];
            this.tiposVinculo.push({name: 'Alunos vinculados nesta escola', code: 1});
            this.tiposVinculo.push({name: 'Alunos sem vínculos com escolas', code: 2});
            this.tiposVinculo.push({name: 'Alunos transferidos na rede', code: 3});
        },
        async buscaModelosRelatorio() {
            this.loading = true;

            try {
                const resp = await window.axios.get(
                    `v4/api/educacao/secretaria/modelos-relatorio/getModelosHistorico`
                );
                this.loading = false;
                resp.data.data.map(modelo => {
                    this.modelos.push(
                        {name: modelo.ed217_c_nome, code: modelo.ed217_i_codigo, orientation: modelo.orientacao}
                    );
                })
            } catch (e) {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
                this.loading = false;
            }
        },
        setaOrientacao() {
            if (this.form.slctTipoModelo.data.orientation != null) {
                let name = this.form.slctTipoModelo.data.orientation == 2 ? 'Retrato' : 'Paisagem';
                this.orientacao = [{
                    name: name,
                    code: this.form.slctTipoModelo.data.orientation
                }];
                this.form.slctOrientacao.data = this.orientacao[0];
                if (this.form.slctOrientacao.data.code == 1) {
                    this.form.slctEtapas.required = false;
                    this.form.slctDisposicao.required = false;
                }
            }
        },
        async buscaDiretores() {
            this.diretores = [{name: 'Selecione', code: null}];
            this.loading = true;
            try {
                const resp = await window.axios.get(
                    `v4/api/educacao/escola/${this.escola}/diretores`
                );
                this.loading = false;
                resp.data.data.map(diretor => {
                    var atoLegal = diretor.numero_ato_legal != null ?
                        ` - ${diretor.descricao_tipo_ato_legal} nº ${diretor.numero_ato_legal}` :
                        '';
                    this.diretores.push(
                        {name: `DIRETOR - ${diretor.nome} ${atoLegal}`, code: diretor.codigo_rechumano}
                    )
                })
            } catch (e) {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
                this.loading = false;
            }
        },
        async buscaSecretarios() {
            this.secretarios = [{name: 'Selecione', code: null}];
            this.loading = true;

            try {
                const resp = await window.axios.get(
                    `v4/api/educacao/escola/secretarios/${this.escola}`
                );
                this.loading = false;
                resp.data.data.map(secretario => {
                    this.secretarios.push(
                        {name: `SECRETÁRIO(A) - ${secretario.nome}`, code: secretario.codigo_rechumano}
                    )
                })
            } catch (e) {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
                this.loading = false;
            }
        },
        async buscaParametros() {
            try {
                const resp = await window.axios.get(
                    `${this.routes.parametros}${this.escola}`
                );
                this.exibeReclassificacao = resp.data.data[0] != undefined ?
                    resp.data.data[0].ed233_reclassificaetapaanterior
                    : false;
            } catch (e) {
                this.$toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
            }
        },
        emitir() {
            var unfilledFields = 0;
            Object.values(this.form).map(element => {
                if (element.required) {
                    if (element.data == null || element.data.length == 0 || element.data.code == null) {
                        unfilledFields++;
                        this.$toast.add({
                            severity: 'warn',
                            summary: 'Atenção',
                            detail: `Campo ${element.label} deve ser preenchido!`,
                            life: 4000
                        });
                    }
                }
            })
            if (unfilledFields > 0) {
                return;
            }

            if (this.alunos[1].length == 0) {
                this.$toast.add({
                    severity: 'warn',
                    summary: 'Atenção',
                    detail: `Selecione pelo menos um aluno!`,
                    life: 4000
                });
                return;
            }
            let alunos = this.alunos[1].map(aluno => {
                return aluno.ed47_i_codigo;
            })
            let data = this.form.dataEmissao.data == null ? "" : this.form.dataEmissao.data.toLocaleDateString();
            let curso = this.form.slctCurso.data.code == null ? "" : this.form.slctCurso.data.code;
            let ano = this.form.anoLimite.data == null ? "" : this.form.anoLimite.data;
            let todasEtapas = this.form.slctEtapas.data.code == null ? "" : this.form.slctEtapas.data.code;
            let disposicao = this.form.slctDisposicao.data.code == null ? "" : this.form.slctDisposicao.data.code;
            let secretario = this.form.slctSecretarios.data.code == null ? "" : this.form.slctSecretarios.data.name;
            let diretor = this.form.slctDiretores.data.code == null ? "" : this.form.slctDiretores.data.name;
            let parametros = [
                `alunos=${alunos.join()}`,
                `iTipoRelatorio=${this.form.slctTipoModelo.data.code}`,
                `iTipoRegistro=${this.form.slctTipoRegistro.data.code}`,
                `sDiretor=${diretor}`,
                `sSecretario=${secretario}`,
                `&iEscola=${this.escola}`,
                `sDisposicao=${disposicao}`,
                `iExibirTodasEtapas=${todasEtapas}`,
                `sExibirReclassificacao=${this.form.slctReclassificacao.data.code}`,
                `sAno=${ano}`,
                `dataemissao=${data}`,
                `iCurso=${curso}`
            ]

            if (this.form.slctOrientacao.data.code == 2) {
                this.$refs['download'].addFile(
                    `${this.routes.emiteRetrato}${parametros.join('&')}`,
                    this.form.slctTipoModelo.data.name
                )
                this.$refs['download'].openModal();
            } else {
                this.$refs['download'].addFile(
                    `${this.routes.emitePaisagem}${parametros.join('&')}`,
                    this.form.slctTipoModelo.data.name
                )
                this.$refs['download'].openModal();
            }
        }
    },
    watch: {},
    async mounted() {
        this.routes.emiteRetrato = `${this.windowURL}/edu2_historicoescolarretrato002.php?`;
        this.routes.emitePaisagem = `${this.windowURL}/edu2_historico002.php?`;
        this.routes.parametros = `${this.apiEscola}/parametros/`;
        this.cursos = [{name: 'Todos', code: 0}];
        this.anosCursos = [{name: 'Todos', code: 0}];
        this.isSecretaria = this.modulo == 7159;
        this.escola = this.isSecretaria ? "" : this.departamento;
        this.form.slctEscolas.required = this.isSecretaria;
        this.form.slctTipoVinculo.data = this.tiposVinculo[0];
        this.form.slctAnosCursos.data = this.anosCursos[0];
        this.form.slctCurso.data = this.cursos[0];
        this.form.slctEtapas.data = this.etapas[0];
        this.form.slctEscolas.data = this.escolas[0];
        this.form.slctTipoModelo.data = this.modelos[0];
        this.form.slctDisposicao.data = this.disposicoes[0];
        this.form.slctTipoRegistro.data = this.tiposRegistro[0];
        this.form.slctDiretores.data = this.diretores[0];
        this.form.slctSecretarios.data = this.secretarios[0];
        this.form.slctReclassificacao.data = this.reclassificao[0];
        await this.buscaModelosRelatorio();

        if (!this.isSecretaria) {
            if (this.aluno === '') {
                this.buscaAlunos();
            } else {
                this.buscaAluno()
            }

        } else {
            this.buscaEscolas();
        }
    }
}
</script>

<style scoped>
</style>
