<?php

namespace App\Jobs;

use App\Domain\Tributario\Arrecadacao\Models\EmissaoPIX;
use App\Domain\Tributario\Arrecadacao\Models\EmissaoPIXDetalhe;
use App\Domain\Tributario\Arrecadacao\Repositories\ReciboRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\CotaUnica;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;
use stdClass;

class RequisicaoAPIPixJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Código do job da Emissão
     *
     * @var int $idEmissao
     */
    public $idEmissao;

     /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Número do numpre que vai ser processado
     *
     * @var int $k00_numpre
     */
    private $k00_numpre;

    /**
     * Data Uso
     *
     * @var DateTIme $anouso
     */
    private $datausu;

    /**
     * Cota ?nica
     *
     * @var CotaUnica $cotaUnica
     */
    private $cotaUnica;

    /**
     * Arretipo
     *
     * @var array $arretipo
     */
    private $arretipo;

    /**
     * Id instituição
     *
     * @var int $instit
     */
    private $instit;

    /**
     * Create a new job instance.
     *
     * @param int $idEmissao
     * @param int $k00_numpre
     * @param CotaUnica $cotaUncia
     * @param DateTime $datausu data atual ou do exercicio
     * @param array $arretipo
     * @param int $instit
     *
     * @return void
     */
    public function __construct(
        $idEmissao,
        $k00_numpre,
        $cotaUnica,
        $datausu,
        $arretipo,
        $instit
    ) {
        $this->idEmissao  = $idEmissao;
        $this->k00_numpre = $k00_numpre;
        $this->cotaUnica  = $cotaUnica;
        $this->datausu    = $datausu;
        $this->arretipo   = $arretipo;
        $this->instit     = $instit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $emissao = EmissaoPIX::where(
                'tr10_sequencial',
                '=',
                $this->idEmissao
            )->first();

            if (!$emissao) {
                throw new Exception('Ocorreu um erro, emissão não encontrada.');
            }

            if ($emissao->tr10_status) {
                DB::commit();
                return;
            }

            $emissaoDetalhe = new EmissaoPIXDetalhe();

            $emissaoDetalhe->tr11_tr10_sequencial = $this->idEmissao;
            $emissaoDetalhe->tr11_numpre_origem = $this->k00_numpre;

            $valores = new stdClass;

            $valores->k00_numpre     = $this->k00_numpre;
            $valores->arretipo       = $this->arretipo;
            $valores->cotaUnica      = $this->cotaUnica;
            $valores->dataVencimento = $this->cotaUnica->getDataVenc();

            ReciboRepository::gerarDadosEmissao(
                $valores,
                $this->datausu,
                $this->instit
            );

            $emissaoDetalhe->tr11_numpre_receita = $valores->recibo
                ->getNumpreRecibo();
            
            $emissaoDetalhe->tr11_valores = serialize($valores);

            if (!$emissaoDetalhe->save()) {
                throw new Exception(
                    'Ocorreu um erro na tentativa' .
                    ' de cadastras os detalhes da emiss?o'
                );
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage(), PHP_EOL;

            throw new Exception(mb_convert_encoding($e->getMessage(), 'UTF-8', 'UTF-8'));
        }
    }
}
