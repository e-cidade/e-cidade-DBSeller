<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
use App\Domain\Financeiro\Contabilidade\Factories\AnexosRREOFactory;
use App\Domain\Financeiro\Contabilidade\Factories\AnexosRGFFactory;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/JSON.php"));
require_once(modification("std/db_stdClass.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));

$oJson = new services_json();
$oParam = $oJson->decode(str_replace("\\", "", $_POST["json"]));

$oRetorno  = new stdClass();
$oRetorno->status = 1;
$oRetorno->msg = "";
$oRetorno->lista = array();

$sDiretorioSIAI = "model/contabilidade/arquivos/siai/";

try {
    switch ($oParam->exec) {
        case "getDadosAnexo":
            $anexoNomeEmArray = explode("-", $oParam->anexo);
            $isRREO = (end($anexoNomeEmArray) == "rreo")?true:false;
            $anexo = preg_replace("/[^0-9]/", "", $oParam->anexo);
            try {
                if ($isRREO) {
                    $dados = AnexosRREOFactory::getPrograma($anexo, db_getsession('DB_anousu'));
                } else {
                    $dados = AnexosRGFFactory::getPrograma($anexo, db_getsession('DB_anousu'));
                }
                $oRetorno->anexo = $oParam->anexo;
                $oRetorno->codigo_relatorio = $dados['relatorio'];
            } catch (Exception $e) {
                $oRetorno->anexo = $oParam->anexo;
                $oRetorno->codigo_relatorio = $oParam->anexo;
            }
         
            break;

        case "getDadosTCE":
            $iAnoUsu        = db_getsession("DB_anousu");
            $codigoOrgao    = $oParam->orgao;
            $codigoUnidade  = $oParam->unidade;
     
            if ($codigoOrgao == 0) {
                $oRetorno->codigoOrgao = "P088";
                $oRetorno->codigoOrgaoXml = "422";
                $oRetorno->nomeUnidade = "PREFEITURA MUNICIPAL DO NATAL";
            } else {
                $clOrcUnidade = new cl_orcunidade();
                $campos = "o41_orgao, o41_unidade, o41_codtri, o41_codigotribunalxml,";
                $campos .= "coalesce(o41_nometribunal, nomeinst) as o41_descr";
                $sSqlUnidade = $clOrcUnidade->sql_query(
                    null,
                    null,
                    null,
                    $campos,
                    null,
                    "(o41_orgao = {$codigoOrgao} and o41_unidade = {$codigoUnidade} and o41_anousu  = {$iAnoUsu})"
                );
                $rsUnidade = $clOrcUnidade->sql_record($sSqlUnidade);
                if ($clOrcUnidade->numrows == 0) {
                    throw new Exception("Não foram encontrados dados para Unidade");
                }
         
                $oUnidade  = db_utils::fieldsMemory($rsUnidade, 0);
         
                $oRetorno->codigoOrgao = $oUnidade->o41_codtri;
                $oRetorno->codigoOrgaoXml = $oUnidade->o41_codigotribunalxml;
                $oRetorno->nomeUnidade = $oUnidade->o41_descr;
            }
       
            break;
       
        case "importarArquivoDePara":
            switch ($oParam->tipoArquivo) {
                case "anexoI_elementos_receita":
                    $oParam->arquivo = $oJson->decode($oParam->arquivo);
                    /*
                     * Verificamos se o diretório de destino existe
                     */
                    $destino = "model/contabilidade/arquivos/siai/v{$oParam->exercicio}/";
                    if (!is_dir($destino)) {
                        throw new Exception("Diretório inválido");
                    }
                    $destino .= "arquivoDeParaElementosReceita.txt";
                    
                    
                    /*
                     * Validações no arquivo
                     */
                    if (strtolower($oParam->arquivo->extension) != "txt") {
                        throw new Exception("Extensão do arquivo inválida. Deve ser .txt");
                    }
                    
                    $linha = 0;
                    $leituraArquivo = file($oParam->arquivo->path);
                    foreach ($leituraArquivo as $dadosTXT) {
                        $linha++;
                        $camposLinha = explode("=", $dadosTXT);
                        if (count($camposLinha) != 2) {
                            $msg = "Qtd de colunas da linha do arquivo inválida.\n\n";
                            $msg .= "O arquivo deve ser informado da seguinte forma: ";
                            $msg .= "Elemento de origem=Elemento de destino:\n";
                            $msg .= "Ex.: 88888888=99999999\n";
                            $msg .= "Linha com o erro: {$linha}";
                            throw new Exception($msg);
                        }
                        
                        if (empty($camposLinha[0]) || empty($camposLinha[1])) {
                            $msg = "Encontrada linha com informação inválida\n";
                            $msg .= "Elemento de origem ou de destino não informado\n";
                            $msg .= "O arquivo deve ser informado da seguinte forma: ";
                            $msg .= "Elemento de origem=Elemento de destino:\n";
                            $msg .= "Ex.: 88888888=99999999\n";
                            $msg .= "Linha com o erro: {$linha}";
                            throw new Exception($msg);
                        }
                        
                        /*
                         * Verificar se a coluna é só numero
                         */
                        $campo1 = preg_replace('/[0-9]/', '', $camposLinha[0]);
                        $campo2 = preg_replace('/[0-9]/', '', $camposLinha[0]);
                        if ($campo1 != "" || $campo2 != "") {
                            $msg = "Encontrada linha com informação inválida\n";
                            $msg .= "Os elementos devem ser formados apenas por caracteres numéricos, sem espaços ou ";
                            $msg .= "outro tipo de caractere\n";
                            $msg .= "Elemento de origem=Elemento de destino:\n";
                            $msg .= "Ex.: 88888888=99999999\n";
                            $msg .= "Linha com o erro: {$linha}";
                            throw new Exception($msg);
                        }
                    }
                    
                    /*
                     * Movemos o arquivo
                     */
                    if (!rename($oParam->arquivo->path, $destino)) {
                        unset($destino);
                        throw new Exception("Erro ao executar mover arquivo!");
                    }
                                       
                    break;
                default:
                    throw new Exception("Tipo de arquivo não definido");
            }
            
            $oRetorno->msg = "Importação do arquivo realizada com sucesso";
            
            break;
     
        case "processarSiai":
            $iAnoUsu        = db_getsession("DB_anousu");
            $iInstituicao   = db_getsession("DB_instit");
         
            $oPeriodo       = new Periodo($oParam->periodo);
            $sMesFinal      = str_pad($oPeriodo->getMesFinal(), 2, "0", STR_PAD_LEFT);
            $sMesInicial    = $oPeriodo->getMesInicial();
            $sDataInicial   = $oPeriodo->getDataInicial($iAnoUsu);
            $sDataFinal     = $oPeriodo->getDataFinal($iAnoUsu);
            $sBimRef        = $iAnoUsu.str_pad($oPeriodo->getOrdem(), 2, "0", STR_PAD_LEFT);
            $sDataGeracao   = date('d/m/Y', time());
            $sHoraGeracao   = date('H:i:s', time());
            $otxtLogger     = fopen("tmp/SIAI.log", "w");
            $aListaArquivos = array();
         
            if ($iAnoUsu <= 2017) {
                $sVersaoSIAI = "v2017";
            } else {
                $iAnoVersaoSIAI = $iAnoUsu;
            
                $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                while (!is_dir($sDiretorioSIAI.$sVersaoSIAI)) {
                    $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                    $iAnoVersaoSIAI--;
                }
            }
       
            /*
             * Inicio da logica da geracao de arquivos de retificacao
             *
             * São informados os empenhos, liquidacoes ou pagamentos especificos para geracao do arquivo
             */
            $aSqlListaGeracao = array();
            if (!empty($oParam->listaEmpenho)) {
                require_once(modification("{$sDiretorioSIAI}{$sVersaoSIAI}/SiaiAnexo14XML.model.php"));
                $oSiaiXML = new SiaiAnexo14XML();
                $oSiaiXML->setCodigoOrgao($oParam->orgao);
                $oSiaiXML->setCodigoUnidade($oParam->unidade);
             
                $sListaEmpenhoOrgaoUnidadeWhere = $oSiaiXML->getDotacaoWhere();
             
                $sListaEmpenho = "'".str_replace(",", "','", str_replace(' ', '', $oParam->listaEmpenho))."'";
             
                $aSqlListaGeracao[] = "select empempenho.e60_anousu as ano, 
                                           extract(month from empempenho.e60_emiss) as mes, 
                                           array_to_string(array_accum(empempenho.e60_numemp), ',') as empempenho,
                                           '' as empnota, 
                                           '' as pagordem 
                                      from empempenho
                                           inner join orcdotacao  on orcdotacao.o58_coddot = empempenho.e60_coddot
                                                                 and orcdotacao.o58_anousu = empempenho.e60_anousu   
                                     where empempenho.e60_codemp||'/'||empempenho.e60_anousu in ({$sListaEmpenho})
                                       and {$sListaEmpenhoOrgaoUnidadeWhere}   
                                     group by empempenho.e60_anousu, extract(month from empempenho.e60_emiss)";
            }
         
            if (!empty($oParam->listaLiquidacao)) {
                $aSqlListaGeracao[] = "select extract(year from e69_dtinclusao) as ano, 
                                           extract(month from e69_dtinclusao) as mes,
                                           '' as empempenho, 
                                           array_to_string(array_accum(e69_codnota), ',') as empnota,
                                           '' as pagordem  
                                      from empnota 
                                           where e69_codnota in ({$oParam->listaLiquidacao})
                                     group by extract(year from e69_dtinclusao),extract(month from e69_dtinclusao)";
            }
         
            if (!empty($oParam->listaPagamento)) {
                $aSqlListaGeracao[] = "select extract(year from e50_data) as ano, 
                                           extract(month from e50_data) as mes,
                                           '' as empempenho,   
                                           '' as empnota,
                                           array_to_string(array_accum(e50_codord), ',') as pagordem 
                                      from pagordem 
                                     where e50_codord in ({$oParam->listaPagamento}) 
                                     group by extract(year from e50_data),extract(month from e50_data)";
            }
         
            if (count($aSqlListaGeracao) > 0) {
                $sSqlListaGeracao = "select * 
                                       from (".implode(" union ", $aSqlListaGeracao).") as dados 
                                      order by ano,mes";
                $rsListaGeracao = db_query($sSqlListaGeracao);
                if (pg_num_rows($rsListaGeracao) == 0) {
                    throw new Exception("Nenhuma informação encontrada para a(s) lista(s) informada(s)");
                }
             
         
                $aListaGeracao = array();
                $sHashListaGeracaoAnoMes   = null;
           
                for ($iInd = 0; $iInd < pg_num_rows($rsListaGeracao); $iInd++) {
                    $oDadosConsulta = db_utils::fieldsMemory($rsListaGeracao, $iInd);
               
                    if ($sHashListaGeracaoAnoMes != $oDadosConsulta->ano.$oDadosConsulta->mes) {
                        $oDadosGeracao = new stdClass();
                        $oDadosGeracao->ano        = $oDadosConsulta->ano;
                        $oDadosGeracao->mes        = $oDadosConsulta->mes;
                 
                        $oDadosGeracao->empempenho = $oDadosConsulta->empempenho;
                        $oDadosGeracao->empnota    = $oDadosConsulta->empnota;
                        $oDadosGeracao->pagordem   = $oDadosConsulta->pagordem;
                 
                        $sHashListaGeracaoAnoMes = $oDadosConsulta->ano.$oDadosConsulta->mes;
                        $aListaGeracao[] = $oDadosGeracao;
                    } else {
                        $oDadosGeracao->empempenho .= $oDadosConsulta->empempenho;
                        $oDadosGeracao->empnota    .= $oDadosConsulta->empnota;
                        $oDadosGeracao->pagordem   .= $oDadosConsulta->pagordem;
                    }
                }
           
                foreach ($aListaGeracao as $oDadosListaGeracao) {
                    $sDiretorioArquivoProcessamento = "{$sDiretorioSIAI}{$sVersaoSIAI}/SiaiAnexo14XML.model.php";
                    if (file_exists($sDiretorioArquivoProcessamento)) {
                        require_once(modification($sDiretorioArquivoProcessamento));
                        $oArquivo    = new SiaiAnexo14XML();
                 
                        $otxtLogger     = fopen("tmp/SIAI.log", "w");
                 
                        $oArquivo->setMes(str_pad($oDadosListaGeracao->mes, 2, "0", STR_PAD_LEFT));
                        $oArquivo->setAno($oDadosListaGeracao->ano);
                        $oArquivo->setCodigoOrgaoTCEXml($oParam->codigoOrgaoTCEXml);
                        $oArquivo->setCodigoOrgao($oParam->orgao);
                        $oArquivo->setCodigoUnidade($oParam->unidade);
                        $oArquivo->setTXTLogger($otxtLogger);
                 
                        $oArquivo->sListaEmpenho    = $oDadosListaGeracao->empempenho;
                        $oArquivo->sListaLiquidacao = $oDadosListaGeracao->empnota;
                        $oArquivo->sListaPagamento  = $oDadosListaGeracao->pagordem;
                 
                        $oArquivo->gerarDados();
                 
                        $oArquivoXML = new stdClass();
                        $oArquivoXML->nome = $oArquivo->getNomeArquivo();
                        $oArquivoXML->caminho = "tmp/{$oArquivo->getNomeArquivo()}";
                 
                        $aListaArquivos[] = $oArquivoXML;
                    } else {
                        throw new Exception("Arquivo responsavel pelo processamento da geração não encontrado!");
                    }
                }
            }
            /*
             * Fim da geracao dos arquivos de retificacao
             *
             */
               
         
            /*
             * Inicio da geracao dos arquivos normais
             *
             */
            if (count($oParam->aArquivos) > 0) {
                foreach ($oParam->aArquivos as $sArquivo) {
     
                  /**
                   * Verifica dinamicamente se a classe existe, se Existe cria uma instancia da classe
                   */
                    $sDiretorioArquivoProcessamento = "{$sDiretorioSIAI}{$sVersaoSIAI}/Siai{$sArquivo}.model.php";
                    if (file_exists($sDiretorioArquivoProcessamento)) {
                        require_once(modification($sDiretorioArquivoProcessamento));
                        $sNomeClasse = "Siai{$sArquivo}";
                        $oArquivo    = new $sNomeClasse;
     
                        $oArquivo->setDataInicial($sDataInicial);
                        $oArquivo->setDataFinal($sDataFinal);
                        $oArquivo->setMes($sMesFinal);
                        $oArquivo->setDataGeracao($sDataGeracao);
                        $oArquivo->setHoraGeracao($sHoraGeracao);
                        $oArquivo->setCodigoOrgaoTCE($oParam->codigoOrgaoTCE);
                        if (isset($oParam->codigoOrgaoTCEXml)) {
                            $oArquivo->setCodigoOrgaoTCEXml($oParam->codigoOrgaoTCEXml);
                        }
                        $oArquivo->setNomeUnidade($oParam->nomeUnidadeTCE);
                        $oArquivo->setAno($iAnoUsu);
                        $oArquivo->setCodigoOrgao($oParam->orgao);
                        $oArquivo->setCodigoUnidade($oParam->unidade);
                        $oArquivo->setTXTLogger($otxtLogger);
                        $oArquivo->setBimReferencia($sBimRef);
                        $oArquivo->gerarDados();
               
                        $oArquivoXML = new stdClass();
                        $oArquivoXML->nome = $oArquivo->getNomeArquivo();
                        $oArquivoXML->caminho = "tmp/{$oArquivo->getNomeArquivo()}";
               
                        $aListaArquivos[] = $oArquivoXML;
                    } else {
                        throw new Exception("Arquivo Siai{$sArquivo}.model.php não encontrado!");
                    }
                }
            }
             /*
              * Fim da geracao
              */
          
             $oRetorno->lista = $aListaArquivos;
     
            break;
       
        case "processarSiaiLOA":
            $iAnoUsu        = db_getsession("DB_anousu");
            $iInstituicao   = db_getsession("DB_instit");
         
            $oPeriodo       = new Periodo($oParam->periodo);
            $sMesFinal      = str_pad($oPeriodo->getMesFinal(), 2, "0", STR_PAD_LEFT);
            $sMesInicial    = $oPeriodo->getMesInicial();
            $sDataInicial   = $oPeriodo->getDataInicial($iAnoUsu);
            $sDataFinal     = $oPeriodo->getDataFinal($iAnoUsu);
            $sBimRef        = $iAnoUsu.str_pad($oPeriodo->getOrdem(), 2, "0", STR_PAD_LEFT);
        
            if ($iAnoUsu <= 2018) {
                $sVersaoSIAI = "v2018";
            } else {
                $iAnoVersaoSIAI = $iAnoUsu;
            
                $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                while (!is_dir($sDiretorioSIAI.$sVersaoSIAI)) {
                    $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                    $iAnoVersaoSIAI--;
                }
            }
        
            require_once(modification("model/contabilidade/arquivos/siai/{$sVersaoSIAI}/SiaiArquivoLOAXML.model.php"));
            $oSIAI = new SiaiArquivoLOAXML();
            $oSIAI->setDataInicial($sDataInicial);
            $oSIAI->setDataFinal($sDataFinal);
            $oSIAI->setAno($iAnoUsu);
            $oSIAI->setDataGeracao(date('d/m/Y', time()));
            $oSIAI->setHoraGeracao(date('H:i:s', time()));
            $oSIAI->setCodigoOrgaoTCE($oParam->codigoOrgaoTCE);
            $oSIAI->setNomeUnidade($oParam->nomeUnidadeTCE);
            $oSIAI->setCodigoOrgao($oParam->orgao);
            $oSIAI->setCodigoUnidade($oParam->unidade);
            $oSIAI->setTXTLogger(fopen("tmp/SIAI_LOA.log", "w"));
            $oSIAI->setBimReferencia($sBimRef);
            $oSIAI->processar();
         
            file_put_contents("tmp/SIAI_LOA.xml", $oSIAI->getArquivo());
            $oFile = new File('tmp/SIAI_LOA.xml');
         

            $oRetorno->sCaminhoArquivo = $oFile->getFilePath();
            $oRetorno->sNomeArquivo    = $oFile->getBaseName();
            $oRetorno->sMensagem       = 'Arquivos gerados com sucesso!';
  
            break;
    
        case "processarSiaiContasAnuais":
               $iInstituicao   = db_getsession("DB_instit");
               $iAno           = db_getsession("DB_anousu");
               $sDataGeracao   = date('d/m/Y', time());
               $sHoraGeracao   = date('H:i:s', time());
               $otxtLogger     = fopen("tmp/SIAI_contas_anuais.log", "w+");
               $codigoOrgaoTCE = 422;
               $codigoOrgao    = $oParam->orgao;
               $codigoUnidade  = $oParam->unidade;
            
            if ($iAnoUsu <= 2018) {
                $sVersaoSIAI = "v2018";
            } else {
                $iAnoVersaoSIAI = $iAnoUsu;
                
                $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                while (!is_dir($sDiretorioSIAI.$sVersaoSIAI)) {
                         $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                         $iAnoVersaoSIAI--;
                }
            }
     
            $model = "model/contabilidade/arquivos/siai/v{$sVersaoSIAI}/SiaiArquivoContasAnuaisXML.model.php";
            require_once(modification($model));
            $oSIAI    = new SiaiArquivoContasAnuaisXML();
     
            $oSIAI->setAno($iAno);
            $oSIAI->setDataInicial("{$iAno}-01-01");
            $oSIAI->setDataFinal("{$iAno}-12-31");
            $oSIAI->setDataGeracao($sDataGeracao);
            $oSIAI->setHoraGeracao($sHoraGeracao);
              
            $oSIAI->setCodigoOrgao($codigoOrgao);
            $oSIAI->setCodigoUnidade($codigoUnidade);
            $oSIAI->setCodigoOrgaoTCE($codigoOrgaoTCE);
     
            $oSIAI->setTXTLogger($otxtLogger);
     
            $oSIAI->setGeraAnexo01($oParam->lGeraAnexo01);
            $oSIAI->setGeraAnexo02($oParam->lGeraAnexo02);
            $oSIAI->setGeraAnexo06($oParam->lGeraAnexo06);
            $oSIAI->setGeraAnexo07($oParam->lGeraAnexo07);
            $oSIAI->setGeraAnexo08($oParam->lGeraAnexo08);
            $oSIAI->setGeraAnexo09($oParam->lGeraAnexo09);
            $oSIAI->setGeraAnexo10($oParam->lGeraAnexo10);
            $oSIAI->setGeraAnexo11($oParam->lGeraAnexo11);
            $oSIAI->setGeraAnexo12($oParam->lGeraAnexo12);
            $oSIAI->setGeraQuadro01($oParam->lGeraQuadro01);
            $oSIAI->setGeraQuadro02($oParam->lGeraQuadro02);
     
            $oSIAI->processar();
     
            file_put_contents("tmp/SIAI_contas_anuais.xml", $oSIAI->getArquivo());
     
            fclose($otxtLogger);
     
            $oFile = new File('tmp/SIAI_contas_anuais.xml');
             
            $oRetorno->sCaminhoArquivo = $oFile->getFilePath();
            $oRetorno->sNomeArquivo    = $oFile->getBaseName();
            $oRetorno->sMensagem       = 'Arquivos gerados com sucesso!';
            
     
            break;
        
        case "processarGeracaoPPA":
            $iAnoUsu        = db_getsession("DB_anousu");
            $iInstituicao   = db_getsession("DB_instit");
         
            $sArquivoPPA = "tmp/ppa.xml";
            $sArquivoLog = "tmp/SIAI_PPA.log";
         
            if ($iAnoUsu <= 2022) {
                $sVersaoSIAI = "v2022";
            } else {
                $iAnoVersaoSIAI = $iAnoUsu;
             
                $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                while (!is_dir($sDiretorioSIAI.$sVersaoSIAI)) {
                    $sVersaoSIAI = "v".$iAnoVersaoSIAI;
                    $iAnoVersaoSIAI--;
                }
            }
         
            $model = "model/contabilidade/arquivos/siai/{$sVersaoSIAI}/SiaiArquivoPPAXML.model.php";
            require_once(modification($model));
            $oSIAI = new SiaiArquivoPPAXML();
            $oSIAI->setAno($iAnoUsu);
            $oSIAI->setDataGeracao(date('d/m/Y', time()));
            $oSIAI->setHoraGeracao(date('H:i:s', time()));
            $oSIAI->setCodigoOrgaoTCE($oParam->codigoOrgaoTCE);
            $oSIAI->setCodigoOrgaoTCEXml($oParam->codigoOrgaoTCEXml);
            $oSIAI->setNomeUnidade($oParam->nomeUnidadeTCE);
            $oSIAI->setCodigoOrgao($oParam->orgao);
            $oSIAI->setCodigoUnidade($oParam->unidade);
            $oSIAI->setTXTLogger(fopen($sArquivoLog, "w"));
            $oSIAI->processar();
         
            file_put_contents($sArquivoPPA, $oSIAI->getArquivo());
         
            $oRetorno->sCaminhoArquivo    = $sArquivoPPA;
            $oRetorno->sNomeArquivo       = "ppa.xml";
            $oRetorno->sCaminhoArquivoLog = $sArquivoLog;
            $oRetorno->sNomeArquivoLog    = "Log de processamento - Erros";
            $oRetorno->sMensagem          = 'Arquivos gerados com sucesso!';
         
            break;
    }
} catch (Exception $eErro) {
    $oRetorno->status = 0;
    $oRetorno->msg = $eErro->getMessage();
}

echo $oJson->encode($oRetorno);
