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
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));

$clrotulo = new rotulocampo;
$clrotulo->label('DBtxt21');
$clrotulo->label('DBtxt22');

db_postmemory($_POST);
?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>

<script>
const exercicio = <?=db_getsession("DB_anousu")?>;
function js_emite(opcao,origem){
  var sUrl = "orc2_anexo1002.php";
        
  itemselecionado = 0;
  numElems = document.form1.qual_tipo_balanco.length;
  for (i=0;i<numElems;i++) {
    if (document.form1.qual_tipo_balanco[i].checked){ 
       itemselecionado = i;
    }
  }

  if (opcao == 3) {
     var data1 = new Date(document.form1.DBtxt21_ano.value,document.form1.DBtxt21_mes.value,document.form1.DBtxt21_dia.value,0,0,0);
     var data2 = new Date(document.form1.DBtxt22_ano.value,document.form1.DBtxt22_mes.value,document.form1.DBtxt22_dia.value,0,0,0);
     if(data1.valueOf() > data2.valueOf()){
       alert('Data inicial maior que data final. Verifique!');
       return false;
     }
     perini = document.form1.DBtxt21_ano.value+'-'+document.form1.DBtxt21_mes.value+'-'+document.form1.DBtxt21_dia.value;
     perfin = document.form1.DBtxt22_ano.value+'-'+document.form1.DBtxt22_mes.value+'-'+document.form1.DBtxt22_dia.value;
  } else if (opcao == 2) {
     if (document.form1.mesfin.value == 0) {
       mesfinal = 12;
     } else if (document.form1.mesfin.value < 10) {
       mesfinal = '0'+document.form1.mesfin.value;
     } else if (document.form1.mesfin.value == 'mes') {
       alert('Mês final do intervalo invalido.Verifique!');
       return false
     } else {
       mesfinal = document.form1.mesfin.value;
     }

     if (document.form1.mesini.value == 0) {
       mesinicial = 12;
     } else if(document.form1.mesini.value < 10) {
       mesinicial = '0'+document.form1.mesini.value;
     } else {
       mesinicial = document.form1.mesini.value;
     }
    
     perini = exercicio+'-'+mesinicial+'-01';
     perfin = exercicio+'-'+mesfinal+'-01';
  } else {
     perini = exercicio+'-01-01';
     perfin = exercicio+'-01-01';
  }

  sUrl += "?tipo_balanco="+document.form1.qual_tipo_balanco[itemselecionado].value;
  sUrl += "&esfera="+((document.form1.esfera.value == 0 || document.form1.esfera.value == 3)?"":document.form1.esfera.value);
  sUrl += "&perini="+perini;
  sUrl += "&perfin="+perfin;
  sUrl += "&opcao="+opcao;
  sUrl += "&origem="+origem;
  sUrl += "&db_selinstit="+document.form1.db_selinstit.value;
  sUrl += "&orgaounidade="+document.form1.orgaos.value;
  sUrl += "&mostrar_receita="+document.form1.mostrar_receita.value;
  sUrl += "&orientacao_pagina="+document.form1.orientacao_pagina.value;
  sUrl += "&mostrar_rodape="+document.form1.mostrar_rodape.value;
  jan = window.open(
            sUrl,
            '',
            'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0'
        );
  jan.moveTo(0,0);
}

//filtro OrgaoUnidade
function js_mostraLookUpOrgaoUnidade(){

  sel_instit  = new Number(document.form1.db_selinstit.value);
  if(sel_instit == 0){
     alert('Você não escolheu nenhuma Instituição. Verifique!');
     return false;
  }

  if (typeof db_iframe_orgao != 'undefined') {
    db_iframe_orgao.show();    
  } else {    
    js_OpenJanelaIframe('','db_iframe_orgao','func_selorcdotacao.php?&nivel=2A&db_selinstit='+document.form1.db_selinstit.value,'pesquisa',true);
  }
 
}
</script> 
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container">
    <form name="form1" method="post" action="" onsubmit="return js_verifica();">
    
        <table>
          <tr>
            <td colspan="3" style="text-align: center">
              <?php db_selinstit('', 450, 150); ?>
            </td>
          </tr>
          <tr>
            <td colspan="3" style="text-align: center">
              <table style="width:100%" class="form-container">
                <tr>
                  <td>Orgão/Unidade :</td>
                  <td>
                    <input name="seleciona" id="seleciona" type="button" value="Selecionar" onclick="js_mostraLookUpOrgaoUnidade();">
                    <input name="orgaos" id="orgaos" type="hidden" value="">
                    <input name="vernivel" id="vernivel" type="hidden" value="">
                  </td>
                </tr>
                <tr>
                  <td>Valores da Receita :</td>
                  <td>
                   <?php
                     db_select("mostrar_receita", ["S" => "SIM","N" => "NÃO"], true, 1);
                    ?>
                  </td>
                </tr>
                <tr>
                  <td title="Escolha a esfera">Esfera:</td>
                  <td>
                    <?php
                       db_select(
                           "esfera",
                           ["0" => "Selecione", "1" => "1 - Fiscal","2" => "2 - Seguridade","3" => "3 - Total"],
                           true,
                           1
                       );
                        ?>
                  </td>
                </tr>
                <tr>
                  <td title="Orientação Página">Orientação:</td>
                  <td>
                    <?php
                       db_select("orientacao_pagina", ["P" => "Retrato","L" => "Paisagem"], true, 1);
                    ?>
                  </td>
                </tr>  
                <tr>
                  <td title="Mostra rodapé">Mostra rodapé:</td>
                  <td>
                    <?php
                       db_select("mostrar_rodape", ["S"=> "SIM","N" => "NÃO"], true, 1);
                    ?>
                  </td>
                </tr>                               
              </table>
            </td>  
          </tr>
          <?php db_selorcbalanco(true, true, true); ?>
        </table>
        
    </form>
</div>    
<?php
  db_menu();
?>
</body>
</html>
