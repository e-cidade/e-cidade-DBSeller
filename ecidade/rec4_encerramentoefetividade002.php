<?php
/**
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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));

use ECidade\RecursosHumanos\RH\PontoEletronico\Manutencao\ProcessamentoAssentamentoRepository;
use ECidade\RecursosHumanos\RH\Efetividade\Repository\Periodo as PeriodoRepository;
?>

  <html>
  <head>
      <?php
      db_app::load('scripts.js, strings.js, prototype.js, estilos.css, datagrid.widget.js, AjaxRequest.js, ProgressBar.widget.js');
      ?>
    <style media="screen" type="text/css">
      #logs {
        height: 50px;
        overflow-y: auto;
        width: 100%;
        background-color: #000;
        padding-top: 3px;
        border-radius: 3px;
      }

      #logs .item-log {
        margin: 14px 10px 2px 10px;
        color: rgba(230, 221, 221, 0.85);
      }
    </style>
  </head>
  <body class="body-default">
  <div class="container">
    <fieldset style="width: 700px; padding: 2px">
      <progress id="barra-progresso" value="0" style="width: 100%; height: 25px;">Processando</progress>
    </fieldset>
    <div id="logs"></div>
  </div>
  </body>
  </html>
  <script type="text/javascript">
    var barra          = $('barra-progresso');
    var barraProgresso = new ProgressBar(barra, $('logs'));
  </script>

<?php
try {
    $oParametros = (object)$_GET;
    $exercicio  = $oParametros->iExercicio;
    $oParametros->aSelecionados = explode('-', $oParametros->aSelecionados);
    $oParametros->matriculas    = empty($oParametros->matriculas)
        ? array()
        : explode('-', $oParametros->matriculas);

    $instituicao = InstituicaoRepository::getInstituicaoSessao();
    $competenciaFolha = DBCompetencia::folha();

    db_inicio_transacao();

    foreach ($oParametros->aSelecionados as $competencia) {
        if ($oParametros->gerarAssentamentos) {
            $oPeriodo = PeriodoRepository::getInstanciaPorExercicioCompetencia($exercicio, $competencia);

            if ($oPeriodo->getExercicio() == $competenciaFolha->getAno() &&
                $oPeriodo->getCompetencia() == $competenciaFolha->getMes()) {
                $aServidoresProcessar = buscarServidoresProcessar($oPeriodo, $instituicao, $oParametros->matriculas);
                $aTiposAssentamentos = buscarTiposAssentamentosConfigurados($instituicao->getCodigo());

                ProcessamentoAssentamentoRepository::processarAssentamentosNoPeriodo(
                    $oPeriodo,
                    $aServidoresProcessar,
                    $aTiposAssentamentos,
                    $instituicao
                );
            }
        }

        $sSqlProcessado = "update configuracoesdatasefetividade";
        $sSqlProcessado .= "   set rh186_processado  = 't'";
        $sSqlProcessado .= " where rh186_exercicio   = {$exercicio}";
        $sSqlProcessado .= "   and rh186_competencia::integer = {$competencia}";
        $sSqlProcessado .= "   and rh186_instituicao = {$instituicao->getCodigo()}";
        $rsProcessado = db_query($sSqlProcessado);

        if (!$rsProcessado) {
            throw new Exception('Erro ao atualizar dados de configuracoesdatasefetividades');
        }
    }

    db_fim_transacao(false);
    echo "<script type=\"text/javascript\">parent.js_mostrarMensagem('Processamento executado com sucesso.')</script>";

} catch (Exception $exception) {
    db_fim_transacao(true);
    echo "<script type=\"text/javascript\">parent.js_mostrarMensagem('".urlencode($exception->getMessage())."')</script>";
}

function buscarServidoresProcessar($oPeriodo, Instituicao $instituicao, $aMatriculas = array()) {

    $oDaoServidoresProcessar   = new cl_pontoeletronicoarquivodata();
    $sWhereServidoresProcessar = "rh197_data between '{$oPeriodo->getDataInicio()->getDate()}' and '{$oPeriodo->getDataFim()->getDate()}'";
    $sWhereServidoresProcessar .= "  AND NOT EXISTS (SELECT 1 FROM assentamentosencerramentoefetividade 
                                                              JOIN assenta ON assenta.h16_codigo = assentamentosencerramentoefetividade.rh230_assentamento 
                                                      WHERE rh230_ano = {$oPeriodo->getExercicio()} 
                                                        AND rh230_mes = '{$oPeriodo->getCompetencia()}'
                                                        AND rh230_instituicao = {$instituicao->getCodigo()}
                                                        AND h16_regist = rh197_matricula)";

    if(!empty($aMatriculas)) {
        $sWhereServidoresProcessar .= " AND rh197_matricula IN (". implode(',', $aMatriculas) .")";
    }

    $sWhereServidoresProcessar.= " and rh197_matricula in ( select rh02_regist from rhpessoalmov ";
    $sWhereServidoresProcessar.= "       inner join rhpeslocaltrab on rh56_seqpes = rh02_seqpes where rh02_instit = ".db_getsession('DB_instit');
    $sWhereServidoresProcessar.= " and rh02_anousu = ".DBPessoal::getAnoFolha()." and rh02_mesusu = ".DBPessoal::getMesFolha()." ) ";

    $sSqlServidoresProcessar   = $oDaoServidoresProcessar->sql_query_file(null, "distinct rh197_matricula as matricula", null, $sWhereServidoresProcessar);
    $rsServidoresProcessar     = db_query($sSqlServidoresProcessar);

    if(!$rsServidoresProcessar) {
        throw new DBException("Ocorreu um erro ao buscar os servidores para processar os assentamentos de horas extras, faltas e adicional noturno.");
    }

    $aServidores = array();

    if(pg_num_rows($rsServidoresProcessar) > 0) {

        $aServidores = db_utils::makeCollectionFromRecord($rsServidoresProcessar, function ($oRetorno) {
            return ServidorRepository::getInstanciaByCodigo($oRetorno->matricula);
        });
    }

    return $aServidores;
}

function buscarTiposAssentamentosConfigurados($iCodigoInstituicao) {

    $oDaPontoeletronicoconfiguracoesgerais = new cl_pontoeletronicoconfiguracoesgerais;
    $sSqlConfiguracoesGerais               = $oDaPontoeletronicoconfiguracoesgerais->sql_query_configuracoes(null, "rh200_instituicao = {$iCodigoInstituicao}");
    $rsSqlConfiguracoesGerais              = db_query($sSqlConfiguracoesGerais);

    if(!$rsSqlConfiguracoesGerais) {
        throw new DBException("Ocorreu um erro ao buscar as configurações gerais para a instituição.");
    }

    $aTiposAssentamentos = array();

    if(pg_num_rows($rsSqlConfiguracoesGerais) > 0) {

        $aTiposAssentamentosConfigurados = db_utils::makeCollectionFromRecord($rsSqlConfiguracoesGerais, function ($oRetorno) {
            return (object)array(
              'tipo'   => str_replace("rh200_tipoasse_", "", $oRetorno->tipo),
              'codigo' => $oRetorno->codigo
            );
        });

        foreach ($aTiposAssentamentosConfigurados as $oTipo) {
            $aTiposAssentamentos[$oTipo->tipo] = TipoAssentamentoRepository::getInstanciaPorCodigo($oTipo->codigo);
        }
    }

    return $aTiposAssentamentos;
}
