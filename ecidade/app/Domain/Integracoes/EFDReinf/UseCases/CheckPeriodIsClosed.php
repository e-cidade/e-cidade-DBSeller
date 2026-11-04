<?php

namespace App\Domain\Integracoes\EFDReinf\UseCases;

use App\Domain\Integracoes\EFDReinf\DTO\PeriodIsClosedInputDTO;
use App\Domain\Integracoes\EFDReinf\DTO\PeriodIsClosedOutputDTO;
use BusinessException;
use Illuminate\Support\Facades\DB;

class CheckPeriodIsClosed
{
    private $instituicao;
    private $competence;

    /**
     * Checar se o periodo do EFD-Reinf esta fechado
     *
     * @param PeriodIsClosedInputDTO $filters
     * @throws BusinessException
     * @return PeriodIsClosedOutputDTO
     */
    public function execute(PeriodIsClosedInputDTO $filters)
    {
        $this->instituicao = $filters->instit;
        $this->competence = $filters->competence;
        $event = $filters->event;

        if ($event == 'R-2099') {
            return $this->checkSerieR2000();
        }

        if ($event == 'R-4099') {
            return $this->checkSerieR4000();
        }

        throw new BusinessException("Evento {$event} não aceito.");
    }

    private function checkSerieR2000()
    {
        $lastEventProcessed = DB::table('esocialenvio')
        ->join('esocialenviostatus', 'rh214_esocialenvio', 'rh213_sequencial')
        ->join('db_config', 'numcgm', 'rh213_empregador')
        ->whereIn('rh213_evento', ['R-2099', 'R-2098'])
        ->where('codigo', $this->instituicao)
        ->where('esocialenvio.rh213_responsavelpreenchimento', $this->competence)
        ->where('rh214_situacao', true)
        ->orderBy('rh213_data', 'desc')
        ->first(['rh213_evento', 'rh213_data']);

        $periodIsClosedDto = new PeriodIsClosedOutputDTO(false, null);
        if (!$lastEventProcessed) {
            return $periodIsClosedDto;
        }

        if ($lastEventProcessed->rh213_evento == 'R-2099') {
            $periodIsClosedDto->setClosed(true);
            $periodIsClosedDto->setDate($lastEventProcessed->rh213_data);
        }

        return $periodIsClosedDto;
    }

    private function checkSerieR4000()
    {
        $lastEventProcessed = DB::table('esocialenvio')
        ->select('rh213_data')
        ->selectRaw("(rh213_dados::json)->>'fechret' as fechamento")
        ->join('esocialenviostatus', 'rh214_esocialenvio', 'rh213_sequencial')
        ->join('db_config', 'numcgm', 'rh213_empregador')
        ->where('rh213_evento', 'R-4099')
        ->where('codigo', $this->instituicao)
        ->whereRaw("(rh213_dados::json)->>'perapur' = '{$this->competence}'")
        ->where('rh214_situacao', true)
        ->orderBy('rh213_data', 'desc')
        ->first();

        $periodIsClosedDto = new PeriodIsClosedOutputDTO(false, null);
        if (!$lastEventProcessed) {
            return $periodIsClosedDto;
        }

        if ($lastEventProcessed->fechamento == 0) {
            $periodIsClosedDto->setClosed(true);
            $periodIsClosedDto->setDate($lastEventProcessed->rh213_data);
        }

        return $periodIsClosedDto;
    }
}
