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

class SiaiPessoas extends SiaiArquivoBase
{
  
 /**
  * Busca os dados para gerar o Arquivo de Suprimento de Fundos
  */
    public function gerarDados()
    {
    
    
        $sNomeArquivo = 'PES_'.$this->sBimReferencia.'.TXT';
        $this->setNomeArquivo($sNomeArquivo);
    
        $iAnoSessao         = db_getsession('DB_anousu');
        $iInstituicaoSessao = db_getsession('DB_instit');
        $numLinha           = 1;
    
      //Array com os CGC's das pessoas estrangeiras
        $aPessoaEstrangeira = array("00501909311", "00000055555");

        $lDebug = true;
        if ($lDebug) {
            $arqFinal = fopen("tmp/PES_".$this->sBimReferencia.".TXT", "w+");
        }

      /*
       * HEADER
       */
        $oDadosHeader = new stdClass();
        $oDadosHeader->TipRegistro     = "0";
        $oDadosHeader->NomeArquivo     = str_pad('PES_'.$this->sBimReferencia, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->TipoArquivo     = "O";
        $oDadosHeader->DataGeracaoArq  = $this->dtDataGeracao;
        $oDadosHeader->HoraGeracaoArq  = $this->dtHoraGeracao;
        $oDadosHeader->CodigoOrgao     = $this->codigoOrgaoTCE;
        $oDadosHeader->NomeOrgao       = str_pad($this->nomeUnidade, 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->Brancos         = str_repeat(" ", 274);
        $oDadosHeader->NumRegistroLido = str_pad($numLinha, 10, "0", STR_PAD_LEFT);
        
        $this->aDados[] = $oDadosHeader;
    
        if ($lDebug) {
            $sLinhaHeader = $oDadosHeader->TipRegistro
                        .$oDadosHeader->NomeArquivo
                        .$oDadosHeader->TipoArquivo
                        .$oDadosHeader->DataGeracaoArq
                        .$oDadosHeader->HoraGeracaoArq
                        .$oDadosHeader->CodigoOrgao
                        .$oDadosHeader->NomeOrgao
                        .$oDadosHeader->Brancos
                        .$oDadosHeader->NumRegistroLido;
            fputs($arqFinal, $sLinhaHeader."\r\n");
        }
    
        $sqlPessoas = "select distinct(z01_cgccpf),
                        z01_cgccpf as cgccpf,
                        z01_nome as nome,
                        0 as ordenador
                    from cgm 
                      inner join empautoriza on e54_numcgm = z01_numcgm
                    where z01_cgccpf not in ('0', '00000000000', '00000000000000')

                    union

                     select distinct(z01_cgccpf),
                        z01_cgccpf as cgccpf,
                        z01_nome as nome,
                        0 as ordenador
                    from cgm 
                      inner join pagordemconta on e49_numcgm = z01_numcgm
                    where z01_cgccpf not in ('0', '00000000000', '00000000000000')

                    union

                    select distinct(z01_cgccpf),
                        z01_cgccpf as cgccpf,
                        z01_nome as nome,
                        1 as ordenador
                    from plugins.assinaturaordenadordespesa
                      inner join plugins.documentoassinaturaordenadordespesa on assinaturaordenadordespesa = plugins.assinaturaordenadordespesa.sequencial
                      inner join cgm on z01_numcgm = plugins.assinaturaordenadordespesa.numcgm
                    where plugins.documentoassinaturaordenadordespesa.tipo = 1 and ativo = 't'

                    union 

                    select distinct(z01_cgccpf),
                        z01_cgccpf  as cpf,
                        (case when z01_nomecomple is null or trim(z01_nomecomple) = '' 
                                  then z01_nome
                                  else substr(z01_nomecomple,1,60)
                              end) as nome,
                        0 as ordenador
                    from rhpessoal 
                           inner join rhpessoalmov    on rh01_regist = rh02_regist 
                           left  join rhpesrescisao   on rh02_seqpes = rh05_seqpes 
                           inner join cgm             on rh01_numcgm = z01_numcgm 
                      where rh05_seqpes is null 
                        and rh02_lota in (18, 21, 22, 23, 25) ";
        $resultDados = pg_query($sqlPessoas);
        $iLinhasDados = pg_num_rows($resultDados);

      /*
      * DETALHE 1
      */
        for ($iInd = 0; $iInd < $iLinhasDados; $iInd++) {
            $oDados = db_utils::fieldsMemory($resultDados, $iInd);

            if (in_array($oDados->cgccpf, $aPessoaEstrangeira)) {
                continue;
            }
       
            $numLinha++;

            $oDadosDetalhe1 = new stdClass();
            $oDadosDetalhe1->TipoRegistro      = "1";
            $oDadosDetalhe1->TipoDocumento     = strlen($oDados->z01_cgccpf) == 11 ? "0" : "1";
            $oDadosDetalhe1->Documento         = str_pad($oDados->cgccpf, 14, " ", STR_PAD_LEFT);
            $oDadosDetalhe1->Nome              = str_pad($oDados->nome, 100, " ");
            $oDadosDetalhe1->EOrdenadorDespesa = $oDados->ordenador;
            $oDadosDetalhe1->Brancos           = str_repeat(" ", 291);
            $oDadosDetalhe1->NumRegistroLido   = str_pad($numLinha, 10, "0", STR_PAD_LEFT);

            $this->aDados[] = $oDadosDetalhe1;
   
            if ($lDebug) {
                $sLinhaDetalhe1 = $oDadosDetalhe1->TipoRegistro
                            .$oDadosDetalhe1->TipoDocumento
                            .$oDadosDetalhe1->Documento
                            .$oDadosDetalhe1->Nome
                            .$oDadosDetalhe1->EOrdenadorDespesa
                            .$oDadosDetalhe1->Brancos
                            .$oDadosDetalhe1->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe1."\r\n");
            }
        }

      /*
      * DETALHE 2
      */
        $CodPessoaFisica   = 1;
        $CodPessoaJuridica = 1;
        for ($iInd = 0; $iInd < $iLinhasDados; $iInd++) {
            $oDados = db_utils::fieldsMemory($resultDados, $iInd);
        
            if (!in_array($oDados->cgccpf, $aPessoaEstrangeira)) {
                continue;
            }

            if (strlen($oDados->z01_cgccpf) == 11) {
                $strDocumento = "PF-".str_pad($CodPessoaFisica, 11, "0", STR_PAD_LEFT);
            } else {
                $strDocumento = "PJ-".str_pad($CodPessoaJuridica, 11, "0", STR_PAD_LEFT);
            }

            $numLinha++;
            $oDadosDetalhe2 = new stdClass();
            $oDadosDetalhe2->TipoRegistro      = "2";
            $oDadosDetalhe2->TipoDocumento     = strlen($oDados->z01_cgccpf) == 11 ? "2" : "3";
            $oDadosDetalhe2->Documento         = str_pad($strDocumento, 14, " ", STR_PAD_LEFT);
            $oDadosDetalhe2->Nome              = str_pad($oDados->nome, 100, " ");
            $oDadosDetalhe2->Brancos           = str_repeat(" ", 292);
            $oDadosDetalhe2->NumRegistroLido   = str_pad($numLinha, 10, "0", STR_PAD_LEFT);
    
            $this->aDados[] = $oDadosDetalhe2;

            if (strlen($oDados->z01_cgccpf) == 11) {
                $CodPessoaFisica++;
            } else {
                $CodPessoaJuridica++;
            }
   
            if ($lDebug) {
                $sLinhaDetalhe2 = $oDadosDetalhe2->TipoRegistro
                             .$oDadosDetalhe2->TipoDocumento
                             .$oDadosDetalhe2->Documento
                             .$oDadosDetalhe2->Nome
                             .$oDadosDetalhe2->Brancos
                             .$oDadosDetalhe2->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe2."\r\n");
            }
        }
      //TRAILLER
        $numLinha++;

        $oDadosTrailler = new stdClass();
        $oDadosTrailler->TipRegistro     = "9";
        $oDadosTrailler->Brancos         = str_repeat(" ", 407);
        $oDadosTrailler->NumRegistroLido = str_pad($numLinha, 10, "0", STR_PAD_LEFT);
    
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
