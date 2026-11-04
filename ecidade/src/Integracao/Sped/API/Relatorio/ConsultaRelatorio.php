<?php

namespace ECidade\Integracao\Sped\API\Relatorio;

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use PDFDocument;
use JSON;

class ConsultaRelatorio
{
    const ALTURA_LINHA = 4;

    const LARGURA_PDF = 190;

    const TAMANHO_FONTE = 6;

    /**
     * @var PDFDocument
     */
    private $pdf;

    /**
     * @var float
     */
    private $larguraLinha;

    /**
     * @var float
     */
    private $alturaLinha;

    /**
     * @var array
     */
    private $dados;

    /**
     * @var string
     */
    private $caminhoArquivo;

    /**
     * @var \CgmBase
     */
    private $cgmResponsavel;

    /**
     * @var \stdClass
     */
    private $parametros;

    /**
     * @var array
     */
    private $totalizadores = [
        'imprime' => false,
        'vrPerRef' => ['titulo' => '', 'valor' => 0],
        'vrCpSeg' => ['titulo' => '', 'valor' => 0],
        'valor' => ['titulo' => '', 'valor' => 0],
        'dpsFGTS' => ['titulo' => '', 'valor' => 0],
        'vrDescSeg' => ['titulo' => '', 'valor' => 0],
        'vrCsSegTerc' => ['titulo' => '', 'valor' => 0],
        'vrDescTerc' => ['titulo' => '', 'valor' => 0],
        'vrDedDep' => ['titulo' => '', 'valor' => 0],
        'vrIrrfDesc' => ['titulo' => '', 'valor' => 0],
        'remFGTS' => ['titulo' => '', 'valor' => 0],
        'remFGTSE' => ['titulo' => '', 'valor' => 0],
        'vrDescCP' => ['titulo' => '', 'valor' => 0],
        'vrBcCp00' => ['titulo' => '', 'valor' => 0],
        'vrBcCp15' => ['titulo' => '', 'valor' => 0],
        'vrBcCp20' => ['titulo' => '', 'valor' => 0],
        'vrBcCp25' => ['titulo' => '', 'valor' => 0],
        'vrSuspBcCp00' => ['titulo' => '', 'valor' => 0],
        'vrSuspBcCp15' => ['titulo' => '', 'valor' => 0],
        'vrSuspBcCp20' => ['titulo' => '', 'valor' => 0],
        'vrSuspBcCp25' => ['titulo' => '', 'valor' => 0],
        'vrDescSest' => ['titulo' => '', 'valor' => 0],
        'vrCalcSest' => ['titulo' => '', 'valor' => 0],
        'vrDescSenat' => ['titulo' => '', 'valor' => 0],
        'vrCalcSenat' => ['titulo' => '', 'valor' => 0],
        'vrSalFam' => ['titulo' => '', 'valor' => 0],
        'vrSalMat' => ['titulo' => '', 'valor' => 0],
        'vrBcCp13' => ['titulo' => '', 'valor' => 0],
        'vrBcFgts' => ['titulo' => '', 'valor' => 0],
        'vlrAquis' => ['titulo' => '', 'valor' => 0],
        'vrCPDescPR' => ['titulo' => '', 'valor' => 0],
        'vrCPNRet' => ['titulo' => '', 'valor' => 0],
        'vrRatNRet' => ['titulo' => '', 'valor' => 0],
        'vrSenarNRet' => ['titulo' => '', 'valor' => 0],
        'vrCPCalcPR' => ['titulo' => '', 'valor' => 0],
        'vrRatDescPR' => ['titulo' => '', 'valor' => 0],
        'vrRatCalcPR' => ['titulo' => '', 'valor' => 0],
        'vrSenarDesc' => ['titulo' => '', 'valor' => 0],
        'vrSenarCalc' => ['titulo' => '', 'valor' => 0],
        'vrBcComPR' => ['titulo' => '', 'valor' => 0],
        'vrCPSusp' => ['titulo' => '', 'valor' => 0],
        'vrRatSusp' => ['titulo' => '', 'valor' => 0],
        'vrSenarSusp' => ['titulo' => '', 'valor' => 0],
        'vrCR' => ['titulo' => '', 'valor' => 0],
        'vrSuspCR' => ['titulo' => '', 'valor' => 0],
        'baseFGTS' => ['titulo' => '', 'valor' => 0],
        'baseFGTSE' => ['titulo' => '', 'valor' => 0],
        'vrFGTS' => ['titulo' => '', 'valor' => 0],
        'vrFGTSE' => ['titulo' => '', 'valor' => 0]
    ];

    /**
     * ConsultaRelatorio constructor.
     * @param array $dados
     * @param \CgmBase $cgmResponsavel
     * @param \stdClass $parametros
     */
    public function __construct(array $dados, \CgmBase $cgmResponsavel, \stdClass $parametros)
    {
        $this->dados = $dados;
        $this->cgmResponsavel = $cgmResponsavel;
        $this->parametros = $parametros;
    }

    /**
     * @throws \Exception
     */
    private function validaParametros()
    {
        if (empty($this->parametros->integracao)) {
            throw new \Exception('É necessário informar o tipo de integração.');
        }

        if (empty($this->parametros->competencia)) {
            throw new \Exception('É necessário informar a competência.');
        }
    }

