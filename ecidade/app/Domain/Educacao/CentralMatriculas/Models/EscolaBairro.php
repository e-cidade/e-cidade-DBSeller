<?php

namespace App\Domain\Educacao\CentralMatriculas\Models;

use App\Domain\Tributario\Cadastro\Models\Bairro;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EscolaBairro
 * @package App\Domain\Educacao\CentralMatriculas\Models
 * @property integer $mo08_codigo
 * @property integer $mo08_escola
 * @property integer $mo08_bairro
 */
class EscolaBairro extends Model
{
    protected $table = "plugins.escbairro";
    protected $primaryKey = 'mo08_codigo';
    public $timestamps = false;
    public $incrementing = false;

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'mo08_escola', 'mo53_codigo');
    }

    public function bairro()
    {
        return $this->belongsTo(Bairro::class, 'mo08_bairro', 'j13_codi');
    }
}
