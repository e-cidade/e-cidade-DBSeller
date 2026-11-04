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

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_utils.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/JSON.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));

use \ParameterException;

$oPost       = db_utils::postMemory($_REQUEST);
$oPost->json = str_replace("\\","", $oPost->json);
$parametro  = JSON::create()->parse($oPost->json);
$retorno    = (object)array( 'erro' => false, 'mensagem'=> '');

try {
    switch ( $parametro->exec ) {

        case "getCodigoConvenio":


                $cl_modcarnepadrao = new cl_modcarnepadrao();
                $sqlConvenio = $cl_modcarnepadrao->sql_query_convenio_por_modelo(null, "k48_cadconvenio", null, "k46_descr = 'AUTOATENDIMENTO'");
                $rsConvenio  = db_query($sqlConvenio);

                if(!$rsConvenio) {
                    throw new DBException("Ocorreu um erro ao executar a consulta na base de dados\n". pg_last_error());
                }

                if(pg_num_rows($rsConvenio) > 1) {
                    throw new BusinessException('O modelo de impressão está vinculado há mais de uma regra de emissão.');
                }
                
                if(pg_num_rows($rsConvenio) == 0) {
                    throw new BusinessException('O modelo de impressão não está vinculado a nenhuma regra de emissão.');
                }
                
                $retorno->codigoConvenio = db_utils::fieldsMemory($rsConvenio, 0)->k48_cadconvenio;

                $DataIni = explode("/",$parametro->dini);
                $datainicial = $DataIni[2]."-".$DataIni[1]."-".$DataIni[0];
                $sDiaUtilInicial   = "select fc_ultimo_dia_util('{$datainicial}'::date)";
                $rsDiaUtilInicial  = db_query($sDiaUtilInicial);
                if(pg_result($rsDiaUtilInicial,0) != $datainicial){
                    throw new BusinessException('Vigência Inicial deve ser dia útil!');
                }

                $DataFim = explode("/",$parametro->dfim);
                $datafinal = $DataFim[2]."-".$DataFim[1]."-".$DataFim[0];
                $sDiaUtilFinal   = "select fc_ultimo_dia_util('{$datafinal}'::date)";
                $rsDiaUtilFinal  = db_query($sDiaUtilFinal);
                if(pg_result($rsDiaUtilFinal,0) != $datafinal){
                    throw new BusinessException('Vigência Final deve ser dia útil!');
                }

            break;

        default:
            $retorno->erro = true;
            break;
    }

} catch (Exception $erro) {
    $retorno->erro = true;
    $retorno->mensagem = $erro->getMessage();
}

echo JSON::create()->stringify($retorno);

