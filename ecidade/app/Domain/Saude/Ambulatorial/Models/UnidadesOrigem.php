<?php

namespace App\Domain\Saude\Ambulatorial\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $sd113_sequencial
 * @property string $sd113_descricao
 * @property boolean $sd113_ativo
*/
class UnidadesOrigem extends Model
{
    protected $table = 'ambulatorial.unidadesorigem';
    protected $primaryKey = 'sd113_sequencial';
    protected $fillable = ['sd113_sequencial', 'sd113_descricao','sd113_ativo'];
    public $timestamps = false;
}
