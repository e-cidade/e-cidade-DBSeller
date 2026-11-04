<template>
    <Toast position="center" group="bc">
        <template #message="slotProps">
            <div class="flex flex-column" style="flex: 1">
                <i
                    class="m-auto pi pi-exclamation-triangle"
                    style="font-size: 3rem"
                ></i>
                <p class="font-bold text-xl my-3 text-center">
                    {{ slotProps.message.summary }}
                </p>
                <p class="text-xl my-3 text-center">
                    {{ slotProps.message.detail1 }}
                </p>
                <p class="text-xl my-3 text-center">
                    {{ slotProps.message.detail2 }}
                </p>
                <p class="text-xl my-3 text-center">
                    {{ slotProps.message.detail3 }}
                </p>
            </div>
        </template>
    </Toast>
    <Fieldset
        legend="Simples Nacional - Importar arquivo optantes"
        class="m-auto mt-5 w-max shadow-4"
    >
        <form class="flex flex-column justify-content-center w-max m-auto">
            <tr>
                <td class="w-12rem">
                    <p class="text-right mr-2 font-semibold">
                        Arquivo optantes:
                    </p>
                </td>
                <td>
                    <input
                        type="file"
                        @change="($event) => lerArquivo($event)"
                    />
                </td>
            </tr>
            <tr>
                <td class="w-12rem">
                    <p class="text-right mr-2 font-semibold">
                        Período de apuração:
                    </p>
                </td>
                <td>
                    <input
                        v-model="dados.datas.periodoApuracao.de"
                        class="shadow-4"
                        type="date"
                        readonly
                    />
                </td>
                <td>
                    <p class="mr-2 ml-2 font-semibold">à</p>
                </td>
                <td>
                    <input
                        v-model="dados.datas.periodoApuracao.ate"
                        class="shadow-4"
                        type="date"
                        readonly
                    />
                </td>
            </tr>
            <tr>
                <td class="w-12rem">
                    <p class="text-right mr-2 font-semibold">
                        Prazo de entrega:
                    </p>
                </td>
                <td>
                    <input
                        v-model="dados.datas.dataLimite"
                        class="shadow-4"
                        type="date"
                        readonly
                    />
                </td>
            </tr>
        </form>
        <div class="flex justify-content-center">
            <Button
                :disabled="dados.disabledButton === true"
                class="m-3 mb-0 shadow-4"
                @click="processaDados"
                >Processar</Button
            >
        </div>
    </Fieldset>

    <Dialog
        header="Processar pendências"
        :maximizable="true"
        :modal="true"
        :style="{ width: '1000px' }"
        position="top"
        class="shadow-4"
        v-model:visible="dados.dialogAberto"
    >
        <section style="width: 100%" class="m-auto">
            <DataTable
                :loading="dados.loading"
                :value="dados.pendencias"
                responsiveLayout="scroll"
                :rowHover="true"
                :rows="8"
                showGridlines
                :paginator="true"
                filterDisplay="menu"
                class="shadow-3"
            >
                <template #header>
                    <div class="m-auto w-max">
                        <tr>
                            <td class="w-12rem text-right">
                                <a
                                    class="mr-2 underline text-blue-400 cursor-pointer"
                                    @click="toggleDialogArquivosEnviados"
                                    >Arquivo de Optantes:</a
                                >
                            </td>
                            <td>
                                <input
                                    class="shadow-4"
                                    type="date"
                                    v-model="dados.datas.dataSolicitacao"
                                    readonly
                                />
                                <input
                                    class="ml-2 w-20rem shadow-4"
                                    v-model="dados.nomeArquivo"
                                    type="text"
                                    readonly
                                />
                            </td>
                        </tr>
                        <tr>
                            <td class="w-12rem text-right">
                                <p class="mr-2">Prazo de entrega:</p>
                            </td>
                            <td>
                                <input
                                    class="shadow-4"
                                    v-model="dados.datas.dataLimite"
                                    type="date"
                                    readonly
                                />
                            </td>
                        </tr>
                    </div>
                </template>
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading><ProgressSpinner /></template>
                <Column field="cnpj" header="CNPJ">
                    <template #body="slotProps">
                        {{ formataCnpj(slotProps.data.cnpj) }}
                    </template>
                </Column>
                <Column field="nome" header="Nome / Razão Social" />
                <Column field="dataProcessamento" header="Solicitação">
                    <template #body="slotProps">
                        {{ formataDataBr(slotProps.data.dataProcessamento) }}
                    </template>
                </Column>
                <Column field="inscricao" header="Inscrição" />
                <Column field="status" header="Status">
                    <template #body="slotProps">
                        <div
                            v-if="slotProps.data.numeroStatus === 0"
                            class="text-teal-700 font-semibold text-center cursor-pointer no-underline hover:underline hover:text-cyan-700"
                            @click="
                                abriDialogPendencias(
                                    slotProps.data.cnpj,
                                    slotProps.data.numeroStatus,
                                    slotProps.data.observacoes
                                )
                            "
                        >
                            <div class="underline">
                                {{ slotProps.data.status }}
                            </div>
                        </div>
                        <div
                            v-if="slotProps.data.numeroStatus === 1"
                            class="text-red-600 font-semibold text-center cursor-pointer no-underline hover:underline hover:text-cyan-700"
                            legend="Teste"
                            @click="
                                abriDialogPendencias(
                                    slotProps.data.cnpj,
                                    slotProps.data.numeroStatus,
                                    slotProps.data.observacoes
                                )
                            "
                        >
                            <div class="underline">
                                {{ slotProps.data.status }}
                            </div>
                        </div>
                    </template>
                </Column>
            </DataTable>

            <div class="flex justify-content-center">
                <Button
                    v-if="dados.processaPendencias"
                    @click="processaPendencias"
                    class="m-3 mb-0 shadow-4"
                    label="Processar"
                />
                <Button
                    v-if="dados.geraArquivoRetorno"
                    @click="geraArquivoRetorno"
                    class="m-3 mb-0 shadow-4"
                    label="Gerar arquivo de retorno"
                />
            </div>
        </section>
    </Dialog>

    <Dialog
        header="Status"
        :maximizable="true"
        :modal="true"
        position="top"
        class="shadow-4 min-w-max"
        v-model:visible="dados.dialogPendencias.dialogPendenciasAberto"
    >
        <div class="m-auto w-max">
            <tr v-if="dados.dialogPendencias.cnpj.toString().trim() !== ''">
                <td class="w-12rem text-left">
                    <p class="mr-2 font-semibold">CNPJ:</p>
                </td>
                <td>
                    <p class="mr-2">
                        {{ formataCnpj(dados.dialogPendencias.cnpj) }}
                    </p>
                </td>
            </tr>
            <tr v-if="dados.dialogPendencias.codigoStatus === 1">
                <td class="w-12rem text-left">
                    <p class="mr-2 font-semibold">Status:</p>
                </td>
                <td>
                    <p class="text-red-600 font-semibold">Com pendência.</p>
                </td>
            </tr>
            <tr v-if="dados.dialogPendencias.codigoStatus === 0">
                <td class="w-12rem text-left">
                    <p class="mr-2 font-semibold">Status:</p>
                </td>
                <td>
                    <p class="text-teal-700 font-semibold">Sem pendência.</p>
                </td>
            </tr>
            <tr v-if="dados.dialogPendencias.observacoes.trim() !== ''">
                <td class="w-12rem text-left">
                    <p class="mr-2 font-semibold">Observações:</p>
                </td>
                <td>
                    <ls>
                        <li
                            v-for="dado in dados.dialogPendencias.observacoes.split(
                                ','
                            )"
                            :key="dado.id"
                        >
                            <div class="mr-2 w-30rem mt-2">{{ dado }}.</div>
                        </li>
                    </ls>
                </td>
            </tr>
        </div>
    </Dialog>

    <Dialog
        header="Arquivos enviados"
        :maximizable="true"
        :modal="true"
        position="top"
        class="shadow-4 min-w-max"
        v-model:visible="dados.dialogArquivosEnviados.dialogArquivosAberto"
    >
        <section style="width: 100%" class="m-auto">
            <DataTable
                :loading="dados.loading"
                :value="dados.dialogArquivosEnviados.arquivos"
                responsiveLayout="scroll"
                :rowHover="true"
                :rows="8"
                showGridlines
                :paginator="true"
                filterDisplay="menu"
                selectionMode="single"
                dataKey="q183_sequencial"
                class="shadow-4"
            >
                <template #empty> Nenhum resultado foi encontrado </template>
                <template #loading><ProgressSpinner /></template>

                <template #header>
                    <form
                        class="flex flex-row flex-wrap justify-content-center"
                        ref="form"
                    >
                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="q183_nomearq">Nome do arquivo:</label>
                            <InputText
                                placeholder="nome"
                                class="padding-custom p-inputtext-sm shadow-4"
                                v-model="
                                    dados.dialogArquivosEnviados.pesquisa
                                        .nomeArquivo
                                "
                            />
                        </span>

                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="q183_dt_import">Data importação:</label>
                            <input
                                type="date"
                                placeholder="Importação"
                                class="padding-custom h-2rem p-inputtext-sm shadow-4"
                                v-model="
                                    dados.dialogArquivosEnviados.pesquisa
                                        .dataImportacao
                                "
                            />
                        </span>

                        <span class="p-input-icon-left m-2 flex flex-column">
                            <label for="q183_data_limite">Data limite:</label>
                            <input
                                type="date"
                                placeholder="Limite"
                                class="padding-custom h-2rem p-inputtext-sm shadow-4"
                                v-model="
                                    dados.dialogArquivosEnviados.pesquisa
                                        .dataLimite
                                "
                            />
                        </span>
                    </form>
                    <div class="m-auto flex justify-content-center flex-wrap">
                        <Button
                            class="m-2 p-button-success shadow-4"
                            @click="buscaArquivosJaExistentesApi"
                            ><i class="pi pi-search"
                        /></Button>
                        <Button
                            class="m-2 shadow-4"
                            @click="resetParametrosDialogArquivosEnviados"
                            ><i class="pi pi-undo"
                        /></Button>
                        <Button
                            class="m-2 p-button-danger shadow-4"
                            @click="toggleDialogArquivosEnviados"
                            ><i class="pi pi-times"
                        /></Button>
                    </div>
                </template>

                <Column field="q183_nomearq" header="Nome do arquivo">
                    <template #body="slotProps">
                        <a
                            class="underline text-blue-400 font-semibold"
                            @click="
                                {
                                    selecionaArquivoImportado(
                                        slotProps.data.q183_nomearq,
                                        slotProps.data.q183_data_limite,
                                        slotProps.data.q183_dt_import,
                                        slotProps.data.q183_sequencial
                                    );
                                }
                            "
                        >
                            {{ formataDataBr(slotProps.data.q183_nomearq) }}
                        </a>
                    </template>
                </Column>
                <Column field="q183_dt_import" header="Data de importação">
                    <template #body="slotProps">
                        {{ formataDataBr(slotProps.data.q183_dt_import) }}
                    </template>
                </Column>
                <Column field="q183_data_limite" header="Data limite">
                    <template #body="slotProps">
                        <div
                            v-if="
                                new Date().getTime() <=
                                new Date(
                                    slotProps.data.q183_data_limite
                                ).getTime()
                            "
                        >
                            {{ formataDataBr(slotProps.data.q183_data_limite) }}
                        </div>
                        <div
                            v-if="
                                new Date().getTime() >
                                new Date(
                                    slotProps.data.q183_data_limite
                                ).getTime()
                            "
                            class="text-red-600 font-semibold text-center cursor-pointer hover:text-cyan-700"
                        >
                            {{ formataDataBr(slotProps.data.q183_data_limite) }}
                        </div>
                    </template>
                </Column>
            </DataTable>
        </section>
    </Dialog>
