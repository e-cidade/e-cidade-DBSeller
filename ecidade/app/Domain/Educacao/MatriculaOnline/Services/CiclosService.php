<?php

namespace App\Domain\Educacao\MatriculaOnline\Services;

use App\Domain\Educacao\MatriculaOnline\Models\Ciclo;
use App\Domain\Educacao\MatriculaOnline\Models\CicloEnsino;

class CiclosService
{
    public function buscar($id = null)
    {
        return is_null($id) ? Ciclo::all() : Ciclo::find($id);
    }

    public function salvar($parametros)
    {
        $pk = array_remove($parametros, 'mo09_codigo');
        if (is_null($pk)) {
            $ciclo =  Ciclo::create($parametros);
            $this->salvarCiclosEnsino($ciclo->mo09_codigo, $parametros['ensinos']);
            return $ciclo;
        } else {
            $ciclo = tap($this->buscar($pk))->update($parametros);
            $this->salvarCiclosEnsino($ciclo->mo09_codigo, $parametros['ensinos']);
            return $ciclo;
        }
    }

    public function excluir($codigo)
    {
        $this->excluirCiclosEnsino($codigo);
        return $this->buscar($codigo)->delete();
    }

    public function salvarCiclosEnsino($ciclo, $ensinos)
    {
        $this->excluirCiclosEnsino($ciclo);
        foreach ($ensinos as $ensino) {
            CicloEnsino::create([
                'mo14_ciclo' => $ciclo,
                'mo14_ensino' => $ensino
            ]);
        }
    }

    public function excluirCiclosEnsino($ciclo)
    {
        CicloEnsino::where('mo14_ciclo', $ciclo)->delete();
    }
}
