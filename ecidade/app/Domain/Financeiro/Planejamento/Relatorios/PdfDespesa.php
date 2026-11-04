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

namespace App\Domain\Financeiro\Planejamento\Relatorios;

/**
 * Classe base para impressão dos relatórios do ppa
 * Class Pdf
 * @package App\Domain\Financeiro\Planejamento\Relatorios
 */
abstract class PdfDespesa extends Pdf
{
    protected $wValor = 20;
    protected $wLinha = 193;

    protected $apresentaRegionalizacao = true;
    protected $apresentaProduto = true;
    protected $apresentaValoresMetaFisicas = true;

    protected $wIniciativa = 50;
    protected $wRegionalizacao = 40;
    protected $wProduto = 33;
    protected $wMetaFisica = 35;

    protected $nivelIniciativa = '1.3';
    protected $nivelObjetivosPrograma = '1.3';
    protected $nivelMetasObjetivosPrograma = '1.3.1';

    /**
     * @var bool
     */
    protected $apresentaValoresMetaObjetivo = false;
    protected $apresentaMetasObjetivoPrograma = true;

    public function headers($titulo)
    {
        $this->addTitulo($titulo);

        $this->addTitulo(sprintf(
            '%s - %s',
            $this->planejamento['pl2_titulo'],
            $this->periodo
        ));
    }

    public function setDados(array $dados)
    {
        parent::setDados($dados);

        $this->wValores = $this->wValor * count($this->planejamento['exercicios']);
        $this->wTitulo = $this->wLinha - $this->wValores;
    }

    public function setFiltros(array $filtros)
    {
        if (isset($filtros['apresentaRegionalizacao'])) {
            $this->apresentaRegionalizacao = $filtros['apresentaRegionalizacao'] == 1;
        }
        if (isset($filtros['apresentaProduto'])) {
            $this->apresentaProduto = $filtros['apresentaProduto'] == 1;
        }
        if (isset($filtros['apresentaValoresMetaFisicas'])) {
            $this->apresentaValoresMetaFisicas = $filtros['apresentaValoresMetaFisicas'] == 1;
        }
        if (isset($filtros['apresentaMetasObjetivoPrograma'])) {
            $this->apresentaMetasObjetivoPrograma = $filtros['apresentaMetasObjetivoPrograma'];
            if ($this->apresentaMetasObjetivoPrograma) {
                $this->nivelIniciativa = '1.3.2';
            }
        }

        if (isset($filtros['isPPA'])) {
            $this->isPPA = $filtros['isPPA'];
        }

        if (!$this->apresentaRegionalizacao) {
            $this->wIniciativa += $this->wRegionalizacao;
        }
        if (!$this->apresentaProduto) {
            $this->wIniciativa += $this->wProduto;
        }
        if (!$this->apresentaValoresMetaFisicas) {
            $this->wIniciativa += $this->wMetaFisica;
        }
    }

    protected function cabecalhoIniciativa()
    {
        if (32 > $this->getAvailHeight()) {
            $this->AddPage();
        }

        $xInicioValores = 133;
        if (!$this->apresentaValoresMetaFisicas) {
            $xInicioValores += $this->wMetaFisica;
        }

        $this->SetFont('Arial', 'B', 8);
        $this->Cell($this->wIniciativa, 10, "$this->nivelIniciativa - Iniciativas", 1, 0, 'C', 1);
        if ($this->apresentaRegionalizacao) {
            $this->Cell($this->wRegionalizacao, 10, 'Regionalização', 1, 0, 'C', 1);
        }
        if ($this->apresentaProduto) {
            $this->Cell($this->wProduto, 10, 'Produto', 1, 0, 'C', 1);
        }
        $this->Cell(35, 5, 'Metas Financeiras', 1, 0, 'C', 1);
        if ($this->apresentaValoresMetaFisicas) {
            $this->Cell($this->wMetaFisica, 5, 'Metas Fisicas', 1, 0, 'C', 1);
        }
        $this->ln();

        $this->SetX($xInicioValores);
        $this->SetFont('Arial', 'B', 6);
        $this->Cell(9, 5, 'Ano', 1, 0, 'C', 1);
        $this->Cell(26, 5, 'Valor', 1, 0, 'C', 1);
        if ($this->apresentaValoresMetaFisicas) {
            $this->Cell(15, 5, 'Unid. Medida', 1, 0, 'C', 1);
            $this->Cell(20, 5, 'Metas Fisicas', 1, 0, 'C', 1);
        }
        $this->Ln();
        $this->SetFont('Arial', '', 7);
    }

