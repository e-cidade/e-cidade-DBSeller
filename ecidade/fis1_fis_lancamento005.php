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

$optionComo = $_GET['como'];

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($HTTP_POST_VARS);
$clcriaabas = new cl_criaabas;
?>
  <html>
  <head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body class="abas">

  <table valign="top" marginwidth="0" width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
       <td>
       <?php

         $clcriaabas->identifica = array("lancamento"   => "Notificação de Lançamento",
                                         "lanclevanta"  => "Levantamento",
                                         "lanctipo"     => "Procedência",
                                         "fiscais"      => "Fiscais",
                                         "testem"       => "Testemunhas",
                                         "responsavel"  => "Responsáveis",
                                         "precalculo"   => "Pré-cálculo");

         $clcriaabas->title      = array("lancamento"   => "Notificação de Lançamento",
                                         "lanclevanta"  => "Levantamento",
                                         "lanctipo"     => "Procedência",
                                         "fiscais"      => "Fiscais",
                                         "testem"       => "Testemunhas",
                                         "responsavel"  => "Responsáveis",
                                         "precalculo"   => "Pré-cálculo");


         $clcriaabas->src        = array("lancamento"   => "fis1_fis_lancamento001.php?abas=1&como=$optionComo",
                                         "lanclevanta"  => "fis1_fis_lanclevanta001.php",
                                         "lanctipo"     => "fis1_fis_lancamentotipo001.php",
                                         "fiscais"      => "fis1_fis_lancusu001.php",
                                         "testem"       => "fis1_fis_lanctestem001.php",
                                         "responsavel"  => "fis1_fis_lancrespons001.php",
                                         "precalculo"   => "fis1_fis_lancprecalc001.php");
         $clcriaabas->cria_abas();

       ?>
       </td>
    </tr>
  </table>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<?php
if(isset($db_opcao) && $db_opcao==2){

  echo "
     <script>
  	   function js_src(){

  	    document.formaba.lancamento.size = 20;
        iframe_lancamento.location.href  = 'fis1_fis_lancamento002.php?abas=1';\n
  	   }
	     js_src();
     </script>";
  exit;
}else if(isset($db_opcao) && $db_opcao==3){

  echo "
       <script>
    	   function js_src(){

    	    document.formaba.lancamento.size            = 15;
          iframe_lancamento.location.href             ='fis1_fis_lancamento003.php?abas=1';\n
          document.formaba.lanclevanta.disabled       = true;
    	    document.formaba.lanctipo.disabled          = true;
    	    document.formaba.fiscais.disabled           = true;
    	    document.formaba.testem.disabled            = true;
          document.formaba.responsavel.disabled       = true;
          document.formaba.precalculo.disabled        = true;
    	   }
  	     js_src();
       </script>";
  exit;
}
  echo "
	   <script>
       document.formaba.lanclevanta.disabled     = true;
	     document.formaba.lanctipo.disabled        = true;
	     document.formaba.fiscais.disabled         = true;
	     document.formaba.testem.disabled          = true;
       document.formaba.responsavel.disabled     = true;
       document.formaba.precalculo.disabled      = true;
	     document.formaba.lancamento.size          = 15;
     </script>";
  exit;
?>
