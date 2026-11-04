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
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <link rel="stylesheet" type="text/css" href="scripts/jquery-2.1.1.min.js" />
  <script type="text/javascript" src="ext/javascript/alertify/src/alertify.js"></script>
  <link rel="stylesheet" type="text/css" href="ext/javascript/alertify/themes/alertify.core.css" />
  <link rel="stylesheet" type="text/css" href="ext/javascript/alertify/themes/alertify.default.css" />

  <style>
    .quadro {
      display: block;
      width: 100%;
      border: 0px solid #000000;
      position:relative;
    }
    iframe {
      width: 100%;
      height: 99%;
      border: 0px;
    }
    html, body {

      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0px;
      border: 0px solid #000000;
      font-family:monospace;
      font-size:medium;
    }

    #quadro_corpo {
      height: calc(100% - 100px);
    }

  </style>
  <script>
    /*
    require.config({
      "baseUrl" : "skins/dev2.0/scripts",
      "paths"   : {
        "jQuery"    : ["libs/jquery/jquery-2.1.1.min"],
        "jQueryUI"  : ["libs/jquery-ui/jquery-ui.min"],
        "Plugin"    : ["plugins"]
      },
      "shim": {
        "jQuery"  : { exports: "$"},
        "jQueryUI": { exports: "$", deps: ["jQuery"] },
        "alertify": { exports: "alertify"}
      }
    });
    */
  </script>
</head>
<body onunload="js_fechaJanela()">
<div id="sistema" style="height: 100%; width: 100% ">
  <div id="quadro_topo"   class="quadro" style="height: 80px; position:relative;">
    <iframe src="topo.php?uso=<?=$uso?>" name="topo"    scrolling="no"   id="topo"></iframe>
  </div>
  <div id="quadro_corpo"  class="quadro">
    <iframe src="instit.php"             name="corpo"   scrolling="auto" id="corpo"></iframe>
  </div>
  <div id="quadro_status" class="quadro" style="height: 20px;">
    <iframe src="status.php"             name="bstatus" scrolling="no"   id="bstatus" ></iframe>
  </div>
</div>
<script>
</script>
</body>
</html>