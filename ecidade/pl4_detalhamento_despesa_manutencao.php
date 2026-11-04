<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_app.utils.php");
require_once modification('libs/db_conecta.php');
require_once modification("libs/db_sessoes.php");
require_once modification("dbforms/db_funcoes.php");

$displayCaracteristicaPeculiarClass = isParaiba() ? 'hide' : '';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="estilos.css"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.core.css"
          rel="stylesheet"/>
    <link type="text/css" href="extension/package/Desktop/assets/vendors/alertify/themes/alertify.bootstrap.css"
          rel="stylesheet"/>
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>
</head>
<body>

<div id='ctnAbas'></div>
<button style="top: 3px; right: 15px; position: fixed" rel='ignore-css' class="retornar" type="button">
    <i class="fas fa-arrow-left"></i>
    Retornar
</button>

<div id="abaDetalhamento">
    <form id="frmDetalhamento" class="container">
        <fieldset>
            <legend>Detalhamento da despesa</legend>
            <table class="form-container">
                <tr>
                    <td><label for="orgao"><a href="#">Órgão:</a></label></td>
                    <td>
                        <select id="orgao" name="pl20_orcorgao">
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraUnidade" for="unidade"><a href="#">Unidade:</a></label></td>
                    <td>
                        <input type="hidden" id="codigoInstituicao" name="pl20_instituicao">
                        <input type="text" id="unidade" name="pl20_orcunidade" lang="o41_unidade"
                               class="field-size2 ">
                        <input type="text" id="descricaoUnidade" lang="o41_descr" class="readonly field-size8 readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraFuncao" for="funcao"><a href="#">Função:</a></label></td>
                    <td>
                        <input type="text" id="funcao" name="pl20_orcfuncao" lang="o52_funcao" class="field-size2">
                        <input type="text" id="descricaoFuncao" lang="o52_descr" class="readonly field-size8 readonly"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraSubFuncao" for="subfuncao"><a href="#">Subfunção:</a></label></td>
                    <td>
                        <input type="text" id="subfuncao" name="pl20_orcsubfuncao" lang="o53_subfuncao"
                               class="field-size2">
                        <input type="text" id="descricaoSubFuncao" lang="o53_descr"
                               class="readonly field-size8 readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td><label for="programa">Programa:</label></td>
                    <td>
                        <input type="text" id="programa" class="field-size2 readonly" readonly>
                        <input type="text" id="descricaoPrograma" class="field-size8 readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td><label for="iniciativa">Iniciativa:</label></td>
                    <td>
                        <input type="text" id="iniciativa" class="field-size2 readonly" readonly>
                        <input type="text" id="descricaoIniciativa" class="field-size8 readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraNatureza" for="natureza"><a href="#">Natureza:</a></label></td>
                    <td>
                        <input type="hidden" id="idElemento" name="pl20_orcelemento" lang="o56_codele">
                        <input type="text" id="natureza" name="pl20_orcelemento" lang="o56_elemento"
                               class="field-size3" maxlength="13">
                        <input type="text" id="descricaoNatureza" lang="o56_descr"
                               class="readonly field-size7 readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraRecurso" for="recurso"><a href="#">Fonte Recurso:</a></label></td>
                    <td>
                        <input type="hidden" id="codigoRecurso" name="pl20_recurso">
                        <input type="text" id="recurso" lang="gestao" class="field-size2 readonly" readonly>
                        <input type="text" id="descricaoRecurso" lang="descricao" class="readonly field-size8"
                               readonly>
                    </td>
                </tr>
                <tr>
                    <td><label>Complemento:</label></td>
                    <td>
                    <input type="text" id="codigoComplemento" class="readonly field-size2">
                    <input type="text" id="descricaoComplemento" class="readonly field-size8 readonly"
                        readonly>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraSubtitulo" for="subtitulo"><a href="#">Subtítulo:</a></label></td>
                    <td>
                        <input type="text" id="subtitulo" name="pl20_subtitulo" lang="o11_sequencial"
                               class="field-size2">
                        <input type="text" id="descricaoSubtitulo" lang="o11_descricao"
                               class="readonly field-size8 readonly" readonly>
                    </td>
                </tr>
                <tr class="<?=$displayCaracteristicaPeculiarClass ?>">
                    <td><label id="ancoraCaracteristica" for="caracteristica">
                            <a href="#">Caracteristica Peculiar:</a>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="caracteristica" name="pl20_concarpeculiar" lang="c58_sequencial"
                            class="field-size2">
                        <input type="text" id="descricaoCaracteristica" lang="c58_descr"
                            class="readonly field-size8 readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="esfera">Esfera Orçamentária:</label>
                    </td>
                    <td>
                        <select id="esfera" name="pl20_esferaorcamentaria">
                            <option value="">Selecione uma esfera orçamentária</option>
                            <option value="10">F - Orçamento Fiscal</option>
                            <option value="20">S - Orçamento da Seguridade Social</option>
                            <option value="30">I - Orçamento de Investimento</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="valorBase">Valor Base:</label></td>
                    <td>
                        <input type="text" id="valorBase" name="pl20_valorbase" class="field-size3">
                    </td>
                </tr>
            </table>

            <fieldset class="separator ">
                <legend>Valores previstos</legend>
                <div id="containerValoresDetalhamento"></div>
            </fieldset>
        </fieldset>

        <input type="hidden" id="codigoDetalhamento" name="pl20_codigo">
        <button type="button" id="btnSalvarDetalhamento">
            <i class="far fa-save"></i>
            Salvar
        </button>

        <button type="button" id="btnNovoDetalhamento">
            <i class="far fa-file"></i>
            Novo
        </button>
    </form>
