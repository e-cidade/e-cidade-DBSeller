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

use Exception;

/**
 * Classe base para impressão dos relatórios do ppa
 * Class Pdf
 * @package App\Domain\Financeiro\Planejamento\Relatorios
 */
abstract class Pdf extends \ECidade\Pdf\Pdf
{
    protected $wValor = 20;
    protected $wLinha = 193;
    protected $isPPA = true;

    protected $yCapa = 125;
    protected $wCapa = 190;

    protected $alturaLinha = 4;
    /**
     * @var array
     */
    protected $dados = [];
    /**
     * @var string
     */
    protected $periodo;
    /**
     * @var array
     */
    protected $exercicios;
    /**
     * @var float|int
     */
    protected $wValores;
    /**
     * @var float|int
     */
    protected $wTitulo;
    /**
     * Dados do planejamento
     * @var array
     */
    protected $planejamento = [];
    /**
     * Altura total dos valores com linha de tamanho 4
     * @var int
     */
    protected $alturaTotalValores;
    /**
     * @var int|void
     */
    protected $quantidadeExercicios;

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);

        $this->init(false);
        $this->AliasNbPages();
        $this->SetFillColor(235);
        $this->SetFont('Arial', '', 8);
    }

    public function headers($titulo)
    {
        $this->addTitulo($titulo);

        $subTitulo = $this->planejamento['pl2_titulo'];
        $this->addTitulo($subTitulo);
    }

    /**
     * Sobrescreve o período do relatório
     * @param $periodo
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;
    }

    public function setDados(array $dados)
    {
        $this->dados = $dados;

        if (isset($this->dados['planejamento'])) {
            $this->setDadosPlanejamento();
        }

        $this->alturaTotalValores = 4 * $this->quantidadeExercicios;
        $this->wValores = $this->wValor * $this->quantidadeExercicios;
        $this->wTitulo = $this->wLinha - $this->wValores;
    }
    protected function setDadosPlanejamento()
    {
        $planejamento = $this->dados['planejamento'];
        $this->planejamento = $planejamento;

        $this->periodo = sprintf('%s - %s', $planejamento['pl2_ano_inicial'], $planejamento['pl2_ano_final']);
        $this->exercicios = $planejamento['exercicios'];

        $this->quantidadeExercicios = count($planejamento['exercicios']);
        if (!$this->isPPA) {
            $this->quantidadeExercicios = 1;
        }
    }

    protected function capa($titulo)
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 28);

        $this->SetY($this->yCapa);
        $this->MultiCell($this->wCapa, 12, $titulo, 0, 'R');
        $this->Cell($this->wCapa, 12, $this->getTituloPlano(), 0, 1, 'R');
        $this->Cell($this->wCapa, 12, $this->periodo, 0, 1, 'R');
    }

    protected function identidadeOrganizacional()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', 18);
        $this->SetY(40);
        $this->MultiCell(190, 12, "Identidade Organizacional", 0, 'C');

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(190, 8, "Missão", 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(190, 4, $this->planejamento['missao'], 0, 'L');

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(190, 8, "Visão", 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(190, 4, $this->planejamento['visao'], 0, 'L');

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(190, 8, "Valores", 0, 1, 'L');
        $this->SetFont('Arial', '', 8);
        $this->MultiCell(190, 4, $this->planejamento['valores'], 0, 'L');
        $this->Ln(4);
    }
    protected function comissao()
    {
        $this->SetFont('Arial', 'B', 18);
        $this->MultiCell(190, 8, "Comissão", 0, 'C');
        $this->SetFont('Arial', '', 8);
        foreach ($this->planejamento['comissao'] as $nome) {
            $this->Cell(190, 6, $nome, 0, 1, 'L');
        }
    }
    protected function tituloCabecalho($titulo)
    {
        $this->SetFont('Arial', 'B', 8);
        $this->Cell($this->wLinha, 5, $titulo, 1, 1, 'L', 1);
        $this->SetFont('Arial', '', 8);
    }
    protected function getTituloPlano()
    {
        if (!isset($this->planejamento['pl2_tipo'])) {
            throw new Exception("Erro ao Buscar o Nome do Plano Selecionado.");
        }
        switch ($this->planejamento['pl2_tipo']) {
            case 'LDO':
                return 'LEI DE DIRETRIZES ORÇAMENTÁRIAS';
            case 'LOA':
                return 'LEI ORÇAMENTÁRIA ANUAL';
            case 'PPA':
            default:
                return 'PLANO PLURIANUAL';
        }
    }

    protected function headerOrigemEmentario()
    {
        $origemEmentario = 'Ementário do e-Cidade';
        if ($this->dados['ementario'] === 'uniao') {
            $origemEmentario = 'Ementário da união/federação';
        }
        if ($this->dados['ementario'] === 'estadual') {
            $origemEmentario = 'Ementário estadual/regional';
        }
        $this->addTitulo($origemEmentario);
    }

    /**
     * adiciona negrito na fonte
     */
    public function bold()
    {
        $this->SetFont('', 'B');
    }

    /**
     * tira o negrito da fonte
     */
    public function regular()
    {
        $this->SetFont('', '');
    }

    protected function imprimeAssinaturas()
    {
        if (!empty($this->dados['instituicaoEmissora'])) {
            $this->ln(30);
            assinaturasFinanceiro($this, $this->dados['instituicaoEmissora'], 'BG');
        }
    }

    /**
     * Retorna a altura da linha de acordo com o numero de linhas, valores impressos e a altura da linha que por
     * default é 4
     *
     * @param $numerLinhas
     * @param int $alturaLinha
     * @return mixed
     */
    protected function calculaAlturaLinha($numerLinhas, $alturaLinha = 4)
    {
        $alturaDescricao = $numerLinhas * $alturaLinha;
        return max([$alturaDescricao, $this->alturaTotalValores]);
    }
}
