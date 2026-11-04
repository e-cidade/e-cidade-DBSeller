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

require_once(modification("model/contabilidade/arquivos/siai/SiaiArquivoBase.model.php"));
require_once(modification("libs/db_liborcamento.php"));

class SiaiSuprimentoFundos extends SiaiArquivoBase
{
  
    protected $iCodigoLayout     = 2000006;
  
 /**
  * Busca os dados para gerar o Arquivo de Suprimento de Fundos
  */
    public function gerarDados()
    {
    
    
        $sNomeArquivo = 'A25_'.$this->sBimReferencia.'.TXT';
        $this->setNomeArquivo($sNomeArquivo);
    
        $iAnoSessao         = db_getsession('DB_anousu');
        $iInstituicaoSessao = db_getsession('DB_instit');
        $numLinha           = 1;
    
        $lDebug = true;
        if ($lDebug) {
            $arqFinal = fopen("tmp/A25_".$this->sBimReferencia.".TXT", "w+");
        }

        $coddotWhere = "in (select o58_coddot from orcdotacao 
                                                      where (o58_orgao = {$this->codigoOrgao} and o58_unidade = {$this->codigoUnidade}) and o58_anousu = $iAnoSessao)";
        $orcdotacaoWhere = "and (o58_orgao = {$this->codigoOrgao} and o58_unidade = {$this->codigoUnidade})";
    

        $sqlDados = "select distinct(e60_codemp),
                        e60_emiss,
                        e60_vlremp,
                        e60_resumo,
                        e150_numeroprocesso,
                        z01_nome,
                        z01_cgccpf,
                        rh01_funcao,
                        rh01_regist,
                        o40_descr,
                        (select max(c70_data) 
                           from conlancam 
                                inner join conlancamdoc on c71_codlan = c70_codlan 
                                inner join conlancamemp on c75_codlan = c70_codlan
                          where c71_coddoc = 5 
                            and c75_numemp = empempenho.e60_numemp) as dataEntrega,
                        (select max(c70_data) 
                           from conlancam 
                                inner join conlancamdoc on c71_codlan = c70_codlan 
                                inner join conlancamemp on c75_codlan = c70_codlan
                          where c71_coddoc = 6 
                            and c75_numemp = empempenho.e60_numemp) as dataRecolhimento,
                        (select sum(c70_valor) 
                           from conlancam 
                                inner join conlancamdoc on c71_codlan = c70_codlan 
                                inner join conlancamemp on c75_codlan = c70_codlan
                          where c71_coddoc = 5 
                            and c75_numemp = empempenho.e60_numemp) as valor,
                        (select sum(c70_valor) 
                           from conlancam 
                                inner join conlancamdoc on c71_codlan = c70_codlan 
                                inner join conlancamemp on c75_codlan = c70_codlan
                          where c71_coddoc = 6 
                            and c75_numemp = empempenho.e60_numemp) as saldoNaoAplicado
                   from conlancam  
                        inner join conlancamemp on c75_codlan              = c70_codlan 
                        inner join empempenho on e60_numemp                = c75_numemp
                        inner join pagordem on e50_numemp                  = e60_numemp
                        inner join pagordemconta on e49_codord             = e50_codord
                        inner join cgm on z01_numcgm                       = e49_numcgm
                        inner join rhpessoal on rh01_numcgm                = z01_numcgm
                         left join empempaut on e61_numemp                 = e60_numemp
                         left join empautorizaprocesso on e150_empautoriza = e61_autori
                        inner join orcdotacao on o58_coddot                = e60_coddot
                                             and o58_anousu                = e60_anousu
                        inner join orcorgao on o40_orgao                   = o58_orgao
                                             and o40_anousu                = o58_anousu
                  where c70_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' 
                    and e60_anousu = {$iAnoSessao}
                    and e60_codtipo = 4
                    and e60_instit = {$iInstituicaoSessao}
                    and o58_coddot {$coddotWhere} 
                    {$orcdotacaoWhere} ";
        $resultDados  =  db_query($sqlDados);
        $iLinhasDados =  pg_num_rows($resultDados);
      //Se não houver dados, o arquivo terá que ser importado só com header e trailler
      /*if ($iLinhasDados == 0) {
      throw new Exception("Nenhum registro encontrado");
      }*/
    