</template>

<script setup>
import { reactive } from "vue";
import Toast from "primevue/toast";
import { useToast } from "primevue/usetoast";

const toast = useToast();
const dados = reactive({
    resultados: [],
    loading: false,
    nomeArquivo: "",
    arquivoImportadoAnteriormente: false,
    datas: {
        periodoApuracao: {
            de: "",
            ate: "",
        },
        dataLimite: "",
        dataSolicitacao: "",
    },
    disabledButton: true,
    dialogAberto: false,
    pendencias: [],
    processaPendencias: true,
    geraArquivoRetorno: false,
    dialogPendencias: {
        dialogPendenciasAberto: false,
        cnpj: "",
        codigoStatus: 0,
        observacoes: "",
    },
    dialogArquivosEnviados: {
        dialogArquivosAberto: false,
        arquivos: [],
        pesquisa: {
            nomeArquivo: "",
            dataImportacao: "",
            dataLimite: "",
        },
    },
});

function lerArquivo(evento) {
    dados.disabledButton = true;
    dados.resultados = [];
    dados.pendencias = [];
    dados.processaPendencias = true;
    dados.geraArquivoRetorno = false;

    const arquivo = evento.target.files[0];
    const leitor = new FileReader();
    leitor.onload = function () {
        const arrayDados = [];
        const resultadoLeitor = leitor.result
            .split(/\r|\n/)
            .filter((item) => item.trim() !== "");

        for (let index = 0; index < resultadoLeitor.length; index++) {
            const arrayNumeros = resultadoLeitor[index].split(";");
            const dadosLeitor = {
                cnpj: arrayNumeros[0],
                tipoUnidade: arrayNumeros[1],
                dataApuracao: arrayNumeros[13],
                dataLimite: arrayNumeros[14],
            };

            for (let index = 0; index <= 10; index++) {
                dadosLeitor[`cnae${index}`] = arrayNumeros[index + 2];
            }

            if (!validaValorResultados(dadosLeitor, index + 1)) {
                dados.resultados = [];
                return;
            }

            arrayDados.push(dadosLeitor);
        }

        dados.nomeArquivo = arquivo.name;
        dados.resultados = [...dados.resultados, ...arrayDados];

        if(!validaResultadoVazio(dados.resultados)){
            return;
        }

        preencheDatas(dados.resultados);
        validaDataLimite();
    };

    leitor.readAsText(arquivo);
}

