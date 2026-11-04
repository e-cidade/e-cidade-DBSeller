<?php

namespace App\Domain\Educacao\MatriculaOnline\Services;

use App\Domain\Educacao\MatriculaOnline\Models\Fase;
use Illuminate\Support\Facades\DB;

class FasesService
{
    public function buscar($id = null)
    {
        return is_null($id) ? Fase::all() : Fase::find($id);
    }

    public function salvar($parametros)
    {
        $pk = array_remove($parametros, 'mo04_codigo');
        if (is_null($pk)) {
            $fase = Fase::create($parametros);
        } else {
            array_remove($parametros, 'mo04_processada');
            array_remove($parametros, 'mo04_encerrada');
            $fase = tap($this->buscar($pk))->update($parametros);
        }
        $sequence = "plugins.protocolo_fase{$fase->mo04_codigo}_seq";
        DB::statement("CREATE SEQUENCE IF NOT EXISTS {$sequence} MINVALUE 1 MAXVALUE 99999;");
        return $fase;
    }
    public function excluir($codigo)
    {
        return $this->buscar($codigo)->delete();
    }
}
