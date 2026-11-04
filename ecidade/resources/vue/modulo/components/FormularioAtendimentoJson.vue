<template>
    <div>
        <div style="text-align: center;margin-top: 20px;">
            <b>Filtrar por:</b>
            <i>(Matriculas)</i> <input type="radio"
                                       name="filtro"
                                       value="matricula"
                                       v-model="filtro"
                                       :checked="true">
            <i>(Nome)</i> <input type="radio"
                                 name="filtro"
                                 value="nome"
                                 v-model="filtro">
        </div>
        <div class="div-pesquisa">
            <div class="div-pesquisa-input-group"
                 v-if="filtro === 'matricula'">
                <div>
                    <b>Pesquisar Matriculas:</b>
                    <i style="color:blue">Exemplo: 121211,12312,213213</i>
                </div>
                <div>
                    <input v-model="searchFilter"
                           style="width: 350px;"
                    />
                </div>
            </div>
            <div class="div-pesquisa-input-group"
                 v-if="filtro === 'nome'">
                <b>Pesquisar Nome:</b>
                <input style="width: 350px;"
                       v-model="searchFilter"/>
            </div>

            <div class="div-pesquisa-input-group">
                <div class="div-button">
                    <button type="button"
                            style="background:#2c5676;color:#fff;border:1px solid #fff;height: 30px; border-radius: 5px"
                            @click="search"
                    >
                        <b>Pesquisar</b>
                    </button>
                </div>
            </div>

        </div>
        <table
            id="table"
        ></table>
        <div class="modal"
             v-if="open">
            <div class="modal-content">
                <a id="modal-close"
                   @click="toogle()">X</a>
                <SecaoComponent
                    v-for="(secao, index) in searchSecoes()"
                    :key="index"
                    :title="secao.label"
                >
                    <div>
                        <div class="campos"
                             v-if="secao.tipo !== 'tabela'">
                            <div v-for="(campo, index) in secao.campos"
                                 class="campo-grupo"
                                 :key="index">
                                <b>{{ campo.label }}</b>
                                <input
                                    v-model="campo.resposta"
                                    class="input"
                                    v-mask="campo.mascara.replaceAll('0','#')"
                                    :readonly="campo.bloquear"
                                    v-if="(campo.tipo === 'string'  || campo.tipo === 'inteiro') && campo.mascara"
                                />
                                <input v-model="campo.resposta"
                                       class="input"
                                       v-if="(campo.tipo === 'string' || campo.tipo === 'inteiro' ) && !campo.mascara"
                                       :readonly="campo.bloquear"
                                />
                                <input
                                    v-model="campo.resposta"
                                    v-mask="campo.mascara.replaceAll('0','#')"
                                    class="input"
                                    v-if="campo.tipo === 'cpfCnpj'"
                                    :readonly="campo.bloquear"
                                />
                                <input
                                    v-model="campo.resposta"
                                    class="input"
                                    v-if="campo.tipo === 'email'"
                                    :readonly="campo.bloquear"
                                />
                                <input
                                    v-model="campo.resposta"
                                    v-mask="campo.mascara.replaceAll('0','#')"
                                    class="input"
                                    v-if="campo.tipo === 'cpf'"
                                    :readonly="campo.bloquear"
                                />
                                <input v-model="campo.resposta"
                                       v-mask="campo.mascara.replaceAll('0','#')"
                                       class="input"
                                       v-if="campo.tipo === 'cnpj'"
                                />

                                <input v-model="campo.resposta"
                                       v-mask="'##/##/####'"
                                       class="input"
                                       v-if="campo.tipo === 'data'"
                                />
                                <input
                                    class="input"
                                    v-if="campo.tipo === 'autocomplete' || campo.tipo === 'lista_dinamica' "
                                    :value="campo.resposta ? campo.resposta.descricao :''"
                                    :readonly="true"
                                >
                                <select
                                    v-model="campo.resposta"
                                    class="input"
                                    v-if="campo.tipo === 'lista'"
                                >
                                    <option value="">--</option>
                                    <option v-for="(opcao,index) in  campo.opcoes"
                                            :value="opcao"
                                            :key="index">
                                        {{ opcao.descricao }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div v-if="secao.tipo === 'tabela'">
                            <div v-for="(resposta,index) in secao.resposta"
                                 class="campos"
                                 :key="index">
                                <div v-for="(campo,index) in secao.campos"
                                     class="campo-grupo"
                                     :key="index">
                                    <b>{{ campo.label }}</b>
                                    <input v-model="resposta[campo.nome]"
                                           class="input"
                                           v-mask="campo.mascara.replaceAll('0','#')"
                                           v-if="campo.tipo === 'string' && campo.mascara"
                                    >

                                    <input class="input"
                                           v-model="resposta[campo.nome]"
                                           v-if="(campo.tipo === 'string' || campo.tipo === 'inteiro' ) && !campo.mascara"
                                    >

                                    <input v-model="resposta[campo.nome]"
                                           v-mask="'###.###.###-##'"
                                           class="input"
                                           v-if="campo.tipo === 'cpfCnpj'"
                                    >

                                    <input v-model="resposta[campo.nome]"
                                           v-mask="'###.###.###-##'"
                                           class="input"
                                           v-if="campo.tipo === 'cpf'"
                                    >

                                    <input v-model="resposta[campo.nome]"
                                           v-mask="'##.###.###/####-##'"
                                           class="input"
                                           v-if="campo.tipo === 'cnpj'"
                                    >

                                    <input
                                        v-model="resposta[campo.nome]"
                                        class="input"
                                        v-if="campo.tipo === 'email'"
                                        :readonly="campo.bloquear"
                                    >

                                    <input
                                        class="input"
                                        v-model="resposta[campo.nome]"
                                        v-mask="'##/##/####'"
                                        v-if="campo.tipo === 'data'"
                                    >

                                    <input
                                        class="input"
                                        v-if="campo.tipo === 'autocomplete' || campo.tipo === 'lista_dinamica' "
                                        :value="resposta[campo.nome] ? resposta[campo.nome].descricao :''"
                                    >

                                    <select v-model="resposta[campo.nome]"
                                            class="input"
                                            v-if="campo.tipo === 'lista'"
                                    >
                                        <option value="">--</option>
                                        <option v-for="(opcao, index) in  campo.opcoes"
                                                :value="opcao"
                                                :key="index">
                                            {{ opcao.descricao }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </SecaoComponent>
                <div style="width: 100%;padding: 15px; text-align: center;">
                    <a class="btn-atualizar"
                       @click="atualizarJson()">
                        Atualizar Dados
                    </a>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
window.jquery = window.$;
import SecaoComponent from "./SecaoComponent";
import Swal from 'sweetalert2'

const FormularioAtendimentoJson = {
    name: "FormularioAtendimentoJson",
    components: {
        SecaoComponent,
    },
    data() {
        return {
            filtro: "matricula",
            searchFilter: "",
            open: false,
            RPC: "rh4_recadastramento.RPC.php",
            bootstrapTable: null,
            json: {
                secoes: [],
            },
            numeroAtendimento: "",
        };
    },
    mounted() {
        this.bootstrapTable = window.$("#table").bootstrapTable({
            sidePagination: "server",
            pagination: true,
            serverSort: true,
            pageSize: 10,
            pageList: [10, 20, 50, 100],
            ajax: (params) => {
                if (this.bootstrapTable) {
                    this.bootstrapTable.bootstrapTable("showLoading");
                }
                params.data.route = "aprovados-instituicao";
                window.$.ajax({
                    type: "GET",
                    url: this.RPC,
                    data: params.data,
                    success: (data) => {
                        params.success(JSON.parse(data));
                    },
                    complete: () => {
                        this.bootstrapTable.bootstrapTable("hideLoading");
                    },
                });
            },
            height: 800,
            showColumns: true,
            toolbar: "#toolbar",
            buttons: this.buttons,
            queryParams: (params) => {
                switch (this.filtro) {
                    case "matricula":
                        params.searchMatricula = this.searchFilter;
                        break;
                    case "nome":
                        params.nome = this.searchFilter;
                        break;
                }

                return params;
            },
            columns: [
                {
                    field: "selecao",
                    checkbox: true,
                },
                {
                    field: "numero_atendimento",
                    title: "N:. ATEND.",
                    sortable: true,
                },
                {
                    field: "data_atendimento",
                    title: "DATA ATEND.",
                    sortable: true,
                    formatter: function dataPtBr(date) {
                        if (
                            typeof date !== "string" || date === "" ||
                            date === "undefined"
                        ) {
                            return date;
                        }

                        const splitsDate = date.split("-");

                        if (typeof splitsDate !== "object" || splitsDate.length !== 3) {
                            return data;
                        }

                        return `${splitsDate[2]}/${splitsDate[1]}/${splitsDate[0]}`;
                    },
                },
                {
                    field: "matricula",
                    title: "MATRICULA",
                    sortable: true,
                },
                {
                    field: "cgm",
                    title: "CGM",
                    sortable: true,
                },
                {
                    field: "cpf",
                    title: "CPF",
                    sortable: true,
                },
                {
                    field: "nome",
                    title: "NOME",
                    sortable: true,
                },
                {
                    field: "nome_instituicao",
                    title: "INSTITUIÇÃO",
                    sortable: true,
                },
                {
                    field: "codigo_lotacao",
                    title: "CÓD. LOTAÇÃO",
                    sortable: true,
                },
                {
                    field: "descricao_lotacao",
                    title: "LOTAÇÃO",
                    sortable: true,
                },
                {
                    field: "status",
                    title: "STATUS",
                },
                {
                    field: "acao",
                    title: "Ação",
                    formatter: function () {
                        return `<a class="edit-json">Editar</a>`;
                    },
                    events: {
                        "click .edit-json": (e, value, row) => {
                            this.json = Object.assign(JSON.parse(row.json));
                            this.numeroAtendimento = row.numero_atendimento;
                            this.toogle();
                        },
                    },
                },
            ],
        });
    },
    methods: {
        searchSecoes() {
            return this.json.secoes.filter((secao) => {
                if (secao.tipo !== 'tabela') {
                    secao.campos.forEach(campo => {
                        if (campo.tipo === 'data') {
                            campo.resposta = this.tratarData(campo.resposta);
                        }
                    });
                } else {

                    secao.resposta.forEach(resposta => {
                        secao.campos.forEach(campo => {
                            if (campo.tipo === 'data') {
                                resposta[campo.nome] = this.tratarData(resposta[campo.nome]);
                            }
                        });
                    });
                }

                return secao.nome !== "anexo" && secao.nome !== "termo";
            });
        },
        toogle() {
            this.open = !this.open;
        },
        buttons() {
            return {
                btnProcessar: {
                    text: "Processar",
                    icon: "fa-paper-plane",
                    event: () => {
                        this.processar();
                    },
                    attributes: {
                        title: "Envia os atendimentos para cadastramento",
                    },
                },
            };
        },
        processar() {
            const selecao = this.bootstrapTable.bootstrapTable("getSelections");
            if (selecao.length < 1) {

                Swal.fire({
                    title: 'Atenção!',
                    text: 'É necessário selecionar  no minimo um atendimento para efetuar o processamento!',
                    icon: 'warning'
                });

                return;
            }

            const selecaoRequest = [];
            selecao.forEach((el) => {
                selecaoRequest.push({
                    numero_atendimento: el.numero_atendimento,
                    matricula: el.matricula,
                    codigo_instituicao: el.codigo_instituicao,
                });
            });

            const params = {route: "processar", selecao: selecaoRequest};
            js_divCarregando("Processar...", "msgBox");
            window.$.ajax({
                type: "POST",
                url: this.RPC,
                data: params,
                success: (data) => {
                    data = JSON.parse(data);
                    if (data.success) {
                        Swal.fire({
                            html: data.message,
                            icon: 'info'
                        });
                        this.bootstrapTable.bootstrapTable("refresh");
                    }
                    alert(data.message);
                    js_removeObj("msgBox");
                },
                complete: () => {
                    js_removeObj("msgBox");
                },
            });
        },
        search() {
            this.bootstrapTable.bootstrapTable("refresh");
        },
        atualizarJson() {
            const params = {
                route: "atualizar-json",
                formulario: JSON.stringify(this.json),
                atendimento: this.numeroAtendimento,
            };

            js_divCarregando("Processar...", "msgBox");

            window.$.ajax({
                type: "POST",
                url: this.RPC,
                data: params,
                success: (data) => {
                    data = JSON.parse(data);
                    if (data.success) {
                        this.open = false;
                        this.bootstrapTable.bootstrapTable("refresh");
                    }
                    alert(data.message);
                    js_removeObj("msgBox");
                },
                complete: () => {
                    js_removeObj("msgBox");
                },
            });
        },
        tratarData(data = null) {
            if (!data) {
                return '';
            }
            const ultimoCaracter = data.slice(-1);
            if (ultimoCaracter === 'Z') {
                const dataUTC = new Date(data);
                return dataUTC.toLocaleDateString();
            }

            let dataSplit = data.split("/");
            if (dataSplit.length === 3) {
                if (dataSplit[0].length === 4) {
                    return `${dataSplit[2]}/${dataSplit[1]}/${dataSplit[0]}`;
                }
            }
            return data;
        }
    },
};

export default FormularioAtendimentoJson;
</script>

<style scoped>
.modal {
    width: 100%;
    height: 100%;
    background: #cccccc70;
    border: 1px #cccc;
    position: absolute;
    z-index: 99999;
    top: 0;
    display: flex;
    justify-content: center;
}

.modal-content {
    overflow-y: auto;
    width: 80%;
    min-height: 500px;
    display: flex;
    background: #fff;
    border: 1px solid #ccc;
    margin-top: 30px;
    border-radius: 5px;
    flex-direction: column;
}

.campos {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    margin: 5px;
    padding: 2px;
}

.campo-grupo {
    display: flex;
    flex-direction: column;
    padding: 5px;
}

#modal-close {
    width: 23px;
    border-radius: 50%;
    border: none;
    background: #0b77b7;
    color: #fff;
    display: flex;
    align-self: flex-end;
    margin: 10px;
    height: 30px;
    align-items: center;
    align-content: center;
    justify-content: center;
}

.div-pesquisa {
    display: flex;
    width: 100%;
    flex-direction: row;
    padding: 10px;
    justify-content: center;
}

.div-pesquisa-input-group {
    display: flex;
    flex-direction: column;
    margin: 5px;
}

.div-pesquisa-input-group input {
    height: 30px;
}

.div-button {
    display: flex;
    flex: 1;
    align-items: flex-end;
}

.input {
    height: 30px;
}

.btn-atualizar {
    background: #337ab7;
    color: white;
    padding: 10px;
    border-radius: 4px;
    text-decoration: none;
}
</style>
