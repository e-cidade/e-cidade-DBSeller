<?php

namespace App\Domain\Tributario\ISSQN\Services;

use Alvara;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Patrimonial\Protocolo\Model\Processo\Processo;
use App\Domain\Tributario\ISSQN\Model\Base\IssAlvara;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use App\Domain\Tributario\ISSQN\Model\Base\IssMovAlvara;
use App\Domain\Tributario\ISSQN\Model\Base\Issmovalvarabaixa;
use App\Domain\Tributario\ISSQN\Model\Base\IssMovAlvaraProcesso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MovimentacaoAlvara;

class AlvaraService
{
    /**
     * @throws \Throwable
     */
    public function create(IssBase $issBase, Usuario $user, $typeCode, $situationCode, $isAutomaticGeneration)
    {
        $issAlvara = new IssAlvara();
        $issAlvara->q123_isstipoalvara = $typeCode;
        $issAlvara->q123_inscr = $issBase->q02_inscr;
        $issAlvara->q123_dtinclusao = Carbon::now()->format("Y-m-d");
        $issAlvara->q123_situacao = $situationCode;
        $issAlvara->q123_usuario = $user->getCodigo();
        $issAlvara->q123_geradoautomatico = $isAutomaticGeneration ? "t" : "f";
        $issAlvara->saveOrFail();

        return $issAlvara;
    }

    /**
     * @throws \Throwable
     */
    public function release(IssAlvara $issAlvara, Usuario $user, $process, $date, $expiryDays, $observation)
    {
        $this->createMovement(
            $issAlvara,
            $user,
            $process,
            MovimentacaoAlvara::TIPO_LIBERACAO,
            $date,
            $expiryDays,
            $observation
        );
    }

    /**
     * @throws \Throwable
     */
    public function baixa(IssAlvara $issAlvara, Usuario $user, $process, $date, $expiryDays, $type, $observation)
    {
        $issmovalvara = $this->createMovement(
            $issAlvara,
            $user,
            $process,
            MovimentacaoAlvara::TIPO_BAIXA,
            $date,
            $expiryDays,
            $observation
        );

        $issAlvara->q123_situacao = Alvara::INATIVO;
        $issAlvara->saveOrFail();

        $issmovalvarabaixa = new Issmovalvarabaixa();
        $issmovalvarabaixa->q129_issmovalvara = $issmovalvara->q120_sequencial;
        $issmovalvarabaixa->q129_tipobaixa = $type;
        $issmovalvarabaixa->saveOrFail();
    }

    /**
     * @throws \Throwable
     */
    private function createMovement(
        IssAlvara $issAlvara,
        Usuario $user,
        $process,
        $typeCode,
        $date,
        $expiryDays,
        $observation
    ) {
        $issMovAlvara = new IssMovAlvara();
        $issMovAlvara->q120_issalvara = $issAlvara->q123_sequencial;
        $issMovAlvara->q120_isstipomovalvara = $typeCode;
        $issMovAlvara->q120_dtmov = $date;
        $issMovAlvara->q120_validadealvara = $expiryDays;
        $issMovAlvara->q120_usuario = $user->getCodigo();
        $issMovAlvara->q120_obs = $observation;
        $issMovAlvara->saveOrFail();

        if ($process) {
            $this->saveProcess($issMovAlvara, $process);
        }

        return $issMovAlvara;
    }

    /**
     * @throws \Throwable
     */
    private function saveProcess(IssMovAlvara $issMovAlvara, Processo $process)
    {
        $issMovAlvaraProcesso = new IssMovAlvaraProcesso();
        $issMovAlvaraProcesso->q124_issmovalvara = $issMovAlvara->q120_sequencial;
        $issMovAlvaraProcesso->q124_codproc = $process->p58_codproc;
        $issMovAlvaraProcesso->saveOrFail();

        return $issMovAlvaraProcesso;
    }

    public static function getPortes($fisica = false)
    {
        $isNull = ($fisica === false) ? '(q40_fisica = false OR false IS NULL)' : "(q40_fisica = true OR true IS NULL)";

        $sql = "
            SELECT
                q40_codporte AS id,
                q40_descr AS descricao
            FROM
                issporte
            WHERE
                {$isNull}
            ORDER BY
                id
        ";

        return DB::select($sql);
    }
}
