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
    <script type="text/javascript" src="scripts/widgets/Input/DBInput.widget.js"></script>
    <script type="text/javascript" src="scripts/widgets/Input/DBInputValor.widget.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBAbas.widget.js"></script>

    <style>
        table.metas {
            width: 100%;
            border-collapse: collapse;
        }

        table.metas tr {
            height: 25px;
        }

        table.metas th, table.metas td {
            border: 1px solid;
        }

        table.metas th {
            text-align: center;
        }
    </style>
</head>
<body class="body-default">

<div id='ctnAbas'></div>
<button style="top: 3px; right: 15px; position: fixed" rel='ignore-css' class="retornar" type="button">
    <i class="fas fa-arrow-left"></i>
    Retornar
</button>

<div id="abaIniciativa">
    <form id="formIniciativa" class="container">
        <fieldset>
            <legend>Dados da Iniciativa</legend>
            <table class="form-container">
                <tr>
                    <td><label for="programa">Programa: </label></td>
                    <td>
                        <input type="text" id="programa" name="programa" readonly
                               class="readonly field-size2">
                        <input type="text" id="nomePrograma" name="nomePrograma" readonly
                               class="readonly field-size8">
                    </td>
                </tr>
                <tr>
                    <td><label for="objetivo">Objetivo: </label></td>
                    <td>
                        <select id="objetivo" name="objetivo" style="width: 430px;">
                            <option value="">Selecione um objetivo</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label id="ancoraAcao" for="codigoAcao"><a href="#">Ação: </a></label></td>
                    <td>
                        <input type="text" id="codigoAcao" name="pl12_orcprojativ" lang="o55_projativ"
                               class="field-size2">
                        <input type="text" id="descricaoAcao" name="descricaoAcao" lang="o55_descr"
                               class="field-size8 readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td><label for="produto">Produto: </label></td>
                    <td>
                        <input type="text" id="produto" name="produto" class="field-size-max readonly" readonly>
                    </td>
                </tr>
                <tr>
                    <td><label for="origemIniciativa">Origem: </label></td>
                    <td>
                        <select id="origemIniciativa" name="pl12_origeminiciativa">
                            <option value="">Selecione uma Origem</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="periodoIniciativa">Período: </label></td>
                    <td>
                        <select id="periodoIniciativa" name="pl12_periodoacao">
                            <option value="">Selecione o Período</option>
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>

        <input type="hidden" id="codigoIniciativa" name="pl12_codigo">
        <button type="button" id="btnSalvarIniciativa">
            <i class="far fa-save"></i>
            Salvar
        </button>
        <button type="button" id="btnNovo">
            <i class="far fa-file"></i>
            Novo
        </button>
    </form>
</div>

