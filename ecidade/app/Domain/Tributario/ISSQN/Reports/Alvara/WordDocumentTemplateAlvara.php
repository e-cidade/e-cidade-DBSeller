<?php

namespace App\Domain\Tributario\ISSQN\Reports\Alvara;

use App\Domain\Configuracao\DocumentosTemplate\GenericWordDocumentTemplate;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Tributario\ISSQN\Interfaces\Reports\AlvaraInterface;
use App\Domain\Tributario\ISSQN\Model\Base\IssAlvara;
use App\Domain\Tributario\ISSQN\Model\Base\IssBairro;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssBasePorte;
use App\Domain\Tributario\ISSQN\Model\Base\IssMovAlvara;
use App\Domain\Tributario\ISSQN\Model\Base\IssMovAlvaraProcesso;
use App\Domain\Tributario\ISSQN\Model\Base\IssProcesso;
use App\Domain\Tributario\ISSQN\Model\Base\IssQuant;
use App\Domain\Tributario\ISSQN\Model\Base\IssRuas;
use App\Domain\Tributario\ISSQN\Model\Base\TabAtiv;
use Carbon\Carbon;
use ECidade\Tributario\Cadastro\Repository\RuasTipoRepository;
use Illuminate\Support\Arr;
use MovimentacaoAlvara;

class WordDocumentTemplateAlvara extends AlvaraBaseReport implements AlvaraInterface
{
    /**
     * @var Carbon
     */
    private $currentDate;

    public function __construct(IssBase $issBase)
    {
        parent::__construct($issBase);

        $this->currentDate = Carbon::now();
    }

    /**
     * @throws \Exception
     */
    public function generate()
    {
        $documentType = 6;
        $documentCode = $this->getDocumentCode();

        $isLaravelApp = isset($_SESSION[\ECidade\Lib\Session\DefaultSession::DB_REQUEST_FROM_API]);

        $documentTemplate = new GenericWordDocumentTemplate($documentType, $documentCode, "", $isLaravelApp);
        $documentTemplate->setNomeDocumento($this->fileName);
        $documentTemplate->setValidate(false);

        $documentTemplate = $this->buildTemplateData($documentTemplate);
        $documentTemplate->processaTemplate();

        $this->fileName = $documentTemplate->getNomeDocumento();
    }

    /**
     * @param GenericWordDocumentTemplate $documentTemplate
     * @return GenericWordDocumentTemplate
     * @throws \Exception
     */
    private function buildTemplateData(GenericWordDocumentTemplate $documentTemplate)
    {
        $documentTemplate = $this->buildIssBaseData($documentTemplate);
        $documentTemplate = $this->buildCgmData($documentTemplate);
        $documentTemplate = $this->buildStoppageData($documentTemplate);
        $documentTemplate = $this->buildAddressData($documentTemplate);
        $documentTemplate = $this->buildBusinessLicenseData($documentTemplate);
        $documentTemplate = $this->buildCgmDefaultDocumentData($documentTemplate);
        $documentTemplate = $this->buildAccountingOfficeData($documentTemplate);
        $documentTemplate = $this->buildPendingDocumentsData($documentTemplate);
        $documentTemplate = $this->buildActivitiesData($documentTemplate);
        $documentTemplate = $this->buildQrCodeData($documentTemplate);

        return $documentTemplate;
    }

    /**
     * @throws \Exception
     */
    private function getDocumentCode()
    {
        $alvaraType = DBQuery()->from("issalvara")
                               ->join("isstipoalvara", "q98_sequencial", "q123_isstipoalvara")
                               ->where("q123_inscr", $this->issBase->q02_inscr)
                               ->first(["q98_documento"]);

        if (!$alvaraType) {
            throw new \Exception("Tipo de alvará não encontrado.");
        }

        return $alvaraType->q98_documento;
    }

