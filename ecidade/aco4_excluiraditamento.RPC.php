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

require_once 'libs/db_stdlib.php';
require_once 'libs/db_conecta' . '.php';
require_once 'libs/db_sessoes.php';
require_once 'libs/db_utils.php';
require_once 'libs/db_app.utils.php';
require_once 'dbforms/db_funcoes.php';

$oJSON = new Services_JSON();
$oParametros = $oJSON->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();
$oRetorno->message = '';
$oRetorno->erro = false;

try {

    db_inicio_transacao();

    switch ($oParametros->exec) {

        case 'excluirAditamento':

            if (db_getsession('DB_login') != 'dbseller') {
                throw new BusinessException('Rotina habilitada somente para o usuário DBSeller.');
            }

            $iAcordoPosicao = (int) $oParametros->iAcordoPosicao;
            $oAcordoPosicao = new AcordoPosicao($iAcordoPosicao);
            $iCodigoAcordo = $oAcordoPosicao->getAcordo();

            if ($oAcordoPosicao->getTipo() === null) {
                throw new Exception('Posição inválida.');
            }

            if ((int) $oAcordoPosicao->getTipo() === AcordoPosicao::TIPO_INCLUSAO) {
                throw new Exception('Não é possível apagar a posição de inclusão.');
            }

            /**
             * Remove o vínculo com a reserva de saldo da posição do acordo
             * @var array
             */
            $aSqlVinculoReserva = array(
                "delete from orcreservaacordoitemdotacao where o84_acordoitemdotacao in (",
                "select ac22_sequencial from acordoitemdotacao where ac22_acordoitem in (",
                "select ac20_sequencial from acordoitem where ac20_acordoposicao in ($iAcordoPosicao)",
                ")",
                ")",
            );
            $sSqlVinculoReserva = implode(' ', $aSqlVinculoReserva);
            $rsDeleteVinculoReserva = db_query($sSqlVinculoReserva);
            if (!$rsDeleteVinculoReserva) {
                throw new DBException("Erro ao executar o comando: {$sSqlVinculoReserva}");
            }

            /**
             * Remove a reserva de saldo vinculada a posição do acordo
             * @var array
             */
            $aSqlReserva = array(
                "delete from orcreserva where o80_codres in (",
                "select o84_orcreserva from orcreservaacordoitemdotacao where o84_acordoitemdotacao in (",
                "select ac22_sequencial from acordoitemdotacao where ac22_acordoitem in (",
                "select ac20_sequencial from acordoitem where ac20_acordoposicao in ($iAcordoPosicao)",
                ")",
                ")",
                ")",
            );
            $sSqlReserva = implode(' ', $aSqlReserva);
            $rsDeleteReserva = db_query($sSqlReserva);
            if (!$rsDeleteReserva) {
                throw new DBException("Erro ao executar o comando: {$sSqlReserva}");
            }

            /**
             * * Apaga evento vinculado ao aditamento
             */
            $sequencialEvento = [];
            $sequencialEvento = pg_fetch_array(db_query("select ac56_acordoevento from acordoposicaoevento where ac56_acordoposicao = {$iAcordoPosicao}"));
            $sqlApagaDocumentoEvento = "delete from acordodocumentoevento where ac57_acordoevento = {$sequencialEvento['ac56_acordoevento']}";
            $sSqlApagarEventoPosicao = "delete from acordoposicaoevento where ac56_acordoposicao = {$iAcordoPosicao}";
            $sSqlApagarEvento = "delete from acordoevento where ac55_sequencial = {$sequencialEvento['ac56_acordoevento']}";
            $rsApagaEventoDocumento = db_query($sqlApagaDocumentoEvento);
            $rsApagaEventoPosicao = db_query($sSqlApagarEventoPosicao);
            $rsApagaEvento = db_query($sSqlApagarEvento);
            if (!$rsApagaEventoPosicao || !$rsApagaEvento || !$rsApagaEvento) {
                throw new DBException('Não foi possível excluir o evento vinculado ao aditamento.');
            }

            $sql = "delete from acordoitemexecutadoempautitem where ac19_acordoitemexecutado in (select ac29_sequencial from acordoitemexecutado where ac29_acordoitem in(select ac20_sequencial from acordoitem where ac20_acordoposicao = {$iAcordoPosicao}));";
            $rs = db_query($sql);
            if (!$rs) {
                throw new Exception(pg_last_error());
            }

            $sql = "delete from acordoitemexecutadomatordemitem where ac30_acordoitemexecutado in (select ac29_sequencial from acordoitemexecutado where ac29_acordoitem in(select ac20_sequencial from acordoitem where ac20_acordoposicao = {$iAcordoPosicao}));";
            $rs = db_query($sql);
            if (!$rs) {
                throw new Exception(pg_last_error());
            }

            $sql = "delete from acordoitemexecutadoperiodo where ac38_acordoitemexecutado in (select ac29_sequencial from acordoitemexecutado where ac29_acordoitem in(select ac20_sequencial from acordoitem where ac20_acordoposicao = {$iAcordoPosicao}));";
            $rs = db_query($sql);
            if (!$rs) {
                throw new Exception(pg_last_error());
            }

            $aTabelasExcluirItem = array(
                'acordoitemaditamento' => 'ac21_acordoitem',
                'acordoitemdotacao' => 'ac22_acordoitem',
                'acordoitemexecutado' => 'ac29_acordoitem',
                'acordoitemexecutadodotacao' => 'ac32_acordoitem',
                'acordoitemperiodo' => 'ac41_acordoitem',
                'acordoitemprevisao' => 'ac37_acordoitem',
                'acordoitemvinculo' => 'ac33_acordoitempai',
                'acordoliclicitem' => 'ac24_acordoitem',
                'acordopcprocitem' => 'ac23_acordoitem',
                'acordoempempitem' => 'ac44_acordoitem',
            );
            $aTabelasExcluirPosicao = array(
                'acordoitem' => 'ac20_acordoposicao',
                'acordoposicaoacordomovimentacao' => 'ac31_acordoposicao',
                'acordoalteracaocontratado' => 'ac60_posicao',
                'acordoposicaoaditamento' => 'ac35_acordoposicao',
                'acordoposicaoevento' => 'ac56_acordoposicao',
                'acordoposicaoperiodo' => 'ac36_acordoposicao',
                'acordovigencia' => 'ac18_acordoposicao',
                'acordoposicao' => 'ac26_sequencial',
            );

            $aComandosSql = array();
            $sSqlItensPosicao = "select ac20_sequencial from acordoitem where ac20_acordoposicao = {$iAcordoPosicao}";
            foreach ($aTabelasExcluirItem as $sTabela => $sCampo) {

                $sComandoSql = "delete from {$sTabela} where {$sCampo} in ($sSqlItensPosicao)";
                $aComandosSql[] = $sComandoSql;
            }
            foreach ($aTabelasExcluirPosicao as $sTabela => $sCampo) {

                $sComandoSql = "delete from {$sTabela} where {$sCampo} = {$iAcordoPosicao}";
                $aComandosSql[] = $sComandoSql;
            }

            foreach ($aComandosSql as $sSql) {

                $rsResultado = db_query($sSql);
                if (!$rsResultado) {
                    throw new DBException("Erro ao executar o comando: {$sSql}");
                }
            }

            $sSqlBuscaUltimaPosicao = "select ac26_sequencial from acordoposicao where ac26_acordo = {$iCodigoAcordo} order by ac26_numero desc;";
            $rsBuscaCodigoUltimaPosicao = db_query($sSqlBuscaUltimaPosicao);
            if (!$rsBuscaCodigoUltimaPosicao) {
                throw new Exception("Não foi possível localizar a última posição do acordo.");
            }
            $iCodigoPosicao = db_utils::fieldsMemory($rsBuscaCodigoUltimaPosicao, 0)->ac26_sequencial;
            $rsAcertaSituacao = db_query("update acordoposicao set ac26_situacao = " . AcordoPosicao::SITUACAO_ATIVO . " where ac26_sequencial = {$iCodigoPosicao}");
            if (!$rsAcertaSituacao) {
                throw new Exception("Não foi possível ativar a última posição do acordo.");
            }

            $ultimoAditamento = new AcordoPosicao($iCodigoPosicao);
            $dataInicial = implode('-', array_reverse(explode('/', $ultimoAditamento->getVigenciaInicial())));
            $dataFinal = implode('-', array_reverse(explode('/', $ultimoAditamento->getVigenciaFinal())));
            $sqlUltimoAditamento = "update acordo
                                    set ac16_datainicio = '{$dataInicial}',
                                        ac16_datafim = '{$dataFinal}'
                                    where ac16_sequencial = {$iCodigoAcordo}";
            $rsAcertaDataVigencia = db_query($sqlUltimoAditamento);
            if (!$rsAcertaDataVigencia){
                throw new Exception("Não foi possível alterar a data de vigência.");
            }
            $oRetorno->message = 'Aditamento excluido com sucesso.';
            break;

        default:
            throw new ParameterException('Método não existente.');
    }

   db_fim_transacao(false);

} catch (Exception $e) {

    db_fim_transacao(true);
    $oRetorno->erro = true;
    $oRetorno->message = $e->getMessage();
}

$oRetorno->message = urlencode($oRetorno->message);
echo $oJSON->encode($oRetorno);
