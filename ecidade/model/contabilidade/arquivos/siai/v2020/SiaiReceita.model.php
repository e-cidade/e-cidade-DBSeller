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

        $aDeParaElementosDeducao = array();
        $aDeParaElementosDeducao["917210102"] = "917180121";
        $aDeParaElementosDeducao["917210105"] = "917180151";
        $aDeParaElementosDeducao["917213600"] = "917180611";
        $aDeParaElementosDeducao["917220101"] = "917280111";
        $aDeParaElementosDeducao["917220102"] = "917280121";
        $aDeParaElementosDeducao["917220104"] = "917280131";

        $iNumLinha = 1;

        $arqFinal   = fopen("tmp/A1R_".$this->sBimReferencia.".TXT", 'w+');
        $arqRecurso = fopen("tmp/TESTE_RECEITA_RECURSO.TXT", 'w+');
        $arqReceita = fopen("tmp/TESTE_RECEITA.TXT", 'w+');

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
        $oDadosHeader->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
        
        $this->aDados [] = $oDadosHeader;
        
        $sLinhaHeader = $oDadosHeader->tipregistro
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

        
        $oDaoOrcTipoRec    = db_utils::getDao("orctiporec");
//      $rsOrcTipoRec      = $oDaoOrcTipoRec->sql_record ( $oDaoOrcTipoRec->sql_query_file ( null, "o15_codigo, o15_codtri, o15_descr", "o15_codtri, o15_codigo", "length(o15_codigo::varchar) = 8 or length(o15_codtri::varchar) = 8" ) );
        $rsOrcTipoRec      = $oDaoOrcTipoRec->sql_record($oDaoOrcTipoRec->sql_query_file(null, "o15_codigo, o15_codtri, o15_descr", "o15_codtri, o15_codigo", " (length(o15_codigo::varchar) = 8 or length(o15_codtri::varchar) = 8) and exists (select 1 from orcreceita where orcreceita.o70_codigo = orctiporec.o15_codigo and orcreceita.o70_anousu = {$this->iAno} )"));
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
                $oDadosDetalhe1->TipRegistro               = "1";
                $oDadosDetalhe1->Brancos_1                 = " ";
                $oDadosDetalhe1->CodigoGrupoExercicioFonte = substr($oDadosOrcTipoRec->o15_codtri, 0, 1);
                $oDadosDetalhe1->CodigoClassificacaoFonte  = substr($oDadosOrcTipoRec->o15_codtri, 1, 3);
                $oDadosDetalhe1->CodigoDetalhamentoFonte   = substr($oDadosOrcTipoRec->o15_codtri, 4, 4);
                $oDadosDetalhe1->Brancos_2                 = str_repeat(" ", 399);
                $oDadosDetalhe1->NumRegistroLido           = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                $this->aDados [] = $oDadosDetalhe1;
                
                $sLinhaRecurso =        $oDadosDetalhe1->TipRegistro
                                 ." | ".$oDadosDetalhe1->Brancos_1
                                 ." | ".$oDadosDetalhe1->CodigoGrupoExercicioFonte
                                 ." | ".$oDadosDetalhe1->CodigoClassificacaoFonte
                                 ." | ".$oDadosDetalhe1->CodigoDetalhamentoFonte
                                 ." | ".$oDadosDetalhe1->Brancos_2
                                 ." | ".$oDadosDetalhe1->NumRegistroLido;
                fputs($arqRecurso, $sLinhaRecurso . "\r\n");
                fputs($arqFinal, str_replace(" | ", "", $sLinhaRecurso) . "\r\n");
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

                
                $sSqlReceita = "select substr(o57_fonte, 1, 9) as elemento,
						       o15_codtri as fonterecursos,
						       sum(saldo_inicial) as saldo_inicial, 
			                               sum(saldo_inicial_prevadic) as saldo_inicial_prevadic,
                                                       sum(saldo_arrecadado) as saldo_arrecadado,
						       sum(saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado
						  from work_receita
						       inner join orcreceita                          on orcreceita.o70_codrec            = work_receita.o70_codrec 
						                                                     and orcreceita.o70_anousu            = {$this->iAno} 
							inner join orctiporec on orcreceita.o70_codigo = o15_codigo
						       inner join orcfontes                           on o70_codfon                       = o57_codfon 
						                                                     and o70_anousu                       = o57_anousu
                                                        left join conplanoorcamento                   on c60_codcon                       = o57_codfon 
                                                                                                     and c60_anousu                       = o57_anousu	
                                                        left join planocontadetalheconplanoorcamento  on conplanoorcamento.c60_codcon     = planocontadetalheconplanoorcamento.c97_conplanoorcamento
                                                        left join planocontadetalhe                   on planocontadetalhe.c95_sequencial = planocontadetalheconplanoorcamento.c97_planocontadetalhe
				                  where work_receita.o70_codigo > 0 
						    and abs(saldo_inicial + saldo_inicial_prevadic + saldo_arrecadado + saldo_arrecadado_acumulado) > 0
						  group by substr(o57_fonte, 1, 9), o15_codtri
						  order by 2, 1";

                $rsReceita = db_query($sSqlReceita);
                $iLinhasOrcReceita = pg_num_rows($rsReceita);
                for ($iInd = 0; $iInd < $iLinhasOrcReceita; $iInd ++) {
                    $oDadosOrcReceita = db_utils::fieldsMemory($rsReceita, $iInd);
                    /*
                     * Somente constara as receitas analiticas e que possuirem vinculo na orcreceita
                     */
                    if ($oDadosOrcReceita->elemento      == "0" ||
                        $oDadosOrcReceita->elemento      == ""  ||
                        $oDadosOrcReceita->fonterecursos == "0" ||
                        $oDadosOrcReceita->fonterecursos == "") {
                        continue;
                    }

                    /*
                     * Realizamos o DePara do estrutural da receita de deducao
                     */
                    if (key_exists($oDadosOrcReceita->elemento, $aDeParaElementosDeducao)) {
                        $oDadosOrcReceita->elemento = $aDeParaElementosDeducao[$oDadosOrcReceita->elemento];
                    }
                    
                    $iCodigoReceita = substr($oDadosOrcReceita->elemento, 1, 8);
                    
                    //verificamos se a receita eh ref. a uma deducao
                    $iTipoOperacao = 90;
                    if (substr($oDadosOrcReceita->elemento, 0, 1) == "9") {
                        $iTipoOperacao = 99;
                        if (in_array(substr($oDadosOrcReceita->elemento, 1)."00", $aElementosFundeb)) {
                            $iTipoOperacao = 95;
                        }
                        $iCodigoReceita = substr($oDadosOrcReceita->elemento, 1, 8);
                    }

                    if (in_array($oDadosOrcReceita->elemento.'-'.$oDadosOrcReceita->fonterecursos, $aReceitasIncluidas)) {
                        continue;
                    }
                    $aReceitasIncluidas[] = $oDadosOrcReceita->elemento.'-'.$oDadosOrcReceita->fonterecursos;

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
                    
                    $oDadosDetalhe2->Brancos_2                 = " ";
                    $oDadosDetalhe2->CodigoGrupoExercicioFonte = substr($oDadosOrcReceita->fonterecursos, 0, 1);
                    $oDadosDetalhe2->CodigoClassificacaoFonte  = substr($oDadosOrcReceita->fonterecursos, 1, 3);
                    $oDadosDetalhe2->CodigoDetalhamentoFonte   = substr($oDadosOrcReceita->fonterecursos, 4, 4);
                    $oDadosDetalhe2->Brancos_3                 = "  ";
                    
                    $oDadosDetalhe2->tipooperacao        = $iTipoOperacao;
                    $oDadosDetalhe2->Brancos_4           = str_repeat(" ", 328);
                    $oDadosDetalhe2->NumRegistroLido     = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                    $this->aDados [] = $oDadosDetalhe2;
                    
                    $sLinhaReceita =               $oDadosDetalhe2->tipregistro
                                         . " | " . $oDadosDetalhe2->brancos1
                                         . " | " . $oDadosDetalhe2->codigoreceita
                                         . " | " . $oDadosDetalhe2->valorprevistoinicial
                                         . " | " . $oDadosDetalhe2->valorprevatualizado
                                         . " | " . $oDadosDetalhe2->valorrealizadobim
                                         . " | " . $oDadosDetalhe2->valorrealizadoexe
                                         . " | " . $oDadosDetalhe2->Brancos_2
                                         . " | " . $oDadosDetalhe2->CodigoGrupoExercicioFonte
                                         . " | " . $oDadosDetalhe2->CodigoClassificacaoFonte
                                         . " | " . $oDadosDetalhe2->CodigoDetalhamentoFonte
                                         . " | " . $oDadosDetalhe2->Brancos_3
                                         . " | " . $oDadosDetalhe2->tipooperacao
                                         . " | " . $oDadosDetalhe2->Brancos_4
                                         . " | " . $oDadosDetalhe2->NumRegistroLido;
                    fputs($arqReceita, $sLinhaReceita . "\r\n");
                    fputs($arqFinal, str_replace(" | ", "", $sLinhaReceita) . "\r\n");
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
        $this->aDados [] = $oDadosTrailler;
        
        $sLinhaTrailer = $oDadosTrailler->tipregistro . " | " . $oDadosTrailler->brancos . " | " . $oDadosTrailler->NumRegistroLido;
        fputs($arqFinal, str_replace(" | ", "", $sLinhaTrailer) . "\r\n");
          
        fclose($arqRecurso);
        fclose($arqReceita);
        fclose($arqFinal);
    }
}
