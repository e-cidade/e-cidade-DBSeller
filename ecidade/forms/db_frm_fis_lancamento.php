<?php

/*echo "<pre>";
var_dump($_REQUEST);*/
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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
require_once  modification("classes/db_fis_tipofiscaliza_classe.php");
require_once  modification("classes/db_db_depart_classe.php");
$cltipofiscaliza = new cl_fis_tipofiscaliza;
$cldb_depart     = new cl_db_depart;


$get            = "";
$naotemnot      = 0;

?>

<form name="form1" method="post" action="">
<?php

if(isset($nl01_codlanc) && $nl01_codlanc != "" && $db_opcao != 1){

  $result = $cllancamento->sql_record($cllancamento->sql_querycgm($nl01_codlanc));
  if($cllancamento->numrows > 0){
    db_fieldsmemory($result,0);
  }
}

if(isset($z01_numcgm) && $z01_numcgm != ""){

  db_input('z01_numcgm',5,'',true,'hidden',1,"");
  include modification("classes/db_cgm_classe.php");
  $clcgm  = new cl_cgm;
	$get    = "&tipo=y101_numcgm&valor=$z01_numcgm";
  $result = $clcgm->sql_record($clcgm->sql_query_ender($z01_numcgm));

  if($clcgm->numrows > 0){

    db_fieldsmemory($result,0);
    $rua=$j14_codigo;
    $bairro=$j13_codi;
    $numero=$z01_numero;
    $compl=$z01_compl;
  }
  $dados = "<a onClick=\"js_abre('prot3_conscgm002.php?fechar=func_nome&numcgm=$z01_numcgm');return false\" href=''>CGM: ".$z01_numcgm." &nbsp;|&nbsp;".@$z01_nome."</a>";
}elseif(isset($j01_matric) && $j01_matric != ""){

  db_input('j01_matric',5,$Ij01_matric,true,'hidden',1,"");
  include modification("classes/db_iptubase_classe.php");
  $cliptubase = new cl_iptubase;
	$get        = "&tipo=y102_matric&valor=$j01_matric";
  //die($cliptubase->proprietario_query($j01_matric));
  $result     = $cliptubase->sql_record($cliptubase->proprietario_query($j01_matric));
  if($cliptubase->numrows > 0){

    db_fieldsmemory($result,0);
    $rua=$j14_codigo;
    $bairro=$j34_bairro;
    $numero=$j39_numero;
    $compl=$j39_compl;
  }
  $dados = "<a onClick=\"js_abre('cad3_conscadastro_002.php?cod_matricula=$j01_matric');return false\" href=''>matrícula: ".$j01_matric." &nbsp;|&nbsp;".@$z01_nome."</a>";
}elseif(isset($q02_inscr)  && $q02_inscr  != ""){

  db_input('q02_inscr',5,'',true,'hidden',1,"");
  include modification("classes/db_issbase_classe.php");
  $clissbase = new cl_issbase;
	$get       = "&tipo=y103_inscr&valor=$q02_inscr";

  $result    = $clissbase->sql_record($clissbase->empresa_query($q02_inscr));

  include(modification("classes/db_issruas_classe.php"));
  $clissruas = new cl_issruas;
  $result1    = $clissruas->sql_record($clissruas->sql_query_inscr($q02_inscr));

  if($clissruas->numrows > 0){

    db_fieldsmemory($result1,0);
    $rua=$j14_codigo;
    $bairro=$q13_bairro;
    $numero=$q02_numero;
    $compl=$q02_compl;

    include(modification("classes/db_cgm_classe.php"));
    $clcgm = new cl_cgm;
    $result = $clcgm->sql_record($clcgm->sql_query($q02_numcgm));
    if($clcgm->numrows > 0 && $rua == ""){
      db_fieldsmemory($result,0);
       $numero=$z01_numero;
       $compl=$z01_compl;
    }

  }
  $dados = "<a onClick=\"js_abre('iss3_consinscr003.php?numeroDaInscricao=$q02_inscr');return false\" href=''>inscrição: ".$q02_inscr." &nbsp;|&nbsp;".@$z01_nome."</a>";
}elseif(isset($y80_codsani)  && $y80_codsani  != ""){

  db_input('y80_codsani',5,$Iy80_codsani,true,'hidden',1,"");
  include modification("classes/db_fis_sanitario_classe.php");
  $clsanitario = new cl_fis_sanitario;
	$get         = "&tipo=y104_codsani&valor=$y80_codsani";
  $result      = $clsanitario->sql_record($clsanitario->sql_query($y80_codsani));
  if($clsanitario->numrows > 0){

    db_fieldsmemory($result,0);
    $rua=$y80_codrua;
    $bairro=$y80_codbairro;
    $numero=$y80_numero;
    $compl=$y80_compl;

    include modification("classes/db_cgm_classe.php");
    $clcgm = new cl_cgm;
    $result = $clcgm->sql_record($clcgm->sql_query($y80_numcgm));
    if($clcgm->numrows > 0){
      db_fieldsmemory($result,0);
    }
  }
  $dados = "<a onClick=\"js_abre('fis3_fis_consultasani002.php?y80_codsani=$y80_codsani');return false;\" href=''>sanitário: ".$y80_codsani." &nbsp;|&nbsp;".@$z01_nome."</a>";
}elseif(isset($y30_codnoti)  && $y30_codnoti  != ""){

  db_input('y30_codnoti',5,$Iy30_codnoti,true,'hidden',1,"");
  include modification("classes/db_fis_fiscal_classe.php");
  $clfiscal = new cl_fis_fiscal;
  $sqlnot = "
  			select
  			y13_codnoti,

  			y13_codigo   as rua,
  			be.j13_codi  as bairro,
  			re.j14_nome  as ruanomee,
  			y13_numero   as numero,
  			be.j13_descr as bairrodescre,
  			y13_compl    as compl,

  			y12_codigo   as rual,
  			bl.j13_codi  as bairrol,
  			rl.j14_nome  as ruanomel,
  			y12_numero   as numerol,
  			bl.j13_descr as bairrodescrl,
  			y12_compl    as compll
  			from fiscalizacao.fis_fiscexec
  			left join fiscalizacao.fis_fiscalocal       on y13_codnoti=y12_codnoti
  			inner join ruas    as re   on re.j14_codigo =y13_codigo
  			inner join bairro  as be   on be.j13_codi   =y13_codi
  			inner join ruas    as rl   on rl.j14_codigo =y12_codigo
  			inner join bairro  as bl   on bl.j13_codi   =y12_codi
  			where y13_codnoti = $y30_codnoti";
    $resultnot = db_query ($sqlnot);
    $linhasnot = pg_num_rows($resultnot);
    if($linhasnot>0){
       db_fieldsmemory($resultnot,0);
       $notific=true;

    }else{
      db_msgbox("Não tem endereço registrado e localizado cadastrado para a notificação $y30_codnoti.");
      $naotemnot = 1;
    }


}
if($numero  == ""){
   $numero = 0;
}

