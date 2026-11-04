<?php

namespace App\Domain\Tributario\ISSQN\Reports\Alvara;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Tributario\ISSQN\Interfaces\Reports\AlvaraInterface;
use App\Domain\Tributario\ISSQN\Model\Base\IssBairro;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssProcesso;
use App\Domain\Tributario\ISSQN\Model\Base\IssQuant;
use App\Domain\Tributario\ISSQN\Model\Base\IssRuas;
use App\Domain\Tributario\ISSQN\Model\Base\ParIssqn;
use App\Domain\Tributario\ISSQN\Model\Base\TabAtiv;
use db_impcarne;
use Illuminate\Database\Eloquent\Builder;
use scpdf;

class DefaultAlvara extends AlvaraBaseReport implements AlvaraInterface
{
    private $alvaraModelCode;

    public function __construct(IssBase $issBase, $alvaraModelCode)
    {
        parent::__construct($issBase);

        $this->alvaraModelCode = $alvaraModelCode;
    }

    /**
     * @throws \Exception
     */
    public function generate()
    {
        require_once(modification("fpdf151/scpdf.php"));
        require_once(modification("fpdf151/impcarne.php"));

        $scpdf = new scpdf();
        $scpdf->Open();

        $pdf = new db_impcarne($scpdf, $this->getTemplateCode());
        $pdf->objpdf->AddPage();
        $pdf->objpdf->SetTextColor(0, 0, 0);

        $pdf = $this->builfBody($pdf);

        $pdf->imprime();
        $pdf->objpdf->Output($this->getFilePath(), false, true);
    }

    /**
     * @throws \Exception
     */
    private function builfBody($pdf)
    {
        $pdf = $this->buildMainActivityInfo($pdf);
        $pdf = $this->buildSecondaryActivitiesInfo($pdf);

        $dbConfig = DBConfig::where("codigo", db_getsession("DB_instit"))->first();
        $pdf->prefeitura = $dbConfig->nomeinst;
        $pdf->municpref = strtoupper($dbConfig->munic);
        $pdf->nrinscr = $this->issBase->q02_inscr;

        $cgm = $this->issBase->cgm;
        $pdf->cgm = $cgm->z01_numcgm;
        $pdf->nome = $cgm->z01_nome;
        $pdf->nomecompl = $cgm->z01_nomecomple;
        $pdf->fantasia = $cgm->z01_nomefanta;
        $pdf->cnpjcpf = $cgm->z01_cgccpf;
        $pdf->icms = $cgm->z01_incest;
        $pdf->rg = $cgm->z01_ident;

        $issRuas = IssRuas::where("q02_inscr", $this->issBase->q02_inscr)->first();
        $pdf->ender = $issRuas->rua->j14_nome;
        $pdf->compl = $issRuas->q02_compl;
        $pdf->numero = $issRuas->q02_numero;

        $issBairro = IssBairro::where("q13_inscr", $this->issBase->q02_inscr)->first();
        $pdf->bairropri = $issBairro->bairro->j13_descr;

        $pdf->datainc = $this->issBase->q02_dtinic;
        $pdf->dtinic = $this->issBase->q02_dtinic;
        $pdf->datacad = $this->issBase->q02_dtcada;
        $pdf->q02memo = $this->issBase->q02_memo;
        $pdf->lancobs = $this->issBase->q02_memo;
        $pdf->q02_memo = substr($this->issBase->q02_memo, 0, 3500);
        $pdf->q02_memo .= " ...";

        $parIssqn = ParIssqn::first();
        $pdf->impdatas = $parIssqn->q60_impdatas;
        $pdf->impcodativ = $parIssqn->q60_impcodativ;
        $pdf->impobsativ = $parIssqn->q60_impobsativ;
        $pdf->impobslanc = $parIssqn->q60_impobsissqn;

        $issQuant = IssQuant::where("q30_inscr", $this->issBase->q02_inscr)
                            ->where("q30_anousu", db_getsession("DB_anousu"))
                            ->first();
        $pdf->areaterreno = $issQuant ? $issQuant->q30_area : null;

        $issProcesso = IssProcesso::where("q14_inscr", $this->issBase->q02_inscr)->first();
        $pdf->processo = $issProcesso ? $issProcesso->q14_proces : null;

        return $pdf;
    }

