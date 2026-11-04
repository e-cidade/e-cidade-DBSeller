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
$clrotulo->label('codret');

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
      <legend>Pagamentos por Débido em Conta</legend>
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
            <td>
              <b>Período de:</b>
            </td>
            <td colspan="2">
              <select name="idPer" id="idPer">
                <option value="1">Crédito</option>
                <option value="2">Pagamento</option>
              </select>
            </td>
          </tr>
        <tr>
          <td>
            <?php
              db_ancora($Ld63_banco,' js_bancos(true); ',$db_opcao);
            ?>
          </td>
          <td>
            <?php
              db_input('d63_banco',5,$Id63_banco,true,'text',1,"onchange='js_bancos(false)'");
              db_input('nome_banco',40,"",true,'text',3);
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Tk15_codage?>">
          <?=@$Lk15_codage?>
          </td>
          <td>
            <?php
              db_input('k15_codage',10,$Ik15_codage,true,'text',$db_opcao,"")
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?=@$Tcodret?>"><b>Código do Retorno (CodRet):</b>
          </td>
           <td> <?php db_input('codret',5,1,true,"text",1); ?> </td>
        </tr>
      </table>
    </fieldset>

    <input type="button" name="emitir" id="emitir" value="Emitir" onclick="return js_verifica();"/>
  </div>

  <?php db_menu(); ?>
</form>
<script type="text/javascript">

  function js_bancos(mostra){
    var bancos=document.form1.d63_banco.value;
    if(mostra==true){
      js_OpenJanelaIframe('','db_iframe2','func_bancos.php?funcao_js=parent.js_mostrabancos|0|1','Pesquisa',true);
    }else{
      js_OpenJanelaIframe('','db_iframe2','func_bancos.php?pesquisa_chave='+bancos+'&funcao_js=parent.js_mostrabancos1','Pesquisa',false);
    }
  }
  function js_mostrabancos(chave1,chave2){
    document.form1.d63_banco.value = chave1;
    document.form1.nome_banco.value = chave2;
    db_iframe2.hide();
  }
  function js_mostrabancos1(chave,erro){
    document.form1.nome_banco.value = chave;
    if(erro==true){
      document.form1.d63_banco.focus();
      document.form1.d63_banco.value = '';
    }
  }
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
        }
        if(document.form1.d63_banco.value == '' && document.form1.k15_codage.value == '' && document.form1.codret.value == ''){
            alert('Informe os dados Bancarios ou do CodRet!.');
            return false;
        }

        if(document.form1.d63_banco.value != ''){
          if(document.form1.k15_codage.value == ''){
            alert('Agencia e obrigatoria. Verifique !.');
            return false;
          }
        }

        var oParametros = {
          iBco:      $F('d63_banco'),
          iAgencia:  $F('k15_codage'),
          iPeriodo:  $F('idPer'),
          iCodRet:   $F('codret'),
          iDatai: document.form1.datai_ano.value+'-'+document.form1.datai_mes.value+'-'+document.form1.datai_dia.value,
          iDataf: document.form1.dataf_ano.value+'-'+document.form1.dataf_mes.value+'-'+document.form1.dataf_dia.value
        };
        new EmissaoRelatorio("arr2_reldebcontapagamento002.php", oParametros).open();


    }
  </script>
</body>
</html>