//-------------------------------/Busca Cgm e verifica se é por cgm,matricula,inscr,sanitario ou notificação/-----------------
if ($db_opcao==2||$db_opcao==3){

  $sSql = $cllancamento->sql_query_busca($nl01_codlanc);
  $result_ident = $cllancamento->sql_record($sSql);
  if($cllancamento->numrows>0){

    db_fieldsmemory($result_ident,0);
    $cod    = $dl_codigo;
    $inform = $dl_identificacao;

    if ($dl_identificacao=='Cgm'){
      $abre = "prot3_conscgm002.php?fechar=func_nome&numcgm";
    }else if ($dl_identificacao=='Inscrição'){
      $abre = "iss3_consinscr003.php?numeroDaInscricao";
    }else if ($dl_identificacao=='Matrícula'){
      $abre = "cad3_conscadastro_002.php?cod_matricula";
    }else if ($dl_identificacao=='Sanitário'){
      $abre = "fis3_fis_consultasani002.php?y80_codsani";
    }else if ($dl_identificacao=='Notificação'){
       $abre = "fis3_fis_fiscal006.php?y30_codnoti";
    }
  }
}
if (isset($cod)&&$cod!=""){
 $dados = "<a onClick=\"js_abre('".$abre."=$cod');return false;\" href=''>".$inform.": ".$cod." &nbsp;|&nbsp;".@$z01_nome."</a>";
}
?>
<fieldset>
  <legend>Notificação de Lançamento</legend>
