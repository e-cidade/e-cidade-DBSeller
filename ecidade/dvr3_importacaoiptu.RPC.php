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

ini_set('memory_limit', '2048M');
set_time_limit(0);

define('MENSAGENS_DVR3_IMPORTACAOIPTU_RPC', 'tributario.diversos.dvr3_importacaoiptu.');
define('TIPO_DEBITO_PARCELAMENTO', 16);

use ECidade\Tributario\Divida\ImportacaoDividaAtiva;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_sql.php"));

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));

$oRetorno = new stdClass();
$oRetorno->erro = false;
$oRetorno->status = 1;
$oRetorno->message = '';

$lErro = false;
$sMsg = '';
$dtHoje = date("Y-m-d", db_getsession("DB_datausu"));

db_app::import('diversos.ImportacaoDiversos');
db_app::import('diversos.ImportacaoGeralDiversos');
db_app::import('exceptions.*');

switch ($oParam->sExec) {

    case 'getDebitosOrigem':

        $aNumpres = array();

        foreach ($oParam->aDebitos as $debito) {
            $aNumpres[$debito[0]] = $debito[0];
        }

        $aOrigens = array();

        foreach ($aNumpres as $numpre) {
            $parcelamento = verificarParcelamento($numpre);

            if (!empty($parcelamento)) {
                $aOrigens = array_merge($aOrigens, buscarOrigemParcelamento($parcelamento));
            }
        }

        if (empty($aOrigens)) {
            $aOrigens = $oParam->aDebitos;
        }

        $oRetorno->aDebitos = $aOrigens;

        break;
    case 'getDebitosImportados' :

        $oDaoDiverImporta = new cl_diverimporta();

        if ($oParam->iTipoPesquisa == 5) {

            $sSqlDaoDiverImporta = $oDaoDiverImporta->sql_queryDebitosImportadosAlvara($oParam->iChavePesquisa);
        } else {

            $sSqlDaoDiverImporta = $oDaoDiverImporta->sql_query_debitos_importados($oParam->iTipoPesquisa, $oParam->iChavePesquisa);
        }

        $rsDaoDiverImporta = $oDaoDiverImporta->sql_record($sSqlDaoDiverImporta);

        if ($oDaoDiverImporta->numrows > 0) {

            $oRetorno->aDebitos = db_utils::getCollectionByRecord($rsDaoDiverImporta, false, false, true);
            $oDaoProcdiver = new cl_procdiver();
        } else {

            $oRetorno->status = 2;
            $oRetorno->message = 'Nenhum registro encontrado.';
        }

        break;

    /**
     * Cancelamento de importação de diversos
     */
    case 'cancelaImportacao':

        try {

            db_inicio_transacao();

            $sMensagem = 'cancelamento_sucesso';

            foreach ($oParam->aCodigosImportacao as $iCodigoImportacao) {

                $oImportacaoDiversos = new ImportacaoDiversos($iCodigoImportacao);
                if ($oImportacaoDiversos->cancelar() == ImportacaoDiversos::CANCELAMENTO_PARCIAL) {
                    $sMensagem = "cancelamento_parcial";
                }
            }

            db_fim_transacao();
            $oRetorno->status = 1;
            $oRetorno->message = urlencode(_M('tributario.diversos.ImportacaoDiversos.' . $sMensagem));

        } catch (Exception $sException) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->message = urlencode($sException->getMessage());
        }

        break;

    //lista debitos da matricula
    case 'getDebitos':

        try {

            $oRetorno->aProcdiver = array();
            $oRetorno->aVinculoReceitaProced  = array();
            $oRetorno->procedenciaDividaAtiva = array();
            $oRetorno->aDebitos = array();

            $oDaoDiverImporta = new cl_diverimporta();
            $origemDebito = !empty($oParam->origemDebito) ? $oParam->origemDebito : null;

            $sSqlDaoDiverImporta = $oDaoDiverImporta->sql_query_cobranca_adm($oParam->iTipoPesquisa, $oParam->aChavePesquisa, $origemDebito);
            if (empty($origemDebito)) {

                if ($oParam->iTipoPesquisa == 5) {
                    $sSqlDaoDiverImporta = $oDaoDiverImporta->sql_queryImportacaoAlvara($oParam->aChavePesquisa);
                } else {
                    $sSqlDaoDiverImporta = $oDaoDiverImporta->sql_query_importa_iptu($oParam->iTipoPesquisa, $oParam->aChavePesquisa);
                }
            }

            $rsDaoDiverImporta = $oDaoDiverImporta->sql_record($sSqlDaoDiverImporta);
            if ($oDaoDiverImporta->erro_status === '0' || $oDaoDiverImporta->numrows === 0) {
                throw new BusinessException('"Nenhum registro encontrado."');
            }

            $oRetorno->aDebitos = db_utils::getCollectionByRecord($rsDaoDiverImporta, false, false, true);

            if (empty($oParam->importaDividaAtiva)) {

                $oDaoProcdiver = new cl_procdiver();
                $sCamposProcDriver = "dv09_procdiver, dv09_descra, dv09_descr, dv09_tipo, dv09_receit as receita";
                $sWhereProcDriver = "dv09_cobranca = 't' and (dv09_dtlimite is null or dv09_dtlimite >= '{$dtHoje}') and dv09_instit = " . db_getsession("DB_instit");
                $sSqlProcedencia = $oDaoProcdiver->sql_query_file(null, $sCamposProcDriver, "dv09_procdiver,dv09_descra", $sWhereProcDriver);
                $rsBuscaProcedencia = $oDaoProcdiver->sql_record($sSqlProcedencia);
                if ($oDaoProcdiver->numrows > 0) {
                    $oRetorno->aProcdiver = db_utils::getCollectionByRecord($rsBuscaProcedencia, false, false, true);
                }
                $sSqlVinculoReceitaProcedencia  = "select k202_receita as receita,
                                                          k202_procdiver as procdiver
                                                     from caixa.vinculoreceitaprocedencia
                                                    inner join procdiver on dv09_procdiver = k202_procdiver";
                $sSqlVinculoReceitaProcedencia .= " where {$sWhereProcDriver}";

                $rsVinculoReceitaProcedencia    = db_query($sSqlVinculoReceitaProcedencia);
                if(!$rsVinculoReceitaProcedencia) {

                  throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_dados_procedencia',$oMensagem));
                }

                $iTotalDadosOrigem = pg_num_rows($rsVinculoReceitaProcedencia);

                for ($iIndice=0; $iIndice < $iTotalDadosOrigem; $iIndice++) {

                  $oDadosVinculo = db_utils::fieldsMemory($rsVinculoReceitaProcedencia, $iIndice);
                  $oRetorno->aVinculoReceitaProced[$oDadosVinculo->receita] = $oDadosVinculo->procdiver;
                }
            }

            if (!empty($oParam->importaDividaAtiva) && $oParam->importaDividaAtiva === true) {

                $daoProcedencia = new cl_proced();
                $sCamposProcedencia = 'v03_codigo as codigo, v03_descr as descricao, v03_receit as receita';
                $sWhereProcedencia = "(v03_limite is null or v03_limite >= '{$dtHoje}') and v03_instit = " . db_getsession("DB_instit");
                $sSqlProcedencia = $daoProcedencia->sql_query_file(null, $sCamposProcedencia, 'v03_codigo', $sWhereProcedencia);
                $resBuscaProcedencia = db_query($sSqlProcedencia);
                if (!$resBuscaProcedencia || pg_num_rows($resBuscaProcedencia) === 0) {
                    throw new BusinessException("Não foi encontrada nenhuma procedência para importação de Dívida Ativa.");
                }
                $oRetorno->procedenciaDividaAtiva = db_utils::getCollectionByRecord($resBuscaProcedencia);
                  foreach ($oRetorno->aDebitos as $oDebitos) {

                  $sSqlVinculoReceitaProcedencia = "select k00_numpre  as numpre,
                                                           dv09_proced as procdiv
                                                      from arrecad
                                                     inner join diversos                          on dv05_numpre    = k00_numpre
                                                     inner join procdiver                         on dv09_procdiver = dv05_procdiver
                                                     inner join proced                            on dv09_proced    = v03_codigo
                                                     where k00_numpre = {$oDebitos->k00_numpre}
                                                       and k00_numpar = {$oDebitos->k00_numpar}
                                                       and k00_receit = {$oDebitos->k02_codigo}
                                                       and (v03_limite is null or v03_limite >= '{$dtHoje}')
                                                       and v03_instit = " . db_getsession("DB_instit");

                  $rsVinculoReceitaProcedencia    = db_query($sSqlVinculoReceitaProcedencia);
                  if(!$rsVinculoReceitaProcedencia) {

                    throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_dados_procedencia',$oMensagem));
                  }

                  $iTotalDadosOrigem = pg_num_rows($rsVinculoReceitaProcedencia);
                  for ($iIndice=0; $iIndice < $iTotalDadosOrigem; $iIndice++) {

                    $oDadosVinculo = db_utils::fieldsMemory($rsVinculoReceitaProcedencia, $iIndice);
                    $oRetorno->aVinculoReceitaProced["{$oDadosVinculo->numpre}"] = $oDadosVinculo->procdiv;
                  }
                }
            }
        } catch (Exception $e) {

            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = $e->getMessage();
        }


        break;

    /**
     * Importação de diversos
     */
    case 'processarDebitos':

        $oDadosProcessamento = $oParam->oDadosProcessamento;

        $lDesativarAccount   = db_getsession("DB_desativar_account", false);

        try {

            db_putsession("DB_desativar_account", false);

            db_inicio_transacao();

            $oProcedenciaDiversos = new ImportacaoDiversos(null);

            /**
             * Tipo de pesquisa
             * 1 - CODIGO IMPORTACAO
             * 2 - MATRICULA
             * 3 - CGM
             * 4 - Debitos (vindos da CGF)
             * 5 - Inscrição
             */
            $sObservacao = "";
            $oProcedenciaDiversos->setTipoOrigem($oDadosProcessamento->iTipoPesquisa);
            $oProcedenciaDiversos->setCodigoOrigem($oDadosProcessamento->aChavePesquisa[0]);

            $sObservacao = $oDadosProcessamento->sObservacoes;

            foreach ($oDadosProcessamento->aDebitos as $oDebito) {

                $oProcedenciaDiversos->importarDiversos($oDebito->iCodigoProcedencia,
                    $oDebito->iNumpre,
                    $oDebito->iNumpar,
                    $oDebito->iReceita);
            }
            $oProcedenciaDiversos->processar($sObservacao);

            db_fim_transacao();

            $oRetorno->status = 1;
            $oRetorno->message = urlencode("Débitos processados com sucesso.");

        } catch (Exception $sException) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->message = urlencode($sException->getMessage());
        }

        db_putsession("DB_desativar_account", $lDesativarAccount);

        break;

    /**
     * Busca Receitas
     */
    case 'getReceitasProcedencias':

        try{

            $oDaoProcdiver = new cl_procdiver();
            $oDaoArrecad = new cl_arrecad();

            $where = array();
            $where[] = "dv09_cobranca = 't' and (dv09_dtlimite is null or dv09_dtlimite >= '{$dtHoje}') and dv09_instit = ". db_getsession("DB_instit");
            if (!empty($oParam->tipoDebitoDestino)) {
                $where[] = "dv09_tipo = {$oParam->tipoDebitoDestino}";
            }

            $sSqlProcedencia = $oDaoProcdiver->sql_query_file(null,
                "dv09_procdiver as codigo, dv09_descra as descricao, dv09_receit as receita",
                "dv09_descra",
                implode(' and ', $where));

      	    $sSqlVinculoReceitaProcedencia  = "select k202_receita as receita,
                                                    k202_procdiver as procdiver
                                                 from caixa.vinculoreceitaprocedencia
                                                inner join procdiver on dv09_procdiver = k202_procdiver
                                                where ".implode(' and ', $where);
            $rsVinculoReceitaProcedencia    = db_query($sSqlVinculoReceitaProcedencia);
            if(!$rsVinculoReceitaProcedencia) {

              throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_dados_procedencia',$oMensagem));
            }

            $iTotalDadosOrigem = pg_num_rows($rsVinculoReceitaProcedencia);
            $oRetorno->aVinculoReceitaProced = [];
            for ($iIndice=0; $iIndice < $iTotalDadosOrigem; $iIndice++) {
              $oDadosVinculo = db_utils::fieldsMemory($rsVinculoReceitaProcedencia, $iIndice);
              $oRetorno->aVinculoReceitaProced[$oDadosVinculo->receita] = $oDadosVinculo->procdiver;
            }

            if (!empty($oParam->importaDividaAtiva) && $oParam->importaDividaAtiva === true) {

                $daoProced = new cl_proced();
                $sCamposProcedencia = 'v03_codigo as codigo, v03_descr as descricao, v03_receit as receita';
                $sWhereProcedencia = "(v03_limite is null or v03_limite >= '{$dtHoje}') and v03_instit = " . db_getsession("DB_instit");
                $sSqlProcedencia = $daoProced->sql_query_file(null, $sCamposProcedencia, 'v03_codigo', $sWhereProcedencia);
            }

            $rsBuscaProcedencia = db_query($sSqlProcedencia);
            if (!$rsBuscaProcedencia) {

                $oMensagem = (object)array('sErro'=>pg_last_error());
                throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_dados_procedencia',$oMensagem));
            }

            if (pg_num_rows($rsBuscaProcedencia) == 0) {
                throw new BusinessException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'nenhuma_procedencia_encontrada'));
            }

            $buscarPorCadTipo = true;
            $oParam->iCadTipo = null;

            if ($oParam->tipoDebitoOrigem) {
                $oParam->iCadTipo = $oParam->tipoDebitoOrigem;
                $buscarPorCadTipo = false;
            }

            if (!empty($oParam->listaDebitos)) {

                $daoListaDeb = new cl_listadeb();

                $oParam->iCadTipo = $daoListaDeb->sql_query_arrecad($oParam->listaDebitos, "distinct k00_tipo");
                $buscarPorCadTipo = false;
            }

            $buscarDebitosOrigem = false;

            if ($oParam->importaDividaAtiva === true ) {

                $daoArretipo = new cl_arretipo();

                $where = "arretipo.k00_tipo = $oParam->tipoDebitoOrigem";

                if (!empty($oParam->listaDebitos)) {
                    $where = "arretipo.k00_tipo in ($oParam->iCadTipo)";
                }

                $sqlArretipo = $daoArretipo->sql_query(null, "cadtipo.k03_tipo as tipo", null, $where);
                $rsArretipo  = $daoArretipo->sql_record($sqlArretipo);

                if (!$rsArretipo) {
                    throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "tipo_origem_invalido"));
                }

                $oArretipo = db_utils::fieldsMemory($rsArretipo, 0);

                if ($oArretipo->tipo == TIPO_DEBITO_PARCELAMENTO) {

                    $buscarDebitosOrigem = true;
                    $oParam->iCadTipo = TIPO_DEBITO_PARCELAMENTO;
                }
            }

            $sSqlDebitos = $oDaoArrecad->sql_query_getReceitasTipo($oParam->iCadTipo, $buscarPorCadTipo);
            if ($oParam->importaDividaAtiva === true ) {
                $sSqlDebitos = $oDaoArrecad->sql_query_getReceitasTipoVinculo($oParam->iCadTipo, $buscarPorCadTipo);
            }

            if ($buscarDebitosOrigem) {
                $sSqlDebitos = "
                    select distinct
                            tabrec.k02_descr,
                            arreold.k00_receit,
                            dv09_procdiver,
                            dv09_proced
                            from arreold
                    inner join arreinstit on arreinstit.k00_numpre = arreold.k00_numpre
                    inner join diversos on diversos.dv05_numpre = arreold.k00_numpre
                    inner join procdiver  on diversos.dv05_procdiver = procdiver.dv09_procdiver
                    inner join proced     on proced.v03_codigo       = procdiver.dv09_proced
                    inner join termodiver on termodiver.dv10_coddiver = diversos.dv05_coddiver
                           inner join termo on termo.v07_parcel = termodiver.dv10_parcel
                           inner join arrecad on arrecad.k00_numpre = termo.v07_numpre
                           inner join tabrec on tabrec.k02_codigo = arrecad.k00_receit
                     where arrecad.k00_tipo = 14
                       and arreinstit.k00_instit = ".db_getsession("DB_instit");
            }

            if (!empty($oParam->listaDebitos)) {

                $sSqlDebitos = "

                    select distinct
                        k02_descr,
                        k00_receit,
                        dv09_procdiver,
                        dv09_proced
                        from arrecad
                       inner join tabrec   on k02_codigo = k00_receit
                  left join diversos   on diversos.dv05_numpre = arrecad.k00_numpre
                  left join procdiver  on diversos.dv05_procdiver = procdiver.dv09_procdiver
                  left join proced     on proced.v03_codigo       = procdiver.dv09_proced
                       natural join arretipo
                       natural join cadtipo
                       natural join arreinstit
                 where arrecad.k00_numpre in (SELECT DISTINCT k61_numpre FROM listadeb WHERE k61_codigo = {$oParam->listaDebitos})
                     and arreinstit.k00_instit = ".db_getsession("DB_instit")."
                     union all
                    select distinct
                           tabrec.k02_descr,
                           arreold.k00_receit,
                           dv09_procdiver,
                           dv09_proced
                           from arreold
                           inner join arreinstit on arreinstit.k00_numpre = arreold.k00_numpre
                           inner join diversos on diversos.dv05_numpre = arreold.k00_numpre

                    inner join procdiver  on diversos.dv05_procdiver = procdiver.dv09_procdiver
                    inner join proced     on proced.v03_codigo       = procdiver.dv09_proced
                    inner join termodiver on termodiver.dv10_coddiver = diversos.dv05_coddiver
                           inner join termo on termo.v07_parcel = termodiver.dv10_parcel
                           inner join arrecad on arrecad.k00_numpre = termo.v07_numpre
                           inner join tabrec on tabrec.k02_codigo = arrecad.k00_receit
                     where arrecad.k00_numpre in (SELECT DISTINCT k61_numpre FROM listadeb WHERE k61_codigo = {$oParam->listaDebitos})
                       and arreinstit.k00_instit = ".db_getsession("DB_instit");
            }

            $rsDAODebitos = db_query($sSqlDebitos);

            if (!$rsDAODebitos) {

                $oMensagem = (object)array('sErro'=>pg_last_error());
                throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_dados_receitas', $oMensagem));
            }

            if (pg_num_rows($rsDAODebitos) == 0) {
                throw new BusinessException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'nenhuma_receita_encontrada'));
            }


            if ($oParam->importaDividaAtiva === true ) {

               $sSqlVinculoReceitaProcedencia  = $sSqlDebitos." and (v03_limite is null or v03_limite >= '{$dtHoje}') and v03_instit = " . db_getsession("DB_instit");

               $rsVinculoReceitaProcedencia    = db_query($sSqlVinculoReceitaProcedencia);
               if(!$rsVinculoReceitaProcedencia) {

                 throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_dados_procedencia',$oMensagem));
               }

               $iTotalDadosOrigem = pg_num_rows($rsVinculoReceitaProcedencia);
               for ($iIndice=0; $iIndice < $iTotalDadosOrigem; $iIndice++) {
                 $oDados = db_utils::fieldsMemory($rsVinculoReceitaProcedencia, $iIndice);
                 $oRetorno->aVinculoReceitaProced[$oDados->k00_receit] = $oDados->dv09_proced;
               }
            }$oRetorno->aReceitas     = db_utils::getCollectionByRecord($rsDAODebitos,   false, false, true);
            $oRetorno->aProcedencias = db_utils::getCollectionByRecord($rsBuscaProcedencia, false, false, true);

        } catch (Exception $oException) {

            $oRetorno->status = 2;
            $oRetorno->message = urlencode($oException->getMessage());
        }

        break;

    /**
     * Busca Receitas
     */
    case 'getImportacaoGeral':

        try{

            $oDaoDiverimporta = new cl_diverimporta();

            $sWhere  = '     exists (select 1 from diverimportareg where dv12_diverimporta = dv11_sequencial)';
            $sWhere .= ' and (extract(year from dv11_data) >= '.db_getsession('DB_anousu');
            $sWhere .= ' and dv11_instit = '. db_getsession("DB_instit") .')';

            $sSqlDiverimporta = $oDaoDiverimporta->sql_query_file(null,
                "dv11_sequencial, dv11_data, dv11_hora, dv11_tipo, dv11_obs",
                "dv11_sequencial",
                $sWhere);

            $rsDAODiverimporta = db_query($sSqlDiverimporta);

            if (!$rsDAODiverimporta) {

                $oMensagem = (object)array('sErro'=>pg_last_error());
                throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_importacoes',$oMensagem));
            }

            if (pg_num_rows($rsDAODiverimporta) == 0) {
                throw new BusinessException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'nenhuma_importacao_geral_encontrada'));
            }

            $oRetorno->aImportacoes = db_utils::getCollectionByRecord($rsDAODiverimporta, false, false, true);

        } catch (Exception $oException) {

            $oRetorno->status = 2;
            $oRetorno->message = urlencode($oException->getMessage());
            echo $oJson->encode($oRetorno);
            exit;
        }

        break;

    /**
     * Importação geral de diversos
     */
    case 'importacaoGeralDiversos':

        try {

            $aDadosImportacao = $oParam->aDados;
            $iQuantidadeParcelas = $oParam->iQuantidadeParcelas;
            $sObservacoes = $oParam->sObservacoes;

            $oImportacao = new ImportacaoGeralDiversos(null);

            foreach ($aDadosImportacao as $aDadoImportacao){

                if(empty($aDadoImportacao->iCodigoProcedencia)){
                    continue;
                }
                $oData = $aDadoImportacao->iVencimento == '' ? null : new DBDate($aDadoImportacao->iVencimento);
                $oImportacao->adicionarReceita($aDadoImportacao->iCodigoReceita, $oData, new ProcedenciaDiversos($aDadoImportacao->iCodigoProcedencia));
            }

            if (!empty($iQuantidadeParcelas)) {
                $oImportacao->setQuantidadeParcelas($iQuantidadeParcelas);
            }

            $oImportacao->setObservacoes($sObservacoes);

            db_inicio_transacao();
            $oImportacao->processar(true);
            db_fim_transacao();

            $oRetorno->status = 1;
            $oRetorno->message = urlencode(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'sucesso_importacao_geral'));
            echo $oJson->encode($oRetorno);
            exit;
        } catch (Exception $oException) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->message = urlencode($oException->getMessage());
            echo $oJson->encode($oRetorno);
            exit;
        }

        break;

    /**
     * Busca os tipos de débitos que um contribuinte possui e tipos de débitos de Diversos
     */
    case 'tiposDebitoParcial':

        try {

            if (empty($oParam->iChavePesquisa)) {
                throw new ParameterException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."codigo_pesquisa_nao_informado"));
            }

            if (empty($oParam->iTipoPesquisa)) {
                throw new ParameterException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."tipo_chave_nao_informado"));
            }

            /**
             * Busca os tipos de débitos de origem com base no contribuinte informado
             */
            $oDaoArrecad = new cl_arrecad();

            $aCampos = array(
                "distinct arretipo.k00_tipo",
                "arretipo.k00_descr",
                "arretipo.k03_tipo"
            );

            $aWhere   = array("cadtipo.k03_tipo not in(5, 6, 12, 13, 15, 16, 18)");
            $aWhere[] = "arretipo.k00_tipo not in (13) ";
            $iTipoDebito = null;

            if ( $oParam->importaDividaAtiva ) {
                $aWhere   = array("arretipo.k00_tipo in(13, 14)");
                $iTipoDebito = 5;
            }

            $sSqlArrecad = $oDaoArrecad->sqlDebitosPorTipoContribuinte($aCampos, $aWhere, $oParam->iChavePesquisa, $oParam->iTipoPesquisa);
            $rsArrecad = db_query($sSqlArrecad);

            if (!$rsArrecad) {
                throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."erro_buscar_tipos_debito_origem"));
            }

            if (pg_num_rows($rsArrecad) == 0) {
                throw new BusinessException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."nenhum_tipo_debito_origem_encontrado"));
            }

            /**
             * Guarda os tipos de débitos retornados, para envio a tela
             */
            $oRetorno->aOpcoesTipoOrigem = db_utils::getCollectionByRecord($rsArrecad, false, false, true);
            $oRetorno->aOpcoesTipoDestino = debitosExercicio($iTipoDebito);
        } catch(Exception $oException) {

            $oRetorno->status = 2;
            $oRetorno->message = urlencode($oException->getMessage());
        }

        break;

    /**
     * Busca os tipos de débitos, com valor, do exercício
     */
    case 'tiposDebitoGeral':

        try {

            $oDaoArrecad = new cl_arrecad();

            $aCampos = array(
                "distinct arretipo.k00_tipo",
                "arretipo.k00_descr"
            );

            $aWhere = array("cadtipo.k03_tipo not in(5, 6, 12, 13, 15, 16, 18)");
            $aWhere[] = "arretipo.k00_tipo not in(13)";

            $sSqlArrecad = $oDaoArrecad->sqlTiposDebitosGeral($aCampos, $aWhere);

            if ( $oParam->importaDividaAtiva ) {

                $sSqlArrecad  = " select distinct                                                               ";
                $sSqlArrecad .= "        arretipo.k00_tipo,                                                     ";
                $sSqlArrecad .= "        arretipo.k00_descr                                                     ";
                $sSqlArrecad .= "   from arretipo                                                               ";
                $sSqlArrecad .= "  where arretipo.k00_tipo in (13, 14)                                          ";
                $sSqlArrecad .= "    and k00_instit = ".db_getsession("DB_instit");
                $sSqlArrecad .= "    and 1 = (select 1 from arrecad where k00_tipo = arretipo.k00_tipo limit 1) ";
                $sSqlArrecad .= "  order by arretipo.k00_descr                                                  ";
            }

            $rsArrecad = db_query($sSqlArrecad);

            if(!$rsArrecad) {
                throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'erro_buscar_tipos_debito_origem'));
            }

            if(pg_num_rows($rsArrecad) == 0) {
                throw new BusinessException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . 'nenhum_tipo_debito_origem_encontrado'));
            }

            /**
             * Guarda os tipos de débitos retornados, para envio a tela
             */
            $iTipoDebito = !empty($oParam->importaDividaAtiva) && $oParam->importaDividaAtiva === true ? 5 : null;
            $oRetorno->aOpcoesTipoOrigem = db_utils::getCollectionByRecord($rsArrecad, false, false, true);
            $oRetorno->aOpcoesTipoDestino = debitosExercicio($iTipoDebito);
        } catch (Exception $oErro) {

            $oRetorno->status = 2;
            $oRetorno->message = urlencode($oException->getMessage());
        }

        break;

    case 'importacaoParcial':

        try {

            $oImportacao = new ImportacaoDiversosCobrancaAdministrativa();
            $oImportacao->setUnificarDebitos($oParam->lUnificarDebitos);
            $oImportacao->setObservacoes(db_stdClass::normalizeStringJsonEscapeString($oParam->oDadosProcessamento->sObservacoes));
            $oImportacao->setDebitos($oParam->oDadosProcessamento->aDebitos);

            if ($oParam->lUnificarDebitos) {

                $oImportacao->setOrdemDataVencimento((int)$oParam->iTipoDataVencimento);

                if (!empty($oParam->sDataVencimento)) {
                    $oImportacao->setDataVencimento(new DBDate($oParam->sDataVencimento));
                }
            }

            db_inicio_transacao();
            $oImportacao->importacaoParcial();
            db_fim_transacao();

            $oRetorno->status = 1;
            $oRetorno->erro = false;
            $oRetorno->message = urlencode(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."sucesso_importacao_geral"));
            echo $oJson->encode($oRetorno);
            exit;

        } catch (Exception $oException) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = urlencode($oException->getMessage());
            echo $oJson->encode($oRetorno);
            exit;
        }

        break;

    case 'importacaoGeralDiversosCobrancaAdministrativa':

        try {

            $oImportacao = new ImportacaoDiversosCobrancaAdministrativa();

            $oImportacao->setTipoDebitoOrigem($oParam->iTipoDebitoOrigem);
            $oImportacao->setObservacoes(db_stdClass::normalizeStringJsonEscapeString($oParam->sObservacoes));
            $oImportacao->setUnificarDebitos($oParam->lUnificarDebitos);

            foreach ($oParam->aDados as $aDadoImportacao) {
                $oImportacao->adicionarReceita($aDadoImportacao->iCodigoReceita, null, new ProcedenciaDiversos($aDadoImportacao->iCodigoProcedencia));
            }

            if ($oParam->lUnificarDebitos) {

                $oImportacao->setOrdemDataVencimento((int)$oParam->iTipoDataVencimento);

                if (!empty($oParam->sDataVencimento)) {
                $oImportacao->setDataVencimento(new DBDate($oParam->sDataVencimento));
            }
            }

            $oImportacao->setListaDebitos($oParam->listaDebitos);

            db_inicio_transacao();
            $oImportacao->importacaoGeral();
            db_fim_transacao();

            $oRetorno->erro = false;
            $oRetorno->message = urlencode(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."sucesso_importacao_geral"));
            echo $oJson->encode($oRetorno);
            exit;

        } catch (Exception $oException) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro = true;
            $oRetorno->message = urlencode($oException->getMessage());
            echo $oJson->encode($oRetorno);
            exit;
        }

        break;

    case 'importacaoGeralDividaParaDividaAtiva':


        try {

            if (empty($oParam->aDados) || count($oParam->aDados) === 0) {
                throw new ParameterException('Não foram informados os parâmetros necessários para execução da importação.');
            }

            db_inicio_transacao();
            $importacao = new ImportacaoDividaAtiva();
            $importacao->setTipoDebitoOrigem($oParam->iTipoDebitoOrigem);
            $importacao->setObservacoes(db_stdClass::normalizeStringJsonEscapeString($oParam->sObservacoes));
            $importacao->setUnificarDebitos($oParam->lUnificarDebitos);

            if ($oParam->processoSistema === true && !empty($oParam->codigoProcesso)) {
                $importacao->setProcessoProtocolo(new processoProtocolo($oParam->codigoProcesso));
            }

            if ($oParam->processoSistema === false) {

                $importacao->setProcesso((object)array(
                    'codigo' => $oParam->codigoProcesso,
                    'titular' => $oParam->titularProcesso,
                    'data' => $oParam->dataProcesso,
                ));
            }

            $importacao->setListaDebitos($oParam->listaDebitos);

            if ($oParam->lUnificarDebitos) {

                $importacao->setOrdemDataVencimento((int)$oParam->iTipoDataVencimento);

                if (!empty($oParam->sDataVencimento)) {
                    $importacao->setDataVencimento(new DBDate($oParam->sDataVencimento));
                }
            }

            foreach ($oParam->aDados as $stdDadosReceita){

                $procedencia = new ProcedenciaDivida($stdDadosReceita->iCodigoProcedencia);
                $importacao->adicionarReceitaProcedencia($stdDadosReceita->iCodigoReceita, $procedencia);
            }
            $importacao->importacaoGeral();

            db_fim_transacao(false);

            $oRetorno->message = urlencode(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."sucesso_importacao_geral"));

        } catch (Exception $e) {

            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro   = true;
            $oRetorno->message = urlencode($e->getMessage());
            echo $oJson->encode($oRetorno);
            exit;
        }

        break;

    case "importacaoParcialDividaAtiva":

        try {
            db_inicio_transacao();

            if (empty($oParam->oDadosProcessamento) || count($oParam->oDadosProcessamento->aDebitos) === 0) {
                throw new ParameterException('Não foram informados os parâmetros necessários para execução da importação.');
            }

            db_inicio_transacao();
            $importacao = new ImportacaoDividaAtiva();
            $importacao->setObservacoes(db_stdClass::normalizeStringJsonEscapeString($oParam->oDadosProcessamento->sObservacoes));
            $importacao->setUnificarDebitos($oParam->lUnificarDebitos);
            $importacao->setDebitos((array) $oParam->oDadosProcessamento);
            $importacao->setTipoDebitoOrigem((integer)$oParam->iOrigemDebito);

            if ($oParam->processoSistema === true && !empty($oParam->codigoProcesso)) {
                $importacao->setProcessoProtocolo(new processoProtocolo($oParam->codigoProcesso));
            }

            if ($oParam->processoSistema === false) {

                $importacao->setProcesso((object)array(
                    'codigo' => $oParam->codigoProcesso,
                    'titular' => $oParam->titularProcesso,
                    'data' => $oParam->dataProcesso,
                ));
            }

            if ($oParam->lUnificarDebitos) {

                $importacao->setOrdemDataVencimento((int)$oParam->iTipoDataVencimento);

                if (!empty($oParam->sDataVencimento)) {
                    $importacao->setDataVencimento(new DBDate($oParam->sDataVencimento));
                }
            }

            $importacao->importacaoParcial();

            db_fim_transacao(false);

            $oRetorno->status = 1;
            $oRetorno->erro = false;
            $oRetorno->message = urlencode(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC."sucesso_importacao_geral"));
            echo $oJson->encode($oRetorno);
            exit;

        } catch (Exception $e) {
            db_fim_transacao(true);
            $oRetorno->status = 2;
            $oRetorno->erro   = true;
            $oRetorno->message = urlencode($e->getMessage());
            echo $oJson->encode($oRetorno);
            exit;
        }


        break;

    case 'validarListaDebitos':
        try{
            $daoListaDeb = new cl_listadeb();
            $sqlListaDeb = $daoListaDeb->sql_query_arrecad_tipo($oParam->listaDebitos);
            $rsListaDeb  = db_query($sqlListaDeb);

            if (empty($rsListaDeb)) {
                throw new DBException( _M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "erro_lista") );
            }

            if (pg_num_rows($rsListaDeb) == 0 ) {
                throw new BusinessException( _M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "lista_invalida_sem_aberto") );
            }

            $where = "(cadtipo.k03_tipo in (5, 6, 12, 13, 15, 16, 18) or arretipo.k00_tipo = 13)";

            if ($oParam->importaDividaAtiva) {
                $where = "arretipo.k00_tipo not in(13, 14)";
            }

            $sqlListaTipo = $daoListaDeb->sql_query_arrecad_tipo($oParam->listaDebitos, "*", null, $where);
            $rsListaTipo = db_query($sqlListaTipo);

            if (empty($rsListaTipo)) {
                throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "erro_lista_tipo") );
            }

            if ( pg_num_rows($rsListaTipo) > 0 ) {
                throw new BusinessException( _M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "lista_tipo_invalido") );
            }

            /* M17476 - Pode ter mais de um tipo
            $sqlListaTipo = $daoListaDeb->sql_query_arrecad_tipo($oParam->listaDebitos, "distinct cadtipo.k03_tipo");
            $rsListaTipo = db_query($sqlListaTipo);

            if ( pg_num_rows($rsListaTipo) > 1 ) {
                throw new BusinessException( _M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "lista_invalida_diversos_parcelamento") );
            }
            */

        } catch (Exception $erro) {
            $oRetorno->status = 2;
            $oRetorno->erro   = true;
            $oRetorno->message = urlencode($erro->getMessage());
            echo $oJson->encode($oRetorno);
            exit;
        }

        break;
}