function validaResultadoVazio(dado) {
    if (dado.length === 0) {
        toast.add({
            severity: "error",
            summary: "Erro",
            detail1: `Tipo de arquivo inválido`,
            group: "bc",
        });

        return false;
    }

    return true;
}

function validaValorResultados(dados, linha) {
    for (const dado of Object.entries(dados)) {
        const campo = dado[0];
        const valor = dado[1];

        const tipoDado =
            campo.trim() === "tipoUnidade"
                ? "tipoUnidade"
                : campo.trim() === "dataApuracao"
                ? "dataApuracao"
                : campo.trim() === "dataLimite"
                ? "dataLimite"
                : campo.trim() === "cnpj"
                ? "cnpj"
                : campo.trim() === "cnae0"
                ? "cnaePrincipal"
                : "cnaeSecundario";

        if (tipoDado === "cnpj") {
            if (Number(valor) === 0 || valor.length !== 14) {
                toast.add({
                    severity: "error",
                    summary: "Erro",
                    detail1: `CNPJ inválido na linha ${linha}.`,
                    group: "bc",
                });

                return false;
            }
        }

        if (tipoDado === "tipoUnidade") {
            if (valor.length !== 2) {
                toast.add({
                    severity: "error",
                    summary: "Erro",
                    detail1: `Tipo de unidade inválido na linha ${linha}.`,
                    group: "bc",
                });

                return false;
            }
        }

        if (tipoDado === "cnaePrincipal") {
            if (
                Number(valor) === 0 ||
                valor.match(/[1-9]/) === null ||
                valor.length !== 7
            ) {
                toast.add({
                    severity: "error",
                    summary: "Erro",
                    detail1: `CNAE principal inválido na linha ${linha}.`,
                    group: "bc",
                });

                return false;
            }
        }

        if (tipoDado === "cnaeSecundario") {
            if (valor.length !== 7) {
                const numeroCnae = campo.replace("cnae", "").trim();

                toast.add({
                    severity: "error",
                    summary: "Erro",
                    detail1: `${numeroCnae}º CNAE secundário inválido na linha ${linha}.`,
                    group: "bc",
                });

                return false;
            }
        }

        if (tipoDado === "dataApuracao") {
            if (Number(valor) === 0 || valor.length !== 8) {
                toast.add({
                    severity: "error",
                    summary: "Erro",
                    detail1: `Data da solicitação inválida na linha ${linha}.`,
                    group: "bc",
                });

                return false;
            }
        }

        if (tipoDado === "dataLimite") {
            if (Number(valor) === 0 || valor.length !== 8) {
                toast.add({
                    severity: "error",
                    summary: "Erro",
                    detail1: `Data limite inválida na linha ${linha}.`,
                    group: "bc",
                });

                return false;
            }
        }
    }

    return true;
}