</div>

<div id="abaCronograma" style="display: none">

    <form id="frmCronograma" class="container">
        <fieldset>
            <legend>Cronograma de desembolso</legend>
            <table class="form-container">
                <tr>
                    <td><label for="exercicio">Exercício:</label></td>
                    <td>
                        <select id="exercicio" name="exercicio"></select>
                    </td>
                </tr>
                <tr>
                    <td><label for="valorExercicio">Recurso Alocado:</label></td>
                    <td>
                        <input type="text" id="valorExercicio" readonly class="readonly field-size3">
                    </td>
                </tr>
            </table>
            <fieldset class="separator">
                <legend>Competência</legend>
                <table class="form-container" >
                    <tr style="border-bottom: 1px solid">
                        <th>Mês</th>
                        <th>Valor</th>
                    </tr>
                    <tr>
                        <td><label for="janeiro">Janeiro:</label></td>
                        <td><input type="text" id="janeiro" name="janeiro" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="fevereiro">Fevereiro:</label></td>
                        <td><input type="text" id="fevereiro" name="fevereiro" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="marco">Março:</label></td>
                        <td><input type="text" id="marco" name="marco" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="abril">Abril:</label></td>
                        <td><input type="text" id="abril" name="abril" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="maio">Maio:</label></td>
                        <td><input type="text" id="maio" name="maio" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="junho">Junho:</label></td>
                        <td><input type="text" id="junho" name="junho" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="julho">Julho:</label></td>
                        <td><input type="text" id="julho" name="julho" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="agosto">Agosto:</label></td>
                        <td><input type="text" id="agosto" name="agosto" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="setembro">Setembro:</label></td>
                        <td><input type="text" id="setembro" name="setembro" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="outubro">Outubro:</label></td>
                        <td><input type="text" id="outubro" name="outubro" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="novembro">Novembro:</label></td>
                        <td><input type="text" id="novembro" name="novembro" class="valores field-size3"></td>
                    </tr>
                    <tr>
                        <td><label for="dezembro">Dezembro:</label></td>
                        <td><input type="text" id="dezembro" name="dezembro" class="valores field-size3"></td>
                    </tr>

                    <tr style="border-top: 1px solid">
                        <td><label for="total">Total:</label></td>
                        <td><input type="text" id="total" class="valor_total field-size3 readonly" readonly></td>
                    </tr>
                </table>
            </fieldset>
        </fieldset>

        <input type="hidden" id="codigoCronograma" name="id">
        <button type="button" id="btnRecalcularCronograma">
            <i class="fas fa-calculator"></i>
            Recalcular
        </button>
        <button type="button" id="btnSalvarCronograma">
            <i class="far fa-save"></i>
            Salvar
        </button>
    </form>
</div>

<div id="modalRecalculoCronograma" style="display: none">
    <div class="alert alert-primary text-left" role="alert">
        Marque os anos que deseja recalcular e a fórmula para cálculo.
    </div>
    <div class="container" >
        <form id="formFatorCorrecao">

            <fieldset>
                <legend>Recalcule o cronograma</legend>

                <fieldset class="separator">
                    <legend>Anos</legend>
                    <div id="cntAnos" style="display: flex; justify-content: space-between;" class="bold" ></div>
                </fieldset>
                <table class="form-container">
                    <tr>
                        <td><label for="formulaRecalculo">Fórmula:</label></td>
                        <td>
                            <select id="formulaRecalculo">
                                <option value="1">Dividir recursos alocados por 12</option>
                                <option value="2">Aplicar total do recurso em um mês</option>
                            </select>
                        </td>
                    </tr>
                    <tr style="display: none;" id="linhaMeses">
                        <td><label for="mesRecalculo">Mês:</label></td>
                        <td><select id="mesRecalculo">
                                <option value="janeiro">Janeiro</option>
                                <option value="fevereiro">Fevereiro</option>
                                <option value="marco">Março</option>
                                <option value="abril">Abril</option>
                                <option value="maio">Maio</option>
                                <option value="junho">Junho</option>
                                <option value="julho">Julho</option>
                                <option value="agosto">Agosto</option>
                                <option value="setembro">Setembro</option>
                                <option value="outubro">Outubro</option>
                                <option value="novembro">Novembro</option>
                                <option value="dezembro">Dezembro</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <button type="button" id="btnSalvarRecalculo">
                <i class="far fa-save"></i>
                Salvar
            </button>
        </form>
    </div>
