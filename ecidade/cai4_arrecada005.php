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
require_once(modification("dbforms/db_funcoes.php"));

$iInstit = db_getsession("DB_instit");
$anousu = db_getsession("DB_anousu");

$oGet = db_utils::postMemory($_GET);

$lValidaDataCredito = ParametroCaixa::getValidaDataCreditoBaixaBanco($iInstit);

$dataSessao = date("Y-m-d", db_getsession("DB_datausu"));

$sql_conta = "
	select k13_reduz as c01_reduz,
	       k13_descr as c01_descr,
	       c60_estrut as c01_estrut
	from   saltes
	inner join conplanoexe   on k13_reduz  = c62_reduz  and c62_anousu = " . db_getsession("DB_anousu") . "
	inner join conplanoreduz on c61_anousu = c62_anousu and c61_reduz  = c62_reduz and c61_instit = {$iInstit}
	inner join conplano      on c60_anousu = c61_anousu and c60_codcon = c61_codcon ";

/* [Extensão] Filtro da Despesa */

$result_conta = db_query($sql_conta);
if (pg_num_rows($result_conta) == 0) {
    echo "<script>parent.alert('Sem Contas Cadastradas.');</script>";
    exit;
}

$sContasTesouraria = "";
/* [Extensão] Filtro da Despesa - Classificacao */


$sqlDataCredito = "
    select distinct (
        select distinct dtcredito
         from disbanco
        where codret = discla.codret
          and instit = discla.instit
          and dtcredito is not null
          limit 1
      ) as data_cred
     from discla
    inner join disarq on disarq.codret = discla.codret
    inner join saltes on disarq.k00_conta = saltes.k13_conta
    where dtaute is null
    and discla.instit =  {$iInstit}
    and extract(year from dtarquivo)  = {$anousu}
    {$sContasTesouraria}
    order by 1 ;
";

$rsDtCredito = db_query($sqlDataCredito);
$aDtCredito = ["" => "Mostrar todas as datas"];
$dataCredito = $dataSessao;

if (pg_num_rows($rsDtCredito) > 0) {
    $dataCredito = db_utils::fieldsMemory($rsDtCredito, 0)->data_cred;

    for ($i = 0; $i < pg_num_rows($rsDtCredito); $i++) {
        $dados = db_utils::fieldsMemory($rsDtCredito, $i);

        if ($dados->data_cred == "") {
            continue;
        }
        $aDtCredito[$dados->data_cred] =  DBDate::converter($dados->data_cred);
    }
}

$whereDataCredito = null;
if (isset($oGet->data_credito) && !empty($oGet->data_credito)) {
    $dataCredito = $oGet->data_credito;
    $dt_credito = $oGet->data_credito;
    $whereDataCredito = "where data_cred = '{$dataCredito}'";
}

$sSqlBuscaClassificacao = "
  select *
    from (  
        select codcla,
               k15_codbco,
               k15_codage,
               k00_conta,
               k13_descr,
               ( select distinct dtcredito
                   from disbanco
                  where codret = discla.codret
                    and instit = discla.instit
                    and dtcredito is not null
                  limit 1 ) as data_cred
          from discla 
               inner join disarq on disarq.codret = discla.codret 
               inner join saltes on disarq.k00_conta = saltes.k13_conta 
         where dtaute is null 
           and discla.instit = {$iInstit} 
           {$sContasTesouraria} 
    ) as classificacoes
    {$whereDataCredito}
    order by codcla";

$result = db_query($sSqlBuscaClassificacao);
if (pg_num_rows($result) == 0) {
    echo "
    <script>
        parent.alert('Não Existe Classificacao para ser Autenticada.');
        parent.js_removeObj('msgBox');
        document.location.href = 'cai4_arrecada005.php';
    </script>
    ";
    exit;
}

?>
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<script>

var lValidaDataCredito = '<?php echo $lValidaDataCredito == true ? "true" : "false"; ?>';

function js_recriaDataCredito(data_credito){

    parent.js_divCarregando("Aguarde, buscando classificações...", "msgBox");
    document.location.href = 'cai4_arrecada005.php?data_credito=' + data_credito;
    parent.recibos.document.location.href = 'cai4_arrecada006.php';
}

function js_autenticar() {

  var dataCredito = document.form1.dt_credito.value;
  var dataSessao = '<?php echo $dataSessao ?>';

  if ( (dataCredito != dataSessao) && (lValidaDataCredito == 'true') ) {

    alert("Antes de realizar a operação confirme a data em que deseja incluir o movimento");
    return;
  }

  parent.js_divCarregando("Aguarde, executando baixa de banco...", "msgBox");
  document.form1.action = 'cai4_arrecada007.php?system=linux';
  document.form1.submit();
}

function js_calculacodcla(codcla){

  parent.js_divCarregando("Aguarde, buscando receitas...", "msgBox");
  parent.recibos.document.location.href = 'cai4_arrecada006.php?codcla='+codcla;
}

function js_baixacaixa(){
   document.location.href = 'cai4_arrecada002.php';
   parent.recibos.document.location.href = 'cai4_arrecada004.php';
}
</script>

<body>
<form name="form1" method="post" >
  <table style="margin-top: 20px; width:99%">
    <tr>
      <td align="right" valign="middle"><strong>Data de Crédito:</strong> </td>
      <td align="left"  valign="middle">
        <?php db_select('dt_credito', $aDtCredito, true, 1, "onchange='js_recriaDataCredito(this.value)'");?>
      </td>
      <td align="right" valign="middle">&nbsp;</td>
      <td align="right" valign="middle">&nbsp;</td>
    </tr>
    <tr>
      <td align="right" valign="middle"><b>Classificação:&nbsp;</b> </td>
      <td>
      <select onChange="js_calculacodcla(this.value);" name="codcla">
      <?php
        for ($i=0; $i<pg_num_rows($result); $i++) {
            db_fieldsmemory($result, $i);
            echo '<option value="'.$codcla.'">'.$codcla.'-'.$k15_codbco.'-'.$k15_codage.'-'.$k00_conta.'-'.$k13_descr.'</option>';
        }
        ?>
        </select>
        <input style="margin-left: 20px;;"name="imprimir" type="button" id="imprimir" onClick="js_imprime()" value="Imprimir">
    </td>
      <td  align="right" valign="middle"><input name="autenticar" type="button" id="autenticar4" onClick="js_autenticar()" value="Autenticar"></td>
      <td  align="right" valign="middle"><input name="caixa"  type="button" id="caixa4"  onClick="js_baixacaixa();" value="Baixa Caixa"></td>
    </tr>
  </table>
</form>
</body>
</html>
<script>
js_calculacodcla(document.form1.codcla.value);
function js_imprime(){

  var codcla = document.form1.codcla.value;
  //window.open('cai4_baixabanco009.php?codcla=<?=$codcla?>','','width=790,height=530,scrollbars=1,location=0');
  window.open('cai4_baixabanco009.php?codcla='+codcla,'','width=790,height=530,scrollbars=1,location=0');
}
</script>
