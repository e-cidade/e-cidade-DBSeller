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
require_once(modification("classes/db_db_syscampo_classe.php"));
require_once(modification("classes/db_db_sysarqcamp_classe.php"));
require_once(modification("classes/db_db_syscampodep_classe.php"));

$cldb_syscampo = new cl_db_syscampo;
$cldb_syscampodep = new cl_db_syscampodep;
$cldb_sysarqcamp = new cl_db_sysarqcamp;

$clrotulo = new rotulocampo;
$clrotulo->label("z01_numcgm");
$clrotulo->label("z01_nome");
$clrotulo->label("z01_ender");
$clrotulo->label("z01_munic");
$clrotulo->label("z01_cgccpf");

$db_opcao = 1;
$db_botao = true;

parse_str($_SERVER["QUERY_STRING"], $queryString);

$z01_nome = str_replace('|', '%', $_GET['z01_nome']);
$z01_cgccpf = $_GET['z01_cgccpf'];

$where = [
  "z01_numcgm not in (select z10_numcgm from cgmcorreto where z10_proc is false) ",
  "z01_numcgm not in (select z11_numcgm from cgmerrado inner join cgmcorreto on z11_codigo = z10_codigo and z10_proc is false)",
];

if (!empty($z01_nome)) {
  $where[] = "to_ascii(z01_nome) like to_ascii('$z01_nome%')";
}
if (!empty($z01_cgccpf)) {
  $where[] = "z01_cgccpf = '$z01_cgccpf'";
}
?>
<html>

<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <script>
    function js_marca(obj) {
      var OBJ = document.form1;
      for (i = 0; i < OBJ.length; i++) {
        if (OBJ.elements[i].type == 'checkbox' && OBJ.elements[i].disabled == false) {
          OBJ.elements[i].checked = !(OBJ.elements[i].checked == true);
        }
      }
      return false;
    }

    function js_desab(cod) {
      obj = document.getElementsByTagName("INPUT")
      var marcado = false;
      for (i = 0; i < obj.length; i++) {
        if (obj[i].type == 'checkbox') {
          nome = obj[i].name.substring(4);
          if (nome == cod) {
            obj[i].checked = false;
            obj[i].disabled = true;
          } else {
            obj[i].disabled = false;
          }
        }
      }

    }
  </script>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
  <form name='form1'>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="100%" align="left" valign="top" bgcolor="#CCCCCC">
          <center>
            <?php
            if (count($where) > 2) {
              $filtro = implode(" and ", $where);
              $sql = "
              select z01_numcgm,z01_nome,z01_ender,z01_munic,z01_cgccpf 
                from cgm 
                where {$filtro} 
                order by z01_nome,z01_cgccpf,z01_munic,z01_ender
              ";
              // die($sql);
              $result = db_query($sql);

              echo "<table border='1' cellspacing='0' cellpadding='0' style='border-style:outset'> ";
              echo "<tr >
            <td style='border-style:outset' align='center'><b>Pri</b></td>
            <td style='border-style:outset' align='center'><b><a  title='Inverte Marcação' href='' onclick='return js_marca(this);return false;'>Sec</a></b></td>
            <td style='border-style:outset' align='center'><b>$RLz01_numcgm</b></td>
            <td style='border-style:outset' align='center'><b>$RLz01_cgccpf</b></td>
            <td style='border-style:outset' align='center'><b>$RLz01_nome</b></td>
            <td style='border-style:outset' align='center'><b>$RLz01_ender</b></td>
            <td style='border-style:outset' align='center'><b>$RLz01_munic</b></td>
            </tr>
            ";

              for ($i = 0; $i < pg_num_rows($result); $i++) {
                db_fieldsmemory($result, $i);
                echo "<tr>
              <td><input type='radio' name='pri' value='$z01_numcgm' " . ($i == 0 ? "checked" : "") . " onclick='js_desab(\"$z01_numcgm\");'></td>
              <td><input type='checkbox' name='sec_$z01_numcgm' value='$z01_numcgm' " . ($i == 0 ? "disabled=true" : "") . " ></td>
              <td nowrap>$z01_numcgm&nbsp</td>
              <td nowrap>$z01_cgccpf&nbsp</td>
              <td nowrap>$z01_nome&nbsp</td>
              <td nowrap>$z01_ender&nbsp</td>
              <td nowrap>$z01_munic&nbsp</td>
              </tr>
              ";
              }
              echo "</table>";
            }
            ?>
          </center>
        </td>
      </tr>
    </table>
  </form>
</body>

</html>