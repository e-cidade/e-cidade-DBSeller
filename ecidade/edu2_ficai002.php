<?php

use ECidade\Educacao\Escola\Registry\AlunoRegistry;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification("libs/db_conect"."a.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("fpdf151/pdf.php"));

try {
    if (empty($_GET['aluno'])) {
        throw new Exception("Código do aluno não informado.");
    }

    $outrosDados = null;
    if (isset($_GET['dados'])) {
        $outrosDados = JSON::create()->parse(base64_decode($_GET['dados']));
    }

    $aluno = AlunoRepository::getAlunoByCodigo($_GET['aluno']);
    $matricula = MatriculaRepository::getUltimaMatriculaAluno($aluno);
    $escola = $matricula->getTurma()->getEscola();

    $telefones = array_map(function ($telefone) {
        return DBString::formatarTelefone($telefone->iDDD . $telefone->iNumero);
    }, $escola->getTelefones());

    $pdf = new FpdfMultiCellBorder();
    $pdf->exibeHeader(true);
    $pdf->setExibeBrasao(true);
    $pdf->mostrarRodape(true);
    $pdf->mostrarTotalDePaginas(true);
    $pdf->Open();
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AliasNbPages();
    $pdf->SetFillColor(235);
    $pdf->SetFont('Arial', 'B', 10);

    $pdf->AddPageFicai();


    $pdf->Cell(192, 6, 'FICHA DE COMUNICAÇÃO DO ALUNO INFREQUENTE - FICAI', 'B', 1, 'C');

    $pdf->Ln(4);
    estiloFonte($pdf, 'B');
    $pdf->Cell(192, 5, "Dados da Escola:   (   ) ESTADUAL       ( X ) MUNICIPAL       (   ) PARTICULAR", '', 1, 'J');
    $pdf->Cell(27, 5, 'Nome da Escola: ');
    estiloFonte($pdf);
    $pdf->Cell(167, 5, $escola->getNome(), 0, 1);

    $endereco = array_filter([
        $escola->getEndereco(),
        $escola->getNumeroEndereco(),
        $escola->getBairro()
    ]);
    estiloFonte($pdf, 'B');
    $pdf->Cell(27, 5, 'Endereço: ');
    estiloFonte($pdf);
    $pdf->Cell(100, 5, implode(', ', $endereco), 0, 1);

    estiloFonte($pdf, 'B');
    $pdf->Cell(27, 5, 'Telefones: ');
    estiloFonte($pdf);
    $pdf->Cell(80, 5, implode(' | ', $telefones), 0, 0);
    estiloFonte($pdf, 'B');
    $pdf->Cell(12, 5, 'E-mail: ');
    estiloFonte($pdf);
    $pdf->Cell(57, 5, $escola->getEmail(), 0, 1);

    $diretor = array_shift($escola->getDiretor());
    if (!empty($diretor)) {
        $diretor = $diretor->sNome;
    }

    estiloFonte($pdf, 'B');
    $pdf->Cell(27, 5, 'Diretor(a): ');
    estiloFonte($pdf);
    $pdf->Cell(100, 5, $diretor, 0, 1);

    linhaSeparadora($pdf);

    estiloFonte($pdf, 'B');
    $pdf->Cell(192, 6, 'Dados do(a) Aluno(a)', 0, 1);

    $pdf->Cell(20, 5, 'Nome: ');
    estiloFonte($pdf);
    $pdf->Cell(167, 5, $aluno->getNome(), 0, 1);

    $dataNascimento = new DateTime($aluno->getDataNascimento());
    $idade = $dataNascimento->diff(new DateTime());
    estiloFonte($pdf, 'B');
    $pdf->Cell(20, 5, 'Data Nasc.:');
    estiloFonte($pdf);
    $pdf->Cell(50, 5, $dataNascimento->format('d/m/Y'));
    estiloFonte($pdf, 'B');
    $pdf->Cell(10, 5, 'Idade:');
    estiloFonte($pdf);
    $pdf->Cell(50, 5, "{$idade->y} anos e {$idade->m} meses", 0, 1);

    $endereco = array_filter([
        $aluno->getEnderecoResidencia(),
        $aluno->getNumeroResidencia(),
        $aluno->getComplementoResidencia(),
        $aluno->getBairroResidencia()
    ]);
    estiloFonte($pdf, 'B');
    $pdf->Cell(20, 5, 'Endereço: ');
    estiloFonte($pdf);
    $pdf->Cell(100, 5, implode(', ', $endereco), 0, 1);

    estiloFonte($pdf, 'B');
    $pdf->Cell(20, 5, 'Município: ');
    estiloFonte($pdf);
    $pdf->Cell(100, 5, $aluno->getMunicipioResidencia()->getNome(), 0, 1);

    linhaSeparadora($pdf);

    estiloFonte($pdf, 'B');
    $pdf->Cell(192, 6, 'Filiação', 0, 1);

    $pdf->Cell(20, 5, 'Filiação 1: ');
    estiloFonte($pdf);
    $pdf->Cell(167, 5, $aluno->getNomeMae(), 0, 1);

    estiloFonte($pdf, 'B');
    $pdf->Cell(20, 5, 'Filiação 2: ');
    estiloFonte($pdf);
    $pdf->Cell(167, 5, $aluno->getNomePai(), 0, 1);

    estiloFonte($pdf, 'B');
    $pdf->Cell(20, 5, 'Telefone(s): ');
    estiloFonte($pdf);
    $telefones = array_map(function ($telefone) {
        return DBString::formatarTelefone($telefone);
    }, array_filter([$aluno->getNumeroTelefone(), $aluno->getNumeroCelular()]));
    $pdf->Cell(167, 5, implode(' e ', $telefones), 0, 1);

    $dadosAluno = AlunoRegistry::get($aluno->getCodigoAluno());
    estiloFonte($pdf, 'B');
    $pdf->Cell(42, 5, 'Beneficiário do Programa: ');
    estiloFonte($pdf);

    $bolsaFamilia = $dadosAluno->isBolsafamilia() ? 'X' : '';
    $pdf->Cell(35, 5, "( {$bolsaFamilia} ) Bolsa Família/BVJ");
    $pdf->Cell(30, 5, '(   ) Renda Melhor');
    $pdf->Cell(35, 5, '(   ) Renda Melhor Jovem', 0, 1);

    linhaSeparadora($pdf);

    estiloFonte($pdf, 'B');
    $pdf->Cell(192, 6, 'Situação Escolar', 0, 1);

    $pdf->Cell(20, 5, 'Modalidade: ');
    estiloFonte($pdf);
    $pdf->Cell(167, 5, $matricula->getEtapaDeOrigem()->getEnsino()->getNome(), 0, 1);

    estiloFonte($pdf, 'B');
    $pdf->Cell(20, 5, 'Etapa: ');
    estiloFonte($pdf);
    $pdf->Cell(15, 5, $matricula->getEtapaDeOrigem()->getNomeAbreviado());
    estiloFonte($pdf, 'B');
    $pdf->Cell(12, 5, 'Turma: ');
    estiloFonte($pdf);
    $pdf->Cell(100, 5, $matricula->getTurma()->getDescricao());

    estiloFonte($pdf, 'B');
    $pdf->Cell(11, 5, 'Turno: ');
    estiloFonte($pdf);
    $turno = $matricula->getTurma()->getTurno();
    $turnosReferencia = array_map(function ($referencia) {
        return Turno::getDescricaoTurno($referencia);
    }, $turno->getTurnoReferente());

    $pdf->Cell(50, 5, implode(' e ', $turnosReferencia), 0, 1);

    estiloFonte($pdf, 'B');
    $pdf->Cell(30, 5, 'Faltando mais de: ');
    $pdf->Cell(20, 5, '(   ) 10 dias     (   ) 1 mês      (   ) 1 Bimestre       (   ) 2 Bimestres      (   ) 3 Bimestres', 0, 1);
    estiloFonte($pdf, '', 6);
    $pdf->AddPage();
    estiloFonte($pdf, 'B');

    /**
     * Imprimir segunda página
     */
    estiloFonte($pdf, 'B', 8);
    $pdf->Cell(192, 5, 'Procedimentos adotados pela escola:', 0, 1);
    if (!is_null($outrosDados) && !empty($outrosDados->procedimentoEscola)) {
        estiloFonte($pdf, '', 7);
        $pdf->MultiCell(192, 4, $outrosDados->procedimentoEscola);
    } else {
        imprimeLinhas($pdf);
    }
    $pdf->Ln(4);
    estiloFonte($pdf, 'B', 8);
    $pdf->Cell(192, 5, 'Observação acerca do(a) aluno(a):', 0, 1);
    if (!is_null($outrosDados) && !empty($outrosDados->observacaoAluno)) {
        estiloFonte($pdf, '', 7);
        $pdf->MultiCell(192, 4, $outrosDados->observacaoAluno);
    } else {
        imprimeLinhas($pdf);
    }

    $pdf->Ln(10);

    assinatura($pdf, 'Assinatura do Diretor');
    estiloFonte($pdf, 'B', 7);
    $pdf->Cell(70, 5, 'Data do encaminhamento da FICAI ao Conselho Tutelar: ');
    $data = '_____/_____/_________';
    if (!is_null($outrosDados) && !empty($outrosDados->data)) {
        $data = $outrosDados->data;
    }
    $pdf->Cell(30, 5, $data, 0, 1);
    $pdf->Ln(4);

    estiloFonte($pdf, 'B');
    $pdf->Cell(192, 6, 'Medidas Adotadas pelo Conselho Tutelar', 0, 1);
    estiloFonte($pdf, 'B', 7);
    $pdf->Cell(38, 6, 'Nome do(a) Pedagogo(a):');
    $pdf->Ln(4);
    $pdf->Line(45, $pdf->GetY(), 202, $pdf->GetY());
    $pdf->Ln(2);

    $pdf->Cell(38, 6, 'Motivos  alegados para faltas:', 0, 1);
    imprimeLinhas($pdf, 5);
    $pdf->Ln(2);
    $pdf->Cell(38, 6, 'Orientações dada pelo Setor Pedagógico:', 0, 1);
    imprimeLinhas($pdf, 5);
    $pdf->Ln(11);
    assinatura($pdf, 'Assinatura do(a) Pedagogo(a)');

    $pdf->Output();
} catch (Exception $erro) {
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $erro->getMessage());
}

function linhaSeparadora(FpdfMultiCellBorder $pdf)
{
    $pdf->Ln();
    $pdf->Line($pdf->GetX(), $pdf->GetY(), 202, $pdf->GetY());
    $pdf->Ln();
}

function estiloFonte(FpdfMultiCellBorder $pdf, $style = '', $size = 9)
{
    $pdf->SetFont('Arial', $style, $size);
}

function imprimeLinhas(FpdfMultiCellBorder $pdf, $numeroDeLinhas = 9)
{
    for ($i = 0; $i < $numeroDeLinhas; $i++) {
        $pdf->Ln(6);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), 202, $pdf->GetY());
    }
}

function assinatura(FpdfMultiCellBorder $pdf, $label)
{
    $pdf->SetX(106);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), 202, $pdf->GetY());
    estiloFonte($pdf, '', 6);
    $pdf->Cell(94, 3, $label, 0, 1, 'C');
}
