<?php

namespace App\Domain\Tributario\Cemiterio\Models\Cemiterio\Parametros;

use Illuminate\Database\Eloquent\Model;

class ParametrosCemiterioModel extends Model
{
    protected $table = "cemiterio.parametroscemiterio";
    protected $primaryKey = 'cem36_sequencial';
    public $timestamps = false;
    public $incrementing = true;
    protected $fillable = ['cem36_obrigatoriedadetaxasepultamento', 'cem36_ano'];
}
