<?php

namespace App\Domain\Patrimonial\Material\Models;

use App\Domain\Patrimonial\Protocolo\Model\Cgm;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $m76_sequencial
 * @property string $m76_nome
 * @property integer|null $m76_numcgm
 *
 * @property Cgm|null $cgm
 */
class Fabricante extends Model
{
    public $timestamps = false;
    protected $table = 'material.matfabricante';
    protected $primaryKey = 'm76_sequencial';

    public function cgm()
    {
        return $this->belongsTo(Cgm::class, 'm76_numcgm', 'z01_numcgm');
    }
}
