<?php

namespace App\Domain\Patrimonial\Material\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $m105_sequencial
 * @property $m105_descricao
 */
class MaterialFamilia extends Model
{
    protected $primaryKey = 'm105_sequencial';
    protected $table = 'matfamilias';
    public $timestamps = false;
    protected $fillable = [
        'm105_sequencial',
        'm105_descricao',
    ];
}
