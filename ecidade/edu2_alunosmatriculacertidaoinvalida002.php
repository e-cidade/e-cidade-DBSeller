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
use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

db_postmemory($_GET);

$matricula = new cl_matricula();

$where = "";
$filtros = [];
if (!empty($escola)) {
    $filtros[] = "ed57_i_escola = {$escola}";
}

if (!empty($ano)) {
    $filtros[] = "ed52_i_ano = {$ano}";
}

if (!empty($etapa)) {
    $filtros[] = "ed221_i_serie = {$etapa}";
}

if ($tipoPreenchimento == '0') {
    $filtros[] = "(trim(ed47_certidaomatricula) = '' or ed47_certidaomatricula is null)";
} elseif ($tipoPreenchimento == 1) {
    $filtros[] = "trim(ed47_certidaomatricula) <> ''";
}
if (count($filtros) > 0) {
    $where = " where ".implode(" and ", $filtros);
}

$sqlMatriculas = "select ed57_i_escola,
                         ed18_c_nome,
                         ed221_i_serie,
                         ed11_c_descr,
                         ed47_i_codigo, 
                         ed47_v_nome, 
                         ed47_certidaomatricula  
                    from matricula 
                         inner join turma on ed57_i_codigo = ed60_i_turma
                         inner join escola on ed18_i_codigo = ed57_i_escola
                         inner join calendario on ed52_i_codigo = ed57_i_calendario
                         inner join matriculaserie on ed221_i_matricula = ed60_i_codigo
                         inner join serie on ed11_i_codigo = ed221_i_serie
                         inner join aluno on ed47_i_codigo = ed60_i_aluno
                   {$where}
                   order by ed18_c_nome, 
                            ed11_c_descr, 
                            ed47_v_nome";
$rsMatriculas  = $matricula->sql_record($sqlMatriculas);
$qtdRegistros = $matricula->numrows;
if ($qtdRegistros == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum_registro_encontrado'");
}

$alunos = db_utils::getCollectionByRecord($rsMatriculas);

$filtroEscola = "Todas as escolas";
if (!empty($escola)) {
    $filtroEscola = $alunos[0]->ed18_c_nome;
}

$filtroEtapa = "Todas as etapas";
if (!empty($etapa)) {
    $filtroEtapa = $alunos[0]->ed11_c_descr;
}

$filtroAnoLetivo = "Todos";
if (!empty($ano)) {
    $filtroAnoLetivo = $ano;
}

/*
 * Emissão do PDF
 */
$pdf = new Pdf();
$pdf->addTitulo("Listagem de alunos com a matricula da certidão de nascimento inválida", 1);
$pdf->addTitulo("Escola: {$filtroEscola}", 3);
$pdf->addTitulo("Ano Letivo: {$filtroAnoLetivo}", 4);
$pdf->addTitulo("Etapa: {$filtroEtapa}", 5);
$pdf->setfillcolor(223);
$pdf->init();

$fill = 1;

$hashEscola = null;
$hashEtapa = null;

$qtdRegistros = 0;
$qtdRegistrosInformados = 0;
$qtdRegistrosNaoInformados = 0;

$qtdRegistrosGeral = 0;
$qtdRegistrosInformadosGeral = 0;
$qtdRegistrosNaoInformadosGeral = 0;

$alunosInvalidos = [];

foreach ($alunos as $aluno) {
    if (!verificaValidadeNumeroMatriculaCertidao($aluno->ed47_certidaomatricula)) {
        $alunosInvalidos[] = $aluno;
    }
}

