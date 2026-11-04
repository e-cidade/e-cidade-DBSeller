<?php

namespace App\Domain\Integracoes\EFDReinf\Retencao\ValueObjects;

use BusinessException;

class Evento
{
    private $eventos = [
        'R-2010' => 'Retenção Contribuição Previdenciária por Serviços Tomados',
        'R-2055' => 'Aquisição de produção rural',
        'R-4010' => 'Pagamentos/créditos a beneficiário PF',
        'R-4020' => 'Pagamentos/créditos a beneficiário PJ',
    ];

    private $evento;

    public function __construct($evento)
    {
        $this->evento = array_filter($this->eventos, function ($key) use ($evento) {
            return $evento == $key;
        }, ARRAY_FILTER_USE_KEY);

        if (empty($this->evento)) {
            return new BusinessException("Evento '{$evento}' Inválido");
        }
    }

    public function codigo()
    {
        return array_keys($this->evento)[0];
    }

    public function descricao()
    {
        return array_values($this->evento)[0];
    }
}
