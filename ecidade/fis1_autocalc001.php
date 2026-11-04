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

$oDaoAuto       = db_utils::getDao('auto');
$oDaoArrecad    = db_utils::getDao('arrecad');
$oDaoArrecadOld = new  \cl_arreold();
$oDaoArrecant   = db_utils::getDao('arrecant');
$oDaoAutonumpre = db_utils::getDao('autonumpre');

if (isset($calcular) ){


  /**
   * Chama pl do calculo
   */
  $rsCalculo = $oDaoAuto->sql_calculo( $y50_codauto );
  $sInfo     = db_utils::fieldsmemory($rsCalculo, 0)->fc_autodeinfracao;
  
  $numpres = explode(".", $sInfo);

  if($numpres[0] == 'Cálculo do Auto de Infração Executado'){
    $numpre = substr($numpres[1], 8, 9);
    $result  = $oDaoArrecad->sql_record($oDaoArrecad->sql_query("","arrecad.*",""," arrecad.k00_numpre = $numpre" ));
      if ($oDaoArrecad->numrows > 0) {

        db_query("DELETE FROM caixa.arrehist WHERE k00_numpre = $numpre");

        $sqlIdhist = "SELECT nextval('arrehist_k00_idhist_seq') AS idHist;";
        $rsIdhist = db_query($sqlIdhist);
        $iIdHist = db_utils::fieldsMemory($rsIdhist, 0)->idhist;

        $codHistorico = 84;
        $historico = 'AUTO DE INFRACAO';
        $usuario = (!empty(db_getsession("DB_id_usuario")) ? db_getsession("DB_id_usuario") : 1);
        $dataCorrente = date("Y-m-d", db_getsession("DB_datausu"));
        $horaCorrente = date('H:i');

        $sqlArrehist = "INSERT INTO caixa.arrehist
                          VALUES
                          (
                            {$numpre},
                            1,
                            {$codHistorico},
                            '{$dataCorrente}',
                            '{$horaCorrente}',
                            {$usuario},
                            '{$historico}',
                            null,
                            {$iIdHist}
                          )";

        $rsArrehist = db_query($sqlArrehist);

        if (!$rsArrehist) {
          echo "Erro ao inserir na tabela arrehist.";
          exit;
        }
      }
  }

  echo "<script>parent.iframe_calculo.location.href='fis1_autocalc001.php?y50_codauto=".$y50_codauto."&info1=$sInfo';</script>";
  exit;

} elseif( !isset($info1) ){

    $info   = "Auto não calculado.";
    $result = $oDaoAutonumpre->sql_record($oDaoAutonumpre->sql_query(null,"*",null,"y50_codauto = {$y50_codauto}"));
    if ($oDaoAutonumpre->numrows > 0){
      db_fieldsmemory($result,0);
      $result  = $oDaoArrecad->sql_record($oDaoArrecad->sql_query("","arrecad.*",""," arrecad.k00_numpre = $y17_numpre and arreinstit.k00_instit = ".db_getsession('DB_instit') ));
      $result1 = $oDaoArrecant->sql_record($oDaoArrecant->sql_query("","*",""," arrecant.k00_numpre = $y17_numpre"));
      $result2 = $oDaoArrecadOld->sql_record($oDaoArrecadOld->sql_query("","*",""," arreold.k00_numpre = $y17_numpre" ));

      if ($oDaoArrecad->numrows > 0) {
        $desabilita = false;
        $info = "Auto já calculado. Numpre = $y17_numpre";
      } elseif($oDaoArrecant->numrows > 0) {
        $desabilita = false;
        $info = "Auto já Pago. Numpre = $y17_numpre";
      } elseif($oDaoArrecadOld->numrows > 0) {
        $desabilita = false;
        $info = " Auto de infração já importado para  dívida . Numpre = $y17_numpre";
      }

    }

} else{

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

    <form method="post" name="form1" action="">
      <?php db_input('y50_codauto',10,'',true,'hidden',3); ?>

      <fieldset style="width: 650px !important">

        <legend>Cálculo do Auto de Infração</legend>
        <table>
          <tr>
            <td><strong><?=$info?></strong></td>
          </tr>
        </table>
    	</fieldset>

      <input name="calcular" type="submit" value="Calcular" />
    </form>
    <script type="text/javascript">
    function js_calculo(){
      document.form1.y50_codauto.value=parent.iframe_auto.document.form1.y50_codauto.value;
    }
    </script>
  </div>
</body>
</html>
<?php
if( isset($desabilita) ){
  echo "<script>document.form1.calcular.disabled = true</script>";
}
?>