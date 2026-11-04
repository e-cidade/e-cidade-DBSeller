<?php

namespace App\Domain\Tributario\ISSQN\Services\Redesim\Action;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\ISSQN\Model\Base\IssAlvara;
use App\Domain\Tributario\ISSQN\Model\Base\Isscadsimples;
use App\Domain\Tributario\ISSQN\Model\Base\Isscadsimplesbaixa;
use App\Domain\Tributario\ISSQN\Model\Base\ParIssqn;
use App\Domain\Tributario\ISSQN\Model\Base\Socios;
use App\Domain\Tributario\ISSQN\Model\Base\TabAtiv;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\AlterarInscricao\RedesimDadosAtividadeParser;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\AlterarInscricao\RedesimDadosEmpresaParser;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\AlterarInscricao\RedesimDadosSocioParser;
use App\Domain\Tributario\ISSQN\Services\AlvaraService;
use App\Domain\Tributario\ISSQN\Services\Base\AtividadeService;
use App\Domain\Tributario\ISSQN\Services\Base\IssBairroService;
use App\Domain\Tributario\ISSQN\Services\Base\IsscadsimplesService;
use App\Domain\Tributario\ISSQN\Services\Base\IssRuasService;
use App\Domain\Tributario\ISSQN\Services\Base\SociosService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RedesimAlterarInscricaoService extends RedesimActionBaseService
{
    /**
     * @var SociosService
     */
    private $sociosService;

    /**
     * @var AtividadeService
     */
    private $atividadeService;

    /**
     * @var IssRuasService
     */
    private $issRuasService;

    /**
     * @var IssBairroService
     */
    private $issBairroService;

    /**
     * @var IsscadsimplesService
     */
    private $isscadsimplesService;

    /**
     * @var AlvaraService
     */
    private $alvaraService;

    public function __construct(
        SociosService $sociosService,
        AtividadeService $atividadeService,
        IssRuasService $issRuasService,
        IssBairroService $issBairroService,
        IsscadsimplesService $isscadsimplesService,
        AlvaraService $alvaraService
    ) {
        $this->sociosService = $sociosService;
        $this->atividadeService = $atividadeService;
        $this->issRuasService = $issRuasService;
        $this->issBairroService = $issBairroService;
        $this->isscadsimplesService = $isscadsimplesService;
        $this->alvaraService = $alvaraService;
    }

    /**
     * @throws \Exception
     */
    public function changeEstablishmentEmail()
    {
        $emailUpdated = $this->issBase->cgm->update(
            RedesimDadosEmpresaParser::buildChangeEmailInfo($this->establishmentData)
        );

        if (!$emailUpdated) {
            throw new \Exception("Erro ao atualizar o e-mail do estabelecimento.");
        }
    }

    /**
     * @throws \Exception
     */
    public function changeFantasyName()
    {
        $fantasyNameUpdated = $this->issBase->cgm->update(
            RedesimDadosEmpresaParser::buildChangeFantasyNameInfo($this->establishmentData)
        );

        if (!$fantasyNameUpdated) {
            throw new \Exception("Erro ao atualizar o nome fantasia do estabelecimento.");
        }
    }

    /**
     * @throws \Exception
     */
    public function changePartnerCapitalStock()
    {
        $partnerList = RedesimDadosSocioParser::buildChangeCapitalStockInfo($this->establishmentData);

        foreach ($partnerList as $partnerInfo) {
            if ($partnerInfo->data["q95_perc"] == null) {
                continue;
            }

            $partner = Socios::whereHas("cgm", function (Builder $builder) use ($partnerInfo) {
                $builder->where("z01_cgccpf", $partnerInfo->cpfCnpj);
            })->where("q95_cgmpri", $this->issBase->q02_numcgm)->first();

            if (!$partner) {
                throw new \Exception("Sócio não encontrado para o CPF/CNPJ: {$partnerInfo->cpfCnpj}");
            }

            $capitalStockUpdated = $partner->where("q95_numcgm", $partner->q95_numcgm)
                                           ->where("q95_cgmpri", $partner->q95_cgmpri)
                                           ->update($partnerInfo->data);

            if (!$capitalStockUpdated) {
                throw new \Exception("Erro ao atualizado o capital social para o sócio de CGM: {$partner->q95_numcgm}");
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function changeEstablishmentName()
    {
        $establishmentNameUpdated = $this->issBase->cgm->update(
            RedesimDadosEmpresaParser::buildChangeEstablishmentNameInfo($this->establishmentData)
        );

        if (!$establishmentNameUpdated) {
            throw new \Exception("Erro ao atualizar o nome do estabelecimento.");
        }
    }

    /**
     * @throws \Throwable
     */
    public function addNewPartner()
    {
        $partnerList = RedesimDadosSocioParser::buildPartnerInfoList($this->establishmentData);

        foreach ($partnerList as $partner) {
            $cgm = Cgm::firstOrCreate(["z01_cgccpf" => $partner->cpfCnpj], $partner->personData);

            $this->sociosService->save($this->issBase, $cgm, $partner->data);
        }
    }

    /**
     * @throws \Throwable
     */
    public function changePartnerInfo()
    {
        $partnerList = RedesimDadosSocioParser::buildPartnerInfoList($this->establishmentData);

        foreach ($partnerList as $partnerInfo) {
            $cgm = Cgm::where("z01_cgccpf", $partnerInfo->cpfCnpj)->first();

            if (!$cgm) {
                throw new \Exception("CGM não encontrado para o CPF/CNPJ: {$partnerInfo->cpfCnpj}");
            }

            $cgmUpdated = $cgm->update((array) $partnerInfo->personData);

            if (!$cgmUpdated) {
                throw new \Exception("Não foi possível atualizar o CGM {$cgm->z01_numcgm}");
            }

            $partner = Socios::where("q95_cgmpri", $this->issBase->q02_numcgm)
                             ->where("q95_numcgm", $cgm->z01_numcgm)
                             ->first();

            if (!$partner) {
                throw new \Exception("Sócio não encontrado.");
            }

            $this->sociosService->update($partner, (array) $partnerInfo->data);
        }
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function baixa($processRecalculation)
    {
        $currentDate = date('Y-m-d', db_getsession("DB_datausu"));
        $currentYear = db_getsession("DB_anousu");

        $canExecuteWithOpenDebitsCode = 1;
        $canExecuteWithOpenDebits = ParIssqn::where("q60_alvbaixadiv", "=", $canExecuteWithOpenDebitsCode)->first();

        if (!$canExecuteWithOpenDebits && $this->hasOpenDebits($currentDate)) {
            throw new \Exception("Esta inscrição não pode ser baixada pois existem débitos em aberto.");
        }

        $activities = TabAtiv::where("q07_inscr", $this->issBase->q02_inscr)->whereNull("q07_databx")->get();

        if ($processRecalculation) {
            $activitiesSequences = $activities->map(function (TabAtiv $tabAtiv) {
                return $tabAtiv->q07_seq;
            });

            $recalculationResponse = fc_issqn(
                $this->issBase->q02_inscr,
                $currentDate,
                $currentDate,
                $currentYear,
                db_getsession('DB_instit'),
                $activitiesSequences->implode(","),
                "true",
                "false"
            );

            if ($recalculationResponse->codigo != "01" && $recalculationResponse->codigo != "24") {
                throw new \Exception("Não foi possível recalcular o alvará.");
            }
        }

        $this->atividadeBaixa($activities);

        $this->issBase->q02_dtbaix = $currentDate;
        $this->issBase->saveOrFail();

        $this->baixaAlvara($currentDate);

        $this->deleteArrecadBaixa();
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function changeAddress()
    {
        $this->issRuasService->update(
            $this->issBase,
            RedesimDadosEmpresaParser::buildIssRuas($this->establishmentData)
        );

        $this->issBairroService->update(
            $this->issBase,
            RedesimDadosEmpresaParser::buildIssBairro($this->establishmentData)
        );

        $cgmAddressUpdated = $this->issBase->cgm->update(
            RedesimDadosEmpresaParser::buildChangeCgmAddress($this->establishmentData)
        );

        if (!$cgmAddressUpdated) {
            throw new \Exception("Erro ao atualizar o endereço do CGM.");
        }
    }

    /**
     * @throws \Exception
     */
    public function changePhone()
    {
        $phoneUpdated = $this->issBase->cgm->update(
            RedesimDadosEmpresaParser::buildChangePhoneInfo($this->establishmentData)
        );

        if (!$phoneUpdated) {
            throw new \Exception("Erro ao atualizar o telefone do estabelecimento.");
        }
    }

    /**
     * @throws \Throwable
     */
    public function changeSimplesNacional()
    {
        $infoList = RedesimDadosEmpresaParser::buildChangeSimplesNacional($this->issBase, $this->establishmentData);

        foreach ($infoList as $info) {
            $isscadsimples = Isscadsimples::query()
                ->where("q38_inscr", $this->issBase->q02_inscr)
                ->where("q38_dtinicial", $info->startDateInfo->q38_dtinicial)->first();

            if (!$isscadsimples) {
                $isscadsimples = $this->isscadsimplesService->save($this->issBase, $info->startDateInfo);
            }

            if ($info->endDateInfo) {
                $isscadsimplesbaixa = Isscadsimplesbaixa::query()
                    ->where("q39_isscadsimples", $isscadsimples->q38_sequencial)
                    ->where("q39_dtbaixa", $info->endDateInfo->q39_dtbaixa)
                    ->first();

                if (!$isscadsimplesbaixa) {
                    $this->isscadsimplesService->baixa($isscadsimples, $info->endDateInfo);
                }
            }
        }
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function changeActivities()
    {
        $activitiesInfo = RedesimDadosAtividadeParser::buildActivities($this->establishmentData);

        $savedActivities = TabAtiv::where("q07_inscr", $this->issBase->q02_inscr)
                             ->whereNull("q07_databx")
                             ->get(["q07_ativ"]);

        $newActivities = array_map(function ($activityInfo) {
            return $activityInfo->data->q07_ativ;
        }, $activitiesInfo);

        $savedActivities = $savedActivities->map(function (TabAtiv $tabAtiv) {
            return $tabAtiv->q07_ativ;
        });

        $removedActivities = $savedActivities->diff($newActivities);

        $activitiesToLow = TabAtiv::where("q07_inscr", $this->issBase->q02_inscr)
                                  ->whereIn("q07_ativ", $removedActivities->toArray())
                                  ->get();

        $this->atividadeBaixa($activitiesToLow);

        $addedActivities = array_diff($newActivities, $savedActivities->toArray());

        foreach ($addedActivities as $activityCode) {
            $activityInfo = $activitiesInfo[$activityCode];

            $this->atividadeService->save($this->issBase, $activityInfo->isMainActivity, $activityInfo->data);
        }
    }

    private function hasOpenDebits($date)
    {
        $hasDebits = DB::query()
                       ->from("arreinscr")
                       ->join("issbase", "issbase.q02_inscr", "arreinscr.k00_inscr")
                       ->join("cgm", "cgm.z01_numcgm", "issbase.q02_numcgm")
                       ->join("arrecad", "arrecad.k00_numpre", "arreinscr.k00_numpre")
                       ->join("arretipo", "arrecad.k00_tipo", "arretipo.k00_tipo")
                       ->where("k00_inscr", $this->issBase->q02_inscr)
                       ->where("k00_dtvenc", "<", $date)
                       ->whereNotIn("k03_tipo", [2, 9, 19])
                       ->first(["arrecad.k00_numpre"]);

        if ($hasDebits) {
            return true;
        }

        return false;
    }

    /**
     * @throws \DBException
     * @throws \Exception
     */
    private function baixaAlvara($date)
    {
        $issalvara = IssAlvara::query()
                       ->join("issmovalvara", "issmovalvara.q120_issalvara", "issalvara.q123_sequencial")
                       ->where("q123_inscr", $this->issBase->q02_inscr)
                       ->where("q120_isstipomovalvara", 1)
                       ->first();

        if (!$issalvara) {
            return;
        }

        $user = Usuario::where("id_usuario", db_getsession("DB_id_usuario"))->first();
        $this->alvaraService->baixa(
            $issalvara,
            $user,
            $this->processedEstablishment->process,
            $date,
            0,
            1,
            "Baixa a partir de processo da REDESIM."
        );
    }

    private function deleteArrecadBaixa()
    {
        $dueDate = Carbon::createFromFormat('Y-m-d', $this->issBase->q02_dtbaix)
                         ->addMonth(1)
                         ->firstOfMonth()
                         ->format("Y-m-d");

        $currentYear = db_getsession("DB_anousu");

        $debitList = DB::query()
                       ->from("arrecad")
                       ->join("arreinscr", "arreinscr.k00_numpre", "arrecad.k00_numpre")
                       ->where("arreinscr.k00_inscr", $this->issBase->q02_inscr)
                       ->where("arrecad.k00_dtvenc", $dueDate)
                       ->whereExists(function (\Illuminate\Database\Query\Builder $builder) use ($currentYear) {
                            $builder->select(DB::raw(1))
                                    ->from("isscalc")
                                    ->whereColumn("isscalc.q01_numpre", "arrecad.k00_numpre")
                                    ->whereColumn("isscalc.q01_recei", "arrecad.k00_receit")
                                    ->whereIn("isscalc.q01_cadcal", [2, 3]) /* ISSQN Fixo e Variavel */
                                    ->where("isscalc.q01_anousu", $currentYear);
                       })
                       ->orderBy("arrecad.k00_numpre")
                       ->orderBy("arrecad.k00_numpar")
                       ->orderBy("arrecad.k00_receit")
                       ->get();

        $debitList->each(function ($data, $index) {
            if ($index == 0) {
                return true;
            }

            DB::query()
                ->from("arrecad")
                ->where("k00_numpre", $data->k00_numpre)
                ->where("k00_numpar", $data->k00_numpar)
                ->where("k00_receit", $data->k00_receit)
                ->delete();
        });
    }

    /**
     * @param $activityList TabAtiv[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Throwable
     */
    private function atividadeBaixa($activityList)
    {
        $certificateNumber = $this->atividadeService->buildCertificateNumber(
            $this->processedEstablishment->q190_process_id
        );

        $logbaixaalvara = new \logbaixaalvara();

        $activityList->each(function (TabAtiv $tabAtiv) use ($certificateNumber, $logbaixaalvara) {
            $this->atividadeService->update(
                $tabAtiv,
                RedesimDadosAtividadeParser::buildBaixa($this->processedEstablishment, $certificateNumber)
            );

            $logbaixaalvara->identificaAlteracao($tabAtiv->q07_inscr, 1, 6, $tabAtiv->q07_ativ);
        });

        $logbaixaalvara->gravarLog();
    }
}
