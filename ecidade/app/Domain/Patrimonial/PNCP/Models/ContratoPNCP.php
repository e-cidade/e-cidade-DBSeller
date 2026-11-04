<?php

namespace App\Domain\Patrimonial\PNCP\Models;

use App\Domain\Patrimonial\Contratos\Models\Acordo;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $pn04_codigo
 * @property $pn04_acordo
 * @property $pn04_unidade
 * @property $pn04_numero
 * @property $pn04_ano
 * @property $pn04_datapublicacao
 * @property $pn04_usuario
 * @property $pn04_instit
 */
class ContratoPNCP extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'pn04_codigo';
    protected $table = 'contratospncp';
    protected $fillable = [
        'pn04_acordo',
        'pn04_unidade',
        'pn04_numero',
        'pn04_ano',
        'pn04_datapublicacao',
        'pn04_usuario',
        'pn04_instit'
    ];

    public function acordo()
    {
        return $this->belongsTo(Acordo::class, 'pn04_acordo', 'ac16_sequencial');
    }

    public function unidadeCompradora()
    {
        return $this->hasOne(UnidadesPNCP::class, 'pn02_unidade', 'pn04_unidade');
    }
}
