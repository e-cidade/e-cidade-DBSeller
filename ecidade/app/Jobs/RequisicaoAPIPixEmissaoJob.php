<?php

namespace App\Jobs;

use App\Domain\Tributario\Arrecadacao\Models\EmissaoPIX;
use App\Domain\Tributario\Arrecadacao\Repositories\CotaUnica;
use App\Domain\Tributario\Arrecadacao\Repositories\ReciboRepository;
use App\Domain\Tributario\Caixa\Models\Arrecad;

use DateTime;
use Exception;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RequisicaoAPIPixEmissaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Id do job no banco de dados
     *
     * @var int $idJob
     */
    private $idJob = 0;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Código da requisição
     *
     * @var string $codRequest
     */
    private $codRequest;

    /**
     * Número do Tipo de Débito
     *
     * @var int $tipoDebito
     */
    private $tipoDebito;

    /**
     * Model
     *
     * @var Array  $arrecad
     */
    private $listNumpre;

    /**
     * Cota Única
     *
     * @var CotaUnica $cotaUnica
     */
    private $cotaUnica;

    /**
     * Anouso
     *
     * @var DateTime $anouso
     */
    private $datausu;

    /**
     * Id instituição
     *
     * @var int $instit
     */
    private $instit;

    /**
     * Create a new job instance.
     *
     * @param int $tipoDebito
     * @param CotaUnica $cotaUnica
     * @param int $datauso
     * @param int $instit
     *
     * @return void
     */
    public function __construct(
        $tipoDebito,
        CotaUnica $cotaUnica,
        $datausu,
        $instit,
        $codRequest
    ) {
        $datauso = (new DateTime('now'))->setTimestamp($datausu);

        $this->datausu    = $datauso;
        $this->codRequest = $codRequest;
        $this->tipoDebito = $tipoDebito;
        $this->cotaUnica  = $cotaUnica;
        $this->instit     = $instit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $arretipo = ReciboRepository::getArretipo($this->tipoDebito)
            ->toArray();
            
        $this->getListNumpreEmissa();

        if (count($this->listNumpre)) {
            $emissao = new EmissaoPIX();
            
            $emissao->tr10_codigo_emissao = $this->codRequest;
            $emissao->tr10_cotaunica      = $this->cotaUnica->existe;
            $emissao->tr10_num_jobs       = count($this->listNumpre);
            $emissao->tr10_codban         = '001';
            $emissao->tr10_tipo           = $arretipo['k00_tipo'];
            $emissao->tr10_status         = false;
            $emissao->tr10_concluidos_jobs = 0;
            $emissao->tr10_data_vencimento = $this->cotaUnica->getDataVenc();
            
            if (!$emissao->save()) {
                throw new Exception(
                    'Ocorreu um erro na tentativa de registrar a emissão do PIX.'
                );
            }

            DB::beginTransaction();

            foreach ($this->listNumpre as $numpre) {
                RequisicaoAPIPixJob::dispatch(
                    $emissao->tr10_sequencial,
                    $numpre->k00_numpre,
                    $this->cotaUnica,
                    $this->datausu,
                    $arretipo,
                    $this->instit
                )->onQueue(ReciboRepository::QUEUE_NAME);
            }

            DB::commit();
        }
    }

    /**
     * Carregar a lista ed numpre de acordo com a cota
     * unica ou a data de verncimento
     *
     * @return void
     */
    private function getListNumpreEmissa()
    {
        $this->listNumpre = Arrecad::where(
            'caixa.arrecad.k00_tipo',
            $this->tipoDebito
        )->select('caixa.arrecad.k00_numpre')->distinct();

        if ($this->cotaUnica->existe) {
            $this->listNumpre->join('caixa.recibounica', function ($query) {
                $query->on(
                    'caixa.arrecad.k00_numpre',
                    '=',
                    'caixa.recibounica.k00_numpre'
                );
            });
        }

        $this->listNumpre = $this->listNumpre->get();
    }
}