foreach ($alunosInvalidos as $aluno) {
    if ($hashEscola != $aluno->ed18_c_nome) {
        if ($qtdRegistros > 0) {
            $pdf->setfont('arial', 'b');
            if ($tipoPreenchimento == '') {
                $pdf->cell(175, 4, "Quantidade de registros não informados:", 0, 0, "R", 0);
                $pdf->cell(20, 4, $qtdRegistrosNaoInformados, 0, 1, "R", 0);
                $pdf->cell(175, 4, "Quantidade de registros informados:", 0, 0, "R", 0);
                $pdf->cell(20, 4, $qtdRegistrosInformados, 0, 1, "R", 0);
            }
            $pdf->cell(175, 4, "Quantidade de registros:", 0, 0, "R", 0);
            $pdf->cell(20, 4, $qtdRegistros, 0, 1, "R", 0);

            $qtdRegistros = 0;
            $qtdRegistrosInformados = 0;
            $qtdRegistrosNaoInformados = 0;
        }

        $pdf->setfont('arial', 'b');
        $pdf->cell(20, 4, "Escola:", 0, 0, "L", 0);
        $pdf->cell(120, 4, $aluno->ed18_c_nome, 0, 1, "L", 0);
        $pdf->setfont('arial', '');

        escreveCabecalho($pdf);
    }
    $hashEscola = $aluno->ed18_c_nome;

    if ($hashEtapa != $aluno->ed11_c_descr) {
        if ($qtdRegistros > 0) {
            $pdf->cell(10, 4, "", 0, 1, "L", 0);
        }
        $pdf->setfont('arial', 'b');
        $pdf->cell(20, 4, "Etapa:", "B", 0, "L", 0);
        $pdf->cell(175, 4, $aluno->ed11_c_descr, "B", 1, "L", 0);
        $pdf->setfont('arial', '');
    }
    $hashEtapa = $aluno->ed11_c_descr;


    if ($pdf->GetY() > ($pdf->getH() - 22)) {
        escreveCabecalho($pdf);
    }

    $fill = ($fill == 1)?0:1;
     
    $pdf->setfont('arial', '');
    $pdf->cell(15, 4, $aluno->ed47_i_codigo, 0, 0, "C", $fill);
    $pdf->cell(85, 4, $aluno->ed47_v_nome, 0, 0, "L", $fill);
    $pdf->cell(95, 4, $aluno->ed47_certidaomatricula, 0, 1, "C", $fill);
    
    $qtdRegistros++;
    if (empty($aluno->ed47_certidaomatricula)) {
        $qtdRegistrosNaoInformados++;
    } else {
        $qtdRegistrosInformados++;
    }

    $qtdRegistrosGeral++;
    if (empty($aluno->ed47_certidaomatricula)) {
        $qtdRegistrosNaoInformadosGeral++;
    } else {
        $qtdRegistrosInformadosGeral++;
    }
}
    $pdf->setfont('arial', 'b');
if ($tipoPreenchimento == '') {
    $pdf->cell(175, 4, "Quantidade de registros não informados:", 0, 0, "R", 0);
    $pdf->cell(20, 4, $qtdRegistrosNaoInformados, 0, 1, "R", 0);
    $pdf->cell(175, 4, "Quantidade de registros informados:", 0, 0, "R", 0);
    $pdf->cell(20, 4, $qtdRegistrosInformados, 0, 1, "R", 0);
}
    $pdf->cell(175, 4, "Quantidade de registros:", 0, 0, "R", 0);
    $pdf->cell(20, 4, $qtdRegistros, 0, 1, "R", 0);

    $pdf->ln(4);

if (empty($escola)) {
    $pdf->setfont('arial', 'b');
    if ($tipoPreenchimento == '') {
        $pdf->cell(65, 4, "Quantidade total de registros não informados:", 0, 0, "R", 0);
        $pdf->cell(20, 4, $qtdRegistrosNaoInformadosGeral, 0, 1, "R", 0);
        $pdf->cell(65, 4, "Quantidade total de registros informados:", 0, 0, "R", 0);
        $pdf->cell(20, 4, $qtdRegistrosInformadosGeral, 0, 1, "R", 0);
    }
    $pdf->cell(65, 4, "Quantidade total de registros:", 0, 0, "R", 0);
    $pdf->cell(20, 4, $qtdRegistrosGeral, 0, 1, "R", 0);
}
$pdf->output();

function escreveCabecalho($pdf)
{
    $pdf->setfont('arial', 'b');
    $pdf->cell(15, 4, "Código", 1, 0, "C", 1);
    $pdf->cell(85, 4, "Nome", 1, 0, "L", 1);
    $pdf->cell(95, 4, "Matricula Certidão", 1, 1, "C", 1);
    $pdf->setfont('arial', '');
}
