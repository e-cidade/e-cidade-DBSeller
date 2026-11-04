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

use App\Domain\Educacao\CentralMatriculas\Models\Escola;
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

$oGet = db_utils::postMemory($_GET);

$pdf = new Pdf();
$pdf->init(false);
$pdf->exibeHeader(true, \Fpdf\Pdf::HEADER_DEFAULT);
$pdf->addTitulo("Escolas por Bairro", 1);

$filtros = [];
$where = "";
$proximaLinha = 2;

if ($oGet->filtroEscola > 0) {
    $escola = Escola::find($oGet->filtroEscola);
    $pdf->addTitulo("\nEscola: ".$escola->mo53_nome, 2);
    $proximaLinha = 3;
    $filtros[] = "mo53_codigo = {$oGet->filtroEscola}";
}

if ($oGet->filtroBairro > 0) {
    $rsBairro = db_query("select j13_descr from bairro where j13_codi = {$oGet->filtroBairro}");
    $bairro = db_utils::fieldsmemory($rsBairro, 0);
    $pdf->addTitulo("\nBairro: ".$bairro->j13_descr, $proximaLinha);
    $filtros[] = "j13_codi = {$oGet->filtroBairro}";
}

if (count($filtros) > 0) {
    $where = "where ".implode(" and ", $filtros);
}

$pdf->addPage();
$pdf->setFont('arial', '', 7);

$sqlEscolasBairros = "
select mo53_codigo, mo53_nome escola, j13_codi, j13_descr bairro
from bairro
inner join plugins.escbairro on bairro.j13_codi = escbairro.mo08_bairro 
inner join plugins.escolas ON escolas.mo53_codigo = escbairro.mo08_escola
$where
order by mo53_nome, j13_descr";

$result = db_query($sqlEscolasBairros);
$numLinhas = pg_num_rows($result);

if ($numLinhas == 0) {
    db_redireciona('db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.');
}

$firstLine = true;
$ultimaEscola = "";
$contagem = 0;

for ($x=0; $x < $numLinhas; $x++) {
    db_fieldsmemory($result, $x);

    if ($escola != $ultimaEscola) {
        if (!$firstLine) {
            $pdf->ln();
            $contagem = 0;
        } else {
            $firstLine = false;
        }
        imprimeCabecalhoEscola($pdf, $escola);
    }

    $fillColor = getFillColor($contagem);
    imprimeBairro($pdf, $bairro, $fillColor);

    $ultimaEscola = $escola;

    if ($pdf->getY() > ($pdf->getH() - 30)) {
        $pdf->addPage();
    }
    $contagem++;
}

$pdf->output();

function imprimeCabecalhoEscola($pdf, $escola)
{
    $pdf->setFont('arial', 'b', 7);
    $pdf->cell(190, 4, $escola, "B", 1, "L", 0);
}

function imprimeBairro($pdf, $bairro, $fillColor)
{
    $pdf->setFillColor($fillColor);
    $pdf->setFont('arial', '', 7);
    $pdf->cell(190, 4, $bairro, "B", 1, "L", 1);
}

function getFillColor($cont)
{
    return $cont % 2 == 0 ? '255' : '240';
}
