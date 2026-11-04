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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
$clrotulo = new rotulocampo;
$clrotulo->label("e60_numemp");
$clrotulo->label("e60_codemp");
$clrotulo->label("z01_nome");
$clrotulo->label("nome");

$db_opcao = 1;
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<script>
function js_abre(){
   obj = document.form1;
   query='';

   if (obj.e60_codemp.value != "" &&  obj.e60_codemp_fim.value == "") {

    obj.e60_codemp_fim.value = obj.e60_codemp.value;
   }

   if (obj.e60_numemp.value != "" &&  obj.e60_numemp_fim.value == "") {

     obj.e60_numemp_fim.value = obj.e60_numemp.value;
   }

   query += "&iCgm="+obj.e54_numcgm.value;
   query += "&e60_codemp_fim="+obj.e60_codemp_fim.value;
   query += "&e60_numemp_fim="+obj.e60_numemp_fim.value;

   if (obj.e60_numemp.value!=''){
       query += "&e60_numemp="+obj.e60_numemp.value;
   }else if (obj.e60_codemp.value!=''){
       query += "&e60_codemp="+obj.e60_codemp.value;
   }else{
     if((obj.dtini_dia.value !='') && (obj.dtini_dia.value !='') && (obj.dtini_mes.value !='')){
	   query +="&dtini_dia="+obj.dtini_dia.value+"&dtini_mes="+obj.dtini_mes.value+"&dtini_ano="+obj.dtini_ano.value;
     }
     if((obj.dtfim_dia.value !='') && (obj.dtfim_mes.value !='') && (obj.dtfim_ano.value !='')){
	   query +="&dtfim_dia="+obj.dtfim_dia.value+"&dtfim_mes="+obj.dtfim_mes.value+"&dtfim_ano="+obj.dtfim_ano.value;
     }
   }
   if(query==''){
      alert("Selecione algum numero de empenho ou indique o período!");
   }else{

     query += "&dtInicial="+$F('dtini')+"&dtFinal="+$F('dtfim');
      jan = window.open('emp2_emitenotaemp002.php?' + query,
                        '',
                        'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
      jan.moveTo(0,0);
   }
}
</script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
<table valign="top" marginwidth="0" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="top">
      <form name='form1'>
        <fieldset>
          <legend><b>Emite Empenho</b></legend>
          <table>
            <tr>
              <td nowrap title="<?=@$Te60_codemp?>">
                <?php db_ancora(@$Le60_codemp,"js_pesquisae60_codemp(true);",1); ?>
              </td>
              <td>
                <?php db_input('e60_codemp',15,$Ie60_codemp,true,'text',$db_opcao,"")  ?>
                  <strong> <? db_ancora("Até","js_pesquisae60_codemp_fim(true);",1); ?></strong>
                  <?php db_input('e60_codemp_fim',15,$Ie60_codemp,true,'text',$db_opcao,"")  ?>
              </td>
            </tr>
            <tr>
              <td nowrap title="<?=@$Te60_numemp?>">
                <?php db_ancora(@$Le60_numemp,"js_pesquisae60_numemp(true);",1); ?>
              </td>
              <td>
                <?php db_input('e60_numemp',15,$Ie60_numemp,true,'text',$db_opcao," onchange='js_pesquisae60_numemp(false);'")  ?>
                <strong> <?php db_ancora("Até","js_pesquisae60_numemp_fim(true);",1); ?></strong>
                <?php db_input('e60_numemp_fim',15,$Ie60_numemp,true,'text',$db_opcao," onchange='js_pesquisae60_numemp_fim(false);'")  ?>
              </td>
            </tr>
            <tr>
              <td nowrap >
                <?php
                  db_ancora(@$Lz01_nome, "js_pesquisae54_numcgm(true);", isset($emprocesso) && $emprocesso == true ? "3" : $db_opcao, "", "ancora_e54_numcgm");
                ?>
              </td>
                <td nowrap="nowrap">
                <?php
                  db_input('e54_numcgm', 10, "", true, 'text', isset($emprocesso) && $emprocesso == true ? "3" : $db_opcao, " onchange='js_pesquisae54_numcgm(false);'");
                  db_input('z01_nome', 48, $Iz01_nome, true, 'text', 3, '');
                ?>
                </td>
            </tr>

            <tr>
              <td align="left" >
                <strong>Período:</strong>
              </td>
              <td>
                <?php
                  db_inputdata('dtini',@$dia,@$mes,@$ano,true,'text',1,"");
                  echo "<strong> a </strong>";
                  db_inputdata('dtfim',@$dia,@$mes,@$ano,true,'text',1,"");
                ?>
              </td>
            </tr>
          </table>
        </fieldset>
      </form>
    </td>
  </tr>
  <tr>
    <td align='center'>
      <input name='pesquisar' type='button' value='Consultar' onclick='js_abre();'>
    </td>
  </tr>
</table>
<?php
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_pesquisae60_numemp(mostra){
  if(mostra==true){
    var sUrl = 'func_empempenho.php?funcao_js=parent.js_mostraempempenho1|e60_numemp';
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho',sUrl,'Pesquisa',true);
  }else{
     if(document.form1.e60_numemp.value != ''){
        var sUrl = 'func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp.value+'&funcao_js=parent.js_mostraempempenho';
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho',sUrl,'Pesquisa',false);
     }else{
       document.form1.e60_numemp.value = '';
     }
  }
}
function js_mostraempempenho(chave,erro){
  if(erro==true){
    document.form1.e60_numemp.focus();
    document.form1.e60_numemp.value = '';
  }
}
function js_mostraempempenho1(chave1,x){
  document.form1.e60_numemp.value = chave1;
  db_iframe_empempenho.hide();
}

function js_pesquisae60_codemp(mostra){
  if(mostra==true){
    var sUrl = 'func_empempenho.php?funcao_js=parent.js_mostraempempenho1|e60_numemp';
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho',sUrl,'Pesquisa',true);
  }else{
     if(document.form1.e60_numemp.value != ''){
        var sUrl = 'func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp.value+'&funcao_js=parent.js_mostraempempenho';
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho',sUrl,'Pesquisa',false);
     }else{
       document.form1.e60_numemp.value = '';
     }
  }
}

function js_pesquisae60_numemp_fim(mostra){
  if(mostra==true){
    var sUrl = 'func_empempenho.php?funcao_js=parent.js_mostraempempenho1_fim|e60_numemp';
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho_fim',sUrl,'Pesquisa',true);
  }else{
     if(document.form1.e60_numemp.value != ''){
        var sUrl = 'func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp_fim.value+'&funcao_js=parent.js_mostraempempenho_fim';
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho_fim',sUrl,'Pesquisa',false);
     }else{
       document.form1.e60_numemp.value = '';
     }
  }
}

function js_pesquisae60_codemp_fim(mostra){
  if(mostra==true){
    var sUrl = 'func_empempenho.php?funcao_js=parent.js_mostraempempenho1_fim|e60_numemp';
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho_fim',sUrl,'Pesquisa',true);
  }else{
     if(document.form1.e60_numemp.value != ''){
        var sUrl = 'func_empempenho.php?pesquisa_chave='+document.form1.e60_numemp_fim.value+'&funcao_js=parent.js_mostraempempenho_fim';
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_empempenho_fim',sUrl,'Pesquisa',false);
     }else{
       document.form1.e60_numemp.value = '';
     }
  }
}

function js_mostraempempenho_fim(chave,erro){
  if(erro==true){
    document.form1.e60_numemp_fim.focus();
    document.form1.e60_numemp_fim.value = '';
  }
}
function js_mostraempempenho1_fim(chave1,x){
  document.form1.e60_numemp_fim.value = chave1;
  db_iframe_empempenho_fim.hide();
}

function js_pesquisae54_numcgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_cgm', 'func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome', 'Pesquisa', true, '0', '1');
        } else {
            if (document.form1.e54_numcgm.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_cgm', 'func_nome.php?pesquisa_chave=' + document.form1.e54_numcgm.value + '&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
            } else {
                document.form1.z01_nome.value = '';
            }
        }
    }

    function js_mostracgm(erro, chave) {

        document.form1.z01_nome.value = chave;
        if (erro == true) {
            document.form1.e54_numcgm.focus();
            document.form1.e54_numcgm.value = '';
        } else {
           // js_debitosemaberto();
        }
    }

    function js_mostracgm1(chave1, chave2) {

        document.form1.e54_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        db_iframe_cgm.hide();

       // js_debitosemaberto();
    }

document.form1.e60_numemp_fim.value = '';
document.form1.e60_codemp_fim.value = '';
document.form1.e54_numcgm.value = '';
</script>