async function importaOptantesApi() {
    dados.loading = true;
    const body = {
        registros: dados.resultados.map((item) => ({
            ...item,
            dataApuracao: formataData(item.dataApuracao),
            dataLimite: formataData(item.dataLimite),
        })),
    };

    return await window.axios
        .post("v4/api/tributario/issqn/simples-nacional/importa-optantes", body)
        .then((data) => {
            dados.loading = false;
            return data;
        })
        .catch((erro) => {
            dados.loading = false;
            toast.add({
                severity: "error",
                summary: "Erro",
                detail1: "Algo deu errado",
                group: "bc",
            });
        });
}

function processaDados() {
    if (dados.pendencias.length === 0) {
        importaOptantesApi().then((resposta) => {
            const respostaApi = resposta.data.data;
            if (respostaApi.dados.length > 0) {
                dados.pendencias = filtraPendencias(
                    mergeResultados(respostaApi.dados)
                );
            }

            if (respostaApi.jaImportado) {
                dados.arquivoImportadoAnteriormente = true;
                toast.add({
                    severity: "info",
                    summary: "Aviso",
                    detail1: "Arquivo já importado anteriormente.",
                    detail2: `Nome do arquivo: ${respostaApi.nomeArquivo}`,
                    detail3: `Data de importação: ${formataDataBr(
                        respostaApi.dataImportacao
                    )}`,
                    group: "bc",
                });
                processaPendencias();
            }

            dados.arquivoImportadoAnteriormente = false;
        });
    }
    dados.dialogAberto = true;
}

