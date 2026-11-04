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
require_once(modification("std/DBString.php"));

class SiaiEmpenhos extends SiaiArquivoBase
{
  
    protected $iCodigoLayout     = 2000004;

  /**
  * Busca os dados para gerar o Arquivo de Empenhos
  */
    public function gerarDados()
    {
  
        $sNomeArquivo = 'A14_'.$this->sBimReferencia;
        $this->setNomeArquivo($sNomeArquivo.".txt");

        $iAnoSessao         = db_getsession('DB_anousu');
        $iInstituicaoSessao = db_getsession('DB_instit');

        $nLinhasArquivo = 1;
        $oDaoEmpempenho = db_utils::getDao('empempenho');
    
        $lDebug = true;
        if ($lDebug) {
            $arqFinal = fopen("tmp/{$sNomeArquivo}.txt", 'w+');
        }

    
        $sCodDotWhere = "in (select o58_coddot from orcdotacao 
                                           where ((o58_orgao = {$this->codigoOrgao} 
                                                    and o58_unidade = {$this->codigoUnidade}) 
                                                  or ({$this->codigoOrgao} = 0 
                                                    and o58_instit = {$this->codigoUnidade})) 
                    and o58_anousu = $iAnoSessao)";
        $sOrcDotacaoWhere = "and ((o58_orgao = {$this->codigoOrgao} 
                                  and o58_unidade = {$this->codigoUnidade}) 
                              or ({$this->codigoOrgao} = 0 
                                  and o58_instit = {$this->codigoUnidade}))";
    
        if ($this->codigoOrgao == "29" && $this->codigoUnidade == "1") {
            $sCodDotWhere = "in (select o58_coddot from orcdotacao 
                                                      where ((o58_orgao = 29 and o58_unidade = 1) 
                                                          or (o58_orgao = 29 and o58_unidade = 46) 
                                                          or (o58_orgao = 29 and o58_unidade = 47)) and o58_anousu = $iAnoSessao)";
            $sOrcDotacaoWhere = "and ((o58_orgao = 29 and o58_unidade = 1) 
                             or (o58_orgao = 29 and o58_unidade = 46) 
                             or (o58_orgao = 29 and o58_unidade = 47))";
        }

        if ($this->codigoOrgao == "20" && $this->codigoUnidade == "1") {
            $sCodDotWhere = "in (select o58_coddot from orcdotacao 
                                                      where ((o58_orgao = 20 and o58_unidade = 1) 
                                                          or (o58_orgao = 20 and o58_unidade = 49)) and o58_anousu = $iAnoSessao)";
            $sOrcDotacaoWhere = "and ((o58_orgao = 20 and o58_unidade = 1) 
                             or (o58_orgao = 20 and o58_unidade = 49))";
        }

        if ($this->codigoOrgao == "34" && $this->codigoUnidade == "1") {
            $sCodDotWhere = "in (select o58_coddot from orcdotacao 
                                                      where ((o58_orgao = 34 and o58_unidade = 1) 
                                                          or (o58_orgao = 34 and o58_unidade = 49)) and o58_anousu = $iAnoSessao)";
            $sOrcDotacaoWhere = "and ((o58_orgao = 34 and o58_unidade = 1) 
                             or (o58_orgao = 34 and o58_unidade = 49))";
        }

        if ($this->codigoOrgao == "18" && $this->codigoUnidade == "1") {
            $sCodDotWhere = "in (select o58_coddot from orcdotacao 
                                                      where ((o58_orgao = 18 and o58_unidade = 1) 
                                                          or (o58_orgao = 18 and o58_unidade = 45) 
                                                          or (o58_orgao = 18 and o58_unidade = 46) 
                                                          or (o58_orgao = 18 and o58_unidade = 47) 
                                                          or (o58_orgao = 18 and o58_unidade = 48)
                                                          or (o58_orgao = 18 and o58_unidade = 49)) and o58_anousu = $iAnoSessao)";
            $sOrcDotacaoWhere = "and ((o58_orgao = 18 and o58_unidade = 1) 
                             or (o58_orgao = 18 and o58_unidade = 45) 
                             or (o58_orgao = 18 and o58_unidade = 46) 
                             or (o58_orgao = 18 and o58_unidade = 47) 
                             or (o58_orgao = 18 and o58_unidade = 48) 
                             or (o58_orgao = 18 and o58_unidade = 49))";
        }
    
        $sCampos  = " orcdotacao.o58_orgao,  ";
        $sCampos .= " orcdotacao.o58_unidade,                        ";
        $sCampos .= " empempenho.e60_numemp,                         ";
        $sCampos .= " empempenho.e60_codemp,                         ";
        $sCampos .= " empempenho.e60_anousu,                         ";
        $sCampos .= " coalesce(empempenho.e60_resumo, 'Justificativa não informada') as e60_resumo, ";
        $sCampos .= " orcdotacao.o58_projativ,                       ";
        $sCampos .= " orcdotacao.o58_codigo,                         ";
        $sCampos .= " orcelemento.o56_elemento,                      ";
        $sCampos .= " empempenho.e60_vlremp,                         ";
        $sCampos .= " CASE                                           ";
        $sCampos .= "   WHEN empempenho.e60_codtipo = 1 THEN 'O'     ";
        $sCampos .= "   WHEN empempenho.e60_codtipo = 2 THEN 'E'     ";
        $sCampos .= "   WHEN empempenho.e60_codtipo = 3 THEN 'G'     ";
        $sCampos .= " END AS e60_codtipo,                            ";
        $sCampos .= " empempenho.e60_emiss,                          ";
        $sCampos .= " cgm.z01_nome,                                  ";
        $sCampos .= " cgm.z01_cgccpf,                                ";
        $sCampos .= " CASE                                           ";
        $sCampos .= "   WHEN char_length(cgm.z01_cgccpf) = 14 THEN 0 ";
        $sCampos .= "   WHEN char_length(cgm.z01_cgccpf) = 11 THEN 1 ";
        $sCampos .= "   ELSE 0                                       ";
        $sCampos .= " END AS tipo_pessoa_credor,                     ";
        $sCampos .= " orcdotacao.o58_orgao,                          ";
        $sCampos .= " orcdotacao.o58_funcao,                         ";
        $sCampos .= " orcdotacao.o58_subfuncao,                      ";
        $sCampos .= " orcdotacao.o58_programa,                       ";
        $sCampos .= "(select e150_numeroprocesso 
                    from empautorizaprocesso inner join empempaut 
                                on e150_empautoriza = e61_autori
                    where e61_numemp = e60_numemp limit 1) as processo_despesa,";
        $sCampos .= "(select numerorecibolicitacao 
                from plugins.empempenhoprocessolicitatorio 
               where empempenho = e60_numemp ) as numeroprocessolicitatorio,";
        $sCampos .= "(select l44_codigotribunal 
                  from empautoriza
                    inner join pctipocompra on pc50_codcom = e54_codcom
                    inner join pctipocompratribunal on l44_sequencial = pc50_pctipocompratribunal
                    inner join empempaut on e61_autori = e54_autori
                  where e61_numemp = e60_numemp) as codigolicitacaotribunal,";
        $sCampos .= "(select T1.cpf from (select max(plugins.documentoassinaturaordenadordespesa.sequencial), z01_cgccpf as cpf 
                    from plugins.documentoassinaturaordenadordespesa 
                         inner join plugins.assinaturaordenadordespesa on assinaturaordenadordespesa.sequencial = documentoassinaturaordenadordespesa.assinaturaordenadordespesa
                         inner join cgm on z01_numcgm = plugins.assinaturaordenadordespesa.numcgm
                   where documentoassinaturaordenadordespesa.chave = e60_numemp 
                     and documentoassinaturaordenadordespesa.tipo = 1
                   group by chave, z01_cgccpf) T1) as numerodocumentoordenador,";
        $sCampos .= "case 
                   when classificacaocredores.cc30_codigo = 4 
                    then 999 
                  else classificacaocredoresdiasliquidacao.diasliquidacao 
                 end as diasliquidacao";
        $sWhereBuscaEmpenhos  = "empempenho.e60_anousu = {$iAnoSessao}                                                 ";
        $sWhereBuscaEmpenhos .= " and empempenho.e60_emiss between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' ";
        $sWhereBuscaEmpenhos .= " and empempenho.e60_instit = {$iInstituicaoSessao}                                    ";
        $sWhereBuscaEmpenhos .= " and e60_codtipo <> 4 ";
        $sWhereBuscaEmpenhos .= " and o58_coddot {$sCodDotWhere} ";
        $sWhereBuscaEmpenhos .= " {$sOrcDotacaoWhere} ";
    
        $sSqlEmpenhos         = "select {$sCampos} 
                               from empempenho                                                                
                                    inner join db_config                      on db_config.codigo                             = empempenho.e60_instit   
                                    inner join orcdotacao                     on orcdotacao.o58_anousu                        = empempenho.e60_anousu   
                                                                             and orcdotacao.o58_coddot                        = empempenho.e60_coddot   
                                    inner join cgm                            on cgm.z01_numcgm                               = empempenho.e60_numcgm   
                                    inner join orcprojativ                    on orcprojativ.o55_anousu                       = orcdotacao.o58_anousu   
                                                                             and orcprojativ.o55_projativ                     = orcdotacao.o58_projativ 
                                    inner join orcelemento                    on orcelemento.o56_codele                       = orcdotacao.o58_codele   
                                                                             and orcelemento.o56_anousu                       = orcdotacao.o58_anousu
                                    inner join classificacaocredoresempenho   on classificacaocredoresempenho.cc31_empempenho = empempenho.e60_numemp
                                    inner join classificacaocredores          on classificacaocredores.cc30_codigo            = classificacaocredoresempenho.cc31_classificacaocredores
                                     left join plugins.classificacaocredoresdiasliquidacao on classificacaocredores           = classificacaocredores.cc30_codigo
                              where {$sWhereBuscaEmpenhos}";
        $rsSqlBuscaEmpenhos   = $oDaoEmpempenho->sql_record($sSqlEmpenhos);
        $iLinhasEmpenhos      = $oDaoEmpempenho->numrows;
      /*
       * HEADER
       */
        $oDadosHeader = new stdClass();
        $oDadosHeader->TipRegistro        = "00";
        $oDadosHeader->NomeArquivo        = str_pad($sNomeArquivo, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->BimReferencia      = $this->sBimReferencia;
        $oDadosHeader->TipoArquivo        = "O";
        $oDadosHeader->DataGeracaoArq     = $this->dtDataGeracao;
        $oDadosHeader->HoraGeracaoArq     = $this->dtHoraGeracao;
        $oDadosHeader->CodigoOrgao        = $this->codigoOrgaoTCE;
        $oDadosHeader->NomeUnidade        = str_pad(substr($this->nomeUnidade, 0, 100), 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->Brancos1           = str_repeat(" ", 1);
        $oDadosHeader->FornecedorSoftware = str_pad(substr("SOFTWARE PUBLICO BRASILEIRO", 0, 100), 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->Brancos2           = str_repeat(" ", 166);
        $oDadosHeader->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
        $oDadosHeader->codigolinha        = 2000750;

        $this->aDados[] = $oDadosHeader;
    
        if ($lDebug) {
            $sLinhaHeader =  $oDadosHeader->TipRegistro
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
                      .$oDadosHeader->NumRegistroLido ;
            fputs($arqFinal, $sLinhaHeader."\r\n");
        }
    
        if ($iLinhasEmpenhos > 0) {
            /*
             * DETALHE 10
             * EMPENHOS
             */
            $aEmpImportados = array("");
            for ($iInd = 0; $iInd < $iLinhasEmpenhos; $iInd++) {
                $oDados = db_utils::fieldsMemory($rsSqlBuscaEmpenhos, $iInd);

                if (in_array($oDados->e60_numemp, $aEmpImportados)) {
                    continue;
                }

                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }
            
                $nLinhasArquivo++;
            
                $aEmpImportados[] = $oDados->e60_numemp;

                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33913900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33913999" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33303900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "31919700" ? "331919900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33313900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33503900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33603900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "44104100" ? "344204100" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "44505100" ? "344805100" : $oDados->o56_elemento;

                $oDadosDetalhe10 = new stdClass();
                $oDadosDetalhe10->TipoRegistro                  = "10";
                $oDadosDetalhe10->BimReferencia                 = $this->sBimReferencia;
                $oDadosDetalhe10->ProcessoDespesa               = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe10->CodigoProcedimentoDespesa     = str_pad($oDados->codigolicitacaotribunal, 4, "0", STR_PAD_LEFT);
                $oDadosDetalhe10->NumeroReciboProcessoLicitacao = str_pad($oDados->numeroprocessolicitatorio, 10, " ", STR_PAD_LEFT);
                $oDadosDetalhe10->DataProcedimentoLicitatorio   = $this->formataData("2016-06-01");
                $oDadosDetalhe10->NumeroEmpenho                 = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe10->TipoEmpenho                   = str_pad($oDados->e60_codtipo ? $oDados->e60_codtipo : "E", 1, " ");
                $oDadosDetalhe10->DataEmpenho                   = $this->formataData($oDados->e60_emiss);
                $oDadosDetalhe10->ValorEmpenhado                = $this->formataValor($oDados->e60_vlremp, 14, "0");
                $oDadosDetalhe10->TipoDocumentoCredor           = strlen($oDados->z01_cgccpf) == 11 ? "0" : "1";
                $oDadosDetalhe10->NumeroDocumentoCredor         = str_pad(($oDados->z01_cgccpf == "99999999999999" ? "08241747000143" : $oDados->z01_cgccpf), 14, " ", STR_PAD_LEFT);
                $oDadosDetalhe10->ClassInstitucional            = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe10->ClassFuncional                = str_pad($oDados->o58_funcao, 2, "0", STR_PAD_LEFT).str_pad($oDados->o58_subfuncao, 3, "0", STR_PAD_LEFT);
                $oDadosDetalhe10->ClassProgramatica             = str_pad(str_pad($oDados->o58_programa, 5, "0", STR_PAD_LEFT).str_pad($oDados->o58_projativ, 5, "0", STR_PAD_LEFT), 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe10->NaturezaDespesa               = substr($oDados->o56_elemento, 1, 8);
                $oDadosDetalhe10->TipoCadastro                  = "0";
                $oDadosDetalhe10->FonteRecurso                  = str_pad($oDados->o58_codigo, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe10->NumeroDocumentoOrdenador      = str_pad($oDados->numerodocumentoordenador, 11, " ");
                $oDadosDetalhe10->TipoRecursoVinculado          = "001";
                $oDadosDetalhe10->Justificativa                 = str_pad(substr(DBString::removerCaracteresEspeciais(empty($oDados->e60_resumo) ? "Justificativa não informada" : DBString::removerCaracteresEspeciais(str_replace(array("\r", "\n"), " ", $oDados->e60_resumo))), 0, 255), 255, " ");
                $oDadosDetalhe10->PrazoMaxLiquidacao            = str_pad($oDados->diasliquidacao, 5, "0", STR_PAD_LEFT);
                $oDadosDetalhe10->Brancos                       = str_repeat(" ", 24);
                $oDadosDetalhe10->NumRegistroLido               = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
            
                $oDadosDetalhe10->codigolinha                   = 2000753;

                $this->aDados[] = $oDadosDetalhe10;

                if ($lDebug) {
                    $sLinhaDetalhe10 = $oDadosDetalhe10->TipoRegistro
                                .$oDadosDetalhe10->BimReferencia
                                .$oDadosDetalhe10->ProcessoDespesa
                                .$oDadosDetalhe10->CodigoProcedimentoDespesa
                                .$oDadosDetalhe10->NumeroReciboProcessoLicitacao
                                .$oDadosDetalhe10->DataProcedimentoLicitatorio
                                .$oDadosDetalhe10->NumeroEmpenho
                                .$oDadosDetalhe10->TipoEmpenho
                                .$oDadosDetalhe10->DataEmpenho
                                .$oDadosDetalhe10->ValorEmpenhado
                                .$oDadosDetalhe10->TipoDocumentoCredor
                                .$oDadosDetalhe10->NumeroDocumentoCredor
                                .$oDadosDetalhe10->ClassInstitucional
                                .$oDadosDetalhe10->ClassFuncional
                                .$oDadosDetalhe10->ClassProgramatica
                                .$oDadosDetalhe10->NaturezaDespesa
                                .$oDadosDetalhe10->TipoCadastro
                                .$oDadosDetalhe10->FonteRecurso
                                .$oDadosDetalhe10->NumeroDocumentoOrdenador
                                .$oDadosDetalhe10->TipoRecursoVinculado
                                .$oDadosDetalhe10->Justificativa
                                .$oDadosDetalhe10->PrazoMaxLiquidacao
                                .$oDadosDetalhe10->Brancos
                                .$oDadosDetalhe10->NumRegistroLido;
                    fputs($arqFinal, $sLinhaDetalhe10."\r\n");
                }
            }
        }
    
      /*
       * Busca os dados dos lancamentos dos empenhos
       */
        $sSqlDados = "select distinct on (c70_codlan) orcdotacao.o58_orgao,
                         orcdotacao.o58_unidade,
                         empempenho.e60_numemp,
                         empempenho.e60_codemp,
                         conplanoconta.c63_banco,
                         conplanoconta.c63_agencia,
                         conplanoconta.c63_dvagencia,
                         conplanoconta.c63_conta,
                         conplanoconta.c63_dvconta,
                         pagordem.e50_codord,
                         empnota.e69_codnota,
                         pagordemnota.e71_codnota,
                         pagordemnota.e71_codord,
                         conlancam.c70_codlan,
                         conlancam.c70_data,
                         conlancam.c70_valor,
                         conlancamcompl.c72_complem,
                         conhistdoctipo.c57_sequencial,
                         corgrupocorrente.k105_corgrupotipo,
                         empanulado.e94_motivo,
                         
                         empnotafiscal.tiponotafiscal,  
                         empnotafiscal.numeronota,      
                         empnotafiscal.localrecebimento,
                         empnotafiscal.dtnota,          
                         empnotafiscal.dtrecebe,        
                         empnotafiscal.dtvencimento,    
                         empnotafiscal.mes_competencia,
                         empnotafiscal.ano_competencia, 
                         empnotafiscal.numeroprocesso,  
                         empnotafiscal.seriefiscal,    
                         empnotafiscal.valorfiscal,     
                         empnotagestor.empnota,
                         empnotagestor.gestor,      
                         empnotagestor.dtatesto,    
                         empnotagestor.dtvencimento,
                         
                         empnotagestor_cgm.z01_numcgm as notafiscal_ordenadorcgm,
                         empnotagestor_cgm.z01_nome as notafiscal_ordenadornome,
                         empnotagestor_cgm.z01_cgccpf as notafiscal_ordenadornumerodocumento,
   
                         empagemovjustificativa.e09_justificativa,
 
                         (select T1.cpf 
                            from (select max(plugins.documentoassinaturaordenadordespesa.sequencial), 
                                         z01_cgccpf as cpf
                                    from plugins.documentoassinaturaordenadordespesa 
                                         inner join plugins.assinaturaordenadordespesa on assinaturaordenadordespesa.sequencial = documentoassinaturaordenadordespesa.assinaturaordenadordespesa
                                         inner join cgm                                on cgm.z01_numcgm                        = plugins.assinaturaordenadordespesa.numcgm
                                   where documentoassinaturaordenadordespesa.chave = c75_numemp 
                                     and documentoassinaturaordenadordespesa.tipo = 1
                                   group by chave, z01_cgccpf) T1) as numerodocumentoordenador,

                         (select T1.cpf 
                            from (select max(plugins.documentoassinaturaordenadordespesa.sequencial), 
                                         z01_cgccpf as cpf
                                    from plugins.documentoassinaturaordenadordespesa 
                                         inner join plugins.assinaturaordenadordespesa on assinaturaordenadordespesa.sequencial = documentoassinaturaordenadordespesa.assinaturaordenadordespesa
                                         inner join cgm                                on cgm.z01_numcgm                        = plugins.assinaturaordenadordespesa.numcgm
                                   where documentoassinaturaordenadordespesa.chave = empagemov.e81_codmov 
                                     and documentoassinaturaordenadordespesa.tipo = 5
                                   group by chave, z01_cgccpf) T1) as numerodocumentoordenador_pagamento,
                                   
                         (select e150_numeroprocesso 
                            from empautorizaprocesso 
                                 inner join empempaut on e150_empautoriza = e61_autori
                           where e61_numemp = empempenho.e60_numemp limit 1) as processo_despesa,
                           
                         case 
                           when pagordemconta.e49_codord is not null 
                             then pagordemconta_cgm.z01_cgccpf
                           else cgm.z01_cgccpf
                         end as documento_credor,
                         
                         (select max(pg.e50_codord) 
                            from pagordem pg 
                           where pg.e50_numemp = empempenho.e60_numemp) as NumOB
                           
                   from conlancam 
                        inner join conlancamdoc                  on conlancamdoc.c71_codlan                  = conlancam.c70_codlan 
                        inner join conhistdoc                    on conhistdoc.c53_coddoc                    = conlancamdoc.c71_coddoc 
                        inner join conhistdoctipo                on conhistdoctipo.c57_sequencial            = conhistdoc.c53_tipo 
                        inner join conlancamemp                  on conlancamemp.c75_codlan                  = conlancam.c70_codlan 
                        inner join empempenho                    on empempenho.e60_numemp                    = conlancamemp.c75_numemp
                        inner join cgm                           on cgm.z01_numcgm                           = empempenho.e60_numcgm
                        inner join orcdotacao                    on orcdotacao.o58_coddot                    = empempenho.e60_coddot
                                                                and orcdotacao.o58_anousu                    = empempenho.e60_anousu
                         left join conlancamcorgrupocorrente     on conlancamcorgrupocorrente.c23_conlancam  = conlancam.c70_codlan
                         left join corgrupocorrente              on corgrupocorrente.k105_sequencial         = conlancamcorgrupocorrente.c23_corgrupocorrente 
                         left join conlancamnota                 on conlancamnota.c66_codlan                 = conlancam.c70_codlan 
                         left join conlancampag                  on conlancampag.c82_codlan                  = conlancam.c70_codlan
                         left join conlancamcompl                on conlancamcompl.c72_codlan                = conlancam.c70_codlan                   
                         left join conplanoreduz                 on conplanoreduz.c61_reduz                  = conlancampag.c82_reduz
                                                                and conplanoreduz.c61_anousu                 = conlancampag.c82_anousu
                         left join conplano                      on conplano.c60_codcon                      = conplanoreduz.c61_codcon
                                                                and conplano.c60_anousu                      = conplanoreduz.c61_anousu
                         left join conplanoconta                 on conplanoconta.c63_codcon                 = conplano.c60_codcon
                                                                and conplanoconta.c63_anousu                 = conplano.c60_anousu
                         left join empnota                       on empnota.e69_codnota                      = conlancamnota.c66_codnota
                         left join conlancamord                  on conlancamord.c80_codlan                  = conlancam.c70_codlan 
                         left join pagordem                      on pagordem.e50_codord                      = conlancamord.c80_codord
                         left join empord                        on empord.e82_codord                        = pagordem.e50_codord
                         left join empagemov                     on empagemov.e81_codmov                     = empord.e82_codmov
                         left join empagemovjustificativa        on empagemovjustificativa.e09_codmov        = empord.e82_codmov
                                                                and empagemovjustificativa.e09_codnota       = empnota.e69_codnota
                         left join pagordemnota                  on pagordemnota.e71_codord                  = conlancamord.c80_codord 
                         left join pagordemconta                 on pagordemconta.e49_codord                 = pagordem.e50_codord
                         left join cgm as pagordemconta_cgm      on pagordemconta_cgm.z01_numcgm             = pagordemconta.e49_numcgm 
                         left join empanulado                    on empanulado.e94_numemp                    = empempenho.e60_numemp
                         left join plugins.empnotagestor         on empnotagestor.empnota                    = (coalesce(empnota.e69_codnota,pagordemnota.e71_codnota))
                         left join cgm as empnotagestor_cgm      on empnotagestor_cgm.z01_numcgm             = empnotagestor.gestor
                         left join plugins.empnotafiscalempnota  on empnotafiscalempnota.empnota             = (coalesce(empnota.e69_codnota,pagordemnota.e71_codnota))
                         left join plugins.empnotafiscal         on empnotafiscal.sequencial                 = empnotafiscalempnota.empnotafiscal
                   where conlancam.c70_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' 
                     and conhistdoctipo.c57_sequencial in (11,20,21,30,31) 
                     and conlancam.c70_anousu = {$iAnoSessao}
                     and empempenho.e60_codtipo <> 4
                     and empempenho.e60_instit = {$iInstituicaoSessao}
                     and empempenho.e60_emiss >= '2016-01-01'
		     and empempenho.e60_anousu = {$iAnoSessao}
                     {$sOrcDotacaoWhere}";
        $rsDados  =  db_query($sSqlDados);
        $iLinhasDados =  pg_num_rows($rsDados);
        if ($iLinhasDados > 0) {
          /*
           * DETALHE 20
           * LIQUIDACOES
           */
            $aLiquidacoesImportadas = array("");
            for ($iIndLiq = 0; $iIndLiq < $iLinhasDados; $iIndLiq++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndLiq);
      
                if ($oDados->c57_sequencial != 20 || in_array($oDados->e69_codnota, $aLiquidacoesImportadas)) {
                    continue;
                }

                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }
       
                $aLiquidacoesImportadas[] = $oDados->e69_codnota;
                $nLinhasArquivo++;
                $oDadosDetalhe20 = new stdClass();
                $oDadosDetalhe20->TipRegistro         = "20";
                $oDadosDetalhe20->BimReferencia       = $this->sBimReferencia;
                $oDadosDetalhe20->ProcessoDespesa     = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe20->NumeroEmpenho       = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe20->NumeroProcesso      = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe20->ClassInstitucional  = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe20->NumeroDocLiquid     = str_pad($oDados->e69_codnota, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe20->DataLiquidacao      = $this->formataData($oDados->c70_data);
                $oDadosDetalhe20->ValorLiquidado      = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe20->TipoDocumentoCredor = strlen($oDados->documento_credor) == 11 ? "0" : "1";
                $oDadosDetalhe20->DocumentoCredor     = str_pad(($oDados->documento_credor == "99999999999999" ? "08241747000143" : $oDados->documento_credor), 14, "0", STR_PAD_LEFT);
                $oDadosDetalhe20->DocumentoRespons    = str_pad($oDados->numerodocumentoordenador, 11, " ", STR_PAD_LEFT);
                $oDadosDetalhe20->Brancos1            = str_repeat(" ", 311);
                $oDadosDetalhe20->NumRegistroLido     = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
      
                $oDadosDetalhe20->codigolinha         = 2000767;
      
                $this->aDados[] = $oDadosDetalhe20;
      
                if ($lDebug) {
                    $sLinhaDetalhe20 =  $oDadosDetalhe20->TipRegistro
                             .$oDadosDetalhe20->BimReferencia
                             .$oDadosDetalhe20->ProcessoDespesa
                             .$oDadosDetalhe20->NumeroEmpenho
                             .$oDadosDetalhe20->NumeroProcesso
                             .$oDadosDetalhe20->ClassInstitucional
                             .$oDadosDetalhe20->NumeroDocLiquid
                             .$oDadosDetalhe20->DataLiquidacao
                             .$oDadosDetalhe20->ValorLiquidado
                             .$oDadosDetalhe20->TipoDocumentoCredor
                             .$oDadosDetalhe20->DocumentoCredor
                             .$oDadosDetalhe20->DocumentoRespons
                             .$oDadosDetalhe20->Brancos1
                             .$oDadosDetalhe20->NumRegistroLido;
                    fputs($arqFinal, $sLinhaDetalhe20."\r\n");
                }
            }
      
      
          /*
           * DETALHE 21
           * NOTA FISCAL
           */
            $aLiquidacoesImportadas = array("");
            for ($iIndLiq = 0; $iIndLiq < $iLinhasDados; $iIndLiq++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndLiq);
      
                if ($oDados->c57_sequencial != 20 || in_array($oDados->e69_codnota, $aLiquidacoesImportadas)) {
                    continue;
                }

                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }
       
                $aLiquidacoesImportadas[] = $oDados->e69_codnota;
                $nLinhasArquivo++;
                $oDadosDetalhe21 = new stdClass();
                $oDadosDetalhe21->TipRegistro         = "21";
                $oDadosDetalhe21->BimReferencia       = $this->sBimReferencia;
                $oDadosDetalhe21->NumeroDocLiquid     = str_pad(substr($oDados->e69_codnota, 0, 15), 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe21->CodDocFiscal        = str_pad($oDados->tiponotafiscal, 4, "0", STR_PAD_LEFT);
                $oDadosDetalhe21->NumDocFiscal        = str_pad(substr($oDados->numeronota, 0, 9), 9, "0", STR_PAD_LEFT);
                $oDadosDetalhe21->SerieFiscal         = str_pad(substr($oDados->seriefiscal, 0, 3), 3, "0", STR_PAD_LEFT);
                $oDadosDetalhe21->DataEmissaoDocFis   = $this->formataData($oDados->dtnota);
                $oDadosDetalhe21->ChaveFiscal         = str_pad("0", 44, "0", STR_PAD_LEFT);
                $oDadosDetalhe21->ValorFaturado       = str_pad($oDados->valorfiscal, 14, "0", STR_PAD_LEFT);
                $oDadosDetalhe21->DataDeRecebimentoNF = $this->formataData($oDados->dtrecebe);
                $oDadosDetalhe21->DataDoAtestoDaNF    = $this->formataData($oDados->dtatesto);
                $oDadosDetalhe21->Brancos1            = str_repeat(" ", 323);
                $oDadosDetalhe21->NumRegistroLido     = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
      
                $oDadosDetalhe21->codigolinha         = 2000767;
      
                $this->aDados[] = $oDadosDetalhe21;
      
                if ($lDebug) {
                    $sLinhaDetalhe21 =  $oDadosDetalhe21->TipRegistro
                             .$oDadosDetalhe21->BimReferencia
                             .$oDadosDetalhe21->NumeroDocLiquid
                             .$oDadosDetalhe21->CodDocFiscal
                             .$oDadosDetalhe21->NumDocFiscal
                             .$oDadosDetalhe21->SerieFiscal
                             .$oDadosDetalhe21->DataEmissaoDocFis
                             .$oDadosDetalhe21->ChaveFiscal
                             .$oDadosDetalhe21->ValorFaturado
                             .$oDadosDetalhe21->DataDeRecebimentoNF
                             .$oDadosDetalhe21->DataDoAtestoDaNF
                             .$oDadosDetalhe21->Brancos1
                             .$oDadosDetalhe21->NumRegistroLido;
                    fputs($arqFinal, $sLinhaDetalhe21."\r\n");
                }
            }
      
      

          /*
           * DETALHE 30
           * PAGAMENTOS
           */
            $aPagamentosImportados = array("");
            for ($iIndPag = 0; $iIndPag < $iLinhasDados; $iIndPag++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndPag);
              //So vai importar se for pagamento, se nao for uma retencao, e se nao tiver sido importado
                if ($oDados->k105_corgrupotipo != 2 && $oDados->c57_sequencial == 30 && !in_array($oDados->e50_codord, $aPagamentosImportados)) {
                    $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                    if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                        $codUnidade .= "220";
                    } else {
                        $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                    }

                    $aPagamentosImportados[] = $oDados->e50_codord;
                    $nLinhasArquivo++;
          
                    $sDomilicioBancario  = str_pad(substr($oDados->c63_banco, 0, 3), 3, " ", STR_PAD_LEFT);
                    $sDomilicioBancario .= str_pad(substr($oDados->c63_agencia.$oDados->c63_dvagencia, 0, 6), 6, " ", STR_PAD_LEFT);
                    $sDomilicioBancario .= str_pad(substr($oDados->c63_conta.$oDados->c63_dvconta, 0, 13), 13, " ", STR_PAD_LEFT);
          
                    $oDadosDetalhe30 = new stdClass();
                    $oDadosDetalhe30->TipRegistro                  = "30";
                    $oDadosDetalhe30->BimReferencia                = $this->sBimReferencia;
                    $oDadosDetalhe30->ProcessoDespesa              = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                    $oDadosDetalhe30->NumeroEmpenho                = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                    $oDadosDetalhe30->NumeroProcesso               = str_pad($oDados->processo_despesa, 20, " ", STR_PAD_LEFT);
                    $oDadosDetalhe30->ClassInstitucional           = str_pad($codUnidade, 11, " ");
                    $oDadosDetalhe30->NumeroDocLiquid              = str_pad($oDados->e71_codnota, 15, "0", STR_PAD_LEFT);
                    $oDadosDetalhe30->DomicilioBancario            = $sDomilicioBancario;
                    $oDadosDetalhe30->EspecieDocumentoPag          = "001";
                    $oDadosDetalhe30->NumeroDocumentoPag           = str_pad($oDados->e50_codord, 10, "0", STR_PAD_LEFT);
                    $oDadosDetalhe30->DataDocumentoPag             = $this->formataData($oDados->c70_data);
                    $oDadosDetalhe30->TipoDocumentoCredor          = strlen($oDados->documento_credor) == 11 ? "0" : "1";
                    $oDadosDetalhe30->DocumentoCredor              = str_pad(($oDados->documento_credor == "99999999999999" ? "08241747000143" : $oDados->documento_credor), 14, "0", STR_PAD_LEFT);
                    $oDadosDetalhe30->ValorPago                    = $this->formataValor($oDados->c70_valor, 14, "0");
                    $oDadosDetalhe30->CodigoRetencao               = str_pad("", 3, "0");
                    $oDadosDetalhe30->NumeroDocumentoOrdenador     = str_pad($oDados->numerodocumentoordenador_pagamento, 11, "0");
                    $oDadosDetalhe30->DataEfetivaTransferência     = $this->formataData($oDados->c70_data);
                    $oDadosDetalhe30->JustificativaQuebraOrdenador = str_pad($oDados->e09_justificativa, 255, " ");
                    $oDadosDetalhe30->Brancos1                     = str_pad("", 8, " ");
                    $oDadosDetalhe30->NumRegistroLido              = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
            
                    $oDadosDetalhe30->codigolinha         = 2000767;

                    $this->aDados[] = $oDadosDetalhe30;

                    if ($lDebug) {
                        $sLinhaDetalhe30 = $oDadosDetalhe30->TipRegistro
                              .$oDadosDetalhe30->BimReferencia
                              .$oDadosDetalhe30->ProcessoDespesa
                              .$oDadosDetalhe30->NumeroEmpenho
                              .$oDadosDetalhe30->NumeroProcesso
                              .$oDadosDetalhe30->ClassInstitucional
                              .$oDadosDetalhe30->NumeroDocLiquid
                              .$oDadosDetalhe30->DomicilioBancario
                              .$oDadosDetalhe30->EspecieDocumentoPag
                              .$oDadosDetalhe30->NumeroDocumentoPag
                              .$oDadosDetalhe30->DataDocumentoPag
                              .$oDadosDetalhe30->TipoDocumentoCredor
                              .$oDadosDetalhe30->DocumentoCredor
                              .$oDadosDetalhe30->ValorPago
                              .$oDadosDetalhe30->CodigoRetencao
                              .$oDadosDetalhe30->NumeroDocumentoOrdenador
                              .$oDadosDetalhe30->DataEfetivaTransferência
                              .$oDadosDetalhe30->JustificativaQuebraOrdenador
                              .$oDadosDetalhe30->Brancos1
                              .$oDadosDetalhe30->NumRegistroLido;
                        fputs($arqFinal, $sLinhaDetalhe30."\r\n");
                    }
                }

                if ($oDados->k105_corgrupotipo == 2 && $oDados->c57_sequencial == 30) {
                    $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                    if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                        $codUnidade .= "220";
                    } else {
                        $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                    }
                  //Busca as retencoes da ordem e insere
                    $sSqlRetencoes = "select e23_retencaotiporec,
                                   e23_valorretencao 
                              from retencaopagordem
                             inner join retencaoreceitas on e23_retencaopagordem = e20_sequencial
                                                        and e23_ativo            = 't'
                                                        and e23_recolhido        = 't'
                             where e20_pagordem = {$oDados->e50_codord} 
                               and e23_dtcalculo between '{$this->dtDataInicial}' 
                               and '{$this->dtDataFinal}'";
                    $rsRetencoes = db_query($sSqlRetencoes);

                    if (pg_num_rows($rsRetencoes) > 0) {
                        for ($i=0; $i<pg_num_rows($rsRetencoes); $i++) {
                            $nLinhasArquivo++;
                            $oDadosRetencoes = db_utils::fieldsMemory($rsRetencoes, $i);
              
                            $oDadosDetalhe30 = new stdClass();
                            $oDadosDetalhe30->TipRegistro                  = "30";
                            $oDadosDetalhe30->BimReferencia                = $this->sBimReferencia;
                            $oDadosDetalhe30->ProcessoDespesa              = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                            $oDadosDetalhe30->NumeroEmpenho                = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                            $oDadosDetalhe30->NumeroProcesso               = str_pad($oDados->processo_despesa, 20, " ", STR_PAD_LEFT);
                            $oDadosDetalhe30->ClassInstitucional           = str_pad($codUnidade, 11, " ");
                            $oDadosDetalhe30->NumeroDocLiquid              = str_pad($oDados->e71_codnota, 15, "0", STR_PAD_LEFT);
                            $oDadosDetalhe30->DomicilioBancario            = str_pad(substr($oDados->c63_banco, 0, 3), 3, " ", STR_PAD_LEFT).str_pad(substr($oDados->c63_agencia.$oDados->c63_dvagencia, 0, 6), 6, " ", STR_PAD_LEFT).str_pad(substr($oDados->c63_conta.$oDados->c63_dvconta, 0, 13), 13, " ", STR_PAD_LEFT);
                            $oDadosDetalhe30->EspecieDocumentoPag          = "001";
                            $oDadosDetalhe30->NumeroDocumentoPag           = str_pad($oDados->e50_codord, 10, "0", STR_PAD_LEFT);
                            $oDadosDetalhe30->DataDocumentoPag             = $this->formataData($oDados->c70_data);
                            $oDadosDetalhe30->TipoDocumentoCredor          = strlen($oDados->documento_credor) == 11 ? "0" : "1";
                            $oDadosDetalhe30->DocumentoCredor              = str_pad(($oDados->documento_credor == "99999999999999" ? "08241747000143" : $oDados->documento_credor), 14, "0", STR_PAD_LEFT);
                            $oDadosDetalhe30->ValorPago                    = $this->formataValor($oDadosRetencoes->e23_valorretencao, 14, "0");
                            $oDadosDetalhe30->CodigoRetencao               = str_pad("", 3, "0");//str_pad($oDadosRetencoes->e23_retencaotiporec, 3, "0");
                            $oDadosDetalhe30->NumeroDocumentoOrdenador     = str_pad($oDados->numerodocumentoordenador, 11, "0");
                            $oDadosDetalhe30->DataEfetivaTransferência     = $this->formataData($oDados->c70_data);
                            $oDadosDetalhe30->JustificativaQuebraOrdenador = str_pad($oDados->e09_justificativa, 255, "0");
                            $oDadosDetalhe30->Brancos1                     = str_pad("", 7, " ");
                            $oDadosDetalhe30->NumRegistroLido     = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
                
                            $oDadosDetalhe30->codigolinha         = 2000767;
                            $this->aDados[] = $oDadosDetalhe30;

                            if ($lDebug) {
                                $sLinhaDetalhe30 = $oDadosDetalhe30->TipRegistro
                                  .$oDadosDetalhe30->BimReferencia
                                  .$oDadosDetalhe30->ProcessoDespesa
                                  .$oDadosDetalhe30->NumeroEmpenho
                                  .$oDadosDetalhe30->NumeroProcesso
                                  .$oDadosDetalhe30->ClassInstitucional
                                  .$oDadosDetalhe30->NumeroDocLiquid
                                  .$oDadosDetalhe30->DomicilioBancario
                                  .$oDadosDetalhe30->EspecieDocumentoPag
                                  .$oDadosDetalhe30->NumeroDocumentoPag
                                  .$oDadosDetalhe30->DataDocumentoPag
                                  .$oDadosDetalhe30->TipoDocumentoCredor
                                  .$oDadosDetalhe30->DocumentoCredor
                                  .$oDadosDetalhe30->ValorPago
                                  .$oDadosDetalhe30->CodigoRetencao
                                  .$oDadosDetalhe30->NumeroDocumentoOrdenador
                                  .$oDadosDetalhe30->DataEfetivaTransferência
                                  .$oDadosDetalhe30->JustificativaQuebraOrdenador
                                  .$oDadosDetalhe30->Brancos1
                                  .$oDadosDetalhe30->NumRegistroLido;
                                fputs($arqFinal, $sLinhaDetalhe30."\r\n");
                            }
                        }
                    }
                }
            }
        
        
          /*
           * DETALHE 40
           * ANULACOES DE EMPENHOS
           */
          //Array de empenhos que nao devem ser incluidos
            $EmpAnuNotIn = array("");
            $aAnulEmpImportados = array("");
            for ($iIndAnu = 0; $iIndAnu < $iLinhasDados; $iIndAnu++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndAnu);

                $sMotivoAnul = "";
            
                if (($oDados->c57_sequencial != 11) || in_array($oDados->c70_codlan, $aAnulEmpImportados)) {
                    continue;
                }

                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }

                $sMotivoAnul = DBString::removerCaracteresEspeciais(substr($oDados->e94_motivo, 0, 50));

                $nLinhasArquivo++;
            
                $aAnulEmpImportados[] = $oDados->c70_codlan;
            
                $oDadosDetalhe40 = new stdClass();
                $oDadosDetalhe40->TipRegistro        = "40";
                $oDadosDetalhe40->BimReferencia      = $this->sBimReferencia;
                $oDadosDetalhe40->ProcessoDespesa    = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe40->NumeroEmpenho      = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe40->ClassInstitucional = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe40->NotaAnulacao       = str_pad($oDados->c70_codlan, 12, "0", STR_PAD_LEFT);
                $oDadosDetalhe40->Brancos1           = " ";
                $oDadosDetalhe40->ValorAnulado       = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe40->MotivoAnulacao     = str_pad(($MotivoAnul ? $MotivoAnul : "Motivo nao informado"), 50, " ", STR_PAD_RIGHT);
                $oDadosDetalhe40->DataAnulacao       = $this->formataData($oDados->c70_data);
                $oDadosDetalhe40->Brancos2           = str_repeat(" ", 309);
                $oDadosDetalhe40->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);

                $oDadosDetalhe40->codigolinha     = 2000768;

                $this->aDados[] = $oDadosDetalhe40;

                if (lDebug) {
                    $sLinhaDetalhe40 = $oDadosDetalhe40->TipRegistro
                                .$oDadosDetalhe40->BimReferencia
                                .$oDadosDetalhe40->ProcessoDespesa
                                .$oDadosDetalhe40->NumeroEmpenho
                                .$oDadosDetalhe40->ClassInstitucional
                                .$oDadosDetalhe40->NotaAnulacao
                                .$oDadosDetalhe40->Brancos1
                                .$oDadosDetalhe40->ValorAnulado
                                .$oDadosDetalhe40->MotivoAnulacao
                                .$oDadosDetalhe40->DataAnulacao
                                .$oDadosDetalhe40->Brancos2
                                .$oDadosDetalhe40->NumRegistroLido;
                    fputs($arqFinal, $sLinhaDetalhe40."\r\n");
                }
            }
        
          /*
           * DETALHE 50
           * ANULACOES DE LIQUIDACAO
           */
            $aAnulLiqImportados = array("");
            for ($iIndAnuLiq = 0; $iIndAnuLiq < $iLinhasDados; $iIndAnuLiq++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndAnuLiq);
          
                if (($oDados->c57_sequencial != 21) || in_array($oDados->c70_codlan, $aAnulLiqImportados)) {
                    continue;
                }

                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }
        
                $nLinhasArquivo++;

                $aAnulLiqImportados[] = $oDados->c70_codlan;

                $oDadosDetalhe50 = new stdClass();
                $oDadosDetalhe50->TipRegistro        = "50";
                $oDadosDetalhe50->BimReferencia      = $this->sBimReferencia;
                $oDadosDetalhe50->ProcessoDespesa    = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe50->NumeroEmpenho      = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe50->ClassInstitucional = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe50->NumeroDocLiquid    = str_pad($oDados->e69_codnota, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe50->NotaAnulacao       = str_pad($oDados->c70_codlan, 12, "0", STR_PAD_RIGHT);
                $oDadosDetalhe50->Brancos1           = " ";
                $oDadosDetalhe50->ValorAnulado       = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe50->MotivoAnulacao     = str_pad(DBString::removerCaracteresEspeciais(substr($oDados->c72_complem, 0, 50)), 50, "0", STR_PAD_LEFT);
                $oDadosDetalhe50->DataAnulacao       = $this->formataData($oDados->c70_data);
                $oDadosDetalhe50->Brancos2           = str_pad("", 294, " ", STR_PAD_LEFT);
                $oDadosDetalhe50->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe50->codigolinha        = 2000769;
        
                $this->aDados[] = $oDadosDetalhe50;
        
                if ($lDebug) {
                    $sLinhaDetalhe50 = $oDadosDetalhe50->TipRegistro
                              .$oDadosDetalhe50->BimReferencia
                              .$oDadosDetalhe50->ProcessoDespesa
                              .$oDadosDetalhe50->NumeroEmpenho
                              .$oDadosDetalhe50->ClassInstitucional
                              .$oDadosDetalhe50->NumeroDocLiquid
                              .$oDadosDetalhe50->NotaAnulacao
                              .$oDadosDetalhe50->Brancos1
                              .$oDadosDetalhe50->ValorAnulado
                              .$oDadosDetalhe50->MotivoAnulacao
                              .$oDadosDetalhe50->DataAnulacao
                              .$oDadosDetalhe50->Brancos2
                              .$oDadosDetalhe50->NumRegistroLido;
                    fputs($arqFinal, $sLinhaDetalhe50."\r\n");
                }
            }
        
          /*
           * DETALHE 60
           * ANULACOES DE PAGAMENTOS
           */
          //Como nao existe tabela de anulacao de pagamento, utilizamos os dados dos lancamentos
            $aAnulPagImportados = array("");
            for ($iIndAnuPag = 0; $iIndAnuPag < $iLinhasDados; $iIndAnuPag++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndAnuPag);
            
                if (($oDados->c57_sequencial != 31) || in_array($oDados->c70_codlan, $aAnulPagImportados)) {
                    continue;
                }
                
                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }

                $nLinhasArquivo++;

                $aAnulPagImportados[] = $oDados->c70_codlan;
            
                $oDadosDetalhe60 = new stdClass();
                $oDadosDetalhe60->TipRegistro        = "60";
                $oDadosDetalhe60->BimReferencia      = $this->sBimReferencia;
                $oDadosDetalhe60->ProcessoDespesa    = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe60->NumeroEmpenho      = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe60->ClassInstitucional = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe60->NumeroDocLiquid    = str_pad($oDados->e71_codnota, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe60->NumeroDocumentoPag = str_pad($oDados->e71_codord, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe60->NotaAnulacao       = str_pad($oDados->c70_codlan, 12, "0", STR_PAD_RIGHT);
                $oDadosDetalhe60->Brancos1           = " ";
                $oDadosDetalhe60->ValorAnulado       = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe60->MotivoAnulacao     = str_pad(DBString::removerCaracteresEspeciais(substr($oDados->c72_complem, 0, 50)), 50, "0", STR_PAD_LEFT);
                $oDadosDetalhe60->DataAnulacao       = $this->formataData($oDados->c70_data);
                $oDadosDetalhe60->Brancos2           = str_repeat(" ", 284);
                $oDadosDetalhe60->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);

                $oDadosDetalhe60->codigolinha     = 2000769;

                $this->aDados[] = $oDadosDetalhe60;
            
                if ($lDebug) {
                    $sLinhaDetalhe60 = $oDadosDetalhe60->TipRegistro
                                 .$oDadosDetalhe60->BimReferencia
                                 .$oDadosDetalhe60->ProcessoDespesa
                                 .$oDadosDetalhe60->NumeroEmpenho
                                 .$oDadosDetalhe60->ClassInstitucional
                                 .$oDadosDetalhe60->NumeroDocLiquid
                                 .$oDadosDetalhe60->NumeroDocumentoPag
                                 .$oDadosDetalhe60->NotaAnulacao
                                 .$oDadosDetalhe60->Brancos1
                                 .$oDadosDetalhe60->ValorAnulado
                                 .$oDadosDetalhe60->MotivoAnulacao
                                 .$oDadosDetalhe60->DataAnulacao
                                 .$oDadosDetalhe60->Brancos2
                                 .$oDadosDetalhe60->NumRegistroLido;
                    fputs($arqFinal, $sLinhaDetalhe60."\r\n");
                }
            }
        }
    
      //TRAILLER

        $nLinhasArquivo++;
        $oDadosTrailler = new stdClass();
     
        $oDadosTrailler->TipRegistro     = "90";
        $oDadosTrailler->Brancos         = str_repeat(" ", 448);
        $oDadosTrailler->NumRegistroLido = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
        
        $oDadosTrailler->codigolinha     = 2000770;
    
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
