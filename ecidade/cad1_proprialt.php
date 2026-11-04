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
require_once(modification("classes/db_propri_classe.php"));
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_cgm_classe.php"));

db_postmemory($_SERVER);
db_postmemory($_POST);

$mostraApenasFracao = isset($_GET['apenasFracao']);

$db_botao    = 1;
$db_opcao    = 1;

$db_op        = $db_opcao;
$db_op02      = $db_opcao;
$outros      = false;
$testasel    = false;
$sqlerro     = false;

$cliptubase   = new cl_iptubase;
$clpropri     = new cl_propri;
$clpromitente = new cl_promitente;
$cltipoproprietario = new cl_tipoproprietario;
$clcgm = new cl_cgm;
$clcgm->rotulo->label();
$cltipoproprietario->rotulo->label();
$clrotulo    = new rotulocampo;
$rotulocampo = new rotulocampo;
$clpercposserural = new cl_percposserural;

$clpercposserural->rotulo->label();
$cliptubase->rotulo->label();
$clpropri->rotulo->label();
$clrotulo->label("j01_numcgm");
$rotulocampo->label("z01_nome");
$j42_numcgm = isset($j42_numcgm) && $j42_numcgm != ''
  ? $j42_numcgm
  : (isset($numcgm) && $numcgm != ''
    ? $numcgm
    : (isset($_POST['j42_numcgm']) && $_POST['j42_numcgm'] != ''
      ? $_POST['j42_numcgm']
      : null));

function validaPercentual($percentual, $matricula, $cgm)
{

  if (!empty($matricula) && !empty($cgm)) {
    $clpercposserural = new cl_percposserural;
    $where = "j166_matric = {$matricula} AND j166_numcgm <> {$cgm}";
    $sql = $clpercposserural->sql_query_file(null, "sum(j166_percentual) as soma", null, $where);
    $rs = db_query($sql);

    if ($rs && pg_num_rows($rs) > 0) {
      $percentual += db_utils::fieldsMemory($rs, 0)->soma;
    }
  }

  if ($percentual > 100) {
    return false;
  }

  return true;
}

if (isset($alterando)) {
  $j42_matric = $j01_matric;
}

if (isset($atualizar)) {
  $redirecionamento = "cad1_proprialt.php?j42_matric={$j42_matric}&j01_tipoimovel={$j01_tipoimovel}";

  if($mostraApenasFracao){
    $redirecionamento .= "&apenasFracao=true";
  }

  db_redireciona($redirecionamento);
}