function preencheDatas(resultados) {
    let menorDataApuracao = "";
    let maiorDataApuracao = "";
    let dataLimite = formataDataBanco(resultados[0].dataLimite);

    resultados.map((dado) => {
        if (menorDataApuracao !== "" && maiorDataApuracao !== "") {
            const menorDataApuracaoTimestamp = new Date(
                menorDataApuracao
            ).getTime();
            const maiorDataApuracaoTimestamp = new Date(
                maiorDataApuracao
            ).getTime();
            const dataApuracaoTimestamp = new Date(
                formataDataBanco(dado.dataApuracao)
            ).getTime();

            if (dataApuracaoTimestamp > maiorDataApuracaoTimestamp) {
                maiorDataApuracao = formataDataBanco(dado.dataApuracao);
            }

            if (dataApuracaoTimestamp < menorDataApuracaoTimestamp) {
                menorDataApuracao = formataDataBanco(dado.dataApuracao);
            }
        } else {
            menorDataApuracao = formataDataBanco(dado.dataApuracao);
            maiorDataApuracao = formataDataBanco(dado.dataApuracao);
        }
    });

    dados.datas.dataSolicitacao = new Date().toISOString().split("T")[0];
    dados.datas.dataLimite = dataLimite;
    dados.datas.periodoApuracao.de = menorDataApuracao;
    dados.datas.periodoApuracao.ate = maiorDataApuracao;
}

function validaDataLimite() {
    const dataLimiteTimestamp = new Date(dados.datas.dataLimite).getTime();
    const dataAtualTimestamp = new Date().getTime();

    if (dataLimiteTimestamp >= dataAtualTimestamp) {
        dados.disabledButton = false;
    } else {
        dados.disabledButton = true;
        toast.add({
            severity: "error",
            summary: "Erro",
            detail1:
                "Prazo de entrega fora da data limite de resposta para RFB.",
            group: "bc",
        });
    }
}

function formataData(data) {
    return `${data[4]}${data[5]}${data[6]}${data[7]}-${data[2]}${data[3]}-${data[0]}${data[1]}`;
}

