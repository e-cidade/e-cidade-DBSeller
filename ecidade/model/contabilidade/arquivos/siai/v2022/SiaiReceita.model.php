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
        $iNumLinha = 1;

        $arqFinal = fopen("tmp/A1R_" . $this->sBimReferencia . ".TXT", 'w+');
        $arqRecurso = fopen("tmp/TESTE_RECEITA_RECURSO.TXT", 'w+');
        $arqReceita = fopen("tmp/TESTE_RECEITA.TXT", 'w+');
        $arqSuperavit = fopen("tmp/TESTE_SUPERAVIT.TXT", 'w+');

        $this->setNomeArquivo("A1R_" . $this->sBimReferencia . ".TXT");
        /*
         * HEADER
         */
        $oDadosHeader = new stdClass();

        $oDadosHeader->tipregistro = "0";
        $oDadosHeader->nomearquivo = str_pad("A1R_" . $this->sBimReferencia, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->bimreferencia = $this->sBimReferencia;
        $oDadosHeader->tipoarquivo = "O";
        $oDadosHeader->datageracaoarq = $this->dtDataGeracao;
        $oDadosHeader->horageracaoarq = $this->dtHoraGeracao;
        $oDadosHeader->codigoorgao = $this->codigoOrgaoTCE;
        $oDadosHeader->nomeorgao = str_pad($this->nomeUnidade, 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->brancos = str_repeat(" ", 269);
        $oDadosHeader->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
        $this->aDados[] = $oDadosHeader;

        $sLinhaHeader = $oDadosHeader->tipregistro
        . $oDadosHeader->nomearquivo
        . $oDadosHeader->bimreferencia
        . $oDadosHeader->tipoarquivo
        . $oDadosHeader->datageracaoarq
        . $oDadosHeader->horageracaoarq
        . $oDadosHeader->codigoorgao
        . $oDadosHeader->nomeorgao
        . $oDadosHeader->brancos
        . $oDadosHeader->NumRegistroLido;
        fputs($arqFinal, $sLinhaHeader . "\r\n");

        $rsFontes = db_query($this->getSqlFontes());
        $iLinhasFontes = pg_num_rows($rsFontes);
        if ($iLinhasFontes > 0) {
            /*
             * DETALHE 1
             * Dados dos Recursos
             *
             */
            for ($iInd = 0; $iInd < $iLinhasFontes; $iInd ++) {
                $oDadosOrcTipoRec = db_utils::fieldsMemory($rsFontes, $iInd);

                $iNumLinha ++;
                $oDadosDetalhe1 = new stdClass();
                $oDadosDetalhe1->TipRegistro = "1";
                $oDadosDetalhe1->Brancos_1 = " ";
                $oDadosDetalhe1->CodigoGrupoExercicioFonte = substr($oDadosOrcTipoRec->fonte, 0, 1);
                $oDadosDetalhe1->CodigoClassificacaoFonte = substr($oDadosOrcTipoRec->fonte, 1, 3);
                $oDadosDetalhe1->CodigoDetalhamentoFonte = substr($oDadosOrcTipoRec->fonte, 4, 4);
                $oDadosDetalhe1->Brancos_2 = str_repeat(" ", 399);
                $oDadosDetalhe1->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                $this->aDados[] = $oDadosDetalhe1;

                $sLinhaRecurso = $oDadosDetalhe1->TipRegistro .
                " | " . $oDadosDetalhe1->Brancos_1 .
                " | " . $oDadosDetalhe1->CodigoGrupoExercicioFonte .
                " | " . $oDadosDetalhe1->CodigoClassificacaoFonte .
                " | " . $oDadosDetalhe1->CodigoDetalhamentoFonte .
                " | " . $oDadosDetalhe1->Brancos_2 .
                " | " . $oDadosDetalhe1->NumRegistroLido;
                fputs($arqRecurso, $sLinhaRecurso . "\r\n");
                fputs($arqFinal, str_replace(" | ", "", $sLinhaRecurso) . "\r\n");
            }

            /*
             * DETALHE2
             * Dados das Receitas
             */
            $iValorPrevisaoAtualizada = 0;
            $iValorReceitasRealizadasBimestre = 0;

            $aDadosReceita = $this->montarDadosReceita();
            foreach ($aDadosReceita as $oDadosOrcReceita) {
                $iNumLinha ++;
                $oDadosDetalhe2 = new stdClass();
                $oDadosDetalhe2->tipregistro = "2";
                $oDadosDetalhe2->brancos1 = " ";
                $oDadosDetalhe2->codigoreceita = str_pad($oDadosOrcReceita->codigo_receita, 10, "0", STR_PAD_RIGHT);
                $oDadosDetalhe2->valorprevistoinicial = $this->formataValor(abs($oDadosOrcReceita->saldo_inicial), 14, "0");
                $oDadosDetalhe2->valorprevatualizado = $this->formataValor(abs($oDadosOrcReceita->saldo_inicial_prevadic), 14, "0");
                $oDadosDetalhe2->valorrealizadobim = $this->formataValor(abs($oDadosOrcReceita->saldo_arrecadado), 14, "0");
                $oDadosDetalhe2->valorrealizadoexe = $this->formataValor(abs($oDadosOrcReceita->saldo_arrecadado_acumulado), 14, "0");
                $oDadosDetalhe2->Brancos_2 = " ";
                $oDadosDetalhe2->CodigoGrupoExercicioFonte = substr($oDadosOrcReceita->fonte_recurso, 0, 1);
                $oDadosDetalhe2->CodigoClassificacaoFonte = substr($oDadosOrcReceita->fonte_recurso, 1, 3);
                $oDadosDetalhe2->CodigoDetalhamentoFonte = substr($oDadosOrcReceita->fonte_recurso, 4, 4);
                $oDadosDetalhe2->Brancos_3 = "  ";
                $oDadosDetalhe2->tipooperacao = $oDadosOrcReceita->tipo_operacao;
                $oDadosDetalhe2->Brancos_4 = str_repeat(" ", 328);
                $oDadosDetalhe2->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
                $this->aDados[] = $oDadosDetalhe2;
                
                $iValorPrevisaoAtualizada += ($oDadosOrcReceita->saldo_inicial_prevadic);
                $iValorReceitasRealizadasBimestre += ($oDadosOrcReceita->saldo_arrecadado);

                $sLinhaReceita = $oDadosDetalhe2->tipregistro .
                " | " . $oDadosDetalhe2->brancos1 .
                " | " . $oDadosDetalhe2->codigoreceita .
                " | " . $oDadosDetalhe2->valorprevistoinicial .
                " | " . $oDadosDetalhe2->valorprevatualizado .
                " | " . $oDadosDetalhe2->valorrealizadobim .
                " | " . $oDadosDetalhe2->valorrealizadoexe .
                " | " . $oDadosDetalhe2->Brancos_2 .
                " | " . $oDadosDetalhe2->CodigoGrupoExercicioFonte .
                " | " . $oDadosDetalhe2->CodigoClassificacaoFonte .
                " | " . $oDadosDetalhe2->CodigoDetalhamentoFonte .
                " | " . $oDadosDetalhe2->Brancos_3 .
                " | " . $oDadosDetalhe2->tipooperacao .
                " | " . $oDadosDetalhe2->Brancos_4 .
                " | " . $oDadosDetalhe2->NumRegistroLido;
                fputs($arqReceita, $sLinhaReceita . "\r\n");
                fputs($arqFinal, str_replace(" | ", "", $sLinhaReceita) . "\r\n");
            }
        }

        /*
         * SUPERAVIT FINANCEIRO PARA CREDITOS ADICIONAIS
         */
        $iNumLinha ++;
        $oDadosDetalhe5 = new stdClass();
        $oDadosDetalhe5->tipregistro = "5";
        $oDadosDetalhe5->brancos_1 = str_repeat(" ", 1);
        $oDadosDetalhe5->ValorPrevisaoAtualizada = $this->formataValor(($iValorPrevisaoAtualizada), 14, "0");
        $oDadosDetalhe5->ValorReceitasRealizadasBimestre = $this->formataValor(($iValorReceitasRealizadasBimestre), 14, "0");
        $oDadosDetalhe5->brancos_2 = str_repeat(" ", 379);
        $oDadosDetalhe5->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
        $this->aDados[] = $oDadosDetalhe5;

        $sLinhaSuperavit = $oDadosDetalhe5->tipregistro .
        " | " . $oDadosDetalhe5->brancos_1 .
        " | " . $oDadosDetalhe5->ValorPrevisaoAtualizada .
        " | " . $oDadosDetalhe5->ValorReceitasRealizadasBimestre .
        " | " . $oDadosDetalhe5->brancos_2 .
        " | " . $oDadosDetalhe5->NumRegistroLido;
        fputs($arqSuperavit, $sLinhaSuperavit . "\r\n");
        fputs($arqFinal, str_replace(" | ", "", $sLinhaSuperavit) . "\r\n");

        /*
         * TRAILLER
         */
        $iNumLinha ++;
        $oDadosTrailler = new stdClass();
        $oDadosTrailler->tipregistro = "9";
        $oDadosTrailler->brancos = str_repeat(" ", 408);
        $oDadosTrailler->NumRegistroLido = str_pad($iNumLinha, 10, "0", STR_PAD_LEFT);
        $this->aDados[] = $oDadosTrailler;

        $sLinhaTrailer = $oDadosTrailler->tipregistro .
        " | " . $oDadosTrailler->brancos .
        " | " . $oDadosTrailler->NumRegistroLido;
        fputs($arqFinal, str_replace(" | ", "", $sLinhaTrailer) . "\r\n");

        fclose($arqRecurso);
        fclose($arqReceita);
        fclose($arqSuperavit);
        fclose($arqFinal);
    }
    
    public function getSqlFontes()
    {
        
        $sSqlFontes = "select distinct 
                              rpad(case 
                                     when length(fonterecurso.codigo_siconfi) > 4 
                                       then substr(fonterecurso.codigo_siconfi,1,4) || 
                                            (case 
                                               when substr(fonterecurso.codigo_siconfi,5) not in ('3120','3110') 
                                                 then '0000' 
                                               else substr(fonterecurso.codigo_siconfi,5) 
                                             end) 
                                     else rpad(fonterecurso.codigo_siconfi||o15_complemento,8,'0') 
                                   end,8,'0') as fonte
			 from fonterecurso
                              inner join orctiporec on orctiporec.o15_codigo = fonterecurso.orctiporec_id
                        where fonterecurso.exercicio = {$this->iAno} 
                        order by fonte";
        return $sSqlFontes;
    }
    
    public function getSqlReceita($sWhere = null)
    {
        
        $rsReceitaSaldo = db_receitasaldo(11, 1, 3, true, $sWhere, $this->iAno, $this->dtDataInicial, $this->dtDataFinal, false);
        $sSqlReceita = "select substr(orcfontes.o57_fonte,1,9) as elemento,
			                   rpad(case 
                                      when length(fonterecurso.codigo_siconfi) > 4 
                                        then substr(fonterecurso.codigo_siconfi,1,4) || 
                                            (case 
                                               when substr(fonterecurso.codigo_siconfi,5) not in ('3120','3110') 
                                                 then '0000' 
                                               else substr(fonterecurso.codigo_siconfi,5) 
                                             end) 
                                      else rpad(fonterecurso.codigo_siconfi||o15_complemento,8,'0') 
                                    end,8,'0')  as fonte_recurso,
			                   sum(work_receita.saldo_inicial) as saldo_inicial,
			                   sum(work_receita.saldo_inicial_prevadic) as saldo_inicial_prevadic,
                               sum(work_receita.saldo_arrecadado) as saldo_arrecadado,
			                   sum(work_receita.saldo_arrecadado_acumulado) as saldo_arrecadado_acumulado
			              from work_receita
			                   inner join orcreceita  on orcreceita.o70_codrec = work_receita.o70_codrec
			                                         and orcreceita.o70_anousu = {$this->iAno}
			                   inner join orcfontes  on orcfontes.o57_codfon = orcreceita.o70_codfon
			                                        and orcfontes.o57_anousu = orcreceita.o70_anousu
                                           inner join fonterecurso on fonterecurso.orctiporec_id = orcreceita.o70_codigo
						                  and fonterecurso.exercicio = orcreceita.o70_anousu
                                           inner join orctiporec on orctiporec.o15_codigo = fonterecurso.orctiporec_id
			              where work_receita.o70_codigo > 0
			                and abs(work_receita.saldo_inicial + work_receita.saldo_inicial_prevadic + work_receita.saldo_arrecadado + work_receita.saldo_arrecadado_acumulado) > 0
                          group by substr(orcfontes.o57_fonte,1,9), fonte_recurso";
        return $sSqlReceita;
    }
    
    public function montarDadosReceita($sWhere = null)
    {
        
        $aDados = array();
        $sNovoElemento = null;
        $sIdNovoElementoReceita = null;
        
        $rsReceita = db_query($this->getSqlReceita($sWhere));
        $iLinhasOrcReceita = pg_num_rows($rsReceita);
        for ($iInd = 0; $iInd < $iLinhasOrcReceita; $iInd ++) {
            $oDadosOrcReceita = db_utils::fieldsMemory($rsReceita, $iInd);
            /*
             * Somente constara as receitas analiticas e que possuirem vinculo na orcreceita
             */
            if ($oDadosOrcReceita->elemento == "0"
                || $oDadosOrcReceita->elemento == ""
                || $oDadosOrcReceita->fonte_recurso == "0"
                || $oDadosOrcReceita->fonte_recurso == "") {
                    continue;
            }
                
            /*
             * Realizamos o DePara do estrutural da receita de deducao
             */
            $sNovoElemento = $this->deParaElementos(substr($oDadosOrcReceita->elemento, 1, 10));
            
            $sIdNovoElementoReceita = substr($oDadosOrcReceita->elemento, 0, 1).$sNovoElemento;
            // verificamos se a receita eh ref. a uma deducao
            $iTipoOperacao = $this->getTipoOperacao($sIdNovoElementoReceita);
            $oDadosOrcReceita->codigo_receita = $sNovoElemento;
            $oDadosOrcReceita->tipo_operacao = $iTipoOperacao;
            
            if (key_exists($sIdNovoElementoReceita.'.'.$oDadosOrcReceita->fonte_recurso, $aDados)) {
                $aDados[substr($oDadosOrcReceita->elemento, 0, 1).$sNovoElemento.'.'.$oDadosOrcReceita->fonte_recurso]->saldo_inicial += $oDadosOrcReceita->saldo_inicial;
                $aDados[substr($oDadosOrcReceita->elemento, 0, 1).$sNovoElemento.'.'.$oDadosOrcReceita->fonte_recurso]->saldo_inicial_prevadic += $oDadosOrcReceita->saldo_inicial_prevadic;
                $aDados[substr($oDadosOrcReceita->elemento, 0, 1).$sNovoElemento.'.'.$oDadosOrcReceita->fonte_recurso]->saldo_arrecadado += $oDadosOrcReceita->saldo_arrecadado;
                $aDados[substr($oDadosOrcReceita->elemento, 0, 1).$sNovoElemento.'.'.$oDadosOrcReceita->fonte_recurso]->saldo_arrecadado_acumulado += $oDadosOrcReceita->saldo_arrecadado_acumulado;
            } else {
                $aDados[$sIdNovoElementoReceita.'.'.$oDadosOrcReceita->fonte_recurso] = $oDadosOrcReceita;
            }
        }
        
        return $aDados;
    }
    
    public function deParaElementos($sElemento)
    {
        
        $sArquivoDePara = "model/contabilidade/arquivos/siai/v2022/arquivoDeParaElementosReceita.txt";
        if (file_exists($sArquivoDePara)) {
            $aArquivo = file($sArquivoDePara);
            for ($i = 0; $i < count($aArquivo); $i++) {
                $aDePara = explode("=", $aArquivo[$i]);
                if (str_pad($sElemento, 10, "0", STR_PAD_RIGHT) == str_pad($aDePara[0], 10, "0", STR_PAD_RIGHT)) {
                    return preg_replace('/[\n\r]/', "", $aDePara[1]);
                }
            }
        }
        
        return $sElemento;
    }
    
    public function getTipoOperacao($sElemento)
    {

        /*
         * O parametro $sElemento deve ser passado com o primeiro digito por exemplo:
         * 917180121 ou 417180121
         * Sendo o primeiro dígito 9 ou 4.
         */
        $aElementosFundeb = array();
        $aElementosFundeb[] = "17180121";
        $aElementosFundeb[] = "17180151";
        $aElementosFundeb[] = "17180611";
        $aElementosFundeb[] = "17280111";
        $aElementosFundeb[] = "17280121";
        $aElementosFundeb[] = "17280131";
        $aElementosFundeb[] = "17115111";
        $aElementosFundeb[] = "17115201";
        $aElementosFundeb[] = "17195101";
        $aElementosFundeb[] = "17215001";
        $aElementosFundeb[] = "17215101";
        $aElementosFundeb[] = "17215201";
        
        $iTipoOperacao = 90;
        if (substr($sElemento, 0, 1) == "9") {
            $iTipoOperacao = 99;
            if (in_array(substr($sElemento, 1), $aElementosFundeb)) {
                $iTipoOperacao = 95;
            }
        }
        return $iTipoOperacao;
    }
}