    /**
     * @param GenericWordDocumentTemplate $documentTemplate
     * @return GenericWordDocumentTemplate
     */
    private function buildIssBaseData(GenericWordDocumentTemplate $documentTemplate)
    {
        $documentTemplate->setVariable("inscricao", $this->issBase->q02_inscr);
        $documentTemplate->setVariable("inscricao_antiga", $this->issBase->q02_inscmu);
        $documentTemplate->setVariable("numcgm", $this->issBase->q02_numcgm);
        $documentTemplate->setVariable("observacao", $this->issBase->q02_obs);
        $documentTemplate->setVariable("texto", $this->issBase->q02_memo);
        $documentTemplate->setVariable(
            "data_inicial",
            Carbon::createFromFormat(\DBDate::DATA_EN, $this->issBase->q02_dtinic)->format(\DBDate::DATA_PTBR)
        );
        $documentTemplate->setVariable(
            "data_cadastro",
            Carbon::createFromFormat(\DBDate::DATA_EN, $this->issBase->q02_dtcada)->format(\DBDate::DATA_PTBR)
        );

        $comapanySize = IssBasePorte::query()->from("issporte")
                                             ->join("issbaseporte", "q45_codporte", "q40_codporte")
                                             ->where("q45_inscr", $this->issBase->q02_inscr)
                                             ->first(["q40_descr"]);

        $companySizeDescription = $comapanySize ? $comapanySize->q40_descr : "";
        $documentTemplate->setVariable("porte", $companySizeDescription);

        /** @var IssQuant $issQuant */
        $issQuant = IssQuant::query()->where("q30_inscr", $this->issBase->q02_inscr)
                                     ->where("q30_anousu", $this->currentDate->year)
                                     ->first();

        $multiplier = "";
        $area = "";
        $advertisingArea = "";
        $staffCount = "";

        if ($issQuant) {
            $multiplier = $issQuant->q30_mult;
            $area = $issQuant->q30_area;
            $advertisingArea = $issQuant->q30_areapublicidade;
            $staffCount = $issQuant->q30_quant;
        }

        $documentTemplate->setVariable("mult", $multiplier);
        $documentTemplate->setVariable("area", $area);
        $documentTemplate->setVariable("areapublicidade", $advertisingArea);
        $documentTemplate->setVariable("quant_funcionarios", $staffCount);

        return $documentTemplate;
    }

    /**
     * @param GenericWordDocumentTemplate $documentTemplate
     * @return GenericWordDocumentTemplate
     */
    private function buildCgmData(GenericWordDocumentTemplate $documentTemplate)
    {
        $fullName = $this->issBase->cgm->z01_nomecomple;
        if (!$fullName) {
            $fullName = $this->issBase->cgm->z01_nome;
        }

        $documentTemplate->setVariable("nomecompleto", $fullName);
        $documentTemplate->setVariable("nome", $this->issBase->cgm->z01_nome);
        $documentTemplate->setVariable("nomefanta", $this->issBase->cgm->z01_nomefanta);
        $documentTemplate->setVariable("cnpj_cpf", formatCpfCnpj($this->issBase->cgm->z01_cgccpf));
        $documentTemplate->setVariable("identidade", $this->issBase->cgm->z01_ident);
        $documentTemplate->setVariable("endereco", $this->issBase->cgm->z01_ender);
        $documentTemplate->setVariable("numero", $this->issBase->cgm->z01_numero);
        $documentTemplate->setVariable("complemento", $this->issBase->cgm->z01_compl);
        $documentTemplate->setVariable("bairro", $this->issBase->cgm->z01_bairro);

        $documentTemplate->setVariable(
            "inscricaoestadual",
            $this->issBase->cgm->z01_incest ? $this->issBase->cgm->z01_incest : "ISENTO"
        );

        $stateRegistrationOrIdentity = "";
        if (\DBString::isCPF($this->issBase->cgm->z01_cgccpf) && $this->issBase->cgm->z01_ident) {
            $stateRegistrationOrIdentity = "Identidade: {$this->issBase->cgm->z01_ident}";
        } elseif (\DBString::isCNPJ($this->issBase->cgm->z01_cgccpf) && $this->issBase->cgm->z01_incest) {
            $stateRegistrationOrIdentity = "Inscricão Estadual: {$this->issBase->cgm->z01_incest}";
        }

        $documentTemplate->setVariable("inscricaoestadual_identidade", $stateRegistrationOrIdentity);

        $companyType = DBQuery()->from("cgmtipoempresa")
                                           ->join("tipoempresa", "db98_sequencial", "z03_tipoempresa")
                                           ->where("z03_numcgm", $this->issBase->q02_numcgm)
                                           ->first(["db98_descricao"]);

        $companyTypeDescription = $companyType ? $companyType->db98_descricao : "";
        $documentTemplate->setVariable("tipoempresa", $companyTypeDescription);

        $cgmjuridico = DBQuery()->from("cgmjuridico")->where("z08_numcgm", $this->issBase->q02_numcgm)->first();
        $nireCode = $cgmjuridico ? $cgmjuridico->z08_nire : "";
        $documentTemplate->setVariable("nire", $nireCode);

        return $documentTemplate;
    }

