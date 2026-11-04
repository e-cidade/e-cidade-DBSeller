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

use ECidade\Financeiro\Tesouraria\SaldoTesourariaHelper;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_libcontabilidade.php"));
require_once(modification("dbforms/db_funcoes.php"));
parse_str($_SERVER["QUERY_STRING"]);
?>
    <html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <meta http-equiv="Expires" CONTENT="0">
        <script type="text/javascript" src="scripts/scripts.js"></script>
        <link href="estilos.css" rel="stylesheet" type="text/css">

        <style>
            .tabela-consulta {
                border-collapse: collapse;
                width: 100%;
                white-space: normal;
                border: 1px solid;
            }

            .tabela-consulta > thead > tr > th {
                background-color: #1e60b0;
                color: #F7F5F1;
                border: 1px solid;
                padding: 1px 4px;
                white-space: nowrap;
            }

            .tabela-consulta > tbody > tr:nth-child(even) {
                background-color: #b0c4de;
            }

            .tabela-consulta > tbody > tr:nth-child(odd) {
                background-color: #bdd1bf;
            }

            .tabela-consulta > tbody > tr > td {
                border: 1px solid;
                white-space: normal;
                overflow: hidden;
            }
        </style>
    </head>
    <body>
    <div class="container">

        <?php
        $cor = "";

        $exercicio = db_getsession("DB_anousu");
        $instituicao = db_getsession("DB_instit");
        $dataSessao = date('Y-m-d', db_getsession("DB_datausu"));
        $dataFiltroSaldo = $dataSessao;

        if (!empty($_GET['datai_dia'])) {
            $dataFiltroSaldo = sprintf('%s-%s-%s', $_GET['datai_ano'], $_GET['datai_mes'], $_GET['datai_dia']);
        }
        switch ($tipo) {
            case 'conta':
                $totais = imprimeTabelaConta($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo);
                break;
            case 'recurso':
                $totais = imprimeTabelaRecurso($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo);
                break;
            case 'recurso_conta':
                $totais = imprimeTabelaRecursoConta($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo);
                break;
            case 'instituicao':
                $totais = imprimeTabelaBancos($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo);
                break;
            case 'domicilio_bancario':
                $totais = imprimeTabelaDomicilioBancario($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo);
                break;
        }

        // imprime totais
        $totval1 = number_format($totais->saldo_anterior, 2, ",", ".");
        $totval2 = number_format($totais->debitado, 2, ",", ".");
        $totval3 = number_format($totais->creditado, 2, ",", ".");
        $totval4 = number_format($totais->saldo_atual, 2, ",", ".");

        ?>
    </div>
    </body>
    <?php
    echo "<script>
 parent.document.form1.tot_ant.value = '$totval1';
 parent.document.form1.tot_deb.value = '$totval2';
 parent.document.form1.tot_cred.value ='$totval3';
 parent.document.form1.tot_atual.value = '$totval4';
</script>
";
    ?>
    </html>

