<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta" . ".php");
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_utils.php");
require_once modification("libs/db_sessoes.php");
require_once modification("std/db_stdClass.php");
require_once modification("dbforms/db_funcoes.php");


$oParametro = json_decode(str_replace("\\", "", $_POST["json"]));
$oRetorno   = new stdClass();
$oRetorno->erro = false;
$oRetorno->mensagem = "";

try {
    db_inicio_transacao();

    switch ($oParametro->exec) {
        case 'salvar':
            $oDaoAcordo = new cl_acordo;

            $dtInicio = urldecode($oParametro->datainicio);
            $dtFim    = urldecode($oParametro->datafim);

            $oDaoAcordo->ac16_datainicio = "".implode("-", array_reverse(explode("/", $dtInicio)));
            $oDaoAcordo->ac16_datafim    = "".implode("-", array_reverse(explode("/", $dtFim)));
            $oDaoAcordo->ac16_sequencial = $oParametro->codigo_contrato;
            $oDaoAcordo->alterar($oParametro->codigo_contrato);

            if ($oDaoAcordo->erro_status == '0') {
                throw new Exception($oDaoAcordo->erro_msg);
            }

            $oDaoAcordoVigencia = new cl_acordovigencia;

            $oDaoAcordoVigencia->ac18_acordoposicao = $oParametro->posicao;

            $oDaoAcordoVigencia->excluir(null, "ac18_acordoposicao={$oParametro->posicao}");

            $oDaoAcordoVigencia->ac18_acordoposicao = $oParametro->posicao;
            $oDaoAcordoVigencia->ac18_ativo         = "true";
            $oDaoAcordoVigencia->ac18_datainicio    = "".implode("-", array_reverse(explode("/", $dtInicio)));
            $oDaoAcordoVigencia->ac18_datafim       = "".implode("-", array_reverse(explode("/", $dtFim)));

            $oDaoAcordoVigencia->incluir(null);

            if ($oDaoAcordoVigencia->erro_status == '0') {
                throw new Exception($oDaoAcordoVigencia->erro_msg);
            }

            $oRetorno->mensagem = "Posição salvo com sucesso.";

            db_fim_transacao(false);

            break;

        case 'excluir':
            $oAcordoPosicao = new AcordoPosicao($oParametro->posicao);


            $iAcordo = $oAcordoPosicao->getAcordo();
            $iCodigo = $oAcordoPosicao->getCodigo();

            if ($oAcordoPosicao->getTipo() == AcordoPosicao::TIPO_INCLUSAO) {
                throw new Exception('Não é possível excluir a posição de inclusão.');
            }

            $oDaoAcordoPosicaoEvento = new cl_acordoposicaoevento();
            $sSqlAcordoEvento = $oDaoAcordoPosicaoEvento->sql_query_file(null, "ac56_acordoevento", null, "ac56_acordoposicao = {$iCodigo}");
            $rsAcordoEvento = $oDaoAcordoPosicaoEvento->sql_record($sSqlAcordoEvento);
            $iAcordoEvento = db_utils::fieldsMemory($rsAcordoEvento, 0)->ac56_acordoevento;

            $oDaoAcordoDocumentoEvento = new cl_acordodocumentoevento();
            if ($iAcordoEvento) {
                $oDaoAcordoDocumentoEvento->excluir(null, "ac57_acordoevento = {$iAcordoEvento}");
            }

            $oDaoAcordoPosicaoEvento->excluir(null, "ac56_acordoposicao = {$iCodigo}");

            $oDaoEvento = new cl_acordoevento();
            $oDaoEvento->excluir($iAcordoEvento);

            $oAcordoPosicao->remover();

            $oAcordo = new Acordo($iAcordo);
            $oAcordoUltimaPosicao = max($oAcordo->getPosicoes());
            $oAcordoUltimaPosicao->setSituacao(1);
            $oAcordoUltimaPosicao->save();

            $oRetorno->mensagem = "Posição excluída com sucesso.";


            break;

        case 'getPosicoesPorContrato':
            $oAcordo = new Acordo($oParametro->codigo_contrato);

            $oPosicoes          = $oAcordo->getPosicoes();
            $oRetorno->posicoes = array();

            foreach ($oPosicoes as $oPosicao) {
                $oStdPosicao             = new stdClass();

                $oStdPosicao->posicao    = $oPosicao->getCodigo();
                $oStdPosicao->datainicio = $oPosicao->getVigenciaInicial();
                $oStdPosicao->datafim    = $oPosicao->getVigenciaFinal();

                $oRetorno->posicoes[]  = $oStdPosicao;
            }


            break;
    }

    db_fim_transacao(false);
} catch (Exception $eErro) {
    db_fim_transacao(true);
    $oRetorno->erro     = true;
    $oRetorno->mensagem = $eErro->getMessage();
}

$oRetorno->mensagem = urlencode($oRetorno->mensagem);
echo json_encode($oRetorno);