<table>
  <tr>
    <td colspan="2">
      <strong>Código:</strong>
      <?php 
db_input('nl01_codlanc',10,'',true,'text',3,"");
      ?>
      <strong>Número do Bloco:</strong>
      <?php 
      db_input('nl01_numbloco',10,'',true,'text',$db_opcao,"");
      ?>
    <strong>Data:</strong>
      <?php 
      if(empty($nl01_data_dia)){

        $nl01_data_dia = date("d",db_getsession("DB_datausu"));
        $nl01_data_mes = date("m",db_getsession("DB_datausu"));
        $nl01_data_ano = date("Y",db_getsession("DB_datausu"));
      }
      db_inputdata('nl01_data',@$nl01_data_dia,@$nl01_data_mes,@$nl01_data_ano,true,'text',$db_opcao,"")
      ?>
             <strong>Hora:</strong>
      <?php 
      db_input('nl01_hora',5,'',true,'text',$db_opcao,"");
      if($db_opcao == 1){
        echo "<script>document.form1.nl01_hora.value='".db_hora()."'</script>";
      }
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap colspan="2" >
    <?php 
      echo "<strong>".@$dados."&nbsp;</strong>";
      db_ancora(@$Ly29_tipofisc,"js_pesquisa_tipofisc(true);",$db_opcao);
      $result_tipofisc=$cltipofiscaliza->sql_record("select * from fiscalizacao.fis_tipofiscaliza inner join fiscalizacao.fis_fisdocdep on
      y27_codtipo = fd02_codtipo and fd02_instit = y27_instit where y27_instit =".db_getsession('DB_instit')." and fd02_coddep =".db_getsession('DB_coddepto')." ORDER BY y27_descr ASC");
      db_selectrecord("nl01_codtipo",$result_tipofisc,true,$db_opcao);
    ?>
 </td>
  </tr>
  <tr>
    <td nowrap>
       <strong>Observação da Notificação:</strong>
    </td>
    <td>
      <?php 
      db_textarea('nl01_obs',1,50,'',true,'text',$db_opcao,"")
      ?>
    </td>
  </tr>

                <tr id="listaParagrafo">
                    <td><strong>Lista Paragrafos:</strong></td>
                    <td>
                      <select name="paragrafos" id="paragrafos">
                        <option value=""></option>
                        <?php
                            $pSql  = 'select distinct pl09_paragrafo, pl09_descr from fiscalizacao.fis_paragrafo where pl09_status = true and pl09_tipo = 4  and pl09_coddepto = '.db_getsession('DB_coddepto');


                            $pRs   = db_query($pSql);
                            $pRows = pg_num_rows($pRs);
                            for($i=0; $i<$pRows; $i++){
                                db_fieldsmemory($pRs, $i);
                                echo '<option value="'.$pl09_paragrafo.'">'.$pl09_descr.'</option>';
                            }
                        ?>
                    </select>
                    <input id="addParagrafo" name="addParagrafo" readonly="" type="button" value="Adicionar">
                    </td>
                </tr>
                <?php
                    $textoRelato       = '';
                    $textoInfringencia = '';
                    $textoSancao       = '';
                    $textoBaseLegal    = '';
                    $codRelato         = '';
                    $codInfringencia   = '';
                    $codSancao         = '';
                    $codBaseLegal      = '';

		    if( isset($nl01_codlanc) ){
                    $paSql  = "select * from fiscalizacao.fis_paragrafolanc where pl30_codlanc = $nl01_codlanc";
                    $paRs   = db_query($paSql);
                    $paRows = pg_num_rows($paRs);
                    for ($i=0; $i < $paRows; $i++) {
                        db_fieldsmemory($paRs, $i);
                        if($pl30_paragrafo == 1){
                            $textoRelato = $pl30_texto;
                            $codRelato   = $pl30_codigo;

                        } else if($pl30_paragrafo == 2){
                            $textoInfringencia = $pl30_texto;
                            $codInfringencia   = $pl30_codigo;

                        } else if($pl30_paragrafo == 3){
                            $textoSancao = $pl30_texto;
                            $codSancao   = $pl30_codigo;

                        } else if($pl30_paragrafo == 4) {
                            $textoBaseLegal = $pl30_texto;
                            $codBaseLegal   = $pl30_codigo;

                        }
		    }
		    }

                ?>

                <tr class="trParagrafo" id="paraRelato" <?=(($textoRelato=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                        <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="RELATO" <?=(($textoRelato=='')?'disabled="disabled"':'')?> />
                        <input class="rm1" type="hidden" name="paragrafolanc[]" value="" <?=(($textoRelato=='')?'disabled="disabled"':'')?> />
                        <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codRelato?>" <?=(($textoRelato=='')?'disabled="disabled"':'')?> />
                        <input class="rm2" type="hidden" name="paragrafoTipo[]" value="1" <?=(($textoRelato=='')?'disabled="disabled"':'')?> />
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoRelato=='')?'disabled="disabled"':'')?>><?=$textoRelato?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>


                <tr class="trParagrafo" id="paraInfringencia" <?=(($textoInfringencia=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                    <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="INFRINGÊNCIA" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?> />
                    <input class="rm1" type="hidden" name="paragrafolanc[]" value="" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?> />
                    <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codInfringencia?>" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?> />
                    <input class="rm2" type="hidden" name="paragrafoTipo[]" value="2" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?> />
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoInfringencia=='')?'disabled="disabled"':'')?>><?=$textoInfringencia?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>

                <tr class="trParagrafo" id="paraSancao" <?=(($textoSancao=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                    <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="SANÇÃO" <?=(($textoSancao=='')?'disabled="disabled"':'')?> />
                    <input class="rm1" type="hidden" name="paragrafolanc[]" value="" <?=(($textoSancao=='')?'disabled="disabled"':'')?> />
                    <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codSancao?>" <?=(($textoSancao=='')?'disabled="disabled"':'')?> />
                    <input class="rm2" type="hidden" name="paragrafoTipo[]" value="3" <?=(($textoSancao=='')?'disabled="disabled"':'')?> />
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoSancao=='')?'disabled="disabled"':'')?>><?=$textoSancao?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>

                <tr class="trParagrafo" id="paraBaseLegal" <?=(($textoBaseLegal=='')?'style="display:none"':'')?>>
                    <td class="tdParagrafo">
                        <input style="background-color:#DEB887" readonly="readonly" type="text" name="paragrafoDescr[]" value="BASE LEGAL" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?> />
                        <input class="rm1" type="hidden" name="paragrafolanc[]" value="" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?> />
                        <input class="rm1" type="hidden" name="paragrafoCod[]" value="<?=$codBaseLegal?>" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?> />
                        <input class="rm2" type="hidden" name="paragrafoTipo[]" value="4" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?> />
                    </td>
                    <td class="tdParagrafoTexto">
                        <textarea name="paragrafoTexto[]" cols="50" rows="4" <?=(($textoBaseLegal=='')?'disabled="disabled"':'')?>><?=$textoBaseLegal?></textarea>
                        <input class="remover" type="button" value="Remover" />
                    </td>
                </tr>
            <tr>
    <td nowrap >
         <strong>Código do Departamento:</strong>
       <?php 
       db_ancora(@$Lnl01_setor,"js_pesquisanl01_setor(true);",3);
       ?>
    </td>
    <td>
      <?php 
      if ($db_opcao==1){
        $nl01_setor = db_getsession("DB_coddepto");
        $result_depto=$cldb_depart->sql_record($cldb_depart->sql_query_file($nl01_setor));
        if ($cldb_depart->numrows>0){
          db_fieldsmemory($result_depto,0);
        }
      }
      db_input('nl01_setor',10,'',true,'text',3," onchange='js_pesquisanl01_setor(false);'");

      db_input('descrdepto',40,'',true,'text',3,'');
       ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl01_nome?>">
       <?php //=@$Lnl01_nome?>
       <strong>Contato:</strong>
    </td>
    <td>
      <?php 
      db_input('nl01_nome',50,'',true,'text',$db_opcao,"")
      ?>
    </td>
  </tr>
 <!--  <tr>
    <td nowrap >

       <strong>Data do Vencimento Atualizada:</strong>
      </td>
    <td>
      <?php 
      //if(empty($nl01_dtvenc_dia)){

        //$dia = date("d",db_getsession("DB_datausu"));
        //$mes = date("m",db_getsession("DB_datausu"));
        //$ano = date("Y",db_getsession("DB_datausu"));
        //$nl01_dtvenc_dia = substr(verifica_data($dia,$mes,$ano),8,2);
        //$nl01_dtvenc_mes = substr(verifica_data($dia,$mes,$ano),5,2);
        //$nl01_dtvenc_ano = substr(verifica_data($dia,$mes,$ano),0,4);
      //}
      db_inputdata('nl01_dtvenc',@$nl01_dtvenc_dia,@$nl01_dtvenc_mes,@$nl01_dtvenc_ano,true,'text',$db_opcao,"");
      ?>
      <strong>Prazo p/ Recurso:</strong>
      <?php 
      db_inputdata('nl01_prazorec',@$nl01_prazorec_dia,@$nl01_prazorec_mes,@$nl01_prazorec_ano,true,'text',$db_opcao,"");
      ?>
    </td>
  </tr> -->

  <tr>
    <td nowrap>
       <?php 
       if(isset($procfiscal) && $procfiscal != "")
          $bloqfis = 3;
        else
          $bloqfis = $db_opcao;

       db_ancora("<strong>Processo Fiscal:</strong>","js_pesquisaprocfiscal(true);",$bloqfis);
       ?>
    </td>
    <td>
      <?php
        db_input('procfiscal',10,'',true,'text',$bloqfis," onchange='js_pesquisaprocfiscal(false);'");
        db_input('nome',40,'',true,'text',3,'');
      ?>
    </td>
  </tr>
  <tr>
    <td colspan="2">
      <fieldset>
      <legend>Endereço registrado</legend>
      <table>
  <tr>
    <td nowrap width="100" title="<?=@$Tnl02_codigo?>">
       <?php


        $op = 3;
      	if (isset($notific)&&$notific==true&&$db_opcao==1){

      	  $nl02_codigo=$rual;
      	  $nl02_codi=$bairrol;
      	  $nl02_numero=$numerol;
      	  $nl02_compl=$compll;
      	  $nl03_codigo=$rua;
      	  $nl03_codi=$bairro;
      	  $nl03_numero=$numero;
      	  $nl03_compl=$compl;
      	  $j14_nome=$ruanomel;
      	  $j13_descr=$bairrodescrl;
      	  $j14_nome_exec=$ruanomee;
      	  $j13_descr_exec=$bairrodescre;
      	}else if (($db_opcao==1) &&( $naotemnot != 1)){

      	  $nl02_codigo=$rua;
      	  $nl02_codi=$bairro;
      	  $nl02_numero=$numero;
      	  $nl02_compl=$compl;
      	  $nl03_codigo=$rua;
      	  $nl03_codi=$bairro;
      	  $nl03_numero=$numero;
      	  $nl03_compl=$compl;
      	  if ((isset($j14_nome)&&$j14_nome!="")&&(isset($j13_descr)&&$j13_descr!="")){

      	    $j14_nome=@$j14_nome;
      	    $j13_descr=@$j13_descr;
      	    $j14_nome_exec=@$j14_nome;
      	    $j13_descr_exec=@$j13_descr;
      	  }else{

      	    $j14_nome_exec=@$z01_ender;
      	    $j13_descr_exec=@$z01_bairro;
      	    $j14_nome=@$z01_ender;
      	    $j13_descr=@$z01_bairro;
      	  }
      	}

        if ((isset($j14_nome)&&$j14_nome!="")&&(isset($j13_descr)&&$j13_descr!="")){

      	  $j14_nome=@$j14_nome;
      	  $j13_descr=@$j13_descr;

      	  if ((isset($j14_nome_exec)&&$j14_nome_exec!="")&&(isset($j13_descr_exec)&&$j13_descr_exec!="")){

      	    $j14_nome_exec=@$j14_nome_exec;
      	    $j13_descr_exec=@$j13_descr_exec;
      	  }
      	}
      	if($db_opcao==1||$db_opcao==2){

      	  if (@$j14_nome==""||$j13_descr==""||$nl02_codigo==""||$nl02_codi==""){
      	    $op=1;
      	  }
      	}
         $op = 3;
       db_ancora("<strong>Cód. Rua/Avenida </strong>","js_ruas1(true);",$op);
       ?>
    </td>
    <td>
      <?php 
      $j14_nome = (@$nl02_codigo=="" or @$nl02_codigo==null)?"":@$j14_nome;

      db_input('nl02_codigo',10,'',true,'text',$op," onChange='js_ruas1(false)'");
      db_input('j14_nome',50,'',true,'text',3,"");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap>
      <strong>Número</strong>
      <?=@$Lnl02_numero?>
    </td>
    <td>
      <?php 
      if(!isset($nl02_numero)){
       $nl02_numero = 0;
      }
      db_input('nl02_numero',10,'',true,'text',$op,"")
      ?>
       <strong>Complemento</strong>
      <?php 
      db_input('nl02_compl',20,'',true,'text',$op,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl02_codi?>">
      <?php 
      db_ancora("<strong>Bairro</strong>","js_bairro1(true);",$op);
      ?>
    </td>
    <td nowrap>
      <?php
        $j13_descr = (@$nl02_codi=="" or @$nl02_codi==null)?"":@$j13_descr;
        db_input('nl02_codi',10,'',true,'text',$op," onChange='js_bairro1(false)'");
        db_input('j13_descr',50,'',true,'text',3);
      ?>
    </td>
  </tr>
  </table>
  </fieldset>
  </td>
  </tr>
  <tr>
    <td colspan="2" align="left">
      <fieldset>
           <legend>Endereço localizado</legend>
      <table>
  <tr>
    <td nowrap width="100" title="<?=@$Tnl03_codigo?>">
       <?php 
       db_ancora("<strong>Cód. Rua/Avenida </strong>","js_ruas(true);",$db_opcao);
       ?>
    </td>
    <td nowrap>
      <?php 
        $j14_nome_exec = (@$nl03_codigo=="" or @$nl03_codigo==null)?"":@$j14_nome_exec;
        db_input('nl03_codigo',10,'',true,'text',$db_opcao," onChange='js_ruas(false)'");
        db_input('j14_nome',50,'',true,'text',3,"","j14_nome_exec");
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap >
       <strong>Número</strong>
    </td>
    <td>
      <?php 
      if(!isset($nl03_numero)){
       $nl03_numero = 0;
      }
      db_input('nl03_numero',10,'',true,'text',$db_opcao,"")
      ?>
             <strong>Complemento</strong>
      <?php 
      db_input('nl03_compl',20,'',true,'text',$db_opcao,"")
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Tnl03_codi?>">
      <?php 
      db_ancora("<strong>Bairro:</strong>","js_bairro(true);",$db_opcao);
      ?>
    </td>
    <td nowrap>
      <?php 
        $j13_descr_exec = (@$nl03_codi=="" or @$nl03_codi==null)?"":@$j13_descr_exec;
        db_input('nl03_codi',10,'',true,'text',$db_opcao," onChange='js_bairro(false)'");
        db_input('j13_descr',50,'',true,'text',3,"","j13_descr_exec");
      ?>
    </td>
  </tr>
  </table>
  </fieldset>
  </td>
  </tr>
  </table>
  </fieldset>
  <input name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> onclick="return js_validaFormulario();"/>
  <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" />
<?php 
if ($db_opcao==22||$db_opcao==2){
?>
<input name="novo" type="button" id="novo" value="Incluir Novo" onclick="parent.location.href='fis1_fis_lancamento005.php';" >
<?php 
}
if($db_opcao==1||$db_opcao==2){

  if (@$j14_nome==""||$j13_descr==""||$nl02_codigo==""||$nl02_codi==""){

    $op=1;
    if (isset($z01_numcgm)&&$z01_numcgm!=""){

      $sql_cgm = "select z01_munic from cgm where z01_numcgm = $z01_numcgm";
      $result_cgm = db_query($sql_cgm);
      if (pg_numrows($result_cgm)>0){

        db_fieldsmemory($result_cgm,0);
        $sql_munic = "select munic from db_config";
        $result_munic = db_query($sql_munic);
        db_fieldsmemory($result_munic,0);
      }
    }
  }
}

?>
</form>
                <script type="text/javascript" src="scripts/jquery-2.1.1.min.js"></script>
                <script type="text/javascript">
                    var $a = jQuery.noConflict();
                    jQuery(document).ready(function($a) {

                        $a('body').on('click', '.remover', function() {
                            var idTr = $a(this).closest('tr').attr('id');
                            $a('#'+idTr).hide();
                            $a('#'+idTr).find('input.rm1').val('').attr('disabled','disabled');
                            $a('#'+idTr).find('input.rm2').attr('disabled','disabled');
                            $a('#'+idTr).find('textarea').val('');
                        });

                        $a('#addParagrafo').click(function() {
                            // function js_pesquisa(){
                            var pParagrafo = $a('#paragrafos').val();
                            js_OpenJanelaIframe('','db_iframe_addparagrafo','fis1_fis_addparagragolanc001.php?pParagrafo='+pParagrafo+'&pTipo=4','Pesquisa',true);

                        });
                    });
                </script>

<script type="text/javascript">


function js_validaFormulario(){

 return true;
}

function js_setatabulacao(){
  js_tabulacaoforms("form1","nl01_numbloco",true,1,"nl01_numbloco",true);
}
function js_bairro(mostra){
  if(mostra == true){
    js_OpenJanelaIframe('','db_iframe_bairro','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro|j13_codi|j13_descr','pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_bairro','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro1&pesquisa_chave='+document.form1.nl03_codi.value,'pesquisa',false);
  }
}
function js_preenchebairro(chave,chave1){
  document.form1.nl03_codi.value = chave;
  document.form1.j13_descr_exec.value = chave1;
  db_iframe_bairro.hide();
}
function js_preenchebairro1(chave,erro){
  document.form1.j13_descr_exec.value = chave;
  if(erro == true){
    document.form1.nl03_codi.focus();
    document.form1.nl03_codi.value='';
  }
  db_iframe_bairro.hide();
}
function js_bairro1(mostra){
  if(mostra == true){
    js_OpenJanelaIframe('','db_iframe_bairro','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro2|j13_codi|j13_descr','pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_bairro','func_bairro.php?rural=1&funcao_js=parent.js_preenchebairro22&pesquisa_chave='+document.form1.nl02_codi.value,'pesquisa',false);
  }
}
function js_preenchebairro2(chave,chave1){
  document.form1.nl02_codi.value = chave;
  document.form1.j13_descr.value = chave1;
  db_iframe_bairro.hide();
}
function js_preenchebairro22(chave,erro){
  document.form1.j13_descr.value = chave;
  if(erro == true){
    document.form1.nl02_codi.focus();
    document.form1.nl02_codi.value='';
  }
 db_iframe_bairro.hide();
}
function js_ruas(mostra){
  if(mostra == true){
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheruas|j14_codigo|j14_nome','Pesquisa',true);
  }else{
    document.form1.j14_nome_exec.value = '';
    document.form1.nl03_numero.value = '';
    document.form1.nl03_compl.value = '';
    document.form1.nl03_codi.value = '';
    document.form1.j13_descr_exec.value = '';
    document.form1.nl03_numero.focus();
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheruas1&pesquisa_chave='+document.form1.nl03_codigo.value+'','Pesquisa',false);
  }
}
function js_preencheruas(chave,chave1){
  document.form1.nl03_codigo.value = chave;
  document.form1.j14_nome_exec.value = chave1;
  db_iframe_ruas.hide();
}
function js_preencheruas1(chave,erro){
  document.form1.j14_nome_exec.value = chave;
  if(erro == true){
    document.form1.nl03_codigo.focus();
    document.form1.nl03_codigo.value='';
  }
  db_iframe_ruas.hide();
}
function js_ruas1(mostra){
  if(mostra == true){
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheender|j14_codigo|j14_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_ruas','func_ruas.php?rural=1&funcao_js=parent.js_preencheender1&pesquisa_chave='+document.form1.nl02_codigo.value+'','Pesquisa',false);
  }
}
function js_preencheender(chave,chave1){
  document.form1.nl02_codigo.value = chave;
  document.form1.j14_nome.value = chave1;
  db_iframe_ruas.hide();
}
function js_preencheender1(chave,erro){
  document.form1.j14_nome.value = chave;
  if(erro==true){
    document.form1.nl02_codigo.focus();
    document.form1.nl02_codigo.value = '';
  }
}
function js_pesquisanl01_setor(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_db_depart','func_db_depart.php?funcao_js=parent.js_mostradb_depart1|coddepto|descrdepto','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_db_depart','func_db_depart.php?pesquisa_chave='+document.form1.nl01_setor.value+'&funcao_js=parent.js_mostradb_depart','Pesquisa',false);
  }
}
function js_mostradb_depart(chave,erro){
  document.form1.descrdepto.value = chave;
  if(erro==true){
    document.form1.nl01_setor.focus();
    document.form1.nl01_setor.value = '';
  }
}
function js_mostradb_depart1(chave1,chave2){
  document.form1.nl01_setor.value = chave1;
  document.form1.descrdepto.value = chave2;
  db_iframe_db_depart.hide();
}
function js_pesquisa_tipofisc(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_tipo','func_fis_tipofiscaliza.php?funcao_js=parent.js_mostratipo1|y27_codtipo|y27_descr','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_tipo','func_fis_tipofiscaliza.php?pesquisa_chave='+document.form1.nl01_codtipo.value+'&funcao_js=parent.js_mostratipo','Pesquisa',false);
  }
}
function js_mostratipo(chave,erro){
  document.form1.y27_descr.value = chave;
  if(erro==true){
    document.form1.nl01_codtipo.focus();
    document.form1.nl01_codtipo.value = '';
  }
}
function js_mostratipo1(chave1,chave2){
  document.form1.nl01_codtipo.value = chave1;
  document.form1.y27_descr.value = chave2;
  db_iframe_tipo.hide();
}