function formataDataBanco(data) {
    if (data && data.toString().trim() !== "") {
        const dia = `${data[0]}${data[1]}`;
        const mes = `${data[2]}${data[3]}`;
        const ano = `${data[4]}${data[5]}${data[6]}${data[7]}`;
        return `${ano}-${mes}-${dia}`;
    }
    return "";
}

function formataDataBr(data) {
    return data.toString().trim().split("-").reverse().join("/");
}

function formataCnpj(cnpj) {
    if (cnpj.toString().trim() !== "") {
        const info = cnpj.toString().trim().split("");

        return `${info[0]}${info[1]}.${info[2]}${info[3]}${info[4]}.${info[5]}${info[6]}${info[7]}/${info[8]}${info[9]}${info[10]}${info[11]}-${info[12]}${info[13]}`;
    }
    return "";
}

function filtraPendencias(pendencias) {
    return pendencias.filter((item) => {
        const dataLimite = new Date(item.dataLimite).getTime();
        const dataAtual = new Date().getTime();

        return dataLimite >= dataAtual;
    });
}

function mergeResultados(resposta) {
    return resposta.map((item) => {
        const dadosAtuais = dados.resultados.find(
            (dado) => dado.cnpj === item.cnpj
        );

        const situacao = dadosAtuais.situacaoCadastrada
            ? dadosAtuais.situacaoCadastrada === "0"
                ? true
                : false
            : item.apto;

        return {
            cnpj: item.cnpj,
            nome: item.nome,
            dataSolicitacao: dados.datas.dataSolicitacao,
            inscricao: item.inscricoes,
            nomeArquivo: dados.nomeArquivo,
            periodoApuracaoDe: dados.datas.periodoApuracao.de,
            periodoApuracaoAte: dados.datas.periodoApuracao.ate,
            dataLimite: formataDataBanco(dadosAtuais.dataLimite),
            dataProcessamento: formataDataBanco(dadosAtuais.dataApuracao),
            possuiCgm: item.possuiCgm,
            possuiInscricaoMunicipalAtiva: item.possuiInscricaoMunicipalAtiva,
            naoPossuiDebitosVencidos: item.naoPossuiDebitosVencidos,
            cnaes: item.cnaes,
            observacoes: item.observacoes,
            apto: situacao,
        };
    });
}

function defineStatus() {
    dados.pendencias = dados.pendencias.map((item) => {
        const possuiCadastroCgm = item.nome.toString().trim() !== "";
        const possuiInscricao = item.inscricao.toString().trim() !== "";
        const semPendencia = possuiCadastroCgm && possuiInscricao && item.apto;
        const numeroStatus = semPendencia ? 0 : 1;
        const status = semPendencia ? "Sem pendência" : "Com pendência";

        return { ...item, status, numeroStatus };
    });
}

function processaPendencias() {
    defineStatus();
    dados.processaPendencias = false;
    dados.geraArquivoRetorno = true;

    if (!dados.arquivoImportadoAnteriormente) {
        salvaInformacoesPendenciasApi();
    }
}

async function salvaInformacoesPendenciasApi() {
    dados.loading = true;
    const body = {
        nomeArquivo: dados.nomeArquivo,
        dataImportacao: dados.datas.dataSolicitacao,
        periodoApuracaoDe: dados.datas.periodoApuracao.de,
        periodoApuracaoAte: dados.datas.periodoApuracao.ate,
        dataLimite: dados.datas.dataLimite,
        registros: [
            ...dados.pendencias.map((item) => ({
                dataSolicitacao: item.dataSolicitacao,
                cnpj: item.cnpj,
                cnaes: [
                    ...item.cnaes.map((cnae, index) => ({
                        cnae: cnae,
                        tipo: index === 0 ? "P" : "S",
                    })),
                ],
                situacao: item.apto ? 0 : 1,
            })),
        ],
    };

    return await window.axios
        .post("v4/api/tributario/issqn/simples-nacional/exporta-arquivo", body)
        .then((data) => {
            dados.loading = false;
            return data;
        })
        .catch((erro) => {
            dados.loading = false;
            toast.add({
                severity: "error",
                summary: "Erro",
                detail1: "Algo deu errado",
                group: "bc",
            });
        });
}