    /**
     * @param GenericWordDocumentTemplate $documentTemplate
     * @return GenericWordDocumentTemplate
     */
    private function buildStoppageData(GenericWordDocumentTemplate $documentTemplate)
    {
        $currentDate = $this->currentDate->format(\DBDate::DATA_EN);

        $stoppageInfo = DBQuery()->from("issbaseparalisacao")
                                 ->join("issmotivoparalisacao", "q141_sequencial", "q140_issmotivoparalisacao")
                                 ->where("q140_issbase", $this->issBase->q02_inscr)
                                 ->first(["q140_datainicio", "q140_datafim", "q141_descricao"]);

        $stoppageValue = "";

        if ($stoppageInfo && (!$stoppageInfo->q140_datafim || $stoppageInfo->q140_datafim > $currentDate)) {
            $startDate = Carbon::createFromFormat(
                \DBDate::DATA_EN,
                $stoppageInfo->q140_datainicio
            )->format(\DBDate::DATA_PTBR);

            $stoppageValue = "Inscrição Paralisada desde {$startDate} - {$stoppageInfo->q141_descricao}";
        }

        $documentTemplate->setVariable("dados_paralisacao", $stoppageValue);

        return $documentTemplate;
    }

    /**
     * @throws \Exception
     */
    private function buildAddressData(GenericWordDocumentTemplate $documentTemplate)
    {
        /** @var IssRuas $issRuas */
        $issRuas = IssRuas::query()->where("q02_inscr", $this->issBase->q02_inscr)->first();

        $tipologradouro = "";
        $rua_iss = "";
        $numero_endereco = "";
        $complemento_endereco = "";

        if ($issRuas) {
            if ($issRuas->rua) {
                $streetType = RuasTipoRepository::find($issRuas->rua->j14_tipo);
                $tipologradouro = $streetType->getDescricao();
            }

            $rua_iss = $issRuas->rua->j14_nome;
            $numero_endereco = $issRuas->q02_numero;
            $complemento_endereco = $issRuas->q02_compl;
        }

        $documentTemplate->setVariable("tipologradouro", $tipologradouro);
        $documentTemplate->setVariable("rua_iss", $rua_iss);
        $documentTemplate->setVariable("numero_endereco", $numero_endereco);
        $documentTemplate->setVariable("complemento_endereco", $complemento_endereco);

        /** @var IssBairro $issBairro */
        $issBairro = IssBairro::query()->where("q13_inscr", $this->issBase->q02_inscr)->first();

        $districtDescription = "";
        if ($issBairro && $issBairro->bairro) {
            $districtDescription = $issBairro->bairro->j13_descr;
        }

        $documentTemplate->setVariable("bairro_iss", $districtDescription);

        $matricInfo = DBQuery()->from("issmatric")
                               ->join("iptubase", "j01_matric", "q05_matric")
                               ->where("q05_inscr", $this->issBase->q02_inscr)
                               ->first(["j01_idbql"]);

        $sectorBlockBatchValue = "";
        $allotmentDescription = "";

        if ($matricInfo) {
            $loteInfo = DBQuery()->from("loteloc")
                                 ->where("j06_idbql", $matricInfo->j01_idbql)
                                 ->first(["j06_setorloc", "j06_quadraloc", "j06_lote"]);

            if ($loteInfo) {
                $sectorBlockBatchValue = $loteInfo->j06_setorloc;
                $sectorBlockBatchValue .= " / {$loteInfo->j06_quadraloc}";
                $sectorBlockBatchValue .= " / {$loteInfo->j06_lote}";
            }

            $loteamentoInfo = DBQuery()->from("loteloteam")
                                       ->join("loteam", "loteam.j34_loteam", "loteloteam.j34_loteam")
                                       ->where("j34_idbql", $matricInfo->j01_idbql)
                                       ->first(["j34_descr"]);

            if ($loteamentoInfo) {
                $allotmentDescription = $loteamentoInfo->j34_descr;
            }
        }

        $documentTemplate->setVariable("setor_quadra_lote", $sectorBlockBatchValue);
        $documentTemplate->setVariable("loteamento", $allotmentDescription);

        return $documentTemplate;
    }

