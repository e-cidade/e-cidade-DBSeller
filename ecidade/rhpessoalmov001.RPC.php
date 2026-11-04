<?php

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_utils.php');
require_once(modification("libs/JSON.php"));

$oJson = new services_json();
$parametros = $oJson->decode(str_replace("\\", "", $_POST["json"]));

try {
    $retorno = new stdClass();
    $retorno->erro = false;

    switch ($parametros->acao) {

        case "buscaMovimentacao":
            
            $clrhpessoalmov = new cl_rhpessoalmov();
            $sSqlDados = $clrhpessoalmov->sql_query_movimentacao($parametros->regist, $parametros->ano, $parametros->mes);
            $rsDados = db_query($sSqlDados);
            $retorno->dados = db_utils::fieldsMemory($rsDados,0);
            
            break;
        case "buscaMatricula":
           
            $clrhpessoal = new cl_rhpessoal();

            $sSqlRhPessoal = '';
            if (isset($parametros->matricula)) {
                if (empty($parametros->matricula)){
                    throw new Exception("É necessário informar o código da Matrícula a ser consultada.");
                }
                $sSqlRhPessoal = $clrhpessoal->sql_query_file(null,"rh01_regist",null,"rh01_regist  ='$parametros->matricula'");
            }
            if (isset($parametros->iCgm)) {
                if (empty($parametros->iCgm)){
                    throw new Exception("É necessário informar o código do CGM a ser consultado.");
                }
                $sSqlRhPessoal = $clrhpessoal->sql_query_file(null,"rh01_regist",null,"rh01_numcgm  ='$parametros->iCgm'");
            }
            $iQtdLinhas = 0;

            if (!empty($sSqlRhPessoal)) {
                $rsRHPessoal = db_query($sSqlRhPessoal);
                $iQtdLinhas = pg_num_rows($rsRHPessoal);    
            }

            $aMatriculas = array();
            for($i = 0; $i < $iQtdLinhas; $i++){

                $aMatriculas[] = db_utils::fieldsMemory($rsRHPessoal, $i);

            }
            
            $retorno->matriculas = $aMatriculas;

            break;
        case "buscaVinculo":

            if (empty($parametros->codigoVinculo) || !is_numeric($parametros->codigoVinculo)) {
                throw new Exception("É necessário informar o código do vínculo a ser consultado.");
            }

            $daoRhRegime = new cl_rhregime();
            $where = " rh30_codreg = {$parametros->codigoVinculo} ";
            $sqlRhRegime = $daoRhRegime->sql_query_file($parametros->codigoVinculo, "rh30_codreg as codigo, rh30_descr as descricao, rh30_vinculoemprego as vinculo");
            $rsRhRegime = db_query($sqlRhRegime);

            if (!$rsRhRegime) {
                throw new Exception("Não foi possível buscar o regime selecionado.\nContate o suporte.");
            }

            $retorno->regime = db_utils::fieldsMemory($rsRhRegime, 0);

            break;
    }
} catch (Exception $exception) {
    $retorno->mensagem = $exception->getMessage();
    $retorno->erro = true;
}

echo JSON::create()->stringify($retorno);