try {

  if (isset($atualizar)) {
    $percentual = 100;
    $percentualMinimoProprietario = 0.0001;

    $sWhere = "j42_matric = $j42_matric AND j42_numcgm <> $j42_numcgm";
    $sSql = $clpropri->sql_query_file($j42_matric, null, "COALESCE(sum(j42_fracaoproprietario), 0) as soma", null, $sWhere);
    $rs = db_query($sSql);

    if ($rs && pg_num_rows($rs) > 0) {
      $percentual -= db_utils::fieldsMemory($rs, 0)->soma;
      $percentual -= $j42_fracaoproprietario;
    }

    $result = $cliptubase->sql_record(
      $cliptubase->sql_query_file(
        $j42_matric,
        "*",
        "",
        "j01_matric = {$j42_matric} AND j01_numcgm = {$j42_numcgm}"
      )
    );
    if ($cliptubase->numrows > 0) {

      $sqlerro = true;
      $db_opcao = 1;
      $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));

      if ($clpropri->numrows != 0) {
        $outros = true;
      }

      if ((round($percentual, 4) - $percentualMinimoProprietario) < 0) {
        $sqlerro = true;
        $erroMensagem = "Soma da fração não pode ultrapassar 100%.";
        $j42_numalt = $j42_numcgm;
      } else {
        $clpropri->j42_fracaoproprietario = $j42_fracaoproprietario;
        $clpropri->j42_arealoteproprietario = $j42_arealoteproprietario;
        $clpropri->alterar($j42_matric, $j42_numcgm);

        $cliptubase->j01_matric = $j42_matric;
        $cliptubase->j01_fracaoproprietario = $percentual;
        $cliptubase->alterar($j42_matric);

        $j42_numcgm = "";
        $z01_nome = "";
        $j42_fracaoproprietario = "";
        $j42_arealoteproprietario = "";
      }

      $outros = true;
    }
  } else if (isset($excluir)) {

    $clpropri->excluir($j42_matric, $j42_numcgm);

    if ($clpropri->erro_status == "0") {
      throw new Exception($clpropri->erro_msg);
    }

    $clpercposserural->excluir(null, "j166_matric = {$j42_matric} AND j166_numcgm = {$j42_numcgm}");

    $percentual = 100;
    $j42_numcgm = "";
    $z01_nome = "";

    $sSql = $clpropri->sql_query_file($j42_matric, null, "COALESCE(sum(j42_fracaoproprietario), 0) as soma");
    $rs = db_query($sSql);

    if ($rs && pg_num_rows($rs) > 0) {
      $percentual -= db_utils::fieldsMemory($rs, 0)->soma;
    }

    $cliptubase->j01_matric = $j42_matric;
    $cliptubase->j01_fracaoproprietario = $percentual;
    $cliptubase->alterar($j42_matric);

    db_fim_transacao();
    $j42_numcgm = "";
    $z01_nome = "";
    $j42_fracaoproprietario = "";
    $j42_arealoteproprietario = "";
  }

  if (isset($incluir)) {

    if (!validaPercentual($j166_percentual, $j42_matric, $j42_numcgm)) {
      $sqlerro = true;
      $erroMensagem = "Percentual de posse não pode ser maior do que 100%.";
    }

    if (!$sqlerro) {

      $clpercposserural->j166_matric = $j42_matric;
      $clpercposserural->j166_numcgm = $j42_numcgm;
      $clpercposserural->j166_percentual = !empty($j166_percentual) ? $j166_percentual : '0';
      $clpercposserural->incluir($j166_sequencial);

      $sSql = $clpropri->sql_query_file($j42_matric, null, "COALESCE(sum(j42_fracaoproprietario), 0) as soma");
      $rs = db_query($sSql);

      $percentual = 100;
      $percentualMinimoProprietario = 0.0001;

      if ($rs && pg_num_rows($rs) > 0) {
        $percentual -= db_utils::fieldsMemory($rs, 0)->soma;
        $percentual -= $j42_fracaoproprietario;
      }
      if ((round($percentual, 4) - $percentualMinimoProprietario) < 0) {
        $sqlerro = true;
        $erroMensagem = "Soma da fração não pode ultrapassar 100%.";
      } else {

        $sql_pessoa = "select * 
        from tipoproprietario where j163_tipoproprietario = $j42_tipoproprietario";
        $result_pessoa = db_query($sql_pessoa);
        if (!$result_pessoa) {

          throw new Exception("Não foi possível buscar tipo de proprietário");
        }

        db_fieldsmemory($result_pessoa, 0);

        $j42_numcgm = $_POST['j42_numcgm'];
        $cgmInstance = \CgmFactory::getInstanceByCgm($j42_numcgm);

        if (isset($_POST['j42_numcgm'])) {
          $oDaoCgm = new cl_cgm();
          $sSqlCgm = $oDaoCgm->sql_query($_POST['j42_numcgm']);
          $rsCgm = db_query($sSqlCgm);
          $resultadosCgm = db_utils::getCollectionByRecord($rsCgm);

          if (count($resultadosCgm) > 0) {
            $resultadoCgm = $resultadosCgm[0];
            $pessoaJuridica = $resultadoCgm->z01_cgccpf && (strlen($resultadoCgm->z01_cgccpf) == 14);
            $tipoProprietarioParaPessoaFisica = $j163_pesfisjur == 1;

            if ($pessoaJuridica && $tipoProprietarioParaPessoaFisica) {
              throw new Exception('Tipo de proprietário inválido para pessoa jurídica');
            }
          }
        }

        if ($cgmInstance instanceof CgmFisico && $j163_pesfisjur == 2) {

          $db_opcao = 1;
          $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));
          if ($clpropri->numrows != 0) {
            $outros = true;
          }

          throw new Exception("CGM deve ser de Pessoa Jurídica.");
        }

        if ($cgmInstance instanceof CgmJuridico && $j163_pesfisjur == 1) {

          $db_opcao = 1;
          $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));
          if ($clpropri->numrows != 0) {
            $outros = true;
          }

          throw new Exception("CGM deve ser de Pessoa Física.");
        }

        $result = $cliptubase->sql_record(
          $cliptubase->sql_query_file(
            $j42_matric,
            "*",
            "",
            "j01_matric = {$j42_matric} AND j01_numcgm = {$j42_numcgm}"
          )
        );
        if ($cliptubase->numrows > 0) {

          $sqlerro = true;
          $db_opcao = 1;
          $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));

          if ($clpropri->numrows != 0) {
            $outros = true;
          }

          throw new Exception("O cgm não pode ser o mesmo cadastrado como proprietário principal nesta matricula.");
        }

        $result = $clpromitente->sql_record($clpromitente->sql_query_file($j42_matric, $j42_numcgm));
        if ($clpromitente->numrows > 0) {

          $sqlerro = true;
          $db_opcao = 1;
          $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));
          if ($clpropri->numrows != 0) {
            $outros = true;
          }
          db_msgbox("O cgm não pode ser o mesmo cadastrado como promitente nesta matricula.");
        }

        $clpropri->j42_fracaoproprietario = $j42_fracaoproprietario;
        $clpropri->j42_arealoteproprietario = $j42_arealoteproprietario;
        $clpropri->j42_tipoproprietario = $j42_tipoproprietario;
        $clpropri->incluir($j42_matric, $j42_numcgm);

        if ($clpropri->erro_status == "0") {
          throw new Exception($clpropri->erro_msg);
        }

        $cliptubase->j01_matric = $j42_matric;
        $cliptubase->j01_fracaoproprietario = $percentual;
        $cliptubase->alterar($j42_matric);

        if ($cliptubase->erro_status == "0") {
          throw new Exception($cliptubase->erro_msg);
        }

        $j42_numcgm = "";
        $z01_nome = "";
        $outros = true;
      }
    }

    if ($tipoImovel == "2") {

      if (!validaPercentual($j166_percentual, $j42_matric, $j42_numcgm)) {

        throw new Exception("Percentual de posse não pode ser maior do que 100%.");
      }

      $clpercposserural->j166_matric = $j42_matric;
      $clpercposserural->j166_numcgm = $j42_numcgm;
      $clpercposserural->j166_percentual = !empty($j166_percentual) ? $j166_percentual : '0';
      $clpercposserural->incluir($j166_sequencial);
      if ($clpercposserural->erro_status == "0") {

        throw new Exception($clpercposserural->erro_msg);
      }
    }
  } else if (isset($alterar)) {

    $sql_pessoa    = "select * 
                        from tipoproprietario 
                       where j163_tipoproprietario = $j42_tipoproprietario";
    $result_pessoa = db_query($sql_pessoa);

    db_fieldsmemory($result_pessoa, 0);
    $cgmInstance = \CgmFactory::getInstanceByCgm($j42_numcgm);
    if ($cgmInstance instanceof CgmFisico && $j163_pesfisjur == 2) {

      $db_opcao = 1;
      $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));
      if ($clpropri->numrows != 0) {
        $outros = true;
      }

      throw new Exception("CGM deve ser de Pessoa Jurídica.");
    }

    if ($cgmInstance instanceof CgmJuridico && $j163_pesfisjur == 1) {

      $db_opcao = 1;
      $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));
      if ($clpropri->numrows != 0) {
        $outros = true;
      }
      throw new Exception("CGM deve ser de Pessoa Física.");
    }

    $clpropri->j42_tipoproprietario = $j42_tipoproprietario;
    $clpropri->alterar($j42_matric, $j42_numcgm);
    if ($clpropri->erro_status == "0") {
      throw new Exception($clpropri->erro_msg);
    }

    if (!$sqlerro && $tipoImovel == "2") {
      $clpercposserural->j166_percentual = !empty($j166_percentual) ? $j166_percentual : '0';
      $clpercposserural->alterar($j166_sequencial);
      if ($clpercposserural->erro_status == "0") {
        throw new Exception($clpercposserural->erro_msg);
      }
    }

    $cliptubase->j01_matric = $j42_matric;
    $cliptubase->j01_fracaoproprietario = 100 - $clpropri->j42_fracaoproprietario;
    $cliptubase->alterar($j42_matric);
    if ($clpropri->erro_status == "0") {
      throw new Exception($clpropri->erro_msg);
    }
  } else if (isset($j42_matric)) {

    if (isset($j42_matric) && isset($j42_numcgm) && (!isset($incluir) && !isset($atualizar) && !isset($excluir))) {
      $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, $j42_numcgm, "propri.*#cgm.z01_nome#a.z01_nome as z01_nomematri"));
      db_fieldsmemory($result, 0);
      $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "propri.*#cgm.z01_nome"));
      $j42_numalt = $j42_numcgm;
      if ($clpropri->numrows > 1) {
        $outros = true;
      } else {
        $outros = false;
      }
      if (isset($j42_matric) && !isset($j42_numcgm)) {

        $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));
        $numrows00 = $clpropri->numrows;
        if ($clpropri->numrows != 0) {
          @db_fieldsmemory($result, 0);
          $db_opcao = 1;
          $outros = true;
        }

        $sqlPercPosseRural = $clpercposserural->sql_record($clpercposserural->sql_query_file("", "*", "", "j166_matric = " . $j42_matric . " and j166_numcgm = " . $j42_numcgm));
        db_fieldsmemory($sqlPercPosseRural, 0);
      }
    }
  } else {

    $result = $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "a.z01_nome as z01_nomematri"));
    @db_fieldsmemory($result, 0);
    if ($clpropri->numrows != 0) {
      if ($j42_tipopro == 't') {

        $db_op = '3';
        $clpropri->sql_record($clpropri->sql_query($j42_matric, "", "propri.*#cgm.z01_nome#a.z01_nome as z01_nomematri", "", "j42_matric = $j42_matric "));
        if ($clpropri->numrows > 1) {
          $db_op02 = '3';
        }
      }
      $db_opcao = 2;
      $numcgm = $j42_numcgm;
      $tipoproprietario = $j42_tipoproprietario;
      $result = $clpropri->sql_record($clpropri->sql_query($j42_matric));

      if ($clpropri->numrows > 1) {
        $outros = true;
      }
    } else {

      $result = $cliptubase->sql_record($cliptubase->sql_query($j42_matric, "z01_nome as z01_nomematri", ""));
      @db_fieldsmemory($result, 0);
      $db_opcao = 1;
    }
  }

  db_fim_transacao();
  if (trim($clpropri->erro_msg) != '') {
    db_msgbox($clpropri->erro_msg);
  }
} catch (\Exception $erro) {

  db_fim_transacao(true);
  if (trim($erro->getMessage()) != '') {
    db_msgbox($erro->getMessage());
  }
  $clpropri->erro(true, false);
  if ($clpropri->erro_campo != "") {
    echo "<script> document.form1." . $clpropri->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
    echo "<script> document.form1." . $clpropri->erro_campo . ".focus();</script>";
  } else {

    $clpropri->erro(true, false);
    $tipoimovel = isset($tipoImovel) ? $tipoImovel : $j01_tipoimovel;

    $redirecionamento = "cad1_proprialt.php?j42_matric=$j42_matric&j01_tipoimovel=$tipoimovel";

    if($mostraApenasFracao){
      $redirecionamento .= "&apenasFracao=true";
    }

    db_redireciona($redirecionamento);
  }
}