function js_pesquisaprocfiscal(mostra){

  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?funcao_js=parent.js_mostraprocfiscal1|y100_sequencial|z01_nome|db_depart_protocolo|db_descr_depart|db_depart_atual<?=$get?>','Pesquisa',true);
  }else{

     if(document.form1.procfiscal.value != ''){
        js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?pesquisa_chave='+document.form1.procfiscal.value+'&funcao_js=parent.js_mostraprocfiscal','Pesquisa',false,'0','1','775','390');
     }else{
		 	 document.form1.nome.value = '';
		 }
  }
}
function js_mostraprocfiscal(chave,erro,dep_prot,depart,dep_atual){

 if (dep_prot == dep_atual) {
  	document.form1.nome.value = chave;
    if(erro==true){
      document.form1.procfiscal.focus();
      document.form1.procfiscal.value = '';
    }
  } else {

    alert('Processo de protocolo não está neste departamento atualmente! \nDepartamento atual do processo:'+depart);
		document.form1.procfiscal.focus();
    document.form1.procfiscal.value = '';
		document.form1.nome.value = '';
		return false;
  }
}
function js_mostraprocfiscal1(chave1,chave2,dep_prot,depart,dep_atual){

  if (dep_prot == dep_atual) {

  	document.form1.procfiscal.value = chave1;
  	document.form1.nome.value = chave2;
  	db_iframe_procfiscal.hide();
  }else {

    alert('Processo de protocolo não está neste departamento atualmente! \nDepartamento atual do processo:'+depart);
		return false;
  }
}