    protected function getAlturaLinha($iniciativa, $regionalizacao, $produto)
    {
        $linhas = [$this->NbLines($this->wIniciativa, $iniciativa)];
        if ($this->apresentaRegionalizacao) {
            $linhas[] = $this->NbLines($this->wRegionalizacao, $regionalizacao);
        }
        if ($this->apresentaProduto) {
            $linhas[] = $this->NbLines($this->wProduto, $produto);
        }

        $numeroLinhas = collect($linhas)->max();
        $numeroExercicios = 1;
        if ($this->isPPA) {
            $numeroExercicios = count($this->exercicios);
        }
        if ($numeroLinhas < $numeroExercicios) {
            $numeroLinhas = $numeroExercicios;
        }

        return $numeroLinhas * 4;
    }

    protected function imprimeIniciativas($iniciativas)
    {
        $this->cabecalhoIniciativa();
        foreach ($iniciativas as $iniciativa) {
            $produto = $iniciativa['descricao_produto'];
            $regionalizacao = collect($iniciativa['regionalizacoes'])->implode('o11_descricao', ', ');
            $descricao = sprintf(
                '%s - %s',
                $iniciativa['acao'],
                $iniciativa['descricao_acao']
            );

            $this->SetFont('Arial', '', 6);
            $alturaLinha = $this->getAlturaLinha($descricao, $regionalizacao, $produto);

            if ($alturaLinha > $this->getAvailHeight()) {
                $this->AddPage();
            }

            $x = $this->GetX();
            $y = $this->GetY();


            $alturaFinal = $y + $alturaLinha;
            // desenha linhas
            $this->Line($x, $y, $x, $alturaFinal); // primeira coluna
            $this->Line(203, $y, 203, $alturaFinal); // ultima coluna
            $this->Line($x, $y, 203, $y); // linha de cima
            $this->Line($x, $alturaFinal, 203, $alturaFinal); // linha de baixo

            $this->MultiCell($this->wIniciativa, 4, $descricao);
            $xAtualizado = $x + $this->wIniciativa;
            $this->Line($xAtualizado, $y, $xAtualizado, $alturaFinal); // coluna final iniciativas

            if ($this->apresentaRegionalizacao) {
                $this->SetXY($xAtualizado, $y);
                $this->MultiCell($this->wRegionalizacao, 4, $regionalizacao, 0, 'L');
                $xAtualizado = $xAtualizado + $this->wRegionalizacao;
                $this->Line($xAtualizado, $y, $xAtualizado, $alturaFinal); //  coluna final regionalizacao
            }

            if ($this->apresentaProduto) {
                $this->SetXY($xAtualizado, $y);
                $this->MultiCell($this->wProduto, 4, $produto, 0, 'L');
                $xAtualizado = $xAtualizado + $this->wProduto;
                $this->Line($xAtualizado, $y, $xAtualizado, $alturaFinal); //  coluna final produto
            }
            $this->SetXY($xAtualizado, $y);

            if (count($iniciativa['metas'])) {
                if (!$this->isPPA) {
                    $valor = array_shift($iniciativa['metas']);
                    $iniciativa['metas'] = [$valor];
                }
                foreach ($iniciativa['metas'] as $meta) {
                    $this->SetX($xAtualizado);
                    $this->Cell(9, 4, $meta['exercicio'], 1, 0, 'C');
                    $this->Cell(26, 4, formataValorMonetario($meta['meta_financeira']), 1, 0, 'R');
                    if ($this->apresentaValoresMetaFisicas) {
                        $this->Cell(15, 4, $meta['unidade'], 1, 0, 'C');
                        $this->Cell(20, 4, $meta['meta_fisica'], 1, 0, 'C');
                    }
                    $this->ln();
                }
            } else {
                $exercicios = $this->exercicios;
                if (!$this->isPPA) {
                    $valor = array_shift($exercicios);
                    $exercicios = [$valor];
                }

                foreach ($exercicios as $exercicio) {
                    $this->SetX($xAtualizado);
                    $this->Cell(9, 4, $exercicio, 1, 0, 'C');
                    $this->Cell(26, 4, '', 1, 0, 'R');
                    if ($this->apresentaValoresMetaFisicas) {
                        $this->Cell(15, 4, '', 1, 0, 'C');
                        $this->Cell(20, 4, '', 1, 0, 'C');
                    }
                    $this->ln();
                }
            }

            $this->SetY($alturaFinal);
        }
    }

