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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\","",$_POST["json"]));

$oRetorno = new stdClass();
$oRetorno->status = 1;
// Foi removida a logica de buscar e atualizar da tela, pois o post estava com muitos parametros
// ocasionando em perca de informacoes e/ou demora na renderizacao da pagina
// os codigo abaixo foram extraido da tela, apenas renomeando variaveis e fazendo adequacoes necessarias
try {
    switch ($oParam->exec) {
       /*
        * Pesquisa status do sistema
        */
        case "atualizar":
            db_inicio_transacao();
            $sqlerro = false;
            $clAlmoxDepto = new \cl_db_almoxdepto;
            $almoxDepto = $clAlmoxDepto->sql_record($clAlmoxDepto->sql_query_file($oParam->codalmox, "", "m92_depto"));
            // deletando vinculos
            if ($clAlmoxDepto->numrows > 0) {
                $qtdAmoxDepto = $clAlmoxDepto->numrows;
                for ($i = 0; $i < $qtdAmoxDepto; $i++) {
                    if ($sqlerro == false) {
                        $codDepto = \db_utils::fieldsMemory($almoxDepto, $i)->m92_depto;
                        $clAlmoxDepto->m92_codalmox = $oParam->codalmox;
                        $clAlmoxDepto->m92_depto = $oParam->codDepto;
                        $clAlmoxDepto->excluir($oParam->codalmox, $codDepto);
                        $erro_msg = $clAlmoxDepto->erro_msg;
                        if ($clAlmoxDepto->erro_status == '0') {
                            $sqlerro = true;
                            continue;
                        }
                    }
                }
            }
            // Caso tenha dado erro na exclusao, nao inclui
            if ($sqlerro == false) {
                $qtdAmoxDepto = sizeof($oParam->departamentos);
                // Criando Vinculos
                for ($i = 0; $i < $qtdAmoxDepto; $i++) {
                    $clAlmoxDepto->m92_depto = $oParam->departamentos[$i];
                    $clAlmoxDepto->m92_codalmox = $oParam->codalmox;
                    $clAlmoxDepto->incluir($oParam->codalmox, $oParam->departamentos[$i]);
                    $erro_msg = $clAlmoxDepto->erro_msg;
                    if ($clAlmoxDepto->erro_status == '0') {
                        $oRetorno->message = str_replace('\n', "", utf8_encode($erro_msg));
                        $sqlerro = true;
                        continue;
                    }
                }
            }

            if ($sqlerro == false) {
                $oRetorno->message = "Departamentos atualizados com sucesso.".$sqlerro;
            }

            db_fim_transacao($sqlerro);
            break;
        case "buscaDepartamentos":
            $sql = "select coddepto, descrdepto, case when m92_depto is not null then 1 else 0 end as selecionado from db_depart left join db_almoxdepto on m92_depto = coddepto and m92_codalmox = $oParam->codalmox where  instit = {$oParam->instituicao} and (limite >=  '$oParam->dtAtual' or limite is null) order by coddepto";
            $rsDepartamentos = db_query($sql);
            if (!$rsDepartamentos) {
                throw new DBException("Erro ao buscar os departamentos. Caso o problema persista, contate o suporte.");
            }

            if (pg_num_rows($rsDepartamentos) == 0) {
                throw new BusinessException("Não foi encontrado nenhum departamento cadastrado para a instituição.");
            }

            $qtdDepartamentos = pg_num_rows($rsDepartamentos);
            $oRetorno->departamentos = array();
            for ($i = 0; $i < $qtdDepartamentos; $i++) {
                $oRetorno->departamentos[] = db_utils::fieldsMemory($rsDepartamentos, $i);

            }
            break;
    }
} catch (Exception $e) {
    $oRetorno->status = 0;
    $oRetorno->message = utf8_encode($e->getMessage());
}

echo Json::create()->stringify($oRetorno);
