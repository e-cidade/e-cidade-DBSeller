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

require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once(modification("libs/db_libcontabilidade.php"));

parse_str($_SERVER['QUERY_STRING']);
$anousu = db_getsession("DB_anousu");

$instit = str_replace("-", ",", $instit);
function php_espaco($nivel)
{
    $espaco = "";
    switch ($nivel) {
        case 1:
            $espaco = "";
            break;
        case 2:
            $espaco = " ";
            break;
        case 3:
            $espaco = "    ";
            break;
        case 4:
            $espaco = "       ";
            break;
        case 5:
            $espaco = "           ";
            break;
        case 6:
            $espaco = "              ";
            break;
        case 7:
            $espaco = "                  ";
            break;
        case 8:
            $espaco = "                      ";
            break;
    }
    return $espaco;
}

$where = " c60_anousu = $anousu and (c61_instit in ($instit) or c61_instit is null  )";
if (isset($estrutural) && $estrutural != "") {
    $where .= " and c60_estrut like '" . $estrutural . "%'";
}
$where .= " and substr(c60_estrut, 1, 1) in ('4', '3', '9') ";
$exercicio = $anousu < 2022 ? "2021" : $anousu;


$sql = "
    SELECT c60_estrut,
           c61_reduz,
           c60_descr,
           c51_descr,
           c52_descrred,
           gestao,
           o15_complemento,
           o15_descr,
           c61_instit,
           nomeinstabrev
    FROM contabilidade.conplanoorcamento
    JOIN contabilidade.consistema ON conplanoorcamento.c60_codsis = consistema.c52_codsis
    JOIN contabilidade.conclass ON conplanoorcamento.c60_codcla = conclass.c51_codcla
    LEFT JOIN contabilidade.conplanoorcamentoanalitica
              ON conplanoorcamento.c60_codcon = conplanoorcamentoanalitica.c61_codcon
              AND conplanoorcamento.c60_anousu = conplanoorcamentoanalitica.c61_anousu
    LEFT JOIN orcamento.orctiporec ON conplanoorcamentoanalitica.c61_codigo = orctiporec.o15_codigo
    LEFT JOIN configuracoes.db_config ON db_config.codigo = conplanoorcamentoanalitica.c61_instit
    LEFT JOIN orcamento.fonterecurso ON fonterecurso.orctiporec_id = orctiporec.o15_codigo
              AND fonterecurso.exercicio = c60_anousu
    where $where order by c60_estrut, c61_instit
 ";

$result = db_query($sql);

if (pg_num_rows($result) == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Plano de Contas não Cadastrado. Exercício: " . db_getsession("DB_anousu"));
}

$pdf = new \ECidade\Pdf\Pdf('L');
$pdf->init(false);
$pdf->addTitulo('PLANO DE CONTAS ORCAMENTÁRIO');
$pdf->addTitulo('');
$pdf->addTitulo("EXERCICIO: " . db_getsession("DB_anousu"));

$pdf->AliasNbPages();
$pdf->setfillcolor(245);

$alt = 4;

/**
 * @param \ECidade\Pdf\Pdf $pdf
 * @param $alt
 * @param $RLo15_descr
 * @return void
 */
function cabecalho(\ECidade\Pdf\Pdf $pdf, $alt)
{
    $pdf->addPage('L');
    $pdf->setFont('arial', 'b', 7);

    $pdf->cell(160, $alt, "PLANO DE CONTA", 1, 0, "C", 1);
    $pdf->cell(120, $alt, "REDUZIDO", 1, 1, "C", 1);

    $pdf->cell(25, $alt, "Estrutural", 1, 0, "C", 1);
    $pdf->cell(10, $alt, "Reduz", 1, 0, "C", 1);
    $pdf->cell(111, $alt, "Descrição", 1, 0, "C", 1);
    $pdf->cell(7, $alt, "Clas", 1, 0, "C", 1);
    $pdf->cell(7, $alt, "Sist", 1, 0, "C", 1);
    $pdf->cell(60, $alt, "Recurso", 1, 0, "C", 1);
    $pdf->cell(60, $alt, "Instituição", 1, 1, "C", 1);
    $pdf->Ln(2);
}

cabecalho($pdf, $alt);

while ($dado = pg_fetch_object($result)) {
    if ($pdf->gety() > $pdf->getH() - 15) {
        cabecalho($pdf, $alt);
    }
    $cfundo = "0";
    $pdf->setFont('arial', 'b', 6);
    if ($dado->c61_reduz != "") {
        $cfundo = "1";
        $pdf->setFont('arial', '', 6);
    }


    $nivel = db_le_mae_conplano($dado->c60_estrut, true);
    $espaco = php_espaco($nivel);
    $pdf->cell(25, $alt, $dado->c60_estrut, 0, 0, "L", $cfundo);
    $pdf->cell(10, $alt, $dado->c61_reduz, 0, 0, "C", $cfundo);
    $pdf->cell(111, $alt, "$espaco $dado->c60_descr", 0, 0, "L", $cfundo);
    $pdf->cell(7, $alt, substr($dado->c51_descr, 0, 1), 0, 0, "C", $cfundo);
    $pdf->cell(7, $alt, "{$dado->c52_descrred}", 0, 0, "C", $cfundo);
    $recurso = "{$dado->gestao} - {$dado->o15_complemento} - {$dado->o15_descr}";
    $pdf->cellAdapt(6, 60, $alt, $recurso, 0, 0, "L", $cfundo);
    $pdf->cell(60, $alt, "{$dado->c61_instit} - {$dado->nomeinstabrev}", 0, 1, "L", $cfundo);
}
$pdf->Output('I');
