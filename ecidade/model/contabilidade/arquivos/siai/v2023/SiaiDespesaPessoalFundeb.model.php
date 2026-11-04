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

class SiaiDespesaPessoalFundeb extends SiaiArquivoBase
{
  
  /**
  * Busca os dados para gerar o Arquivo de Empenhos
  */
    public function gerarDados()
    {
  
        $sNomeArquivo = 'A27_'.$this->sBimReferencia;
        $this->setNomeArquivo($sNomeArquivo.".TXT");

        $iAnoSessao         = db_getsession('DB_anousu');
        $iInstituicaoSessao = db_getsession('DB_instit');

        $aPrimeiroSemestre = array("01", "02", "03");
        $this->MesInicioSemestre = in_array(substr($this->sBimReferencia, 4, 2), $aPrimeiroSemestre) ? 1 : 4;
        $this->MesFimSemestre    = in_array(substr($this->sBimReferencia, 4, 2), $aPrimeiroSemestre) ? 3 : 6;
        $nLinhasArquivo = 1;
    
        $arqFinal = fopen("tmp/{$sNomeArquivo}.TXT", 'w+');
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
        $oDadosHeader->Brancos1           = str_repeat(" ", 10);
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
                      .$oDadosHeader->NumRegistroLido ;
        fputs($arqFinal, $sLinhaHeader."\r\n");
    
        $sSqlCargo       = "select distinct rh37_funcao as Codigo,
                               rh37_descr as Descricao
                        from rhfuncao
                        order by 1";
        $rsSqlBuscaCargo = db_query($sSqlCargo);
        $iLinhasCargo    = pg_num_rows($rsSqlBuscaCargo);
    
        if ($iLinhasCargo > 0) {
            /*
             * DETALHE 1
             * CARGO
             */
            for ($iInd = 0; $iInd < $iLinhasCargo; $iInd++) {
                $oDados = db_utils::fieldsMemory($rsSqlBuscaCargo, $iInd);
          
                $nLinhasArquivo++;
            
                $oDadosDetalhe1 = new stdClass();
                $oDadosDetalhe1->TipoRegistro    = "2";
                $oDadosDetalhe1->Codigo          = str_pad($oDados->codigo, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe1->Descricao       = str_pad($oDados->descricao, 60, " ", STR_PAD_RIGHT);
                $oDadosDetalhe1->CodigoTipo      = str_pad("10", 2, " ", STR_PAD_LEFT);
                $oDadosDetalhe1->Brancos         = str_repeat(" ", 77);
                $oDadosDetalhe1->NumRegistroLido = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);

                $this->aDados[] = $oDadosDetalhe1;

                $sLinhaDetalhe1 = $oDadosDetalhe1->TipoRegistro .
                                $oDadosDetalhe1->Codigo       .
                                $oDadosDetalhe1->Descricao    .
                                $oDadosDetalhe1->CodigoTipo   .
                                $oDadosDetalhe1->Brancos      .
                                $oDadosDetalhe1->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe1."\r\n");
            }
        }
    
        $sSqlLotacao    = "select r70_codigo as Codigo,
                              r70_descr  as Descricao
                      from rhlota ";
        $rsLotacao      = db_query($sSqlLotacao);
        $iLinhasLotacao = pg_num_rows($rsLotacao);

        if ($iLinhasLotacao > 0) {
          /*
           * DETALHE 2
           * LOTAÇÃO
           */
            for ($iInd = 0; $iInd < $iLinhasLotacao; $iInd++) {
                $oDados = db_utils::fieldsMemory($rsLotacao, $iInd);
             
                $nLinhasArquivo++;
                $oDadosDetalhe2 = new stdClass();
         
                $oDadosDetalhe2->TipRegistro     = "3";
                $oDadosDetalhe2->Codigo          = str_pad($oDados->codigo, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe2->Descricao       = str_pad($oDados->descricao, 60, " ", STR_PAD_RIGHT);
                $oDadosDetalhe2->Brancos1        = str_repeat(" ", 77);
                $oDadosDetalhe2->NumRegistroLido = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
      
                $this->aDados[] = $oDadosDetalhe2;
      
                $sLinhaDetalhe2 = $oDadosDetalhe2->TipRegistro .
                            $oDadosDetalhe2->Codigo      .
                            $oDadosDetalhe2->Descricao   .
                            $oDadosDetalhe2->Brancos1    .
                            $oDadosDetalhe2->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe2."\r\n");
            }
        }
    
        $sSqlNivelFuncional    = "select r02_codigo as Codigo,
                                     r02_descr  as Descricao
                              from padroes
                              where r02_anousu = {$this->iAno}";
        $rsNivelFuncional      = db_query($sSqlNivelFuncional);
        $iLinhasNivelFuncional = pg_num_rows($rsNivelFuncional);

