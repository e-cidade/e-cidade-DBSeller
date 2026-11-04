<?php

namespace App\Domain\Integracoes\EFDReinf\DTO;

class PeriodIsClosedInputDTO
{
    public $event;
    public $competence;
    public $instit;

    /**
     * @param string $event Evento de fechamento
     * @param string $competece
     * @param int $instit
     */
    public function __construct($event, $competence, $instit)
    {
        $this->event = $event;
        $this->competence = $competence;
        $this->instit = $instit;
    }
}