?>
<html>

<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
  <style type="text/css">
    td {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
    }

    input {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 12px;
      height: 17px;
      border: 1px solid #999999;
    }

    .cores:nth-child(even) {
      background: #FFF;
    }

    .cores:nth-child(odd) {
      background: #efefef;
    }

    table.form-container tr td {
      font-weight: normal !important;
    }
  </style>
</head>

<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="js_trocacordeselect()">
  <br /><br />
  <form name="form1" method="post" onSubmit="return js_verifica_campos_digitados();" action="">
    <table height="430" align="center" width="790" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left" valign="top" bgcolor="#CCCCCC">
          <center>
            <?php
            require_once(modification("forms/db_frmproprialt.php"));
            ?>
          </center>
        </td>
      </tr>
    </table>
  </form>
</body>

<script>
  parent['0'].recarregaPaginaMatricula();
</script>

</html>

<?php
if (isset($incluir) || isset($atualizar) || isset($excluir)) {
  if (!$sqlerro && $clpropri->erro_status == "0") {
    $clpropri->erro(true, false);
    if ($clpropri->erro_campo != "") {
      echo "<script> document.form1." . $clpropri->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
      echo "<script> document.form1." . $clpropri->erro_campo . ".focus();</script>";
    }
  } else if ($sqlerro) {
    db_msgbox($erroMensagem);
  } else {
    $clpropri->erro(true, false);
    $tipoimovel = isset($tipoImovel) ? $tipoImovel : $j01_tipoimovel;

    $redirecionamento = "cad1_proprialt.php?j42_matric=$j42_matric&j01_tipoimovel=$tipoimovel";

    if($mostraApenasFracao){
      $redirecionamento .= "&apenasFracao=true";
    }

    db_redireciona($redirecionamento);
  }
}
?>