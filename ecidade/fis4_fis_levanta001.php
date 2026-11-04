<?php
/**
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

$opcao = $_GET['valor'];


require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_issbase_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));
require_once(modification("classes/db_fis_procfiscal_classe.php"));

$clissbase = new cl_issbase;
$clcgm     = new cl_cgm;
$clrotulo  = new rotulocampo;

db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);

$db_botao = true;
$db_opcao = 2;

$sStringRedireciona = 'q02_inscr';

$clrotulo->label("z01_nome");
$clrotulo->label("z01_numcgm");
$clrotulo->label("q02_inscr");
$clrotulo->label("y100_sequencial");

if(isset($q02_inscr) && $q02_inscr!=""){

  $result = $clissbase->sql_record($clissbase->sql_query($q02_inscr,'z01_nome'));
  if($clissbase->numrows > 0){

    db_fieldsmemory($result,0);
    db_redireciona("fis4_fis_levanta004.php?tipo=q02_inscr&valor=$q02_inscr&procfiscal=$procfiscal");
  }else{
    $msgerro='Inscrição inválida.';
  }

}else if(isset($z01_numcgm) && $z01_numcgm!=''){

  $sStringRedireciona = 'z01_numcgm';
  $sql01   = $clissbase->sql_query_file('','q02_inscr','',"q02_numcgm=$z01_numcgm");
  $result  = $clissbase->sql_record($sql01);
  $numrows = $clissbase->numrows;

  if( $numrows == 0 ){   //quando não tiver inscrição para o z01_numcgm informado

    $clcgm->sql_record($clcgm->sql_query_file($z01_numcgm,"z01_nome"));
    $numrows01 = $clcgm->numrows;
    if($numrows01==0){
      $msgerro = 'Numcgm inválido.';
    }else if($numrows01==1){  //quando o z01_numcgm for válido
      db_redireciona("fis4_fis_levanta004.php?tipo=z01_numcgm&valor=$z01_numcgm&procfiscal=$procfiscal");
    }

  }else if($numrows==1){  // uma inscrição para o z01_numcgm

    db_fieldsmemory($result,0);
	  $result01 = $clcgm->sql_record($clcgm->sql_query_file($z01_numcgm,"z01_nome"));
    db_fieldsmemory($result01,0);
    db_redireciona("fis4_fis_levanta004.php?tipo=q02_inscr&valor=$q02_inscr");

  }else if($numrows>1){   // varias inscrições para o z01_numcgm

    $sStringRedireciona = 'q02_inscr';
    $result  = $clcgm->sql_record($clcgm->sql_query_file($z01_numcgm,"z01_nome"));
    db_fieldsmemory($result,0);
    $varias_inscr = true;
  }
}

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="container">

  <form name="form1" method="post" action=""  onSubmit="return js_verifica_campos_digitados();" >

    <fieldset>
      <legend>Levantamento</legend>

      <table border="0">
      <?php 

      if(isset($varias_inscr) || isset($filtroquery)){//quando tiver várias inscrições para um cgm
      ?>
        <tr>
          <td>
            <?=$Lz01_numcgm?>
          </td>
          <td>
          <?php 
            if(empty($z01_numcgmx)){
              $z01_numcgmx=$z01_numcgm;
            }
            db_input('z01_numcgm',6,$Iz01_numcgm,true,'text',3,"","z01_numcgmx");
            db_input('q02_inscr',6,0,true,'hidden',3);

          ?>
          </td>
        </tr>
        <tr>
          <td>
            <?=$Lz01_nome?>
          </td>
          <td>
          <?php 
            db_input('z01_nome',40,$Iz01_nome,true,'text',3,"");
          ?>
          </td>
        </tr>
        <tr>
          <td colspan='2'>
          <?php 
            if(!empty($sql01)){
             db_lovrot($sql01,15,"()","","js_retorna|q02_inscr");
            }
          ?>
          </td>
        </tr>
      </table>
      </fieldset>
      <input type="submit" name="entrar" value="Entrar">
      <input type="button" name="voltar" value="Voltar" onclick="js_voltar();">

      <?php 
      }else{
      ?>
           
      <?php if($opcao == 'munic'): ?>
        <tr>
          <td title="<?=$Tq02_inscr?>">
            <?php db_ancora($Lq02_inscr,' js_inscr(true); ',1); ?>
          </td>
          <td>
            <?php 
              db_input('q02_inscr',5,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
              db_input('z01_nome',30,0,true,'text',3,"","z01_nomeinscr");
            ?>
          </td>
        </tr>
        </table>
       </fieldset>
       <input type="submit" name="entrar" value="Entrar" onclick="return js_testacamp('munic')" />
      <?php elseif($opcao == 'nome'): ?>
        <tr>
          <td title="<?=$Tz01_numcgm?>">
            <?php db_ancora($Lz01_nome,' js_cgm(true); ',1); ?>
          </td>
          <td>
            <?php 
              db_input('z01_numcgm',5,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'");
              db_input('z01_nome',30,0,true,'text',3,"","z01_nomecgm");
            ?>
          </td>
        </tr>
        </table>
       </fieldset>
       <input type="submit" name="entrar" value="Entrar" onclick="return js_testacamp(nome)" />
      <?php elseif($opcao == 'proc'): ?>
        <tr>
          <td nowrap title="<?=@$Ty100_sequencial?>">
            <?php db_ancora('Processo Fiscal ','js_pesquisaprocfiscal(true)',1); ?>
          </td>
          <td>
            <?php  
              db_input('q02_inscr',5,$Iq02_inscr,true,'hidden',1,"");
              db_input('z01_numcgm',30,0,true,'hidden',3,"","z01_numcgm");
              db_input('z01_nome',30,0,true,'hidden',3,"","z01_nomecgm");
              db_input('z01_nome',30,0,true,'hidden',3,"","z01_nomeinscr");
              db_input('procfiscal',5,@$Iy100_sequencial,true,'text',1,"onchange='js_pesquisaprocfiscal(false)'");
              db_input('nome',30,@$Iz01_nome,true,'text',3,"","nome");
            ?>
          </td>
        </tr>
        </table>
       </fieldset>
       <input type="submit" name="entrar" value="Entrar" onclick="return js_testacamp('proc')" />
      <?php else: ?>
        <tr>
          <td title="<?=$Tq02_inscr?>">
            <?php db_ancora($Lq02_inscr,' js_inscr(true); ',1); ?>
          </td>
          <td>
            <?php 
              db_input('q02_inscr',5,$Iq02_inscr,true,'text',1,"onchange='js_inscr(false)'");
              db_input('z01_nome',30,0,true,'text',3,"","z01_nomeinscr");
            ?>
          </td>
        </tr>
        <tr>
          <td title="<?=$Tz01_numcgm?>">
            <?php db_ancora($Lz01_nome,' js_cgm(true); ',1); ?>
          </td>
          <td>
            <?php 
              db_input('z01_numcgm',5,$Iz01_numcgm,true,'text',1,"onchange='js_cgm(false)'");
              db_input('z01_nome',30,0,true,'text',3,"","z01_nomecgm");
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Ty100_sequencial?>">
            <?php db_ancora('Processo Fiscal ','js_pesquisaprocfiscal(true)',1); ?>
          </td>
          <td>
            <?php  
              db_input('procfiscal',5,@$Iy100_sequencial,true,'text',1,"onchange='js_pesquisaprocfiscal(false)'");
              db_input('nome',30,@$Iz01_nome,true,'text',3,"","nome");
            ?>
          </td>
        </tr>
        </table>
       </fieldset>
       <input type="submit" name="entrar" value="Entrar" onclick="return js_testacamp('all')" />
      <?php endif; ?>  
      <?php 
      }
      ?>
    </form>

<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script type="text/javascript">

function js_testacamp(param){
  if (param == 'proc') {
    var procfiscal = document.form1.procfiscal.value;
    if (procfiscal == '') {
      alert('Informe um processo fiscal para pesquisa!');
      return false;
    }
  } else if (param == 'munic'){
    var inscr  = document.form1.q02_inscr.value;
    if (inscr == '') {
      alert('Informe uma inscrição para pesquisa!');
      return false;
    }
  } else if (param == 'nome') {
    var numcgm = document.form1.z01_numcgm.value;
    if (numcgm == '') {
      alert('Informe um CGM para pesquisa!');
      return false;
    }
  } else {
    var inscr  = document.form1.q02_inscr.value;
    var numcgm = document.form1.z01_numcgm.value;
    var procfiscal = document.form1.procfiscal.value;

    if (inscr == '' && numcgm == '' && procfiscal == '') {
      alert('Informe um campo para pesquisa!');
      return false;
    }
  }
  return true;
}
function js_retorna(inscr){

  console.log("fis4_fis_levanta004.php?tipo=<?php echo $sStringRedireciona ?>&valor="+inscr);
  location.href = "fis4_fis_levanta004.php?tipo=<?php echo $sStringRedireciona ?>&valor="+inscr;
  return;
}
function js_voltar(){
  location.href="<?=basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])?>";
}
function js_inscr(mostra){
  var inscr=document.form1.q02_inscr.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr','func_issbase.php?funcao_js=parent.js_mostrainscr|q02_inscr|z01_nome','Pesquisa',true);
  }else{
    if(inscr!=""){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr','func_issbase.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1','Pesquisa',false);
    }else{
      document.form1.z01_nomeinscr.value = "";
    }
  }
}
function js_mostrainscr(chave1,chave2){
  document.form1.q02_inscr.value = chave1;
  document.form1.z01_nomeinscr.value = chave2;
  db_iframe_inscr.hide();
}
function js_mostrainscr1(chave,erro){
  document.form1.z01_nomeinscr.value = chave;
  if(erro==true){
    document.form1.q02_inscr.focus();
    document.form1.q02_inscr.value = '';
  }
}

function js_cgm(mostra){
  var cgm=document.form1.z01_numcgm.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_numcgm','func_nome.php?funcao_js=parent.js_mostracgm|0|1','Pesquisa',true);
  }else{
    if(cgm!=""){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_numcgm','func_nome.php?pesquisa_chave='+cgm+'&funcao_js=parent.js_mostracgm1','Pesquisa',false);
    }else{
      document.form1.z01_nomecgm.value = '';
    }
  }
}
function js_mostracgm(chave1,chave2){
  document.form1.z01_numcgm.value = chave1;
  document.form1.z01_nomecgm.value = chave2;
  db_iframe_numcgm.hide();
}
function js_mostracgm1(erro,chave){
  document.form1.z01_nomecgm.value = chave;
  if(erro==true){
    document.form1.z01_numcgm.focus();
    document.form1.z01_numcgm.value = '';
  }
}

function js_pesquisaprocfiscal(mostra){
    if (mostra == true) {
        js_OpenJanelaIframe('', 'db_iframe_procfiscal', 'func_fis_procfiscal_alt.php?funcao_js=parent.js_mostraprocessofiscal1|y100_sequencial|z01_nome|y101_numcgm|y103_inscr','Pesquisa',true);
    } else {
      if(document.form1.procfiscal.value != ''){
        js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal_alt.php?pesquisa_chave='+document.form1.procfiscal.value+'&funcao_js=parent.js_mostraprocessofiscal&procLev=1','Pesquisa',false,'0','1','775','390');
      }else{
        document.form1.nome.value = '';
      }
    }
}

function js_mostraprocessofiscal1(chave1, chave2, chave3, chave4) {
   if(chave4 != ''){
    document.form1.q02_inscr.value = chave4;
    document.form1.z01_nomeinscr.value = chave2;
  }else{
    document.form1.z01_numcgm.value = chave3;
    document.form1.z01_nomecgm.value = chave2;
  }
  document.form1.procfiscal.value = (chave1 != '') ? chave1 : '';
  document.form1.nome.value = (chave2 != '') ? chave2 : '' ;
  db_iframe_procfiscal.hide();
}

function js_mostraprocessofiscal(chave, erro, chave2, chave3) {
  console.log(chave2,chave3);
  if(erro==false){
    document.form1.nome.value = (chave != '') ? chave : '';
    if(chave3 != ''){
      document.form1.q02_inscr.value = chave3;
      document.form1.z01_nomeinscr.value = chave;
    }else{
      document.form1.z01_numcgm.value = chave2;
      document.form1.z01_nomecgm.value = chave;
    }
  }else{
    document.form1.procfiscal.focus();
    document.form1.nome.value = chave;
    document.form1.procfiscal.value    = '';
    document.form1.z01_numcgm.value    = '';
    document.form1.z01_nomecgm.value   = '';
    document.form1.q02_inscr.value     = '';
    document.form1.z01_nomeinscr.value = '';
  }
}
</script>
<?php 
if(isset($msgerro)){
  db_msgbox($msgerro);
}
?>