function geraArquivoRetorno() {
    let conteudoArquivo = "";

    dados.pendencias.map((item) => {
        conteudoArquivo =
            conteudoArquivo + `${item.cnpj};${item.numeroStatus}\n`;
    });

    conteudoArquivo = conteudoArquivo.trim();

    const linkDownload = document.createElement("a");
    const arquivoBlob = new Blob([conteudoArquivo], { type: "text/plain" });

    linkDownload.href = URL.createObjectURL(arquivoBlob);
    linkDownload.download = "arquivoRetorno.txt";
    linkDownload.click();
}

function abriDialogPendencias(cnpj, numeroStatus, observacoes) {
    dados.dialogPendencias.dialogPendenciasAberto = true;
    dados.dialogPendencias.cnpj = cnpj;
    dados.dialogPendencias.codigoStatus = numeroStatus;
    dados.dialogPendencias.observacoes = observacoes;
}

async function buscaArquivosJaExistentesApi() {
    dados.loading = true;
    const { nomeArquivo, dataImportacao, dataLimite } =
        dados.dialogArquivosEnviados.pesquisa;

    return window.axios
        .get(
            `v4/api/tributario/issqn/simples-nacional/busca-arquivos?nomeArquivo=${nomeArquivo}&dataImportacao=${dataImportacao}&dataLimite=${dataLimite}`
        )
        .then((data) => {
            dados.loading = false;
            dados.dialogArquivosEnviados.arquivos = data.data.data;
        })
        .catch((erro) => {
            dados.loading = false;
            toast.add({
                severity: "error",
                summary: "Erro",
                detail1: "Algo deu errado",
                group: "bc",
            });
        });
}

function toggleDialogArquivosEnviados() {
    dados.dialogArquivosEnviados.dialogArquivosAberto =
        !dados.dialogArquivosEnviados.dialogArquivosAberto;
}

function resetParametrosDialogArquivosEnviados() {
    dados.dialogArquivosEnviados.arquivos = [];
    dados.dialogArquivosEnviados.pesquisa.dataImportacao = "";
    dados.dialogArquivosEnviados.pesquisa.dataLimite = "";
    dados.dialogArquivosEnviados.pesquisa.nomeArquivo = "";
}

function selecionaArquivoImportado(
    nomeArquivo,
    dataLimite,
    dataSolicitacao,
    idArquivo
) {
    carregaInformacoesNovoArquivoSelecionado(idArquivo);
    toggleDialogArquivosEnviados();
    dados.nomeArquivo = nomeArquivo;
    dados.datas.dataLimite = dataLimite;
    dados.datas.dataSolicitacao = dataSolicitacao;
}

function carregaInformacoesNovoArquivoSelecionado(idArquivo) {
    buscaInformacoesNovoArquivoSelecionadoApi(idArquivo).then((data) => {
        const resposta = data.data.data;

        dados.resultados = resposta.registros.map((item) => {
            const dadosArquivoSelecionado = {
                cnpj: item.q184_cnpj,
                dataApuracao: removeBarras(
                    formataDataBr(item.q184_dt_solicitacao)
                ),
                dataLimite: removeBarras(
                    formataDataBr(resposta.q183_data_limite)
                ),
                situacaoCadastrada: item.envio.q186_situacao.toString(),
            };

            for (let index = 0; index <= 10; index++) {
                dadosArquivoSelecionado[`cnae${index}`] =
                    item.cnaes[index] && item.cnaes[index].q185_cnae
                        ? item.cnaes[index].q185_cnae
                        : "0000000";
            }

            return dadosArquivoSelecionado;
        });
        dados.pendencias = [];
        processaDados();
    });
}

async function buscaInformacoesNovoArquivoSelecionadoApi(idArquivo) {
    dados.loading = true;
    return window.axios
        .get(
            `v4/api/tributario/issqn/simples-nacional/busca-arquivo?id=${idArquivo}`
        )
        .then((data) => {
            dados.loading = false;
            return data;
        })
        .catch((erro) => {
            dados.loading = false;
            toast.add({
                severity: "error",
                summary: "Erro",
                detail1: "Algo deu errado",
                group: "bc",
            });
        });
}

function removeBarras(item) {
    return item.replaceAll(/\//gi, "");
}
</script>
