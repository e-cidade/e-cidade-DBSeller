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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$oJson = JSON::create();
$parametro = $oJson->parse(str_replace("\\", "", $_POST["json"]));
$retorno = new stdClass();
$retorno->iStatus = 1;
$retorno->sMensagem = '';
$retorno->erro = false;
$iInstituicaoSessao = db_getsession("DB_instit");
try {
    switch ($parametro->exec) {


        case  'getLinhasDePactoDoPlano':

            $retorno->linhas = array();
            $daoPlanoOrcamentarioLinhaPacto = new cl_planoorcamentariolinhapacto();
            $sSqlQuery = $daoPlanoOrcamentarioLinhaPacto->sql_query(null,
                "o156_sequencial as codigo, c07_titulo as descricao",
                "o156_sequencial",
                "o156_orcdotacaoplanoorcamentario = " . $parametro->plano
            );
            $rsDados = db_query($sSqlQuery);
            if (!$rsDados) {
                throw  new \DBException('Erro ao pesquisar dados de ' . $this->DDTabela->name);
            }
            if (pg_num_rows($rsDados) > 0) {
                $retorno->linhas = db_utils::makeCollectionFromRecord($rsDados, function ($dados) {
                    $dados->saldo_final = getSaldoPacto($dados->codigo);
                    return $dados;
                });
            }
            break;
    }

    $retorno->sMensagem = urlencode($retorno->sMensagem);
} catch (Exception $eErro) {

    $retorno->iStatus = 2;
    $retorno->sMensagem = urlencode($eErro->getMessage());
    $retorno->erro = true;
}

echo $oJson->stringify($retorno);

/**
 * @param $pacto
 * @return int
 */
function getSaldoPacto($pacto)
{
    $saldo = 0;
    $sqlSaldo = "select 
                       coalesce((select sum(o161_saldo)
                                       from linhapactosaldo
                                      where o161_linhapacto = o156_sequencial), 0) as saldo_final
                  from planoorcamentariolinhapacto
                 where o156_sequencial = {$pacto}";
     $rs = db_query($sqlSaldo);
     if (pg_num_rows($rs) > 0) {
         $saldo =   db_utils::fieldsMemory($rs, 0)->saldo_final;
     }
     return $saldo;
}

