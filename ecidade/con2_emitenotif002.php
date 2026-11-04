<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBSeller Servicos de Informatica             
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

include(modification("libs/db_sql.php"));
include(modification("libs/db_utils.php"));
include(modification("fpdf151/pdf2.php"));
include(modification("libs/db_libdocumento.php"));
include(modification("classes/db_notificacao_classe.php"));
include(modification("classes/db_db_config_classe.php"));
$cldb_config     = new cl_db_config;
$clnotificacao   = new cl_notificacao;
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
if ( $contribuicao == '' ) {
   db_redireciona('db_erros.php?fechar=true&db_erro=Contribuição não encontrada!');
   exit; 
}
$resultinst = $cldb_config->sql_record($cldb_config->sql_query(db_getsession("DB_instit")));
db_fieldsmemory($resultinst,0,true);

//$head1 = 'Departamento de Fazenda';


$contr = '';
if (isset($campo)){
   if ($tipo == 2){
       $contr = " d07_contri = ".$contribuicao." and d07_matric in (".str_replace('-',', ',$campo).") ";
   }elseif ($tipo == 3){
       $contr = " d07_contri = ".$contribuicao." and d07_matric not in (".str_replace('-',', ',$campo).") ";
   }
}else{
   $contr = '';
}

//die( $clnotificacao->sql_noticontri($contribuicao,"","","contrinot.d08_notif,contrib.d07_contri as d08_contr, contricalc.d09_matric as d08_matric, contricalc.d09_numpre,contrib.d07_valor,edital.d01_numero,ruas.j14_nome,ruas.j14_tipo,edital.d01_numtot,edital.d01_perunica,d01_privenc, proprietario.z01_nome,proprietario.z01_ender,proprietario.z01_numero,proprietario.z01_munic,proprietario.z01_uf,proprietario.z01_cep,proprietario.z01_compl,proprietario.z01_numcgm","",$contr,"proprietario.z01_nome"));


$sCamposss = "
contrinot.d08_notif, 
contrib.d07_contri as d08_contr,
contricalc.d09_matric as d08_matric,
contricalc.d09_numpre,
contrib.d07_valor,
edital.d01_numero,
ruas.j14_nome,
ruas.j14_tipo,
edital.d01_numtot,
edital.d01_perunica,
d01_privenc,
d01_receit,
d01_data,
proprietario.z01_nome,
proprietario.z01_ender,
proprietario.z01_numero,
proprietario.z01_munic,
proprietario.z01_uf,
proprietario.z01_cep,
proprietario.z01_compl,
proprietario.z01_numcgm
";




$sData = ucwords(strtolower($munic)).", ".date('d',db_getsession("DB_datausu"))." de ".db_mes(date('m',db_getsession("DB_datausu")))." de ".date('Y',db_getsession("DB_datausu")).".";

$sCampos = "
       
       contrinot.d08_notif     ,
       edital.d01_numero         as edital, 
       edital.d01_numtot         as tot_parcelas, 
       edital.d01_perunica       as desc_unica,
       edital.d01_receit         as receit, 
       edital.d01_privenc        as dt_vcto, 
       to_char(edital.d01_data, 'DD/MM/YYYY')           as dt_edital, 
       edital.d01_codedi         as seq_edital,
       edital.d01_descr          as desc_edital,
       contricalc.d09_matric     as matric,
       contricalc.d09_numpre     as numpre, 
       contrib.d07_contri        as d08_contr,
       contrib.d07_valor         as vlr_contr, 
       contrib.d07_vlrdes        as desc_unica,
       contrib.d07_venal         as vlr_venal,
       editalrua.d02_profun      as profundidade,
       editalrua.d02_valorizacao as valorizacao,
       ruas.j14_nome, 
       ruas.j14_tipo, 
       ruastipo.j88_sigla,
       ruastipo.j88_descricao,
       proprietario.z01_numcgm,
       proprietario.z01_nome,
       z01_nomecompleto         as nomecompleto,
       proprietario.z01_cgccpf  as cpfcnpj,
       proprietario.z01_ender, 
       proprietario.z01_numero, 
       proprietario.z01_compl, 
       z01_bairro               as bairro,
       z01_munic                as z01_munic , 
       z01_munic                as municipio ,
       proprietario.z01_uf, 
       proprietario.z01_cep, 
       j40_refant,
       j34_setor                as setorimovel,
       j34_quadra               as quadraimovel,
       j34_lote                 as loteimovel,
       j34_bairro               as bairroimovel,
       j06_setorloc,
       j06_quadraloc,
       j06_lote,
       d01_numero

