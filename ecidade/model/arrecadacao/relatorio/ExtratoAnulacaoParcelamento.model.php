<?php
require_once(modification("libs/db_utils.php"));
use \ECidade\Tributario\Arrecadacao\Proprietario;
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2014  DBSeller Servicos de Informatica
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

class ExtratoAnulacaoParcelamento {

  /**
   * @var PDFDocument
   */
  public $oPdf;

 

  /**
   * @var integer Largura disponível do relatório.
   */
  public $iLargura;

  /**
   * @var integer Numero Parcelamento.
   */
  public $iTermo;
 
 
  /**
   * @var integer Altura da linha do relatório.
   */
  public $iAltura;

  /**
   *
   * @var callback
   */
  private $fCabecalho;

  /**
   *
   * @var array
   */
  private $aDados;

  private $iMatricula;
  
  private $iInscricao;
  /**
   *
   * @var array
   */
  private $aDadosContribuinte;

  /**
   *
   * @var array
   */
  private $aDadosParcelamento;

  /**
   *
   * @var array
   */
  private $aDadosParcelas;

  private $iLinha;

  private $iQuantidadeAberto = 0;

  private $iQuantidadePago = 0;

  private $iQuantidadeParcelado = 0;

  private $iTotal = 0;

  /**
   * Construtor
   *
   * @param Instituicao    $oInstituicao
   * @param DBDepartamento $oDepartamento
   */
  public function __construct() {

    $this->oPdf     = new PDFDocument(PDFDocument::PRINT_LANDSCAPE);
    $this->iLargura = $this->oPdf->getAvailWidth() - $this->oPdf->GetRightMargin();
    $this->iAltura  = 5;
  }

  
  /**
   * @param integer $iTermo
   */
  public function setTermo($iTermo) {

    $this->iTermo = $iTermo;
  }
 
