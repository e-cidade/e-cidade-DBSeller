<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Fundeb;

//use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class RhCargosFundeb
 * @property int rh283_codigo
 * @property string rh283_descricao
 * @package App\Domain\RecursosHumanos\Pessoal\Model\Fundeb
 */
class RhCargosFundeb extends Model
{
    protected $table = 'pessoal.rhcargosfundeb';
    protected $primaryKey = ['rh283_codigo'];
    public $incrementing = false;
    public $timestamps = false;

    const TIPO_DIRETOR = 1;
    const TIPO_SUPERVISOR = 2;
    const TIPO_PROFESSOR = 3;
    const TIPO_APOIO = 4;

    public static function retornaQuantidadeCargo($tipo)
    {
        $quantidades = DB::table('rhcargosfundeb')
        ->select(['rh283_codigo', 'rh283_quantidade'])
        ->where('rh283_codigo', '=', $tipo)
        ->first();

        return $quantidades;
    }
}