";


$sSQlNotificações =            $clnotificacao->sql_noticontribuicao($contribuicao,
                                                                    "",
                                                                    "",
                                                                    $sCampos,
                                                                     "",
                                                                     $contr,
                                                                     "proprietario.z01_nome"
                                                                   );

            
//echo $sSQlNotificações;die();

$result = $clnotificacao->sql_record($sSQlNotificações);                                                                   

                      


if ($clnotificacao->numrows == 0){
   db_redireciona('db_erros.php?fechar=true&db_erro=Sem notificações a serem geradas. Verifique!');
}
$oDocumento        = new libdocumento(1706);
$oDocumento->getParagrafos();
$oDocAssinatura    = new libdocumento(1707);
$oDocAssinatura->getParagrafos();
$aCodigoAssinatura = $oDocAssinatura->aParagrafos[1]->db02_texto;


$pdf = new pdf(); 
$pdf->Open(); 
$pdf->AliasNbPages(); 
$pdf->SetFont('Arial','',13);     

if ( $tiporel == 1 ) {
   
   for($x=0;$x < $clnotificacao->numrows;$x++){
      
      $oNotif = db_utils::fieldsmemory($result,$x);
      
      
      $head2 = "Notificação de Contribuição de Melhoria: ".$oNotif->d08_notif;
      
      $pdf->AddPage();
      
      $oNotif->xtipo = "Rua ";
      if($oNotif->j14_tipo == 'A'){
         $oNotif->xtipo = "Av. ";
      }elseif($oNotif->j14_tipo == 'T'){
         $oNotif->xtipo = "Trav. ";
      }	
      $sSqlTtipoCorrecao         =  "SELECT k02_corr,  i01_descr"; 
      $sSqlTtipoCorrecao        .= " from tabrecregrasjm ";
      $sSqlTtipoCorrecao        .= "      inner join tabrec   on       k04_receit  = k02_codigo ";
      $sSqlTtipoCorrecao        .= "      inner join tabrecjm on tabrec.k02_codjm  = tabrecjm.k02_codjm ";
      $sSqlTtipoCorrecao        .= "      inner join inflan   on tabrecjm.k02_corr = inflan.i01_codigo";
      $sSqlTtipoCorrecao        .= " where '{$oNotif->d01_data}' between k04_dtini ";
      $sSqlTtipoCorrecao        .= "   and k04_dtfim and k04_receit = {$oNotif->d01_receit}";
      $rsTipoCorrecao            = db_query($sSqlTtipoCorrecao);
      
      $oDocumento->d01_numero    = $oNotif->d01_numero;
      $oDocumento->j14_nome      = $oNotif->xtipo." ".trim($oNotif->j14_nome);
      $oDocumento->d08_matric    = $oNotif->d08_matric;
      $oDocumento->d07_valor     = trim(db_formatar($oNotif->d07_valor,'f'));
      $oDocumento->valor_extenso = trim(db_extenso($oNotif->d07_valor));
      $oDocumento->d01_perunica  = $oNotif->d01_perunica;
      $oDocumento->d01_numtot    = $oNotif->d01_numtot;
      $oDocumento->d01_privenc   = db_Formatar($oNotif->d01_privenc,"d");
      $oDocumento->strcorrecao   = db_utils::fieldsMemory($rsTipoCorrecao,0)->k02_corr;
      $oDocumento->descrcorrecao = ucwords(strtolower(db_utils::fieldsMemory($rsTipoCorrecao,0)->i01_descr));
      $oDocumento->ender         = $ender;
      $oDocumento->bairro        = $bairro;
      $oDocumento->munic         = $munic;


$oDocumento->d08_notif          = $oNotif->d08_notif;
$oDocumento->d01_receit         = $oNotif->d01_receit;
$oDocumento->d01_data           = $oNotif->d01_data;
$oDocumento->d01_codedi         = $oNotif->d01_codedi;
$oDocumento->d01_descr          = $oNotif->d01_descr;
$oDocumento->d09_numpre         = $oNotif->d09_numpre;
$oDocumento->d07_contri         = $oNotif->d07_contri;
$oDocumento->d07_vlrdes         = $oNotif->d07_vlrdes;
$oDocumento->d02_profun         = $oNotif->d02_profun;
$oDocumento->j14_tipo           = $oNotif->j14_tipo;
$oDocumento->j88_sigla          = $oNotif->j88_sigla;
$oDocumento->j88_descricao      = $oNotif->j88_descricao;
$oDocumento->d09_matric         = $oNotif->d09_matric;
$oDocumento->d07_venal          = $oNotif->d07_venal;
$oDocumento->d02_valorizacao    = $oNotif->d02_valorizacao;
$oDocumento->j40_refant         = $oNotif->j40_refant;
$oDocumento->j34_setor          = $oNotif->j34_setor;
$oDocumento->j34_quadra         = $oNotif->j34_quadra;
$oDocumento->j34_lote           = $oNotif->j34_lote;
$oDocumento->j34_bairro         = $oNotif->j34_bairro;
$oDocumento->j06_setorloc       = $oNotif->j06_setorloc;
$oDocumento->j06_quadraloc      = $oNotif->j06_quadraloc;
$oDocumento->j06_lote           = $oNotif->j06_lote;
$oDocumento->z01_numcgm         = $oNotif->z01_numcgm;
$oDocumento->z01_nome           = $oNotif->z01_nome;
$oDocumento->z01_nomecompleto   = $oNotif->z01_nomecompleto;
$oDocumento->z01_cgccpf         = $oNotif->z01_cgccpf;
$oDocumento->z01_ender          = $oNotif->z01_ender;
$oDocumento->z01_numero         = $oNotif->z01_numero;
$oDocumento->z01_compl          = $oNotif->z01_compl;
$oDocumento->z01_bairro         = $oNotif->z01_bairro;
$oDocumento->z01_munic          = $oNotif->z01_munic;
$oDocumento->z01_uf             = $oNotif->z01_uf;
$oDocumento->z01_cep            = $oNotif->z01_cep;
$oDocumento->d01_numero         = $oNotif->d01_numero   ;





$oDocumento->d01_numero     = $oNotif->d01_numero   ;
$oDocumento->d08_notif      = $oNotif->d08_notif     ;
$oDocumento->edital         = $oNotif->edital        ;
$oDocumento->tot_parcelas   = $oNotif->tot_parcelas  ;
$oDocumento->desc_unica     = $oNotif->desc_unica    ;
$oDocumento->receit         = $oNotif->receit        ;
$oDocumento->dt_vcto        = $oNotif->dt_vcto       ;
$oDocumento->dt_edital      = $oNotif->dt_edital     ;
$oDocumento->seq_edital     = $oNotif->seq_edital    ;
$oDocumento->desc_edital    = $oNotif->desc_edital   ;
$oDocumento->matric         = $oNotif->matric        ;
$oDocumento->numpre         = $oNotif->numpre        ;
$oDocumento->d08_contr      = $oNotif->d08_contr     ;
$oDocumento->vlr_contr      = $oNotif->vlr_contr     ;
$oDocumento->desc_unica     = $oNotif->desc_unica    ;
$oDocumento->vlr_venal      = $oNotif->vlr_venal     ;
$oDocumento->profundidade   = $oNotif->profundidade  ;
$oDocumento->valorizacao    = $oNotif->valorizacao   ;
$oDocumento->j14_nome       = $oNotif->j14_nome      ;
$oDocumento->j14_tipo       = $oNotif->j14_tipo      ;
$oDocumento->j88_sigla      = $oNotif->j88_sigla     ;
$oDocumento->j88_descricao  = $oNotif->j88_descricao ;
$oDocumento->z01_numcgm     = $oNotif->z01_numcgm    ;
$oDocumento->z01_nome       = $oNotif->z01_nome      ;
$oDocumento->nomecompleto   = $oNotif->nomecompleto  ;
$oDocumento->cpfcnpj        = $oNotif->cpfcnpj       ;
$oDocumento->z01_ender      = $oNotif->z01_ender     ;
$oDocumento->z01_numero     = $oNotif->z01_numero    ;
$oDocumento->z01_compl      = $oNotif->z01_compl     ;
$oDocumento->bairro         = $oNotif->bairro        ;
$oDocumento->z01_munic      = $oNotif->z01_munic     ;
$oDocumento->municipio      = $oNotif->municipio     ;
$oDocumento->z01_uf         = $oNotif->z01_uf        ;
$oDocumento->z01_cep        = $oNotif->z01_cep       ;
$oDocumento->j40_refant     = $oNotif->j40_refant    ;
$oDocumento->setorimovel    = $oNotif->setorimovel   ;
$oDocumento->quadraimovel   = $oNotif->quadraimovel  ;
$oDocumento->loteimovel     = $oNotif->loteimovel    ;
$oDocumento->bairroimovel   = $oNotif->bairroimovel  ;
$oDocumento->j06_setorloc   = $oNotif->j06_setorloc  ;
$oDocumento->j06_quadraloc  = $oNotif->j06_quadraloc ;
$oDocumento->j06_lote       = $oNotif->j06_lote      ;








     //quebraPagina( $pdf );





      $aParagrafosNotificacao = $oDocumento->getDocParagrafos();
      //$pdf->multicell(0,4,ucwords(strtolower($munic)).", ".date('d',db_getsession("DB_datausu"))." de ".db_mes(date('m',db_getsession("DB_datausu")))." de ".date('Y',db_getsession("DB_datausu")).".",0,"R",0);
      $pdf->ln(5);
      $pdf->SetFont('Arial','B',13);
      //$pdf->multicell(0,6,"Notificação de Contribuição de Melhoria: ".$oNotif->d08_notif,0,"C",0);
      $pdf->SetFont('Arial','',13);
      $pdf->ln(5);
      $pdf->setx(35);
      $pdf->multicell(0,6,"Prezado Senhor(a),",0,"L",0);
      $pdf->ln(5);
      foreach ($aParagrafosNotificacao as $iIndex => $oParagrafo) {
      	$pdf->multicell(0,6,$oParagrafo->oParag->db02_texto,0,
                        $oParagrafo->oParag->db02_alinhamento,0,$oParagrafo->oParag->db02_inicia);
      }
      $pdf->ln(5);
      $pdf->ln(5);
      $pdf->setx(100);
      $posicaoy = $pdf->gety();
      if ($aCodigoAssinatura != "") {
        eval($aCodigoAssinatura);  
      }


      $pdf->multicell(0,6,"$sData ",0,"L",0);
      $pdf->ln();

   }






} elseif( $tiporel == 2 ) {
  

   $pdf->addpage();
   $pdf->setfillcolor(235);
   $pdf->setfont('arial','b',8);
   $pdf->cell(15,05,'Notificação',1,0,"c",1);
   $pdf->cell(15,05,'Matrícula',1,0,"c",1);
   $pdf->cell(15,05,'Numcgm',1,0,"c",1);
   $pdf->cell(80,05,'Nome',1,1,"c",1);
   $pdf->setfont('arial','',8);
   $total = 0;
   for($x=0;$x < $clnotificacao->numrows;$x++){
     
     db_fieldsmemory($result,$x);
     if ($pdf->gety() > $pdf->h - 35) {
       
        $pdf->addpage();
        $pdf->setfont('arial','b',8);
        $pdf->cell(15,05,'Notificação',1,0,"c",1);
        $pdf->cell(15,05,'Matrícula',1,0,"c",1);
        $pdf->cell(15,05,'Numcgm',1,0,"c",1);
        $pdf->cell(80,05,'Nome',1,1,"c",1);
        $pdf->setfont('arial','',8);
        
     }
     
     $pdf->cell(15,05,$d08_notif,0,0,"R",0);
     $pdf->cell(15,5,$d08_matric,0,0,"R",0);
     $pdf->cell(15,5,$z01_numcgm,0,0,"R",0);
     $pdf->cell(80,5,$z01_nome,0,1,"L",0);
     $total += 1;
   }
   $pdf->cell(125,05,'Total de Registros:   '.$total,1,1,"c",1);


}
$pdf->Output();
