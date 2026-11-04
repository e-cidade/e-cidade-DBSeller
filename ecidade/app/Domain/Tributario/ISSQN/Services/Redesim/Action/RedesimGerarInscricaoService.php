<?php

namespace App\Domain\Tributario\ISSQN\Services\Redesim\Action;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\Isscadsimples;
use App\Domain\Tributario\ISSQN\Model\Base\Isscadsimplesbaixa;
use App\Domain\Tributario\ISSQN\Model\Base\Socios;
use App\Domain\Tributario\ISSQN\Model\Redesim\InscricaoRedesim;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishment;
use App\Domain\Tributario\ISSQN\Model\Redesim\ProcessedEstablishmentData;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao\RedesimDadosAtividadeParser;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao\RedesimDadosEmpresaParser;
use App\Domain\Tributario\ISSQN\Parsers\Redesim\GerarInscricao\RedesimDadosSocioParser;
use App\Domain\Tributario\ISSQN\Services\AlvaraService;
use App\Domain\Tributario\ISSQN\Services\Base\AtividadeService;
use App\Domain\Tributario\ISSQN\Services\Base\IssBairroService;
use App\Domain\Tributario\ISSQN\Services\Base\IssBaseService;
use App\Domain\Tributario\ISSQN\Services\Base\IsscadsimplesService;
use App\Domain\Tributario\ISSQN\Services\Base\IssProcessoService;
use App\Domain\Tributario\ISSQN\Services\Base\IssQuantService;
use App\Domain\Tributario\ISSQN\Services\Base\IssRuasService;
use App\Domain\Tributario\ISSQN\Services\Base\SociosService;

class RedesimGerarInscricaoService extends RedesimActionBaseService
{
    /**
     * @var IssBaseService
     */
    private $issBaseService;

    /**
     * @var IssQuantService
     */
    private $issQuantService;

    /**
     * @var IssRuasService
     */
    private $issRuasService;

    /**
     * @var IssBairroService
     */
    private $issBairroService;

    /**
     * @var IssProcessoService
     */
    private $issProcessoService;

    /**
     * @var AtividadeService
     */
    private $atividadeService;

    /**
     * @var SociosService
     */
    private $sociosService;

    /**
     * @var AlvaraService
     */
    private $alvaraService;
    private $isscadsimplesService;

    public function __construct(
        IssBaseService $issBaseService,
        IssQuantService $issQuantService,
        IssRuasService $issRuasService,
        IssBairroService $issBairroService,
        IssProcessoService $issProcessoService,
        AtividadeService $atividadeService,
        SociosService $sociosService,
        AlvaraService $alvaraService,
        IsscadsimplesService $isscadsimplesService
    ) {
        $this->issBaseService = $issBaseService;
        $this->issQuantService = $issQuantService;
        $this->issRuasService = $issRuasService;
        $this->issBairroService = $issBairroService;
        $this->issProcessoService = $issProcessoService;
        $this->atividadeService = $atividadeService;
        $this->sociosService = $sociosService;
        $this->alvaraService = $alvaraService;
        $this->isscadsimplesService = $isscadsimplesService;
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function run()
    {
        $this->findEstablishmentData();

        $issBase = $this->createMunicipalRegistration();
        $this->createActivities($issBase);
        $this->createPartners($issBase);
        $this->saveInscricaoRedesim($issBase);

        $user = Usuario::where("id_usuario", db_getsession("DB_id_usuario"))->first();
        $alvaraTypeCode = env("REDESIM_GERACAO_AUTOMATICA_INSCRICAO_TIPO_ALVARA");

        if (!$alvaraTypeCode) {
            throw new \Exception("Tipo de alvará não configurado.");
        }

        $alvaraSituationCode = 1;
        $this->alvaraService->create($issBase, $user, $alvaraTypeCode, $alvaraSituationCode, true);

        $this->saveSimplesNacional($issBase);

        return $issBase;
    }

    /**
     * @throws \Throwable
     * @return IssBase
     */
    private function createMunicipalRegistration()
    {
        $issBase = $this->issBaseService->save(
            $this->cgm,
            RedesimDadosEmpresaParser::buildIssBase($this->establishmentData)
        );

        $this->issQuantService->save(
            $issBase,
            RedesimDadosEmpresaParser::buildIssQuant($this->establishmentData)
        );

        $issRuasInfo = RedesimDadosEmpresaParser::buildIssRuas($this->establishmentData);

        if ($issRuasInfo) {
            $this->issRuasService->save($issBase, $issRuasInfo);
        }

        $issBairroInfo = RedesimDadosEmpresaParser::buildIssBairro($this->establishmentData);

        if ($issBairroInfo) {
            $this->issBairroService->save($issBase, $issBairroInfo);
        }

        $this->issProcessoService->save($issBase, $this->processedEstablishment->process);

        return $issBase;
    }

    /**
     * @throws \Throwable
     */
    private function createActivities(IssBase $issBase)
    {
        $activityList = RedesimDadosAtividadeParser::buildActivities($this->establishmentData);

        foreach ($activityList as $activity) {
            $this->atividadeService->save($issBase, $activity->isMainActivity, $activity->data);
        }
    }

    /**
     * @throws \Throwable
     */
    private function createPartners(IssBase $issBase)
    {
        $partnerList = RedesimDadosSocioParser::buildPartners($this->establishmentData);

        foreach ($partnerList as $partner) {
            $cgm = Cgm::firstOrCreate(["z01_cgccpf" => $partner->cpfCnpj], $partner->personData);

            $partnerAlreadyExists = Socios::query()->where("q95_cgmpri", $issBase->cgm->z01_numcgm)
                                                   ->where("q95_numcgm", $cgm->z01_numcgm)
                                                   ->first(["q95_numcgm"]);

            if (!$partnerAlreadyExists) {
                $this->sociosService->save($issBase, $cgm, $partner->data);
            }
        }
    }

    /**
     * @throws \Throwable
     */
    private function saveInscricaoRedesim(IssBase $issBase)
    {
        $inscricaoRedesim = new InscricaoRedesim();
        $inscricaoRedesim->setInscricao($issBase->q02_inscr);
        $inscricaoRedesim->setProcesso($this->processedEstablishment->process->p58_codproc);
        $inscricaoRedesim->setIdentificadorRedesim($this->processedEstablishment->q190_external_id);
        $inscricaoRedesim->saveOrFail();
    }

    /**
     * @throws \Throwable
     */
    private function saveSimplesNacional(IssBase $issBase)
    {
        $infoList = RedesimDadosEmpresaParser::buildSimplesNacionalInfo($this->establishmentData);

        foreach ($infoList as $info) {
            $isscadsimples = $this->isscadsimplesService->save($issBase, $info->startDateInfo);

            if ($info->endDateInfo) {
                $this->isscadsimplesService->baixa($isscadsimples, $info->endDateInfo);
            }
        }
    }
}
