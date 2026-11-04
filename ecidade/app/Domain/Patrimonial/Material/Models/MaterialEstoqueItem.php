<?php

namespace App\Domain\Patrimonial\Material\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialEstoqueItem extends Model
{
    protected $table = 'material.matestoqueitem';
    protected $primaryKey = 'm71_codlanc';
    public $timestamps = false;
    protected $fillable = [
        'm71_codlanc',
        'm71_codmatestoque',
        'm71_data',
        'm71_valor',
        'm71_quant',
        'm71_quantatend'
    ];

    public function estoque()
    {
        return $this->belongsTo(MaterialEstoque::class, 'm71_codmatestoque', 'm70_codigo');
    }
}