    protected function imprimeOrgaos(array $orgaos)
    {
        $this->cabecalhoOrgao();
        foreach ($orgaos as $orgao) {
            $this->Cell(15, 4, $orgao['formatado'], 1, 0, 'L');
            $this->Cell(178, 4, $orgao['descricao'], 1, 1, 'L');
        }
    }


    protected function cabecalhoOrgao()
    {
        if (($this->GetY() + 10) > $this->getH()) {
            $this->AddPage();
        }
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(193, 5, '1.2 - Órgãos Responsáveis', 1, 1, 'L', 1);
        $this->Cell(15, 5, 'Código', 1, 0, 'C', 1);
        $this->Cell(178, 5, 'Descrição', 1, 1, 'C', 1);
        $this->SetFont('Arial', '', 7);
    }


    protected function imprimeAreaResultado(array $programa)
    {
        if ($this->planejamento['pl2_composicao'] === 2 && !empty($programa['areasResultado'])) {
            $areaResultado = $programa['areasResultado'];
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(193, 5, 'Área de Resultado', 1, 1, 'L', 1);
            $this->SetFont('Arial', '', 7);
            $this->MultiCell(193, 5, "Título: {$areaResultado['pl4_titulo']}", 1);
            if (!empty($areaResultado['pl4_contextualizacao'])) {
                $this->MultiCell(193, 5, "Contextualização: {$areaResultado['pl4_contextualizacao']}", 1);
            }
        }
    }

    protected function imprimeObjetivos($objetivos)
    {
        if (empty($objetivos)) {
            return;
        }
        if (!$this->apresentaMetasObjetivoPrograma) {
            return;
        }
        $this->cabecalhoObjetivos();
        foreach ($objetivos as $objetivo) {
            $this->SetFont('Arial', 'B', 8);

            $descricao = sprintf(
                '%s - %s',
                str_pad($objetivo['pl11_numero'], 5, ' ', STR_PAD_LEFT),
                $objetivo['pl11_descricao']
            );

            $nbLines = $this->NbLines(153, $descricao);
            $linhas = ($nbLines > $this->quantidadeExercicios) ? $nbLines : $this->quantidadeExercicios;

            $altura = $this->calculaAlturaLinha($linhas);
            if ($this->getAvailHeight() < ($altura + 5)) {
                $this->AddPage();
            }

            $this->Cell(153, 5, 'Descrição do Objetivo', 1, 0, 'L', 1);
            $this->Cell(40, 5, 'Valores', 1, 1, 'L', 1);
            $this->SetFont('Arial', '', 7);

            $y = $this->GetY();
            $this->MultiCell(153, 4, $descricao);
            $this->SetY($y);

            $this->imprimevalores($objetivo['valores']);

            $this->SetY($y + $altura);
            $x = $this->GetX();
            $this->Line($x, $y, $x, $this->GetY());
            $this->Line($x, $this->GetY(), 203, $this->GetY());
            $this->Line(203, $y, 203, $this->GetY());

            if (!empty($objetivo['ods'])) {
                $this->SetFont('Arial', 'B', 7);
                if ($this->getAvailHeight() < 4) {
                    $this->AddPage();
                }
                $this->Cell(9, 4, 'ODS', 1);
                $this->SetFont('Arial', '', 7);

                $this->Cell(184, 4, $objetivo['ods']['pl26_descricao'], 1, 1);
            }

            if (!empty($objetivo['metas'])) {
                $this->imprimeMetaObjetivo($objetivo['metas']);
            }

            if (!empty($objetivo['iniciativas'])) {
                $this->imprimeIniciativas($objetivo['iniciativas']);
            }
        }
    }

