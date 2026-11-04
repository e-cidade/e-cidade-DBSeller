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
class SiaiReceita extends SiaiArquivoBase
{
    protected $iCodigoLayout = 2000002;

    /**
     * Busca os dados para gerar o Arquivo da Receita
     */
    public function gerarDados()
    {
        
        $aElementosFundeb = array();
        $aElementosFundeb[] = "1718012100";
        $aElementosFundeb[] = "1718015100";
        $aElementosFundeb[] = "1718061100";
        $aElementosFundeb[] = "1728011100";
        $aElementosFundeb[] = "1728012100";
        $aElementosFundeb[] = "1728013100";
        
        $lDebug = true;
        $iNumLinha = 1;
        if ($lDebug) {
            $arqFinal   = fopen("tmp/A1R_".$this->sBimReferencia.".TXT", 'w+');
            $arqRecurso = fopen("tmp/TESTE_RECEITA_RECURSO.TXT", 'w+');
            $arqReceita = fopen("tmp/TESTE_RECEITA.TXT", 'w+');
        }
        $this->setNomeArquivo("A1R_".$this->sBimReferencia.".TXT");
        /*
         * HEADER
         */
        $oDadosHeader = new stdClass();
        
        $oDadosHeader->tipregistro    = "0";
        $oDadosHeader->nomearquivo    = str_pad("A1R_".$this->sBimReferencia, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->bimreferencia  = $this->sBimReferencia;
        $oDadosHeader->tipoarquivo    = "O";
        $oDadosHeader->datageracaoarq = $this->dtDataGeracao;
        $oDadosHeader->horageracaoarq = $this->dtHoraGeracao;
        $oDadosHeader->codigoorgao    = $this->codigoOrgaoTCE;
        $oDadosHeader->nomeorgao      = str_pad($this->nomeUnidade, 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->brancos        = str_repeat(" ", 269);
        $oDadosHeader->codigolinha    = 2000007;
        $oDadosHeader->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
        
        $this->aDados [] = $oDadosHeader;
        
        if ($lDebug) {
            $sLinhaHeader =  $oDadosHeader->tipregistro
                            .$oDadosHeader->nomearquivo
                            .$oDadosHeader->bimreferencia
                            .$oDadosHeader->tipoarquivo
                            .$oDadosHeader->datageracaoarq
                            .$oDadosHeader->horageracaoarq
                            .$oDadosHeader->codigoorgao
                            .$oDadosHeader->nomeorgao
                            .$oDadosHeader->brancos
                            .$oDadosHeader->NumRegistroLido;
            fputs($arqFinal, $sLinhaHeader . "\r\n");
        }
        
        $oDaoOrcTipoRec    = db_utils::getDao("orctiporec");
        $rsOrcTipoRec      = $oDaoOrcTipoRec->sql_record($oDaoOrcTipoRec->sql_query_file(null, "o15_codigo, o15_descr", "o15_codigo"));
        $iLinhasOrcTipoRec = pg_num_rows($rsOrcTipoRec);

        if ($iLinhasOrcTipoRec > 0) {
            /*
             * DETALHE 1
             * Dados dos Recursos
             *
             */
            for ($iInd = 0; $iInd < $iLinhasOrcTipoRec; $iInd ++) {
                $oDadosOrcTipoRec = db_utils::fieldsMemory($rsOrcTipoRec, $iInd);
                                
                if (str_pad(substr($oDadosOrcTipoRec->o15_codigo, 0, 10), 10, "0", STR_PAD_LEFT) == "0000000000") {
                    continue;
                }

                $iNumLinha++;
                $oDadosDetalhe1 = new stdClass();
                $oDadosDetalhe1->tipregistro   = "1";
                $oDadosDetalhe1->tipocadastro  = "0";
                $oDadosDetalhe1->numerodafonte = str_pad(substr($oDadosOrcTipoRec->o15_codigo, 0, 10), 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->descricao     = str_pad(substr($oDadosOrcTipoRec->o15_codigo != "000"? $oDadosOrcTipoRec->o15_descr : " ", 0, 350), 350, " ");
                $oDadosDetalhe1->brancos2      = str_repeat(" ", 47);
                $oDadosDetalhe1->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->codigolinha   = 2000008;
                
                $this->aDados [] = $oDadosDetalhe1;
                
                if ($lDebug) {
                    $sLinhaRecurso = $oDadosDetalhe1->tipregistro
                                     ." | ".$oDadosDetalhe1->tipocadastro
                                     ." | ".$oDadosDetalhe1->numerodafonte
                                     ." | ".$oDadosDetalhe1->descricao
                                     ." | ".$oDadosDetalhe1->brancos2
                                     ." | ".$oDadosDetalhe1->NumRegistroLido;
                    fputs($arqRecurso, $sLinhaRecurso . "\r\n");
                    fputs($arqFinal, str_replace(" | ", "", $sLinhaRecurso) . "\r\n");
                }
            }
            
            /*
             * DETALHE2
             * Dados das Receitas
             */
            $sWhere = "";
            $aReceitasIncluidas = array();

            $rsReceitaSaldo = db_receitasaldo(11, 1, 3, true, $sWhere, $this->iAno, $this->dtDataInicial, $this->dtDataFinal, false);
            for ($i = 0; $i < $iLinhasOrcTipoRec; $i ++) {
                $oDadosOrcTipoRec = db_utils::fieldsMemory($rsOrcTipoRec, $i);

                if (str_pad(substr($oDadosOrcTipoRec->o15_codigo, 0, 10), 10, "0", STR_PAD_LEFT) == "0000000000") {
                    continue;
                }

                
                $sql_receita = "select substr(replace(c95_estrutural,'.',''), 1, 9) as elemento,
								       {$oDadosOrcTipoRec->o15_codigo} as fonterecursos,
								       sum(saldo_inicial) as saldo_inicial, 
			                           sum(saldo_inicial_prevadic) as saldo_inicial_prevadic,
			                           sum(saldo_arrecadado) as saldo_arrecadado,
								       sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado
								  from work_receita
									   inner join orcreceita                          on orcreceita.o70_codrec            = work_receita.o70_codrec 
									                                                 and orcreceita.o70_anousu            = {$this->iAno} 
									   inner join orcfontes                           on o70_codfon                       = o57_codfon 
									                                                 and o70_anousu                       = o57_anousu
                                        left join conplanoorcamento                   on c60_codcon                       = o57_codfon 
                                                                                     and c60_anousu                       = o57_anousu	
                                        left join planocontadetalheconplanoorcamento  on conplanoorcamento.c60_codcon     = planocontadetalheconplanoorcamento.c97_conplanoorcamento
                                        left join planocontadetalhe                   on planocontadetalhe.c95_sequencial = planocontadetalheconplanoorcamento.c97_planocontadetalhe
					            where  work_receita.o70_codigo > 0 
					              and abs(saldo_inicial + saldo_inicial_prevadic + saldo_arrecadado + saldo_arrecadado_acumulado) > 0
								group by substr(replace(c95_estrutural,'.',''), 1, 9), {$oDadosOrcTipoRec->o15_codigo}
								order by 2, 1";
                $res_receita = db_query($sql_receita);
                $iLinhasOrcReceita = pg_num_rows($res_receita);
                for ($iInd = 0; $iInd < $iLinhasOrcReceita; $iInd ++) {
                    $oDadosOrcReceita = db_utils::fieldsMemory($res_receita, $iInd);
                    /*
                     * Somente constara as receitas analiticas e que possuirem vinculo na orcreceita
                     */
                    if ($oDadosOrcReceita->elemento      == "0" ||
                        $oDadosOrcReceita->elemento      == ""  ||
                        $oDadosOrcReceita->fonterecursos == "0" ||
                        $oDadosOrcReceita->fonterecursos == "") {
                        continue;
                    }
                    
                    // Se o Codigo da Receita nao comecar com "9" o caractere 11, ou seja, o nono caractere sera em branco
                    if (substr($oDadosOrcReceita->elemento, 0, 1) != "9") {
                        $iCodigoReceita = substr($oDadosOrcReceita->elemento, 1, 8);
                    } else {
                        $iCodigoReceita = substr($oDadosOrcReceita->elemento, 0, 8);
                    }
                    
                    //verificamos se a receita eh ref. a uma deducao
                    //@todo verificar apos atualizacao do ementario
                    $iTipoOperacao = 90;
                    if (substr($oDadosOrcReceita->elemento, 0, 1) == "9") {
                        $iTipoOperacao = 99;
                        if (in_array(substr($oDadosOrcReceita->elemento, 1)."00", $aElementosFundeb)) {
                            $iTipoOperacao = 95;
                        }
                    }

                    if (in_array($oDadosOrcReceita->elemento
                                .$oDadosOrcReceita->saldo_inicial
                                .$oDadosOrcReceita->saldo_inicial_prevadic
                                .$oDadosOrcReceita->saldo_arrecadado
                                .$oDadosOrcReceita->saldo_arrecadado_acumulado, $aReceitasIncluidas)) {
                        continue;
                    }
                    $aReceitasIncluidas[] = $oDadosOrcReceita->elemento
                                           .$oDadosOrcReceita->saldo_inicial
                                           .$oDadosOrcReceita->saldo_inicial_prevadic
                                           .$oDadosOrcReceita->saldo_arrecadado
                                           .$oDadosOrcReceita->saldo_arrecadado_acumulado;

                    $iNumLinha++;
                    $oDadosDetalhe2 = new stdClass();
                    $oDadosDetalhe2->tipregistro          = "2";
                    $oDadosDetalhe2->brancos1             = " ";
                    $oDadosDetalhe2->codigoreceita        = str_pad($iCodigoReceita, 10, "0", STR_PAD_RIGHT);
                    
                    if ($this->codigoOrgaoTCE != "P088") {
                        $oDadosDetalhe2->valorprevistoinicial = $this->formataValor(0, 14, "0");
                        $oDadosDetalhe2->valorprevatualizado  = $this->formataValor(0, 14, "0");
                        $oDadosDetalhe2->valorrealizadobim    = $this->formataValor(0, 14, "0");
                        $oDadosDetalhe2->valorrealizadoexe    = $this->formataValor(0, 14, "0");
                    } else {
                        $oDadosDetalhe2->valorprevistoinicial = $this->formataValor(abs($oDadosOrcReceita->saldo_inicial), 14, "0");
                        $oDadosDetalhe2->valorprevatualizado  = $this->formataValor(abs($oDadosOrcReceita->saldo_inicial_prevadic), 14, "0");
                        $oDadosDetalhe2->valorrealizadobim    = $this->formataValor(abs($oDadosOrcReceita->saldo_arrecadado), 14, "0");
                        $oDadosDetalhe2->valorrealizadoexe    = $this->formataValor(abs($oDadosOrcReceita->saldo_arrecadado_acumulado), 14, "0");
                    }

                    $oDadosDetalhe2->tipocadastro         = "0";
                    $oDadosDetalhe2->fonterecursos        = str_pad(substr($oDadosOrcReceita->fonterecursos, 0, 10), 10, "0", STR_PAD_LEFT);
                    $oDadosDetalhe2->tipooperacao         = $iTipoOperacao;
                    $oDadosDetalhe2->brancos2             = str_repeat(" ", 328);
                    $oDadosDetalhe2->NumRegistroLido      = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                    $oDadosDetalhe2->codigolinha          = 2000009;
                    
                    $this->aDados [] = $oDadosDetalhe2;
                    
                    if ($lDebug) {
                        $sLinhaReceita = $oDadosDetalhe2->tipregistro
                                         . " | " . $oDadosDetalhe2->brancos1
                                         . " | " . $oDadosDetalhe2->codigoreceita
                                         . " | " . $oDadosDetalhe2->valorprevistoinicial
                                         . " | " . $oDadosDetalhe2->valorprevatualizado
                                         . " | " . $oDadosDetalhe2->valorrealizadobim
                                         . " | " . $oDadosDetalhe2->valorrealizadoexe
                                         . " | " . $oDadosDetalhe2->tipocadastro
                                         . " | " . $oDadosDetalhe2->fonterecursos
                                         . " | " . $oDadosDetalhe2->tipooperacao
                                         . " | " . $oDadosDetalhe2->brancos2
                                         . " | " . $oDadosDetalhe2->NumRegistroLido;
                        fputs($arqReceita, $sLinhaReceita . "\r\n");
                        fputs($arqFinal, str_replace(" | ", "", $sLinhaReceita) . "\r\n");
                    }
                    
                    //@todo verificar detalhe3 e detalhe4
                    /*
                    $oDadosDetalhe3 = new stdClass ();
                    $oDadosDetalhe3->TipRegistro                       = "";
                    $oDadosDetalhe3->Brancos1                          = "";
                    $oDadosDetalhe3->CodigoReceitaExerciciosAnteriores = "";
                    $oDadosDetalhe3->Descrição                         = "";
                    $oDadosDetalhe3->Brancos2                          = "";
                    $oDadosDetalhe3->NumRegistroLido                   = "";

                    $this->aDados[] = $oDadosDetalhe3;
                    if ($lDebug) {

                        $sLinhaReceitaClassificacaoRecursosAnteriores = $oDadosDetalhe3->TipRegistro
                                                              . " | " . $oDadosDetalhe3->Brancos1
                                                              . " | " . $oDadosDetalhe3->CodigoReceitaExerciciosAnteriores
                                                              . " | " . $oDadosDetalhe3->Descrição
                                                              . " | " . $oDadosDetalhe3->Brancos2
                                                              . " | " . $oDadosDetalhe3->NumRegistroLido;
                        fputs ( $arqReceita, $sLinhaReceitaClassificacaoRecursosAnteriores . "\r\n" );
                        fputs ( $arqFinal, str_replace(" | ", "", $sLinhaReceitaClassificacaoRecursosAnteriores) . "\r\n" );

                    }

                    $oDadosDetalhe4 = new stdClass ();
                    $oDadosDetalhe4->TipRegistro                       = "";
                    $oDadosDetalhe4->Brancos1                          = "";
                    $oDadosDetalhe4->CodigoReceitaExerciciosAnteriores = "";
                    $oDadosDetalhe4->ValorPrevistoInicial              = "";
                    $oDadosDetalhe4->ValorPrevAtualizado               = "";
                    $oDadosDetalhe4->ValorRealizadoBim                 = "";
                    $oDadosDetalhe4->ValorRealizadoExe                 = "";
                    $oDadosDetalhe4->TipoCadastro                      = "";
                    $oDadosDetalhe4->FonteRecurso                      = "";
                    $oDadosDetalhe4->Brancos2                          = "";
                    $oDadosDetalhe4->NumRegistroLido                   = "";

                    $this->aDados[] = $oDadosDetalhe4;
                    if ($lDebug) {

                        $sLinhaReceitaRecursosAnteriores = $oDadosDetalhe4->TipRegistro
                                                 . " | " . $oDadosDetalhe4->Brancos1
                                                 . " | " . $oDadosDetalhe4->CodigoReceitaExerciciosAnteriores
                                                 . " | " . $oDadosDetalhe4->ValorPrevistoInicial
                                                 . " | " . $oDadosDetalhe4->ValorPrevAtualizado
                                                 . " | " . $oDadosDetalhe4->ValorRealizadoBim
                                                 . " | " . $oDadosDetalhe4->ValorRealizadoExe
                                                 . " | " . $oDadosDetalhe4->TipoCadastro
                                                 . " | " . $oDadosDetalhe4->FonteRecurso
                                                 . " | " . $oDadosDetalhe4->Brancos2
                                                 . " | " . $oDadosDetalhe4->NumRegistroLido;
                        fputs ( $arqReceita, $sLinhaReceitaRecursosAnteriores . "\r\n" );
                        fputs ( $arqFinal, str_replace(" | ", "", $sLinhaReceitaRecursosAnteriores) . "\r\n" );

                    }
                    */
                }
            }
        }

        /*
         * TRAILLER
         */
        $iNumLinha++;
        $oDadosTrailler = new stdClass();
        $oDadosTrailler->tipregistro = "9";
        $oDadosTrailler->brancos     = str_repeat(" ", 408);
        $oDadosTrailler->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
        $oDadosTrailler->codigolinha = 2000010;
        $this->aDados [] = $oDadosTrailler;
        
        if ($lDebug) {
            $sLinhaTrailer = $oDadosTrailler->tipregistro . " | " . $oDadosTrailler->brancos . " | " . $oDadosTrailler->NumRegistroLido;
            fputs($arqFinal, str_replace(" | ", "", $sLinhaTrailer) . "\r\n");
          
            fclose($arqRecurso);
            fclose($arqReceita);
            fclose($arqFinal);
        }
    }
}