    private function buildBusinessLicenseData(GenericWordDocumentTemplate $documentTemplate)
    {
        $documentTemplate->setVariable("data_emissao", $this->currentDate->format(\DBDate::DATA_PTBR));
        $documentTemplate->setVariable(
            "data_extenso",
            (new \DBDate($this->currentDate->format(\DBDate::DATA_EN)))->dataPorExtenso()
        );

        /** @var IssAlvara $issAlvara */
        $issAlvara = IssAlvara::query()->where("q123_inscr", $this->issBase->q02_inscr)->first();

        $documentTemplate->setVariable("sequencial_alvara", $issAlvara->q123_sequencial);
        $documentTemplate->setVariable("data_libera", $issAlvara->q123_dtinclusao->format(\DBDate::DATA_EN));
        $documentTemplate->setVariable("ano_liberacao", $issAlvara->q123_dtinclusao->year);

        $documentTemplate = $this->buildBusinessLicenseReleaseMovementData($documentTemplate, $issAlvara);
        $documentTemplate = $this->buildBusinessLicenseRenewalMovementData($documentTemplate, $issAlvara);

        /** @var IssMovAlvara $issMovAlvaraTransformation */
        $issMovAlvaraTransformation = IssMovAlvara::query()->where("q120_issalvara", $issAlvara->q123_sequencial)
            ->where("q120_isstipomovalvara", MovimentacaoAlvara::TIPO_TRANSFORMACAO)
            ->orderBy("q120_sequencial", "DESC")
            ->first();

        $transformationMovementObservation = "";
        if ($issMovAlvaraTransformation) {
            $transformationMovementObservation = $issMovAlvaraTransformation->q120_obs;
        }

        $documentTemplate->setVariable("obs_transformacao", $transformationMovementObservation);

        /** @var IssMovAlvara $issMovAlvara */
        $issMovAlvara = IssMovAlvara::query()->where("q120_issalvara", $issAlvara->q123_sequencial)
            ->whereIn(
                "q120_isstipomovalvara",
                [
                    MovimentacaoAlvara::TIPO_LIBERACAO,
                    MovimentacaoAlvara::TIPO_RENOVACAO,
                    MovimentacaoAlvara::TIPO_TRANSFORMACAO
                ]
            )
            ->orderBy("q120_sequencial", "DESC")
            ->first();

        $documentTemplate->setVariable("data_inicio_alvara", $issMovAlvara->q120_dtmov->format(\DBDate::DATA_PTBR));
        $documentTemplate->setVariable(
            "data_validade_alvara",
            $issMovAlvara->q120_dtmov->addDay($issMovAlvara->q120_validadealvara)->format(\DBDate::DATA_EN)
        );

        $alvaraTypeDescription = DBQuery()->from("issalvara")
                                          ->join("isstipoalvara", "q98_sequencial", "q123_isstipoalvara")
                                          ->where("q123_inscr", $this->issBase->q02_inscr)
                                          ->orderBy("q123_sequencial", "DESC")
                                          ->first(["q98_descricao"])->q98_descricao;

        $documentTemplate->setVariable("tipo_alvara", $alvaraTypeDescription);

        /** @var IssProcesso $issProcesso */
        $issProcesso = IssProcesso::query()->where("q14_inscr", $this->issBase->q02_inscr)->first();

        $processId = $issProcesso ? $issProcesso->q14_proces : "";
        $documentTemplate->setVariable("processo", $processId);

        $locationTypeInfo = DBQuery()->from("formalocalvara")
                                     ->where("q167_sequencial", $this->issBase->q02_formalocalvara)
                                     ->first(["q167_descricao"]);

        $locationTypeDescription = $locationTypeInfo ? $locationTypeInfo->q167_descricao : "";
        $documentTemplate->setVariable("forma_localizacao", $locationTypeDescription);

        return $documentTemplate;
    }