<div id="abaMetas" style="display: none">
    <div id="helpValores" class="alert alert-primary text-left" role="alert">
        Se o objetivo da iniciativa for lançado, o controle dos valores das metas financeiras será pelo saldo do objetivo, caso contrário, o controle será pelo saldo do Programa.<br>
        Na alteração da Iniciativa, os valores de saldo do Programa/Objetivo é o seu valor total menos os valores das iniciativas já associadas a esse Programa/objetivo sem considerar os valores da iniciativa a ser editada.
    </div>

    <form id="frmMetas" class="container">
        <fieldset id="ctnValores">
            <legend>Saldos</legend>
            <fieldset class="separator">
                <legend>Saldo do Programa</legend>
                <div id="saldoPrograma" style="display: flex; justify-content: center;"></div>
            </fieldset>
            <fieldset class="separator">
                <legend>Saldo do Objetivo</legend>
                <div id="saldoObjetivo" style="display: flex; justify-content: center;"></div>
            </fieldset>
        </fieldset>
        <fieldset style="width: 1000px;">
            <legend>Metas</legend>
            <table id="tableMetas" class="metas">
                <tr>
                    <th colspan="2">Metas Financeiras</th>
                    <th colspan="2">Metas Físicas</th>
                </tr>
                <tr>
                    <th class="field-size2">Exercício</th>
                    <th class="field-size3">Valor</th>
                    <th>Unidade de Medida</th>
                    <th class="field-size3">Valor</th>
                </tr>
            </table>
        </fieldset>
        <div id="modalUnidade" class="container">
        <fieldset>
            <table class="form-container">
              <tr>
                <td>
                  <label><b>Ano:</b></label>
                  <input readonly type="text" name="ano_unidade" id="ano_unidade" width = '80px' value="">
                </td>              
              </tr>
              <tr>  
                <td> 
                  <label>Unidade:</label>     
                  <select id = 'unidadeMedida' name = 'unidadeMedida' onChange = 'js_controleUnidadeMedida()'
                      style= 'width:150px'>
                  <option value="0">PERSONALIZADO</option>
                  <?php                                
                    $clSigfisUnidadeMedida = new cl_sigfisunidademedida;
                    $sqlSigfisUnidade = $clSigfisUnidadeMedida->sql_query_file(
                      null,
                      "o220_sequencial, 
                      o220_abreviacao");
                    $rsSigfisUnidade = db_query($sqlSigfisUnidade);
                    for($i = 0; $i < pg_num_rows($rsSigfisUnidade); $i++){
                      $unidadeMedidaObjeto = db_utils::fieldsMemory($rsSigfisUnidade,$i);
                      $optionTag = "<option value = '$unidadeMedidaObjeto->o220_sequencial'>";
                      $optionTag .= "$unidadeMedidaObjeto->o220_abreviacao</option>";
                      echo $optionTag;
                    }                    
                  ?>
                  </select>  
                  <?php 
                    db_input('descricao_unidade',10,3,true,'text',1);                    
                  ?>                                      
                </td>
              </tr>
            </table>
        </fieldset>
        
        <button class="btn btn-light" id="btnAdicionarUnidade">
            <i class="fa fa-plus-circle" aria-hidden="true"></i>
            Adicionar
        </button>
        </div>           
        <button type="button" id="btnSalvarMetas">
            <i class="far fa-save"></i>
            Salvar
        </button>
    </form>
</div>
<div id="abaRegionalizacao" style="display: none">

    <div class="alert alert-primary text-left" role="alert">
        Informe os subtitulos que compõem a iniciativa e clique em <kbd>salvar</kbd>.
    </div>

    <form id="formRegionalizacao" class="container">
        <div style="width: 680px;" id="ctnLancadorSubtitulo"></div>

        <button type="button" id="btnSalvarRegionalizacao">
            <i class="far fa-save"></i>
            Salvar
        </button>
        <button type="button" id="btnExcluirRegionalizacao">
            <i class="far fa-trash-alt"></i>
            Excluir Todos
        </button>
    </form>
</div>

<div id="abaAbrangencia" style="display: none">
    <div class="alert alert-primary text-left" role="alert">
        Informe as Abrangências que compõem a iniciativa e clique em <kbd>salvar</kbd>.
    </div>

    <form id="formAbrangencia" class="container">
        <div style="width: 680px;" id="ctnLancadorAbrangencia"></div>

        <button type="button" id="btnSalvarAbrangencia">
            <i class="far fa-save"></i>
            Salvar
        </button>
        <button type="button" id="btnExcluirAbrangencia">
            <i class="far fa-trash-alt"></i>
            Excluir Todas
        </button>
    </form>
</div>

<?php db_menu() ?>
<script type="text/javascript" src="scripts/session.js"></script>

<script rel="script" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>

<script type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
<script type="text/javascript" src="scripts/classes/planejamento/valores.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/windowAux.widget.js"></script>

<script type="text/javascript" src='extension/package/Desktop/assets/vendors/alertify/alertify.js'></script>
<!-- requires bootstrap table -->
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">

<script type="text/javascript">

    /**
     * Variaveis globais
     */
    var plano = {}
    var dadosIniciaitiva = {}
    var programaEstrategico = {}
    let isRJ = '<?=isRioDeJaneiro()?>';

    // Abas
    const ctnAbaIniciativa = document.getElementById('abaIniciativa')
    const ctnAbaMetas = document.getElementById('abaMetas')
    const ctnAbaRegionalizacao = document.getElementById('abaRegionalizacao')
    const ctnAbaAbrangencia = document.getElementById('abaAbrangencia')

    const ctnAbas = new DBAbas(document.getElementById('ctnAbas'));
    const abaIniciativa = ctnAbas.adicionarAba("Iniciativas", ctnAbaIniciativa);
    const abaMetas = ctnAbas.adicionarAba("Metas", ctnAbaMetas);
    const abaRegionalizacao = ctnAbas.adicionarAba("Regionalização", ctnAbaRegionalizacao);
    const abaAbrangencia = ctnAbas.adicionarAba("Abrangência", ctnAbaAbrangencia);

    abaMetas.bloquear();
    abaRegionalizacao.bloquear();
    abaAbrangencia.bloquear();

    var lancadorRegionalizacao = new DBLancador("lancadorSubtitulos");
    var lancadorAbrangencia = new DBLancador("lancadorAbrangencia");
    var configuracao = {};

    $.noConflict();
    jQuery(document).ready(function (jQuery) {
        const get = js_urlToObject();
        // rotas
        const routs = {
            periodos: 'financeiro/planejamento/periodos',
            origens: 'financeiro/planejamento/origens',
            configuracao: 'financeiro/planejamento/configuracao',
            programa: {
                show: 'financeiro/planejamento/programas-estrategico',
                saldo: 'financeiro/planejamento/programas-estrategico/saldo/iniciativas'
            },
            objetivo: {
                saldo: 'financeiro/planejamento/objetivo-programa/saldo/iniciativas'
            },
            iniciativa: {
                show: 'financeiro/planejamento/iniciativa',
                salvar: 'financeiro/planejamento/iniciativa/salvar'
            },
            metas: {
                salvar: 'financeiro/planejamento/metas-iniciativa/salvar'
            },
            regionalizacao: {
                salvar: 'financeiro/planejamento/regionalizacao/salvar',
                excluir: 'financeiro/planejamento/regionalizacao/excluir'
            },
            abrangencia: {
                salvar: 'financeiro/planejamento/abrangencia/salvar',
                excluir: 'financeiro/planejamento/abrangencia/excluir'
            },
        };

        const btnNovo = document.getElementById('btnNovo');
        const btnAdicionarUnidade = document.getElementById('btnAdicionarUnidade');

        /** ------------------------------------- IMPUTS DA ABA INICIATIVA ------------------------------------------*/
        const formIniciativa = {
            form: document.getElementById('formIniciativa'),
            idPrograma: null,
            programa: document.getElementById('programa'),
            objetivo: document.getElementById('objetivo'),
            nomePrograma: document.getElementById('nomePrograma'),
            ancoraAcao: document.getElementById('ancoraAcao'),
            codigoAcao: document.getElementById('codigoAcao'),
            descricaoAcao: document.getElementById('descricaoAcao'),
            produto: document.getElementById('produto'),
            origem: document.getElementById('origemIniciativa'),
            periodo: document.getElementById('periodoIniciativa'),
            codigo: document.getElementById('codigoIniciativa'),
            salvar: document.getElementById('btnSalvarIniciativa'),
        };

        /** ------------------------------------- INPUTS DA ABA META ------------------------------------------------*/

        const formMeta = {
            form: document.getElementById('frmMetas'),
            ctnSaldoPrograma: document.getElementById('saldoPrograma'),
            ctnSaldoObjetivo: document.getElementById('saldoObjetivo'),
            table: document.getElementById('tableMetas'),
            salvar: document.getElementById('btnSalvarMetas'),
            valoresSaldoPrograma: new Valores(),
            valoresSaldoObjetivo: new Valores(),

        }
        /** ------------------------------------- INPUTS DA ABA REGIONALIZACAO --------------------------------------*/
        const formRegionalizacao = {
            form: document.getElementById('formRegionalizacao'),
            containerLancador: document.getElementById('ctnLancadorSubtitulo'),
            salvar: document.getElementById('btnSalvarRegionalizacao'),
            excluir: document.getElementById('btnExcluirRegionalizacao')
        };

        lancadorRegionalizacao.iGridHeight = 180;
        lancadorRegionalizacao.sTextoFieldset = 'Subtítulos';
        lancadorRegionalizacao.selecionarAposPesquisar = true;
        lancadorRegionalizacao.setLabelAncora("Subtítulo:");
        lancadorRegionalizacao.setNomeInstancia("lancadorRegionalizacao");
        lancadorRegionalizacao.setHabilitado(true);
        lancadorRegionalizacao.setParametrosPesquisa(
            "func_ppasubtitulolocalizadorgasto.php",
            ["o11_sequencial", "o11_descricao"]
        );
        lancadorRegionalizacao.show(formRegionalizacao.containerLancador);

        /** ------------------------------------- INPUTS DA ABA ABRANGÊNCIA -----------------------------------------*/
        const formAbrangencia = {
            form: document.getElementById('formAbrangencia'),
            containerLancador: document.getElementById('ctnLancadorAbrangencia'),
            salvar: document.getElementById('btnSalvarAbrangencia'),
            excluir: document.getElementById('btnExcluirAbrangencia')
        };

        lancadorAbrangencia.iGridHeight = 180;
        lancadorAbrangencia.sTextoFieldset = 'Abrangências';
        lancadorAbrangencia.selecionarAposPesquisar = true;
        lancadorAbrangencia.setLabelAncora("Abrangência:");
        lancadorAbrangencia.setNomeInstancia("lancadorAbrangencia");
        lancadorAbrangencia.setHabilitado(true);
        lancadorAbrangencia.setParametrosPesquisa("func_abrangencia.php", ["pl18_codigo", "pl18_descricao"]);
        lancadorAbrangencia.show(formAbrangencia.containerLancador);

        /** ----------------------------------- FUNÇÕES DA ABA INICIATIVA -------------------------------------------*/
        const lookUpProjeto = new DBLookUp(
            formIniciativa.ancoraAcao,
            formIniciativa.codigoAcao,
            formIniciativa.descricaoAcao,
            {
                'sArquivo': 'func_orcprojativplanejamento.php',
                'sLabel': 'Pesquisar Ações',
                'sObjetoLookUp': "db_iframe_orcprojativ",
                'aCamposAdicionais': ['db_produto']
            }
        );

        const preencheFormProjeto = (codigo, label, produto) => {
            formIniciativa.codigoAcao.value = codigo;
            formIniciativa.descricaoAcao.value = label;
            formIniciativa.produto.value = produto;
        };

        lookUpProjeto.setCallBack('onClick', retorno => {
            preencheFormProjeto(retorno[0], retorno[1], retorno[2]);
        });

        lookUpProjeto.setCallBack('onChange', (erro, retorno) => {
            if (erro) {
                preencheFormProjeto('', retorno[0], '');
                return;
            }

            preencheFormProjeto(formIniciativa.codigoAcao.value, retorno[0], retorno[3]);
        });

        const buscaOrigem = () => {
            HttpClient.get(`${PHPSession.requestApi}/${routs.origens}`).then(response => {
                response.data.map(origem => {
                    formIniciativa.origem.add(new Option(origem.pl13_descricao, origem.pl13_codigo));
                });
            });
        }

        const buscaPeriodo = () => {
            HttpClient.get(`${PHPSession.requestApi}/${routs.periodos}`).then(response => {
                response.data.map(periodo => {
                    formIniciativa.periodo.add(new Option(periodo.pl14_descricao, periodo.pl14_codigo));
                });
            });
        };

        const setDadosProgramaEstrategico = () => {
            criaComboObjetivo();
            formIniciativa.idPrograma = programaEstrategico.pl9_codigo;
            formIniciativa.programa.value = programaEstrategico.programa;
            formIniciativa.nomePrograma.value = programaEstrategico.descricao;
        };

        const criaComboObjetivo = () => {
            formIniciativa.objetivo.options.length = 0;
            formIniciativa.objetivo.add(new Option('Selecione um objetivo', ''));
            for (let objetivo of programaEstrategico.objetivos) {
                formIniciativa.objetivo.add(new Option(`${objetivo.pl11_numero} - ${objetivo.pl11_descricao}`, objetivo.pl11_codigo));
            }
        };

        const setDadosFormIniciativa = () => {
            setDadosProgramaEstrategico();
            formIniciativa.codigoAcao.value = dadosIniciaitiva.acao;
            formIniciativa.descricaoAcao.value = dadosIniciaitiva.descricao_acao;
            formIniciativa.produto.value = dadosIniciaitiva.descricao_produto;
            if (dadosIniciaitiva.pl12_origeminiciativa) {
                formIniciativa.origem.value = dadosIniciaitiva.pl12_origeminiciativa;
            }
            if (dadosIniciaitiva.pl12_periodoacao) {
                formIniciativa.periodo.value = dadosIniciaitiva.pl12_periodoacao;
            }
            formIniciativa.codigo.value = dadosIniciaitiva.pl12_codigo;

            if (dadosIniciaitiva.objetivos.length > 0) {
                let objetivo = dadosIniciaitiva.objetivos[0];
                formIniciativa.objetivo.value = objetivo.pl11_codigo;
            }
        }

        PHPSession.loadData().then(() => {
            let urlNovo = 'pla4_iniciativasmanutencao.php?';
            buscaOrigem();
            buscaPeriodo();
            buscaConfiguracao();
            if (get.programa && !get.codigo) {
                urlNovo += `programa=${get.programa}`;
                HttpClient.get(`${PHPSession.requestApi}/${routs.programa.show}/${get.programa}`).then(response => {
                    programaEstrategico = response.data;
                    plano = programaEstrategico.planejamento;
                    lookUpProjeto.setParametrosAdicionais(['previsao=true', `ano=${plano.pl2_ano_inicial}`]);

                    setDadosProgramaEstrategico();
                    criaTabelaMetas();
                });
            }

            if (get.codigo) {
                HttpClient.get(`${PHPSession.requestApi}/${routs.iniciativa.show}/${get.codigo}`).then(response => {
                    dadosIniciaitiva = response.data;
                    programaEstrategico = dadosIniciaitiva.programa_estrategico;

                    urlNovo += `programa=${programaEstrategico.pl9_codigo}`;
                    plano = programaEstrategico.planejamento;

                    setDadosFormIniciativa();

                    lookUpProjeto.setParametrosAdicionais(['previsao=true', `ano=${plano.pl2_ano_inicial}`]);
                    formIniciativa.codigo.value = get.codigo;
                    criaTabelaMetas();
                    buscaSaldos();
                    setValoresMetas();
                    liberaAbas();

                    setRegionalizacoes();
                    setAbrangencias();
                });
            }

            ctnAbaMetas.style.display = '';
            ctnAbaRegionalizacao.style.display = '';
            ctnAbaAbrangencia.style.display = '';

            btnNovo.addEventListener('click', () => {
                location.href = urlNovo;
            });
        });

        const buscaConfiguracao = () => {
            HttpClient.get(`${PHPSession.requestApi}/${routs.configuracao}`).then(response => {
                configuracao = response.data;

                if (configuracao.apenas_valor_analitico) {
                    document.getElementById('helpValores').style.display = 'none';
                    document.getElementById('ctnValores').style.display = 'none';
                }
            });
        };

        formIniciativa.salvar.addEventListener('click', () => {
            if (formIniciativa.codigoAcao.value == '') {
                alert('Você deve informar a Ação');
                return;
            }

            const formData = new FormData();
            formData.append('pl12_codigo', formIniciativa.codigo.value);
            formData.append('pl12_objetivo', formIniciativa.objetivo.value);
            formData.append('pl12_orcprojativ', Number(formIniciativa.codigoAcao.value));
            formData.append('pl12_anoorcamento', plano.pl2_ano_inicial);
            formData.append('pl12_programaestrategico', programaEstrategico.pl9_codigo);
            formData.append('pl12_origeminiciativa', formIniciativa.origem.value);
            formData.append('pl12_periodoacao', formIniciativa.periodo.value);
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, salvando iniciativa.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.iniciativa.salvar}`, parametros).then(response => {

                alert(response.message);
                if (response.error) {
                    return;
                }

                dadosIniciaitiva = response.data;
                dadosIniciaitiva.pl12_programaestrategico = response.data.pl12_programaestrategico;
                dadosIniciaitiva.pl12_periodoacao = response.data.pl12_periodoacao;
                dadosIniciaitiva.pl12_orcprojativ = response.data.pl12_orcprojativ;
                dadosIniciaitiva.pl12_anoorcamento = response.data.pl12_anoorcamento;
                dadosIniciaitiva.pl12_codigo = response.data.pl12_codigo;

                formIniciativa.codigo.value = dadosIniciaitiva.pl12_codigo;

                buscaSaldos();
                liberaAbas();

                abaIniciativa.setVisibilidade(false);
                abaMetas.setVisibilidade(true);
            });
        });

        /** ----------------------------------- FUNÇÕES DA ABA METAS ------------------------------------------------*/

        const createInput = (classes, ano) => {
            let input = document.createElement('input');
            input.dataset.ano = ano;
            input.className = ` field-size-max ${classes}`;
            input.style.paddingLeft = '1px';
            input.style.paddingRight = '1px';
            input.style.height = '25px';

            return input;
        };

        const createInputValor = (classe, ano) => {
            let input = createInput(classe, ano);
            input.setAttribute('maxlength', 15);

            new DBInputValor(input);
            return input;
        };

        const criaTabelaMetas = () => {

            const configuracaoValores = {
                container: {"tableCell": true},
                input: {"readOnly": true},
            };
            formMeta.valoresSaldoPrograma.criaInputValores(formMeta.ctnSaldoPrograma, plano, configuracaoValores);
            formMeta.valoresSaldoObjetivo.criaInputValores(formMeta.ctnSaldoObjetivo, plano, configuracaoValores);

            if (formIniciativa.objetivo.value !== '') {
                formMeta.ctnSaldoObjetivo.parentElement.style.display = 'block';
            }

            for (let ano = plano.pl2_ano_inicial; ano <= plano.pl2_ano_final; ano++) {
                let row = formMeta.table.insertRow();
                row.style.padding = '3px 0 3px 0';
                row.style.height = '25px';
                let cellLabel = row.insertCell();                
                cellLabel.className = 'bold';
                cellLabel.innerText = ano;
                let cellIdUnidade = row.insertCell();
                cellIdUnidade.setAttribute('style','display:none');

                let cellValorMetaFinanceira = row.insertCell();                
                let cellUnidade = row.insertCell();
                let cellValorMetaFisica = row.insertCell();
                
                cellValorMetaFinanceira.append(createInputValor('meta-financeira valor', ano));
                cellUnidade.append(createInput('unidade', ano));
                cellIdUnidade.append(createInput('id_unidade', ano));
                cellValorMetaFisica.append(createInputValor('meta-fisica valor', ano));
            }
        };

        const buscaSaldoPrograma = () => {
            const formData = new FormData()
            formData.append('pl9_codigo', programaEstrategico.pl9_codigo);
            formData.append('pl12_codigo', dadosIniciaitiva.pl12_codigo);
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, calculando saldo do programa estratégico.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.programa.saldo}`, parametros).then(response => {
                if (response.error) {
                    alert(response.message);
                    return
                }
                for (let valor of response.data) {
                    formMeta.valoresSaldoPrograma.set(valor.pl10_ano, valor.pl10_valor);
                }
            });
        };

        const buscaSaldoObjetivo = () => {
            const formData = new FormData()
            formData.append('pl11_codigo', formIniciativa.objetivo.value);
            formData.append('pl12_codigo', dadosIniciaitiva.pl12_codigo);
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, calculando saldo do objetivo.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.objetivo.saldo}`, parametros).then(response => {
                if (response.error) {
                    alert(response.message);
                    return
                }
                for (let valor of response.data) {
                    formMeta.valoresSaldoObjetivo.set(valor.pl10_ano, valor.pl10_valor);
                }
            });
        };

        const buscaSaldos = () => {

            if (configuracao.apenas_valor_analitico) {
                return true;
            }

            buscaSaldoPrograma();
            formMeta.valoresSaldoObjetivo.reset();
            if (formIniciativa.objetivo.value !== '') {
                buscaSaldoObjetivo();
            }
        };

        const getInputMeta = (ano, classe) => {
            return formMeta.table.querySelector(`input[data-ano="${ano}"].${classe}`);
        };

        const setValoresMetas = () => {
            for (let meta of dadosIniciaitiva.metas) {
                getInputMeta(meta.exercicio, 'meta-financeira').value = meta.meta_financeira;
                getInputMeta(meta.exercicio, 'meta-fisica').value = meta.meta_fisica;
                
                /**
                 * Bloco de controle para o RJ no qual há uma
                 * vinculo padrão de unidades de medidas fixas com a ação
                 */                
                if(isRJ){
                    getInputMeta(meta.exercicio, 'unidade').addEventListener('click',() => {
                        WindowUnidade.show(0, 0, true);
                        document.getElementById('descricao_unidade').style.display = 'initial';
                        document.getElementById('ano_unidade').value = meta.exercicio;                                                     
                        document.getElementById('unidadeMedida').value = '0'; 
                        document.getElementById('descricao_unidade').value = '';
    
                        document.getElementById('unidadeMedida').value = getInputMeta(meta.exercicio, 'id_unidade').value;
                        document.getElementById('descricao_unidade').value = getInputMeta(meta.exercicio, 'unidade').value;
                        /**
                         * Na inicialização do componente
                         */
                        if(getInputMeta(meta.exercicio, 'id_unidade').value.length == 0){
                            document.getElementById('unidadeMedida').value = '0';                       
                        } 
                        
                        /**
                         * No controle do componente 
                         * */
                        if(document.getElementById('unidadeMedida').value != '0'){
                            document.getElementById('descricao_unidade').style.display = 'none';
    
                        } else {
                            document.getElementById('descricao_unidade').style.display = 'initial';
                        }                                                           
                    });  
                }
                              
                getInputMeta(meta.exercicio, 'unidade').value = meta.unidade;                            
            }
            
            /**
             * BLoco apenas para o RJ. Para os demais clientes do e-cidade
             * não há iteração. 
             */
            for (let vinculo of dadosIniciaitiva.vinculoSigfisUnidadeMedida){
                getInputMeta(vinculo.o221_anousu, 'id_unidade').value = vinculo.o221_unidade;
            }
        }

        const getMetasForm = () => {
            const metas = [];
            for (let ano = plano.pl2_ano_inicial; ano <= plano.pl2_ano_final; ano++) {
                let vlrMetaFinanceira = getInputMeta(ano, 'meta-financeira').value.replace(',', '.');
                let vlrMetaFisica = getInputMeta(ano, 'meta-fisica').value.replace(',', '.');
                
                /**
                 * o idUnidade funciona apenas para o RJ
                 *  */
                let idUnidade = '';
                if(getInputMeta(ano, 'id_unidade').value != undefined 
                    && getInputMeta(ano, 'id_unidade').value != ''){
                    idUnidade = getInputMeta(ano, 'id_unidade').value;
                }

                metas.push({
                    "exercicio": ano,
                    "meta_financeira": Number(vlrMetaFinanceira),                    
                    "unidade": getInputMeta(ano, 'unidade').value,
                    "id_unidade": idUnidade,
                    "meta_fisica": vlrMetaFisica
                });
            }

            return metas;
        };

        const validarValoresMeta = (metas) => {
            if (configuracao.apenas_valor_analitico) {
                return true;
            }
            try {
                for (let meta of metas) {
                    if (meta.meta_financeira === '') {
                        throw 'Você deve informar os valores das "Metas Financeiras".';
                    }
                    // se não tem objetivo, validar pelo saldo do programa
                    let saldoAno = formMeta.valoresSaldoPrograma.getValor(meta.exercicio);
                    let msg = 'disponível no Saldo do programa.';

                    if (formIniciativa.objetivo.value !== '') {
                        saldoAno = formMeta.valoresSaldoObjetivo.getValor(meta.exercicio)
                        msg = 'disponível no Saldo do objetivo.';
                    }
                    if (meta.meta_financeira > saldoAno) {
                        throw `Valor da "Meta Financeira" no exercício ${meta.exercicio} é maior que o ${msg}`;
                    }
                }
            } catch (e) {
                alert(e);
                return false;
            }
            return true;
        }

        formMeta.salvar.addEventListener('click', () => {
            let metas = getMetasForm();

            if (!validarValoresMeta(metas)) {
                return;
            }

            const formData = new FormData();
            formData.append('pl12_codigo', dadosIniciaitiva.pl12_codigo);
            for (let meta of metas) {
                if (meta.meta_financeira === '') {
                    alert('Você deve inforar os valores das "Metas Financeiras".');
                    return;
                }
                formData.append('metas[]', JSON.stringify(meta));
            }
            PHPSession.appendFormData(formData);

            const parametros = {
                body: formData,
                reportMessage: `Aguarde, salvando metas.`
            }

            HttpClient.post(`${PHPSession.requestApi}/${routs.metas.salvar}`, parametros).then(response => {

                alert(response.message);
                if (response.error) {
                    return;
                }

                dadosIniciaitiva.metas = response.dados;
            });
        });

        /** ----------------------------------- FUNÇÕES DA ABA REGIONALIZAÇÃO ---------------------------------------*/
        const setRegionalizacoes = () => {
            let regionalizacoes = dadosIniciaitiva.regionalizacoes.map(regionalizacao => {
                return [regionalizacao.o11_sequencial, regionalizacao.o11_descricao];
            });

            lancadorRegionalizacao.carregarRegistros(regionalizacoes);
        }

        formRegionalizacao.salvar.addEventListener('click', () => {
            if (lancadorRegionalizacao.getRegistros().length === 0) {
                alert('Informe ao menos um subtítulo se deseja para salvar.');
                return;
            }

            const formData = new FormData();
            formData.append('pl12_codigo', dadosIniciaitiva.pl12_codigo);

            lancadorRegionalizacao.getRegistros().map(regionalizacao => {
                formData.append('regionalizacoes[]', regionalizacao.sCodigo);
            });

            const parametros = {
                body: formData,
                reportMessage: 'Aguarde, salvando regionalização.'
            }

            let rota = `${PHPSession.requestApi}/${routs.regionalizacao.salvar}`;
            HttpClient.post(rota, parametros).then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }

                dadosIniciaitiva.regionalizacoes = response.data;
            });
        });

        formRegionalizacao.excluir.addEventListener('click', () => {

            let rota = `${PHPSession.requestApi}/${routs.regionalizacao.excluir}`;
            httpExecuta(rota, 'Aguarde, excluindo Regionalizações.');
            dadosIniciaitiva.regionalizacoes = [];
            lancadorRegionalizacao.clearAll();
        });

        /** ----------------------------------- FUNÇÕES DA ABA ABRANGÊNCIA ------------------------------------------*/
        const setAbrangencias = () => {
            let abrangencias = dadosIniciaitiva.abrangencias.map(abrangencia => {
                return [abrangencia.pl18_codigo, abrangencia.pl18_descricao];
            });

            lancadorAbrangencia.carregarRegistros(abrangencias);
        }

        formAbrangencia.salvar.addEventListener('click', () => {
            if (lancadorAbrangencia.getRegistros().length === 0) {
                alert('Informe ao menos uma abrangência para salvar.');
                return;
            }

            const formData = new FormData();
            formData.append('pl12_codigo', dadosIniciaitiva.pl12_codigo);

            lancadorAbrangencia.getRegistros().map(regionalizacao => {
                formData.append('abrangencias[]', regionalizacao.sCodigo);
            });

            const parametros = {
                body: formData,
                reportMessage: 'Aguarde, salvando abrangência.'
            }

            let rota = `${PHPSession.requestApi}/${routs.abrangencia.salvar}`;
            HttpClient.post(rota, parametros).then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }

                dadosIniciaitiva.abrangencias = response.data;
            });
        });

        formAbrangencia.excluir.addEventListener('click', () => {
            httpExecuta(`${PHPSession.requestApi}/${routs.abrangencia.excluir}`, 'Aguarde, excluindo Abrangência.');
            dadosIniciaitiva.abrangencias = [];
            lancadorAbrangencia.clearAll();
        });

        const httpExecuta = (rota, message) => {
            const formData = new FormData();
            formData.append('pl12_codigo', dadosIniciaitiva.pl12_codigo);

            PHPSession.appendFormData(formData);

            message = !empty(message) ? message : 'Aguarde, ...';

            const parametros = {
                body: formData,
                reportMessage: message
            }
            HttpClient.post(rota, parametros).then((response) => {
                alert(response.message);
                if (response.error) {
                    return;
                }
                return true
            });
        };

        /**
         * Botão de retorno
         */
        document.querySelectorAll('.retornar').forEach(el => el.addEventListener('click', event => {
            let parametros = [
                `planejamento=${get.planejamento}`,
                `programa=${get.programa}`
            ];
            location.href = `pla4_iniciativas.php?${parametros.join('&')}`;
        }));

        const modalUnidade     = document.getElementById('modalUnidade');
        const hideWindowUnidade = () => {
            
            if (!!WindowUnidade.oDBMask) {
                WindowUnidade.oDBMask.destroy();
            }
            
          WindowUnidade.hide();
        }
        
        var WindowUnidade = new windowAux('WindowUnidade', 'Escolher Unidade', 600, 400);
        WindowUnidade.setContent(modalUnidade);
        WindowUnidade.allowCloseWithEsc(true);
        WindowUnidade.setShutDownFunction(function () {
          hideWindowUnidade();
        });   

        btnAdicionarUnidade.on('click',() => {
            
            let ano = document.getElementById('ano_unidade').value;
            let unidadeMedidaElement = document.getElementById('unidadeMedida');

            getInputMeta(ano, 'id_unidade').value = unidadeMedidaElement.value;

            if(unidadeMedidaElement.value == 0){
                getInputMeta(ano, 'unidade').value = document.getElementById('descricao_unidade').value;
            } else {
                getInputMeta(ano, 'unidade').value = unidadeMedidaElement[unidadeMedidaElement.selectedIndex].text;
            }            
            hideWindowUnidade();
        });                    
    });

    function js_controleUnidadeMedida(){
        if(document.getElementById('unidadeMedida').value != '0'){
            let unidadeMedidaElement = document.getElementById('unidadeMedida');            
            document.getElementById('descricao_unidade').value =  unidadeMedidaElement[unidadeMedidaElement.selectedIndex].text;
            document.getElementById('descricao_unidade').style.display = 'none';
        } else {      
          document.getElementById('descricao_unidade').value = '';
          document.getElementById('descricao_unidade').style.display = 'initial';
        }        
    }

    const liberaAbas = () => {
        abaMetas.desbloquear();
        abaRegionalizacao.desbloquear();
        abaAbrangencia.desbloquear();
    };
</script>
</body>