    /**
     * @throws \Exception
     */
    private function buildMainActivityInfo($pdf)
    {
        $mainActivityInfo = $this->getMainActivityInfo(true);

        if ($mainActivityInfo->q07_perman) {
            $paragrapfInfo = $this->getParagraphInfo(1034, false);

            if ($paragrapfInfo) {
                $alvaraTypeText = $paragrapfInfo->db02_texto;
            } else {
                $alvaraTypeText = "ALVARÁ DE LICENÇA PARA LOCALIZAÇÃO OU EXERCÍCIO DA ATIVIDADE";
            }

            $pdf->tipoalvara = $alvaraTypeText;
            $pdf->permanente = "t";

            $pdf = $this->buildSignature($pdf, 1010);
        } else {
            $pdf->tipoalvara = "LICENÇA PROVISÓRIA DE ATIVIDADE";
            $pdf->permanente = "f";

            $pdf = $this->buildSignature($pdf, 1011);
        }

        $mainActivityInfo = $this->getMainActivityInfo(false);

        $pdf->ativ = $mainActivityInfo->q07_ativ;
        $pdf->dtiniativ = $mainActivityInfo->q07_datain;
        $pdf->dtfimativ = $mainActivityInfo->q07_datafi;
        $pdf->q07horaini = trim($mainActivityInfo->q07_horaini);
        $pdf->q07horafim = trim($mainActivityInfo->q07_horafim);
        $pdf->obsativ = $mainActivityInfo->q03_atmemo;
        $pdf->descrativ = $mainActivityInfo->q03_descr;

        $pdf->iAtivPrincCnae = substr($mainActivityInfo->q71_estrutural, 1, strlen($mainActivityInfo->q71_estrutural));

        return $pdf;
    }

    private function buildSecondaryActivitiesInfo($pdf)
    {
        $tabAtivList = $this->getSecondaryActivitiesInfo();

        $pdf->q03_atmemo = [];
        $pdf->outrasativs = [];

        $tabAtivList->each(function ($tabAtiv, $key) use ($pdf) {
            $activityInfo = [
                "codativ" => $tabAtiv->q07_ativ,
                "descr" => $tabAtiv->q03_descr,
                "datain" => $tabAtiv->q07_datain,
                "datafi" => $tabAtiv->q07_datafi,
                "atv_perman" => $tabAtiv->q07_perman
            ];

            $pdf->outrasativs[$key] = $activityInfo;

            $q03_atmemo = str_replace("\n", "", $tabAtiv->q03_atmemo);
            $q03_atmemo = str_replace("\r", "", $q03_atmemo);
            $pdf->q03_atmemo[$tabAtiv->q07_ativ] = $q03_atmemo;

            $pdf->aCodigosCnae[] = substr($tabAtiv->q71_estrutural, 1, strlen($tabAtiv->q71_estrutural));
        });

        return $pdf;
    }

    /**
     * @throws \Exception
     */
    private function buildSignature($pdf, $documentTypeCode)
    {
        $signatureInfoList = $this->getParagraphInfo($documentTypeCode, true);

        $signatureInfoList->each(function ($signature) use ($pdf) {
            switch ($signature->db04_ordem) {
                case 1:
                    $pdf->texto = $signature->db02_texto;
                    break;
                case 2:
                    $pdf->obs = $signature->db02_texto;
                    break;
                case 3:
                    $pdf->assalvara = $signature->db02_texto;
                    break;
            }
        });

        return $pdf;
    }

