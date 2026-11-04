<?php
// phpcs:ignoreFile
namespace App\Domain\Financeiro\Contabilidade\Relatorios\Deliberacao;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;

class ModeloCincoPdf extends \ECidade\Pdf\Pdf
{
    const FINANCEIRO = 1;
    const CONTABILIDADE = 4;
    private $wLinha = 280;
    private $hLinha = 4;

    private $modelo = 5;

    private $tipoImpresso = '';

    private $alturaInicialQuadros = 20;
    private $alturaAssinaturas = 165;

    /**
     * Filtros usados na emissão
     * @var array
     */
    private $filtros = [];

    private $dadosMde = [];
    private $dadosFundeb = [];

    /**
     * @var \stdClass
     */
    private $responsavelArea;
    /**
     * @var \stdClass
     */
    private $responsavelContabilidade;

    protected $declaracaoAssinatura = "[   ] Declaro que os valores acima descritos guardam paridade com o constante nos registros contábeis\n[   ] Declaro que os valores acima não guardam paridade com o constante nos registros contábeis, conforme apontado em Notas Explicativas.";
    /**
     * @var DBConfig
     */
    private $instituicao;

    public function __construct($orientation = 'L', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        $this->init(false,
            false,
            false,
            false,
            false
        );
        $this->SetAutoPageBreak(false, 10);
        $this->AliasNbPages();
        $this->SetFillColor(235);
        $this->setFont('Arial', '', 7);
    }