<?php
function imprimeTabelaConta($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo)
{
    $helper = new SaldoTesourariaHelper($instituicao, $exercicio, $dataSessao, $dataFiltroSaldo);
    $porConta = $helper->totalizaPorConta('k13_descr');
    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];

    echo "<table class='tabela-consulta'>\n";
    echo "<thead>";
    echo "<tr >\n";
    echo "<th style='width:80px'>Código</th>\n";
    echo "<th style='width:400px'>Descrição</th>\n";
    echo "<th style='width:100px'>Fonte Recurso</th>\n";
    echo "<th style='width:100px'>Subrecurso</th>\n";
    echo "<th style='width:100px'>Complemento</th>\n";
    echo "<th style='width:120px'>Saldo Ant.</th>\n";
    echo "<th style='width:120px'>Vlr. Debit.</th>\n";
    echo "<th style='width:120px'>Vlr. Cred.</th>\n";
    echo "<th style='width:120px'>Saldo Atual</th>\n";
    echo "</tr>\n";
    echo "</thead>";
    echo "<tbody>";

    foreach ($porConta as $conta) {
        echo "<tr>";
        echo "<td class='text-center' title='{$conta->c60_estrut}'>{$conta->k13_conta}</td>\n";
        echo "<td> {$conta->k13_descr} </td>\n";
        echo "<td class='text-center' title='$conta->descricao'>{$conta->gestao}</td>\n";
        echo "<td class='text-center' title='$conta->descricao'>{$conta->o15_recurso}</td>\n";
        echo "<td class='text-center' >{$conta->o15_complemento}</td>\n";
        if ($conta->tipo == "2") {
            echo "<td  colspan=\"4\">Nada no Corrente</td>\n";
        } elseif ($conta->tipo == "3") {
            echo "<td  colspan=\"4\">Não encontrado no cfautent</td>\n";
        } else {
            echo "<td class='text-right'>" . db_formatar($conta->saldo_anterior, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->debitado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->creditado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->saldo_atual, 'f') . "</td>\n";
            $totais->saldo_anterior += $conta->saldo_anterior;
            $totais->debitado += $conta->debitado;
            $totais->creditado += $conta->creditado;
            $totais->saldo_atual += $conta->saldo_atual;
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    return $totais;
}

function imprimeTabelaRecurso($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo)
{
    $helper = new SaldoTesourariaHelper($instituicao, $exercicio, $dataSessao, $dataFiltroSaldo);
    $porRecurso = $helper->totalizaContasPorRecurso();

    echo "<table class='tabela-consulta'>";
    echo "  <thead>";
    echo "    <tr >";
    echo "      <th style='width:100px'>Fonte Recurso</th>";
    echo "      <th style='width:100px'>Subrecurso</th>";
    echo "      <th style='width:100px'>Complemento</th>";
    echo "      <th style='width: 400px !important; '>Descricao</th>";
    echo "      <th style='width:120px'>Saldo Ant.</th>";
    echo "      <th style='width:120px'>Vlr. Debit.</th>";
    echo "      <th style='width:120px'>Vlr. Cred.</th>";
    echo "      <th style='width:120px'>Saldo Atual</th>";
    echo "    </tr>";
    echo "  </thead>";

    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];

    echo "<tbody>";
    foreach ($porRecurso as $dado) {
        echo "<tr>";
        echo "  <td class='text-center'>$dado->gestao</td>";
        echo "  <td class='text-center'>$dado->o15_recurso</td>";
        echo "  <td class='text-center'>$dado->o15_complemento</td>";
        echo "  <td >$dado->descricao</td>";
        echo "  <td class='text-right'>" . db_formatar($dado->saldo_anterior, 'f') . "</td>";
        echo "  <td class='text-right'>" . db_formatar($dado->debitado, 'f') . "</td>";
        echo "  <td class='text-right'>" . db_formatar($dado->creditado, 'f') . "</td>";
        echo "  <td class='text-right'>" . db_formatar($dado->saldo_atual, 'f') . "</td>";
        echo "</tr>";
        $totais->saldo_anterior += $dado->saldo_anterior;
        $totais->debitado += $dado->debitado;
        $totais->creditado += $dado->creditado;
        $totais->saldo_atual += $dado->saldo_atual;
    }
    echo "</tbody>";
    echo "</table>";

    return $totais;
}

