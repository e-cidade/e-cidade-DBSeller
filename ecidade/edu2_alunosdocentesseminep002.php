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

use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\Helpers\Pessoa as PessoaHelper;
use ECidade\Educacao\Escola\Censo\Identificacao\DadosExportacao as DadosExportacaoIdentificacao;
use ECidade\Educacao\Escola\Censo\Identificacao\Model\Pessoa;
use ECidade\Educacao\Escola\Registry\CensoMunicipioRegistry;
use DBDate;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));


try {
    if (!isset($_GET['filtros'])) {
        throw new Exception('Não foi informado os filros para emissão do relatório.');
    }
    $filtros = JSON::create()->parse(base64_decode($_GET['filtros']));

    $censo = new Censo($filtros->ano);
    $escolas = array_map(function ($codigo) {
        return EscolaRepository::getEscolaByCodigo($codigo);
    }, $filtros->escolas);

    $dadosExportacao = new DadosExportacaoIdentificacao();
    $dadosExportacao->setCenso($censo);
    $possuiDataPersonalizada = (!is_null($filtros->data_censo)
                                && $censo->getDataCenso()->getDate(DBDate::DATA_PTBR) != $filtros->data_censo);
    
    if ($possuiDataPersonalizada) {
        $dadosExportacao->setDataCensoPersonalizada(new DBDate($filtros->data_censo));
    }

    $dadosExportacao->setEscolas($escolas);
    $dadosExportacao->processar();
    $serviceExportacao = $dadosExportacao->getService();
    if ($possuiDataPersonalizada) {
        $serviceExportacao->setDataCensoPersonalizada(new DBDate($filtros->data_censo));
    }

    $profissionais = [];
    $alunos = [];

    $pessoas = $serviceExportacao->getPessoas();
    foreach ($pessoas as $pessoa) {
        if (PessoaHelper::isAluno($pessoa->getCodigoPessoa())) {
            $alunos[] = $pessoa;
        } else {
            $profissionais[] = $pessoa;
        }
    }

    $pdf = new Pdf();
    $pdf->init(false);
    $pdf->exibeHeader(true, \Fpdf\Pdf::HEADER_DEFAULT);
    $pdf->setExibeBrasao(true);
    $pdf->addTitulo("Listagem de Pessoas sem Código INEP", 1);

    $pdf->SetAutoPageBreak(false, 15);
    $pdf->SetFillColor('240');

    imprimirAlunos($pdf, $alunos);
    imprimirProfissionais($pdf, $profissionais);

    $pdf->Output();
} catch (Exception $e) {
    $sMsg = urlencode($e->getMessage());
    db_redireciona('db_erros.php?fechar=true&db_erro=' . $sMsg);
}

/**
 * @param $pdf
 * @param string $label1
 * @param string $label2
 */
function imprimeHeader($pdf, $labelTitulo, $label1 = 'Código', $label2 = "Aluno")
{
    $larguraUtil = ($pdf->getW() - $pdf->getRightMargin() - $pdf->getLeftMargin());

    $pdf->AddPage("L");
    $pdf->setfont('arial', 'b', 8);
    $pdf->cell($larguraUtil, 4, $labelTitulo.":", 0, 1, "L", 0);

    $pdf->cell(20, 4, $label1, "TBR", 0, "C", 1);
    $pdf->cell(90, 4, $label2, 1, 0, "C", 1);
    $pdf->cell(20, 4, 'Nascimento', 1, 0, "C", 1);
    $pdf->cell(90, 4, 'Mãe', 1, 0, "C", 1);
    $pdf->cell(50, 4, 'Naturalidade', 1, 0, "C", 1);
    $pdf->cell(5, 4, 'UF', "TBL", 1, "C", 1);
    $pdf->setfont('arial', '', 8);
}

/**
 * @param $pdf
 * @param Pessoa $pessoa
 * @throws Exception
 */
function imprimePessoa($pdf, Pessoa $pessoa)
{
    $codigoMunicipioNascimento = $pessoa->getCodigoMunicipioNascimento();
    $codigoComMascara = $pessoa->getCodigoPessoa();
    $codigo = PessoaHelper::decode($codigoComMascara);

    if (empty($codigoMunicipioNascimento) && $pessoa->getNacionalidade() !== 3) {
        $tipo = PessoaHelper::isAluno($codigoComMascara) ? 'Aluno' : 'Profissional';
        throw new Exception(sprintf('%s %s não possui município de nascimento informado.', $tipo, $pessoa->getNome()));
    }

    $municipioNascimento = 'ESTRANGEIRO';
    $uFMunicipioNascimento = '';
    if ($pessoa->getNacionalidade() !== 3) {
        $municipio = CensoMunicipioRegistry::get($codigoMunicipioNascimento);
        $municipioNascimento = $municipio->getNome();
        $uFMunicipioNascimento = $municipio->getCensoUf()->getSigla();
    }

    $pdf->cell(20, 4, $codigo, "TBR", 0, "C");
    $pdf->cell(90, 4, $pessoa->getNome(), 1, 0, "L");
    $pdf->cell(20, 4, $pessoa->getDataNascimento(), 1, 0, "C");
    $pdf->cell(90, 4, $pessoa->getFiliacao1(), 1, 0, "L");
    $pdf->cell(50, 4, $municipioNascimento, 1, 0, "L");
    $pdf->cell(5, 4, $uFMunicipioNascimento, "TBL", 1, "C");
}

/**
 * @param $pdf
 * @param string $label
 * @param integer $total
 */
function imprimeTotalizador($pdf, $label, $total)
{
    $pdf->setfont('arial', 'B', 8);
    $pdf->cell(230, 4, "Total de {$label}", "TBR", 0, "R");
    $pdf->cell(48, 4, $total, "TBL", 0, "R");
    $pdf->setfont('arial', '', 8);
}

/**
 * @param $pdf
 * @param Pessoa[] $alunos
 * @throws Exception
 */
function imprimirAlunos($pdf, array $alunos)
{
    imprimeHeader($pdf, "Alunos");
    foreach ($alunos as $aluno) {
        if ($pdf->getY() >= ($pdf->getH() - 18)) {
            imprimeHeader($pdf, "Alunos");
        }
        imprimePessoa($pdf, $aluno);
    }

    imprimeTotalizador($pdf, 'Alunos', count($alunos));
}

/**
 * @param $pdf
 * @param Pessoa[] $profissionais
 * @throws Exception
 */
function imprimirProfissionais($pdf, array $profissionais)
{
    imprimeHeader($pdf, "Docentes", 'CPF', 'Docente');

    foreach ($profissionais as $profissional) {
        if ($pdf->getY() >= ($pdf->getH() - 18)) {
            imprimeHeader($pdf, "Docentes", 'CGM', 'Docente');
        }

        imprimePessoa($pdf, $profissional);
    }

    imprimeTotalizador($pdf, 'Docentes', count($profissionais));
}
