<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009 DBSeller Servicos de Informatica             
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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("fpdf151/pdf.php"));

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));
$oRetorno = new stdClass();

$oRetorno->status  = 1;
$oRetorno->message = '';
$sMsg = '';
$lErro = false;

db_inicio_transacao();
try {
    switch ($oParam->exec) {
    case 'getExercicioMatriculas':
        if (empty($oParam->matricula)) {
            throw new BusinessException("Matrícula não informada.");   
        }

        $oRetorno = buscaInformacoes($oParam);
        //inserirExercioeMesNaoExistentesEmServidorRelatorioArquivoGenerico($oParam->matricula);

        $sql = <<<SQL
            select
                rh217_mesusu as mes,
                rh217_anousu as exercicio,
                rh217_informacao as valor,
                'slag' as addres,
                1 as editado
            from
                servidorrelatorioarquivogenerico
            where
                rh217_regist = {$oParam->matricula}
                and rh217_arquivorelatorio = 'portaria154AnexoII'
            union all
            select
                r14_mesusu as mes,
                r14_anousu as exercicio,
                round(r14_valor, 2)::varchar as valor,
                'gerfsal' as addres,
                coalesce(
                    (
                        select 
                            1 
                        from 
                            servidorrelatorioarquivogenerico 
                        where 
                            rh217_regist = r14_regist
                            and rh217_arquivorelatorio = 'portaria154AnexoII'
                            and rh217_mesusu = r14_mesusu
                            and rh217_anousu = r14_anousu limit 1
                    ),
                0) as editado
            from
                gerfsal
            where r14_regist = {$oParam->matricula}
                union all
            select
                r48_mesusu as mes,
                r48_anousu as exercicio,
                round(r48_valor, 2)::varchar as valor,
                'gerfcom' as addres,
                coalesce(
                    (
                        select 
                            1 
                        from 
                            servidorrelatorioarquivogenerico 
                        where 
                            rh217_regist = r48_regist
                            and rh217_arquivorelatorio = 'portaria154AnexoII'
                            and rh217_mesusu = r48_mesusu
                            and rh217_anousu = r48_anousu limit 1
                    ),
                0) as editado
            from
                gerfcom
            where r48_regist = {$oParam->matricula}
            union all
            select
                r35_mesusu as mes,
                r35_anousu as exercicio,
                round(r35_valor, 2)::varchar as valor,
                'gerfs13' as addres,
                coalesce(
                    (
                        select 
                            1 
                        from 
                            servidorrelatorioarquivogenerico 
                        where 
                            rh217_regist = r35_regist
                            and rh217_arquivorelatorio = 'portaria154AnexoII'
                            and rh217_mesusu = r35_mesusu
                            and rh217_anousu = r35_anousu limit 1
                    ),
                0) as editado
            from
                gerfs13
            where r35_regist = {$oParam->matricula}
            order by exercicio asc, mes asc, addres asc;
SQL;
        $rs = db_query($sql);

        $oRetorno->possuiDados = false;
        if (!$rs) {
            throw new DBException("Erro ao buscar informacoes na base de dados para a matrícula " . $oParam->matricula . ".");                
        }           
        $mesesDefault = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0,
            13 => 0
        ];
        if (pg_num_rows($rs) > 0) {
            $oRetorno->possuiDados = true;
            $aExercicioAnterior    = array();
            $contador              = pg_num_rows($rs);

            for ($i = 0; $i < $contador; $i++) {
                $dado = db_utils::fieldsMemory($rs, $i);

                if (trim($dado->addres) == 'gerfs13') {
                    $dado->mes = 13; 
                }

                if (empty($aExercicioAnterior[$dado->exercicio])) {
                    $aExercicioAnterior[$dado->exercicio] = array();
                    $aExercicioAnterior[$dado->exercicio]['exercicio'] = $dado->exercicio;
                    $aExercicioAnterior[$dado->exercicio]['mes'] = $mesesDefault;
                }
                if ($dado->editado) {
                    if ($dado->addres == 'slag') {
                        $aExercicioAnterior[$dado->exercicio]['mes'][$dado->mes] = $dado->valor;
                    }
                } else {
                    if ($dado->addres != 'slag') {
                        $aExercicioAnterior[$dado->exercicio]['mes'][$dado->mes] += $dado->valor;
                    }
                }
            } 

            $oRetorno->dados = inserirDecimoEmExerciciosZerados($aExercicioAnterior);          
        }
        break;
    case 'salvarExercicios':
        if (empty($oParam->matricula)) {
            throw new BusinessException("Matrícula não informada.");
        }

        if (empty($oParam->dados) || sizeof($oParam->dados) == 0) {
            throw new BusinessException("Nenhuma informação informada.");
        }
        $oPeriodo = $oRetorno = buscaInformacoes($oParam);
        foreach ($oParam->dados as $dado) {
            $atualiza = false;
            $sql = "select * from servidorrelatorioarquivogenerico where rh217_regist = " . $oParam->matricula 
                    . " and rh217_anousu = " . $dado->exercicio . " and rh217_mesusu = " .  $dado->mes . " and rh217_arquivorelatorio = 'portaria154AnexoII'";
            
            $rs = db_query($sql);

            if (!$rs) {
                throw new DBException("Erro ao buscar informacoes na base de dados para a matrícula " . $oParam->matricula . ".");                
            }                 

            if (pg_num_rows($rs) > 0) {
                $atualiza = true;
            }   

            if (empty($dado->valor)) {
                $dado->valor = 0.00;
            }
            $informacoes = new \cl_servidorrelatorioarquivogenerico();
            $informacoes->rh217_anousu = $dado->exercicio;
            $informacoes->rh217_mesusu = $dado->mes;
            $informacoes->rh217_regist = $oParam->matricula;
            $informacoes->rh217_arquivorelatorio = "portaria154AnexoII";
            $informacoes->rh217_informacao = (string)$dado->valor;
            if ($atualiza) {
                $informacoes->alterar();
            } else {
                $informacoes->incluir();
            }
        }
        $oRetorno->message = utf8_encode('Informações dos exercicios salvas com sucesso.');
        break;
    }
    db_fim_transacao();
} catch (Exception $e) {
    $oRetorno->status = 2;
    db_fim_transacao(true);
    $oRetorno->erro = utf8_encode($e->getMessage());
}
echo $oJson->encode($oRetorno);

