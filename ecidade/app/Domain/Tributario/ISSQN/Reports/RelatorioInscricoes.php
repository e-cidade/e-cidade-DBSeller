<?php

namespace App\Domain\Tributario\ISSQN\Reports;

use App\Domain\Tributario\ISSQN\Model\Redesim\InscricaoRedesim;

class RelatorioInscricoes extends \GenericPdf
{
    /**
     * @var string
     */
    private $descricaoRelatorio = "INSCRIÇÕES GERADAS A PARTIR DA REDESIM";

    /**
     * @var InscricaoRedesim[]
     */
    private $inscricoesRedesim;

    /**
     * @var string
     */
    protected $dataInicio;

    /**
     * @var string
     */
    protected $dataFim;

    /**
     * @param string $dataInicio
     */
    public function setDataInicio($dataInicio)
    {
        $this->dataInicio = $dataInicio;
    }

    /**
     * @param string $dataFim
     */
    public function setDataFim($dataFim)
    {
        $this->dataFim = $dataFim;
    }

    /**
     * @param InscricaoRedesim[] $inscricoesRedesim
     * @return RelatorioInscricoes
     */
    protected function setInscricoes($inscricoesRedesim)
    {
        $this->inscricoesRedesim = $inscricoesRedesim;
        return $this;
    }

    protected function gerar()
    {
        $this->globalVariables();
        $this->headerFile();
        $this->bodyFile();

        $this->generate();
    }

    private function globalVariables()
    {
        global $head2;
        $head2 = $this->descricaoRelatorio;

        global $head3;
        $head3 = sprintf(
            "Período: %s até %s",
            db_formatar($this->dataInicio, 'd'),
            db_formatar($this->dataFim, 'd')
        );
    }

    private function headerFile()
    {
        $this->Open();
        $this->AliasNbPages();
        $this->addpage("L");
        $this->setfillcolor(235);
        $this->setfont('arial', 'B', 9);

        $this->setfont('arial', 'B', 10);
        $this->setY(40);
        $this->setX(10);
        $this->Cell(279, 6, $this->descricaoRelatorio, 1, 0, "C", 1);
    }

    private function bodyFile()
    {
        $this->headerTable(4);
        $this->bodyTable(5);
    }

    private function headerTable($height)
    {
        $this->setfont('arial', 'B', 9);

        $this->setY(50);
        $this->setX(10);
        $this->Cell(35, $height, "Número da Inscrição", 1, 0, "C");

        $this->setY(50);
        $this->setX(45);
        $this->Cell(33, $height, "CNPJ", 1, 0, "C");

        $this->setY(50);
        $this->setX(78);
        $this->Cell(171, $height, "Nome / Razão Social", 1, 0, "C");

        $this->setY(50);
        $this->setX(249);
        $this->Cell(40, $height, "Data de inicio da empresa", 1, 0, "C");

        $this->setY(54);
        $this->setX(10);
        $this->Cell(139, $height, "Endereço", 1, 0, "C");

        $this->setY(54);
        $this->setX(149);
        $this->Cell(140, $height, "Atividade Principal", 1, 0, "C");
    }

    private function bodyTable($height)
    {
        $this->setfont('arial', '', 9);

        $altura = 62;
        $total  = 0;
        $num    = 1;

        foreach ($this->inscricoesRedesim as $oInscricaoRedesim) {
            $this->setY($altura);
            $this->setX(10);
            $this->Cell(35, $height, $oInscricaoRedesim->getInscricao(), 1, 0, "C");

            $this->setY($altura);
            $this->setX(45);
            $this->Cell(33, $height, db_formatar($oInscricaoRedesim->issBase->cgm->z01_cgccpf, "cnpj"), 1, 0, "C");

            $this->setY($altura);
            $this->setX(78);
            $this->Cell(171, $height, substr($oInscricaoRedesim->issBase->cgm->z01_nome, 0, 25), 1, 0, "L");

            $this->setY($altura);
            $this->setX(249);
            $this->Cell(40, $height, \DBDate::converter($oInscricaoRedesim->issBase->getDtinic()), 1, 0, "C");

            $this->setY($altura + 5);
            $this->setX(10);
            $sTipoRua = trim($oInscricaoRedesim->j88_descricao);
            $sNomeRua = trim($oInscricaoRedesim->j14_nome);
            $sNumeroRua = trim($oInscricaoRedesim->q02_numero);
            $sNomeBairro = trim($oInscricaoRedesim->j13_descr);
            $sRua = "{$sTipoRua} {$sNomeRua}, {$sNumeroRua}, {$sNomeBairro}";
            $this->Cell(139, $height, substr($sRua, 0, 65), 1, 0, "L");

            $this->setY($altura + 5);
            $this->setX(149);
            $this->Cell(
                140,
                $height,
                substr("{$oInscricaoRedesim->q03_ativ} - {$oInscricaoRedesim->q03_descr}", 0, 70),
                1,
                0,
                "L"
            );

            $altura += $height + 9;
            $num++;
            $total++;
            
            if ($num >= 9 && $total < count($this->inscricoesRedesim)) {
                $this->AddPage('L');
                $altura = 38;
                $num = -1;
            }
        }
    }
}
