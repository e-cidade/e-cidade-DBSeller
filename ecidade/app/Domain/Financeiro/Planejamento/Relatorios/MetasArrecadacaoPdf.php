<?php

namespace App\Domain\Financeiro\Planejamento\Relatorios;

class MetasArrecadacaoPdf extends Pdf
{
    protected $wLinha = 279;

    protected $wValor = 23;

    protected $fonte = 7;

    protected $alturaLinha = 5;

    protected $yCapa = 105;
    protected $wCapa = 270;

    protected $porReceita = true;
    protected $porBimestre = true;
    /**
     * @var float|int
     */
    protected $wReceita;
    /**
     * @var float|int
     */
    protected $wDescricaoReceita;
    /**
     * @var string
     */
    protected $tipoAgrupador = '';

    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
    }

    public function setDados(array $dados)
    {
        parent::setDados($dados);

        $this->porBimestre = $this->dados['filtros']['periodicidade'] === 'bimestral';
        $this->porReceita = $this->dados['filtros']['agruparPor'] === 'receita';
        $this->tipoAgrupador = $this->dados['filtros']['agruparPor'];
    }

    public function emitir()
    {
        $this->headers('METAS DE ARRECADAÇÃO DA RECEITA');
        $this->capa('METAS DE ARRECADAÇÃO DA RECEITA');

        $this->imprimeMetas();
        $this->imprimeFonteNotas();

        $filename = sprintf('tmp/meta-arrecadacao-%s.pdf', time());
        $this->Output('F', $filename, false);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }


    public function headers($titulo)
    {
        parent::headers($titulo);

        $this->addTitulo('Art. 13, da Lei Complementar 101/2000');
        $recurso = "Agrupado por: Recurso";
        if ($this->porReceita) {
            $recurso = "Agrupado por: Receita";
        }
        $this->addTitulo($recurso);

        $periodicidade = "Periodicidade: Mensal";
        if ($this->porBimestre) {
            $periodicidade = "Periodicidade: Bimestral";
        }
        $this->addTitulo($periodicidade);

        if ($this->dados['filtros']['filtrouRecurso']) {
            $this->addTitulo("Filtrou recurso");
        }
        $this->headerOrigemEmentario();

        $this->wValores = $this->wValor * 7;
        $this->wReceita = $this->wLinha - $this->wValores;
        $this->wDescricaoReceita = $this->wReceita - $this->wValor;
    }

    protected function imprimeMetas()
    {
        if ($this->porBimestre) {
            $this->imprimeModeloBimestral();
        } else {
            $this->imprimeModeloMensal();
        }

        $this->imprimeTotalizador();
    }

    protected function imprimeModeloBimestral()
    {
        $this->cabecalhoBimestral();
        foreach ($this->dados['dados'] as $dado) {
            if ($this->getAvailHeight() < $this->alturaLinha) {
                $this->cabecalhoBimestral();
            }
            $this->imprimeLinhaDescricao($dado);
            $this->imprimeLinhaValor($dado->bimestre_1);
            $this->imprimeLinhaValor($dado->bimestre_2);
            $this->imprimeLinhaValor($dado->bimestre_3);
            $this->imprimeLinhaValor($dado->bimestre_4);
            $this->imprimeLinhaValor($dado->bimestre_5);
            $this->imprimeLinhaValor($dado->bimestre_6);
            $this->imprimeLinhaValor($dado->valor, 'BTL', $this->alturaLinha, 1);
        }
    }

    protected function imprimeModeloMensal()
    {
        $this->cabecalhoMensal();
        $h = $this->alturaLinha * 2;
        foreach ($this->dados['dados'] as $dado) {
            if ($this->getAvailHeight() < $h) {
                $this->cabecalhoMensal();
            }

            $yInicial = $this->GetY();
            $this->imprimeLinhaDescricao($dado, $h);
            $wReceita = $this->wLinha - $this->wValores;

            $this->imprimeLinhaValor($dado->janeiro);
            $this->imprimeLinhaValor($dado->fevereiro);
            $this->imprimeLinhaValor($dado->marco);
            $this->imprimeLinhaValor($dado->abril);
            $this->imprimeLinhaValor($dado->maio);
            $this->imprimeLinhaValor($dado->junho, 1, $this->alturaLinha, 1);
            $this->SetX(10 + $wReceita);
            $this->imprimeLinhaValor($dado->julho);
            $this->imprimeLinhaValor($dado->agosto);
            $this->imprimeLinhaValor($dado->setembro);
            $this->imprimeLinhaValor($dado->outubro);
            $this->imprimeLinhaValor($dado->novembro);
            $this->imprimeLinhaValor($dado->dezembro);

            $this->SetXY(($this->wLinha - $this->wValor) + 10, $yInicial);
            $this->imprimeLinhaValor($dado->valor, 'TBL', $h, 1);
        }
    }

    /**
     * @param integer $w tamanho do campo no multicell
     * @param integer $h altura linha
     * @param integer $x2 tamanho para re-setar o eixo X e tb usado para desenhar a linha
     * @param string $txt texto a ser impresso
     * @param integer $wWordwarp tamanho usado para cortar a string
     * @return void
     */
    protected function imprimeDescricaoValidandoQuebraLinha($w, $h, $x2, $txt, $wWordwarp)
    {
        $yAntes = $this->getY();
        $yLinhaBotton = $this->getY() + $h;

        $descricao = wordwrap($txt, $wWordwarp, "\n");

        $linhas = explode("\n", $descricao);
        if (count($linhas) > 2) {
            $descricao = "$linhas[0]\n$linhas[1]...";
        }

        if ($this->porBimestre && count($linhas) > 1) {
            $descricao = "$linhas[0]...";
        }

        $this->multiCell($w, 5, $descricao, '', 'L');

        $this->line(10, $yAntes, $x2, $yAntes);
        $this->line(10, $yLinhaBotton, $x2, $yLinhaBotton);
        $this->setXY($x2, $yAntes);
    }

    /**
     * @param \stdClass $dado
     * @param integer $hLinha
     */
    protected function imprimeLinhaDescricao($dado, $hLinha = 5)
    {
        if ($this->porReceita) {
            $this->Cell($this->wValor, $hLinha, $dado->estrutural, 'TBR', 0, 'C');

            $x2 = $this->wValor + $this->wDescricaoReceita + 10;
            $this->imprimeDescricaoValidandoQuebraLinha($this->wDescricaoReceita, $hLinha, $x2, $dado->natureza, 79);
        } else {
            $descricao = sprintf('%s - %s', $dado->fonte_recurso, $dado->descricao_recurso);
            if ($this->tipoAgrupador === 'recurso') {
                $descricao = sprintf(
                    '%s - %s - %s',
                    $dado->fonte_recurso,
                    str_pad($dado->complemento, 4, '0', STR_PAD_LEFT),
                    $dado->descricao_recurso
                );
            }

            $x2 = $this->wReceita + 10;
            $this->imprimeDescricaoValidandoQuebraLinha($this->wReceita, $hLinha, $x2, $descricao, 95);
        }
    }

    protected function imprimeLinhaValor($valor, $b = 1, $hLinha = 5, $ln = 0)
    {
        $this->cellAdapt($this->fonte, $this->wValor, $hLinha, formataValorMonetario($valor), $b, $ln, 'R');
    }

    protected function primeiraLinhaCabecalho($periodicidade)
    {
        $label = $this->porReceita ? 'Receita' : 'Recurso';

        $this->Cell($this->wReceita, $this->alturaLinha, $label, 'TBR', 0, 'C');
        $this->Cell($this->wValores, $this->alturaLinha, $periodicidade, 'TBL', 1, 'C');
    }

    protected function cabecalhoBimestral()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', $this->fonte);
        $this->primeiraLinhaCabecalho('Bimestral');

        $this->descricaoCabecalho($this->alturaLinha);
        $this->imprimeBimestres();
        $this->Cell($this->wValor, $this->alturaLinha, 'Total', 'TBL', 1, 'C');

        $this->SetFont('Arial', '', $this->fonte);
    }

    protected function cabecalhoMensal()
    {
        $this->AddPage();
        $this->SetFont('Arial', 'B', $this->fonte);
        $this->primeiraLinhaCabecalho('Mensal');
        $yInicial = $this->GetY();
        $h = $this->alturaLinha * 2;

        $this->descricaoCabecalho($h);
        $this->imprimeMeses();

        $this->SetXY(($this->wLinha - $this->wValor) + 10, $yInicial);
        $this->Cell($this->wValor, $h, 'Total', 'TBL', 1, 'C');

        $this->SetFont('Arial', '', $this->fonte);
    }

    protected function descricaoCabecalho($hLinha)
    {
        if ($this->porReceita) {
            $this->Cell($this->wValor, $hLinha, 'Estrutural', 'TBR', 0, 'C');
            $this->Cell($this->wDescricaoReceita, $hLinha, 'Descrição', 1, 0, 'C');
        } else {
            $label = '';
            if ($this->tipoAgrupador === 'recurso') {
                $label = 'Recurso | Complemento';
            }
            $this->Cell($this->wReceita, $hLinha, $label, 'TBR', 0, 'C');
        }
    }

    protected function imprimeBimestres()
    {
        $this->Cell($this->wValor, $this->alturaLinha, '1º Bimestre', 1, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, '2º Bimestre', 1, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, '3º Bimestre', 1, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, '4º Bimestre', 1, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, '5º Bimestre', 1, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, '6º Bimestre', 1, 'C');
    }

    protected function imprimeMeses()
    {
        $wReceita = $this->wLinha - $this->wValores;
        $this->Cell($this->wValor, $this->alturaLinha, 'Janeiro', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Fevereiro', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Março', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Abril', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Maio', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Junho', 1, 1, 'C');

        $this->SetX(10 + $wReceita);

        $this->Cell($this->wValor, $this->alturaLinha, 'Julho', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Agosto', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Setembro', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Outubro', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Novembro', 1, 0, 'C');
        $this->Cell($this->wValor, $this->alturaLinha, 'Dezembro', 1, 1, 'C');
    }

    protected function imprimeFonteNotas()
    {
        if (!empty($this->dados['fonte'])) {
            $this->imprimeTexto($this->dados['fonte'], $this->fonte);
        }

        if (!empty($this->dados['notaExplicativa'])) {
            $this->imprimeTexto($this->dados['notaExplicativa'], $this->fonte);
        }
    }

    protected function imprimeTexto($texto, $fonteSize)
    {
        $this->SetFont('Arial', '', $fonteSize);
        $linhas = $this->NbLines($this->wLinha, $texto);
        if ($this->getAvailHeight() < ($this->alturaLinha * $linhas)) {
            $this->AddPage();
        }
        $this->MultiCell($this->wLinha, $this->alturaLinha, $texto);
    }

    protected function imprimeTotalizador()
    {
        $h = $this->porBimestre ? $this->alturaLinha : $this->alturaLinha * 2;
        if ($this->getAvailHeight() < $h) {
            $this->AddPage();
        }

        $this->SetFont('Arial', 'B', $this->fonte);

        if ($this->porBimestre) {
            $this->imprimeTotalizadorBimestral();
        } else {
            $this->imprimeTotalizadorMensal();
        }
        $this->SetFont('Arial', '', $this->fonte);
    }

    protected function imprimeTotalizadorBimestral()
    {
        $this->Cell($this->wReceita, $this->alturaLinha, 'Total Geral', 'TBR', 0, 'L');
        $this->imprimeLinhaValor($this->dados['totalizador']->bimestre_1);
        $this->imprimeLinhaValor($this->dados['totalizador']->bimestre_2);
        $this->imprimeLinhaValor($this->dados['totalizador']->bimestre_3);
        $this->imprimeLinhaValor($this->dados['totalizador']->bimestre_4);
        $this->imprimeLinhaValor($this->dados['totalizador']->bimestre_5);
        $this->imprimeLinhaValor($this->dados['totalizador']->bimestre_6);
        $this->imprimeLinhaValor($this->dados['totalizador']->valor, 'BTL', $this->alturaLinha, 1);
    }

    protected function imprimeTotalizadorMensal()
    {
        $h = $this->alturaLinha * 2;
        $yInicial = $this->getY();
        $this->Cell($this->wReceita, $h, 'Total Geral', 'TBR', 0, 'L');
        $this->imprimeLinhaValor($this->dados['totalizador']->janeiro);
        $this->imprimeLinhaValor($this->dados['totalizador']->fevereiro);
        $this->imprimeLinhaValor($this->dados['totalizador']->marco);
        $this->imprimeLinhaValor($this->dados['totalizador']->abril);
        $this->imprimeLinhaValor($this->dados['totalizador']->maio);
        $this->imprimeLinhaValor($this->dados['totalizador']->junho, 1, $this->alturaLinha, 1);
        $this->SetX(10 + $this->wReceita);
        $this->imprimeLinhaValor($this->dados['totalizador']->julho);
        $this->imprimeLinhaValor($this->dados['totalizador']->agosto);
        $this->imprimeLinhaValor($this->dados['totalizador']->setembro);
        $this->imprimeLinhaValor($this->dados['totalizador']->outubro);
        $this->imprimeLinhaValor($this->dados['totalizador']->novembro);
        $this->imprimeLinhaValor($this->dados['totalizador']->dezembro);

        $this->SetXY(($this->wLinha - $this->wValor) + 10, $yInicial);
        $this->imprimeLinhaValor($this->dados['totalizador']->valor, 'TBL', $h, 1);
    }
}