function buscaInformacoes($oParam) {
    $oRetorno = new \stdClass();
    $oRetorno->status  = 1;
    $oRetorno->message = '';
    $oServidor = \ServidorRepository::getServidoresByMatriculas(\DBPessoal::getAnoFolha(), \DBPessoal::getMesFolha(), array($oParam->matricula));
    $oServidor = $oServidor[$oParam->matricula];
    $oRetorno->anoInicial = date('Y', $oServidor->getDataAdmissao()->getTimestamp());
    $oRetorno->mesInicial = date('m', $oServidor->getDataAdmissao()->getTimestamp());
    $oRetorno->exercicio = date('Y', $oServidor->getDataAdmissao()->getTimestamp());
    $sql = "select r14_anousu, r14_mesusu, 'gerfsal' as addres from gerfsal where r14_rubric = 'R992' and r14_regist = {$oParam->matricula} order by r14_anousu asc, r14_mesusu asc limit 1";
    $rs  = db_query($sql);
    $oRetorno->anoBloqueio = \DBPessoal::getAnoFolha();
    $oRetorno->mesBloqueio = \DBPessoal::getMesFolha();
    $possuiRescisao = $oServidor->getDataRescisao(); 
    if (!empty($possuiRescisao)) {
        $oRetorno->anoBloqueio = date('Y', $oServidor->getDataRescisao()->getTimestamp());
        $oRetorno->mesBloqueio = date('m', $oServidor->getDataRescisao()->getTimestamp());
    }
    if ($rs) {
        if (pg_num_rows($rs) > 0) {
            $bloqueio = db_utils::fieldsMemory($rs, 0);
            if ($bloqueio->r14_mesusu == 1) {
                $bloqueio->r14_mesusu = 12;
                $bloqueio->r14_anousu -= 1;
            }
            $oRetorno->mesBloqueio = $bloqueio->r14_mesusu;
            $oRetorno->anoBloqueio = $bloqueio->r14_anousu;
        }
    }

    $oRetorno->status  = 1;
    return $oRetorno;
}


