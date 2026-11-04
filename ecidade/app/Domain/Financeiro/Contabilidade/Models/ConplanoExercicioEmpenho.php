<?php
namespace App\Domain\Financeiro\Contabilidade\Models;

use App\Domain\Financeiro\Empenho\Models\Empenho;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $c151_codigo
 * @property int $c151_exercicio
 * @property int $c151_reduzido
 * @property int $c151_empenho
 * @property float $c151_valor
 * @property string $c151_natureza
 */
class ConplanoExercicioEmpenho extends Model
{
    protected $table = 'contabilidade.conplanoexeempenho';
    protected $primaryKey = 'c151_codigo';
    public $timestamps = false;

    public function empenho()
    {
        return $this->hasOne(Empenho::class, 'e60_numemp', 'c151_empenho');
    }
}
