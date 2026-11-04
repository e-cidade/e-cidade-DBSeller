<?php
set_time_limit(0);
require_once ("libs/db_stdlib.php");
require_once ("libs/db_conecta.php");
require_once ("libs/db_sessoes.php");
require_once ("libs/db_usuariosonline.php");
require_once ("dbforms/db_funcoes.php");

require_once ("classes/db_lista_classe.php");
require_once ("classes/db_listadeb_classe.php");
require_once ("classes/db_listanotifica_classe.php");

require_once ("libs/db_sql.php");
require_once ("classes/db_termo_classe.php");
require_once ("classes/db_cgm_classe.php");
require_once("libs/db_app.utils.php");
require_once("classes/db_listacda_classe.php");
require_once("dbforms/db_classesgenericas.php");

$clcgm           = new cl_cgm;
$cllista         = new cl_lista;
$cllistadeb      = new cl_listadeb;
$cllistanotifica = new cl_listanotifica;
$clListaCda      = new cl_listacda;
$clrotulo        = new rotulocampo;

$clrotulo->label('DBtxt10');
$clrotulo->label('DBtxt11');
$clrotulo->label('k60_codigo');
$clrotulo->label('k60_descr');
$clrotulo->label('k51_procede');
$clrotulo->label('k51_descr');
$clrotulo->label('k31_situacao');
$clrotulo->label('am14_mensagem');
$instit = db_getsession("DB_instit");
db_postmemory($HTTP_POST_VARS);
parse_str($HTTP_SERVER_VARS['QUERY_STRING']);
db_app::load("scripts.js");
db_app::load("prototype.js");
db_app::load("strings.js");
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
<body bgcolor=#CCCCCC bgcolor="#CCCCCC" onload="js_habilita();"  >
  <form class="container" name="form1" method="post">
    <fieldset>
      <legend>Procedimentos - Geração de Arquivos de Remessa</legend>
      <table class="form-container">
        <tr>
          <td align="right" nowrap title="<?=@$Tk60_codigo?>" >
            <?php db_ancora(@$Lk60_codigo, "js_pesquisalista(true);", 4);?>
          </td>
          <td align="left">
            <?php
              db_input("k60_codigo",  4, $Ik60_codigo, true, "text", 4, "onchange='js_pesquisalista(false);'");
              db_input("k60_descr",  40, $Ik60_descr,  true, "text", 3, "");
            ?>
          </td>
        </tr>
        <tr>
          <td align="right" nowrap title="<?=@$Tk31_situacao?>" >
	    <?=$Lk31_situacao?>
          </td>
          <td align="left">
            <?php
              db_input("k31_situacao",  40, $Ik31_situacao,  true, "text", 3, "");
            ?>
	    <input type="button" id="detalhes"  value="Exibir Detalhes da Lista" onclick="js_pesquisadetalhes();">
          </td>
        </tr>
      </table>
    </fieldset>
    <input type="button" id="processar"  value="Processar" onclick="js_processar();">
  </form>
<?php
db_menu(db_getsession("DB_id_usuario"), db_getsession("DB_modulo"), db_getsession("DB_anousu"), db_getsession("DB_instit"));
?>
</body>
</html>

<script>

var sUrlRPC = "jur4_proceletronicoremessa.RPC.php";

function js_processar(){

  var iLista                             = $F('k60_codigo');
  var oParametros                        = new Object();
  oParametros.exec                       = 'processar';
  oParametros.iLista                     = iLista;
  js_divCarregando(_M('tributario.juridico.jur4_certidarqremessa001.processando_arquivo'),'msgBox');

   var oAjaxLista  = new Ajax.Request(sUrlRPC,
                                             {method: "post",
                                              parameters:'json='+Object.toJSON(oParametros),
                                              onComplete: js_retornoProcessar
                                             });

}
function js_retornoProcessar(oAjax){

    js_removeObj('msgBox');
    var oRetorno = JSON.parse(oAjax.responseText);

    if (oRetorno.status == 1) {

      alert(oRetorno.message);
      location.href = "jur4_proceletronicoprocessa001.php";

    }

}

function js_habilita(){

  if($F('k60_codigo') == null || $F('k60_codigo') == '' ) {
    $('processar').disabled = true;
  } else {
    $('processar').disabled = false;
  }

}

function js_pesquisalista(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('top.corpo','db_iframe_lista','func_listacda.php?funcao_js=parent.js_mostralista1|k60_codigo|k60_descr|k31_situacao&exibetj=true','Pesquisa',true);
  }else{
    js_OpenJanelaIframe('top.corpo','db_iframe_lista','func_listacda.php?pesquisa_chave='+document.form1.k60_codigo.value+'&funcao_js=parent.js_mostralista&exibetj=true','Pesquisa','false');
  }
}


function js_pesquisadetalhes(){
    js_OpenJanelaIframe('top.corpo','db_iframe_lista','func_listacda.php?&exibetj=true&detalhe=true&lista='+document.form1.k60_codigo.value,'Pesquisa',true);
}

function js_mostralista(chave,chave1,erro){
  document.form1.k60_descr.value = chave;
  document.form1.k31_situacao.value = chave1;
  if(erro==true){
    document.form1.k60_descr.focus();
    document.form1.k60_descr.value = '';
    document.form1.k31_situacao.value = '';
  }
  db_iframe_lista.hide();
  js_habilita();
}

function js_mostralista1(chave1,chave2,chave3){
  document.form1.k60_codigo.value = chave1;
  document.form1.k60_descr.value = chave2;
  document.form1.k31_situacao.value = chave3;
  db_iframe_lista.hide();
  js_habilita();
}

</script>
<script>

$("k60_codigo").addClassName("field-size2");
$("k60_descr").addClassName("field-size7");
$("k31_situacao").addClassName("field-size4");

</script>
