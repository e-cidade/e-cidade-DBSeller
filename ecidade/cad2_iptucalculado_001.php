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
require_once(modification("classes/db_iptucalc_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

$cliptucalc = new cl_iptucalc;
?>
 <html>
  <head>
   <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
   <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
   <meta http-equiv="Expires" CONTENT="0">
   <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
   <script type="text/javascript">
    function js_AbreJanelaRelatorio() {

			if (document.form1.mesini.value>document.form1.mesfim.value){
				alert("Mês inicial naum pode ser maior q o mês final!!");
			}else{
				window.open('cad2_iptucalculado_002.php?exercicio='+document.form1.anousu.value+'&vlrvenalini='+document.form1.vlrvenalini.value+'&vlrvenalfim='+document.form1.vlrvenalfim.value+'&considerarisentos='+document.form1.considerarisentos.value+'&emitirpormes='+document.form1.emitirpormes.value+'&mesini='+document.form1.mesini.value+'&mesfim='+document.form1.mesfim.value,'','width=790,height=530,scrollbars=1,location=0');
			}

    }
   </script>
   <link href="estilos.css" rel="stylesheet" type="text/css">
  </head>
  <body class="body-default" onLoad="a=1" >

    <div class="container">

      <form name="form1" method="post" action="cad2_iptucalculado_002.php">

      <fieldset style="width: 570px">

        <legend>Relatório da posição do IPTU calculado</legend>

        <table border="0" class="form-container">
           <tr>
  	           <td><strong>Exercício:</strong></td>
               <td>
                  <select name="anousu" id="anousu">
                   <?php

  		               $sSql = "select j18_anousu as j23_anousu from cfiptu order by j18_anousu desc";
                     $result = db_query($sSql) or die($sSql);

                    for($i = 0;$i < pg_numrows($result);$i++){

                      db_fieldsmemory($result,$i);
                      $sel = "";
                      if($j23_anousu==date("Y")){
                       $sel = "selected";
                      }
                      echo "<option value=\"".($j23_anousu)."\" ".$sel.">".($j23_anousu)."</option>\n";
                    }
                   ?>
                  </select>
               </td>
           </tr>

          <tr>
            <td><strong>Valor Venal Inicial:</strong></td>
            <td><input name='vlrvenalini' type='text' value=''/></td>
          </tr>

          <tr>
           <td><strong>Valor Venal Final:</strong></td>
           <td><input name='vlrvenalfim' type='text' value=''/></td>
          </tr>

        	<tr>
  					<td><strong>Considerar isentos:</strong></td>
            <td><select name='considerarisentos'>
  					      <option value='n'>Não</option>
  					      <option value='s'>Sim</option>
  					    </select>
  					</td>
  			  </tr>

        	<tr>
  					<td><strong>Emitir demonstrativo por mês:</strong></td>
            <td><select name='emitirpormes'>
  					     <option value='s'>Sim</option>
  					     <option value='n'>Não</option>
  					   </select>
  					</td>
  			  </tr>

	        <tr>
           <td><strong>Mês inicial:</strong> </td><td>
             <?php
              $meses = array("01"=>"Janeiro","02"=>"Fevereiro","03"=>"Março","04"=>"Abril","05"=>"Maio","06"=>"Junho","07"=>"Julho","08"=>"Agosto","09"=>"Setembro","10"=>"Outubro","11"=>"Novembro","12"=>"Dezembro");
              $mesini = "01" ;
              db_select("mesini",$meses,true,"text",1);
             ?>
           </td>
  	      </tr>

	        <tr>
	           <td><strong>Mês final:</strong> </td><td>
	             <?php
  	             $mesfim = date("m");
  	             db_select("mesfim",$meses,true,"text",1);
	             ?>
	           </td>
	        </tr>
        </table>
      </fieldset>

      <input name="exibir_relatorio" type="button" id="exibir_relatorio" value="Exibir relat&oacute;rio" onClick="js_AbreJanelaRelatorio()"/>

    </form>
   </div>
   <?php
    db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
   ?>
  </body>
 </html>