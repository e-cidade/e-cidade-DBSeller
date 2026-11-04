<?php

namespace App\Domain\Educacao\Escola\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Calendario
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed52_i_codigo
 * @property string $ed52_c_descr
 * @property integer $ed52_i_duracaocal
 * @property integer $ed52_i_ano
 * @property integer $ed52_i_periodo
 * @property Carbon $ed52_d_inicio
 * @property Carbon $ed52_d_fim
 * @property Carbon $ed52_d_resultfinal
 * @property string $ed52_c_aulasabado
 * @property integer $ed52_i_diasletivos
 * @property integer $ed52_i_semletivas
 * @property integer $ed52_i_calendant
 * @property string $ed52_c_passivo
 * @property mixed calendarioAnterior
 */
class Calendario extends Model
{
    protected $table = 'escola.calendario';
    protected $primaryKey = 'ed52_i_codigo';
    public $timestamps = false;
    public $incrementing = false;

    protected $dates = [
        'ed52_d_inicio',
        'ed52_d_fim',
        'ed52_d_resultfinal',
    ];

    protected $casts = [
        'ed52_c_descr' => 'string'
    ];
    /**
     * @var []
     */
    private $storage = [];

    /**
     * @return integer
     */
    public function getCodigo()
    {
        return $this->ed52_i_codigo;
    }
    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->ed52_c_descr;
    }
    /**
     * @return integer
     */
    public function getDuracao()
    {
        return $this->ed52_i_duracaocal;
    }
    /**
     * @return integer
     */
    public function getAno()
    {
        return $this->ed52_i_ano;
    }
    /**
     * @return integer
     */
    public function getPeriodo()
    {
        return $this->ed52_i_periodo;
    }
    /**
     * @return Carbon
     */
    public function getDataInicio()
    {
        return $this->ed52_d_inicio;
    }
    /**
     * @return Carbon
     */
    public function getDataFim()
    {
        return $this->ed52_d_fim;
    }
    /**
     * @return Carbon
     */
    public function getDataResultFinal()
    {
        return $this->ed52_d_resultfinal;
    }
    /**
     * @return boolean
     */
    public function hasAulaSabado()
    {
        return $this->ed52_c_aulasabado === 'S';
    }
    /**
     * @return integer
     */
    public function getDiasLetivos()
    {
        return $this->ed52_i_diasletivos;
    }
    /**
     * @return integer
     */
    public function getSemamasLetivas()
    {
        return $this->ed52_i_semletivas;
    }
    /**
     * @return bool
     */
    public function isPassivo()
    {
        return $this->ed52_c_passivo === 'S';
    }
    /**
     * @return Calendario
     */
    public function getCalendarioAnterior()
    {
        if (empty($this->storage['calendarioAnterior'])) {
            $this->storage['calendarioAnterior'] = $this->calendarioAnterior;
        }

        return $this->storage['calendarioAnterior'];
    }
    /**
     * @return HasOne
     */
    public function calendarioAnterior()
    {
        return $this->hasOne(Calendario::class, 'ed52_i_codigo', 'ed52_i_calendant');
    }

    public function scopeApenasAtivos($query)
    {
        return $query->where('ed52_c_passivo', 'N');
    }

    public function calendarioEscola()
    {
        return $this->belongsTo(CalendarioEscola::class, 'ed52_i_codigo', 'ed38_i_calendario');
    }

    public function turmas()
    {
        return $this
            ->hasMany(Turma::class, 'ed57_i_calendario', 'ed52_i_codigo')
            ->orderBy('ed57_c_descr');
    }

    public function periodosCalendario()
    {
        return $this->hasMany(PeriodoCalendario::class, 'ed53_i_calendario', 'ed52_i_codigo');
    }

    public function periodos()
    {
        return $this->hasMany(PeriodoCalendario::class, 'ed53_i_calendario', 'ed52_i_codigo')
            ->with('periodoAvaliacao');
    }
}
