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
        $oDaoEmpempenho = new cl_empempenho();
    
        $arqFinal = fopen("tmp/{$sNomeArquivo}.txt", 'w+');
    
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
    
        $sCampos  = " orcdotacao.o58_orgao,                          ";
        $sCampos .= " orcdotacao.o58_unidade,                        ";
        $sCampos .= " empempenho.e60_codemp,                         ";
        $sCampos .= " empempenho.e60_anousu,                         ";
        $sCampos .= " coalesce(empempenho.e60_resumo, 'Justificativa não informada') as e60_resumo, ";
        $sCampos .= " orcdotacao.o58_projativ,                       ";
        $sCampos .= " (CASE
                    WHEN orcdotacao.o58_anousu < 2017 THEN CAST(orcdotacao.o58_codigo AS VARCHAR(10))
                    ELSE (SELECT fonte_tce FROM plugins.deparafontetce WHERE orgao = orcdotacao.o58_orgao
                                                                         AND unidade = orcdotacao.o58_unidade
                                                                         AND fonte_orcamento = orcdotacao.o58_codigo
                                                                         AND exercicio = orcdotacao.o58_anousu
                                                                         AND instit = orcdotacao.o58_instit)
                  END) o58_codigo,                         ";
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
                   group by chave, z01_cgccpf) T1) as numerodocumentoordenador";
        $sWhereBuscaEmpenhos  = "empempenho.e60_anousu = {$iAnoSessao}                                                 ";
        $sWhereBuscaEmpenhos .= " and empempenho.e60_emiss between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' ";
        $sWhereBuscaEmpenhos .= " and empempenho.e60_instit = {$iInstituicaoSessao}                                    ";
        $sWhereBuscaEmpenhos .= " and e60_codtipo <> 4 ";
        $sWhereBuscaEmpenhos .= " and o58_coddot {$sCodDotWhere} ";
        $sWhereBuscaEmpenhos .= " {$sOrcDotacaoWhere} ";
    
        $sSqlEmpenhos         = $oDaoEmpempenho->sql_query_buscaempenhos(null, $sCampos, null, $sWhereBuscaEmpenhos);
      //die($sSqlEmpenhos);
        $rsSqlBuscaEmpenhos   = $oDaoEmpempenho->sql_record($sSqlEmpenhos);
        $iLinhasEmpenhos      = $oDaoEmpempenho->numrows;
      /*
       * HEADER
       */
        $oDadosHeader = new stdClass();
        $oDadosHeader->TipRegistro        = "0";
        $oDadosHeader->NomeArquivo        = str_pad($sNomeArquivo, 10, " ", STR_PAD_RIGHT);
        $oDadosHeader->BimReferencia      = $this->sBimReferencia;
        $oDadosHeader->TipoArquivo        = "O";
        $oDadosHeader->DataGeracaoArq     = $this->dtDataGeracao;
        $oDadosHeader->HoraGeracaoArq     = $this->dtHoraGeracao;
        $oDadosHeader->CodigoOrgao        = $this->codigoOrgaoTCE;
        $oDadosHeader->NomeUnidade        = str_pad(substr($this->nomeUnidade, 0, 100), 100, " ", STR_PAD_RIGHT);
        $oDadosHeader->Brancos1           = str_repeat(" ", 1);
        $oDadosHeader->FornecedorSoftware = str_pad("SOFTWARE PUBLICO BRASILEIRO", 100, " ", STR_PAD_LEFT);
        $oDadosHeader->Brancos2           = str_repeat(" ", 166);
        $oDadosHeader->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
    
        $this->aDados[] = $oDadosHeader;
    
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
    
        if ($iLinhasEmpenhos > 0) {
            /*
             * DETALHE 1
             * EMPENHOS
             */
            $aEmpImportados = array("");
            for ($iInd = 0; $iInd < $iLinhasEmpenhos; $iInd++) {
                $oDados = db_utils::fieldsMemory($rsSqlBuscaEmpenhos, $iInd);

                if (in_array($oDados->e60_codemp, $aEmpImportados)) {
                    continue;
                }

                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }
            
                $nLinhasArquivo++;
            
                $aEmpImportados[] = $oDados->e60_codemp;

                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33913900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33913999" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33303900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "31919700" ? "331919900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33313900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33503900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "33603900" ? "333903900" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "44104100" ? "344204100" : $oDados->o56_elemento;
                $oDados->o56_elemento = substr($oDados->o56_elemento, 1, 8) == "44505100" ? "344805100" : $oDados->o56_elemento;

                $oDadosDetalhe1 = new stdClass();
                $oDadosDetalhe1->TipoRegistro                  = "1";
                $oDadosDetalhe1->BimReferencia                 = $this->sBimReferencia;
                $oDadosDetalhe1->ProcessoDespesa               = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->CodigoProcedimentoDespesa     = str_pad($oDados->codigolicitacaotribunal, 4, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->NumeroReciboProcessoLicitacao = str_pad($oDados->numeroprocessolicitatorio, 10, " ", STR_PAD_LEFT);
                $oDadosDetalhe1->DataProcedimentoLicitatorio   = $this->formataData("2016-06-01");//str_pad("", 10 , " ");
                $oDadosDetalhe1->NumeroEmpenho                 = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->TipoEmpenho                   = str_pad($oDados->e60_codtipo ? $oDados->e60_codtipo : "E", 1, " ");
                $oDadosDetalhe1->DataEmpenho                   = $this->formataData($oDados->e60_emiss);
                $oDadosDetalhe1->ValorEmpenhado                = $this->formataValor($oDados->e60_vlremp, 14, "0");
                $oDadosDetalhe1->TipoDocumentoCredor           = strlen($oDados->z01_cgccpf) == 11 ? "0" : "1";
                $oDadosDetalhe1->NumeroDocumentoCredor         = str_pad(($oDados->z01_cgccpf == "99999999999999" ? "08241747000143" : $oDados->z01_cgccpf), 14, " ", STR_PAD_LEFT);
                $oDadosDetalhe1->ClassInstitucional            = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe1->ClassFuncional                = str_pad($oDados->o58_funcao, 2, "0", STR_PAD_LEFT).str_pad($oDados->o58_subfuncao, 3, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->ClassProgramatica             = str_pad(str_pad($oDados->o58_programa, 5, "0", STR_PAD_LEFT).str_pad($oDados->o58_projativ, 5, "0", STR_PAD_LEFT), 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->NaturezaDespesa               = substr($oDados->o56_elemento, 1, 8);
                $oDadosDetalhe1->TipoCadastro                  = "0";
                $oDadosDetalhe1->FonteRecurso                  = str_pad($oDados->o58_codigo, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->NumeroDocumentoOrdenador      = str_pad($oDados->numerodocumentoordenador, 11, " ");
                $oDadosDetalhe1->TipoRecursoVinculado          = "001";
                $oDadosDetalhe1->Justificativa                 = str_pad(substr(DBString::removerCaracteresEspeciais(empty($oDados->e60_resumo) ? "Justificativa não informada" : DBString::removerCaracteresEspeciais(str_replace(array("\r", "\n"), " ", $oDados->e60_resumo))), 0, 254), 254, " ");
                $oDadosDetalhe1->Brancos                       = str_repeat(" ", 31);
                $oDadosDetalhe1->NumRegistroLido               = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
            
                $this->aDados[] = $oDadosDetalhe1;

                $sLinhaDetalhe1 = $oDadosDetalhe1->TipoRegistro                 .
                                $oDadosDetalhe1->BimReferencia                .
                                $oDadosDetalhe1->ProcessoDespesa              .
                                $oDadosDetalhe1->CodigoProcedimentoDespesa    .
                                $oDadosDetalhe1->NumeroReciboProcessoLicitacao.
                                $oDadosDetalhe1->DataProcedimentoLicitatorio  .
                                $oDadosDetalhe1->NumeroEmpenho                .
                                $oDadosDetalhe1->TipoEmpenho                  .
                                $oDadosDetalhe1->DataEmpenho                  .
                                $oDadosDetalhe1->ValorEmpenhado               .
                                $oDadosDetalhe1->TipoDocumentoCredor          .
                                $oDadosDetalhe1->NumeroDocumentoCredor        .
                                $oDadosDetalhe1->ClassInstitucional           .
                                $oDadosDetalhe1->ClassFuncional               .
                                $oDadosDetalhe1->ClassProgramatica            .
                                $oDadosDetalhe1->NaturezaDespesa              .
                                $oDadosDetalhe1->TipoCadastro                 .
                                $oDadosDetalhe1->FonteRecurso                 .
                                $oDadosDetalhe1->NumeroDocumentoOrdenador     .
                                $oDadosDetalhe1->TipoRecursoVinculado         .
                                $oDadosDetalhe1->Justificativa                .
                                $oDadosDetalhe1->Brancos                      .
                                $oDadosDetalhe1->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe1."\r\n");
            }
        }
    
    
    
      /*
       * Busca os dados dos lancamentos dos empenhos
       */
        $sSqlDados = "select *,
                         (select T1.cpf from (select max(plugins.documentoassinaturaordenadordespesa.sequencial), z01_cgccpf as cpf
                                                from plugins.documentoassinaturaordenadordespesa 
                                                     inner join plugins.assinaturaordenadordespesa on assinaturaordenadordespesa.sequencial = documentoassinaturaordenadordespesa.assinaturaordenadordespesa
                                                     inner join cgm on z01_numcgm = plugins.assinaturaordenadordespesa.numcgm
                                               where documentoassinaturaordenadordespesa.chave = c75_numemp 
                                                 and documentoassinaturaordenadordespesa.tipo = 1
                                               group by chave, z01_cgccpf) T1) as numerodocumentoordenador,
                         (select e150_numeroprocesso 
                            from empautorizaprocesso inner join empempaut 
                                        on e150_empautoriza = e61_autori
                            where e61_numemp = e60_numemp limit 1) as processo_despesa,
                         case 
                           when pagordemconta.e49_codord is not null 
                             then cgm_pagordemconta.z01_cgccpf
                           else cgm.z01_cgccpf
                         end as documento_credor,
                         (select max(pg.e50_codord) from pagordem pg where pg.e50_numemp = e60_numemp) as NumOB
                   from conlancam 
                        inner join conlancamdoc             on c71_codlan                   = c70_codlan 
                        inner join conhistdoc               on c53_coddoc                   = c71_coddoc 
                        inner join conhistdoctipo           on c57_sequencial               = c53_tipo 
                        inner join conlancamemp             on c75_codlan                   = c70_codlan 
                        inner join empempenho               on e60_numemp                   = c75_numemp
                        inner join cgm                      on z01_numcgm                   = e60_numcgm
                        inner join orcdotacao               on o58_coddot                   = e60_coddot
                                                           and o58_anousu                   = e60_anousu
                         left join conlancamcorgrupocorrente on c23_conlancam               = c70_codlan
                         left join corgrupocorrente         on k105_sequencial              = c23_corgrupocorrente 
                         left join conlancamnota            on c66_codlan                   = c70_codlan 
                         left join conlancampag             on c82_codlan                   = c70_codlan
                         left join conlancamcompl           on c70_codlan                   = c72_codlan
                         left join conplanoreduz            on c61_reduz                    = c82_reduz
                                                           and c61_anousu                   = c82_anousu
                         left join conplano                 on c60_codcon                   = c61_codcon
                                                           and c60_anousu                   = c61_anousu
                         left join conplanoconta            on c63_codcon                   = c60_codcon
                                                           and c63_anousu                   = c60_anousu
                         left join empnota                  on e69_codnota                  = c66_codnota 
                         left join conlancamord             on c80_codlan                   = c70_codlan 
                         left join pagordem pg2             on pg2.e50_codord               = c80_codord
                         left join pagordemnota             on e71_codord                   = c80_codord 
                         left join pagordemconta            on e49_codord                   = pg2.e50_codord
                         left join cgm as cgm_pagordemconta on cgm_pagordemconta.z01_numcgm = e49_numcgm 
                         left join empanulado               on e94_numemp                   = e60_numemp
                   where c70_data between '{$this->dtDataInicial}' and '{$this->dtDataFinal}' 
                     and c57_sequencial in (11,20,21,30,31) 
                     and c70_anousu = {$iAnoSessao}
                     and e60_codtipo <> 4
                     and e60_instit = {$iInstituicaoSessao}
                     and e60_emiss >= '2016-01-01'
                     and e60_anousu = {$iAnoSessao}
                     {$sOrcDotacaoWhere}";

        $rsDados  =  db_query($sSqlDados);
        $iLinhasDados =  pg_num_rows($rsDados);
        if ($iLinhasDados > 0) {
          /*
           * DETALHE 2
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
        
                $oDadosDetalhe2 = new stdClass();
                $oDadosDetalhe2->TipRegistro         = "2";
                $oDadosDetalhe2->BimReferencia       = $this->sBimReferencia;
                $oDadosDetalhe2->ProcessoDespesa     = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->NumeroEmpenho       = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->NumeroProcesso      = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->ClassInstitucional  = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe2->CodDocFiscal        = str_pad(substr($oDados->e69_tipodocumentosfiscal, 0, 4), 4, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->NumDocFiscal        = str_pad(substr((!is_int($oDados->e69_numero) ? "" : $oDados->e69_numero), 0, 9), 9, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->SerieFiscal         = str_pad("", 3, "0", STR_PAD_LEFT); // @todo Hélio - Verificar
                $oDadosDetalhe2->DataEmissaoDocFis   = $this->formataData($oDados->e69_dtnota);//str_pad("",  10, "0",STR_PAD_LEFT); // @todo Hélio - Verificar
                $oDadosDetalhe2->ChaveFiscal         = str_pad("", 44, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->ValorFaturado       = str_pad("", 14, "0", STR_PAD_LEFT); // @todo Hélio - Verificar
                $oDadosDetalhe2->NumeroDocLiquid     = str_pad($oDados->e69_codnota, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->DataLiquidacao      = $this->formataData($oDados->c70_data);
                $oDadosDetalhe2->ValorLiquidado      = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe2->TipoDocumentoCredor = strlen($oDados->documento_credor) == 11 ? "0" : "1";
                $oDadosDetalhe2->DocumentoCredor     = str_pad(($oDados->documento_credor == "99999999999999" ? "08241747000143" : $oDados->documento_credor), 14, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->DocumentoRespons    = str_pad($oDados->numerodocumentoordenador, 11, " ", STR_PAD_LEFT);
                $oDadosDetalhe2->Brancos1            = str_repeat(" ", 228);
                $oDadosDetalhe2->NumRegistroLido     = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
      
                $this->aDados[] = $oDadosDetalhe2;
      
                $sLinhaDetalhe2 = $oDadosDetalhe2->TipRegistro        .
                                  $oDadosDetalhe2->BimReferencia      .
                                  $oDadosDetalhe2->ProcessoDespesa    .
                                  $oDadosDetalhe2->NumeroEmpenho      .
                                  $oDadosDetalhe2->NumeroProcesso     .
                                  $oDadosDetalhe2->ClassInstitucional .
                                  $oDadosDetalhe2->CodDocFiscal       .
                                  $oDadosDetalhe2->NumDocFiscal       .
                                  $oDadosDetalhe2->SerieFiscal        .
                                  $oDadosDetalhe2->DataEmissaoDocFis  .
                                  $oDadosDetalhe2->ChaveFiscal        .
                                  $oDadosDetalhe2->ValorFaturado      .
                                  $oDadosDetalhe2->NumeroDocLiquid    .
                                  $oDadosDetalhe2->DataLiquidacao     .
                                  $oDadosDetalhe2->ValorLiquidado     .
                                  $oDadosDetalhe2->TipoDocumentoCredor.
                                  $oDadosDetalhe2->DocumentoCredor    .
                                  $oDadosDetalhe2->DocumentoRespons   .
                                  $oDadosDetalhe2->Brancos1           .
                                  $oDadosDetalhe2->NumRegistroLido     ;
                fputs($arqFinal, $sLinhaDetalhe2."\r\n");
            }
      
          /*
           * DETALHE 3
           * PAGAMENTOS
           */
            $aPagamentosImportados = array("");
            for ($iIndPag = 0; $iIndPag < $iLinhasDados; $iIndPag++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndPag);
        
              //Somente vai importar se for pagamento, se nao for uma retencao e se nao tiver sido importado
                if ($oDados->k105_corgrupotipo != 2
                 && $oDados->c57_sequencial == 30
                 && !in_array($oDados->e50_codord, $aPagamentosImportados)
                ) {
                    $nLinhasArquivo++;
          
                    $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                    if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                        $codUnidade .= "220";
                    } else {
                        $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                    }

                    $sDomicilioBancario  = str_pad(substr($oDados->c63_banco, 0, 3), 3, " ", STR_PAD_LEFT);
                    $sDomicilioBancario .= str_pad(substr($oDados->c63_agencia.$oDados->c63_dvagencia, 0, 6), 6, " ", STR_PAD_LEFT);
                    $sDomicilioBancario .= str_pad(substr($oDados->c63_conta.$oDados->c63_dvconta, 0, 13), 13, " ", STR_PAD_LEFT);
          
                    $oDadosDetalhe3 = new stdClass();
                    $oDadosDetalhe3->TipRegistro         = "3";
                    $oDadosDetalhe3->BimReferencia       = $this->sBimReferencia;
                    $oDadosDetalhe3->ProcessoDespesa     = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                    $oDadosDetalhe3->NumeroEmpenho       = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                    $oDadosDetalhe3->NumeroProcesso      = str_pad($oDados->processo_despesa, 20, " ", STR_PAD_LEFT);
                    $oDadosDetalhe3->ClassInstitucional  = str_pad($codUnidade, 11, " ");
                    $oDadosDetalhe3->NumeroDocLiquid     = str_pad($oDados->e71_codnota, 15, "0", STR_PAD_LEFT);
                    $oDadosDetalhe3->DomicilioBancario   = $sDomicilioBancario;
                    $oDadosDetalhe3->EspecieDocumentoPag = "001";
                    $oDadosDetalhe3->NumeroDocumentoPag  = str_pad($oDados->e50_codord, 10, "0", STR_PAD_LEFT);
                    $oDadosDetalhe3->DataDocumentoPag    = $this->formataData($oDados->c70_data);
                    $oDadosDetalhe3->TipoDocumentoCredor = strlen($oDados->documento_credor) == 11 ? "0" : "1";
                    $oDadosDetalhe3->DocumentoCredor     = str_pad(($oDados->documento_credor == "99999999999999" ? "08241747000143" : $oDados->documento_credor), 14, "0", STR_PAD_LEFT);
                    $oDadosDetalhe3->ValorPago           = $this->formataValor($oDados->c70_valor, 14, "0");
                    $oDadosDetalhe3->CodigoRetencao      = str_pad("", 3, "0");
                    $oDadosDetalhe3->Brancos1            = str_pad("", 285, " ");
                    $oDadosDetalhe3->NumRegistroLido     = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
            
                    $this->aDados[] = $oDadosDetalhe3;
          
                    $aPagamentosImportados[] = $oDados->e50_codord;

                    $sLinhaDetalhe3 = $oDadosDetalhe3->TipRegistro
                              .$oDadosDetalhe3->BimReferencia
                              .$oDadosDetalhe3->ProcessoDespesa
                              .$oDadosDetalhe3->NumeroEmpenho
                              .$oDadosDetalhe3->NumeroProcesso
                              .$oDadosDetalhe3->ClassInstitucional
                              .$oDadosDetalhe3->NumeroDocLiquid
                              .$oDadosDetalhe3->DomicilioBancario
                              .$oDadosDetalhe3->EspecieDocumentoPag
                              .$oDadosDetalhe3->NumeroDocumentoPag
                              .$oDadosDetalhe3->DataDocumentoPag
                              .$oDadosDetalhe3->TipoDocumentoCredor
                              .$oDadosDetalhe3->DocumentoCredor
                              .$oDadosDetalhe3->ValorPago
                              .$oDadosDetalhe3->CodigoRetencao
                              .$oDadosDetalhe3->Brancos1
                              .$oDadosDetalhe3->NumRegistroLido;
                    fputs($arqFinal, $sLinhaDetalhe3."\r\n");
                }

                if ($oDados->k105_corgrupotipo == 2
                && $oDados->c57_sequencial == 30
                ) {
                    $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                    if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                        $codUnidade .= "220";
                    } else {
                        $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                    }
                  //Busca as retencoes da ordem e insere
                    $sSqlRetencoes = "SELECT * 
                              FROM retencaopagordem
                                   INNER JOIN retencaoreceitas ON e23_retencaopagordem = e20_sequencial
                                                              AND e23_ativo = 't'
                                                              AND e23_recolhido = 't'
                             WHERE e20_pagordem = {$oDados->e50_codord} 
                               and e23_dtcalculo between '{$this->dtDataInicial}' and '{$this->dtDataFinal}'";
                    $rsRetencoes = db_query($sSqlRetencoes);

                    if (pg_num_rows($rsRetencoes) > 0) {
                        for ($i=0; $i<pg_num_rows($rsRetencoes); $i++) {
                            $nLinhasArquivo++;
              
                            $oDadosRetencoes = db_utils::fieldsMemory($rsRetencoes, $i);
              
                            $oDadosDetalhe3 = new stdClass();
                            $oDadosDetalhe3->TipRegistro         = "3";
                            $oDadosDetalhe3->BimReferencia       = $this->sBimReferencia;
                            $oDadosDetalhe3->ProcessoDespesa     = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                            $oDadosDetalhe3->NumeroEmpenho       = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                            $oDadosDetalhe3->NumeroProcesso      = str_pad($oDados->processo_despesa, 20, " ", STR_PAD_LEFT);
                            $oDadosDetalhe3->ClassInstitucional  = str_pad($codUnidade, 11, " ");
                            $oDadosDetalhe3->NumeroDocLiquid     = str_pad($oDados->e71_codnota, 15, "0", STR_PAD_LEFT);
                            $oDadosDetalhe3->DomicilioBancario   = $sDomicilioBancario;
                            $oDadosDetalhe3->EspecieDocumentoPag = "001";
                            $oDadosDetalhe3->NumeroDocumentoPag  = str_pad($oDados->e50_codord, 10, "0", STR_PAD_LEFT);
                            $oDadosDetalhe3->DataDocumentoPag    = $this->formataData($oDados->c70_data);
                            $oDadosDetalhe3->TipoDocumentoCredor = strlen($oDados->documento_credor) == 11 ? "0" : "1";
                            $oDadosDetalhe3->DocumentoCredor     = str_pad(($oDados->documento_credor == "99999999999999" ? "08241747000143" : $oDados->documento_credor), 14, "0", STR_PAD_LEFT);
                            $oDadosDetalhe3->ValorPago           = $this->formataValor($oDadosRetencoes->e23_valorretencao, 14, "0");
                            $oDadosDetalhe3->CodigoRetencao      = str_pad("", 3, "0");//str_pad($oDadosRetencoes->e23_retencaotiporec, 3, "0");
                            $oDadosDetalhe3->Brancos1            = str_pad("", 285, " ");
                            $oDadosDetalhe3->NumRegistroLido     = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
                
                            $this->aDados[] = $oDadosDetalhe3;

                            $sLinhaDetalhe3 = $oDadosDetalhe3->TipRegistro
                                  .$oDadosDetalhe3->BimReferencia
                                  .$oDadosDetalhe3->ProcessoDespesa
                                  .$oDadosDetalhe3->NumeroEmpenho
                                  .$oDadosDetalhe3->NumeroProcesso
                                  .$oDadosDetalhe3->ClassInstitucional
                                  .$oDadosDetalhe3->NumeroDocLiquid
                                  .$oDadosDetalhe3->DomicilioBancario
                                  .$oDadosDetalhe3->EspecieDocumentoPag
                                  .$oDadosDetalhe3->NumeroDocumentoPag
                                  .$oDadosDetalhe3->DataDocumentoPag
                                  .$oDadosDetalhe3->TipoDocumentoCredor
                                  .$oDadosDetalhe3->DocumentoCredor
                                  .$oDadosDetalhe3->ValorPago
                                  .$oDadosDetalhe3->CodigoRetencao
                                  .$oDadosDetalhe3->Brancos1
                                  .$oDadosDetalhe3->NumRegistroLido;
                            fputs($arqFinal, $sLinhaDetalhe3."\r\n");
                        }
                    }
                }
            }
        
        
          /*
           * DETALHE 4
           * ANULAÇÕES DE EMPENHOS
           */
          //Array de empenhos que não devem ser incluí­dos
            $EmpAnuNotIn = array("");
            $aAnulEmpImportados = array("");
            for ($iIndAnu = 0; $iIndAnu < $iLinhasDados; $iIndAnu++) {
                $oDados = db_utils::fieldsMemory($rsDados, $iIndAnu);

                if (($oDados->c57_sequencial != 11) || in_array($oDados->c70_codlan, $aAnulEmpImportados)) {
                    continue;
                }

                $codUnidade = str_pad($oDados->o58_orgao, 2, "0", STR_PAD_LEFT);
                if ($oDados->o58_orgao == "24" && $oDados->o58_unidade == "20") {
                    $codUnidade .= "220";
                } else {
                    $codUnidade .= str_pad($oDados->o58_unidade, 2, "0", STR_PAD_LEFT);
                }

                $sMotivoAnul = "";
                $sMotivoAnul = DBString::removerCaracteresEspeciais(substr($oDados->e94_motivo, 0, 50));

                $nLinhasArquivo++;
            
                $oDadosDetalhe4 = new stdClass();
                $oDadosDetalhe4->TipRegistro        = "4";
                $oDadosDetalhe4->BimReferencia      = $this->sBimReferencia;
                $oDadosDetalhe4->ProcessoDespesa    = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->NumeroEmpenho      = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->ClassInstitucional = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe4->NotaAnulacao       = str_pad($oDados->c70_codlan, 12, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->Brancos1           = " ";
                $oDadosDetalhe4->ValorAnulado       = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe4->MotivoAnulacao     = str_pad((!empty($MotivoAnul)?$MotivoAnul : "Motivo nao informado"), 50, " ", STR_PAD_RIGHT);
                $oDadosDetalhe4->DataAnulacao       = $this->formataData($oDados->c70_data);
                $oDadosDetalhe4->Brancos2           = str_repeat(" ", 310);
                $oDadosDetalhe4->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);

                $aAnulEmpImportados[] = $oDados->c70_codlan;
            
                $this->aDados[] = $oDadosDetalhe4;

                $sLinhaDetalhe4 = $oDadosDetalhe4->TipRegistro
                                .$oDadosDetalhe4->BimReferencia
                                .$oDadosDetalhe4->ProcessoDespesa
                                .$oDadosDetalhe4->NumeroEmpenho
                                .$oDadosDetalhe4->ClassInstitucional
                                .$oDadosDetalhe4->NotaAnulacao
                                .$oDadosDetalhe4->Brancos1
                                .$oDadosDetalhe4->ValorAnulado
                                .$oDadosDetalhe4->MotivoAnulacao
                                .$oDadosDetalhe4->DataAnulacao
                                .$oDadosDetalhe4->Brancos2
                                .$oDadosDetalhe4->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe4."\r\n");
            }
        
          /*
           * DETALHE 5
           * ANULAÇÕES DE LIQUIDACAO
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
        
                $aAnulLiqImportados[] = $oDados->c70_codlan;
          
                $nLinhasArquivo++;
          
                $oDadosDetalhe5 = new stdClass();
                $oDadosDetalhe5->TipRegistro        = "5";
                $oDadosDetalhe5->BimReferencia      = $this->sBimReferencia;
                $oDadosDetalhe5->ProcessoDespesa    = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->NumeroEmpenho      = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->ClassInstitucional = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe5->NumeroDocLiquid    = str_pad($oDados->e69_codnota, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->NotaAnulacao       = str_pad($oDados->c70_codlan, 12, "0", STR_PAD_RIGHT);
                $oDadosDetalhe5->Brancos1           = " ";
                $oDadosDetalhe5->ValorAnulado       = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe5->MotivoAnulacao     = str_pad(DBString::removerCaracteresEspeciais(substr($oDados->c72_complem, 0, 50)), 50, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->DataAnulacao       = $this->formataData($oDados->c70_data);
                $oDadosDetalhe5->Brancos2           = str_pad("", 295, " ", STR_PAD_LEFT);
                $oDadosDetalhe5->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
        
                $this->aDados[] = $oDadosDetalhe5;
        
                $sLinhaDetalhe5 = $oDadosDetalhe5->TipRegistro
                             .$oDadosDetalhe5->BimReferencia
                             .$oDadosDetalhe5->ProcessoDespesa
                             .$oDadosDetalhe5->NumeroEmpenho
                             .$oDadosDetalhe5->ClassInstitucional
                             .$oDadosDetalhe5->NumeroDocLiquid
                             .$oDadosDetalhe5->NotaAnulacao
                             .$oDadosDetalhe5->Brancos1
                             .$oDadosDetalhe5->ValorAnulado
                             .$oDadosDetalhe5->MotivoAnulacao
                             .$oDadosDetalhe5->DataAnulacao
                             .$oDadosDetalhe5->Brancos2
                             .$oDadosDetalhe5->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe5."\r\n");
            }
        
          /*
           * DETALHE 6
           * ANULAÇÕES DE PAGAMENTOS
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
            
                $oDadosDetalhe6 = new stdClass();
                $oDadosDetalhe6->TipRegistro        = "6";
                $oDadosDetalhe6->BimReferencia      = $this->sBimReferencia;
                $oDadosDetalhe6->ProcessoDespesa    = str_pad($oDados->processo_despesa, 20, "0", STR_PAD_LEFT);
                $oDadosDetalhe6->NumeroEmpenho      = str_pad($oDados->e60_codemp, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe6->ClassInstitucional = str_pad($codUnidade, 11, " ");
                $oDadosDetalhe6->NumeroDocLiquid    = str_pad($oDados->e71_codnota, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe6->NumeroDocumentoPag = str_pad($oDados->e71_codord, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe6->NotaAnulacao       = str_pad($oDados->c70_codlan, 12, "0", STR_PAD_RIGHT);
                $oDadosDetalhe6->Brancos1           = " ";
                $oDadosDetalhe6->ValorAnulado       = $this->formataValor($oDados->c70_valor, 14, "0");
                $oDadosDetalhe6->MotivoAnulacao     = str_pad(DBString::removerCaracteresEspeciais(substr($oDados->c72_complem, 0, 50)), 50, "0", STR_PAD_LEFT);
                $oDadosDetalhe6->DataAnulacao       = $this->formataData($oDados->c70_data);
                $oDadosDetalhe6->Brancos2           = str_repeat(" ", 285);
                $oDadosDetalhe6->NumRegistroLido    = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);

                $this->aDados[] = $oDadosDetalhe6;
            
                $sLinhaDetalhe6 = $oDadosDetalhe6->TipRegistro       .
                                  $oDadosDetalhe6->BimReferencia     .
                                  $oDadosDetalhe6->ProcessoDespesa   .
                                  $oDadosDetalhe6->NumeroEmpenho     .
                                  $oDadosDetalhe6->ClassInstitucional.
                                  $oDadosDetalhe6->NumeroDocLiquid   .
                                  $oDadosDetalhe6->NumeroDocumentoPag.
                                  $oDadosDetalhe6->NotaAnulacao      .
                                  $oDadosDetalhe6->Brancos1          .
                                  $oDadosDetalhe6->ValorAnulado      .
                                  $oDadosDetalhe6->MotivoAnulacao    .
                                  $oDadosDetalhe6->DataAnulacao      .
                                  $oDadosDetalhe6->Brancos2          .
                                  $oDadosDetalhe6->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe6."\r\n");
            }
        }
    
      //TRAILLER
        $nLinhasArquivo++;
        $oDadosTrailler = new stdClass();
     
        $oDadosTrailler->TipRegistro     = "9";
        $oDadosTrailler->Brancos         = str_repeat(" ", 408);
        $oDadosTrailler->NumRegistroLido = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
        
        $this->aDados[] = $oDadosTrailler;
    
        $sLinhaTrailler = $oDadosTrailler->TipRegistro
                       .$oDadosTrailler->Brancos
                       .$oDadosTrailler->NumRegistroLido;
        fputs($arqFinal, $sLinhaTrailler."\r\n");
        fclose($arqFinal);
    }
}
