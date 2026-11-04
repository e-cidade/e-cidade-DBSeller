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

class SiaiContaCorrente extends SiaiArquivoBase
{
  
    protected $iCodigoLayout     = 2000229;
  
  /**
  * Busca os dados para gerar o Arquivo de ContaCorrente
  */
    public function gerarDados()
    {
    
        $iAnoSessao         = db_getsession('DB_anousu');
        $iInstituicaoSessao = db_getsession('DB_instit');
        $iNumRegistroLido   = 1;

        $sNomeArquivo = 'A26_'.$this->sBimReferencia.'.TXT';
        $this->setNomeArquivo($sNomeArquivo);
    
        $lDebug = true;
        if ($lDebug) {
            $arqFinal = fopen("tmp/A26_".$this->sBimReferencia.".TXT", 'w+');
        }

        if ($this->codigoOrgaoTCE != "P088") {
            $whereDepart = "c61_instit = '{$iInstituicaoSessao}' and k212_departamento in (select db01_coddepto from db_departorg 
                                                            where db01_anousu    = '{$iAnoSessao}' 
                                                                and db01_orgao   = {$this->codigoOrgao}
                                                                and db01_unidade = {$this->codigoUnidade})";
        
            if ($this->codigoOrgao == "29" && $this->codigoUnidade == "1") {
                $whereDepart = "c61_instit = '{$iInstituicaoSessao}' and k212_departamento in (select db01_coddepto from db_departorg 
                                                            where db01_anousu    = '{$iAnoSessao}' 
                                                                and ((db01_orgao = 29 and db01_unidade = 1) 
                                                                  or (db01_orgao = 29 and db01_unidade = 46) 
                                                                  or (db01_orgao = 29 and db01_unidade = 47)))";
            }

