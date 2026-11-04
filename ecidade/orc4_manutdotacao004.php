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

use ECidade\Financeiro\Orcamento\Service\AcompanhamentoDesembolsoDespesaService;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_orcdotacao_classe.php"));
require_once(modification("classes/db_orcdotacaocontr_classe.php"));
require_once(modification("classes/db_orcelemento_classe.php"));
require_once(modification("classes/db_orcparametro_classe.php"));
require_once(modification("classes/db_orcorgao_classe.php"));
require_once(modification("classes/db_orcunidade_classe.php"));
require_once(modification("classes/db_orcfuncao_classe.php"));
require_once(modification("classes/db_orcsubfuncao_classe.php"));
require_once(modification("classes/db_orcprograma_classe.php"));
require_once(modification("classes/db_orcprojativ_classe.php"));
require_once(modification("classes/db_orctiporec_classe.php"));
require_once(modification("libs/db_liborcamento.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);

$labelBotao = 'Incluir';

$clorcdotacao = new cl_orcdotacao;
$clorcdotacaocontr = new cl_orcdotacaocontr;
$clorcelemento = new cl_orcelemento;
$clorcparametro = new cl_orcparametro;
$clorcorgao = new cl_orcorgao;
$clorcunidade = new cl_orcunidade;
$clorcfuncao = new cl_orcfuncao;
$clorcsubfuncao = new cl_orcsubfuncao;
$clorcprograma = new cl_orcprograma;
$clorcprojativ = new cl_orcprojativ;
$clorctiporec = new cl_orctiporec;
$db_opcao = 1;
$db_botao = true;
$anousu = db_getsession("DB_anousu");

if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Incluir") {
    try {
        if ($o58_orgao == "") {
            throw new Exception("Orgão não informado.");
        }
        if ($o58_unidade == "") {
            throw new Exception("Unidade não informado.");
        }
        if ($o58_funcao == "") {
            throw new Exception("Função não informado.");
        }
        if ($o58_subfuncao == "") {
            throw new Exception("Sub-Função não informado.");
        }
        if ($o58_programa == "") {
            throw new Exception("Programa não informado.");
        }
        if ($o58_projativ == "") {
            throw new Exception("Projeto/Atividade não informado.");
        }
        if ($o56_elemento == "") {
            throw new Exception("Elemento não informado.");
        }
        if ($o58_codigo == "") {
            throw new Exception("Recurso não informado.");
        }
        if ($o58_concarpeculiar == "") {
            throw new Exception("Você deve selecionar uma C.Peculiar/Cod. de Aplicação antes de incluir a Dotação.");
        }
        if ($o58_esferaorcamentaria === "0") {
            throw new Exception("Você deve selecionar uma Esfera Orçamentária antes de incluir a Dotação.");
        }
        if (!FONTE_RECURSO_UNIAO) {
            $o58_esferaorcamentaria = "0";
        }

        db_inicio_transacao();
        $result = $clorcdotacao->sql_record(
            $clorcdotacao->sql_query(
                null,
                null,
                "*",
                "",
                "o58_anousu = {$anousu}
             and o58_orgao = $o58_orgao
             and o58_unidade = $o58_unidade
             and o58_funcao = $o58_funcao
             and o58_subfuncao = $o58_subfuncao
             and o58_programa = $o58_programa
             and o58_projativ = $o58_projativ
             and orcelemento.o56_elemento = '$o56_elemento'
             and o58_codele = orcelemento.o56_codele
             and orcelemento.o56_anousu = o58_anousu
             and o58_codigo = $o58_codigo
             and o58_instit = $o58_instit
             and o58_concarpeculiar = '{$o58_concarpeculiar}'
             and o58_esferaorcamentaria = {$o58_esferaorcamentaria} "
            )
        );

        if ($clorcdotacao->numrows > 0) {
            throw new Exception("Dotação já Cadastrada.");
        }

        $resultPar = $clorcparametro->sql_record($clorcparametro->sql_query_file($anousu, "o50_subelem"));

        db_fieldsmemory($resultPar, 0);

        if ($o50_subelem == 'f') {
            $o56_elemento = substr($o56_elemento, 0, 7) . "000000";
            $sSql = $clorcelemento->sql_query_file(
                null,
                null,
                'o56_codele',
                'o56_elemento',
                " o56_anousu = " . $anousu . " and o56_elemento = '$o56_elemento' "
            );
        } else {
            $sSql = $clorcelemento->sql_query_file(
                null,
                null,
                'o56_codele',
                '',
                " o56_anousu = {$anousu} and  o56_elemento = '{$o56_elemento}' "
            );
        }
        $result = $clorcelemento->sql_record($sSql);
        if ($clorcelemento->numrows > 0) {
            db_fieldsmemory($result, 0);
            $clorcdotacao->o58_codele = $o56_codele;

            $sqlUpdate = "update orcparametro set o50_coddot = o50_coddot + 1
                           where o50_anousu = {$anousu}";
            $result = $clorcparametro->sql_record($sqlUpdate);
            $result = $clorcparametro->sql_record($clorcparametro->sql_query_file(
                db_getsession('DB_anousu'),
                'o50_coddot as o58_coddot'
            ));
            if ($clorcparametro->numrows > 0) {
                db_fieldsmemory($result, 0);

                $clorcdotacao->o58_localizadorgastos = $o58_localizadorgastos;
                $clorcdotacao->o58_programa = $o58_programa;
                $clorcdotacao->incluir($o58_anousu, $o58_coddot);
                $o58_coddot = $clorcdotacao->o58_coddot;
                if ($clorcdotacao->erro_status == 0) {
                    throw new Exception("$clorcdotacao->erro_msg");
                }

                $dotacao = new \ECidade\Financeiro\Orcamento\Model\Dotacao();
                $dotacao->setCodigoDotacao($clorcdotacao->o58_coddot)
                    ->setAno($clorcdotacao->o58_anousu)
                    ->setValor($clorcdotacao->o58_valor);

                $acompanhamento = new AcompanhamentoDesembolsoDespesaService();
                $acompanhamento->criar($dotacao);
            } else {
                throw new Exception("Erro no código Sequencial.");
            }
        } else {
            throw new Exception("Elemento  ($o56_elemento) não Cadastrado.");
        }

        db_fim_transacao();
    } catch (Exception $e) {
        $erro_trans = true;
        $clorcdotacao->erro_msg = $e->getMessage();
        $clorcdotacao->erro_status = 0;
        db_fim_transacao(true);
    }
}
?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body onLoad="document.form1.o50_estrutdespesa.focus();">

    <div class="container">
        <?php
        include(modification("forms/db_frmorcdotacao001.php"));
        ?>
    </div>
</body>

</html>
<?php
if ((isset($_POST["db_opcao"]) && $_POST["db_opcao"]) == "Incluir") {
    if ($clorcdotacao->erro_status == "0") {
        $clorcdotacao->erro(true, false);
        $db_botao = true;
        echo "<script> document.form1.db_opcao.disabled=false;
	                   document.form1.o58_coddot.value = '';
	          </script>  ";
        if ($clorcdotacao->erro_campo != "") {
            echo "<script> document.form1." . $clorcdotacao->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
            echo "<script> document.form1." . $clorcdotacao->erro_campo . ".focus();</script>";
        }
    } else {
        echo "<script>
                alert(\"" . $clorcdotacao->erro_msg . "\");
            </script>  ";
    }
}
?>
