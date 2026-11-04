<?php

namespace App\Domain\Configuracao\GeradorRelatorio\Relatorios\Traits;

trait PodeQuebrar
{
    private $controleQuebras;

    private function verificaQuebra($dados, $campos)
    {
        foreach ($campos as $campo) {
            if (!$campo['quebra']) {
                continue;
            }

            if (!array_key_exists($campo['nome'], $this->controleQuebras)) {
                $this->controleQuebras[$campo['nome']] = '';
            }

            if ($this->controleQuebras[$campo['nome']] != $dados->{$campo['nome']}) {
                $this->controleQuebras[$campo['nome']] = $dados->{$campo['nome']};

                yield $dados->{$campo['nome']};
            } else {
                yield '';
            }
        }
    }
}