    public function setFiltros(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function emitir()
    {
        $this->imprimir();

        $filename = sprintf('tmp/modelo-5-%s.pdf', time());
        $this->Output('F', $filename);
        return [
            'pdf' => $filename,
            'pdfLinkExterno' => ECIDADE_REQUEST_PATH . $filename
        ];
    }

    public function setDadosMde(array $dadosMde)
    {
        $this->dadosMde = $dadosMde;
    }

    public function setDadosFundeb(array $dadosFundeb)
    {
        $this->dadosFundeb = $dadosFundeb;
    }

    public function setInstituicao(DBConfig $instituicao)
    {
        $this->instituicao = $instituicao;
    }

    /**
     * Adiciona uma página nova e cria o rodapé
     * @return void
     */
    protected function novaPagina()
    {
        $this->addPage();
        $this->setFont('', 'B');
        $this->cell($this->wLinha, 4, 'TRIBUNAL DE CONTAS DO ESTADO DO RIO DE JANEIRO', 0, 1, 'C');
        $this->cell($this->wLinha, 4, 'PRESTAÇÃO DE CONTAS ANUAL DE GOVERNO DOS MUNICÍPIOS', 0, 1, 'C');
        $this->setFont('', '');

        $this->rodape();
    }

    private function imprimir()
    {
        $this->imprimirQuadros();
    }

    protected function rodape()
    {
        $this->setXY(10, $this->alturaAssinaturas);

        $this->assinaturaResponsavelSetor();

        $this->ln();

        $this->assinaturaResponsavelContabilidade();

        $this->ln(0.3);
        $this->cell(292, 4, "Página {$this->pageNo()} - {$this->modelo} da Deliberação TCE-RJ nº 285/18", 0, 1, 'C');
    }

    protected function assinaturaResponsavelSetor()
    {
        $responsavelSetor = 'Tesoureiro';

        $responsavelArea = $this->getResponsavelArea();

        $this->setFont("Arial", "", 8);
        $this->cell(10, 4, "Nome:", 'TBL', 0, "L");
        $this->cell(170, 4, "$responsavelArea->nome", 'TBR', 0, "L");
        $this->cell(100, 4, $responsavelSetor, 1, 1, "C");

        $this->cell(15, 8, "Matrícula:", 'TBL', 0, "L");
        $this->cell(135, 8, "$responsavelArea->matricula", 'TBR', 0, "L");

        $this->cell(10, 8, "Data:", 'TBL', 0, "R");
        $this->cell(20, 8, date('d/m/Y'), 'TBR', 0, "L");

        $this->cell(100, 8, "Assinatura:", 1, 0, "L");
    }

    protected function assinaturaResponsavelContabilidade()
    {
        $responsavelContabilidade = $this->getResponsavelContabilidade();
        $numeroLinhas = $this->NbLines(180, $this->declaracaoAssinatura);

        $documento = "CRC-RJ nº\n\n";
        $documento .= !empty($responsavelContabilidade->crc) ? $responsavelContabilidade->crc : str_repeat("_", 15);

        $yAntes = $this->GetY();


        $this->multiCell("180", 4, $this->declaracaoAssinatura, 1);


        $this->cell(10, 4, "Nome:", 'TBL', 0, "L");
        $this->cell(170, 4, "$responsavelContabilidade->nome", 'TBR', 0, "L");

        $this->SetXY(190, $yAntes);
        $numeroLinhas++;
        $this->cell(73, $numeroLinhas * 4, "Responsável pelo Setor Contábil", 1, 0, 'C');
        $this->SetXY(263, $yAntes);

        $documento = "CRC-RJ nº\n\n";
        $documento .= !empty($responsavelContabilidade->crc) ? $responsavelContabilidade->crc : str_repeat("_", 15);

        $this->multiCell(27, 4, $documento, 1, "C");
        $this->cell(15, 8, "Matrícula:", 'TBL', 0, "L");
        $this->cell(135, 8, "$responsavelContabilidade->matricula", 'TBR', 0, "L");

        $this->cell(10, 8, "Data:", 'TBL', 0, "R");
        $this->cell(20, 8, date('d/m/Y'), 'TBR', 0, "L");
        $this->cell(100, 8, "Assinatura:", 1, 1, "L");

        $this->setFont("Arial", '', 6);
    }

    protected function getResponsavelArea()
    {
        if (empty($this->responsavelArea)) {
            $this->responsavelArea = $this->getResponsavel(self::FINANCEIRO);
        }

        return $this->responsavelArea;
    }

    protected function getResponsavelContabilidade()
    {
        if (empty($this->responsavelContabilidade)) {
            $this->responsavelContabilidade = $this->getResponsavel(self::CONTABILIDADE);
        }
        return $this->responsavelContabilidade;
    }

    private function getResponsavel($area)
    {
        $where = [];
        $where[] = "area = {$area}";
        $where[] = "instituicao = {$this->filtros['instituicao']}";
        $where[] = "exercicio = {$this->filtros['exercicio']}";

        $sql = "
        select matricula, nome, crc
          from plugins.deliberacao_assinaturas
         where " . implode(' and ', $where);

        $rs = \db_query($sql);

        if (!$rs) {
            throw new \Exception("Erro ao buscar responsável");
        }

        $responsavel = \db_utils::fieldsMemory($rs, 0);
        $responsavel = empty($responsavel) ? '' : $responsavel;

        return $responsavel;
    }

    private function imprimirQuadros()
    {

        foreach ($this->filtros['tipoConta'] as $tipoConta) {
            if ($tipoConta === 'fundeb') {
                $this->imprimeFundeb();
            }
            if ($tipoConta === 'mde') {
                $this->imprimeMde();
            }
        }

    }

    private function imprimeFundeb()
    {
        $this->tipoImpresso = 'FUNDEB';
        $this->imprimeDadosDisponibilidade($this->dadosFundeb['disponibilidade']);
        $this->imprimeDebitos($this->dadosFundeb['debitos']);
        $this->imprimeCreditos($this->dadosFundeb['creditos']);
    }

    private function imprimeMde()
    {
        $this->tipoImpresso = 'MDE';
        $this->imprimeDadosDisponibilidade($this->dadosMde['disponibilidade']);
        $this->imprimeDebitos($this->dadosMde['debitos']);
        $this->imprimeCreditos($this->dadosMde['creditos']);
    }

    private function imprimeCabecalhoDisponibilidade()
    {
        $this->novaPagina();
        $this->imprimeCabecalhoQuadro('MODELO 5 - QUADRO AUXILIAR DAS DISPONIBILIDADES FINANCEIRAS');
        $this->setFont('Arial', 'B', 7);
        $this->cell(70, 16, "Banco", 1, 0, 'C');
        $this->cell(30, 16, "Conta Bancária", 1, 0, 'C');

        $x = 110;
        $yAntes = $this->GetY();
        $this->multiCell(30, 4, "\nSaldo em 31/12 conforme extrato (R$) (A)", 1, 'C');
        $x += 30;
        $this->SetXY($x, $yAntes);
        $this->cell(60, 4, "Débitos (R$) (B)", 1, 0, 'C');
        $this->cell(60, 4, "Créditos (R$) (C)", 1, 0, 'C');
        $this->multiCell(30, 4, "\nSaldo Contábil em 31/12\n(R$) (D = A + B - C)", 1, 'C');

        $yAntes = $yAntes + 4;
        $this->SetXY($x, $yAntes);
        $this->multiCell(30, 4, "\nAnteriores ao Exercício de Referência", 1, 'C');
        $x += 30;
        $this->SetXY($x, $yAntes);
        $this->multiCell(30, 4, "\nRelativos ao Exercício de Referência", 1, 'C');

        $x += 30;
        $this->SetXY($x, $yAntes);
        $this->multiCell(30, 4, "\nAnteriores ao Exercício de Referência", 1, 'C');
        $x += 30;
        $this->SetXY($x, $yAntes);
        $this->multiCell(30, 4, "\nRelativos ao Exercício de Referência", 1, 'C');

        $this->setFont('Arial', '', 7);
    }

    private function validaQuebraPagina($h = 4)
    {
        if (($this->getY() + $h) >= $this->alturaAssinaturas) {
            return true;
        }
        return false;
    }

    /**
     * Valida se tem que quebrar a página pela altura do quadro das assinaturas
     * @return void
     */
    private function quebraPaginaDisponibilidade()
    {
        if ($this->validaQuebraPagina()) {
            $this->imprimeCabecalhoDisponibilidade();
        }
    }

    private function imprimeDadosDisponibilidade($dados)
    {
        $this->imprimeCabecalhoDisponibilidade();

        $valorTotal = 0;
        foreach ($dados as $dado) {
            $this->quebraPaginaDisponibilidade();

            $dado->saldo_contabil = ($dado->saldo_31_12 + abs($dado->debito_ex_anterior) + abs($dado->debito_ex_atual)) - (abs($dado->credito_ex_anterior) + abs($dado->credito_ex_atual));
            $valorTotal += $dado->saldo_contabil;
            $this->cell(70, 4, $dado->banco, 1, 0);
            $this->cell(30, 4, $dado->agencia_conta, 1, 0);
            $this->cell(30, 4, formataValorMonetario($dado->saldo_31_12), 1, 0, 'R');
            $this->cell(30, 4, formataValorMonetario($dado->debito_ex_anterior), 1, 0, 'R');
            $this->cell(30, 4, formataValorMonetario($dado->debito_ex_atual), 1, 0, 'R');
            $this->cell(30, 4, formataValorMonetario($dado->credito_ex_anterior), 1, 0, 'R');
            $this->cell(30, 4, formataValorMonetario($dado->credito_ex_atual), 1, 0, 'R');
            $this->cell(30, 4, formataValorMonetario($dado->saldo_contabil), 1, 1, 'R');
        }

        $this->quebraPaginaDisponibilidade();
        $this->setFont('Arial', 'B', 7);
        $this->cell(250, 4, 'Total Bancos em 31/12 (I)', 1, 0);
        $this->cell(30, 4, number_format($valorTotal, 2, ',', '.'), 1, 1, 'R');
        $this->quebraPaginaDisponibilidade();
        $this->setFont('Arial', 'B', 7);
        $this->cell(250, 4, 'Total em Espécie em 31/12 (II)', 1, 0);
        $this->cell(30, 4, number_format(0, 2, ',', '.'), 1, 1, 'R');
        $this->quebraPaginaDisponibilidade();
        $this->setFont('Arial', 'B', 7);
        $this->cell(250, 4, 'Total das Disponibilidades Financeiras  (III = I + II)', 1, 0);
        $this->cell(30, 4, number_format($valorTotal, 2, ',', '.'), 1, 1, 'R');
    }

    private function imprimeCabecalhoQuadroDebitoCredito($titulo)
    {
        $this->novaPagina();
        $this->imprimeCabecalhoQuadro($titulo);
    }

    private function imprimeBanco($tituloQuadro, $banco)
    {
        $this->imprimeCabecalhoQuadroDebitoCredito($tituloQuadro);
        $this->setFont("Arial", "B", 7);
        $this->cell(10, 4, "Banco: ", "TBL", 0, "L");
        $this->setFont("Arial", "", 7);
        $this->cell(130, 4, $banco->banco, "TBR", 0, "L");

        $this->setFont("Arial", "B", 7);
        $this->cell(20, 4, "Conta Bancária: ", "TBL", 0, "L");
        $this->setFont("Arial", "", 7);
        $this->cell(120, 4, $banco->conta, "TBR", 1, "L");
    }

    private function imprimeCabecalhoBanco($tituloQuadro, $quadro, $banco)
    {
        if ($this->validaQuebraPagina(12)) {
            $this->imprimeBanco($tituloQuadro, $banco);
        }

        $labelHistorico = $banco->tipo === 'D' ? 'Débito' : 'Crédito';

        $this->setFont('Arial', 'B', 7);
        $this->cell(280, 4, "$quadro", 1, 1, 'C');

        $this->cell(22, 8, "Data do Evento", 1, 0, "C");
        $this->cell(69, 8, "Número do Documento", 1, 0, "C");
        $this->cell(69, 8, "Natureza/Histórico do " . $labelHistorico, 1, "C");
        $this->cell(30, 8, "Valor (R$)", 1, 0, 'C');
        $this->cell(65, 8, "Nota Explicativa para Regularização", 1, 0, 'C');
        $this->multiCell(25, 4, "Data da Regularização", 1, 'C');

        $this->setFont('Arial', '', 7);
    }

    private function imprimeValoresQuadroBanco($tituloQuadro, $quadro, $banco, $valores)
    {
        $total = 0;
        foreach ($valores as $dado) {
            if ($this->validaQuebraPagina(4)) {
                $this->imprimeBanco($tituloQuadro, $banco);
                $this->imprimeCabecalhoBanco($tituloQuadro, $quadro, $banco);
            }
            $total += $dado->valor;

            $yAntes = $this->GetY();
            $this->setFont('Arial', '', 7);
            $linhas = $this->NbLines(118, $dado->nota_explicativa);
            $alturaLinha = 4 * $linhas;
            $this->cell(22, $alturaLinha, db_formatar($dado->data_evento, 'd'), 1, 0, 'C');
            $this->cell(69, $alturaLinha, $dado->numero_documento, 1, 0, 'L');
            $this->cell(69, $alturaLinha, substr($dado->natureza, 0, 25), 1, 0, 'L');
            $this->cell(30, $alturaLinha, formataValorMonetario(abs($dado->valor)), 1, 0, 'R');
            $this->multiCell(65, 4, substr($dado->nota_explicativa, 0, 50), 1, 'L');
            $this->SetXY(265, $yAntes);
            $this->cell(25, $alturaLinha, $dado->data_regularizacao, 1, 1, 'C');
        }
        return $total;
    }

    private function imprimeDebitos($debitos)
    {
        $tituloQuadro = 'MODELO 5 - QUADRO I - DÉBITOS';
        $this->imprimeDadosBancarios($debitos, $tituloQuadro);
    }

    private function imprimeCreditos($creditos)
    {
        $tituloQuadro = 'MODELO 5 - QUADRO II - CRÉDITOS';
        $this->imprimeDadosBancarios($creditos, $tituloQuadro);
    }

    public function imprimeDadosBancarios($valores, $tituloQuadro)
    {
        $quadro1 = 'Anteriores ao Exercício de Referência';
        $quadro2 = 'Relativos ao Exercício de Referência';

        foreach ($valores as $banco) {
            $this->imprimeBanco($tituloQuadro, $banco);
            $this->imprimeCabecalhoBanco($tituloQuadro, $quadro1, $banco);

            $totalAnterior = $this->imprimeValoresQuadroBanco($tituloQuadro, $quadro1, $banco, $banco->anteriorExercicio);

            $this->setFont('Arial', 'B', 7);
            $this->cell(160, 4, 'Subtotal (A)', 1, 0, 'L');
            $this->cell(30, 4, formataValorMonetario($totalAnterior), 1, 0, 'R');
            $this->cell(90, 4, "", 1, 1, 'L', 1);
            $this->ln();

            $this->imprimeCabecalhoBanco($tituloQuadro, $quadro2, $banco);
            $totalExercicio = $this->imprimeValoresQuadroBanco($tituloQuadro, $quadro2, $banco, $banco->doExercicio);
            $this->setFont('Arial', 'B', 7);
            $this->cell(160, 4, 'Subtotal (B)', 1, 0, 'L');
            $this->cell(30, 4, formataValorMonetario($totalExercicio), 1, 0, 'R');
            $this->cell(90, 4, "", 1, 1, 'L', 1);

            $total = $totalAnterior + $totalExercicio;
            $this->cell(160, 4, 'Total (A+B)', 1, 0, 'L');
            $this->cell(30, 4, formataValorMonetario($total), 1, 0, 'R');
            $this->cell(90, 4, "", 1, 1, 'L', 1);
        }
    }

    private function imprimeCabecalhoQuadro($string)
    {
        $this->SetXY(10, $this->alturaInicialQuadros);
        $this->setFont('', 'B');
        $this->cell($this->wLinha, $this->hLinha, $string, 1, 1, 'C');
        $this->cell($this->wLinha, $this->hLinha, "Quadro referente ao {$this->tipoImpresso}", 1, 1, 'C');

        $this->setFont("Arial", "B", 7);
        $this->cell(10, 4, "Orgão: ", "TBL", 0, "L");
        $this->setFont("Arial", "", 7);
        $this->cell(105, 4, "{$this->instituicao->nomeinst}", "TBR", 0, "L");

        $this->setFont("Arial", "B", 7);
        $this->cell(15, 4, "Município:", "TBL", 0, "L");
        $this->setFont("Arial", "", 7);
        $this->cell(105, 4, "{$this->instituicao->munic}", "TBR", 0, "L");

        $this->setFont("Arial", "B", 7);
        $this->cell(15, 4, "Exercício:", "TBL", 0, "L");
        $this->setFont("Arial", "", 7);
        $this->cell(30, 4, "{$this->filtros['exercicio']}", "TBR", 1, "L");
        $this->setFont("Arial", "", 7);
        $this->ln();
    }

}
