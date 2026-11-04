<?php
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once modification("libs/db_usuariosonline.php");
require_once modification("libs/db_sql.php");
require_once modification("libs/db_utils.php");


$listaRecursos = $_GET['recursos'];
$anousu = db_getsession('DB_anousu');

$sqlDotacao = "
select fc_estruturaldotacao(o58_anousu, o58_coddot) as estrutural,
       o58_coddot as reduzido,
       o15_recurso,
       codigo_siconfi as siconfi,
       gestao,
       o15_complemento
  from orcdotacao
  join orctiporec on o15_codigo = o58_codigo
  join fonterecurso on orctiporec_id = o58_codigo
       and exercicio = o58_anousu
 where o58_codigo in ({$listaRecursos})
   and o58_anousu = {$anousu}
 order by 1
";

$rsDotacao = db_query($sqlDotacao);

$sqlReceita = "
select o57_fonte as estrutural, o70_codrec as reduzido,
       o15_recurso,
       codigo_siconfi as siconfi,
       gestao,
       o15_complemento
from orcreceita
  join orcfontes on o57_codfon = o70_codfon and o57_anousu = o70_anousu
  join orctiporec on o15_codigo = o70_codigo
  join fonterecurso on orctiporec_id = o70_codigo
       and exercicio = o70_anousu
where o70_anousu = {$anousu}
  and o70_codigo in ({$listaRecursos})
order by 1
";

$rsReceita = db_query($sqlReceita);
$dotacoes = db_utils::getCollectionByRecord($rsDotacao);
$receitas = db_utils::getCollectionByRecord($rsReceita);

if (count($dotacoes) === 0 && count($receitas) === 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Não Dotações ou Receitas para os recursos selecionados.');
    exit;
}

$pdf = new \ECidade\Pdf\Pdf();
$pdf->init(false);
$pdf->setAutoPageBreak(false, 15);
$pdf->AliasNbPages();
$pdf->addTitulo('Orçamento previsto com recursos selecionados');
$pdf->addTitulo('Exercício: ' . $anousu);

function cabecalho(\ECidade\Pdf\Pdf $pdf, $label)
{
    $pdf->addPage();
    $pdf->setFont('arial', 'b', 8);
    $pdf->cell(192, 5, $label, 0, 1);
    $pdf->cell(20, 5, "Reduzido", 0, 0);
    $pdf->cell(110, 5, "Estrutural", 0, 0);
    $pdf->cell(20, 5, "Recurso", 0, 0);
    $pdf->cell(20, 5, "Gestão", 0, 0);
    $pdf->cell(20, 5, "Siconfi", 0, 1);
    $pdf->setFont('arial', '', 8);
}

if (count($dotacoes) !== 0) {
    imprimeDados($pdf, $dotacoes, 'DOTAÇÔES');
}

if (count($receitas) !== 0) {
    imprimeDados($pdf, $receitas, 'RECEITAS');
}

$pdf->output('I');
/**
 * @param \ECidade\Pdf\Pdf $pdf
 * @param $dados
 * @param $label
 * @return void
 */
function imprimeDados(\ECidade\Pdf\Pdf $pdf, $dados, $label)
{
    cabecalho($pdf, $label);
    $pinta = false;
    foreach ($dados as $dado) {
        if ($pdf->gety() > $pdf->getH() - 15) {
            cabecalho($pdf, 'DOTAÇÔES');
        }

        $pdf->cell(20, 5, $dado->reduzido, 0, 0, 'L', $pinta);
        $pdf->cell(110, 5, $dado->estrutural, 0, 0, 'L', $pinta);
        $pdf->cell(20, 5, "$dado->o15_recurso - $dado->o15_complemento", 0, 0, 'L', $pinta);
        $pdf->cell(20, 5, "$dado->gestao - $dado->o15_complemento", 0, 0, 'L', $pinta);
        $pdf->cell(20, 5, "$dado->siconfi - $dado->o15_complemento", 0, 1, 'L', $pinta);
        $pinta = !$pinta;
    }
}