    private function buildBusinessLicenseReleaseMovementData(
        GenericWordDocumentTemplate $documentTemplate,
        IssAlvara $issAlvara
    ) {
        /** @var IssMovAlvara $issMovAlvaraRelease */
        $issMovAlvaraRelease = IssMovAlvara::query()->where("q120_issalvara", $issAlvara->q123_sequencial)
                                                    ->where("q120_isstipomovalvara", MovimentacaoAlvara::TIPO_LIBERACAO)
                                                    ->orderBy("q120_sequencial", "DESC")
                                                    ->first();

        $dueDate = "";
        if ($issMovAlvaraRelease->q120_validadealvara) {
            $dueDate = $issMovAlvaraRelease->q120_dtmov->addDay(
                $issMovAlvaraRelease->q120_validadealvara
            )->format(\DBDate::DATA_EN);
        }
        $documentTemplate->setVariable("data_validade", $dueDate);
        $documentTemplate->setVariable("obs_liberacao", $issMovAlvaraRelease->q120_obs);

        /** @var IssMovAlvaraProcesso $issMovAlvaraProcessoRelease */
        $issMovAlvaraProcessoRelease = IssMovAlvaraProcesso::query()->where(
            "q124_issmovalvara",
            $issMovAlvaraRelease->q120_sequencial
        )->first();

        $processId = $issMovAlvaraProcessoRelease ? $issMovAlvaraProcessoRelease->q124_codproc : "";
        $documentTemplate->setVariable("processo_alvara", $processId);

        return $documentTemplate;
    }

    private function buildBusinessLicenseRenewalMovementData(
        GenericWordDocumentTemplate $documentTemplate,
        IssAlvara $issAlvara
    ) {
        /** @var IssMovAlvara $issMovAlvara */
        $issMovAlvara = IssMovAlvara::query()->where("q120_issalvara", $issAlvara->q123_sequencial)
                                             ->where("q120_isstipomovalvara", MovimentacaoAlvara::TIPO_RENOVACAO)
                                             ->orderBy("q120_sequencial", "DESC")
                                             ->first();

        $dueDate = "";
        $movementDate = "";
        $movementObservation = "";
        $processId = "";

        if ($issMovAlvara) {
            $movementDate = $issMovAlvara->q120_dtmov->format(\DBDate::DATA_EN);
            $movementObservation = $issMovAlvara->q120_obs;

            if ($issMovAlvara->q120_validadealvara) {
                $dueDate = $issMovAlvara->q120_dtmov->addDay(
                    $issMovAlvara->q120_validadealvara
                )->format(\DBDate::DATA_EN);
            }

            /** @var IssMovAlvaraProcesso $issMovAlvaraProcesso */
            $issMovAlvaraProcesso = IssMovAlvaraProcesso::query()->where(
                "q124_issmovalvara",
                $issMovAlvara->q120_sequencial
            )->first();

            if ($issMovAlvaraProcesso) {
                $processId = $issMovAlvaraProcesso->q124_codproc;
            }
        }

        $documentTemplate->setVariable("data_nova_validade", $dueDate);
        $documentTemplate->setVariable("data_renova", $movementDate);
        $documentTemplate->setVariable("obs_renovacao", $movementObservation);
        $documentTemplate->setVariable("processo_renova", $processId);

        return $documentTemplate;
    }

    private function buildCgmDefaultDocumentData(GenericWordDocumentTemplate $documentTemplate)
    {
        $result = DBQuery()->from("cgmdocumento")
                           ->join("documento", "db58_sequencial", "z06_documento")
                           ->join("caddocumentoatributovalor", "db43_documento", "db58_sequencial")
                           ->join("caddocumentoatributo", "db45_sequencial", "db43_caddocumentoatributo")
                           ->join("caddocumento", "db45_caddocumento", "db44_sequencial")
                           ->where("z06_numcgm", $this->issBase->q02_numcgm)
                           ->orderBy("db45_caddocumento")
                           ->orderBy("db45_sequencial")
                           ->get(["db43_valor", "db45_descricao", "db45_caddocumento"]);

        $result = $result->groupBy("db45_caddocumento");


        $groupedInfoValue = "";
        $result->each(function ($groupedResult) use (&$groupedInfoValue) {
            if ($groupedInfoValue) {
                $groupedInfoValue .= " | ";
            }

            $documentInfoList = [];
            $groupedResult->each(function ($resultQuery) use (&$documentInfoList) {
                if ($resultQuery->db43_valor) {
                    $documentInfoList[] = "{$resultQuery->db45_descricao}: {$resultQuery->db43_valor}";
                }
            });

            $groupedInfoValue .= implode(" - ", $documentInfoList);
        });

        $documentTemplate->setVariable("documento_cgm_padrao", $groupedInfoValue);

        return $documentTemplate;
    }