function imprimeTabelaRecursoConta($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo)
{
    $helper = new SaldoTesourariaHelper($instituicao, $exercicio, $dataSessao, $dataFiltroSaldo);
    $porRecurso = $helper->totalizaContasPorRecurso();

    echo "<table class='tabela-consulta'>\n";
    echo "  <thead>";
    echo "    <tr >\n";
    echo "      <th class='field-size3'>Fonte Recurso</th>\n";
    echo "      <th class='field-size2'>Subrecurso</th>\n";
    echo "      <th class='field-size2'>Complemento</th>\n";
    echo "      <th class='field-size8'>Descricao</th>\n";
    echo "      <th class='field-size3'>Saldo Ant.</th>\n";
    echo "      <th class='field-size3'>Vlr. Debit.</th>\n";
    echo "      <th class='field-size3'>Vlr. Cred.</th>\n";
    echo "      <th class='field-size3'>Saldo Atual</th>\n";
    echo "    </tr>\n";
    echo "  </thead>";

    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];
    echo "<tbody>";
    foreach ($porRecurso as $recurso) {
        echo "<tr class='bold'>";
        echo "<td class='text-center'>$recurso->gestao</td>";
        echo "<td class='text-center'>$recurso->o15_recurso</td>";
        echo "<td class='text-center'>$recurso->o15_complemento</td>";
        echo "<td>$recurso->descricao</td>";

        echo "<td class='text-right'>" . db_formatar($recurso->saldo_anterior, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($recurso->debitado, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($recurso->creditado, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($recurso->saldo_atual, 'f') . "</td>\n";
        echo "</tr>";

        foreach ($recurso->contas as $conta) {
            echo "<tr>";
            echo "<td colspan='3'></td>";
            echo "<td title='{$conta->c60_estrut}'>($conta->k13_conta) - $conta->k13_descr</td>";
            echo "<td class='text-right'>" . db_formatar($conta->saldo_anterior, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->debitado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->creditado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->saldo_atual, 'f') . "</td>\n";
            echo "</tr>";
        }

        $totais->saldo_anterior += $recurso->saldo_anterior;
        $totais->debitado += $recurso->debitado;
        $totais->creditado += $recurso->creditado;
        $totais->saldo_atual += $recurso->saldo_atual;
    }
    echo "</tbody>";
    echo "</table>";
    return $totais;
}

function imprimeTabelaBancos($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo)
{
    $helper = new SaldoTesourariaHelper($instituicao, $exercicio, $dataSessao, $dataFiltroSaldo);
    $porBanco = $helper->totalizaPorBanco();
    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];
    echo "<table class='tabela-consulta'>\n";
    echo "  <thead>";
    echo "    <tr >\n";
    echo "      <th class='field-size3'>Banco</th>\n";
    echo "      <th class='field-size8'>Descricao</th>\n";
    echo "      <th class='field-size3'>Saldo Ant.</th>\n";
    echo "      <th class='field-size3'>Vlr. Debit.</th>\n";
    echo "      <th class='field-size3'>Vlr. Cred.</th>\n";
    echo "      <th class='field-size3'>Saldo Atual</th>\n";
    echo "    </tr>\n";
    echo "  </thead>";
    echo "  <tbody>";
    foreach ($porBanco as $banco) {
        echo "<tr class='bold'>";
        echo "<td class='text-center'>{$banco->db90_codban}</td>";
        echo "<td>$banco->db90_descr</td>";
        echo "<td class='text-right'>" . db_formatar($banco->saldo_anterior, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($banco->debitado, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($banco->creditado, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($banco->saldo_atual, 'f') . "</td>\n";
        echo "</tr>";

        foreach ($banco->contas as $conta) {
            echo "<tr>";
            echo "<td></td>";
            echo "<td>($conta->k13_conta) - $conta->k13_descr</td>";
            echo "<td class='text-right'>" . db_formatar($conta->saldo_anterior, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->debitado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->creditado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->saldo_atual, 'f') . "</td>\n";
            echo "</tr>";
        }
        $totais->saldo_anterior += $banco->saldo_anterior;
        $totais->debitado += $banco->debitado;
        $totais->creditado += $banco->creditado;
        $totais->saldo_atual += $banco->saldo_atual;
    }
    echo "</tbody>";
    echo "</table>";

    return $totais;
}

function imprimeTabelaDomicilioBancario($exercicio, $instituicao, $dataSessao, $dataFiltroSaldo)
{
    $helper = new SaldoTesourariaHelper($instituicao, $exercicio, $dataSessao, $dataFiltroSaldo);
    $porDomicilio = $helper->totalizaPorDomiciolioBancario();
    $totais = (object)[
        "saldo_anterior" => 0,
        "debitado" => 0,
        "creditado" => 0,
        "saldo_atual" => 0,
    ];
    echo "<table class='tabela-consulta'>\n";
    echo "  <thead>";
    echo "    <tr >\n";
    echo "      <th class='field-size3'>Domicilio Bancário</th>\n";
    echo "      <th class='field-size8'>Descricao</th>\n";
    echo "      <th class='field-size3'>Saldo Ant.</th>\n";
    echo "      <th class='field-size3'>Vlr. Debit.</th>\n";
    echo "      <th class='field-size3'>Vlr. Cred.</th>\n";
    echo "      <th class='field-size3'>Saldo Atual</th>\n";
    echo "    </tr>\n";
    echo "  </thead>";
    echo "  <tbody>";
    foreach ($porDomicilio as $domicilio) {
        echo "<tr class='bold'>";
        echo "<td class='text-center'>{$domicilio->bancoagencia}</td>";
        echo "<td></td>";
        echo "<td class='text-right'>" . db_formatar($domicilio->saldo_anterior, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($domicilio->debitado, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($domicilio->creditado, 'f') . "</td>\n";
        echo "<td class='text-right'>" . db_formatar($domicilio->saldo_atual, 'f') . "</td>\n";
        echo "</tr>";

        foreach ($domicilio->contas as $conta) {
            echo "<tr>";
            echo "<td></td>";
            echo "<td>($conta->k13_conta) - $conta->k13_descr - $conta->gestao - $conta->o15_recurso - $conta->o15_complemento</td>";
            echo "<td class='text-right'>" . db_formatar($conta->saldo_anterior, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->debitado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->creditado, 'f') . "</td>\n";
            echo "<td class='text-right'>" . db_formatar($conta->saldo_atual, 'f') . "</td>\n";
            echo "</tr>";
        }
        $totais->saldo_anterior += $domicilio->saldo_anterior;
        $totais->debitado += $domicilio->debitado;
        $totais->creditado += $domicilio->creditado;
        $totais->saldo_atual += $domicilio->saldo_atual;
    }
    echo "</tbody>";
    echo "</table>";

    return $totais;
}