function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_lancamento','func_fis_lancamento_alteracao.php?db_opcao=<?=$db_opcao?>&funcao_js=parent.js_preenchepesquisa|dl_notificacao_lancamento&fislanc=1','Pesquisa',true);
}

function js_pesquisare(num,origem){
   js_OpenJanelaIframe('','db_iframe_lancamento','func_fis_lancamento001.php?db_opcao=<?=$db_opcao?>&funcao_js=parent.js_preenchepesquisa|dl_notificacao_lancamento&origem='+origem+'&num='+num,'Pesquisa',true);
}

function js_preenchepesquisa(chave){
  db_iframe_lancamento.hide();
  <?php 
    if($db_opcao == 2 || $db_opcao == 22){
      echo " location.href = 'fis1_fis_lancamento002.php?abas=1&chavepesquisa='+chave;";
    }elseif($db_opcao == 33 || $db_opcao == 3){
      echo " location.href = 'fis1_fis_lancamento003.php?abas=1&chavepesquisa='+chave;";
    }
  ?>
}
function js_abre(pagina){
  js_OpenJanelaIframe('','db_iframe_consulta',pagina,'Pesquisa',true,0);
}

</script>

<?php
 if($nl02_codigo == ""){
        $j14_nome= "";
      }else{
        echo"<script>
        js_ruas1(false)
        </script>";
      }

if($nl02_codi == ""){
  $j13_descr = "";
}else{
  echo"<script>
        js_bairro1(false)
        </script>";
}

?>