$oRetorno->erro = $oRetorno->status == 2;

echo JSON::create()->stringify($oRetorno);

/**
 * @param null $iTipo tipos de débitos
 * @return stdClass[]
 * @throws BusinessException
 * @throws DBException
 */
function debitosExercicio($iTipo = null) {

    $oDaoArretipo   = new cl_arretipo();

    if (is_null($iTipo)) {
        $iTipo = 7;
    }

    $sWhereArretipo = "k03_tipo = $iTipo and k00_instit = " . db_getsession("DB_instit");
    $sSqlArretipo   = $oDaoArretipo->sql_query_file(null, "k00_tipo, k00_descr", "k00_descr", $sWhereArretipo);
    $rsArretipo     = db_query($sSqlArretipo);

    if(!$rsArretipo) {
        throw new DBException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "erro_buscar_tipos_debito_destino"));
    }

    if(pg_num_rows($rsArretipo) == 0) {
        throw new BusinessException(_M(MENSAGENS_DVR3_IMPORTACAOIPTU_RPC . "nenhum_tipo_debito_destino_encontrado"));
    }

    $aOpcoesTipoDestino = db_utils::getCollectionByRecord($rsArretipo, false, false, true);

    return $aOpcoesTipoDestino;
}

function verificarParcelamento($numpre)
{
    $oDaoTermo = new \cl_termo();
    $sWhere    = " v07_numpre = {$numpre} ";
    $sSqlTermo = $oDaoTermo->sql_query_file(
        null,
        "distinct v07_parcel as codigo_parcelamento",
        null,
        $sWhere );
    $rsTermo   = db_query($sSqlTermo);

    if (!$rsTermo) {
        throw new \DBException("Erro ao consultar dados do parcelamento.");
    }

    if ( pg_num_rows($rsTermo) > 0 ) {

        $oTermo = \db_utils::fieldsMemory($rsTermo, 0);
        return $oTermo->codigo_parcelamento;
    }

    return false;
}

