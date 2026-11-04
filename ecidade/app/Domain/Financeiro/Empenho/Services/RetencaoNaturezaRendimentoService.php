<?php

namespace App\Domain\Financeiro\Empenho\Services;

use App\Domain\Financeiro\Empenho\Models\RetencaoNaturezaRendimento;

class RetencaoNaturezaRendimentoService
{
    private $retencaoreceitas;
    private $naturezarendimento;

    public function setRetencaoreceitas($retencaoreceitas)
    {
        $this->retencaoreceitas = $retencaoreceitas;
    }

    public function setNaturezarendimento($naturezarendimento)
    {
        $this->naturezarendimento = $naturezarendimento;
    }

    public function save()
    {
        $model = RetencaoNaturezaRendimento::where('e168_retencaoreceitas', $this->retencaoreceitas)->first();

        if (!$model) {
            $model = new RetencaoNaturezaRendimento;
        }

        $model->e168_naturezarendimento = $this->naturezarendimento;
        $model->e168_retencaoreceitas = $this->retencaoreceitas;

        $model->save();
    }
}
