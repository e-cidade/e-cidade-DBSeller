<?php

namespace App\Domain\Tributario\ISSQN\Services\Base;

use App\Domain\Tributario\ISSQN\Model\Base\AtivPrinc;
use App\Domain\Tributario\ISSQN\Model\Base\CertBaixaNumero;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\ParIssqn;
use App\Domain\Tributario\ISSQN\Model\Base\TabAtiv;
use App\Domain\Tributario\ISSQN\Model\Base\TabativBaixa;
use Illuminate\Support\Arr;

class AtividadeService
{
    /**
     * @throws \Throwable
     */
    public function save(IssBase $issBase, $isMainActivity, $data)
    {
        $tabAtiv = new TabAtiv();
        $tabAtiv->q07_inscr = $issBase->q02_inscr;
        $tabAtiv->q07_seq = $this->nextSequence($issBase);
        $tabAtiv->q07_ativ = $data->q07_ativ;
        $tabAtiv->q07_datain = isset($data->q07_datain) ? $data->q07_datain : null;
        $tabAtiv->q07_datafi = isset($data->q07_datafi) ? $data->q07_datafi : null;
        $tabAtiv->q07_databx = isset($data->q07_databx) ? $data->q07_databx : null;
        $tabAtiv->q07_quant = isset($data->q07_quant) ? $data->q07_quant : null;
        $tabAtiv->q07_tipbx = isset($data->q07_tipbx) ? $data->q07_tipbx : null;
        $tabAtiv->q07_perman = isset($data->q07_perman) ? $data->q07_perman : null;
        $tabAtiv->q07_horaini = isset($data->q07_horaini) ? $data->q07_horaini : null;
        $tabAtiv->q07_horafim = isset($data->q07_horafim) ? $data->q07_horafim : null;
        $tabAtiv->q07_val_ativ_int = isset($data->q07_val_ativ_int) ? $data->q07_val_ativ_int : null;
        $tabAtiv->q07_imprimealvara = isset($data->q07_imprimealvara) ? $data->q07_imprimealvara : null;
        $tabAtiv->saveOrFail();

        if ($isMainActivity) {
            $this->saveAsMainActivity($tabAtiv);
        }

        return $tabAtiv;
    }

    /**
     * @throws \Throwable
     */
    public function update(TabAtiv $tabAtiv, $data)
    {
        $tabAtivData = Arr::get($data, "tabAtiv");

        if (!$tabAtivData) {
            throw new \Exception("Dados para atualizar a tabativ não informados.");
        }

        TabAtiv::where("q07_inscr", $tabAtiv->q07_inscr)->where("q07_seq", $tabAtiv->q07_seq)->update($tabAtivData);

        if (Arr::get($tabAtivData, "q07_databx")) {
            $ativPrinc = AtivPrinc::where("q88_inscr", $tabAtiv->q07_inscr)
                                  ->where("q88_seq", $tabAtiv->q07_seq)
                                  ->first();

            if ($ativPrinc) {
                $this->deleteAsMainActivity($ativPrinc);
            }

            $this->saveTabAtivBaixa($tabAtiv, Arr::get($data, "tabAtivBaixa"));
        }

        return $tabAtiv;
    }

    /**
     * @throws \Throwable
     */
    public function buildCertificateNumber($processId)
    {
        $parIssqn = ParIssqn::first();

        if ($parIssqn->q60_tiponumcertbaixa == 1) {
            if (!$processId) {
                throw new \Exception("Identificador do processo não informado.");
            }

            return $processId;
        } else {
            $currentYear = db_getsession("DB_anousu");
            $certBaixaNumero = CertBaixaNumero::where("q79_anousu", $currentYear)->first();
            $certBaixaNumero->q79_ultcodcertbaixa = $certBaixaNumero->q79_ultcodcertbaixa + 1;
            $certBaixaNumero->saveOrFail();

            if ($parIssqn->q60_tiponumcertbaixa == 3) {
                return "{$certBaixaNumero->q79_ultcodcertbaixa}/{$certBaixaNumero->q79_anousu}";
            } else {
                return $certBaixaNumero->q79_ultcodcertbaixa;
            }
        }
    }

    /**
     * @throws \Throwable
     */
    private function saveAsMainActivity(TabAtiv $tabAtiv)
    {
        $ativPrinc = new AtivPrinc();
        $ativPrinc->q88_inscr = $tabAtiv->q07_inscr;
        $ativPrinc->q88_seq = $tabAtiv->q07_seq;
        $ativPrinc->saveOrFail();

        return $ativPrinc;
    }

    /**
     * @throws \Exception
     */
    private function deleteAsMainActivity(AtivPrinc $ativPrinc)
    {
        AtivPrinc::where("q88_inscr", $ativPrinc->q88_inscr)
                 ->where("q88_seq", $ativPrinc->q88_seq)
                 ->delete();
    }

    private function nextSequence(IssBase $issBase)
    {
        $tabAtiv = TabAtiv::where("q07_inscr", $issBase->q02_inscr)->orderBy("q07_seq", "desc")->first();

        if ($tabAtiv) {
            return $tabAtiv->q07_seq + 1;
        }

        return 1;
    }

    /**
     * @throws \Throwable
     */
    private function saveTabAtivBaixa(TabAtiv $tabAtiv, $data)
    {
        if (!$data) {
            throw new \Exception("Dados para salvar a tabativbaixa não informados.");
        }

        $tabativBaixa = new TabativBaixa();
        $tabativBaixa->q11_inscr = $tabAtiv->q07_inscr;
        $tabativBaixa->q11_seq = $tabAtiv->q07_seq;
        $tabativBaixa->q11_processo = isset($data["q11_processo"]) ? $data["q11_processo"] : null;
        $tabativBaixa->q11_oficio = isset($data["q11_oficio"]) ? $data["q11_oficio"] : null;
        $tabativBaixa->q11_obs = isset($data["q11_obs"]) ? $data["q11_obs"] : null;
        $tabativBaixa->q11_login = isset($data["q11_login"]) ? $data["q11_login"] : null;
        $tabativBaixa->q11_data = isset($data["q11_data"]) ? $data["q11_data"] : null;
        $tabativBaixa->q11_hora = isset($data["q11_hora"]) ? $data["q11_hora"] : null;
        $tabativBaixa->q11_numero = $data["q11_numero"];
        $tabativBaixa->saveOrFail();
    }
}
