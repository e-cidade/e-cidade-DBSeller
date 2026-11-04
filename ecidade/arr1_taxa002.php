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
use  \ECidade\Tributario\Arrecadacao\Repository\Taxa as TaxaRepository;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_taxa_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("libs/db_app.utils.php"));
parse_str($_SERVER["QUERY_STRING"]);
$oGet       = db_utils::postMemory($_GET);
$oPost      = db_utils::postMemory($_POST);
$cltaxa   = new cl_taxa;
$db_opcao = 22;
$db_botao = false;
$lErro    = false;

if (isset($alterar)) {
    try {
        db_inicio_transacao();

        $oTaxa = new Taxa();
        $oTaxa->setGrupoTaxas($ar36_grupotaxa);
        $oTaxa->setTaxas($ar36_sequencial);
        $oTaxa->setReceita($ar36_receita);
        $oTaxa->setDescricao($ar36_descricao);
        $oTaxa->setPercentual($ar36_perc);
        $oTaxa->setValor($ar36_valor);
        $oTaxa->setValorMinimo($ar36_valormin);
        $oTaxa->setValorMaximo($ar36_valormax);
        $oTaxa->aplicaJuroMulta($ar36_aplicajurosmulta == 't');
        $oTaxa->aplicaHonorario($ar36_honorario == 't');
        $oTaxa->setPermissaoParcelamento($ar36_permiteparcelamento  == 't');

        if (isset($ar36_debitoscomprocesso)) {
            $oTaxa->setDebitosComProcesso($ar36_debitoscomprocesso);
        }

        if (isset($ar36_debitossemprocesso)) {
            $oTaxa->setDebitosSemProcesso($ar36_debitossemprocesso);
        }

        $oTaxaRepository = TaxaRepository::getInstance();

        $valida = $oTaxaRepository->getValidaReceita($ar36_receita, $ar36_sequencial);

        if (!$valida) {
            db_msgbox("Já existe taxa cadastrada com essa receita.");
        } else {
            $db_opcao = 2;
            $lTaxaSalavar = $oTaxaRepository->persist($oTaxa);

            if (!$lTaxaSalavar) {
                throw new Exception("Erro ao cadastrar.");
            }

            db_msgbox("Taxa/Custa alterada com Sucesso.");
        }

        db_fim_transacao();
    } catch (Exception $e) {

        $lErro = true;
        db_msgbox("Erro ao alterar Taxa / Tarifa:" . $e->getMessage());
        db_fim_transacao(true);
    }
} else if(isset($chavepesquisa)) {

    $db_opcao = 2;
    $result   = $cltaxa->sql_record($cltaxa->sql_query($chavepesquisa));
    db_fieldsmemory($result,0);
    $db_botao = true;
}
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <script language="JavaScript" type="text/javascript"src="scripts/strings.js"></script>
  <script language="JavaScript" type="text/javascript"src="scripts/prototype.js"></script>
  <script language="JavaScript" type="text/javascript"src="scripts/numbers.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class='body-default' >
<div class ='container'>
  <?php
	  include(modification("forms/db_frmtaxa.php"));
	?>
</div>
</body>
</html>
<?php
if (isset($alterar)) {

  if ($lErro) {

    $cltaxa->erro(true,false);
    $db_botao=true;
    echo "<script> document.form1.db_opcao.disabled=false;</script>";

  }else{
    if($valida){
        db_redireciona("arr1_taxa002.php?liberaaba=true&chavepesquisa=".$ar36_sequencial);
    }
  }
}
if( ($db_opcao == 22) ){

	 //echo $chavepesquisa; die();
  echo "<script>document.form1.pesquisar.click(); </script>";
}

if (isset($chavepesquisa)) {

  if ($ar36_perc > 0) {

    echo "<script>
             js_tipoCobranca(2);
             $('tipo_cobranca').options.length = 0;
             $('tipo_cobranca').options[0]     = new Option('Percentual de Débito', '2');
             $('tipo_cobranca').options[1]     = new Option('Valor Fixado', '1');
          </script>";

  } else {

    echo "<script>
             js_tipoCobranca(1);
             $('tipo_cobranca').options.length = 0;
             $('tipo_cobranca').options[0]     = new Option('Valor Fixado', '1');
             $('tipo_cobranca').options[1]     = new Option('Percentual de Débito', '2');
          </script>";
  }

 echo "
  <script>
      function js_db_libera(){
         parent.document.formaba.favorecido.disabled = false;
         (window.CurrentWindow || parent.CurrentWindow).corpo.iframe_favorecido.location.href='arr1_taxaFavorecido001.php?ar36_sequencial=".@$chavepesquisa."';
     ";
         if(isset($liberaaba)){
           echo "  parent.mo_camada('favorecido');";
         }
 echo"}\n
    js_db_libera();
  </script>\n
 ";

}


?>
<script>
js_tabulacaoforms("form1","ar36_grupotaxa",true,1,"ar36_grupotaxa",true);
</script>
