<?php

namespace App\Domain\Saude\Farmacia\Models;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use App\Domain\Saude\Ambulatorial\Models\CgsUnidade;
use App\Domain\Saude\Ambulatorial\Models\Unidade;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $fa67_id
 * @property \DateTime $fa67_data_hora
 * @property integer $fa67_paciente
 * @property integer $fa67_medicamento
 * @property integer $fa67_quantidade
 * @property integer $fa67_usuario
 * @property integer $fa67_unidade_saude
 * @property string $fa67_observacoes
 *
 * @property CgsUnidade $paciente
 * @property Medicamento $medicamento
 * @property Usuario $usuario
 * @property Unidade $unidade
 */
class DemandaReprimida extends Model
{
    protected $table = 'farmacia.demanda_reprimida';
    protected $primaryKey = 'fa67_id';
    public $timestamps = false;

    public $casts = [
        'fa67_data_hora' => 'datetime'
    ];

    public function paciente()
    {
        return $this->belongsTo(CgsUnidade::class, 'fa67_paciente', 'z01_i_cgsund');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'fa67_medicamento', 'fa01_i_codigo');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'fa67_usuario', 'id_usuario');
    }

    public function unidade()
    {
        return $this->belongsTo(Unidade::class, 'fa67_unidade_saude', 'sd02_i_codigo');
    }
}