      /*
       * HEADER
       */
        $oDadosHeader = new stdClass();
        $oDadosHeader->TipRegistro     = "0";
        $oDadosHeader->NomeArquivo     = str_pad('A25_'.$this->sBimReferencia, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->BimReferencia   = $this->sBimReferencia;
        $oDadosHeader->TipoArquivo     = "O";
        $oDadosHeader->DataGeracaoArq  = $this->dtDataGeracao;
        $oDadosHeader->HoraGeracaoArq  = $this->dtHoraGeracao;
        $oDadosHeader->CodigoOrgao     = $this->codigoOrgaoTCE;
        $oDadosHeader->NomeUnidade     = str_pad(substr($this->nomeUnidade, 0, 100), 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->Brancos         = str_repeat(" ", 90);
        $oDadosHeader->NumRegistroLido = str_pad($numLinha, 10, "0", STR_PAD_LEFT);
        
        $oDadosHeader->codigolinha     = 2000763;

        $this->aDados[] = $oDadosHeader;
    
        if ($lDebug) {
            $sLinhaHeader = $oDadosHeader->TipRegistro
                        .$oDadosHeader->NomeArquivo
                        .$oDadosHeader->BimReferencia
                        .$oDadosHeader->TipoArquivo
                        .$oDadosHeader->DataGeracaoArq
                        .$oDadosHeader->HoraGeracaoArq
                        .$oDadosHeader->CodigoOrgao
                        .$oDadosHeader->NomeUnidade
                        .$oDadosHeader->Brancos
                        .$oDadosHeader->NumRegistroLido;
            fputs($arqFinal, $sLinhaHeader."\r\n");
        }
    
      /*
        * DETALHE 1
        */
        for ($iInd = 0; $iInd < $iLinhasDados; $iInd++) {
            $numLinha++;
            $oDados = db_utils::fieldsMemory($resultDados, $iInd);
            $oDadosDetalhe1 = new stdClass();
            $oDadosDetalhe1->TipoRegistro      = "1";
            $oDadosDetalhe1->BimReferencia     = $this->sBimReferencia;
            $oDadosDetalhe1->NumeroProcesso    = str_pad($oDados->e150_numeroprocesso, 20, " ", STR_PAD_LEFT);
            $oDadosDetalhe1->Brancos           = " ";
            $oDadosDetalhe1->OrgaoBeneficiado  = str_pad(substr($oDados->o40_descr, 0, 50), 50, " ");
            $oDadosDetalhe1->ObjetoSolicitacao = str_pad(substr($oDados->e60_resumo, 0, 50), 50, " ");
            $oDadosDetalhe1->FundamentoLegal   = "  ";
            $oDadosDetalhe1->Nome              = str_pad(substr($oDados->z01_nome, 0, 50), 50, " ");
            $oDadosDetalhe1->CPF               = str_pad(substr($oDados->z01_cgccpf, 0, 11), 11, " ");
            $oDadosDetalhe1->Matricula         = str_pad($oDados->rh01_regist, 20, "0", STR_PAD_LEFT);
            $oDadosDetalhe1->Funcao            = str_pad($oDados->rh01_funcao, 40, " ");
            $oDadosDetalhe1->DataConcessao     = $this->formataData($oDados->e60_emiss);
            $oDadosDetalhe1->NumeroEmpenho     = str_pad($oDados->e60_codemp, 12, "0", STR_PAD_LEFT);
            $oDadosDetalhe1->ValorSuprimento   = $this->formataValor($oDados->e60_vlremp, 14, "0");
            $oDadosDetalhe1->Brancos2          = str_repeat(" ", 4);
            $oDadosDetalhe1->NumRegistroLido   = str_pad($numLinha, 10, "0", STR_PAD_LEFT);
            $oDadosDetalhe1->codigolinha       = 2000764;
            $this->aDados[] = $oDadosDetalhe1;
       
            if ($lDebug) {
                $sLinhaDetalhe1 = $oDadosDetalhe1->TipoRegistro
                               .$oDadosDetalhe1->BimReferencia
                               .$oDadosDetalhe1->NumeroProcesso
                               .$oDadosDetalhe1->Brancos
                               .$oDadosDetalhe1->OrgaoBeneficiado
                               .$oDadosDetalhe1->ObjetoSolicitacao
                               .$oDadosDetalhe1->FundamentoLegal
                               .$oDadosDetalhe1->Nome
                               .$oDadosDetalhe1->CPF
                               .$oDadosDetalhe1->Matricula
                               .$oDadosDetalhe1->Funcao
                               .$oDadosDetalhe1->DataConcessao
                               .$oDadosDetalhe1->NumeroEmpenho
                               .$oDadosDetalhe1->ValorSuprimento
                               .$oDadosDetalhe1->Brancos2
                               .$oDadosDetalhe1->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe1."\r\n");
            }
        }
        
        /*
         * DETALHE 2
         */
        for ($iInd = 0; $iInd < $iLinhasDados; $iInd++) {
            $numLinha++;
            $oDados = db_utils::fieldsMemory($resultDados, $iInd);

            $oDadosDetalhe2 = new stdClass();
            $oDadosDetalhe2->TipoRegistro     = "2";
            $oDadosDetalhe2->BimReferencia    = $this->sBimReferencia;
            $oDadosDetalhe2->NumeroProcesso   = str_pad($oDados->e150_numeroprocesso, 20, " ", STR_PAD_LEFT);
            $oDadosDetalhe2->Brancos1         = " ";
            $oDadosDetalhe2->DataEntrega      = str_pad($this->formataData($oDados->dataEntrega), 10, " ", STR_PAD_LEFT);
            $oDadosDetalhe2->Prazo            = str_repeat(" ", 4);
            $oDadosDetalhe2->Brancos2         = str_repeat(" ", 6);
            $oDadosDetalhe2->Valor            = $this->formataValor($oDados->valor, 14, "0");
            $oDadosDetalhe2->SaldoNaoAplicado = $this->formataValor($oDados->saldoNaoAplicado, 14, "0");
            $oDadosDetalhe2->DataRecolhimento = str_pad($this->formataData($oDados->dataRecolhimento), 10, " ");
            $oDadosDetalhe2->DataPrestacao    = str_repeat(" ", 10);
            $oDadosDetalhe2->Brancos          = str_repeat(" ", 134);
            $oDadosDetalhe2->NumRegistroLido  = str_pad($numLinha, 10, "0", STR_PAD_LEFT);

            $oDadosDetalhe2->codigolinha      = 2000765;

            $this->aDados[] = $oDadosDetalhe2;
            
            if ($lDebug) {
                $sLinhaDetalhe2 = $oDadosDetalhe2->TipoRegistro
                                .$oDadosDetalhe2->BimReferencia
                                .$oDadosDetalhe2->NumeroProcesso
                                .$oDadosDetalhe2->Brancos1
                                .$oDadosDetalhe2->DataEntrega
                                .$oDadosDetalhe2->Prazo
                              .$oDadosDetalhe2->Brancos2
                                .$oDadosDetalhe2->Valor
                                .$oDadosDetalhe2->SaldoNaoAplicado
                                .$oDadosDetalhe2->DataRecolhimento
                                .$oDadosDetalhe2->DataPrestacao
                                .$oDadosDetalhe2->Brancos
                                .$oDadosDetalhe2->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe2."\r\n");
            }
        }
      //TRAILLER

        $numLinha++;
        $oDadosTrailler = new stdClass();
        $oDadosTrailler->TipRegistro     = "9";
        $oDadosTrailler->Brancos         = str_repeat(" ", 229);
        $oDadosTrailler->NumRegistroLido = str_pad($numLinha, 10, "0", STR_PAD_LEFT);
        $oDadosTrailler->codigolinha     = 2000766;
    
        $this->aDados[] = $oDadosTrailler;
    
        if ($lDebug) {
            $sLinhaTrailler = $oDadosTrailler->TipRegistro
                          .$oDadosTrailler->Brancos
                          .$oDadosTrailler->NumRegistroLido;
            fputs($arqFinal, $sLinhaTrailler);
            fclose($arqFinal);
        }
    }
}
