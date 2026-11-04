<?php

namespace App\Domain\Tributario\ISSQN\Reports\Alvara;

use App\Domain\Tributario\ISSQN\Interfaces\Reports\AlvaraInterface;
use App\Domain\Tributario\ISSQN\Model\Base\IssBairro;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssRuas;
use App\Domain\Tributario\ISSQN\Model\Base\TabAtiv;
use db_impcarne;
use scpdf;

class PreviouslyPrintedAlvara extends AlvaraBaseReport implements AlvaraInterface
{
    public function __construct(IssBase $issBase)
    {
        parent::__construct($issBase);
    }

    /**
     * @throws \Exception
     */
    public function generate()
    {
        require_once(modification("fpdf151/scpdf.php"));
        require_once(modification("fpdf151/impcarne.php"));

        $tabAtiv = $this->getMainActivityInfo();
        $issRuas = IssRuas::where("q02_inscr", $this->issBase->q02_inscr)->first();
        $issBairro = IssBairro::where("q13_inscr", $this->issBase->q02_inscr)->first();

        $spdf1 = new scpdf("P", "mm", [290, 95]);
        $spdf1->Open();
        $pdf2 = new db_impcarne($spdf1, '26');
        $pdf2->objpdf->AddPage();
        $pdf2->objpdf->SetTextColor(0, 0, 0);

        $pdf2->nrinscr = $this->issBase->q02_inscr;
        $pdf2->dtiniativ = $tabAtiv->q07_datain;
        $pdf2->dtinic = $this->issBase->q02_dtinic;
        $pdf2->ativ = $tabAtiv->q07_ativ;
        $pdf2->nome = $this->issBase->cgm->z01_nome;
        $pdf2->fantasia = $this->issBase->cgm->z01_nomefanta;
        $pdf2->ender = trim($issRuas->rua->j14_nome);
        $pdf2->cnpjcpf = $this->issBase->cgm->z01_numcgm;
        $pdf2->descrativ = $tabAtiv->q03_descr;
        $pdf2->numero = $issRuas->q02_numero;
        $pdf2->compl = $issRuas->q02_compl;
        $pdf2->bairropri = $issBairro->bairro->j13_descr;

        $pdf2->imprime();
        $pdf2->objpdf->Output($this->getFilePath(), false, true);
    }

    /**
     * @throws \Exception
     */
    private function getMainActivityInfo()
    {
        $tabAtiv = TabAtiv::join("ativid", "ativid.q03_ativ", "tabativ.q07_ativ")
                          ->join("ativprinc", function ($join) {
                              $join->on("tabativ.q07_inscr", "ativprinc.q88_inscr");
                              $join->on("tabativ.q07_seq", "ativprinc.q88_seq");
                          })
                          ->where("q07_inscr", $this->issBase->q02_inscr)
                          ->first(["q07_datain", "q07_ativ", "q03_descr"]);

        if (!$tabAtiv) {
            throw new \Exception("Atividade principal não encontrada.");
        }

        return $tabAtiv;
    }
}
