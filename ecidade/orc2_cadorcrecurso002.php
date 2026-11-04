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
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_sql.php");
require_once modification("libs/db_utils.php");

$oGet = db_utils::postMemory($_GET);
$clorctiporec = new cl_orctiporec;
$clorctiporec->rotulo->label();

$exercicioSessao = db_getsession("DB_anousu");
$where = ["exercicio = {$exercicioSessao}"];

$filtroRecurso = "RECURSO: Todos";
$date = date('Y-m-d', db_getsession('DB_datausu'));
if ($oGet->recurso == 'sa') {
    $filtroRecurso = "RECURSO: Somente Ativos";
    $where[] = " (o15_datalimite is null or o15_datalimite > '{$date}')";
}
if ($oGet->recurso == 'si') {
    $filtroRecurso = "RECURSO: Somente Inativos";
    $where[] = " (o15_datalimite is not null or o15_datalimite < '{$date}')";
}

$sql = "
    select codigo_siconfi,
           gestao,
           descricao,
           o15_descr,
           o15_complemento,
           o15_datalimite,
           o15_finali,
           o15_recurso
      from orctiporec
      join fonterecurso on orctiporec_id = o15_codigo
    where %s
    order by o15_recurso, gestao, o15_complemento
";

$sql = sprintf($sql, implode(' and ', $where));

$rs = db_query($sql);

$linhas = pg_num_rows($rs);
if ($linhas == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Não existem recursos cadastradas.');
    exit;
}

$pdf = new \ECidade\Pdf\Pdf("P");
$pdf->init(false);
$pdf->setAutoPageBreak(false, 15);
$pdf->AliasNbPages();
$pdf->addTitulo('RELATÓRIO DE RECURSOS VINCULADOS');
$pdf->addTitulo('');
$pdf->addTitulo("EXERCÍCIO: " . $exercicioSessao);
$pdf->addTitulo($filtroRecurso);


$total = 0;

$pdf->setfont('arial', 'b', 8);
$troca = 1;
$alt = 4;

cabecalho($pdf, $oGet);

$pinta = false;
while ($dado = pg_fetch_object($rs)) {
    if ($pdf->gety() > $pdf->getH() - 15) {
        cabecalho($pdf, $oGet);
    }

    $pdf->setfont('arial', '', 7);

    $pdf->cell(15, 4, $dado->codigo_siconfi, 1, 0, "C", $pinta);
    $pdf->cell(15, 4, $dado->gestao, 1, 0, "C", $pinta);
    $pdf->cell(15, 4, $dado->o15_recurso, 1, 0, "C", $pinta);
    $pdf->cell(20, 4, $dado->o15_complemento, 1, 0, "C", $pinta);
    $pdf->cellAdapt(7, 108, 4, $dado->descricao, 1, 0, "L", $pinta);
    $pdf->cell(20, 4, db_formatar($dado->o15_datalimite, 'd'), 1, 1, "C", $pinta);

    if ($oGet->finalidade === 'S') {
        $pdf->multicell(192, 4, $dado->o15_finali, 1, 'L', $pinta);
    }

    $pinta = !$pinta;
}

$pdf->Output('I');

function cabecalho($pdf, $oGet)
{
    $pdf->addpage();
    $pdf->setfillcolor(215);
    $pdf->setfont('arial', 'b', 8);

    $pdf->cell(15, 4, "Siconfi", 1, 0, "C", 1);
    $pdf->cell(15, 4, "Gestão", 1, 0, "C", 1);
    $pdf->cell(15, 4, "Rec. Ant.", 1, 0, "C", 1);
    $pdf->cell(20, 4, "Complemento", 1, 0, "C", 1);
    $pdf->cell(108, 4, "Descrição", 1, 0, "C", 1);
    $pdf->cell(20, 4, "Validade", 1, 1, "C", 1);

    if ($oGet->finalidade === 'S') {
        $pdf->cell(192, 4, "Finalidade", 1, 1, "C", 1);
    }
    $pdf->setfillcolor(240);
}