        if ($iLinhasNivelFuncional > 0) {
          /*
           * DETALHE 3
           * NÍVEL FUNCIONAL
           */
            for ($iInd = 0; $iInd < $iLinhasNivelFuncional; $iInd++) {
                $oDados = db_utils::fieldsMemory($rsNivelFuncional, $iInd);
             
                $nLinhasArquivo++;
                $oDadosDetalhe3 = new stdClass();
         
                $oDadosDetalhe3->TipRegistro     = "4";
                $oDadosDetalhe3->Codigo          = str_pad($oDados->codigo, 4, " ", STR_PAD_LEFT);
                $oDadosDetalhe3->Descricao       = str_pad($oDados->descricao, 40, " ", STR_PAD_RIGHT);
                $oDadosDetalhe3->Brancos1        = str_repeat(" ", 105);
                $oDadosDetalhe3->NumRegistroLido = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
      
                $this->aDados[] = $oDadosDetalhe3;
      
                $sLinhaDetalhe3 = $oDadosDetalhe3->TipRegistro .
                            $oDadosDetalhe3->Codigo      .
                            $oDadosDetalhe3->Descricao   .
                            $oDadosDetalhe3->Brancos1    .
                            $oDadosDetalhe3->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe3."\r\n");
            }
        }

        $sSqlPessoal   = "select x.z01_cgccpf                 as cpf,
                             (case when z01_nomecomple is null or trim(z01_nomecomple) = '' 
                                  then z01_nome
                                  else substr(z01_nomecomple,1,60)
                              end) as nome,
                             z01_ident                    as identidade,
                             rh16_titele                  as titulo_eleitor,
                             rh01_nasc                    as dt_nascimento,
                             z01_escolaridade,
                             (case rh01_instru
                                  when 1  then 1
                                  when 2  then 2
                                  when 3  then 2
                                  when 4  then 2
                                  when 5  then 3
                                  when 6  then 4
                                  when 7  then 5
                                  when 8  then 7
                                  when 9  then 8
                                  when 10 then 10
                                  when 11 then 10
                                  else 11
                             end) as grau_instrucao,
                             rh01_sexo as sexo
                      from (select z01_cgccpf, 
                             max(rh01_regist) as rh01_regist 
                      from rhpessoal 
                           inner join rhpessoalmov    on rh01_regist = rh02_regist 
                           left  join rhpesrescisao   on rh02_seqpes = rh05_seqpes 
                           inner join cgm             on rh01_numcgm = z01_numcgm 
                      where rh05_seqpes is null 
                        and rh02_anousu = {$this->iAno} 
                        and rh02_mesusu = {$this->MesFimSemestre}
                        and rh02_lota in (18, 21, 22, 23, 25) 
                      group by z01_cgccpf) as x
                           inner join rhpessoal           on x.rh01_regist = rhpessoal.rh01_regist
                           left  join rhpesdoc            on x.rh01_regist = rh16_regist
                           inner join cgm                 on z01_numcgm    = rh01_numcgm
                      order by nome";
        $rsPessoal      = db_query($sSqlPessoal);
        $iLinhasPessoal = pg_num_rows($rsPessoal);

        if ($iLinhasPessoal > 0) {
          /*
           * DETALHE 4
           * CADASTRO DE PESSOAL
           */
            for ($iInd = 0; $iInd < $iLinhasPessoal; $iInd++) {
                $oDados = db_utils::fieldsMemory($rsPessoal, $iInd);
             
                $nLinhasArquivo++;
                $oDadosDetalhe4 = new stdClass();
         
                $oDadosDetalhe4->TipRegistro     = "7";
                $oDadosDetalhe4->Brancos1        = str_repeat(" ", 1);
                $oDadosDetalhe4->CPF             = str_pad($oDados->cpf, 14, " ", STR_PAD_LEFT);
                $oDadosDetalhe4->Brancos2        = str_repeat(" ", 1);
                $oDadosDetalhe4->Nome            = str_pad($oDados->nome, 60, " ", STR_PAD_RIGHT);
                $oDadosDetalhe4->ID              = str_pad($oDados->identidade, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->TituloEleitor   = str_pad($oDados->titulo_eleitor, 15, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->DataNascimento  = $this->formataData($oDados->dt_nascimento);
                $oDadosDetalhe4->Sexo            = str_pad($oDados->sexo, 1, " ", STR_PAD_LEFT);
                $oDadosDetalhe4->GrauInstrucao   = str_pad($oDados->grau_instrucao, 2, "0", STR_PAD_LEFT);
                $oDadosDetalhe4->Brancos3        = str_repeat(" ", 30);
                $oDadosDetalhe4->NumRegistroLido = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
      
                $this->aDados[] = $oDadosDetalhe4;
      
                $sLinhaDetalhe4 = $oDadosDetalhe4->TipRegistro .
                            $oDadosDetalhe4->Brancos1 .
                            $oDadosDetalhe4->CPF .
                            $oDadosDetalhe4->Brancos2 .
                            $oDadosDetalhe4->Nome .
                            $oDadosDetalhe4->ID .
                            $oDadosDetalhe4->TituloEleitor .
                            $oDadosDetalhe4->DataNascimento .
                            $oDadosDetalhe4->Sexo .
                            $oDadosDetalhe4->GrauInstrucao .
                            $oDadosDetalhe4->Brancos3 .
                            $oDadosDetalhe4->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe4."\r\n");
            }
        }

        $sSqlFolha    = " select z01_cgccpf  as cpf,
                             rh01_regist as matricula,
                             1           as vinculo,
                             (case rh30_vinculo
                                  when 'A' then 1
                                  when 'I' then 2
                                  when 'P' then 3
                             end) as situacao,
                             rh37_funcao as cargo,
                             rh03_padrao as nivel,
                             r70_codigo  as lotacao,
                             (case rh02_codreg 
                                  when 36 then '05'
                                  when 42 then '07'
                                  else '01' 
                              end) as forma_ingresso,
                             to_char(rh01_admiss,'dd/mm/YYYY') as admissao,
                             '' as forma_afastamento,
                             round(sum(sal_base.r14_valor),2) as salario_base,
                             round(sum(out_prov.r14_valor),2) as outras_vantagens,
                             round(sum(val_prev.r14_valor),2) as inss ,
                             round(sum(val_irrf.r14_valor),2) as irrf,
                             round(sum(out_desc.r14_valor),2) as outros_descontos
                      from rhpessoal 
                           inner join rhfuncao            on rh37_funcao = rh01_funcao and rh37_instit = rh01_instit
                           inner join rhpessoalmov        on rh02_regist = rh01_regist
                           left  join rhpesrescisao       on rh02_seqpes = rh05_seqpes
                           inner join rhregime            on rh30_codreg = rh02_codreg
                                                         and rh30_instit = rh02_instit
                           left  join rhpespadrao         on rh02_seqpes = rh03_seqpes
                           inner join cgm                 on rh01_numcgm = z01_numcgm
                           inner join rhlota              on r70_codigo  = rh02_lota
                           left  join gerfsal as sal_base on rh01_regist = sal_base.r14_regist
                                                         and sal_base.r14_anousu  = {$this->iAno}
                                                         and sal_base.r14_mesusu  between {$this->MesInicioSemestre} and {$this->MesFimSemestre}
                                                         and sal_base.r14_rubric in ('0001', '0003', '0032')
                           left  join gerfsal as out_prov on rh01_regist = out_prov.r14_regist
                                                         and out_prov.r14_anousu = {$this->iAno} 
                                                         and out_prov.r14_mesusu between {$this->MesInicioSemestre} and {$this->MesFimSemestre}
                                                         and out_prov.r14_rubric not in ('0001', '0003', '0032')
                                                         and out_prov.r14_pd = 1
                           left  join gerfsal as val_prev on rh01_regist = val_prev.r14_regist
                                                         and val_prev.r14_anousu  = {$this->iAno}
                                                         and val_prev.r14_mesusu  between {$this->MesInicioSemestre} and {$this->MesFimSemestre}
                                                         and val_prev.r14_rubric in ('R901','R902','R903','R904','R905','R906','R907','R908','R909','R910','R911','R912')
                           left  join gerfsal as val_irrf on rh01_regist = val_irrf.r14_regist
                                                         and val_irrf.r14_anousu  = {$this->iAno}
                                                         and val_irrf.r14_mesusu  between {$this->MesInicioSemestre} and {$this->MesFimSemestre}
                                                         and val_irrf.r14_rubric in ('R913','R914','R915')
                           left  join gerfsal as out_desc on rh01_regist = out_desc.r14_regist
                                                         and out_desc.r14_anousu  = {$this->iAno}
                                                         and out_desc.r14_mesusu  between {$this->MesInicioSemestre} and {$this->MesFimSemestre}
                                                         and out_desc.r14_rubric not in ('R901','R902','R903','R904','R905','R906','R907','R908','R909','R910','R911','R912','R913','R914','R915')
                                                         and out_desc.r14_pd = 2
                      where rh05_seqpes is null
                        and rh02_anousu = {$this->iAno} 
                        and rh02_mesusu = {$this->MesFimSemestre}
                        and rh02_lota in (18, 21, 22, 23, 25) 
                      group by z01_cgccpf,
                               rh01_regist,
                               rh02_codreg,
                               rh30_vinculo,
                               rh03_padrao,
                               r70_codigo,
                               rh01_admiss,
                               rh37_funcao";
        $rsFolha      = db_query($sSqlFolha);
        $iLinhasFolha = pg_num_rows($rsFolha);

        if ($iLinhasFolha > 0) {
          /*
           * DETALHE 5
           * FOLHA DE PAGAMENTO
           */
            for ($iInd = 0; $iInd < $iLinhasFolha; $iInd++) {
                $oDados = db_utils::fieldsMemory($rsFolha, $iInd);
             
                $nLinhasArquivo++;
                $oDadosDetalhe5 = new stdClass();
         
                $oDadosDetalhe5->TipRegistro     = "8";

                $oDadosDetalhe5->Brancos1             = str_repeat(" ", 1);
                $oDadosDetalhe5->CPF                  = str_pad($oDados->cpf, 14, " ", STR_PAD_LEFT);
                $oDadosDetalhe5->Brancos2             = str_repeat(" ", 1);
                $oDadosDetalhe5->Matricula            = str_pad($oDados->matricula, 10, " ", STR_PAD_LEFT);
                $oDadosDetalhe5->Vinculo              = str_pad($oDados->vinculo, 2, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->SituacaoFuncional    = str_pad($oDados->situacao, 1, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->Cargo                = str_pad($oDados->cargo, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->NivelFuncional       = str_pad($oDados->nivel, 4, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->Lotacao              = str_pad($oDados->lotacao, 10, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->FormaIngresso        = str_pad($oDados->forma_ingresso, 2, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->DataAdmissao         = $this->formataData($oDados->admissao);
                $oDadosDetalhe5->FormaAfastamento     = str_pad($oDados->forma_afastamento, 2, "0", STR_PAD_LEFT);
                $oDadosDetalhe5->DataAfastamento      = str_repeat(" ", 10);
                $oDadosDetalhe5->VencimentoBase       = $this->formataValor($oDados->salario_base, 14, "0");
                $oDadosDetalhe5->TotalOutrasVantagens = $this->formataValor($oDados->outras_vantagens, 14, "0");
                $oDadosDetalhe5->INSS                 = $this->formataValor($oDados->inss, 14, "0");
                $oDadosDetalhe5->IRRF                 = $this->formataValor($oDados->irrf, 14, "0");
                $oDadosDetalhe5->TotalOutrosDescontos = $this->formataValor($oDados->outros_descontos, 14, "0");
                $oDadosDetalhe5->Brancos3             = str_repeat(" ", 2);
                $oDadosDetalhe5->NumRegistroLido      = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
      
                $this->aDados[] = $oDadosDetalhe5;
      
                $sLinhaDetalhe5 = $oDadosDetalhe5->TipRegistro .
                            $oDadosDetalhe5->Brancos1 .
                            $oDadosDetalhe5->CPF .
                            $oDadosDetalhe5->Brancos2 .
                            $oDadosDetalhe5->Matricula .
                            $oDadosDetalhe5->Vinculo .
                            $oDadosDetalhe5->SituacaoFuncional .
                            $oDadosDetalhe5->Cargo .
                            $oDadosDetalhe5->NivelFuncional .
                            $oDadosDetalhe5->Lotacao .
                            $oDadosDetalhe5->FormaIngresso .
                            $oDadosDetalhe5->DataAdmissao .
                            $oDadosDetalhe5->FormaAfastamento .
                            $oDadosDetalhe5->DataAfastamento .
                            $oDadosDetalhe5->VencimentoBase .
                            $oDadosDetalhe5->TotalOutrasVantagens .
                            $oDadosDetalhe5->INSS .
                            $oDadosDetalhe5->IRRF .
                            $oDadosDetalhe5->TotalOutrosDescontos .
                            $oDadosDetalhe5->Brancos3    .
                            $oDadosDetalhe5->NumRegistroLido;
                fputs($arqFinal, $sLinhaDetalhe5."\r\n");
            }
        }
      //TRAILLER

        $nLinhasArquivo++;
        $oDadosTrailler = new stdClass();
     
        $oDadosTrailler->TipRegistro     = "9";
        $oDadosTrailler->Brancos         = str_repeat(" ", 149);
        $oDadosTrailler->NumRegistroLido = str_pad($nLinhasArquivo, 10, "0", STR_PAD_LEFT);
            
        $this->aDados[] = $oDadosTrailler;
    
        $sLinhaTrailler = $oDadosTrailler->TipRegistro
                       .$oDadosTrailler->Brancos
                       .$oDadosTrailler->NumRegistroLido;
        fputs($arqFinal, $sLinhaTrailler."\r\n");
        fclose($arqFinal);
    }
}
