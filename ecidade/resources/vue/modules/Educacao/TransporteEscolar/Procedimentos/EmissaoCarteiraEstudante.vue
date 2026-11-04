<template>
    <div class="container-wrapper">
        <Fieldset style="width: 50vw; margin: 0 auto 1rem;" legend="Filtros">
            <div class="card escola-wrapper">
                <Dropdown
                    filter
                    id="slctEscola"
                    v-model="form.slctEscola.data"
                    :options="escolas"
                    option-label="name"
                    placeholder="Selecione uma Escola"
                    @change="buscarLinhas">
                </Dropdown>
            </div>
            <div class="line">
            </div>
            <div v-show="isLinhas"  class="card escola-wrapper">
                <Dropdown
                    filter
                    id="slctLinha"
                    v-model="form.slctLinha.data"
                    :options="linhasTransporte"
                    option-label="name"
                    placeholder="Selecione uma Linha"
                    @change="buscarAlunos">
                </Dropdown>
            </div>
        </Fieldset>
        <div v-if="alunosVinculados.data.length > 0">
            <Fieldset style="width: 50vw; margin: 0 auto 1rem;" legend="Alunos">
                <div class="select-alunos">
                    <MultiSelect
                    id="selectAlunos"
                    v-model="form.alunosSelecionados"
                    :options="alunosVinculados.data"
                    filter
                    option-label="nomeAluno"
                    placeholder="Selecione os Alunos"
                    :maxSelectedLabels="1">
                    <template #value>
                        <span v-if="form.alunosSelecionados.length <= 0">Selecione os Alunos.</span>
                        <span v-else>
                            <b>{{ form.alunosSelecionados.length }}</b>
                            aluno{{ form.alunosSelecionados.length > 1 ? 's' : '' }}
                            selecionado{{ form.alunosSelecionados.length > 1 ? 's' : '' }}.
                        </span>
                    </template>
                    </MultiSelect>
                </div>
                <div style="text-align: center; margin-top: 1rem">
                    <Button icon="pi pi-print" label="Emitir Carteirinha" @click="emitirCarteira" />
                </div>
            </Fieldset>
        </div>
    </div>
    <Dialog header="Arquivos para Download" :modal="true"  closable v-model:visible="display"
            :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '30vw'}"
            @hide="closeModal">
        <div class="card">
            <div class="d-flex flex-column justify-content-center flex-wrap card-container gap-1 pt-1">
                <ul class="list-none p-0">
                    <li v-for="file in files" class="mt-2 text-xl">
                        <a id="link-download" :href="file.url" download>
                            <i :class="file.icon" class="m2" style="font-size: 2rem; color:#4a789c"></i>
                            <span class="font-weight-bold text-secondary">
                                {{ file.name }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </Dialog>
    <ModalLoading :is-loading="loading" :message="mensagemModal"/>
</template>
<script>
import ModalLoading from "@modules/Components/ModalLoading.vue"
import { ref } from 'vue';
import { useToast } from "primevue/usetoast";
import MultiDownload from "@modules/Components/MultiDownload.vue";

export default {
    components: {MultiDownload, ModalLoading},
    data() {
        return {
            alunosResponse: false,
            alunosVinculados: {
                data: []
            },
            arquivoCarregado: false,
            calendarios: [],
            codCalendarios: null,
            codigoEscola: null,
            display: false,
            files: [],
            linkArquivo: [{
                nome: null,
                path: null,
                pathExterno: null
            }],
            escolas: [{
                name: 'Selecione',
                code: null
            }],
            form: {
                slctEscola: {
                    label: 'Escola',
                    data: []
                },
                slctLinha: {
                    label: 'Linha de Transporte',
                    data: []
                },
                alunosSelecionados: ref([]),
            },
            isTurmasCarregadas: false,
            isLinhas: false,
            linhasTransporte: [{
                code: null,
                name: '',
                abreviatura: ''
            }],
            loading: false,
            mensagemModal: ref(null),
            mensagens: {
                escolas: 'Buscando Escolas...',
                linhas: 'Buscando Linhas de Transporte...',
                alunos: 'Buscando Alunos...',
                carteira: 'Emitindo a(s) Carteira(s)...',
            },
            routes: {
                escola: `v4/api/educacao/escola/`,
                buscaLinhas: `v4/api/educacao/transporte-escolar/busca-linhas/`,
                buscaAlunos: `v4/api/educacao/transporte-escolar/get-alunos-vinculados/`,
                emissao: `v4/api/educacao/transporte-escolar/emitir-carteira`
            },
            toast: useToast(),
        }
    },
    methods: {
        async buscarEscolas() {
            this.limparVariaveis('escola');
            this.mensagemModal = this.mensagens.escolas;
            this.loading = true;
            try {
                const response = await window.axios.get(this.routes.escola);
                this.loading = false;
                response.data.data.map(escola => {
                    this.escolas.push({
                        name: escola.ed18_c_nome,
                        code: escola.ed18_i_codigo
                    });
                });
            } catch (e) {
                this.toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
                this.loading = false;
            }
        },
        async buscarLinhas() {
            this.limparVariaveis('linhas');
            this.mensagemModal = this.mensagens.linhas;
            this.loading = true;
            this.codigoEscola = this.form.slctEscola.data.code;
            try {
                const response = await window.axios.post(this.routes.buscaLinhas + this.codigoEscola);
                response.data.data.map(linha => {
                   this.linhasTransporte.push({
                       code: linha.tre06_sequencial,
                       name: linha.tre06_nome,
                       abreviatura: linha.tre06_abreviatura
                   });
                });

                if (this.linhasTransporte.length > 0) {
                    this.isLinhas = true;
                } else {
                    this.toast.add({
                        severity: 'warn',
                        summary: 'Atenção',
                        detail: 'Não existem linhas cadastradas nessa escola.',
                        life: 10000
                    });
                }
                this.loading = false;
            } catch (e) {
                this.toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
                this.loading = false;
            }
        },
        async buscarAlunos() {
            this.limparVariaveis('alunos');
            this.mensagemModal = this.mensagens.alunos;
            const oParametro = {
                iLinha: this.form.slctLinha.data.code
            };
            try {
                this.loading = true;
                const response = await window.axios.post(this.routes.buscaAlunos, oParametro);
                response.data.data.aAlunos.map(aluno => {
                    this.alunosVinculados.data.push(aluno);
                });
                this.loading = false;
            } catch (e) {
                this.toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: `${e.response.data.message}`,
                    life: 5000
                });
                this.loading = false;
            }
            this.loading = false;
        },
        validador() {
            if (this.form.alunosSelecionados.length <= 0) {
                this.toast.add({
                    severity: 'error',
                    summary: 'Atenção!',
                    detail: 'Selecione ao menos um aluno!',
                    life: 10000
                });
                return false;
            }
            return true;
        },
        async emitirCarteira() {
            this.loading = true;
            this.mensagemModal = this.mensagens.carteira;
            if(this.validador()) {
                const params = {
                    escola: this.form.slctEscola.data.name,
                    linha: this.form.slctLinha.data.name,
                    alunos: this.form.alunosSelecionados
                }
                try {
                    const response = await window.axios.post(this.routes.emissao, params)
                    response.data.data.map(carteira => {
                        this.addFile(
                            `${carteira.pathExterno}`,
                            `${carteira.name}`
                        )
                    });
                    this.openModal();
                } catch (e) {
                    const errorMessage = e.response && e.response.data && e.response.data.message
                        ? e.response.data.message
                        : 'Ocorreu um erro ao emitir a carteira. Por favor, tente novamente mais tarde.';

                    this.toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: errorMessage,
                        life: 5000
                    });
                } finally {
                    this.loading = false;
                }
            }
        },
        limparVariaveis($chave) {
            switch ($chave) {
                case 'escola':
                    this.form.slctEscola.data = [];
                    this.form.slctLinha.data = [];
                    this.form.alunosSelecionados = [];
                    this.alunosVinculados.data = [];
                    this.linhasTransporte = [];
                    break;
                case 'linhas':
                    this.form.slctLinha.data = [];
                    this.linhasTransporte = [];
                    this.form.alunosSelecionados = [];
                    this.alunosVinculados.data = [];
                    break;
                case 'alunos':
                    this.form.alunosSelecionados = [];
                    this.alunosVinculados.data = [];
                    break;
            }
        },
        openModal() {
            this.display = true;
        },
        closeModal() {
            this.display = false;
            this.files = [];
        },
        addFile(url, name, icon = 'pi pi-file-pdf') {
            let extension = url.split('.').pop();
            switch (extension) {
                case 'pdf':
                    icon = 'pi pi-file-pdf';
                    break;

                case 'doc':
                case 'docx':
                case 'odt':
                    icon = 'pi pi-file-word';
                    break;

                case 'xlsx':
                case 'xls':
                case 'csv':
                case 'ods':
                    icon = 'pi pi-file';
                    break;
            }

            this.files.push({
                url: url,
                name: name,
                icon: icon
            });
        }
    },
    async mounted() {
        await this.buscarEscolas();
    }
}
</script>
<style lang="scss" scoped>

.container-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #E0E0E0;
    max-width: 70%;
    margin: 2rem auto;
    padding: 2rem;
    box-sizing: border-box;
    border-radius: 0.6rem;
}

.escola-wrapper,
.select-alunos {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tabela {
    max-width: 70%;
    margin-top: 1rem;
}

#slctEscola,
#slctLinha,
#selectAlunos {
    margin-bottom: 1rem;
    text-align: center;
    min-width: 60%;
    max-width: 85%;
}

#link-download {
    text-decoration: none;
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    justify-content: center;
    text-align: center;
    gap: 1rem;
}

</style>