function buscarOrigemParcelamento($parcelamento)
{
    $oDaoTermo  = new \cl_termo();
    $sSqlOrigem = $oDaoTermo->sql_query_origem_parcelamento($parcelamento);

    $sCampos = implode("," , array(
        "x.k00_numpre",
        "x.k00_numpre as numpre",
        "x.k00_numpar",
        "arretipo.k00_descr",
        "x.k00_receit",
        "tabrec.k02_descr",
        "x.k00_valor"
    ));

    $sSql  = "select $sCampos           ";
    $sSql .= "  from ($sSqlOrigem) as x ";
    $sSql .= "      inner join arretipo on arretipo.k00_tipo = x.k00_tipo ";
    $sSql .= "      inner join tabrec on tabrec.k02_codigo = x.k00_receit ";
    $sSql .= " where k00_valor > 0      ";
    $sSql .= " order by k00_numpre, k00_numpar ";

    $rsOrigem = db_query($sSql);

    if (!$rsOrigem) {
        throw new \DBException("Erro ao consultar os dados de origem do parcelamento.");
    }

    if (pg_num_rows($rsOrigem) == 0) {
        throw new \Exception("Não há débitos de origem para este parcelamento.");
    }

    $retorno = \db_utils::getCollectionByRecord($rsOrigem);

    $aNumpres = array();
    $retornoOrigem = array();

    foreach ($retorno as $debito) {
        $aNumpres[$debito->k00_numpre] = $debito->k00_numpre;
    }

    foreach ($aNumpres as $numpre) {
        $parcelamento = verificarParcelamento($numpre);

        if (!empty($parcelamento)) {
            $retornoOrigem = array_merge($retornoOrigem, buscarOrigemParcelamento($parcelamento));
        }
    }

    if (!empty($retornoOrigem)) {
        $retorno = $retornoOrigem;
    }

    foreach ($retorno as $indice => $dados){
        $retorno[$indice] = array_values( (array) $dados);
    }

    return $retorno;
}
