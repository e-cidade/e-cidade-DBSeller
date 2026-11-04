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
 *  Foundation, Inc., 59 Temple Place, Suite 350, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_fis_sanitario_classe.php"));
require_once(modification("classes/db_fis_fiscal_classe.php"));

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

$db_botao = 1;
$db_opcao = 1;
$cliptubase = new cl_iptubase;
$cliptubase->rotulo->label();
$clissbase  = new cl_issbase;
$clissbase->rotulo->label("q02_inscr");
$clcgm = new cl_cgm;
$clcgm->rotulo->label("z01_numcgm");
$clsanitario = new cl_fis_sanitario;
$clsanitario->rotulo->label("y80_codsani");
$clfiscal = new cl_fis_fiscal;
$clfiscal->rotulo->label("y30_codnoti");
/** Código para extensão */
$clrotulo = new rotulocampo;
$clrotulo->label('y100_sequencial');
$clrotulo->label('z01_nome');
/** Fim do código para extensão */
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
<form name="form1" method="post" action="fis1_fis_auto001.php?pri=true&abas=1&como=<?=$optionComo?>"  onSubmit="return js_verifica_campos_digitados();">
  <fieldset>
   <legend>Auto de Infração </legend>
   <table border="0">
   <?php 
   // ******************************
   // VERIFICA SE A OPÇÃO FOI NUMCGM
   // ******************************
   if($optionComo == 'cgm'){
   ?>
     <tr>
      <td>
      <?php 
        db_ancora($Lz01_numcgm,' js_cgm(true); ',1);
      ?>
       </td>
       <td>
      <?php 
        db_input('z01_numcgm',5,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'");
        db_input('z01_nome',50,0,true,'text',3,"","z01_nomecgm");
      ?>
       </td>
     </tr>
     <script>
      onLoad = document.form1.z01_numcgm.focus();
      function js_testacamp(){
        var numcgm = document.form1.z01_numcgm.value;
        if(numcgm==""){
          alert("Informe um campo para prosseguir!");
          return false;
        }
        document.form1.submit();
      }
      </script>
      <?php 
      }
      // ****************************************
      // VERIFICA SE A OPÇÃO FOI MATRICULA IMÓVEL
      // ****************************************
      if($optionComo == 'matric'){
      ?>
     <tr>
       <td>
      <?php 
        db_ancora($Lj01_matric,' js_matri(true); ',1);
      ?>
       </td>
       <td>
      <?php 
        db_input('j01_matric',5,$Ij01_matric,true,'text',1,"onchange='js_matri(false)'");
        db_input('z01_nome',50,0,true,'text',3,"","z01_nomematri");
      ?>
       </td>
     </tr>
     <script>
      onLoad = document.form1.j01_matric.focus();
      function js_testacamp(){
        var matri = document.form1.j01_matric.value;
        if(matri==""){
          alert("Informe um campo para prosseguir!");
          return false;
        }
        document.form1.submit();
      }
      </script>
      <?php 
      }
      // *******************************************
      // VERIFICA SE A OPÇÃO FOI INSCRIÇÃO MUNICIPAL
      // *******************************************
      if($optionComo == 'munic'){
      ?>
     <tr>
       <td>
      <?php 
        db_ancora($Lq02_inscr,' js_inscr(true); ',1);
      ?>
       </td>
       <td>
      <?php 
        db_input('q02_inscr',5,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
        db_input('z01_nome',50,0,true,'text',3,"","z01_nomeinscr");
      ?>
       </td>
     </tr>
     <script>
      onLoad = document.form1.q02_inscr.focus();
      function js_testacamp(){
        var inscr = document.form1.q02_inscr.value;
        if(inscr==""){
          alert("Informe um campo para prosseguir!");
          return false;
        }
        document.form1.submit();
      }
    </script>
      <?php 
      }
      // *****************************************
      // VERIFICA SE A OPÇÃO FOI ALVARÁ SANITÁRIO
      // *****************************************
      if($optionComo == 'sani'){
      ?>
    <tr>
      <td nowrap title="<?=@$Ty80_codsani?>">
         <?php 
            db_ancora(@$Ly80_codsani,"js_sanitario(true);",1);
         ?>
      </td>
      <td>
        <?php 
            db_input('y80_codsani',5,$Iy80_codsani,true,'text',1,"onchange='js_sanitario(false)'");
            db_input('z01_nome',50,0,true,'text',3,"","z01_nomesani");
        ?>
      </td>
    </tr>
    <script>
      onLoad = document.form1.y80_codsani.focus();
      function js_testacamp(){
        var sani = document.form1.y80_codsani.value;
        if(sani==""){
          alert("Informe um campo para prosseguir!");
          return false;
        }
        document.form1.submit();
      }
    </script>
    <?php 
    }
    // ******************************************
    // VERIFICA SE A OPÇÃO FOI CÓDIGO NOTIFICAÇÃO
    // ******************************************
    if($optionComo == 'notif'){
    ?>
    <tr>
      <td nowrap title="<?=@$Ty30_codnoti?>">
         <?php 
            db_ancora(@$Ly30_codnoti,"js_noti(true);",1);
         ?>
      </td>
      <td>
        <?php 
            db_input('y30_codnoti',5,$Iy30_codnoti,true,'text',$db_opcao,"onchange='js_noti(false)'");
            db_input('z01_nome',50,0,true,'text',3,"","z01_nomenoti");
        ?>
      </td>
    </tr>
    <script>
      onLoad = document.form1.y30_codnoti.focus();
      function js_testacamp(){
        var noticod = document.form1.y30_codnoti.value;
        if(noticod==""){
          alert("Informe um campo para prosseguir!");
          return false;
        }
        document.form1.submit();
      }
    </script>
    <?php 
    }
    // ***************************************
    // VERIFICA SE A OPÇÃO FOI PROCESSO FISCAL
    // ***************************************
    if($optionComo == 'proc'){
    ?>

    <!-- Código para extensão  -->
    <tr>
      <td nowrap title="<?=@$Ty100_sequencial?>">
	<?php
            db_ancora(@$Ly100_sequencial, 'js_pesquisaprocfiscal(true)', $db_opcao);
        ?>
      </td>
      <td>
	<?php
	  db_input('procfiscal',5,$Iy100_sequencial,true,'text',$db_opcao," onchange='js_pesquisaprocfiscal(false);'");
	  db_input('nome',50,$Iz01_nome,true,'text',3,'');
	?>
      </td>
    </tr>
    <script>
      onLoad = document.form1.procfiscal.focus();
      function js_testacamp(){
        var proc = document.form1.procfiscal.value;
        if(proc == ""){
          alert("Informe um campo para prosseguir!");
          return false;
        }
        document.form1.submit();
      }
      </script>
      <?php 
      }
	// Variável não foi definida
	if(!isset($optionComo) || empty($optionComo) || $optionComo == ""){
      ?>
	<tr>
      <td>
      <?php 
       db_ancora($Lz01_numcgm,' js_cgm(true); ',1);
      ?>
       </td>
       <td>
      <?php 
       db_input('z01_numcgm',5,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'");
       db_input('z01_nome',50,0,true,'text',3,"","z01_nomecgm");
      ?>
       </td>
     </tr>
     <tr>
       <td>
      <?php 
       db_ancora($Lj01_matric,' js_matri(true); ',1);
      ?>
       </td>
       <td>
      <?php 
       db_input('j01_matric',5,$Ij01_matric,true,'text',1,"onchange='js_matri(false)'");
      db_input('z01_nome',50,0,true,'text',3,"","z01_nomematri");
      ?>
       </td>
     </tr>

     <tr>
       <td>
      <?php 
       db_ancora($Lq02_inscr,' js_inscr(true); ',1);
      ?>
       </td>
       <td>
      <?php 
       db_input('q02_inscr',5,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
       db_input('z01_nome',50,0,true,'text',3,"","z01_nomeinscr");
      ?>
       </td>
     </tr>
    <tr>
      <td nowrap title="<?=@$Ty80_codsani?>">
         <?php 
         db_ancora(@$Ly80_codsani,"js_sanitario(true);",1);
         ?>
      </td>
      <td>
        <?php 
        db_input('y80_codsani',5,$Iy80_codsani,true,'text',1,"onchange='js_sanitario(false)'");
        db_input('z01_nome',50,0,true,'text',3,"","z01_nomesani");
        ?>
      </td>
    </tr>
    <tr>
      <td nowrap title="<?=@$Ty30_codnoti?>">
         <?php 
         db_ancora(@$Ly30_codnoti,"js_noti(true);",1);
         ?>
      </td>
      <td>
        <?php 
        db_input('y30_codnoti',5,$Iy30_codnoti,true,'text',$db_opcao,"onchange='js_noti(false)'");
        db_input('z01_nome',50,0,true,'text',3,"","z01_nomenoti");
        ?>
      </td>
    </tr>
    <!-- Código para extensão  -->
    <tr>
      <td nowrap title="<?=@$Ty100_sequencial?>">
	<?php db_ancora(@$Ly100_sequencial, 'js_pesquisaprocfiscal(true)', $db_opcao); ?>
      </td>
      <td>
	<?php
	db_input('procfiscal',5,$Iy100_sequencial,true,'text',$db_opcao," onchange='js_pesquisaprocfiscal(false);'");
	db_input('nome',50,$Iz01_nome,true,'text',3,'');
	?>
      </td>
    </tr>

	<?php 
	}
	?>

    <!-- Fim do código para extensão -->
    </table>
   </fieldset>

   <input type="hidden" name="rnum"    value="" />
   <input type="hidden" name="rorigem" value="" />
   <input type="button" name="fiscal"  value="Incluir" onclick="return js_testacamp();" />

  </form>
  </div>
</body>
</html>
<script type="text/javascript">

<?php if (!isset($optionComo) || empty($optionComo)): ?>
    function js_testacamp() {
      var matri  = $F("j01_matric");
      var inscr  = $F("q02_inscr");
      var numcgm = $F("z01_numcgm");
      var sani   = $F("y80_codsani");
      var noti   = $F("y30_codnoti");

      if (matri=="" && inscr=="" && numcgm=="" && sani=="" && noti==""){
        alert("Informe um campo para prosseguir!");
        return false;
      }
      document.form1.submit();
    }
<?php  endif; ?>

function js_sanitario(mostra){
  var sani=document.form1.y80_codsani.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_sanitario','func_fis_sanitario.php?funcao_js=parent.js_preenchesanitario|y80_codsani|z01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_sanitario','func_fis_sanitario.php?pesquisa_chave='+sani+'&funcao_js=parent.js_preenchesanitario1','Pesquisa',false);
  }
}
function js_preenchesanitario(chave,chave1){
  document.form1.y80_codsani.value = chave;
  document.form1.z01_nomesani.value = chave1;
  db_iframe_sanitario.hide();
}
function js_preenchesanitario1(chave,chave1,erro){
  document.form1.z01_nomesani.value = chave1;
  if(erro==true){
    document.form1.y80_codsani.focus();
    document.form1.y80_codsani.value = '';
  }
}
function js_matri(mostra){
  var matri=document.form1.j01_matric.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe3','func_iptubase.php?funcao_js=parent.js_mostramatri|j01_matric|z01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe3','func_iptubase.php?pesquisa_chave='+matri+'&funcao_js=parent.js_mostramatri1','Pesquisa',false);
  }
}
function js_mostramatri(chave1,chave2){
  document.form1.j01_matric.value = chave1;
  document.form1.z01_nomematri.value = chave2;
  /** Código para extensão */
  document.form1.z01_numcgm.value = '';
  document.form1.z01_nomecgm.value = '';
  document.form1.q02_inscr.value = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.y80_codsani.value = '';
  document.form1.z01_nomesani.value = '';
  document.form1.y30_codnoti.value = '';
  document.form1.z01_nomenoti.value = '';
  document.form1.procfiscal.value = '';
  document.form1.nome.value = '';
  /** Fim do código para extensão */
  db_iframe3.hide();
}
function js_mostramatri1(chave,erro){
  document.form1.z01_nomematri.value = chave;
  /** Código para extensão */
  document.form1.z01_numcgm.value = '';
  document.form1.z01_nomecgm.value = '';
  document.form1.q02_inscr.value = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.y80_codsani.value = '';
  document.form1.z01_nomesani.value = '';
  document.form1.y30_codnoti.value = '';
  document.form1.z01_nomenoti.value = '';
  document.form1.procfiscal.value = '';
  document.form1.nome.value = '';
  /** Fim do código para extensão */
  if(erro==true){
    document.form1.j01_matric.focus();
    document.form1.j01_matric.value = '';
  }
}

function js_inscr(mostra){
  var inscr=document.form1.q02_inscr.value;
  console.log(inscr);
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe_issbase','func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_issbase','func_issbase.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1','Pesquisa',false);
  }
}
function js_mostrainscr(chave1,chave2){

//console.log('oi'); return false;

  document.form1.q02_inscr.value = chave1;
  document.form1.z01_nomeinscr.value = chave2;

  /** Código para extensão */

  if (document.form1.z01_numcgm) {
    document.form1.z01_numcgm.value = '';
  }

  if (document.form1.z01_nomecgm) {
    document.form1.z01_nomecgm.value = '';
  }

  if (document.form1.j01_matric) {
    document.form1.j01_matric.value = '';
  }

  if (document.form1.z01_nomematri) {
    document.form1.z01_nomematri.value = '';
  }

  if (document.form1.y80_codsani) {
    document.form1.y80_codsani.value = '';
  }

  if (document.form1.z01_nomesani) {
    document.form1.z01_nomesani.value = '';
  }

  if (document.form1.y30_codnoti) {
    document.form1.y30_codnoti.value = '';
  }

  if (document.form1.z01_nomenoti) {
    document.form1.z01_nomenoti.value = '';
  }

  if (document.form1.procfiscal) {
    document.form1.procfiscal.value = '';
  }

  if (document.form1.nome) {
    document.form1.nome.value = '';
  }

  /** Fim do código para extensão */
  db_iframe_issbase.hide();
}
function js_mostrainscr1(chave,erro){
  document.form1.z01_nomeinscr.value = chave;
  /** Código para extensão */
  document.form1.z01_numcgm.value = '';
  document.form1.z01_nomecgm.value = '';
  document.form1.j01_matric.value = '';
  document.form1.z01_nomematri.value = '';
  document.form1.y80_codsani.value = '';
  document.form1.z01_nomesani.value = '';
  document.form1.y30_codnoti.value = '';
  document.form1.z01_nomenoti.value = '';
  document.form1.procfiscal.value = '';
  document.form1.nome.value = '';
  /** Fim do código para extensão */
  if(erro==true){
    document.form1.q02_inscr.focus();
    document.form1.q02_inscr.value = '';
    /** Código para extensão */
    document.form1.z01_nomeinscr.value = '';
    document.form1.z01_nomeinscr.focus();
    /** Fim código para extensão */
  }
}

function js_cgm(mostra){
  var cgm=document.form1.z01_numcgm.value;
  if(mostra==true){
    js_OpenJanelaIframe('','func_nome','func_nome.php?funcao_js=parent.js_mostracgm|0|1','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','func_nome','func_nome.php?pesquisa_chave='+cgm+'&funcao_js=parent.js_mostracgm1','Pesquisa',false);
  }
}
function js_mostracgm(chave1,chave2){
  document.form1.z01_numcgm.value = chave1;
  document.form1.z01_nomecgm.value = chave2;
  /** Código para extensão */
  document.form1.j01_matric.value = '';
  document.form1.z01_nomematri.value = '';
  document.form1.q02_inscr.value = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.y80_codsani.value = '';
  document.form1.z01_nomesani.value = '';
  document.form1.y30_codnoti.value = '';
  document.form1.z01_nomenoti.value = '';
  document.form1.procfiscal.value = '';
  document.form1.nome.value = '';
  /** Fim do código para extensão */
  func_nome.hide();
}
function js_mostracgm1(erro,chave){
  document.form1.z01_nomecgm.value = chave;
  /** Código para extensão */
  document.form1.j01_matric.value = '';
  document.form1.z01_nomematri.value = '';
  document.form1.q02_inscr.value = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.y80_codsani.value = '';
  document.form1.z01_nomesani.value = '';
  document.form1.y30_codnoti.value = '';
  document.form1.z01_nomenoti.value = '';
  document.form1.procfiscal.value = '';
  document.form1.nome.value = '';
  /** Fim do código para extensão */
  if(erro==true){
    document.form1.z01_numcgm.focus();
    document.form1.z01_numcgm.value = '';
    /** Código para extensão */
    document.form1.z01_nomecgm.value = '';
    document.form1.z01_nomecgm.focus();
    /** Fim código para extensão */
  }
}
function js_noti(mostra){
  var noti=document.form1.y30_codnoti.value;
  if(mostra==true){
    js_OpenJanelaIframe('','db_iframe','func_fis_fiscal.php?funcao_js=parent.js_mostrafiscal|y30_codnoti|y30_nome','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe','func_fis_fiscal.php?pesquisa_chave='+noti+'&funcao_js=parent.js_mostrafiscal1','Pesquisa',false);
  }
}
function js_mostrafiscal(chave1,chave2){

  document.form1.y30_codnoti.value = chave1;
  document.form1.z01_nomenoti.value = chave2;
  /** Código para extensão */
  document.form1.z01_numcgm.value = '';
  document.form1.z01_nomecgm.value = '';
  document.form1.q02_inscr.value = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.y80_codsani.value = '';
  document.form1.z01_nomesani.value = '';
  document.form1.j01_matric.value = '';
  document.form1.z01_nomematri.value = '';
  document.form1.procfiscal.value = '';
  document.form1.nome.value = '';
  /** Fim do código para extensão */
  db_iframe.hide();
}
function js_mostrafiscal1(chave,erro){
  document.form1.z01_nomenoti.value = chave;
  /** Código para extensão */
  document.form1.z01_numcgm.value = '';
  document.form1.z01_nomecgm.value = '';
  document.form1.q02_inscr.value = '';
  document.form1.z01_nomeinscr.value = '';
  document.form1.y80_codsani.value = '';
  document.form1.z01_nomesani.value = '';
  document.form1.j01_matric.value = '';
  document.form1.z01_nomematri.value = '';
  document.form1.procfiscal.value = '';
  document.form1.nome.value = '';
  /** Fim do código para extensão */
  if(erro==true){
    document.form1.y30_codnoti.focus();
    document.form1.y30_codnoti.value = '';
  }
}

/** Código para extensão */

/**
 * Mostra a janela para a pesquisa do processo fiscal
 * @param boolean mostra
 * @return void
 */
function js_pesquisaprocfiscal(mostra){
    if (mostra == true) {
        js_OpenJanelaIframe('', 'db_iframe_procfiscal', 'func_fis_procfiscal_alt.php?funcao_js=parent.js_mostraprocfiscal1|y100_sequencial|z01_nome|db_depart_protocolo|db_descr_depart|db_depart_atual|z01_numcgm|j01_matric|q02_inscr|y80_codsani|y30_codnoti','Pesquisa',true);
    } else {
   if(document.form1.procfiscal.value != ''){
        js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?pesquisa_chave='+document.form1.procfiscal.value+'&funcao_js=parent.js_mostraprocfiscal','Pesquisa',false);
         }else{
	     document.form1.nome.value = '';
        }
    }
}

/**
 * Executa a pesquisa pelo processo fiscal
 *
 * @param {String} chave
 * @param {String} erro
 * @param {String} dep_prot
 * @param {String} depart
 * @param {String} dep_atual
 * @return {Void|Boolean}
 */
function js_mostraprocfiscal(chave,erro,dep_prot,depart,dep_atual,z01_numcgm,j01_matric,q02_inscr,y80_codsani,y30_codnoti){

    if (dep_prot == dep_atual) {
        document.form1.nome.value = (chave != '') ? chave : '';
        document.form1.z01_numcgm.value = (z01_numcgm != '') ? z01_numcgm : '';
        document.form1.j01_matric.value = (j01_matric != '') ? j01_matric : '';
        document.form1.q02_inscr.value = (q02_inscr != '') ? q02_inscr : '';
        document.form1.y80_codsani.value = (y80_codsani != '') ? y80_codsani : '';
        document.form1.y30_codnoti.value = (y30_codnoti != '') ? y30_codnoti : '';
        document.form1.z01_nomeinscr.value = (chave != '') ? chave : '';
        if (z01_numcgm == '') {
          document.form1.z01_nomecgm.value = '';
        }
        if (j01_matric == '') {
          document.form1.z01_nomematri.value = '';
        }
        if (q02_inscr == '') {
          document.form1.z01_nomeinscr.value = '';
        }
        if (y80_codsani == '') {
          document.form1.z01_nomesani.value = '';
        }
        if (y30_codnoti == '') {
          document.form1.z01_nomenoti.value = '';
        }
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

/**
 * Executa a pesquisa pelo processo fiscal
 *
 * @param {String} chave1
 * @param {String} chave2
 * @param {String} dep_prot
 * @param {String} depart
 * @param {String} dep_atual
 * @return {Void|Boolean}
 */function js_mostraprocfiscal1(chave1,chave2,chave3,chave4,chave5,chave6,chave7,chave8,chave9,chave10){
    if (chave3 == chave5) {

	document.form1.procfiscal.value = (chave1 != '') ? chave1 : '';
  	document.form1.nome.value = (chave2 != '') ? chave2 : '' ;

        <?php if ($optionComo == 'cgm'): ?>
          document.form1.z01_numcgm.value = (chave6 != '') ? chave6 : '';
        <?php endif; ?>

        <?php if ($optionComo == 'matric'): ?>
        document.form1.j01_matric.value = (chave7 != '') ? chave7 : '';
        <?php endif; ?>

        <?php if ($optionComo == 'munic'): ?>
        document.form1.q02_inscr.value = (chave8 != '') ? chave8 : '';
        document.form1.z01_nomeinscr.value = (chave2 != '') ? chave2 : '' ;
        <?php endif; ?>

        <?php if ($optionComo == 'sani'): ?>
        document.form1.y80_codsani.value = (chave9 != '') ? chave9 : '';
        <?php endif; ?>

        <?php if ($optionComo == 'notif'): ?>
        document.form1.y30_codnoti.value = (chave10 != '') ? chave10 : '';
        <?php endif; ?>


        /*document.form1.procfiscal.value = (chave1 != '') ? chave1 : '';
  	document.form1.nome.value = (chave2 != '') ? chave2 : '' ;
        document.form1.z01_numcgm.value = (chave6 != '') ? chave6 : '';
        document.form1.j01_matric.value = (chave7 != '') ? chave7 : '';
        document.form1.q02_inscr.value = (chave8 != '') ? chave8 : '';
        document.form1.y80_codsani.value = (chave9 != '') ? chave9 : '';
        document.form1.y30_codnoti.value = (chave10 != '') ? chave10 : '';
        document.form1.z01_nomeinscr.value = (chave2 != '') ? chave2 : '' ;
        if (chave6 == '') {
          document.form1.z01_nome.value = '';
        }
        if (chave7 == '') {
          document.form1.z01_nomematri.value = '';
        }
        if (chave8 == '') {
          document.form1.z01_nomeinscr.value = '';
        }
        if (chave9 == '') {
          document.form1.z01_nomesani.value = '';
        }
        if (chave10 == '') {
          document.form1.z01_nomenoti.value = '';
        }*/
  	    db_iframe_procfiscal.hide();
    }else {
        alert('Processo de protocolo não está neste departamento atualmente! \nDepartamento atual do processo:'+depart);
	return false;
    }
}

</script>
