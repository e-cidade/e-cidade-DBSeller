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

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$oDaoLancamento       = db_utils::getDao("fis_lancamento");
$oDaoArrecad          = db_utils::getDao('arrecad');
$oDaoArrecant         = db_utils::getDao('arrecant');
$oDaoLancamentonumpre = db_utils::getDao("fis_lancamentonumpre");

if( isset($calcular) ){
  /**
   * Chama pl do precalculo
   */
  if( isset($recalcular) ){
    $oDaoLancamento->excluir_precalculo( $nl01_codlanc );
  }

  $rsprecalculo = $oDaoLancamento->sql_precalculo($nl01_codlanc);
  $sInfo     = db_utils::fieldsmemory($rsprecalculo, 0)->fc_fis_calculolancamentodeinfracao;
  echo "<script>parent.iframe_precalculo.location.href='fis1_fis_lancprecalc001.php?nl01_codlanc=".$nl01_codlanc."&info1=$sInfo';</script>";
  exit;

}elseif( !isset($info1) ){
    $info   = "Lançamento não pré-calculado.";
    $result = $oDaoLancamentonumpre->sql_record($oDaoLancamentonumpre->sql_query(null,"*",null,"nl16_codlanc = {$nl01_codlanc}"));

    if($oDaoLancamentonumpre->numrows > 0){

      db_fieldsmemory($result,0);      
      $result  = $oDaoArrecad->sql_record($oDaoArrecad->sql_query("","arrecad.*",""," arrecad.k00_numpre = $nl16_numpre and arreinstit.k00_instit = ".db_getsession('DB_instit') ));
      $result1 = $oDaoArrecant->sql_record($oDaoArrecant->sql_query("","*",""," arrecant.k00_numpre = $nl16_numpre"));

      if($oDaoArrecad->numrows > 0){
        $info = "Lançamento já calculado. Numpre = $nl16_numpre";
        $desabilita = true;
        $calculado = true;
      }elseif($oDaoArrecant->numrows > 0){
        $info = "Lançamento já Pago. Numpre = $nl16_numpre";
        $desabilita = true;
        $calculado = true;
      }else{
       
        $result = $oDaoLancamentonumpre->sql_record($oDaoLancamentonumpre->sql_query_precalculo(null,"*",null,"nl17_codlanc = {$nl01_codlanc}"));
        if($oDaoLancamentonumpre->numrows > 0){
          $info = "Lançamento já pré-calculado.";
          $desabilita = false;
        }
      }
    }else{

    }

}else{

  $info       = $info1;
  $desabilita = true;
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div class="container">

    <form method="post" name="form1" action="" onsubmit="return js_recalcular();">
      <?php
        db_input('nl01_codlanc',10,'',true,'hidden',3);
        if( isset($desabilita) ){
          db_input('recalcular',10,'',true,'hidden',3);
        }
      ?>

      <fieldset style="width: 650px; !important">

        <legend>Pré-cálculo da Notificação de Lançamento</legend>
        <table>
          <tr>
            <td><strong><?=$info?></strong></td>
          </tr>
        </table>
    	</fieldset>
      <?php
      if( isset($desabilita) ){
        echo "<input name=\"calcular\" type=\"submit\" ".(($calculado) ? "disabled" : "")." value=\"Recalcular\" />";
      }else{
        echo "<input name=\"calcular\" type=\"submit\" value=\"Calcular\" />";
      }
      ?>
    </form>
    <script type="text/javascript">
    function js_precalculo(){
      document.form1.nl01_codlanc.value=parent.iframe_lanc.document.form1.nl01_codlanc.value;
    }
    </script>
  </div>
</body>
</html>
<?php 
if( isset($desabilita) ){
  echo "
  <script>
  function js_recalcular(){
    var recalcular = confirm(\"Lançamento já pré-calculado. Deseja recalcular?\");
    if (recalcular == true) {
      return true;
    } else {
      return false;
    }
  }
</script>
  ";
}else{
    echo "
  <script>
  function js_recalcular(){
    return true;
  }
</script>
  ";
}
?>
