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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db"."_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/exceptions/BusinessException.php"));
require_once(modification("libs/exceptions/DBException.php"));
require_once(modification("libs/exceptions/ParameterException.php"));
require_once(modification("dbforms/db_funcoes.php"));


$oJson                = new services_json();
$oParam               = $oJson->decode(str_replace("\\","",$_POST["json"]));
$oRetorno             = new stdClass();
$oRetorno->lErro      = false;
$oRetorno->sMessage   = '';

$iInstituicaoSessao = db_getsession('DB_instit');
$iAnoSessao         = db_getsession('DB_anousu');

try {

    db_inicio_transacao();

    switch ($oParam->exec) {


        case "AcertaAcordoItemDotacao":


            $ac16_sequencial = $oParam->ac16_sequencial;

            $sSqlAcerto = "
                       update acordoitemdotacao
                        set ac22_valor = ac20_valorunitario * ac22_quantidade
                      from
                        acordoitem
                      where
                        ac20_acordoposicao in(
                          select
                            ac26_sequencial
                          from
                            acordoposicao
                          where
                            ac26_acordo in({$ac16_sequencial})
                        )
                        and ac22_acordoitem = ac20_sequencial
  		";

            $rsAcerto = db_query($sSqlAcerto);

            if ( !$rsAcerto ) {
                Throw new Exception( "Erro ao Alterar Registro \n " . pg_last_error()  );
            }

            db_fim_transacao(false);
            $oRetorno->sMessage = urlencode("Processo Efetuado Com Sucesso.");


            break;


    }

} catch (Exception $eErro) {

    db_fim_transacao(true);
    $oRetorno->lErro    = true;
    $oRetorno->sMessage = urlencode($eErro->getMessage());
}

echo $oJson->encode($oRetorno);