    private function inicializaPdf()
    {
        $this->pdf = new \PDFDocument();
        $this->pdf->Open();
        $this->pdf->SetAutoPageBreak(true, 15);
        $this->pdf->AliasNbPages();
        $this->pdf->SetFillColor(235);

        $tipoResponsavel = 'Empregador:';

        if ($this->parametros->integracao == Tipo::EFD_REINF) {
            $tipoResponsavel = 'Contribuinte:';
        }

        $this->pdf->addHeaderDescription(
            $tipoResponsavel . ' ' . ucwords(strtolower($this->cgmResponsavel->getNomeCompleto()))
        );
        if (!empty($this->parametros->evento) && !empty($this->parametros->layout)) {
            $this->pdf->addHeaderDescription(
                sprintf(
                    'Retorno: %s referente ao evento %s',
                    $this->parametros->layout,
                    $this->parametros->evento
                )
            );
        }
        $this->pdf->addHeaderDescription('Competência: ' . $this->parametros->competencia);
        $this->pdf->addHeaderDescription('Quantidade de Registros: ' . sizeof($this->dados));
        $this->pdf->addHeaderDescription(
            'Data de fechamento: ' . date_format(date_create($this->parametros->dt), "d/m/Y")
        );
        $this->pdf->SetFont('arial', '', static::TAMANHO_FONTE);
        $this->pdf->AddPage();

        $this->alturaLinha = static::ALTURA_LINHA;
        $this->larguraLinha = static::LARGURA_PDF;
    }

    final private function montarLinha($largura, $valor = '', $quebra = false, $preenche = false, $alinhamento = 'L')
    {
        $borda = 'LRBT';
        $linhasOcupadas = $this->pdf->NbLines($largura, $valor);

        if (static::ALTURA_LINHA * 4 > $this->pdf->getAvailHeight()) {
            $this->pdf->AddPage();
        }

        if ($linhasOcupadas > 1) {
            $y = $this->pdf->GetY();
            $this->pdf->MultiCell($largura, static::ALTURA_LINHA, $valor, $borda, $alinhamento, $preenche);
            $x = 10 + $largura;
            $this->pdf->SetXY($x, $y);

            $this->alturaLinha = static::ALTURA_LINHA * $linhasOcupadas;
        } else {
            $this->pdf->Cell($largura, $this->alturaLinha, $valor, $borda, $quebra, $alinhamento, $preenche);
        }

        if ($quebra) {
            $this->alturaLinha = static::ALTURA_LINHA;
        }

        return $this;
    }

    private function imprimeGrupo($grupo)
    {
        $this->montarLinha(static::LARGURA_PDF, $grupo->description, true, true);
        foreach ($grupo->children as $indice => $filhos) {
            if (is_object($filhos)) {
                $this->imprimeFilho($filhos);
            } else {
                foreach ($filhos as $filho) {
                    $this->imprimeFilho($filho);
                }
                if ($indice < (sizeof($grupo->children) - 1)) {
                    $this->montarLinha(static::LARGURA_PDF, '', true, true);
                }
            }
        }
        $this->pdf->Ln();
    }

    private function imprimeFilho($filho)
    {
        if (!empty($this->totalizadores[$filho->name])) {
            $this->totalizadores['imprime'] = true;
            $this->totalizadores[$filho->name]['titulo'] = "Total {$filho->description}";
            $this->totalizadores[$filho->name]['valor'] += $filho->value;
            $filho->value = db_formatar($filho->value, 'f');
        }
        $this->montarLinha(static::LARGURA_PDF / 2, $filho->description);
        $this->montarLinha(static::LARGURA_PDF / 2, $filho->value, true, false, 'R');
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function montaRelatorio()
    {
        $this->caminhoArquivo = 'tmp/retorno_sped_' . time() . '.pdf';
        foreach ($this->dados as $grupos) {
            foreach ($grupos as $grupo) {
                if (isset($grupo->children)) {
                    $this->imprimeGrupo($grupo);
                }
            }
        }

        // Imprimimos os totalizadores, caso existam
        if ($this->totalizadores['imprime']) {
            unset($this->totalizadores['imprime']);
            $this->montarLinha(static::LARGURA_PDF, "Totalizadores", true, true);
            foreach ($this->totalizadores as $totalizador) {
                if (!empty($totalizador['titulo'])) {
                    $this->montarLinha(static::LARGURA_PDF / 2, $totalizador['titulo']);
                    $this->montarLinha(
                        static::LARGURA_PDF / 2,
                        db_formatar($totalizador['valor'], 'f'),
                        true,
                        false,
                        'R'
                    );
                }
            }
        }

        $this->pdf->Output($this->caminhoArquivo, false, true);

        if (!file_exists($this->caminhoArquivo)) {
            throw new \Exception("Erro ao gerar o relatório.\nContate o suporte.");
        }

        return $this->caminhoArquivo;
    }


    public function montarCSV()
    {
        $this->caminhoArquivo = 'tmp/retorno_sped_' . time() . '.csv';

        foreach ($this->dados as $dados) {
            foreach ($dados as $dado) {
                $info[] = $dado->description;

                foreach ($dado->children as $dadoChildren) {
                    $children = [];
                    $children[] = $dadoChildren->description;
                    $children[] = $dadoChildren->value;
                    $info[] = $children;
                }
            }

            $info[] = "\n";
        }
        file_put_contents($this->caminhoArquivo, '');

        foreach ($info as $key => &$value) {
            if (is_array($value)) {
                $value = implode(';', $value);
            }
            file_put_contents($this->caminhoArquivo, $value . PHP_EOL, FILE_APPEND);
        }

        return $this->caminhoArquivo;
    }


    public function gerarCSV()
    {
        return $this->montarCSV();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function gerar()
    {
        $this->validaParametros();
        $this->inicializaPdf();
        return $this->montaRelatorio();
    }
}