            if ($this->codigoOrgao == "20" && $this->codigoUnidade == "1") {
                $whereDepart = "c61_instit = '{$iInstituicaoSessao}' and k212_departamento in (select db01_coddepto from db_departorg 
                                                            where db01_anousu    = '{$iAnoSessao}' 
                                                                and ((db01_orgao = 20 and db01_unidade = 1) 
                                                                  or (db01_orgao = 20 and db01_unidade = 49)))";
            }
 
            if ($this->codigoOrgao == "34" && $this->codigoUnidade == "1") {
                $whereDepart = "c61_instit = '{$iInstituicaoSessao}' and k212_departamento in (select db01_coddepto from db_departorg 
                                                            where db01_anousu    = '{$iAnoSessao}' 
                                                                and ((db01_orgao = 34 and db01_unidade = 1) 
                                                                  or (db01_orgao = 34 and db01_unidade = 49)))";
            }

            if ($this->codigoOrgao == "18" && $this->codigoUnidade == "1") {
                $whereDepart = "c61_instit = '{$iInstituicaoSessao}' and k212_departamento in (select db01_coddepto from db_departorg 
                                                            where db01_anousu    = '{$iAnoSessao}' 
                                                                and ((db01_orgao = 18 and db01_unidade = 1) 
                                                                  or (db01_orgao = 18 and db01_unidade = 45) 
                                                                  or (db01_orgao = 18 and db01_unidade = 46) 
                                                                  or (db01_orgao = 18 and db01_unidade = 47) 
                                                                  or (db01_orgao = 18 and db01_unidade = 48) 
                                                                  or (db01_orgao = 18 and db01_unidade = 49)))";
            }
        } else {
            $whereDepart = "k212_departamento in (select db01_coddepto from db_departorg 
                                                            where db01_anousu    = '{$iAnoSessao}')";
        }

        $sqlContas = "select distinct on (c63_banco||c63_agencia||c63_dvagencia||c63_conta||c63_dvconta)
                         c63_banco,
                         c63_agencia,
                         c63_dvagencia,
                         c63_conta,
                         c63_dvconta,
                         db83_descricao,
                         coalesce(z01_cgccpf, '00000000000') as cgccpf
                    from saltes 
                         inner join saltesdepartamento  on k212_saltes = k13_conta 
                                                         and k212_principal is true
                         inner join plugins.assinaturaordenadordespesa  on assinaturaordenadordespesa.departamento = saltesdepartamento.k212_departamento
                                                                       and assinaturaordenadordespesa.principal is true
                         inner join conplanoreduz         on c61_reduz        = k13_reduz 
                         inner join conplanoconta         on c63_codcon       = c61_codcon 
                                                         and c63_anousu       = c61_anousu
                         inner join conplano              on c60_codcon       = c63_codcon
                                                         and c60_anousu       = c63_anousu
                         inner join conplanocontabancaria on c56_codcon       = c60_codcon
                                                         and c56_anousu       = c60_anousu
                         inner join contabancaria         on db83_sequencial  = c56_contabancaria    
                         inner join cgm                   on z01_numcgm       = plugins.assinaturaordenadordespesa.numcgm
                          left join (select distinct z01_cgccpf as cgccpf from cgm where z01_cgccpf not in ('0', '00000000000', '00000000000000')) as cgm1 on cgm1.cgccpf = db83_identificador                
                    where {$whereDepart} and plugins.assinaturaordenadordespesa.ativo = 't' and c61_anousu = '{$iAnoSessao}'";

        $resultContas  =  db_query($sqlContas);
        $iLinhasContas =  pg_num_rows($resultContas);
      //Se não houver dados, o arquivo terá que ser importado só com header e trailler
      /*if ($iLinhasContas == 0) {
      throw new Exception("Nenhum registro encontrado");
      }*/

      /*
       * HEADER
       */

        $oDadosHeader = new stdClass();
    
        $oDadosHeader->TipRegistro        = "0";
        $oDadosHeader->NomeArquivo        = str_pad("A26_".$this->sBimReferencia, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->BimReferencia      = $this->sBimReferencia;
        $oDadosHeader->TipoArquivo        = "O";
        $oDadosHeader->DataGeracaoArq     = "$this->dtDataGeracao";
        $oDadosHeader->HoraGeracaoArq     = "$this->dtHoraGeracao";
        $oDadosHeader->CodigoOrgao        = $this->codigoOrgaoTCE;
        $oDadosHeader->NomeUnidade        = str_pad($this->nomeUnidade, 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->Brancos1           = " ";
        $oDadosHeader->FornecedorSoftware = str_pad(substr("SOFTWARE PUBLICO BRASILEIRO", 0, 100), 100, " ", STR_PAD_LEFT);
        $oDadosHeader->Brancos2           = str_repeat(" ", 167);
        $oDadosHeader->NumRegistroLido    = str_pad($iNumRegistroLido, 10, "0", STR_PAD_LEFT);
        
        $oDadosHeader->codigolinha     = 2001749;

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
                        .$oDadosHeader->Brancos1
                        .$oDadosHeader->FornecedorSoftware
                        .$oDadosHeader->Brancos2
                        .$oDadosHeader->NumRegistroLido   ;
            fputs($arqFinal, $sLinhaHeader."\r\n");
        }
    
        if ($iLinhasContas > 0) {
            /*
             * DETALHE 1
             */
            for ($iInd = 0; $iInd < $iLinhasContas; $iInd++) {
                $iNumRegistroLido++;
                $oDadosContas = db_utils::fieldsMemory($resultContas, $iInd);
                $oDadosDetalhe = new stdClass();
                    
                $oDadosDetalhe->TipRegistro   = "1";
                $oDadosDetalhe->Brancos       = " ";
                $oDadosDetalhe->Banco         = str_pad($oDadosContas->c63_banco, 3, " ", STR_PAD_LEFT);
                $oDadosDetalhe->Agencia       = str_pad($oDadosContas->c63_agencia.$oDadosContas->c63_dvagencia, 6, " ", STR_PAD_LEFT);
                $oDadosDetalhe->ContaCorrente = str_pad($oDadosContas->c63_conta.$oDadosContas->c63_dvconta, 13, " ", STR_PAD_LEFT);
                $oDadosDetalhe->DescConta     = str_pad(substr(($oDadosContas->db83_descricao ? $oDadosContas->db83_descricao :'S/ Descr'), 0, 50), 50, " ", STR_PAD_RIGHT);
                $oDadosDetalhe->Brancos1      = "   ";
                $oDadosDetalhe->TitularConta  = str_pad($oDadosContas->cgccpf, 11, "0", STR_PAD_LEFT);
                $oDadosDetalhe->Brancos2      = str_repeat(" ", 320);
                $oDadosDetalhe->NumRegistroLido = str_pad($iNumRegistroLido, 10, "0", STR_PAD_LEFT);
            
                $oDadosDetalhe->codigolinha   = 2001750;

                $this->aDados[] = $oDadosDetalhe;
            
                if ($lDebug) {
                    $sLinhaDetalhe = $oDadosDetalhe->TipRegistro
                                 .$oDadosDetalhe->Brancos
                                 .$oDadosDetalhe->Banco
                                 .$oDadosDetalhe->Agencia
                                 .$oDadosDetalhe->ContaCorrente
                                 .$oDadosDetalhe->DescConta
                                 .$oDadosDetalhe->Brancos1
                                 .$oDadosDetalhe->TitularConta
                                 .$oDadosDetalhe->Brancos2
                                 .$oDadosDetalhe->NumRegistroLido ;
                    fputs($arqFinal, $sLinhaDetalhe."\r\n");
                }
            }
        }
    
      //TRAILLER
        $iNumRegistroLido++;
        $oDadosTrailler = new stdClass();
        $oDadosTrailler->TipRegistro     = "9";
        $oDadosTrailler->Brancos         = str_repeat(" ", 407);
        $oDadosTrailler->NumRegistroLido = str_pad($iNumRegistroLido, 10, "0", STR_PAD_LEFT);

        $oDadosTrailler->codigolinha = 2001751;
    
        $this->aDados[] = $oDadosTrailler;
    
        if ($lDebug) {
            $sLinhaTrailler = $oDadosTrailler->TipRegistro
                       .$oDadosTrailler->Brancos
                       .$oDadosTrailler->NumRegistroLido;
            fputs($arqFinal, $sLinhaTrailler."\r\n");
            fclose($arqFinal);
        }
    }
}