    protected function cabecalhoObjetivos()
    {
        if (($this->GetY() + 10) > $this->getH()) {
            $this->AddPage();
        }

        $titulo = sprintf('%s - Objetivos do Programa', $this->nivelObjetivosPrograma);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(193, 5, $titulo, 1, 1, 'L', 1);
        $this->SetFont('Arial', '', 7);
    }

    protected function cabecalhoMetaObjetivo()
    {
        $altura = $this->apresentaValoresMetaObjetivo ? 25 : 15;
        if ($this->getAvailHeight() < $altura) {
            $this->AddPage();
        }

        $titulo = sprintf('%s - Metas do Objetivo', $this->nivelMetasObjetivosPrograma);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(193, 5, $titulo, 1, 1, 'L', 1);

        if ($this->apresentaValoresMetaObjetivo) {
            $this->Cell(153, 5, 'Descrição', 1, 0, 'L', 1);
            $this->Cell(40, 5, 'Indicadores de Resultado', 1, 1, 'L', 1);
        } else {
            $this->Cell(193, 5, 'Descrição', 1, 1, 'L', 1);
        }
        $this->SetFont('Arial', '', 7);
    }

    /**
     * @param $metas
     */
    protected function imprimeMetaObjetivo($metas)
    {
        $this->cabecalhoMetaObjetivo();
        foreach ($metas as $meta) {
            if ($this->apresentaValoresMetaObjetivo) {
                $this->imprimeValoresMetaObjetivo($meta);
            } else {
                $this->imprimeLinhaMetaObjetivo($meta['pl21_texto']);
            }
        }
    }

    protected function imprimeValoresMetaObjetivo(array $meta)
    {
        $texto = $meta['pl21_texto'];
        $linhas = $this->NbLines(153, $texto);

        $linhas = $linhas > $this->quantidadeExercicios ? $linhas : $this->quantidadeExercicios;
        $alturaLinha = $this->calculaAlturaLinha($linhas);

        if ($this->getAvailHeight() < $alturaLinha) {
            $this->AddPage();
        }

        $y = $this->GetY();
        $yFinal = $alturaLinha + $y;

        $this->imprimeLinhaMetaObjetivo($texto, 153, 0);
        $this->SetY($y);
        $this->imprimevalores($meta['valores']);
        $this->Line($this->GetX(), $y, $this->GetX(), $yFinal);
        $this->Line(203, $y, 203, $yFinal);
        $this->Line($this->GetX(), $y, 203, $y);
        $this->Line($this->GetX(), $yFinal, 203, $yFinal);
        $this->SetY($yFinal);
    }

    protected function imprimeLinhaMetaObjetivo($texto, $w = 193, $border = 1)
    {
        $linhas = $this->NbLines($w, $texto);
        $alturaLinha = $this->calculaAlturaLinha($linhas);
        if ($this->getAvailHeight() < $alturaLinha) {
            $this->AddPage();
        }

        $this->MultiCell($w, 4, $texto, $border, "L");
    }

    protected function imprimevalores($valores, $x = 163)
    {

        if (!empty($valores)) {
            if (!$this->isPPA) {
                $valor = array_shift($valores);
                $valores = [$valor];
            }
            
            foreach ($valores as $valor) {
                $this->SetX($x);
                $this->Cell(9, 4, $valor['pl10_ano'], 1, 0, 'C');
                $this->Cell(31, 4, formataValorMonetario($valor['pl10_valor']), 1, 1, 'R');
            }
        } else {
            // se não tem valores imprime as colunas vazias
            foreach ($this->exercicios as $exercicio) {
                $this->SetX($x);
                $this->Cell(9, 4, $exercicio, 1, 0, 'C');
                $this->Cell(31, 4, '', 1, 1, 'R');
            }
        }
    }
}