    private function buildAccountingOfficeData(GenericWordDocumentTemplate $documentTemplate)
    {
        $accountingOfficeInfo = DBQuery()->from("escrito")
                                         ->join("cgm", "q10_numcgm", "z01_numcgm")
                                         ->where("q10_inscr", $this->issBase->q02_inscr)
                                         ->where(function ($builder) {
                                             $builder->whereNull("q10_dtfim");
                                             $builder->orWhere(
                                                 "q10_dtfim",
                                                 $this->currentDate->format(\DBDate::DATA_EN)
                                             );
                                         })
                                         ->first(["z01_nome"]);

        $accountingOfficeName = $accountingOfficeInfo ? $accountingOfficeInfo->z01_nome : "";
        $documentTemplate->setVariable("escritorio", $accountingOfficeName);

        return $documentTemplate;
    }

    private function buildPendingDocumentsData(GenericWordDocumentTemplate $documentTemplate)
    {
        $pendingDocumentInfoList = DBQuery()->from("ativid")
                                            ->join("tabativ", "q07_ativ", "q03_ativ")
                                            ->join("issatividconfdocumento", "q119_ativid", "q03_ativ")
                                            ->join("caddocumento", "db44_sequencial", "q119_caddocumento")
                                            ->where("q07_inscr", $this->issBase->q02_inscr)
                                            ->where(function ($builder) {
                                                $builder->whereNull("q07_datafi");
                                                $builder->orWhere(
                                                    "q07_datafi",
                                                    $this->currentDate->format(\DBDate::DATA_EN)
                                                );
                                            })
                                            ->where(function ($builder) {
                                                $builder->whereNull("q07_databx");
                                                $builder->orWhere(
                                                    "q07_databx",
                                                    $this->currentDate->format(\DBDate::DATA_EN)
                                                );
                                            })->whereNotExists(function ($builder) {
                                                $builder->selectRaw(1);
                                                $builder->from("issalvaradocumento");
                                                $builder->join("issalvara", "q123_sequencial", "q122_issalvara");
                                                $builder->whereColumn("q123_inscr", "q07_inscr");
                                                $builder->whereColumn("q122_caddocumento", "db44_sequencial");
                                            })
                                            ->get(["db44_descricao"]);

        $pendingDocumentDescriptionList = $pendingDocumentInfoList->map(function ($data) {
            return $data->db44_descricao;
        });

        $documentTemplate->setVariable("documentos_pendentes", $pendingDocumentDescriptionList->implode(" , "));

        return $documentTemplate;
    }

    private function buildActivitiesData(GenericWordDocumentTemplate $documentTemplate)
    {
        $documentTemplate = $this->buildMainActivityData($documentTemplate);
        $documentTemplate = $this->buildSecondaryActivitiesData($documentTemplate);

        $activitiesCount = Arr::get($documentTemplate->getVariables(), "ativ_princ_atividade") ? 1 : 0;
        $activitiesCount += count($documentTemplate->getVariables("atividadesSecundarias1"));
        $activitiesCount += count($documentTemplate->getVariables("atividadesSecundarias2"));

        $documentTemplate->setVariable("quantidadeatividades", $activitiesCount);

        return $documentTemplate;
    }

    private function buildMainActivityData(GenericWordDocumentTemplate $documentTemplate)
    {
        $mainActivityInfo = $this->getActivitiesInfo(true)->first();

        foreach ($mainActivityInfo as $field => $activityInfo) {
            switch ($field) {
                case "codigoClasse":
                    $documentTemplate->setVariable("codclasseativprinc", $activityInfo);
                    break;
                case "classeatividade":
                    $documentTemplate->setVariable("classeativprinc", $activityInfo);
                    break;
                case "tresultdigativ":
                    $documentTemplate->setVariable("tresultdigativprinc", $activityInfo);
                    break;
                case "primeirodigativ":
                    $documentTemplate->setVariable("primeirodigativprinc", $activityInfo);
                    break;
            }

            $documentTemplate->setVariable("ativ_princ_{$field}", $activityInfo);
        }

        return $documentTemplate;
    }

    private function buildSecondaryActivitiesData(GenericWordDocumentTemplate $documentTemplate)
    {
        $firstPageTotalActivitiesLimit = 14;
        $activitiesInfo = $this->getActivitiesInfo(false);

        $documentTemplate->setVariable(
            "atividadesSecundarias1",
            $activitiesInfo->slice(0, $firstPageTotalActivitiesLimit)->toArray()
        );

        $documentTemplate->setVariable(
            "atividadesSecundarias2",
            $activitiesInfo->slice($firstPageTotalActivitiesLimit, $activitiesInfo->count())->toArray()
        );

        return $documentTemplate;
    }

