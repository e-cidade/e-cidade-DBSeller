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
?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type="text/css">
    table.tableRegistros {
        margin-left: auto;
        margin-right: auto;
        border: 1px solid threedshadow;
        background-color: #EEE;
        border-spacing: 1;
        min-width: 650px;
    }

    table.tableRegistros th {
        background-color: #CCC;
        height: 30px;
    }

    table.tableRegistros td {
        height: 25px;
        border-bottom: 1px solid threedshadow;
    }

    table.tableRegistros tr:hover {
        background: #D7E4EA !important;
    }

    .cancelapagto {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    height: 20px;
    width: 100px;
    background-color: #AAAF96;
}
</style>
</head>
<script>
function js_removelinha(linha,linha1) {
  var tab = document.getElementById('tab');
  for(i=0;i<tab.rows.length;i++){
    if(linha == tab.rows[i].id){
      var totalapagar  = new Number(parent.document.form1.apagar.value);
      var totalapagar1 = new Number(document.getElementById(linha1).value);
      totalapagar = totalapagar - totalapagar1;
      parent.document.form1.apagar.value = totalapagar.toFixed(2);
      tab.deleteRow(i);
      break;
    }
  }
}
</script>
<body>
   <table class="tableRegistros" id="tab">
        <thead>
            <tr>    
                <th width="8%">Tipo</th>
                <th width="30%">Origem</th>
                <th width="8%">Valor Histórico</th>
                <th width="8%">Valor Corrigido</th>
                <th width="8%">Valor dos Juros</th>
                <th width="8%">Valor da Multa</th>
                <th width="8%">Valor do Desconto</th>
                <th width="10%">Valor à Pagar</th>
                <th width="12%">Cancela Pagto.</th>
            </tr>
        </thead>
  </table> 
</body>
</html>
