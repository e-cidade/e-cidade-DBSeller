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

//MODULO: itbi
require_once(modification("libs/db_conecta.php"));
$clitbiruralcaract->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("it01_guia");

$caracteristicas = $_GET['caracteristicas'];
if (!empty($caracteristicas)) {
  $arrayCaracteristicas = explode('|', $caracteristicas);
  $arrayAssociativo = array();

  foreach ($arrayCaracteristicas as $caracteristica) {
    $arrayAuxiliar = explode('X', $caracteristica);

    $id = $arrayAuxiliar[0];
    $valor = $arrayAuxiliar[1];

    $arrayAssociativo[$id] = $valor;
  }
}

?>
<form name="form1" method="post" action="">
 <center>
   <fieldset>
   <table border="0">
     <tr>
       <td>
		 <?php
		    $sWhere = "1 = 1";
		    if ( $oGet->tipo == "imovel" ) {
		      $sCampo = "f.j32_grupo";
		      $sWhere .= " and it19_tipocaract = 1 ";
		    } else if ( $oGet->tipo == "util" ) {
		      $sCampo =	"e.j32_grupo";
			    $sWhere .= " and it19_tipocaract = 2 ";
		    }

        if ( isset($oGet->guia) && trim($oGet->guia) != "" ) {
          $sSql  = "select *                                                                 ";
          $sSql .= "  from caracter                                         ";
          $sSql .= "       inner join cargrup      on  cargrup.j32_grupo         = caracter.j31_grupo          ";
          $sSql .= "        left join itbiruralcaract     on  caracter.j31_codigo       = itbiruralcaract.it19_codigo   ";
          $sSql .= "        left join itbitipocaract on itbitipocaract.it31_sequencial = itbiruralcaract.it19_tipocaract ";
          $sSql .= " where {$sWhere} and it19_guia = {$oGet->guia}";

			    $rsConsultaRuralCaract = db_query($sSql);
			    $iNumCaracter			 = pg_num_rows($rsConsultaRuralCaract);
          if ($iNumCaracter > 0) {
            for ( $iInd=0; $iInd < $iNumCaracter; $iInd++) {

		 	         $oCaraceter  = db_utils::fieldsMemory($rsConsultaRuralCaract,$iInd);

		 	         if ($_GET['tipo'] == 'imovel') {
                       $valor = $_GET['flagModificadoImovel'] ? $arrayAssociativo[$oCaraceter->j31_codigo] : $oCaraceter->it19_valor;
                     } else {
                       $valor = $_GET['flagModificadoUtil'] ? $arrayAssociativo[$oCaraceter->j31_codigo] : $oCaraceter->it19_valor;
                     }

		           echo " <tr> 																   	   						   ";
	      	     echo "   <td>																   	   						   ";
				       echo "	   <strong>$oCaraceter->j31_descr:</strong>							      						   ";
				       echo "	 </td>																	  						   ";
				       echo "	 <td>																							   ";
				       echo "	   <input type='text' size='6' name='".$oCaraceter->j31_codigo."' value='". $valor ."'>";
				       echo "	 </td>																	  						   ";
			 	       echo " </tr> 																  	   						   ";
	          }
          } else {
            $rsParamITBI = $clparitbi->sql_record($clparitbi->sql_query(db_getsession('DB_anousu'),$sCampo));
            if ( $clparitbi->numrows > 0 ) {

               $oParamITBI = db_utils::fieldsMemory($rsParamITBI,0);

               $rsCaracter   = $clcaracter->sql_record($clcaracter->sql_query_file(null,"*",null," j31_grupo = {$oParamITBI->j32_grupo}"));
               $iNumCaracter = $clcaracter->numrows;

               for ( $iInd=0; $iInd < $iNumCaracter; $iInd++) {

                  $oCaraceter  = db_utils::fieldsMemory($rsCaracter,$iInd);
                  $valor = $arrayAssociativo[$oCaraceter->j31_codigo] ? $arrayAssociativo[$oCaraceter->j31_codigo] : 0;

                  echo " <tr>                                            ";
                  echo "   <td>                                          ";
                  echo "     <strong>$oCaraceter->j31_descr:</strong>                      ";
                  echo "   </td>                                       ";
                  echo "   <td>                                      ";
                  echo "     <input type='text' size='6' name='".$oCaraceter->j31_codigo."' value='". $valor ."'> ";
                  echo "   </td>                                       ";
                  echo " </tr>                                           ";

               }
            }

          }
        } else {
		 	    $rsParamITBI = $clparitbi->sql_record($clparitbi->sql_query(db_getsession('DB_anousu'),$sCampo));

		 	    if ( $clparitbi->numrows > 0 ) {

		 	       $oParamITBI = db_utils::fieldsMemory($rsParamITBI,0);

		 	       $rsCaracter   = $clcaracter->sql_record($clcaracter->sql_query_file(null,"*",null," j31_grupo = {$oParamITBI->j32_grupo}"));
		 	       $iNumCaracter = $clcaracter->numrows;

		 	       for ( $iInd=0; $iInd < $iNumCaracter; $iInd++) {

		 	   	      $oCaraceter  = db_utils::fieldsMemory($rsCaracter,$iInd);
                      $valor = $arrayAssociativo[$oCaraceter->j31_codigo] ? $arrayAssociativo[$oCaraceter->j31_codigo] : 0;

		            echo " <tr> 																	   	     ";
	      		    echo "   <td>																	   	     ";
				        echo "	   <strong>$oCaraceter->j31_descr:</strong>							         ";
				        echo "	 </td>																	     ";
				        echo "	 <td>																	     ";
				        echo "	   <input type='text' size='6' name='".$oCaraceter->j31_codigo."' value='". $valor ."'> ";
				        echo "	 </td>																	  	 ";
				        echo " </tr> 																	  	   	 ";

		 	       }
		 	    }
        }
		 ?>
       </td>
     </tr>
   </table>
   </fieldset>
  </center>
  <input name="o" type="button" id="db_opcao" value="Enviar" onClick="preenche()">
</form>
<script>
function preenche(){
  const form = document.form1;
  var prefixo  = "";
  var caracteristicas   = "";

  for (var i = 0; i < form.length; i++) {
    if (form.elements[i].type == 'text') {
      caracteristicas += prefixo + form.elements[i].name + "X" + form.elements[i].value;
      prefixo = "|";
    }
  }

  <?php
  if ($oGet->tipo == "imovel") { ?>
    parent.document.form1.valorCaracImovel.value = caracteristicas;
    parent.document.form1.flagModificadoImovel.value = 1;
  <?php
  } else { ?>
    parent.document.form1.valorCaracUtil.value = caracteristicas;
    parent.document.form1.flagModificadoUtil.value = 1;
  <?php
  } ?>
  parent.js_fecha();
}
</script>
