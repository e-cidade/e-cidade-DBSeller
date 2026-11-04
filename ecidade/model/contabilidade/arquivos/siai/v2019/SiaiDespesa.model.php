<?php
/*
 * E-cidade Software Publico para Gestao Municipal
 * Copyright (C) 2013 DBselller Servicos de Informatica
 * www.dbseller.com.br
 * e-cidade@dbseller.com.br
 *
 * Este programa e software livre; voce pode redistribui-lo e/ou
 * modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 * publicada pela Free Software Foundation; tanto a versao 2 da
 * Licenca como (a seu criterio) qualquer versao mais nova.
 *
 * Este programa e distribuido na expectativa de ser util, mas SEM
 * QUALQUER GARANTIA; sem mesmo a garantia implicita de
 * COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 * PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 * detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 * junto com este programa; se nao, escreva para a Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307, USA.
 *

 * Copia da licenca no diretorio licenca/licenca_en.txt
 * licenca/licenca_pt.txt
 */
require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));
require_once(modification("libs/db_liborcamento.php"));
class SiaiDespesa extends SiaiArquivoBase
{
    
    /**
     * Busca os dados para gerar o Arquivo da Despesa
     */
    public function gerarDados()
    {
        
        $lDebug = true;
        $sMsgLogErro = "";
    
        $nValordotinicial    = 0;
        $nValordotacumulada  = 0;
        $nValorempbimestre   = 0;
        $nValorempexercicio  = 0;
        $nValorliqbimestre   = 0;
        $nValorliqexercicio  = 0;
        $nValorpagoexercicio = 0;
        $nValorrpnp          = 0;
    
        $aCodigoDespesasDePara = array();
        $aCodigoDespesasDePara["31900900"] = "31909900";
        $aCodigoDespesasDePara["31701100"] = "31999900";
        $aCodigoDespesasDePara["31999901"] = "31999900";
        $aCodigoDespesasDePara["33209901"] = "33209900";
        $aCodigoDespesasDePara["33203900"] = "33209900";
        $aCodigoDespesasDePara["99999901"] = "99999999";
        $aCodigoDespesasDePara["99999901"] = "99999999";
    
        $iNumLinha = 1;
        $arqFinal = fopen("tmp/A1D_".$this->sBimReferencia.".TXT", 'w+');
        $arqPrograma = fopen("tmp/TESTE_DESPESA_PROGRAMA.TXT", 'w+');
        $arqProjAtiv = fopen("tmp/TESTE_DESPESA_PROJATIV.TXT", 'w+');
        $arqUnidade = fopen("tmp/TESTE_DESPESA_UNIDADE.TXT", 'w+');
        $arqDotacao = fopen("tmp/TESTE_DESPESA_DOTACAO.TXT", 'w+');
        
        $sHashPrograma = "";
        $sHashProgramaProjetoAtividade = "";
        $sHashUnidade = "";

        $this->setNomeArquivo("A1D_".$this->sBimReferencia.".TXT");
        
        /*
         * DADOS DO HEADER
         */
        $oDadosHeader = new stdClass();
        $oDadosHeader->tipregistro    = "0";
        $oDadosHeader->nomearquivo    = str_pad("A1D_".$this->sBimReferencia, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->bimreferencia  = $this->sBimReferencia;
        $oDadosHeader->tipoarquivo    = "O";
        $oDadosHeader->datageracaoarq = $this->dtDataGeracao;
        $oDadosHeader->horageracaoarq = $this->dtHoraGeracao;
        $oDadosHeader->codigoorgao    = $this->codigoOrgaoTCE;
        $oDadosHeader->nomeorgao      = str_pad($this->nomeUnidade, 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->brancos        = str_repeat(" ", 269);
        $oDadosHeader->numRegistro    = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
        $oDadosHeader->codigolinha    = 2000001;
        
        $this->aDados [] = $oDadosHeader;
        
        $sLinhaHeader = $oDadosHeader->tipregistro . $oDadosHeader->nomearquivo . $oDadosHeader->bimreferencia . $oDadosHeader->tipoarquivo . $oDadosHeader->datageracaoarq . $oDadosHeader->horageracaoarq . $oDadosHeader->codigoorgao . $oDadosHeader->nomeorgao . $oDadosHeader->brancos . $oDadosHeader->numRegistro;
        fputs($arqFinal, $sLinhaHeader . "\r\n");
        
        $oDaoOrcPrograma = db_utils::getDao("orcprograma");
        $oDaoOrcProjAtiv = db_utils::getDao("orcprojativ");
        
        $sListaInstit = db_getsession("DB_instit");
        
        $sWhereOrgaoUnidade = "";
        if ($this->codigoOrgaoTCE != "P088") {
            $sWhereOrgaoUnidade = " o58_orgao = {$this->getCodigoOrgao()} and o58_unidade = {$this->getCodigoUnidade()}";
        }

        if ($this->codigoOrgao == "29" && $this->codigoUnidade == "1") {
            $sWhereOrgaoUnidade = "((o58_orgao = 29 and o58_unidade = 1) 
			                     or (o58_orgao = 29 and o58_unidade = 46) 
			                     or (o58_orgao = 29 and o58_unidade = 47))";
        }

        if ($this->codigoOrgao == "20" && $this->codigoUnidade == "1") {
            $sWhereOrgaoUnidade = "((o58_orgao = 20 and o58_unidade = 1) 
			                     or (o58_orgao = 20 and o58_unidade = 49))";
        }

        if ($this->codigoOrgao == "34" && $this->codigoUnidade == "1") {
            $sWhereOrgaoUnidade = "((o58_orgao = 34 and o58_unidade = 1) 
			                     or (o58_orgao = 34 and o58_unidade = 49))";
        }