    /**
     * @throws \Exception
     */
    private function getTemplateCode()
    {
        switch ($this->alvaraModelCode) {
            case AlvaraBaseReport::ALVARA_TAMANHO_A5:
                return 23;
            case AlvaraBaseReport::ALVARA_TAMANHO_A4:
                return 24;
            case AlvaraBaseReport::ALVARA_A4_FONTE_REDUZIDA:
                return 35;
            case AlvaraBaseReport::ALVARA_PRE_IMPRESSO_TAMANHO_A4:
                return 50;
            case AlvaraBaseReport::ALVARA_PRE_IMPRESSO_TAMANHO_A4_CNAE:
                return 59;
            case AlvaraBaseReport::ALVARA_A4_FRENTE_VERSO:
                return 63;
            case AlvaraBaseReport::ALVARA_TAMANHO_A4_PROCESSO_AREA:
                return 64;
            default:
                throw new \Exception("Modelo não implementado.");
        }
    }

    /**
     * @throws \Exception
     */
    private function getMainActivityInfo($considerLowActivities)
    {
        $query = $this->getBaseActivityQuery();
        $query->where("q07_inscr", $this->issBase->q02_inscr)->whereNotNull("q88_inscr");

        if ($considerLowActivities) {
            $query->where(function (Builder $builder) {
                $builder->orWhere("q07_databx", "<", date("Y-m-d", db_getsession("DB_datausu")));
                $builder->orWhereNull("q07_databx");
            });
        } else {
            $query->whereNull("q11_inscr");
        }

        $tabAtiv = $query->first();

        if (!$tabAtiv) {
            throw new \Exception("Atividade principal não encontrada.");
        }

        return $tabAtiv;
    }

    private function getSecondaryActivitiesInfo()
    {
        $query = $this->getBaseActivityQuery();
        $query->where("q07_inscr", $this->issBase->q02_inscr);
        $query->whereNull("q88_inscr");
        $query->where(function (Builder $builder) {
            $builder->orWhere("q07_databx", ">", date("Y-m-d", db_getsession("DB_datausu")));
            $builder->orWhereNull("q07_databx");
        });

        return $query->get();
    }

    /**
     * @throws \Exception
     */
    private function getParagraphInfo($documentTypeCode, $isSignature)
    {
        $query = DBQuery()
                   ->from("db_documento")
                   ->join("db_docparag", "db03_docum", "db04_docum")
                   ->join("db_tipodoc", "db08_codigo", "db03_tipodoc")
                   ->join("db_paragrafo", "db04_idparag", "db02_idparag")
                   ->where("db08_codigo", $documentTypeCode)
                   ->where("db03_instit", db_getsession('DB_instit'));

        if ($isSignature) {
            $query->where("db02_descr", "NOT ILIKE", "assinatura%");
            $query->orderBy("db04_ordem");

            $result = $query->get();

            if (!$result) {
                throw new \Exception("Configure o documento do alvara!");
            }
        } else {
            $query->orderBy("db02_descr");

            $result = $query->first();
        }

        return $result;
    }

    private function getBaseActivityQuery()
    {
        $query = TabAtiv::join("ativid", "ativid.q03_ativ", "tabativ.q07_ativ");
        $query->leftJoin("ativprinc", function ($join) {
            $join->on("ativprinc.q88_inscr", "tabativ.q07_inscr");
            $join->on("ativprinc.q88_seq", "tabativ.q07_seq");
        });
        $query->leftJoin("tabativbaixa", function ($join) {
            $join->on("tabativbaixa.q11_inscr", "tabativ.q07_inscr");
            $join->on("tabativbaixa.q11_seq", "tabativ.q07_seq");
        });
        $query->leftJoin("atividcnae", "q74_ativid", "q07_ativ");
        $query->leftJoin("cnaeanalitica", "q72_sequencial", "q74_cnaeanalitica");
        $query->leftJoin("cnae", "q71_sequencial", "q72_cnae");

        return $query;
    }
}
