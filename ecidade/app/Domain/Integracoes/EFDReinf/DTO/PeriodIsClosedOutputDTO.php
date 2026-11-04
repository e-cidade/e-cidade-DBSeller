<?php

namespace App\Domain\Integracoes\EFDReinf\DTO;

class PeriodIsClosedOutputDTO
{
    private $closed;
    private $date;

    /**
     * @param bool $closed
     * @param string $date
     */
    public function __construct($closed, $date)
    {
        $this->closed = $closed;
        $this->date = $date;
    }

    /**
     * @param bool $closed
     * @return void
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    /**
     * @param string $date
     * @return void
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return bool
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    public function toArray()
    {
        return [
            'closed' => $this->getClosed(),
            'date' => $this->getDate()
        ];
    }
}
