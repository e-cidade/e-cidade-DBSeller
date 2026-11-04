<?php
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("dbforms/db_funcoes.php"));

$parametros = JSON::requestParameters();
$retorno = (object)['erro' => false, 'mensagem' => ''];

$iInstituicao = db_getsession("DB_instit");

try {

    db_inicio_transacao();
    
    switch ($parametros->acao) {
        case 'buscarDadosCadastroLocalTrabalho':
            
            $clRhLocalTrab                       = new cl_rhlocaltrab;
            $clRhLocalTrabEquipamentoProtecao    = new cl_rhlocaltrabequipamentoprotecao();
            $clRhLocalTrabEquipamentoProtecaoEPI = new cl_rhlocaltrabequipamentoprotecaoepi();
            $clRhLocalTrabAgentesNocivos         = new cl_rhlocaltrabagentesnocivos();
            $clRhLocalTrabRegistroAmbiental      = new cl_rhlocaltrabregistroambiental();
            
            $aDadosAgentesNocivos = array();
            $aDadosResponsaveisRegistrosAmbientais = array();
            
            $arquivoEventoS2240Tabela24     = array();
            $arquivoEventoS2240OpcoesUnMed  = array();
            
            $sCaminhoArquivoEventoS2240Tabela24 = "./arquivos/esocial/tabelas/eventoS2240_tabela24.json";
            if (file_exists($sCaminhoArquivoEventoS2240Tabela24)) {
                $arquivoEventoS2240Tabela24Quimicos = json_decode(file_get_contents($sCaminhoArquivoEventoS2240Tabela24));
            }
            
            $sCaminhoArquivoEventoS2240OpcoesUnMed = "./arquivos/esocial/tabelas/eventoS2240_opcoes_unMed.json";
            if (file_exists($sCaminhoArquivoEventoS2240OpcoesUnMed)) {
                $arquivoEventoS2240OpcoesUnMed = json_decode(file_get_contents($sCaminhoArquivoEventoS2240OpcoesUnMed));
            }
            
            
            $sCampos = "rhlocaltrab.*,rh86_criteriorateio, cc08_descricao";
            $sSqlRhLocalTrab = $clRhLocalTrab->sql_query_centro_custo($parametros->rh55_codigo,$iInstituicao,$sCampos);
            $rsDadosRhLocalTrab = $clRhLocalTrab->sql_record($sSqlRhLocalTrab);
            if ($clRhLocalTrab->numrows == "0") {
                throw new Exception("Não foram encontradas informações para o local de trabalho informado");
            }
            $oDadosRhLocalTrab = db_utils::fieldsMemory($rsDadosRhLocalTrab,0); 
            
            /*
             * Buscamos os dados dos agentes nocivos do local de trabalho
             * Percorremos os resultados montamos um array com os dados
             * Atribuimos o array ao objeto $oDadosRhLocalTrab
             */
            $aDadosRhLocalTrabAgentesNocivos = array();
            
            $sSqlRhLocalTrabAgentesNocivos = $clRhLocalTrabAgentesNocivos->sql_query_file(null,
                                                                                          "rh256_sequencial as id, 
                                                                                           rhlocaltrabagentesnocivos.*",
                                                                                          null,
                                                                                          "rh256_rhlocaltrab = {$parametros->rh55_codigo}");
            $rsDadosRhLocalTrabAgentesNocivos = $clRhLocalTrabAgentesNocivos->sql_record($sSqlRhLocalTrabAgentesNocivos);
            for ($iInd = 0; $iInd < $clRhLocalTrabAgentesNocivos->numrows; $iInd++) {
                
                $oAgenteNocivo = db_utils::fieldsMemory($rsDadosRhLocalTrabAgentesNocivos, $iInd);
                
                $oAgenteNocivo->rh256_agentenocivo_descricao    = $oAgenteNocivo->rh256_agentenocivo;
                $oAgenteNocivo->rh256_medida_descricao          = $oAgenteNocivo->rh256_medida;          
                
                if (!empty($oAgenteNocivo->rh256_agentenocivo)) {
                    foreach($arquivoEventoS2240Tabela24 as $oOpcao) {
                      if ($oAgenteNocivo->rh256_agentequimico == $oOpcao->value) {
                          $oAgenteNocivo->rh256_agentequimico_descricao = $oOpcao->label;
                      }
                    }
                }
                
                if (!empty($oAgenteNocivo->rh256_medida)) {
                    foreach($arquivoEventoS2240OpcoesUnMed as $oOpcao) {
                      if ($oAgenteNocivo->rh256_medida == $oOpcao->value) {
                         $oAgenteNocivo->rh256_medida_descricao = $oOpcao->label;
                      }
                    }
                }
                
                $oAgenteNocivo->oDadosEquipamentosProtecao = new stdClass();
                $oAgenteNocivo->oDadosEquipamentosProtecao->aDadosEPIs = array();
                
                /*
                 * Buscamos os dados dos equipamentos de protecao
                 * Percorremos os resultados e adicionamos ao objeto $oDadosRhLocalTrab
                 */
                $sSqlRhLocalTrabEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->sql_query_file(null,
                                                                                                        "rh257_rhlocaltrabagentesnocivos as agentenocivo_id, 
                                                                                                         rhlocaltrabequipamentoprotecao.*",
                                                                                                        null,
                                                                                                        "rh257_rhlocaltrabagentesnocivos = {$oAgenteNocivo->rh256_sequencial}");
                $rsDadosRhLocalTrabEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->sql_record($sSqlRhLocalTrabEquipamentoProtecao);
                if ($clRhLocalTrabEquipamentoProtecao->numrows > 0) {
                    $oDadosRhLocalTrabEquipamentoProtecao = db_utils::fieldsMemory($rsDadosRhLocalTrabEquipamentoProtecao,0);
                    
                    $sWhere = "rhlocaltrabequipamentoprotecaoepi.rh259_rhlocaltrabequipamentoprotecao = {$oDadosRhLocalTrabEquipamentoProtecao->rh257_sequencial}";
                    $sSqlRhLocalTrabEquipamentoProtecaoEPI = $clRhLocalTrabEquipamentoProtecaoEPI->sql_query_file(null,
                                                                                                                  "{$oDadosRhLocalTrabEquipamentoProtecao->rh257_rhlocaltrabagentesnocivos} as agentenocivo_id, 
                                                                                                                   rhlocaltrabequipamentoprotecaoepi.*",
                                                                                                                  null,
                                                                                                                  $sWhere);
                    $rsDadosRhLocalTrabEquipamentoProtecaoEPI = $clRhLocalTrabEquipamentoProtecaoEPI->sql_record($sSqlRhLocalTrabEquipamentoProtecaoEPI);
                    $oDadosRhLocalTrabEquipamentoProtecao->aDadosEPIs = db_utils::getCollectionByRecord($rsDadosRhLocalTrabEquipamentoProtecaoEPI);
                    
                    
                    $oAgenteNocivo->oDadosEquipamentosProtecao = $oDadosRhLocalTrabEquipamentoProtecao;
                }
                    
                $aDadosRhLocalTrabAgentesNocivos[] = $oAgenteNocivo;
            }
            
            $oDadosRhLocalTrab->aDadosAgentesNocivos = $aDadosRhLocalTrabAgentesNocivos;
            
            /*
             * Buscamos os dados dos responsaveis pelos registros ambientais do local de trabalho
             * Percorremos os resultados montamos um array com os dados
             * Atribuimos o array ao objeto $oDadosRhLocalTrab
             */
            $sSqlRhLocalTrabRegistroAmbiental = $clRhLocalTrabRegistroAmbiental->sql_query_file(null,
                                                                                                "*",
                                                                                                null,
                                                                                                "rh258_rhlocaltrab = {$parametros->rh55_codigo}");
            $rsDadosRhLocalTrabRegistroAmbiental = $clRhLocalTrabRegistroAmbiental->sql_record($sSqlRhLocalTrabRegistroAmbiental);
            $oDadosRhLocalTrabRegistroAmbiental = db_utils::getCollectionByRecord($rsDadosRhLocalTrabRegistroAmbiental);
            $oDadosRhLocalTrab->aDadosResponsaveisRegistrosAmbientais = $oDadosRhLocalTrabRegistroAmbiental;
            
            $retorno->dados = $oDadosRhLocalTrab;
            
        break;
        case 'salvarLocalTrabalho':
            
            $aDadosAgentesNocivos                  = json_decode(utf8_encode($parametros->JsonDadosAgentesNocivos));
            $aDadosResponsaveisRegistrosAmbientais = json_decode(utf8_encode($parametros->JsonDadosResponsaveisRegistrosAmbientais));
            
            $clRhLocalTrab = new cl_rhlocaltrab;
            $clRhLocalTrab->rh55_instit                        = $iInstituicao;
            $clRhLocalTrab->rh55_estrut                        = $parametros->rh55_estrut;
            $clRhLocalTrab->rh55_descr                         = $parametros->rh55_descr; 
            $clRhLocalTrab->rh55_inep                          = $parametros->rh55_inep;
            $clRhLocalTrab->rh55_tipolocal                     = $parametros->rh55_tipolocal;                      
            $clRhLocalTrab->rh55_endereco                      = $parametros->rh55_endereco;                      
            $clRhLocalTrab->rh55_tipoestabelecimento           = $parametros->rh55_tipoestabelecimento;           
            $clRhLocalTrab->rh55_tipoinscricao                 = $parametros->rh55_tipoinscricao;                 
            $clRhLocalTrab->rh55_numeroinscricao               = $parametros->rh55_numeroinscricao;               
            $clRhLocalTrab->rh55_observacaoregistrosambientais = $parametros->rh55_observacaoregistrosambientais; 
            $clRhLocalTrab->rh55_lotacaotributaria             = $parametros->rh55_lotacaotributaria; 
            
            if (empty($parametros->rh55_codigo)) {
                $clRhLocalTrab->incluir(null,$iInstituicao);
            } else {
                $clRhLocalTrab->rh55_codigo = $parametros->rh55_codigo;
                $clRhLocalTrab->alterar($parametros->rh55_codigo,$iInstituicao);
            }
            
            if ($clRhLocalTrab->erro_status == 0)  {
                throw new Exception($clRhLocalTrab->erro_msg);
            }
            
            if ($parametros->rh86_criteriorateio != "")
            {
                
                $clRhpesLocalCusto = new cl_rhlocaltrabcustoplano;
                $sWhereExclusao = "rh86_instit = $clRhLocalTrab->rh55_instit and rh86_rhlocaltrab = $clRhLocalTrab->rh55_codigo";
                $clRhpesLocalCusto->excluir(null, $sWhereExclusao);
                if ($clRhpesLocalCusto->erro_status == 0) {
                    throw new Exception($clRhpesLocalCusto->erro_msg);
                }
                
                $clRhpesLocalCusto                      = new cl_rhlocaltrabcustoplano;
                $clRhpesLocalCusto->rh86_rhlocaltrab    = $clRhLocalTrab->rh55_codigo;
                $clRhpesLocalCusto->rh86_instit         = $clRhLocalTrab->rh55_instit;
                $clRhpesLocalCusto->rh86_criteriorateio = $parametros->rh86_criteriorateio;
                $clRhpesLocalCusto->incluir(null);
                if ($clRhpesLocalCusto->erro_status == 0) {
                    throw new Exception($clRhpesLocalCusto->erro_msg);
                }
            }
            
            $clRhLocalTrabEquipamentoProtecao = new cl_rhlocaltrabequipamentoprotecao();
            $sCampoLista = "array_to_string(array_accum(rhlocaltrabequipamentoprotecao.rh257_sequencial), ',') as lista";
            $sWhere  = "rhlocaltrabagentesnocivos.rh256_rhlocaltrab = {$clRhLocalTrab->rh55_codigo} "; 
            $sWhere .= " and rhlocaltrabagentesnocivos.rh256_instituicao = {$iInstituicao} ";
            $sSqlEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->sql_query(null,$sCampoLista,null,$sWhere);
            $rsEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->sql_record($sSqlEquipamentoProtecao);
            $iQtdRegistrosEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->numrows;
            if ($iQtdRegistrosEquipamentoProtecao > 0) {
                
                $sListaEquipamentosProtecao = db_utils::fieldsMemory($rsEquipamentoProtecao, 0)->lista;
                if (!empty($sListaEquipamentosProtecao)) {
                  $clRhLocalTrabEquipamentoProtecaoEPI = new cl_rhlocaltrabequipamentoprotecaoepi();
                  $sWhereExclusao = "rh259_rhlocaltrabequipamentoprotecao in ({$sListaEquipamentosProtecao})";
                  $clRhLocalTrabEquipamentoProtecaoEPI->excluir(null,$sWhereExclusao);
                  if ($clRhLocalTrabEquipamentoProtecaoEPI->erro_status == 0)
                  {
                      $sMsgErro  = "Erro realizando exclusão dos EPIs ligados aos equipamentos do local de trabalho.\n\n";
                      $sMsgErro .= $clRhLocalTrabAgentesNocivos->erro_msg."\n\n";
                      $sMsgErro .= pg_last_error();
                      throw new Exception($sMsgErro);
                  }
                  
                  $clRhLocalTrabEquipamentoProtecao = new cl_rhlocaltrabequipamentoprotecao();
                  $sWhereExclusao = "rh257_sequencial in ({$sListaEquipamentosProtecao})";
                  $clRhLocalTrabEquipamentoProtecao->excluir(null,$sWhereExclusao);
                  if ($clRhLocalTrabEquipamentoProtecao->erro_status == 0)
                  {
                      $sMsgErro  = "Erro realizando exclusão das informações de EPC e EPI.\n\n";
                      $sMsgErro .= $clRhLocalTrabEquipamentoProtecao->erro_msg."\n\n";
                      $sMsgErro .= pg_last_error();
                      throw new Exception($sMsgErro);
                  }
                }
                
            }
            
            $clRhLocalTrabAgentesNocivos = new cl_rhlocaltrabagentesnocivos();
            $sWhereExclusao = "rh256_rhlocaltrab = {$clRhLocalTrab->rh55_codigo} and rh256_instituicao = {$iInstituicao}";
            $clRhLocalTrabAgentesNocivos->excluir(null,$sWhereExclusao);
            if ($clRhLocalTrabAgentesNocivos->erro_status == 0) 
            {
                $sMsgErro  = "Erro realizando exclusão dos agentes nocivos ligados ao local de trabalho.\n\n";
                $sMsgErro .= $clRhLocalTrabAgentesNocivos->erro_msg."\n\n";
                $sMsgErro .= pg_last_error();
                throw new Exception($sMsgErro);
            }
            
            foreach ($aDadosAgentesNocivos as $oAgenteNocivo) 
            {
                $clRhLocalTrabAgentesNocivos = new cl_rhlocaltrabagentesnocivos();
                $clRhLocalTrabAgentesNocivos->rh256_sequencial              = null;              
                $clRhLocalTrabAgentesNocivos->rh256_rhlocaltrab             = $clRhLocalTrab->rh55_codigo;
                $clRhLocalTrabAgentesNocivos->rh256_instituicao             = $iInstituicao;             
                $clRhLocalTrabAgentesNocivos->rh256_agentenocivo            = $oAgenteNocivo->rh256_agentenocivo;           
                $clRhLocalTrabAgentesNocivos->rh256_tipoavaliacao           = $oAgenteNocivo->rh256_tipoavaliacao;           
                $clRhLocalTrabAgentesNocivos->rh256_intensidadeconcentracao = utf8_decode($oAgenteNocivo->rh256_intensidadeconcentracao); 
                $clRhLocalTrabAgentesNocivos->rh256_tolerancialimite        = utf8_decode($oAgenteNocivo->rh256_tolerancialimite);        
                $clRhLocalTrabAgentesNocivos->rh256_medida                  = $oAgenteNocivo->rh256_medida;                  
                $clRhLocalTrabAgentesNocivos->rh256_tecnicamedicao          = utf8_decode($oAgenteNocivo->rh256_tecnicamedicao);
                $clRhLocalTrabAgentesNocivos->rh256_processo = $oAgenteNocivo->rh256_processo;
                $clRhLocalTrabAgentesNocivos->incluir(null);
                if ($clRhLocalTrabAgentesNocivos->erro_status == 0) 
                {
                    $sMsgErro  = "Erro realizando inclusao do agente nocivo ligado ao local de trabalho.\n\n";
                    $sMsgErro .= $clRhLocalTrabAgentesNocivos->erro_msg."\n\n";
                    $sMsgErro .= pg_last_error();
                    throw new Exception($sMsgErro);
                }
                
                
                $clRhLocalTrabEquipamentoProtecao = new cl_rhlocaltrabequipamentoprotecao();
                $clRhLocalTrabEquipamentoProtecao->rh257_sequencial                = null;
                $clRhLocalTrabEquipamentoProtecao->rh257_rhlocaltrabagentesnocivos = $clRhLocalTrabAgentesNocivos->rh256_sequencial;
                $clRhLocalTrabEquipamentoProtecao->rh257_utilizaepc                = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_utilizaepc;
                $clRhLocalTrabEquipamentoProtecao->rh257_eficaciaepc               = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_eficaciaepc;
                $clRhLocalTrabEquipamentoProtecao->rh257_utilizaepi                = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_utilizaepi;
                $clRhLocalTrabEquipamentoProtecao->rh257_eficaciaepi               = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_eficaciaepi;
                $clRhLocalTrabEquipamentoProtecao->rh257_medidaprotecaoepi         = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_medidaprotecaoepi;
                $clRhLocalTrabEquipamentoProtecao->rh257_funcionamentoepi          = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_funcionamentoepi;
                $clRhLocalTrabEquipamentoProtecao->rh257_usoininterruptoepi        = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_usoininterruptoepi;
                $clRhLocalTrabEquipamentoProtecao->rh257_validadeepi               = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_validadeepi;
                $clRhLocalTrabEquipamentoProtecao->rh257_periodicidadeepi          = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_periodicidadeepi;
                $clRhLocalTrabEquipamentoProtecao->rh257_higienizacaoepi           = $oAgenteNocivo->oDadosEquipamentosProtecao->rh257_higienizacaoepi;
                $clRhLocalTrabEquipamentoProtecao->incluir(null);
                if ($clRhLocalTrabEquipamentoProtecao->erro_status == 0)
                {
                    $sMsgErro  = "Erro realizando inclusao das informações de EPC e EPI.\n\n";
                    $sMsgErro .= $clRhLocalTrabEquipamentoProtecao->erro_msg."\n\n";
                    $sMsgErro .= pg_last_error();
                    throw new Exception($sMsgErro);
                }
                
                foreach ($oAgenteNocivo->oDadosEquipamentosProtecao->aDadosEPIs as $oEPI)
                {
                       $clRhLocalTrabEquipamentoProtecaoEPI = new cl_rhlocaltrabequipamentoprotecaoepi();
                       $clRhLocalTrabEquipamentoProtecaoEPI->rh259_sequencial                     = null;
                       $clRhLocalTrabEquipamentoProtecaoEPI->rh259_rhlocaltrabequipamentoprotecao = $clRhLocalTrabEquipamentoProtecao->rh257_sequencial;
                       $clRhLocalTrabEquipamentoProtecaoEPI->rh259_documentoavaliacao             = utf8_decode($oEPI->rh259_documentoavaliacao);
                       $clRhLocalTrabEquipamentoProtecaoEPI->rh259_descricao                      = utf8_decode($oEPI->rh259_descricao);
                       $clRhLocalTrabEquipamentoProtecaoEPI->incluir(null);
                       if ($clRhLocalTrabEquipamentoProtecaoEPI->erro_status == 0)
                       {
                           $sMsgErro  = "Erro realizando inclusao do EPI ligado aos equipamentos do local de trabalho.\n\n";
                           $sMsgErro .= $clRhLocalTrabEquipamentoProtecaoEPI->erro_msg."\n\n";
                           $sMsgErro .= pg_last_error();
                           throw new Exception($sMsgErro);
                       }
                }
                
            }
            
            $clRhLocalTrabRegistroAmbiental = new cl_rhlocaltrabregistroambiental();
            $sWhereExclusao = "rh258_rhlocaltrab = {$clRhLocalTrab->rh55_codigo} and rh258_instituicao = {$iInstituicao}";
            $clRhLocalTrabRegistroAmbiental->excluir(null,$sWhereExclusao);
            if ($clRhLocalTrabRegistroAmbiental->erro_status == 0)
            {
                $sMsgErro  = "Erro realizando exclusão dos agentes nocivos ligados ao local de trabalho.\n\n";
                $sMsgErro .= $clRhLocalTrabRegistroAmbiental->erro_msg."\n\n";
                $sMsgErro .= pg_last_error();
                throw new Exception($sMsgErro);
            }
            
            foreach ($aDadosResponsaveisRegistrosAmbientais as $oResponsavelRegistroAmbiental) 
            {
             
                $clRhLocalTrabRegistroAmbiental = new cl_rhlocaltrabregistroambiental();
                $clRhLocalTrabRegistroAmbiental->rh258_sequencial           = null;          
                $clRhLocalTrabRegistroAmbiental->rh258_rhlocaltrab          = $clRhLocalTrab->rh55_codigo;         
                $clRhLocalTrabRegistroAmbiental->rh258_instituicao          = $iInstituicao;         
                $clRhLocalTrabRegistroAmbiental->rh258_cpfresponsavel       = $oResponsavelRegistroAmbiental->rh258_cpfresponsavel;      
                $clRhLocalTrabRegistroAmbiental->rh258_identificacaoorgao   = $oResponsavelRegistroAmbiental->rh258_identificacaoorgao;  
                $clRhLocalTrabRegistroAmbiental->rh258_numeroinscricaoorgao = $oResponsavelRegistroAmbiental->rh258_numeroinscricaoorgao; 
                $clRhLocalTrabRegistroAmbiental->rh258_descricaoorgao       = utf8_decode($oResponsavelRegistroAmbiental->rh258_descricaoorgao);      
                $clRhLocalTrabRegistroAmbiental->rh258_uforgao              = $oResponsavelRegistroAmbiental->rh258_uforgao;             
                $clRhLocalTrabRegistroAmbiental->rh258_periodoinicial       = $oResponsavelRegistroAmbiental->rh258_periodoinicial;      
                $clRhLocalTrabRegistroAmbiental->rh258_periodofinal         = $oResponsavelRegistroAmbiental->rh258_periodofinal;        
                $clRhLocalTrabRegistroAmbiental->incluir(null);
                if ($clRhLocalTrabRegistroAmbiental->erro_status == 0)
                {
                    $sMsgErro  = "Erro realizando inclusao do responsável pelo registro ambiental ao local de trabalho.\n\n";
                    $sMsgErro .= $clRhLocalTrabRegistroAmbiental->erro_msg."\n\n";
                    $sMsgErro .= pg_last_error();
                    throw new Exception($sMsgErro);
                }
                    
            }
            
            $retorno->mensagem = "Operaçao realizada com sucesso";
            
        break;
        case 'excluirLocalTrabalho':
        
            $clRhpesLocalCusto = new cl_rhlocaltrabcustoplano();
            $sWhereExclusao = "rh86_instit = {$iInstituicao} and rh86_rhlocaltrab = $parametros->rh55_codigo";
            $clRhpesLocalCusto->excluir(null, $sWhereExclusao);
            if ($clRhpesLocalCusto->erro_status == 0) {
                throw new Exception($clRhpesLocalCusto->erro_msg);
            }
            
            $clRhLocalTrabRegistroAmbiental = new cl_rhlocaltrabregistroambiental();
            $sWhereExclusao = "rh258_rhlocaltrab = {$parametros->rh55_codigo} and rh258_instituicao = {$iInstituicao}";
            $clRhLocalTrabRegistroAmbiental->excluir(null,$sWhereExclusao);
            if ($clRhLocalTrabRegistroAmbiental->erro_status == 0)
            {
                $sMsgErro  = "Erro realizando exclusão dos registros ambientais ligados ao local de trabalho.\n\n";
                $sMsgErro .= $clRhLocalTrabRegistroAmbiental->erro_msg."\n\n";
                $sMsgErro .= pg_last_error();
                throw new Exception($sMsgErro);
            }

            $clRhLocalTrabEquipamentoProtecao = new cl_rhlocaltrabequipamentoprotecao();
            $sCampoLista = "array_to_string(array_accum(rhlocaltrabequipamentoprotecao.rh257_sequencial), ',') as lista";
            $sWhere  = "rhlocaltrabagentesnocivos.rh256_rhlocaltrab = {$parametros->rh55_codigo} ";
            $sWhere .= " and rhlocaltrabagentesnocivos.rh256_instituicao = {$iInstituicao} ";
            $sSqlEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->sql_query(null,$sCampoLista,null,$sWhere);
            $rsEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->sql_record($sSqlEquipamentoProtecao);
            $iQtdRegistrosEquipamentoProtecao = $clRhLocalTrabEquipamentoProtecao->numrows;
            if ($iQtdRegistrosEquipamentoProtecao > 0) {
                
                $sListaEquipamentosProtecao = db_utils::fieldsMemory($rsEquipamentoProtecao, 0)->lista;
                if (!empty($sListaEquipamentosProtecao)) {
                    $clRhLocalTrabEquipamentoProtecaoEPI = new cl_rhlocaltrabequipamentoprotecaoepi();
                    $sWhereExclusao = "rh259_rhlocaltrabequipamentoprotecao in ({$sListaEquipamentosProtecao})";
                    $clRhLocalTrabEquipamentoProtecaoEPI->excluir(null,$sWhereExclusao);
                    if ($clRhLocalTrabEquipamentoProtecaoEPI->erro_status == 0)
                    {
                        $sMsgErro  = "Erro realizando exclusão dos EPIs ligados aos equipamentos do local de trabalho.\n\n";
                        $sMsgErro .= $clRhLocalTrabAgentesNocivos->erro_msg."\n\n";
                        $sMsgErro .= pg_last_error();
                        throw new Exception($sMsgErro);
                    }
                    
                    $clRhLocalTrabEquipamentoProtecao = new cl_rhlocaltrabequipamentoprotecao();
                    $sWhereExclusao = "rh257_sequencial in ({$sListaEquipamentosProtecao})";
                    $clRhLocalTrabEquipamentoProtecao->excluir(null,$sWhereExclusao);
                    if ($clRhLocalTrabEquipamentoProtecao->erro_status == 0)
                    {
                        $sMsgErro  = "Erro realizando exclusão das informações de EPC e EPI.\n\n";
                        $sMsgErro .= $clRhLocalTrabEquipamentoProtecao->erro_msg."\n\n";
                        $sMsgErro .= pg_last_error();
                        throw new Exception($sMsgErro);
                    }
                }
                
            }
            
            $clRhLocalTrabAgentesNocivos = new cl_rhlocaltrabagentesnocivos();
            $sWhereExclusao = "rh256_rhlocaltrab = {$parametros->rh55_codigo} and rh256_instituicao = {$iInstituicao}";
            $clRhLocalTrabAgentesNocivos->excluir(null,$sWhereExclusao);
            if ($clRhLocalTrabAgentesNocivos->erro_status == 0)
            {
                $sMsgErro  = "Erro realizando exclusão dos agentes nocivos ligados ao local de trabalho.\n\n";
                $sMsgErro .= $clRhLocalTrabAgentesNocivos->erro_msg."\n\n";
                $sMsgErro .= pg_last_error();
                throw new Exception($sMsgErro);
            }
            
            
            $clRhLocalTrab = new cl_rhlocaltrab;
            $clRhLocalTrab->excluir($parametros->rh55_codigo,$iInstituicao);
            if ($clRhLocalTrab->erro_status == 0)  {
                throw new Exception($clRhLocalTrab->erro_msg);
            }
            
            $retorno->mensagem = "Operaçao realizada com sucesso";
            
        break;
    }
    
} catch (Exception $erro) {
    $retorno->mensagem = $erro->getMessage();
    $retorno->erro = true;
}

db_fim_transacao($retorno->erro);
echo JSON::create()->stringify($retorno);