</div>

<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/valores.js"></script>
<script type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>

<script type="text/javascript">
    const get = js_urlToObject();
    var ufAtual = '';

    const routs = {
        plano: 'financeiro/planejamento/consulta/plano',
        iniciativa: 'financeiro/planejamento/iniciativa',
        detalhamento: {
            salvar: 'financeiro/planejamento/despesa/detalhamento/salvar',
            show: 'financeiro/planejamento/despesa/detalhamento',
        },
        fatorCorrecao: 'financeiro/planejamento/fator-correcao/index',
        cronograma: {
            salvar : 'financeiro/planejamento/despesa/cronograma/salvar',
            recalcular : 'financeiro/planejamento/despesa/cronograma/recalcular',
        },
        parametro : 'financeiro/orcamento/utiliza-decimal'
    }

    var precisao = 2;

    /**
     * Abas
     */
    const cntAbaDetalhamento = document.getElementById('abaDetalhamento')
    const cntAbaCronograma = document.getElementById('abaCronograma')
    const ctnAbas = new DBAbas(document.getElementById('ctnAbas'));
    const abaDetalhamento = ctnAbas.adicionarAba("Detalhamento", cntAbaDetalhamento);
    const abaCronograma = ctnAbas.adicionarAba("Cronograma de desembolso", cntAbaCronograma);
    abaCronograma.bloquear();

    var plano = {};
    var iniciativa = {};
    var detalhamento = {};
    const fatorCorrecao = [];

    const formDetalhamento = {
        frm: document.getElementById('frmDetalhamento'),
        cntValores: document.getElementById('containerValoresDetalhamento'),
        salvar: document.getElementById('btnSalvarDetalhamento'),
        novo: document.getElementById('btnNovoDetalhamento'),
        codigo: document.getElementById('codigoDetalhamento'),
        unidade: document.getElementById('unidade'),
        codigoInstituicao: document.getElementById('codigoInstituicao'),
        orgao: document.getElementById('orgao'),
        codigoOrgao: document.getElementById('codigoOrgao'),
        programa: document.getElementById('programa'),
        descricaoPrograma: document.getElementById('descricaoPrograma'),
        iniciativa: document.getElementById('iniciativa'),
        descricaoIniciativa: document.getElementById('descricaoIniciativa'),
        descricaoUnidade: document.getElementById('descricaoUnidade'),
        funcao: document.getElementById('funcao'),
        descricaoFuncao: document.getElementById('descricaoFuncao'),
        subfuncao: document.getElementById('subfuncao'),
        descricaoSubFuncao: document.getElementById('descricaoSubFuncao'),
        codigoElemento: document.getElementById('idElemento'),
        natureza: document.getElementById('natureza'),
        descricaoNatureza: document.getElementById('descricaoNatureza'),
        codigoRecurso: document.getElementById('codigoRecurso'),
        recurso: document.getElementById('recurso'),
        descricaoRecurso: document.getElementById('descricaoRecurso'),
        codigoComplemento: document.getElementById('codigoComplemento'),
        descricaoComplemento: document.getElementById('descricaoComplemento'),
        subtitulo: document.getElementById('subtitulo'),
        descricaoSubtitulo: document.getElementById('descricaoSubtitulo'),
        caracteristica: document.getElementById('caracteristica'),
        descricaoCaracteristica: document.getElementById('descricaoCaracteristica'),
        esfera: document.getElementById('esfera'),
        valorBase: new DBInputValor(document.getElementById('valorBase'))
    };

    valoresDetalhamento = new Valores();

    const formCronograma = {
        form : document.getElementById('frmCronograma'),
        salvar : document.getElementById('btnSalvarCronograma'),
        codigo : document.getElementById('codigoCronograma'),
        exercicio : document.getElementById('exercicio'),
        valorExercicio : document.getElementById('valorExercicio'),
        janeiro : document.getElementById('janeiro'),
        fevereiro : document.getElementById('fevereiro'),
        marco : document.getElementById('marco'),
        abril : document.getElementById('abril'),
        maio : document.getElementById('maio'),
        junho : document.getElementById('junho'),
        julho : document.getElementById('julho'),
        agosto : document.getElementById('agosto'),
        setembro : document.getElementById('setembro'),
        outubro : document.getElementById('outubro'),
        novembro : document.getElementById('novembro'),
        dezembro : document.getElementById('dezembro'),
        totalizador: document.getElementById('total'),
        valorTotal: 0
    }

    const lookUpUnidade = new DBLookUp(
        document.getElementById('ancoraUnidade'),
        formDetalhamento.unidade,
        formDetalhamento.descricaoUnidade, {
            'sArquivo': 'func_orcunidade_nova.php',
            'sLabel': 'Pesquisar Unidade',
            'sObjetoLookUp': "db_iframe_orcunidade",
            'aCamposAdicionais': ['o41_instit']
        });

    lookUpUnidade.setCallBack('onClick', (retorno) => {
        formDetalhamento.codigoInstituicao.value = retorno[2];
    });

    lookUpUnidade.setCallBack('onChange', (erro, retorno) => {
        formDetalhamento.codigoInstituicao.value = ''
        if (erro) {
            return;
        }
        formDetalhamento.codigoInstituicao.value = retorno[2];
    });

    const lookupFuncao = new DBLookUp(
        document.getElementById('ancoraFuncao'),
        formDetalhamento.funcao,
        formDetalhamento.descricaoFuncao, {
            'sArquivo': 'func_orcfuncao.php',
            'sLabel': 'Pesquisa Função',
            'sObjetoLookUp': "db_iframe_orcfuncao"
        }
    );

    const lookupSubFuncao = new DBLookUp(
        document.getElementById('ancoraSubFuncao'),
        formDetalhamento.subfuncao,
        formDetalhamento.descricaoSubFuncao, {
            'sArquivo': 'func_orcsubfuncao.php',
            'sLabel': 'Pesquisa Subfunção',
            'sObjetoLookUp': "db_iframe_orcsubfuncao"
        }
    );

    const lookupNatureza = new DBLookUp(
        document.getElementById('ancoraNatureza'),
        formDetalhamento.natureza,
        formDetalhamento.descricaoNatureza,
        {
            'sArquivo': 'func_orcelemento.php',
            'sLabel': 'Pesquisa Natureza de Despesa',
            'sObjetoLookUp': "db_iframe_orcelemento",
            'aCamposAdicionais': ['o56_codele']
        }
    );
    const preencheFormNatureza = (natureza, descricao, codigoElemento) => {
        formDetalhamento.codigoElemento.value = codigoElemento;
        formDetalhamento.natureza.value = natureza;
        formDetalhamento.descricaoNatureza.value = descricao;
        buscarFatorCorrecao();
    }

    lookupNatureza.setCallBack('onClick', (retorno) => {
        preencheFormNatureza(retorno[0], retorno[1], retorno[2]);
    });

    lookupNatureza.setCallBack('onChange', (erro, retorno) => {
        if (erro) {
            preencheFormNatureza('', retorno[0], '');
            return;
        }
        preencheFormNatureza(formDetalhamento.natureza.value, retorno[0], retorno[2]);
    });

    const lookupFonteRecurso = new DBLookUp(
        document.getElementById('ancoraRecurso'),
        formDetalhamento.recurso,
        formDetalhamento.descricaoRecurso,
        {
            'sArquivo': 'func_novosRecursos.php',
            'sLabel': 'Pesquisa Fonre de Recurso',
            'sObjetoLookUp': "db_iframe_orctiporec",
            'aCamposAdicionais': ['o15_codigo', 'o200_descricao', 'o15_complemento'],
            'aParametrosAdicionais': [`exercicio=${get.exercicio}`]
        }
    );

    const preencheFormFonteRecurso = (recurso, descricao, codigoRecurso, complemento, codigoComplemento) => {
        console.log(recurso, descricao, codigoRecurso, complemento);
        formDetalhamento.codigoRecurso.value = codigoRecurso;
        formDetalhamento.recurso.value = recurso;
        formDetalhamento.descricaoRecurso.value = descricao;
        formDetalhamento.descricaoComplemento.value = complemento;
        formDetalhamento.codigoComplemento.value = codigoComplemento
    }

    lookupFonteRecurso.setCallBack('onClick', retorno => {
        preencheFormFonteRecurso(retorno[0], retorno[1], retorno[2], retorno[3], retorno[4]);
    });

    const lookupSubtitulo = new DBLookUp(
        document.getElementById('ancoraSubtitulo'),
        formDetalhamento.subtitulo,
        formDetalhamento.descricaoSubtitulo,
        {
            'sArquivo': 'func_ppasubtitulolocalizadorgasto.php',
            'sLabel': 'Pesquisa Subtítulo',
            'sObjetoLookUp': "db_iframe_ppasubtitulolocalizadorgasto"
        }
    );

    const lookupCaracteristicaPeculiar = new DBLookUp(
        document.getElementById('ancoraCaracteristica'),
        formDetalhamento.caracteristica,
        formDetalhamento.descricaoCaracteristica,
        {
            'sArquivo': 'func_concarpeculiar.php',
            'sLabel': 'Pesquisa Caracteristica Peculiar',
            'sObjetoLookUp': "db_iframe_concarpeculiar"
        }
    );

    const setAnoLockups = (ano) => {
        lookUpUnidade.setParametrosAdicionais([`ano=${ano}`]);
        lookupNatureza.setParametrosAdicionais([`ano=${ano}&analitica=1`]);
    }

    PHPSession.loadData().then(() => {
        ufAtual = PHPSession.UF;
        let urlNovo = 'pl4_detalhamento_despesa_manutencao.php?';
        if (get.exercicio) {
            setAnoLockups(get.exercicio);
            urlNovo += `exercicio=${get.exercicio}`;
        }

        if (!get.iniciativa) {
            alert('Iniciativa não foi informada. ');
            return;
        }

        /**
         * Botão de retorno
         */
        document.querySelectorAll('.retornar').forEach(el => el.addEventListener('click', event => {
            let parameters = [
                `planejamento=${get.planejamento}`,
                `programa=${get.programa}`,
                `iniciativa=${get.iniciativa}`
            ];
            location.href = `pl4_detalhamento_despesa.php?${parameters.join('&')}`;
        }));

        HttpClient.get(`${PHPSession.requestApi}/${routs.iniciativa}/${get.iniciativa}`).then(response => {
            urlNovo += `&iniciativa=${get.iniciativa}`;
            iniciativa = response.data;
            plano = iniciativa.programa_estrategico.planejamento;
            valoresDetalhamento.criaInputValores(formDetalhamento.cntValores, plano);

            formDetalhamento.orgao.addEventListener('change', () => {
                let orgao = formDetalhamento.orgao.value;
                lookUpUnidade.setParametrosAdicionais([`ano=${get.exercicio}`, `orgao=${orgao}`]);
                formDetalhamento.codigoInstituicao.value = '';
                formDetalhamento.unidade.value = '';
                formDetalhamento.descricaoUnidade.value = '';
            });
            iniciativa.programa_estrategico.orgaos.map(orgao => {
                let descricao = `${orgao.orgao} - ${orgao.descricao}`;
                formDetalhamento.orgao.add(new Option(descricao, orgao.pl27_orcorgao));
            });

            formDetalhamento.orgao.dispatchEvent(new Event('change'));
            formDetalhamento.programa.value = iniciativa.programa_estrategico.programa;
            formDetalhamento.descricaoPrograma.value = iniciativa.programa_estrategico.descricao;
            formDetalhamento.iniciativa.value = iniciativa.acao;
            formDetalhamento.descricaoIniciativa.value = iniciativa.descricao_acao;

        }).then(() => {
            // quando informado o id do detalhamento
            if (get.codigo) {
                HttpClient.get(`${PHPSession.requestApi}/${routs.detalhamento.show}/${get.codigo}`).then(response => {
                    detalhamento = response.data;
                    setDadosFormDetalhamento();
                    abaCronograma.desbloquear();
                });
            }
        });

        cntAbaCronograma.style.display = '';

        formDetalhamento.novo.addEventListener('click', () => {
            location.href = urlNovo;
        });

        let ano = PHPSession.getValueSession('DB_anousu');

        HttpClient.get(`${PHPSession.requestApi}/${routs.parametro}/${ano}`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            precisao = response.data ? 2 : 0;
        });
    });

    const setDadosFormDetalhamento = () => {
        formDetalhamento.orgao.value = detalhamento.orgao.o40_orgao;
        formDetalhamento.orgao.dispatchEvent(new Event('change'));

        formDetalhamento.codigo.value = detalhamento.pl20_codigo;
        formDetalhamento.unidade.value = String(detalhamento.unidade.o41_unidade).padStart(2, '0');
        formDetalhamento.descricaoUnidade.value = detalhamento.unidade.o41_descr;
        formDetalhamento.codigoInstituicao.value = detalhamento.pl20_instituicao;
        formDetalhamento.funcao.value = String(detalhamento.funcao.o52_funcao).padStart(2, '0');
        formDetalhamento.descricaoFuncao.value = detalhamento.funcao.o52_descr;
        formDetalhamento.subfuncao.value = String(detalhamento.subfuncao.o53_subfuncao).padStart(2, '0');
        formDetalhamento.descricaoSubFuncao.value = detalhamento.subfuncao.o53_descr;
        formDetalhamento.codigoElemento.value = detalhamento.natureza_despesa.o56_codele;
        formDetalhamento.natureza.value = detalhamento.natureza_despesa.o56_elemento;
        formDetalhamento.descricaoNatureza.value = detalhamento.natureza_despesa.o56_descr;
        formDetalhamento.codigoRecurso.value = detalhamento.recurso.o15_codigo;
        formDetalhamento.recurso.value = detalhamento.recurso.fonteRecurso.gestao;
        formDetalhamento.descricaoRecurso.value = detalhamento.recurso.fonteRecurso.descricao;
        formDetalhamento.codigoComplemento.value = detalhamento.recurso.o15_complemento;
        formDetalhamento.descricaoComplemento.value = detalhamento.recurso.complemento.descricao;
        formDetalhamento.subtitulo.value = detalhamento.subtitulo.o11_sequencial;
        formDetalhamento.descricaoSubtitulo.value = detalhamento.subtitulo.o11_descricao;
        formDetalhamento.caracteristica.value = detalhamento.caracteristica_peculiar.c58_sequencial;
        formDetalhamento.descricaoCaracteristica.value = detalhamento.caracteristica_peculiar.c58_descr;
        formDetalhamento.esfera.value = detalhamento.pl20_esferaorcamentaria;
        formDetalhamento.valorBase.value = detalhamento.pl20_valorbase;

        detalhamento.valores.map(valor => {
            valoresDetalhamento.set(valor.pl10_ano, valor.pl10_valor);
        });

        setDadosFormCronograma();
        buscarFatorCorrecao();
    };

    const buscarFatorCorrecao = () => {
        const formData = new FormData();
        formData.append('planejamento_id', plano.pl2_codigo);
        formData.append('natureza_id', formDetalhamento.codigoElemento.value);
        formData.append('tipo', 'despesa');
        PHPSession.appendFormData(formData);

        const parametros = {
            body: formData,
            reportMessage: `Aguarde, buscando fator de correção.`
        }

        HttpClient.post(`${PHPSession.requestApi}/${routs.fatorCorrecao}`, parametros).then(response => {
            for (let fator of response.data) {
                fatorCorrecao.push(fator);
            }
        });
    };

    const getFatorAno = (ano) => {
        return fatorCorrecao.filter(fator => {
            return fator.pl7_exercicio === ano;
        }).shift();
    }

    const getPercentualFator = (ano) => {
        let fator = getFatorAno(ano);
        if (fator.deflator) {
            fator.pl7_percentual *= -1;
        }
        return fator.pl7_percentual;
    }

    document.getElementById('valorBase').addEventListener('change', () => {
        let valor = formDetalhamento.valorBase.value
        let novoValor = valor;

        valoresDetalhamento.getValores().each(function (data) {

            if (fatorCorrecao.length > 0) {
                let percentual = getPercentualFator(data.ano);
                novoValor = Number(novoValor * (1 + (percentual / 100))).toFixed(precisao);
            }

            valoresDetalhamento.set(data.ano, novoValor);
        })
    });

    const setDadosFormCronograma = () => {

        getElementosValoresCronograma().map(elemento => {
            new DBInputValor(elemento);
            elemento.addEventListener('change', () => {
                formCronograma.totalizador.dispatchEvent(new Event('change'));
            });
        });

        formCronograma.exercicio.options.length = 0;
        valoresDetalhamento.getValores().map( valor => {
            formCronograma.exercicio.add(new Option(valor.ano, valor.ano));
        });

        formCronograma.exercicio.dispatchEvent(new Event('change'));
        formCronograma.totalizador.dispatchEvent(new Event('change'));
    };

    /**
     * Calcula o totalizador do cronograma de desembolso
     */
    formCronograma.totalizador.addEventListener('change', () => {
        formCronograma.valorTotal = 0;
        getElementosValoresCronograma().map(elemento => {
            formCronograma.valorTotal += Number(elemento.value.replace(',', '.'));
        });

        formCronograma.valorTotal = formCronograma.valorTotal.toFixed(2);
        formCronograma.totalizador.value = formataValor(formCronograma.valorTotal);
    });


    const getElementosValoresCronograma = () => {
        return [...document.querySelectorAll('input.valores')];
    };

    /**
     * Busca os valores do cronograma do detalhamento
     */
    formCronograma.exercicio.addEventListener('change', () => {
        formCronograma.valorExercicio.value = formataValor(valoresDetalhamento.getValor(formCronograma.exercicio.value));

        if (detalhamento.cronograma_desembolso.length > 0) {
            cronograma = detalhamento.cronograma_desembolso.filter(cronograma => {
                if (cronograma.exercicio == formCronograma.exercicio.value) {
                    return cronograma
                }
            }).shift();

            formCronograma.codigo.value = cronograma.id;
            formCronograma.janeiro.value = cronograma.janeiro;
            formCronograma.fevereiro.value = cronograma.fevereiro;
            formCronograma.marco.value = cronograma.marco;
            formCronograma.abril.value = cronograma.abril;
            formCronograma.maio.value = cronograma.maio;
            formCronograma.junho.value = cronograma.junho;
            formCronograma.julho.value = cronograma.julho;
            formCronograma.agosto.value = cronograma.agosto;
            formCronograma.setembro.value = cronograma.setembro;
            formCronograma.outubro.value = cronograma.outubro;
            formCronograma.novembro.value = cronograma.novembro;
            formCronograma.dezembro.value = cronograma.dezembro;
        }
        formCronograma.totalizador.dispatchEvent(new Event('change'));
    });

    const validaFormDetalhamento = () => {
        try {
            if (empty(get.exercicio)) {
                throw 'Você deve informar o exercício.';
            }
            if (empty(formDetalhamento.unidade.value)) {
                throw 'Você deve selecionar a Unidade.';
            }
            if (empty(formDetalhamento.funcao.value)) {
                throw 'Você deve selecionar a Funcao.';
            }
            if (empty(formDetalhamento.subfuncao.value)) {
                throw 'Você deve selecionar a Subfunção.';
            }
            if (empty(formDetalhamento.codigoElemento.value)) {
                throw 'Você deve selecionar a Natureza de Despesa.';
            }
            if (empty(formDetalhamento.codigoRecurso.value)) {
                throw 'Você deve selecionar a Fonte de Recurso.';
            }
            if (empty(formDetalhamento.subtitulo.value) && formDetalhamento.subtitulo.value !== "0") {
                throw 'Você deve selecionar o Subtítulo.';
            }

            if (ufAtual != 'PB') {
                if (empty(formDetalhamento.caracteristica.value)) {
                    throw 'Você deve selecionar a Caracteristica Peculiar.';
                }
            }

            if (empty(formDetalhamento.esfera.value)) {
                throw 'Você deve selecionar a Esfera Orçamentaria.';
            }

            if (valoresDetalhamento.existeValoresNaoInformados()) {
                throw 'Você deve informar o valor de todos exercícios.';
            }
        } catch (e) {
            alert(e);
            return false
        }

        return true;
    };

    formDetalhamento.salvar.addEventListener('click', () => {

        if (!validaFormDetalhamento()) {
            return;
        }

        const formData = new FormData(formDetalhamento.frm);
        formData.set('pl20_orcunidade', Number(formDetalhamento.unidade.value));
        formData.set('pl20_orcfuncao', Number(formDetalhamento.funcao.value));
        formData.set('pl20_orcsubfuncao', Number(formDetalhamento.subfuncao.value));
        formData.append('pl20_codigo', formDetalhamento.codigo.value);
        formData.append('pl20_iniciativaprojativ', iniciativa.pl12_codigo);
        formData.append('pl20_anoorcamento', get.exercicio);
        formData.append('pl20_instituicao', formDetalhamento.codigoInstituicao.value);
        formData.append('pl20_orcorgao', formDetalhamento.orgao.value);
        formData.append('pl20_orcelemento', formDetalhamento.codigoElemento.value);
        formData.append('pl20_recurso', formDetalhamento.codigoRecurso.value);
        formData.append('pl20_valorbase', formDetalhamento.valorBase.value);
        formData.append('valores', JSON.stringify(valoresDetalhamento.getValores()));

        if (ufAtual == 'PB') {
            formData.append('pl20_concarpeculiar', 0);
        }

        PHPSession.appendFormData(formData);

        const parametros = {
            body: formData,
            reportMessage: `Aguarde, salvando detalhamento.`
        }

        HttpClient.post(`${PHPSession.requestApi}/${routs.detalhamento.salvar}`, parametros).then(response => {

            alert(response.message);
            if (response.error) {
                return;
            }
            detalhamento = response.data;
            formDetalhamento.codigo.value = response.data.pl20_codigo;
            abaCronograma.desbloquear();
            setDadosFormCronograma();
        });
    });

    const validaCronograma = () => {

        const valorDetalhamento = detalhamento.valores.filter(valor => {
            if (valor.pl10_ano == formCronograma.exercicio.value) {
                return valor;
            }
        }).shift();

        try {
            if (formCronograma.valorTotal > valorDetalhamento.pl10_valor) {
                throw 'Valor informado no cronograma não pode ser maior que o recurso alocado para o exercício.';
            }
            if (formCronograma.valorTotal < valorDetalhamento.pl10_valor) {
                throw 'Valor informado no cronograma não pode ser menor que o recurso alocado para o exercício.';
            }
        } catch (e) {
            alert(e);
            return false;
        }

        return true;
    };

    formCronograma.salvar.addEventListener('click', () => {

        if (!validaCronograma()) {
            return;
        }
        const formData = new FormData();
        formData.append('id', formCronograma.codigo.value);
        formData.append('exercicio', formCronograma.exercicio.value);
        formData.append('detalhamentoiniciativa_id', detalhamento.pl20_codigo);

        getElementosValoresCronograma().map(elemento => {
            formData.append(elemento.name, elemento.value.replace(',', '.'));
        });

        const parametros = {
            body: formData,
            reportMessage: `Aguarde, salvando cronograma.`
        }

        HttpClient.post(`${PHPSession.requestApi}/${routs.cronograma.salvar}`, parametros).then(response => {
            alert(response.message);
            if (response.error) {
                return;
            }

            atualizaCronograma(response.data);
        });
    });

    const atualizaCronograma = (cronograma) => {
        let index = detalhamento.cronograma_desembolso.findIndex(obj => obj.id === cronograma.id);
        detalhamento.cronograma_desembolso[index] = cronograma;
    }

    const formataValor = (value) => {
        let numObj = parseFloat(value);

        return numObj.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    };


    const btnRecalculo = document.getElementById('btnRecalcularCronograma');
    const modalRecalculo = {
        container: document.getElementById('modalRecalculoCronograma'),
        salvar : document.getElementById('btnSalvarRecalculo'),
        linhaMeses : document.getElementById('linhaMeses'),
        cboFormula : document.getElementById('formulaRecalculo'),
        cboMes : document.getElementById('mesRecalculo'),
        cntAnos : document.getElementById('cntAnos'),
    }

    var windowRecalculo = new windowAux('windowRecalculo', 'Recalcula o cronograma', 450, 300);
    windowRecalculo.setContent(modalRecalculo.container);
    windowRecalculo.allowCloseWithEsc(false);
    windowRecalculo.setShutDownFunction(() => {
        if (!!windowRecalculo.oDBMask) {
            windowRecalculo.oDBMask.destroy();
        }
    });

    modalRecalculo.cboFormula.addEventListener('change', () => {
        modalRecalculo.linhaMeses.style.display = 'none';
        if (modalRecalculo.cboFormula.value == 2) {
            modalRecalculo.linhaMeses.style.display = 'table-row';
        }
    })


    const montaCheckAno = (ano) => {

        const id = `check_ano_${ano}`;

        const div = document.createElement('div');
        const input = document.createElement('input');
        const label = document.createElement('label');
        input.className = 'recalcula_ano';
        input.type = 'checkbox';
        input.dataset.ano = ano;
        input.setAttribute('id', id);
        label.setAttribute('for', id);
        label.innerText = ano;
        div.append(input);
        div.append(label);
        return div
    }

    const montaFormRecalculo = () => {
        modalRecalculo.cntAnos.innerHTML = '';
        for (let valor of valoresDetalhamento.getValores()) {
            modalRecalculo.cntAnos.append(montaCheckAno(valor.ano));
        }
    };

    btnRecalculo.addEventListener('click', () => {
        montaFormRecalculo();
        modalRecalculo.container.style.display = '';
        windowRecalculo.show(0, 0, true);
    });

    modalRecalculo.salvar.addEventListener('click', () => {
       const inputs = document.querySelectorAll('input[type=checkbox].recalcula_ano:checked');
        if (inputs.length === 0) {
            alert('Você deve marcar um ou mais ano(s)');
            return;
        }

        const formData = new FormData();
        formData.append('detalhamentoiniciativa_id', detalhamento.pl20_codigo);
        formData.append('formula', modalRecalculo.cboFormula.value);
        formData.append('mes', modalRecalculo.cboMes.value);

        for (let inputAno of inputs ) {
            formData.append('anos[]', inputAno.dataset.ano);
        }
        PHPSession.appendFormData(formData);

        const parametros = {
            body: formData,
            reportMessage: `Aguarde, recalculando cronograma.`
        }

        HttpClient.post(`${PHPSession.requestApi}/${routs.cronograma.recalcular}`, parametros).then(response => {

            alert(response.message);
            if (response.error) {
                return;
            }
            console.log(response.data)

            for (let cronograma of response.data) {
                atualizaCronograma(cronograma);
            }

            formCronograma.exercicio.dispatchEvent(new Event('change'));
        });
    });
</script>
</body>
