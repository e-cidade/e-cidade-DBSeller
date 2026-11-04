<?php

namespace App\Domain\Tributario\Arrecadacao\Controller;

use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\Arrecadacao\Models\EmissaoPIX;
use App\Domain\Tributario\Arrecadacao\Models\EmissaoPIXDetalhe;
use App\Domain\Tributario\Arrecadacao\Models\Recibobarpix;
use App\Domain\Tributario\Arrecadacao\Models\Recibopaga;
use App\Http\Controllers\Controller;
use App\Jobs\RequisicaoAPIPixEmissaoJob;
use App\Domain\Tributario\Arrecadacao\Repositories\CotaUnica;
use App\Domain\Tributario\Arrecadacao\Repositories\EmissaoPIXRepository;
use App\Domain\Tributario\Arrecadacao\Repositories\ReciboRepository;
use App\Models\FailedJobs;
use App\Models\Jobs;
use App\Providers\EmissaoEcartaProvider;

use Exception;

use Illuminate\Http\Request;

class RequisicaoAPIPIXController extends Controller
{
    public function geral()
    {
        return view('Tributario.Arrecadacao.RequisicaoAPIPIXGeral');
    }

    public function request(Request $resquest)
    {
        try {
            $result = EmissaoPIX::where('tr10_status', false)->count();
            $falha  = FailedJobs::where('queue', ReciboRepository::QUEUE_NAME)->count();

            if ($result > 0 || $falha > 0) {
                $this->updateStatusEmissao();

                throw new Exception(
                    'Já existe um processo de emissão em execução, ' .
                    'e só é possível iniciar outro quando o processo ' .
                    'atual terminar ou ser interrompido.'
                );
            }

            $this->processarJobRequisicaoAPIPix($resquest->input());

            return new DBJsonResponse(
                'Emissão criada com sucesso',
                'Success'
            );
        } catch (Exception $e) {
            return new DBJsonResponse(
                [],
                $e->getMessage(),
                400
            );
        }
    }

    public function concluidos()
    {
        $this->updateStatusEmissao();
        
        return new DBJsonResponse(
            EmissaoPIXRepository::listEmissaoConcluidas(),
            'Sucess'
        );
    }

    public function listEmissao()
    {
        $this->updateStatusEmissao();
            
        return new DBJsonResponse(
            EmissaoPIXRepository::listEmissao(),
            'Success'
        );
    }

    public function interromper($tr10_sequencial)
    {
        try {
            $emissao = EmissaoPIX::where(
                'tr10_sequencial',
                $tr10_sequencial
            )->first();

            if (!$emissao) {
                throw new Exception('Emissão não encontrada');
            } elseif ($emissao->tr10_status) {
                throw new Exception('A Emissão já está concluida e não pode ser interrompida');
            }
   
            FailedJobs::where(
                'queue',
                ReciboRepository::QUEUE_NAME
            )->delete();

            Jobs::where(
                'queue',
                ReciboRepository::QUEUE_NAME
            )->delete();

            $emissaoDetalhesQuery = EmissaoPIXDetalhe::where(
                'tr11_tr10_sequencial',
                $emissao->tr10_sequencial
            );

            $emissaoDetalhes = $emissaoDetalhesQuery->get();

            $kumpresRecibo = $emissaoDetalhes->map(function ($emissaoDetalhe) {
                return $emissaoDetalhe->tr11_numpre_receita;
            });

            Recibopaga::whereIn('k00_numnov', $kumpresRecibo->all())
                ->delete();

            Recibobarpix::whereIn('k00_numpre', $kumpresRecibo->all())
                ->delete();

            $emissaoDetalhesQuery->delete();

            $emissao->tr10_status   = true;
            $emissao->tr10_num_jobs = 0;
            $emissao->tr10_concluidos_jobs = 0;
            $emissao->tr10_failed_jobs = 0;

            $emissao->save();

            return new DBJsonResponse(
                'Emissão interrompida com sucesso',
                'Success'
            );
        } catch (Exception $e) {
            return new DBJsonResponse(
                [],
                $e->getMessage(),
                400
            );
        }
    }

    public function tryAgainJob()
    {
        try {
            system('php artisan queue:retry all');

            return new DBJsonResponse(
                'Nova tentativa emcaminhada com sucesso',
                'Success'
            );
        } catch (Exception $e) {
            return new DBJsonResponse(
                [],
                $e->getMessage(),
                400
            );
        }
    }

    private function updateStatusEmissao()
    {
        $emissao = EmissaoPIX::where('tr10_status', false)
            ->orWhere('tr10_failed_jobs', '>', 0);

        if ($emissao->count() > 1) {
            $emissao->each(function ($emissao) {
                EmissaoEcartaProvider::ajusteEmissao($emissao->tr10_sequencial);
            });
        } elseif ($emissao->count() === 1) {
            $emissao = $emissao->first();

            if ($emissao) {
                EmissaoEcartaProvider::ajusteEmissao($emissao->tr10_sequencial);
            }
        }
    }

    private function processarJobRequisicaoAPIPix($data)
    {
        $cotaUnica = $this->getCotaUnica($data);
        $datausu   = db_getsession("DB_datausu");
        $instit    = db_getsession("DB_instit");

        RequisicaoAPIPixEmissaoJob::dispatch(
            $data['tipoDebito']['k00_tipo'],
            $cotaUnica,
            $datausu,
            (int) $instit,
            $data['codigoRequisicao']
        )->onQueue(ReciboRepository::QUEUE_CREATE_NAME);
    }

    private function getCotaUnica($data)
    {
        if ($data['contaUnica'] == '0') {
            return new CotaUnica($data['dataVencimento'], 0, false);
        } else {
            return new CotaUnica(
                $data['cotaUnicaSelect']['k00_dtvenc'],
                (int) $data['cotaUnicaSelect']['k00_percdes'],
                true
            );
        }
    }
}