  public function getDados() {
   
    $sSql    = "select ((select v01_exerc 
                           from divida 
                          where v01_numpre = v23_numpre  
                            and v01_numpar = v23_numpar)
                       union
                       (select dv05_exerc
                          from diversos 
                         where dv05_numpre = v23_numpre  
                       )   
                       ) as exercicio,
                       v23_numpre as numpre,
                       v23_numpar as numpar,
                       k00_descr  as tipo,
                       k02_drecei as receita,
                       v23_valor  as valor,
                       v23_vlrcor as valorcorrigido,
                       v23_vlrjur as valorjuros,
                       v23_vlrmul as valormulta
                  from termoanu 
                 inner join termoanusimula 
                    on v20_termoanu = v09_sequencial                     
                 inner join termosimula 
                    on v21_sequencial = v20_termosimula
                   and v21_ativo is true    
                 inner join termosimulareg 
                    on v23_termosimula = v20_termosimula                  
                 inner join tabrec
                    on k02_codigo = v23_receit   
                 inner join arretipo
                    on v23_tipo = k00_tipo     
                 where v09_parcel = ".$this->iTermo;
    $rsDados = db_query($sSql);
    if (!$rsDados || pg_num_rows($rsDados) === 0) {

      throw new DBException('Não foram encontrados as origens.');
    }

    return db_utils::getCollectionByRecord($rsDados);
  }
 
  public function getDadosParcelamento() {

    $sSql    = " select extract(year from v07_dtlanc) as ano,
                        v07_dtlanc,
                        v07_totpar,
                        v07_valor,
                        v09_data,
                        'Motivo: '||coalesce(substr(v09_motivo, 1, 200), '') as motivo,
                        v21_valorpago
                   from termo
                  inner join termoanu    on v07_parcel = v09_parcel  
                  inner join termosimula on v07_parcel = v21_parcel
                                        and v21_ativo is true
                  where v07_parcel = ".$this->iTermo;

    $rsDados = db_query($sSql);
    if (!$rsDados || pg_num_rows($rsDados) === 0) {

      throw new DBException('Não foram encontrados Anulação.');
    }

    return db_utils::fieldsmemory($rsDados, 0);
  }

  public function getDadosCustas() {

    $sSql    = "select k00_numpar     as numpar,
                       ar36_descricao as receita,
                       sum(k00_valor) as valor
                  from termo
                 inner join termoanu 
                    on v07_parcel = v09_parcel  
                 inner join arrepaga 
                    on v07_numpre = k00_numpre
                 inner join taxa
                    on ar36_receita = k00_receit                       
                 where v07_parcel = {$this->iTermo}
                 group by 1,2
                 order by 1";

    $rsDados = db_query($sSql);
    if (!$rsDados) {

      throw new DBException('Não foram encontrados custas.');
    }
   
   $aDados = array();
   $aDados = db_utils::getCollectionByRecord($rsDados, 0);
   return $aDados;
  }


  public function getDadosParcelas() {

    $sSql    = " select * 
                   from ((select p.k00_numpre as numpre,
                                 p.k00_numpar as numpar,
                                 k02_drecei as receita,
                                 p.k00_dtvenc as dtvencimento,
                                 coalesce(c.k00_valor)  as valor,
                                 to_char(round(p.k00_valor, 2), '999G999G999G990D99')::text  as valorpago,
                                 p.k00_dtpaga as datapagamento,
                                 'Pago' as situacao
                            from termo
                           inner join arrepaga p
                              on v07_numpre = p.k00_numpre                             
                            left join arrecant c
                              on p.k00_numpre = c.k00_numpre
                             and p.k00_numpar = c.k00_numpar
                             and p.k00_receit = c.k00_receit  
                           inner join tabrec 
                              on k02_codigo = p.k00_receit                            
                          where v07_parcel = {$this->iTermo}
                            and p.k00_hist not in (11403, 11503)
                          )
                          union all
                          (select a.k00_numpre,
                                  a.k00_numpar,
                                  k02_descr,
                                  a.k00_dtvenc,
                                  a.k00_valor,
                                  'ANULADO' as valorpago,
                                  null::date as k00_dtpaga,
                                  'Rompido' as situacao
                             from termo
                            inner join arreold a
                               on a.k00_numpre = v07_numpre 
                            inner join tabrec 
                              on k02_codigo = a.k00_receit   
                           where v07_parcel = {$this->iTermo})
                          union all
                          (select a.k00_numpre,
                                  a.k00_numpar,
                                  k02_descr,
                                  a.k00_dtvenc,
                                  a.k00_valor,
                                  'CANCELADO' as valorpago,
                                  null::date as k00_dtpaga,
                                  'Rompido' as situacao
                             from termo
                            inner join arrecant a
                               on a.k00_numpre = v07_numpre 
                            inner join cancdebitosreg
                               on a.k00_numpre = k21_numpre
                              and a.k00_numpar = k21_numpar
                              and a.k00_receit = k21_receit      
                            inner join tabrec 
                              on k02_codigo = a.k00_receit   
                           where v07_parcel = {$this->iTermo}) 
                         ) as x
                 order by 1, 2, 3";

    $rsDados = db_query($sSql);
    if (!$rsDados || pg_num_rows($rsDados) === 0) {

      throw new DBException('Não foram encontrados as parcelas.');
    }

   return db_utils::getCollectionByRecord($rsDados, 0);
  }

  public function getDadosContribuinte() {
    

    $sSqlContribuinte = "select case 
                                when m.k00_matric is not null 
                                then m.k00_matric 
                                when i.k00_inscr is not null 
                                then i.k00_inscr 
                                else v07_numcgm 
                                end as numero, 
                                case 
                                when m.k00_matric is not null 
                                then 'M' 
                                when i.k00_inscr is not null 
                                then 'I' 
                                else 'C' 
                                end as tipocontribuinte 
                           from termo 
                           left join arrematric m 
                             on m.k00_numpre = v07_numpre 
                           left join arreinscr i 
                             on i.k00_numpre = v07_numpre                           
                          where v07_parcel = ".$this->iTermo." limit 1";                          

    $rsContribuinte = db_query($sSqlContribuinte);                       

    $oContribuinteDebito = db_utils::fieldsmemory($rsContribuinte, 0);      


    switch ($oContribuinteDebito->tipocontribuinte) {
      case 'M':
           
           $oCgmPro =Proprietario::getProprietarioByMatricula($oContribuinteDebito->numero);

           $prefixCampos = 'cgm';

           if ($oCgmPro->j41_promitipo == 'C' || $oCgmPro->j41_promitipo == 'S') {

              $prefixCampos = 'proprietario';
           }

           $sSqlIdentificacao  = "select j01_matric,
                                         $prefixCampos.z01_nome,
                                         $prefixCampos.z01_ender,
                                         $prefixCampos.z01_numero,
                                         $prefixCampos.z01_compl,
                                         $prefixCampos.z01_munic,
                                         $prefixCampos.z01_uf,
                                         $prefixCampos.z01_cep,
                                         proprietario.nomepri,
                                         proprietario.j39_compl,
                                         proprietario.j39_numero,
                                         proprietario.j13_descr as bairro_matricula,
                                         case 
                                           when proprietario.j13_descr is not null and proprietario.j13_descr != '' 
                                             then proprietario.j13_descr 
                                             else ''
                                         end as j13_descr,
                                         proprietario.j34_setor||'.'||proprietario.j34_quadra||'.'||proprietario.j34_lote as sql,
                                         proprietario.z01_cgccpf, 
                                         $prefixCampos.z01_bairro,
                                         proprietario.z01_cgmpri as z01_numcgm,
                                         proprietario.j40_refant,
                                         proprietario.pql_localizacao
                                    from proprietario
                                    inner join cgm on cgm.z01_numcgm = proprietario.z01_numcgm
                                   where j01_matric = {$oContribuinteDebito->numero} limit 1";
      break;
      
      case 'I':

        $sSqlIdentificacao  ="select distinct issbase.q02_inscr,
                                     cgm.z01_numcgm,
                                     cgm.z01_nome,
                                     cgm.z01_ender,
                                     cgm.z01_numero,
                                     cgm.z01_compl,
                                     cgm.z01_bairro,
                                     cgm.z01_munic,
                                     cgm.z01_uf,
                                     cgm.z01_cep,
                                     empresa.z01_ender as nomepri,
                                     empresa.z01_compl as j39_compl,
                                     empresa.z01_numero as j39_numero,
                                     empresa.z01_bairro as j13_descr, 
                                     '' as sql,
                                     cgm.z01_cgccpf
                                from issbase
                               inner join empresa on issbase.q02_inscr = empresa.q02_inscr
                               inner join cgm on issbase.q02_numcgm = cgm.z01_numcgm
                               where issbase.q02_inscr = {$oContribuinteDebito->numero}";
    
      break;

      default:
          
        $sSqlIdentificacao = "select z01_numcgm,
                                     z01_nome,
                                     z01_ender,
                                     z01_numero,
                                     z01_compl,
                                     z01_bairro,
                                     z01_munic,
                                     z01_uf,
                                     z01_cep,
                                     z01_ender as nomepri, 
                                     z01_compl as j39_compl,
                                     z01_numero as j39_numero,
                                     z01_bairro as j13_descr,
                                     '' as sql,
                                     z01_cgccpf
                                from cgm
                               where z01_numcgm = {$oContribuinteDebito->numero}";
      break;
    }   

    $rsDados = db_query($sSqlIdentificacao);
    if (!$rsDados || pg_num_rows($rsDados) === 0) {

      throw new DBException('Não foram encontrados resultados.');
    } 

    return db_utils::getCollectionByRecord($rsDados);    
  }  

  private function escreverLinha(stdClass $oStdLinha, $iChave) {

    $this->iLinha = $iChave;
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ""                        , 0, 0, 'C');
    $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, $oStdLinha->exercicio     , 1, 0, 'C');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oStdLinha->numpre        , 1, 0, 'C');
    $this->oPdf->Cell($this->iLargura * 0.05, $this->iAltura, $oStdLinha->numpar        , 1, 0, 'C');
    $this->oPdf->Cell($this->iLargura * 0.15, $this->iAltura, $oStdLinha->tipo          , 1, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, $oStdLinha->receita       , 1, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, db_formatar(abs($oStdLinha->valor),"f", ' ', 15, 'e', 2), 1, 0, 'R');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, db_formatar(abs($oStdLinha->valorcorrigido),"f", ' ', 15, 'e', 2), 1, 0, 'R');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, db_formatar(abs($oStdLinha->valorjuros),"f", ' ', 15, 'e', 2), 1, 0, 'R');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, db_formatar(abs($oStdLinha->valormulta),"f", ' ', 15, 'e', 2), 1, 1, 'R');
  }
  
  private function escreverLinhaParcelas(stdClass $oStdLinha, $iChave) {
      
      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ''                       , 0, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oStdLinha->numpre       , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oStdLinha->numpar       , 1, 0, 'C');
      $oDataVencimento = new DBDate($oStdLinha->dtvencimento);
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oDataVencimento->getDate(DBDate::DATA_PTBR), 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, db_formatar(abs($oStdLinha->valor),"f", ' ', 15, 'e', 2), 1, 0, 'R');
      $this->oPdf->Cell($this->iLargura * 0.25, $this->iAltura, $oStdLinha->receita      , 1, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.15, $this->iAltura, $oStdLinha->valorpago    , 1, 0, 'R');
      $dDataPagamento = '';
      if($oStdLinha->datapagamento != null) {

        $oDataPagamento = new DBDate($oStdLinha->datapagamento);
        $dDataPagamento = $oDataPagamento->getDate(DBDate::DATA_PTBR);
      }
      $this->oPdf->Cell($this->iLargura * 0.16, $this->iAltura, $dDataPagamento, 1, 1, 'C');
  }

  private function escreverLinhaCustas(stdClass $oStdLinha, $iChave) {
      
      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ''                       , 0, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oStdLinha->numpar       , 1, 0, 'R');
      $this->oPdf->Cell($this->iLargura * 0.21, $this->iAltura, $oStdLinha->receita      , 1, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.15, $this->iAltura, db_formatar(abs($oStdLinha->valor),"f", ' ', 15, 'e', 2), 1, 1, 'R');      
  }

  public function emitir() {

    $aDados       = $this->getDados();
    $this->aDados = $aDados;
    $this->configuraPdf();
    $this->oPdf->setBold(true);
    $this->oPdf->SetFontSize(8);
    $this->oPdf->SetFillcolor(255);
    $aDadosContribuinte = $this->getDadosContribuinte();
    $this->aDadosContribuinte = $aDadosContribuinte;
    $this->oPdf->RoundedRect($this->iLargura-262,$this->iAltura+30,266,20,1,'DF','1234', 'DF');
    $this->oPdf->Ln($this->iAltura-3);
    foreach ($this->aDadosContribuinte as $oContribuinte) {
      
      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ''             , 0, 0, 'C');
      $this->oPdf->SetFont('Arial','B',9);
      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura,'Contribuinte', 0, 1, 'L');
      $this->oPdf->SetFont('Arial','',8);
      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, '', 0, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, 'Nome: ', 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.30, $this->iAltura, $oContribuinte->z01_nome, 0, 0, 'L'); //  $this->nome);
      $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, 'CNPJ/CPF:', 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, $oContribuinte->z01_cgccpf, 0, 0, 'L');
      if(isset($oContribuinte->j01_matric)) {

        $this->oPdf->Cell($this->iLargura * 0.05, $this->iAltura, 'Matrícula:', 0, 0, 'L');
        $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oContribuinte->j01_matric, 0, 0, 'L');
      }
      
      if(isset($oContribuinte->q02_inscr)) {

        $this->oPdf->Cell($this->iLargura * 0.05, $this->iAltura, 'Inscrição:', 0, 0, 'L');
        $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oContribuinte->q02_inscr, 0, 0, 'L');
      }

      $this->oPdf->Cell($this->iLargura * 0.05, $this->iAltura, 'CGM:', 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oContribuinte->z01_numcgm, 0, 1, 'L');

      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, '', 0, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, 'Endereço: ', 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.30, $this->iAltura, $oContribuinte->z01_ender.', '.$oContribuinte->z01_numero, 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, 'Bairro: ', 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, $oContribuinte->z01_bairro, 0, 0, 'L');    
      $this->oPdf->Cell($this->iLargura * 0.05, $this->iAltura, 'CEP: ', 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oContribuinte->z01_cep, 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.05, $this->iAltura, 'Município: ', 0, 0, 'L');
      $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, $oContribuinte->z01_munic, 0, 1, 'L');
    }
    
    $sProcessos = null; 
    $sSqlProcessos = "select 'Processos: '||array_to_string(array_agg(distinct v70_codforo), ', ') as processos from termo inner join termoanu on v09_parcel = v07_parcel inner join termoini on parcel = v07_parcel inner join processoforoinicial on v71_inicial = inicial and v71_anulado is false inner join processoforo on v70_sequencial = v71_processoforo where v07_parcel = ".$this->iTermo;
    $rsProcessos = db_query($sSqlProcessos);
    $sProcessos  = db_utils::fieldsmemory($rsProcessos, 0)->processos;
    $iAlturaQuadro = 25;
    if($sProcessos) {

      $iAlturaQuadro = 32; 
      
    }

    $this->oPdf->RoundedRect($this->iLargura-262,$this->iAltura+55,266,$iAlturaQuadro,1,'DF','1234');
    $aDadosParcelamento = $this->getDadosParcelamento();
    $this->aDadosParcelamento = $aDadosParcelamento;
    $this->oPdf->Ln($this->iAltura+5);
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ''            , 0, 0, 'C');
    $this->oPdf->SetFont('Arial','B',9);
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura,'Dados Parcelamento', 0, 1, 'L');
    $this->oPdf->SetFont('Arial','',8);
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, '', 0, 0, 'C');
    $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, 'Exercício: ', 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $this->aDadosParcelamento->ano, 0, 0, 'L'); //  $this->nome);
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Parcelas: ', 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $this->aDadosParcelamento->v07_totpar, 0, 0, 'L'); //  $this->nome);    
    $this->oPdf->Cell($this->iLargura * 0.08, $this->iAltura, 'Valor Pago:', 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.12, $this->iAltura, db_formatar(abs($this->aDadosParcelamento->v21_valorpago),"f", ' ', 15, 'e', 2), 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Parcelado em:', 0, 0, 'L');
    $oData = new DBDate($this->aDadosParcelamento->v07_dtlanc);
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oData->getDate(DBDate::DATA_PTBR), 0, 1, 'L');
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, '', 0, 0, 'C');
    $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, 'Termo:', 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $this->iTermo, 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Rompido em:', 0, 0, 'L');
    $oData = new DBDate($this->aDadosParcelamento->v09_data);
    $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, $oData->getDate(DBDate::DATA_PTBR), 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.08, $this->iAltura, 'Valor Parcelado:', 0, 0, 'L');
    $this->oPdf->Cell($this->iLargura * 0.12, $this->iAltura, db_formatar(abs($this->aDadosParcelamento->v07_valor),"f", ' ', 15, 'e', 2), 0, 1, 'L');    
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, '', 0, 0, 'C'); 
    $this->oPdf->MultiCell($this->iLargura * 0.90, $this->iAltura, $this->aDadosParcelamento->motivo, 0, 1);
    $iYantes = $this->oPdf->getY();
    $iXantes = $this->oPdf->getX();
    if($sProcessos) { 

      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, '', 0, 0, 'C'); 
      $this->oPdf->MultiCell($this->iLargura * 0.90, $this->iAltura, $sProcessos, 0, 1);    
    }
    
    $iAltura  = 5;
    if($this->oPdf->getY() > 88) {

      $iAltura   = 0;
    }
   
    $this->oPdf->Ln($this->iAltura + $iAltura);

    $this->oPdf->setBold(false);
    $this->oPdf->SetFont('arial', 'B', 9);
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura - 5, '', 0, 0, 'C');
    $this->oPdf->SetFillcolor(235);
    $this->oPdf->Cell($this->iLargura * 0.96, $this->iAltura, 'Origem do Parcelamento', 1, 1, 'C', true);
    $this->oPdf->Ln($this->iAltura-5);
    $this->imprimirCabecalhoOrigem($this->oPdf, $this->iAltura, true);
    $this->oPdf->SetFont('arial', '', 7);
    foreach ($aDados as $iChave => $oStdLinha) {

      $this->escreverLinha($oStdLinha, $iChave);
      $this->imprimirCabecalhoOrigem($this->oPdf, $this->iAltura, false);
      $this->oPdf->SetFont('arial', '', 7);
    }
    
    $this->oPdf->Ln($this->iAltura);
    $this->oPdf->SetFont('arial', 'B', 9);
    $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura - 5, '', 0, 0, 'C');
    $this->oPdf->SetFillcolor(235);
    $this->oPdf->Cell($this->iLargura * 0.96, $this->iAltura, 'Parcelas do Parcelamento', 1, 1, 'C', true);
    $this->oPdf->SetFont('Arial','',8);
    $this->imprimirCabecalhoParcelas($this->oPdf, $this->iAltura, true);
    $this->oPdf->SetFont('arial', '', 7);
    $aDadosParcelas       = $this->getDadosParcelas();
    $this->aDadosParcelas = $aDadosParcelas;
    foreach ($aDadosParcelas as $iChave => $oStdLinha) {

      $this->escreverLinhaParcelas($oStdLinha, $iChave);
      $this->imprimirCabecalhoParcelas($this->oPdf, $this->iAltura, false);
      $this->oPdf->SetFont('arial', '', 7);
    }
    
    $aDadosCustas = $this->getDadosCustas();
    if(count($aDadosCustas) > 1) {

      $this->oPdf->Ln($this->iAltura);
      $this->oPdf->SetFont('arial', 'B', 9);
      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura - 5, '', 0, 0, 'C');
      $this->oPdf->SetFillcolor(235);
      $this->oPdf->Cell($this->iLargura * 0.46, $this->iAltura, 'Demonstrativo das Custas Pagas', 1, 1, 'C', true);
      $this->imprimirCabecalhoCustas($this->oPdf, $this->iAltura, true);
      
      foreach ($aDadosCustas as $iChave => $oStdLinha) {
        
        $this->oPdf->SetFont('arial', '', 7);
        $this->escreverLinhaCustas($oStdLinha, $iChave);
        $this->imprimirCabecalhoCustas($this->oPdf, $this->iAltura, false);
      }
    }

    $this->oPdf->showPDF("ExtratoAnulacaoParcelamento_" . time());
  }

  private function imprimirCabecalhoOrigem($oPdf, $iAlturalinha, $lImprime) {

    if ( $oPdf->GetY() > $oPdf->h - 30 || $lImprime ) {

      $this->oPdf->SetFont('Arial','B',8);
      if ( !$lImprime ) {
           
          $oPdf->AddPage("L");
      }

      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ''         , 0, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.06, $this->iAltura, 'Exercício', 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Numpre'   , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.05, $this->iAltura, 'Parcela'  , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.15, $this->iAltura, 'Tipo'     , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.20, $this->iAltura, 'Receita'  , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Valor'    , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Corrigido', 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Juros'    , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Multa'    , 1, 1, 'C');
      
    }
  }  
  
  private function imprimirCabecalhoParcelas($oPdf, $iAlturalinha, $lImprime) {

    if ( $oPdf->GetY() > $oPdf->h - 30 || $lImprime ) {

      $this->oPdf->SetFont('Arial','B',8);
      if ( !$lImprime ) {
           
          $oPdf->AddPage("L");
      }

      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ''                    , 0, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Numpre'              , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Parcela'             , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Vencimento'          , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Valor Parcela'       , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.25, $this->iAltura, 'Receita'             , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.15, $this->iAltura, 'Valor Pago'          , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.16, $this->iAltura, 'Data Pagamento'      , 1, 1, 'C');
    }
  }  
  
  private function imprimirCabecalhoCustas($oPdf, $iAlturalinha, $lImprime) {

    if ( $oPdf->GetY() > $oPdf->h - 30 || $lImprime ) {

      $this->oPdf->SetFont('Arial','B',8);
      if ( !$lImprime ) {
           
          $oPdf->AddPage("L");
      }

      $this->oPdf->Cell($this->iLargura * 0.02, $this->iAltura, ''                    , 0, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.10, $this->iAltura, 'Parcela'             , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.21, $this->iAltura, 'Honorários/custas'   , 1, 0, 'C');
      $this->oPdf->Cell($this->iLargura * 0.15, $this->iAltura, 'Valor Pago'          , 1, 1, 'C');      
    }
  }  

  private function configuraPdf() {

    $this->oPdf->SetFillcolor(235);
    $this->oPdf->addHeaderDescription("Extrato Anulação Parcelamento");
    $this->oPdf->addHeaderDescription("");
    $this->oPdf->addHeaderDescription("Data Emissão: " .date("d/m/Y"));
    $this->oPdf->Open();
    $this->oPdf->SetLeftMargin(10);
    $this->oPdf->AliasNbPages();
    $this->oPdf->SetFont('arial', '', 7);
    //$this->oPdf->SetAutoPageBreak(true, 20);
    $this->oPdf->AddPage();
  }
  
}