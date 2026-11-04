<?php

namespace App\Domain\Patrimonial\Material\Models;

use App\Domain\Configuracao\Departamento\Models\Departamento;
use App\Domain\Configuracao\Usuario\Models\Usuario;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $m80_codigo
 * @property $m80_login
 * @property $m80_data
 * @property $m80_obs
 * @property $m80_codtipo
 * @property $m80_coddepto
 * @property $m80_hora
 */
class LancamentoMovimentacao extends Model
{
    protected $table = 'material.matestoqueini';
    protected $primaryKey = 'm80_codigo';
    public $timestamps = false;
    protected $fillable = [
        'm80_codigo',
        'm80_login',
        'm80_data',
        'm80_hora',
        'm80_obs',
        'm80_codtipo',
        'm80_coddepto'
    ];

    public function movimentacoes()
    {
        return $this->hasMany(MovimentacaoEstoqueItem::class, 'm82_matestoqueini', 'm80_codigo')
            ->with('valores')
            ->with('estoqueItem');
    }

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_usuario', 'm80_login');
    }

    public function tipoLancamento()
    {
        return $this->belongsTo(TipoLancamento::class, 'm80_codtipo', 'm81_codtipo');
    }

    public function departamentoDestino()
    {
        return $this->belongsTo(Departamento::class, 'm80_coddepto', 'coddepto');
    }
}
