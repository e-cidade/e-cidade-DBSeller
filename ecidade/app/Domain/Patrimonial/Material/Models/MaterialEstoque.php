<?php

namespace App\Domain\Patrimonial\Material\Models;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $m70_codigo
 * @property integer $m70_codmatmater
 * @property integer $m70_coddepto
 * @property double $m70_quant
 * @property double $m70_valor
 *
 * @property Material $material
 * @property Departamento $departamento
 */
class MaterialEstoque extends Model
{
    protected $table = 'material.matestoque';
    protected $primaryKey = 'm70_codigo';
    public $timestamps = false;
    protected $fillable = [
        'm70_codigo',
        'm70_coddepto',
        'm70_codmatmater',
        'm70_valor',
        'm70_quant'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'm70_codmatmater', 'm60_codmater');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'm70_coddepto', 'coddepto');
    }
}
