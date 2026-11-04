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
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_utils.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('d63_banco');
$clrotulo->label('k15_codage');
$clrotulo->label('j01_matric');
$j18_nomefunc = "func_iptubase.php";
?>
<html>
<head>
  <?php
    db_app::load(array(
      "estilos.css",
      "prototype.js",
      "scripts.js",
      "strings.js",
      "DBLookUp.widget.js",
      "EmissaoRelatorio.js"
    ));
  ?>
</head>
<body class="body-default">
  <div class="container">
  	<form name="form1" enctype="multipart/form-data" method="post" action="">
    <fieldset>
      <legend>Cadastro em Conta por Matrícula</legend>
      <table>
      	<tr>
          <td align="rigth" ><strong>Data Inicial :</strong>
          <?php db_inputdata('datai','01','01',db_getsession("DB_anousu"),true,'text',4,'onchange="js_dataini(this);"')?>
          </td>
          <td align="left" ><strong>Data Final :</strong>
          <?php
           $datausu = date("Y/m/d",db_getsession("DB_datausu"));
           $dataf_ano = substr($datausu,0,4);
           $dataf_mes = substr($datausu,5,2);
           $dataf_dia = substr($datausu,8,2);

          ?>
          <?php db_inputdata('dataf',$dataf_dia,$dataf_mes,$dataf_ano,true,'text',4,'onchange="js_datafim(this);"')?>
          </td>
        </tr>
        <tr>
          <td title="<?php echo $Tj01_matric; ?>">
          <?php db_ancora($Lj01_matric, "js_mostramatricula(true,'$j18_nomefunc');", 2); ?>
          </td>
          <td>
          <input type="text" name="j01_matric" id="j01_matric" maxlength = "8" size="8" autocomplete="off" onkeyup="js_ValidaCampos(this,1,'Matrícula do Imóvel','t','f',event);" onblur="js_ValidaMaiusculo(this,'f',event);" onchange  = "js_mostramatricula(false,'<?=$j18_nomefunc?>')" title     = "Codigo da matrícula do imovel para identificar o proprietário de um determinado lote. Campo:j01_matric "/>
          <?php db_input("z01_nome", 40, $Iz01_nome, true, 'text', 5); ?>
          </td>
        </tr>
      </table>
    </fieldset>

    <input type="button" name="emitir" id="emitir" value="Emitir" onclick="return js_verifica();"/>
  </div>

  <?php db_menu(); ?>
</form>
<script type="text/javascript">

function js_dataini(obj){
//alert(obj.value);
	data_inicio = obj.value;
	datai = data_inicio.split("/");
	document.getElementById("datai_dia").value = datai[0];
	document.getElementById("datai_mes").value = datai[1];
	document.getelementById("datai_ano").value = datai[2];
}
function js_datafim(obj){
	data_fim = obj.value;
	dataf = data_fim.split("/");
	document.getElementById("dataf_dia").value = dataf[0];
	document.getElementById("dataf_mes").value = dataf[1];
	document.getelementById("dataf_ano").value = dataf[2];
}
function js_mostramatricula(mostra, nome_func){
  if(mostra==true){
    if(nome_func != "func_iptubase.php") {
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matric',nome_func+'?funcao_js=parent.js_preenchematricula|0|1','Pesquisa',true);
    } else {
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matric',nome_func+'?funcao_js=parent.js_preenchematricula3|0|1|2','Pesquisa',true);
    }
  }else {
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_matric',nome_func+'?pesquisa_chave='+document.form1.j01_matric.value+'&funcao_js=parent.js_preenchematricula2','Pesquisa',false);
  }
}
function js_preenchematricula3(chave,chave1,chave2){

  document.form1.j01_matric.value = chave;
  document.form1.z01_nome.value   = chave2;
  db_iframe_matric.hide();

}
function js_preenchematricula(chave,chave1){

  document.form1.j01_matric.value = chave;
  document.form1.z01_nome.value   = chave1;
  db_iframe_matric.hide();

}
function js_preenchematricula2(chave,chave1){

  if(chave1 == false) {
    document.form1.z01_nome.value = chave;
    db_iframe_matric.hide();
  }else {
    document.form1.z01_nome.value   = chave;
    document.form1.j01_matric.value = "";
    db_iframe_matric.hide();
  }
  if(document.form1.j01_matric.value == '' && document.form1.z01_nome.value == ''){
    document.form1.z01_nome.value   = '';
  }
}

	function js_verifica(){
		var anoi = new Number(document.form1.datai_ano.value);
        var anof = new Number(document.form1.dataf_ano.value);
        if(anoi.valueOf() > anof.valueOf()){
           alert('Intervalo de data invalido. Verifique !.');
           return false;
        } else if(document.form1.datai.value == ''){
           alert('Data Inicial e obrigatoria. Verifique !.');
           return false;
        } else if(document.form1.dataf.value == ''){
           alert('Data Final e obrigatoria. Verifique !.');
           return false;
        } else if(document.form1.j01_matric.value == ''){
           alert('Matricula e obrigatoria. Verifique !.');
           return false;
        } else {
        	var oParametros = {
        	  iMatric: $F('j01_matric'),
        	  iDatai: document.form1.datai_ano.value+'-'+document.form1.datai_mes.value+'-'+document.form1.datai_dia.value,
        	  iDataf: document.form1.dataf_ano.value+'-'+document.form1.dataf_mes.value+'-'+document.form1.dataf_dia.value
        	};
            new EmissaoRelatorio("arr2_reldebcontapedidomatricula002.php", oParametros).open();
        }

    }
  </script>
</body>
</html>