function inserirDecimoEmExerciciosZerados($exercicios){
    foreach($exercicios as $key => $exercicio){
        if(empty($exercicio["mes"][13])){
            $exercicio["mes"][13] = 0;
        }

        $exercicios[$key] = $exercicio;
        
    }
    
    return $exercicios;
}

function inserirExercioeMesNaoExistentesEmServidorRelatorioArquivoGenerico($matricula){
    
    $oServidor = \ServidorRepository::getServidoresByMatriculas(\DBPessoal::getAnoFolha(), \DBPessoal::getMesFolha(), array($matricula));
    $oServidor = $oServidor[$matricula];
    $anoAdmissao = date('Y', $oServidor->getDataAdmissao()->getTimestamp());
    $mesAdmissao = date('m', $oServidor->getDataAdmissao()->getTimestamp()); 
    
    $anoDemissao = null;
    $mesDemissao = null;

    if (!empty($oServidor->getDataRescisao())){
        $anoDemissao = date('Y', $oServidor->getDataRescisao()->getTimestamp());
        $mesDemissao = date('m', $oServidor->getDataRescisao()->getTimestamp());

    }


    $anoAtual = !empty($anoDemissao) ? $anoDemissao:\DBPessoal::getAnoFolha();
    
     
    foreach(range($anoAdmissao,$anoAtual) as $ano) {
               
        $meses = periodoDeMeses($ano,$anoAdmissao,$mesAdmissao,$anoAtual,$mesDemissao);        
        
        foreach ($meses as $mes){
               
            if(!existeInformacaoEmServidorRelatorioArquivoGenerico($matricula, $ano, $mes)){
                inserirEmServidorRelatorioArquivoGenerico($matricula, $ano, $mes);

            }

        }
        
    }

}

function inserirEmServidorRelatorioArquivoGenerico($matricula, $ano, $mes) {

    $daoServidorRelatorioArquivoGenerico = new \cl_servidorrelatorioarquivogenerico();

    $daoServidorRelatorioArquivoGenerico->rh217_regist = $matricula;
    $daoServidorRelatorioArquivoGenerico->rh217_anousu = $ano;
    $daoServidorRelatorioArquivoGenerico->rh217_mesusu = $mes;
    $daoServidorRelatorioArquivoGenerico->rh217_arquivorelatorio = 'portaria154AnexoII';
    $daoServidorRelatorioArquivoGenerico->rh217_informacao = '0,00';
    $daoServidorRelatorioArquivoGenerico->incluir();

}

function periodoDeMeses($ano,$anoAdmissao,$mesAdmissao,$anoAtual,$mesDemissao = null){
    
    $meses = array();

    switch ($ano) {
        case $anoAdmissao:
            $meses = range($mesAdmissao,12);
        break;
        case $anoAtual:
            $mesFolha = !empty($mesDemissao) ? $mesDemissao: \DBPessoal::getMesFolha();
            if ($anoAtual == $anoAdmissao){
                $meses = range($mesAdmissao,$mesFolha);
            }else{
                $meses = range(1,$mesFolha);
            }
        break;
        default:
            $meses = range(1,12);
        break;
    }

    return $meses;
}

function existeInformacaoEmServidorRelatorioArquivoGenerico($matricula, $ano, $mes){

        $sql = "select 
                    * 
                from 
                    servidorrelatorioarquivogenerico 
                where 
                    rh217_regist = {$matricula} 
                    and rh217_anousu = {$ano} 
                    and rh217_mesusu = {$mes}";
        
        $result = db_query($sql);

        if (pg_num_rows($result) > 0) {

            return true;

        }
                

    return false;

}