        if ($this->codigoOrgao == "18" && $this->codigoUnidade == "1") {
            $sWhereOrgaoUnidade = "((o58_orgao = 18 and o58_unidade = 45) 
			                     or (o58_orgao = 18 and o58_unidade = 46) 
			                     or (o58_orgao = 18 and o58_unidade = 47) 
			                     or (o58_orgao = 18 and o58_unidade = 48) 
			                     or (o58_orgao = 18 and o58_unidade = 49) 
			                     or (o58_orgao = 18 and o58_unidade = 1))";
        }

        
        db_query("begin");
        $rsDotacaoSaldo = db_dotacaosaldo(8, 1, 4, true, $sWhereOrgaoUnidade, $this->iAno, $this->dtDataInicial, $this->dtDataFinal);
        db_query("rollback");

        $nValorDotacaoInicial = 0;
        
        $aDadosDespesas = array();
        $iLinhasDotacaoSaldo = pg_num_rows($rsDotacaoSaldo);
        if ($iLinhasDotacaoSaldo > 0) {
            
            /**
             * **********************************************************************************************
             * DETALHE 1
             * Programas do PPA
             */
            for ($iInd = 0; $iInd < $iLinhasDotacaoSaldo; $iInd ++) {
                $oDespesa = db_utils::fieldsMemory($rsDotacaoSaldo, $iInd);
                
                /*
                 * Apenas serao mostradas as despesas ligadas a dotacao
                 */
                if ($oDespesa->o58_codigo == 0) {
                    continue;
                }
                
                $sSqlFonte = "SELECT o15_codtri 
					            FROM orctiporec 
					           WHERE o15_codigo = $oDespesa->o58_codigo";
                $sFonte    = db_utils::fieldsMemory(db_query($sSqlFonte), 0)->o15_codtri;

                $sIdPrograma = $oDespesa->o58_programa;
                if ($sHashPrograma != $sIdPrograma) {
                    $iNumLinha++;
                    $oDadosDetalhe1 = new stdClass();
                    $oDadosDetalhe1->tipregistro = "1";
                    $oDadosDetalhe1->numerodoprograma  = str_pad(substr($oDespesa->o58_programa, 0, 5), 5, "0", STR_PAD_LEFT);
                    $oDadosDetalhe1->descricaoprograma = str_pad(substr(DBString::removerCaracteresEspeciais($oDespesa->o54_descr), 0, 255), 255, " ");
                    $oDadosDetalhe1->brancos     = str_repeat(" ", 148);
                    $oDadosDetalhe1->numRegistro = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                    $oDadosDetalhe1->codigolinha = 2000002;
                    
                    $this->aDados [] = $oDadosDetalhe1;
                    
                    $sLinhaPrograma = $oDadosDetalhe1->tipregistro . " | " . $oDadosDetalhe1->numerodoprograma . " | " . $oDadosDetalhe1->descricaoprograma . " | " . $oDadosDetalhe1->brancos . " | " . $oDadosDetalhe1->numRegistro;
                    fputs($arqPrograma, $sLinhaPrograma . "\r\n");
                        
                    fputs($arqFinal, str_replace(" | ", "", $sLinhaPrograma) . "\r\n");
                }
                $sHashPrograma = $sIdPrograma;
                
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "33913900" ? "333903900" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "33913999" ? "333903900" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "33303900" ? "333903900" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "31919700" ? "331919900" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "33313900" ? "333903900" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "33503900" ? "333903900" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "33603900" ? "333903900" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "44104100" ? "344204100" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "44505100" ? "344805100" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "31903400" ? "331900400" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "31904600" ? "331901600" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "44209900" ? "344903500" : $oDespesa->o58_elemento;
                $oDespesa->o58_elemento = substr($oDespesa->o58_elemento, 1, 8) == "99999900" ? "399999901" : $oDespesa->o58_elemento;

                /*
                 * Montamos o Objeto de Vetor com os dados das dotacoes
                 */
                $iIdDespesa  = str_pad(substr($oDespesa->o58_elemento, 1, 8), 8, "0", STR_PAD_LEFT);
                $iIdDespesa .= ".".str_pad(substr($sFonte, 0, 10), 10, "0", STR_PAD_LEFT);
                $iIdDespesa .= ".".str_pad($oDespesa->o58_orgao, 2, "0", STR_PAD_LEFT) . str_pad($oDespesa->o58_unidade, 2, "0", STR_PAD_LEFT);
                $iIdDespesa .= ".".str_pad(substr(str_pad($oDespesa->o58_funcao, 2, 0, STR_PAD_LEFT) . str_pad($oDespesa->o58_subfuncao, 3, 0, STR_PAD_LEFT), 0, 5), 5, "0", STR_PAD_LEFT);
                $iIdDespesa .= ".".str_pad(substr($oDespesa->o58_programa, 0, 4), 4, "0", STR_PAD_LEFT);
                $iIdDespesa .= ".".str_pad(substr($oDespesa->o58_projativ, 0, 4), 4, "0", STR_PAD_LEFT);
                
                $nValorRPNP = 0;
                $sSqlValorRPNP = "select round((e91_vlremp - e91_vlranu - e91_vlrliq),2) as rp_nao_processado
			                        from empresto
			                             inner join empempenho  on e60_numemp = e91_numemp
				  	                     inner join orcdotacao  on o58_coddot = e60_coddot 
						                                       and o58_anousu = e60_anousu
				  	                     inner join orcelemento on o56_codele = o58_codele 
						                                       and o56_anousu = o58_anousu
                                    where e91_anousu    = ".db_getsession("DB_anousu")."
				  					  and o56_elemento  = '".$oDespesa->o58_elemento."'
				  					  and o58_codigo    = ".$oDespesa->o58_codigo."
				  					  and o58_funcao    = ".$oDespesa->o58_funcao."
				  					  and o58_subfuncao = ".$oDespesa->o58_subfuncao."
				  					  and o58_programa  = ".$oDespesa->o58_programa."
				  					  and o58_projativ  = ".$oDespesa->o58_projativ."
                                      and {$sWhereOrgaoUnidade} ";
                $rsValorRPNP = db_query($sSqlValorRPNP);
                if (pg_num_rows($rsValorRPNP) > 0) {
                    $nValorRPNP = db_utils::fieldsMemory($rsValorRPNP, 0)->rp_nao_processado;
                }
                if (array_key_exists($iIdDespesa, $aDadosDespesas)) {
                    $aDadosDespesas[$iIdDespesa]["valordotinicial"]   += $oDespesa->dot_ini;
                    $aDadosDespesas[$iIdDespesa]["valordotacumulada"] += $oDespesa->dot_ini + $oDespesa->suplementado_acumulado - $oDespesa->reduzido_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorempbimestre"]  += $oDespesa->empenhado - $oDespesa->anulado;
                    $aDadosDespesas[$iIdDespesa]["valorempexercicio"] += $oDespesa->empenhado_acumulado - $oDespesa->anulado_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorliqbimestre"]  += $oDespesa->liquidado;
                    $aDadosDespesas[$iIdDespesa]["valorliqexercicio"] += $oDespesa->liquidado_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorpagoexercicio"]+= $oDespesa->pago_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorrpnp"]         += $nValorRPNP;
                } else {
                    $sCodigoDespesa = str_pad(substr($oDespesa->o58_elemento, 1, 8), 8, "0", STR_PAD_LEFT);
                    if (array_key_exists(substr($oDespesa->o58_elemento, 1, 8), $aCodigoDespesasDePara)) {
                        $sCodigoDespesa = $aCodigoDespesasDePara[substr($oDespesa->o58_elemento, 1, 8)];
                    }
                    
                    $aDadosDespesas[$iIdDespesa]["codigodespesa"]      = $sCodigoDespesa;
                    $aDadosDespesas[$iIdDespesa]["fonterecurso"]       = str_pad(substr($sFonte, 0, 10), 10, "0", STR_PAD_LEFT);
                    $aDadosDespesas[$iIdDespesa]["classinstitucional"] = str_pad($oDespesa->o58_orgao, 2, "0", STR_PAD_LEFT);
                    if ($oDespesa->o58_orgao == "24" && $oDespesa->o58_unidade == "20") {
                        $aDadosDespesas[$iIdDespesa]["classinstitucional"] .= "220";
                    } else {
                        $aDadosDespesas[$iIdDespesa]["classinstitucional"] .= str_pad($oDespesa->o58_unidade, 2, "0", STR_PAD_LEFT);
                    }
                    $aDadosDespesas[$iIdDespesa]["classfuncional"]     = str_pad(substr(str_pad($oDespesa->o58_funcao, 2, 0, STR_PAD_LEFT) . str_pad($oDespesa->o58_subfuncao, 3, 0, STR_PAD_LEFT), 0, 5), 5, "0", STR_PAD_LEFT);
                    $aDadosDespesas[$iIdDespesa]["classprograma"]      = str_pad(substr($oDespesa->o58_programa, 0, 4), 4, "0", STR_PAD_LEFT);
                    $aDadosDespesas[$iIdDespesa]["classprojeto"]       = str_pad(substr($oDespesa->o58_projativ, 0, 4), 4, "0", STR_PAD_LEFT);
                  
                    $aDadosDespesas[$iIdDespesa]["valordotinicial"]    = $oDespesa->dot_ini;
                    $aDadosDespesas[$iIdDespesa]["valordotacumulada"]  = $oDespesa->dot_ini + $oDespesa->suplementado_acumulado - $oDespesa->reduzido_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorempbimestre"]   = $oDespesa->empenhado - $oDespesa->anulado;
                    $aDadosDespesas[$iIdDespesa]["valorempexercicio"]  = $oDespesa->empenhado_acumulado - $oDespesa->anulado_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorliqbimestre"]   = $oDespesa->liquidado;
                    $aDadosDespesas[$iIdDespesa]["valorliqexercicio"]  = $oDespesa->liquidado_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorpagoexercicio"] = $oDespesa->pago_acumulado;
                    $aDadosDespesas[$iIdDespesa]["valorrpnp"]          = $nValorRPNP;
                }
            }
            
            /**
             * **********************************************************************************************
             * DETALHE 2
             * Programa/ProjetoAtividade
             */
            for ($iInd = 0; $iInd < $iLinhasDotacaoSaldo; $iInd ++) {
                $oDespesa = db_utils::fieldsMemory($rsDotacaoSaldo, $iInd);
                
                /*
                 * Apenas serao mostradas as despesas ligadas a dotacao
                 */
                if ($oDespesa->o58_codigo == 0) {// || $oDespesa->o58_instit != db_getsession("DB_instit")) {
                    continue;
                }
                $sIdProgramaProjetoAtividade = "$oDespesa->o58_programa|$oDespesa->o58_projativ";
                if ($sHashProgramaProjetoAtividade != $sIdProgramaProjetoAtividade) {
                    $iNumLinha++;
                    $oDadosDetalhe2 = new stdClass();
                    $oDadosDetalhe2->tipregistro = "2";
                    $oDadosDetalhe2->numerodoprograma = str_pad(substr($oDespesa->o58_programa, 0, 5), 5, "0", STR_PAD_LEFT);
                    $oDadosDetalhe2->numeroprojetoatividade = str_pad(substr($oDespesa->o58_projativ, 0, 5), 5, "0", STR_PAD_LEFT);
                    $oDadosDetalhe2->descricaoprojetoatividade = str_pad(substr(DBString::removerCaracteresEspeciais($oDespesa->o55_descr), 0, 255), 255, " ", STR_PAD_RIGHT);
                    $oDadosDetalhe2->numRegistro = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                    $oDadosDetalhe2->brancos     = str_repeat(" ", 143);
                    $oDadosDetalhe2->codigolinha = 2000003;
                    
                    $this->aDados [] = $oDadosDetalhe2;
                    
                    $sLinhaProjetoAtividade = $oDadosDetalhe2->tipregistro . " | " . $oDadosDetalhe2->numerodoprograma . " | " . $oDadosDetalhe2->numeroprojetoatividade . " | " . $oDadosDetalhe2->descricaoprojetoatividade . " | " . $oDadosDetalhe2->brancos . " | " . $oDadosDetalhe2->numRegistro;
                    fputs($arqProjAtiv, $sLinhaProjetoAtividade . "\r\n");
                    fputs($arqFinal, str_replace(" | ", "", $sLinhaProjetoAtividade) . "\r\n");
                }
                $sHashProgramaProjetoAtividade = $sIdProgramaProjetoAtividade;
            }
            
            /**
             * **********************************************************************************************
             * DETALHE 3
             * Instituicoes
             */
            for ($iInd = 0; $iInd < $iLinhasDotacaoSaldo; $iInd ++) {
                $oDespesa = db_utils::fieldsMemory($rsDotacaoSaldo, $iInd);
                
                /*
                 * Apenas serao mostradas as despesas ligadas a dotacao
                 */
                if ($oDespesa->o58_codigo == 0) {
                    continue;
                }
                $iIdUnidade = str_pad($oDespesa->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDespesa->o58_orgao == "24" && $oDespesa->o58_unidade == "20") {
                    $iIdUnidade .= "220";
                } else {
                    $iIdUnidade .= str_pad($oDespesa->o58_unidade, 2, "0", STR_PAD_LEFT);
                }
                if ($sHashUnidade != $iIdUnidade) {
                    $iNumLinha++;
                    $oDadosDetalhe3 = new stdClass();
                    $oDadosDetalhe3->tipregistro = "3";
                    $oDadosDetalhe3->numeroinstitucional = str_pad($iIdUnidade, 11, " ");
                    $oDadosDetalhe3->descricaounidade = str_pad(substr(DBString::removerCaracteresEspeciais($oDespesa->o41_descr), 0, 255), 255, " ", STR_PAD_RIGHT);
                    $oDadosDetalhe3->brancos     = str_repeat(" ", 142);
                    $oDadosDetalhe3->numRegistro = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                    $oDadosDetalhe3->codigolinha = 2000004;
                    
                    $this->aDados [] = $oDadosDetalhe3;
                    
                    $sLinhaUnidade = $oDadosDetalhe3->tipregistro . " | " . $oDadosDetalhe3->numeroinstitucional . " | " . $oDadosDetalhe3->descricaounidade . " | " . $oDadosDetalhe3->brancos . " | " . $oDadosDetalhe3->numRegistro;
                    fputs($arqUnidade, $sLinhaUnidade . "\r\n");
                    fputs($arqFinal, str_replace(" | ", "", $sLinhaUnidade) . "\r\n");
                }
                $sHashUnidade = $iIdUnidade;
            }
            
            /**
             * **********************************************************************************************
             * DETALHE 4
             * Dados das Dotacoes
             */
            asort($aDadosDespesas);
            foreach ($aDadosDespesas as $iIdDespesa => $aDespesa) {
                $oDespesa = (object) $aDespesa;

                $iNumLinha++;
                $oDadosDetalhe4 = new stdClass();
                $oDadosDetalhe4->tipregistro        = "4";
                $oDadosDetalhe4->brancos1           = " ";
                $oDadosDetalhe4->codigodespesa      = str_pad(substr($oDespesa->codigodespesa, 0, 8), 8, "0", STR_PAD_LEFT);
                
                $oDadosDetalhe4->Brancos_2                 = "  ";
                $oDadosDetalhe4->CodigoGrupoExercicioFonte = substr($oDespesa->fonterecurso, 2, 1);
                $oDadosDetalhe4->CodigoClassificacaoFonte  = substr($oDespesa->fonterecurso, 3, 3);
                $oDadosDetalhe4->CodigoDetalhamentoFonte   = substr($oDespesa->fonterecurso, 6, 4);
                $oDadosDetalhe4->Brancos_3                 = " ";

                $oDadosDetalhe4->classinstitucional = str_pad($oDespesa->classinstitucional, 11, " ");
                $oDadosDetalhe4->classfuncional     = str_pad(substr($oDespesa->classfuncional, 0, 5), 5, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->classprograma      = str_pad(substr($oDespesa->classprograma, 0, 5), 5, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->classprojeto       = str_pad(substr($oDespesa->classprojeto, 0, 5), 5, "0", STR_PAD_LEFT);
                
                if ($this->codigoOrgaoTCE != "P088") {
                    $oDadosDetalhe4->valordotinicial   = $this->formataValor(0, 14, "0");
                    $oDadosDetalhe4->valordotacumulada = $this->formataValor(0, 14, "0");
                    $oDadosDetalhe4->valorempbimestre  = $this->formataValor(0, 14, "0");
                    $oDadosDetalhe4->valorempexercicio = $this->formataValor(0, 14, "0");
                    $oDadosDetalhe4->valorliqbimestre  = $this->formataValor(0, 14, "0");
                    $oDadosDetalhe4->valorliqexercicio = $this->formataValor(0, 14, "0");
                    $oDadosDetalhe4->valorpagoexercicio= $this->formataValor(0, 14, "0");
                    $oDadosDetalhe4->valorrpnp         = $this->formataValor(0, 14, "0");
                } else {
                    $oDadosDetalhe4->valordotinicial   = $this->formataValor($oDespesa->valordotinicial, 14, "0");
                    $oDadosDetalhe4->valordotacumulada = $this->formataValor($oDespesa->valordotacumulada, 14, "0");
                    $oDadosDetalhe4->valorempbimestre  = $this->formataValor($oDespesa->valorempbimestre, 14, "0");
                    $oDadosDetalhe4->valorempexercicio = $this->formataValor($oDespesa->valorempexercicio, 14, "0");
                    $oDadosDetalhe4->valorliqbimestre  = $this->formataValor($oDespesa->valorliqbimestre, 14, "0");
                    $oDadosDetalhe4->valorliqexercicio = $this->formataValor($oDespesa->valorliqexercicio, 14, "0");
                    $oDadosDetalhe4->valorpagoexercicio= $this->formataValor($oDespesa->valorpagoexercicio, 14, "0");
                    $oDadosDetalhe4->valorrpnp         = $this->formataValor($oDespesa->valorrpnp, 14, "0");
                }


                $oDadosDetalhe4->brancos           = str_repeat(" ", 250);
                $oDadosDetalhe4->numRegistro       = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                
                $this->aDados [] = $oDadosDetalhe4;

                $sLinhaDotacao =           $oDadosDetalhe4->tipregistro
                                 . " | " . $oDadosDetalhe4->brancos1
                                 . " | " . $oDadosDetalhe4->codigodespesa
                                 . " | " . $oDadosDetalhe4->Brancos_2
                                 . " | " . $oDadosDetalhe4->CodigoGrupoExercicioFonte
                                 . " | " . $oDadosDetalhe4->CodigoClassificacaoFonte
                                 . " | " . $oDadosDetalhe4->CodigoDetalhamentoFonte
                                 . " | " . $oDadosDetalhe4->Brancos_3
                                 . " | " . $oDadosDetalhe4->classinstitucional
                                 . " | " . $oDadosDetalhe4->classfuncional
                                 . " | " . $oDadosDetalhe4->classprograma
                                 . " | " . $oDadosDetalhe4->classprojeto
                                 . " | " . $oDadosDetalhe4->valordotinicial
                                 . " | " . $oDadosDetalhe4->valordotacumulada
                                 . " | " . $oDadosDetalhe4->valorempbimestre
                                 . " | " . $oDadosDetalhe4->valorempexercicio
                                 . " | " . $oDadosDetalhe4->valorliqbimestre
                                 . " | " . $oDadosDetalhe4->valorliqexercicio
                                 . " | " . $oDadosDetalhe4->valorpagoexercicio
                                 . " | " . $oDadosDetalhe4->valorrpnp
                                 . " | " . $oDadosDetalhe4->brancos
                                 . " | " . $oDadosDetalhe4->numRegistro;
                fputs($arqDotacao, $sLinhaDotacao . "\r\n");
                fputs($arqFinal, str_replace(" | ", "", $sLinhaDotacao) . "\r\n");
                
                $nValordotinicial    += $oDadosDetalhe4->valordotinicial   ;
                $nValordotacumulada  += $oDadosDetalhe4->valordotacumulada ;
                $nValorempbimestre   += $oDadosDetalhe4->valorempbimestre  ;
                $nValorempexercicio  += $oDadosDetalhe4->valorempexercicio ;
                $nValorliqbimestre   += $oDadosDetalhe4->valorliqbimestre  ;
                $nValorliqexercicio  += $oDadosDetalhe4->valorliqexercicio ;
                $nValorpagoexercicio += $oDadosDetalhe4->valorpagoexercicio;
                $nValorrpnp          += $oDadosDetalhe4->valorrpnp         ;
            }
            
            $sLinhaTotais .= "nValordotinicial   : $nValordotinicial    \n";
            $sLinhaTotais .= "nValordotacumulada : $nValordotacumulada  \n";
            $sLinhaTotais .= "nValorempbimestre  : $nValorempbimestre   \n";
            $sLinhaTotais .= "nValorempexercicio : $nValorempexercicio  \n";
            $sLinhaTotais .= "nValorliqbimestre  : $nValorliqbimestre   \n";
            $sLinhaTotais .= "nValorliqexercicio : $nValorliqexercicio  \n";
            $sLinhaTotais .= "nValorpagoexercicio: $nValorpagoexercicio \n";
            $sLinhaTotais .= "nValorrpnp         : $nValorrpnp          \n";
            fputs($arqDotacao, $sLinhaTotais . "\r\n");
            fputs($arqDotacao, $sMsgLogErro."\r\n");
        }
        /*
         * TRAILLER
         */
        $iNumLinha++;
        $oDadosTrailler = new stdClass();
        $oDadosTrailler->tipregistro = "9";
        $oDadosTrailler->brancos     = str_repeat(" ", 408);
        $oDadosTrailler->numRegistro = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);

        $oDadosTrailler->codigolinha = 2000006;
        $this->aDados [] = $oDadosTrailler;
        
        $sLinhaTrailler = $oDadosTrailler->tipregistro . " | " . $oDadosTrailler->brancos . " | " . $oDadosTrailler->numRegistro;
        fputs($arqFinal, str_replace(" | ", "", $sLinhaTrailler) . "\r\n");
            
        fclose($arqPrograma);
        fclose($arqProjAtiv);
        fclose($arqUnidade);
        fclose($arqDotacao);
        fclose($arqFinal);
    }
}