    private function getActivitiesInfo($isMainActivity)
    {
        $bageClientCode = 15;
        $config = DBConfig::isPrefeitura()->first(["db21_codcli"]);

        $query = TabAtiv::isActive($this->currentDate->format(\DBDate::DATA_EN));
        $query->join("ativid", "ativid.q03_ativ", "tabativ.q07_ativ");
        $query->leftJoin("atividcnae", "atividcnae.q74_ativid", "ativid.q03_ativ");
        $query->leftJoin("cnaeanalitica", "cnaeanalitica.q72_sequencial", "atividcnae.q74_cnaeanalitica");
        $query->leftJoin("cnae", "cnae.q71_sequencial", "cnaeanalitica.q72_cnae");
        $query->leftJoin("atividcbo", "atividcbo.q75_ativid", "ativid.q03_ativ");
        $query->leftJoin("rhcbo", "rhcbo.rh70_sequencial", "atividcbo.q75_rhcbo");
        $query->leftJoin("clasativ", "clasativ.q82_ativ", "ativid.q03_ativ");
        $query->leftJoin("classe", "classe.q12_classe", "clasativ.q82_classe");

        $mainActivityJoinClosure = function ($builder) {
            $builder->on("q88_inscr", "q07_inscr");
            $builder->whereColumn("q88_seq", "q07_seq");
        };

        if ($isMainActivity) {
            $query->join("ativprinc", $mainActivityJoinClosure);
        } else {
            $query->leftJoin("ativprinc", $mainActivityJoinClosure);
            $query->whereNull("q88_inscr");
        }

        $query->where("q07_inscr", $this->issBase->q02_inscr);

        $resultData = $query->get([
            "q71_descr",
            "rh70_descr",
            "rh70_estrutural",
            "q03_ativ",
            "q07_ativ",
            "q12_descr",
            "q07_datain",
            "q07_datafi",
            "q07_horaini",
            "q07_horafim",
            "q07_databx",
            "q07_val_ativ_int",
            "q07_perman",
            "q07_quant",
            "q71_estrutural",
            "q03_descr",
            "q12_classe",
            "q82_classe"
        ]);

        return $resultData->map(function ($data) use ($config, $bageClientCode) {
            if ($data->q71_descr) {
                $description = $data->q71_descr;
            } elseif ($data->rh70_descr) {
                $description = $data->rh70_descr;
            } else {
                $description = $data->q03_descr;
            }

            if ($data->q71_descr) {
                $structural = $data->q71_estrutural;
            } elseif ($data->rh70_descr) {
                $structural = $data->rh70_estrutural;
            } else {
                $structural = $data->q03_ativ;
            }

            $responseData = [
                "atividade" => $data->q07_ativ,
                "descricao" => $description,
                "codigoClasse" => $data->q82_classe,
                "classeatividade" => $data->q12_descr,
                "dataini" => $data->q07_datain,
                "datafin" => $data->q07_datafi,
                "horaini" => $data->q07_horaini,
                "horafim" => $data->q07_horafim,
                "databaixa" => $data->q07_databx,
                "atividade_interna" => $data->q07_val_ativ_int,
                "permanente" => $data->q07_perman ? "Sim" : "Não",
                "quant" => $data->q07_quant,
                "sessao_cnae" => substr($data->q71_estrutural, 0, 1),
                "estrut_cnae" => $data->q71_estrutural,
                "descricao_atividade" => $data->q03_descr,
                "codclasseatividade" => $data->q12_classe,
                "tresultdigativ" => substr($data->q07_ativ, -3),
                "estrutural" => $structural
            ];

            $firstDigitsLength = 1;
            if ($config->db21_codcli == $bageClientCode && strlen($data->q07_ativ) >= 5) {
                $firstDigitsLength = 2;
            }

            $responseData["primeirodigativ"] = substr($data->q07_ativ, 0, $firstDigitsLength);

            return utf8_encode_all($responseData);
        });
    }

    private function buildQrCodeData(GenericWordDocumentTemplate $documentTemplate)
    {
        $url = env("APP_URL")."/conferencia_alvara.php?inscricao={$this->issBase->q02_inscr}";

        $documentTemplate->setVariable("qr_code", $url);

        return $documentTemplate;
    }
}
