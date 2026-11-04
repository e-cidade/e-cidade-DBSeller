<?php


namespace App\Domain\Patrimonial\Licitacoes\Relatorios;

use App\Domain\Financeiro\Contabilidade\Relatorios\Pdf;
use stdClass;
use DBDate;

class ItensBloqueados extends Pdf
{
    /**
     * Dados para impressão
     * @var array
     */
    public $dados;
    protected $licitacao;
    protected $registroPreco;
    protected $modalidade;
    public $movimentacao;
    /**
     * @var float
     */
    protected $wValor = 30.4;

    public function headers()
    {
        $this->addTitulo('RELATÓRIO DE ITENS BLOQUEADOS');
        $this->addTitulo("LICITAÇÃO: {$this->licitacao}");
        $this->addTitulo("MODALIDADE: {$this->modalidade}");
        $this->addTitulo("COMPILAÇÂO: {$this->registroPreco}");
    }

    public function setLicitacao($licitacao)
    {
        $this->licitacao = $licitacao;
    }

    public function setRegistroPreco($registroPreco)
    {
        $this->registroPreco = $registroPreco;
    }

    public function setModalidade($modalidade)
    {
        $this->modalidade = $modalidade;
    }

    public function setMovimentacao($movimentacao)
    {
        $this->movimentacao = $movimentacao;
    }

    public function setDados($dados)
    {
        $this->dados = $dados;
    }

    /**
     * @return array
     */
    public function emitir()
    {
        $this->init(false);
        $this->SetMargins(8, 8, 8);
        $this->headers();
        $this->imprimeCabecalho();
        $this->imprimeDados($this->dados);
        $filename = sprintf('tmp/itens-bloqueados-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    /**
     * Imprime o cabeçalho do relatório
     */
    public function imprimeCabecalho()
    {
        $this->AddPage();
    }

    /**
     * @param $itens
     */
    public function imprimeDados($movimentacoes)
    {
        $fill = true;
        if (empty($movimentacoes)) {
            $this->Cell(60, 5, 'Nenhum Item Bloqueado', '', 0, 'L', $fill);
        } else {
            foreach ($movimentacoes as $itens) {
                $this->SetFillColor(205);
                $this->SetFont('Arial', 'B', 8);
                $this->setY($this->GetY() + 5);
                $this->MultiCell(195, 5, 'MOVIMENTAÇÃO: ' . $this->movimentacao, 'L T R', 'L', 1);
                $this->MultiCell(195, 5, 'JUSTIFICATIVA: '. utf8_decode($itens[0]['justificativa']), 'L B R', 'L', 1);

                $this->Cell(20, 5, 'CODIGO', 1, 0, 'C', 1);
                $this->Cell(65, 5, 'ITEM', 1, 0, 'C', 1);
                $this->Cell(20, 5, 'QTD', 1, 0, 'C', 1);
                $this->Cell(45, 5, 'UNIDADE', 1, 0, 'C', 1);
                $this->Cell(45, 5, 'PERÍODO', 1, 1, 'C', 1);

                $this->regular();

                foreach ($itens as $item) {
                    $this->SetFillColor(235);
                    $fill = !$fill;
                    $linhas = "";
                    $codigo = $item['codigomaterial'];
                    $mateiral = $item['descricaomaterial'];
                    $quantidade = $item['quantidadematerial'];
                    $unidade = $item['unidadematerial'];
                    $dataI = new DBDate($item['datainicial']);
                    $dataF = new DBDate($item['datafinal']);
                    $periodo = $dataI . ' à ' . $dataF;
                    $resumo = !empty($item['resumomaterial']) ? $item['resumomaterial'] : '';

                    $alturaLinha = 5;
                    $this->Cell(20, $alturaLinha, $codigo, 'L', 0, 'C', $fill);
                    $this->Cell(65, $alturaLinha, $mateiral, '', 0, 'C', $fill);
                    $this->Cell(20, $alturaLinha, $quantidade, '', 0, 'C', $fill);
                    $this->Cell(45, $alturaLinha, $unidade, '', 0, 'C', $fill);
                    $this->Cell(45, $alturaLinha, $periodo, 'R', 1, 'C', $fill);
                    $this->MultiCell(195, 5, $resumo, 'L R B', 'L', $fill);
                }
            }
        }
    }
}
