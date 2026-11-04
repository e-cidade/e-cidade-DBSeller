<?php

namespace App\Domain\Educacao\Escola\Models;

use App\Domain\Educacao\Secretaria\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Turma
 * @package App\Domain\Educacao\Escola\Models
 * @property integer $ed57_i_codigo
 * @property integer $ed57_i_escola
 * @property integer $ed57_i_calendario
 * @property string $ed57_c_descr
 * @property integer $ed57_i_base
 * @property integer $ed57_i_turno
 * @property integer $ed57_i_sala
 * @property string $ed57_c_medfreq
 * @property string $ed57_t_obs
 * @property integer $ed57_i_codigoinep
 * @property integer $ed57_i_tipoatend
 * @property integer $ed57_i_ativqtd
 * @property integer $ed57_i_censocursoprofiss
 * @property integer $ed57_i_tipoturma
 * @property integer $ed57_i_censoetapa
 * @property boolean $ed57_censoprogramamaiseducacao
 * @property TurmaEtapaRegimeMatricula[] $etapasTurma
 * @property Escola $escola
 */
class Turma extends Model
{
    protected $table = 'escola.turma';
    protected $primaryKey = 'ed57_i_codigo';
    public $timestamps = false;
    public $incrementing = false;
    protected $appends = ['etapa', 'regenciaGlobal'];

    /**
     * @var array|mixed
     */
    private $etapas;
    /**
     * @var array
     */
    private $storage = [];

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->ed57_i_codigo;
    }

    /**
     * @param int $ed57_i_codigo
     */
    public function setCodigo($ed57_i_codigo)
    {
        $this->ed57_i_codigo = $ed57_i_codigo;
    }

    /**
     * @return int
     */
    public function getEscola()
    {
        return $this->ed57_i_escola;
    }

    /**
     * @param int $ed57_i_escola
     */
    public function setEscola($ed57_i_escola)
    {
        $this->ed57_i_escola = $ed57_i_escola;
    }

    /**
     * @return int
     */
    public function getCalendario()
    {
        return $this->ed57_i_calendario;
    }

    /**
     * @param int $ed57_i_calendario
     */
    public function setCalendario($ed57_i_calendario)
    {
        $this->ed57_i_calendario = $ed57_i_calendario;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->ed57_c_descr;
    }

    /**
     * @param string $ed57_c_descr
     */
    public function setDescricao($ed57_c_descr)
    {
        $this->ed57_c_descr = $ed57_c_descr;
    }

    /**
     * @return int
     */
    public function getBase()
    {
        return $this->ed57_i_base;
    }

    /**
     * @param int $ed57_i_base
     */
    public function setBase($ed57_i_base)
    {
        $this->ed57_i_base = $ed57_i_base;
    }

    /**
     * @return int
     */
    public function getTurno()
    {
        return $this->ed57_i_turno;
    }

    /**
     * @param int $ed57_i_turno
     */
    public function setTurno($ed57_i_turno)
    {
        $this->ed57_i_turno = $ed57_i_turno;
    }

    /**
     * @return int
     */
    public function getSala()
    {
        return $this->ed57_i_sala;
    }

    /**
     * @param int $ed57_i_sala
     */
    public function setSala($ed57_i_sala)
    {
        $this->ed57_i_sala = $ed57_i_sala;
    }

    /**
     * @return string
     */
    public function getMedfreq()
    {
        return $this->ed57_c_medfreq;
    }

    /**
     * @param string $ed57_c_medfreq
     */
    public function setMedfreq($ed57_c_medfreq)
    {
        $this->ed57_c_medfreq = $ed57_c_medfreq;
    }

    /**
     * @return string
     */
    public function getObservacao()
    {
        return $this->ed57_t_obs;
    }

    /**
     * @param string $ed57_t_obs
     */
    public function setObservacao($ed57_t_obs)
    {
        $this->ed57_t_obs = $ed57_t_obs;
    }

    /**
     * @return int
     */
    public function getCodigoInep()
    {
        return $this->ed57_i_codigoinep;
    }

    /**
     * @param int $ed57_i_codigoinep
     */
    public function setCodigoInep($ed57_i_codigoinep)
    {
        $this->ed57_i_codigoinep = $ed57_i_codigoinep;
    }

    /**
     * @return int
     */
    public function getTipoAtendimento()
    {
        return $this->ed57_i_tipoatend;
    }

    /**
     * @param int $ed57_i_tipoatend
     */
    public function setTipoAtendimento($ed57_i_tipoatend)
    {
        $this->ed57_i_tipoatend = $ed57_i_tipoatend;
    }

    /**
     * @return int
     */
    public function getAtivqtd()
    {
        return $this->ed57_i_ativqtd;
    }

    /**
     * @param int $ed57_i_ativqtd
     */
    public function setAtivqtd($ed57_i_ativqtd)
    {
        $this->ed57_i_ativqtd = $ed57_i_ativqtd;
    }

    /**
     * @return int
     */
    public function getCensoCursoProfiss()
    {
        return $this->ed57_i_censocursoprofiss;
    }

    /**
     * @param int $ed57_i_censocursoprofiss
     */
    public function setCensoCursoProfiss($ed57_i_censocursoprofiss)
    {
        $this->ed57_i_censocursoprofiss = $ed57_i_censocursoprofiss;
    }

    /**
     * @return int
     */
    public function getTipoTurma()
    {
        return $this->ed57_i_tipoturma;
    }

    /**
     * @param int $ed57_i_tipoturma
     */
    public function setTipoTurma($ed57_i_tipoturma)
    {
        $this->ed57_i_tipoturma = $ed57_i_tipoturma;
    }

    /**
     * @return int
     */
    public function getCensoEtapa()
    {
        return $this->ed57_i_censoetapa;
    }

    /**
     * @param int $ed57_i_censoetapa
     */
    public function setCensoEtapa($ed57_i_censoetapa)
    {
        $this->ed57_i_censoetapa = $ed57_i_censoetapa;
    }

    /**
     * @return bool
     */
    public function isCensoProgramaMaisEducacao()
    {
        return $this->ed57_censoprogramamaiseducacao;
    }

    /**
     * @param bool $ed57_censoprogramamaiseducacao
     */
    public function setCensoProgramaMaisEducacao($ed57_censoprogramamaiseducacao)
    {
        $this->ed57_censoprogramamaiseducacao = $ed57_censoprogramamaiseducacao;
    }

    /**
     * @param array $etapas
     */
    public function setEtapas($etapas)
    {
        $this->etapas = $etapas;
    }


    public function getEtapas()
    {
        if (empty($this->etapas)) {
            $etapas = DB::select("select serie.* as codigo from turma
                    join turmaserieregimemat ON turmaserieregimemat.ed220_i_turma = turma.ed57_i_codigo
                    join serieregimemat ON serieregimemat.ed223_i_codigo = turmaserieregimemat.ed220_i_serieregimemat
                    join serie ON serie.ed11_i_codigo = serieregimemat.ed223_i_serie
                where turma.ed57_i_codigo = {$this->getCodigo()}");

            $this->etapas = [];
            foreach ($etapas as $etapa) {
                $this->etapas[] = Etapa::with('ensino')->find($etapa->ed11_i_codigo);
            }
        }

        return $this->etapas;
    }

    public function calendario()
    {
        return $this->belongsTo(Calendario::class, 'ed57_i_calendario', 'ed52_i_codigo');
    }

    public function ensino()
    {
        return $this->belongsTo(Ensino::class, '', 'ed10_i_codigo');
    }
    /**
     * @return Matricula[]
     */
    public function getMatriculas()
    {
        if (!array_key_exists('matriculas', $this->storage)) {
            $this->storage['matriculas'] = $this->matriculas;
        }

        return $this->storage['matriculas'];
    }

    public function matriculas()
    {
        return $this->hasMany(Matricula::class, 'ed60_i_turma', 'ed57_i_codigo')
            ->orderBy('ed60_i_numaluno', 'asc');
    }

    public function turnosReferentes()
    {
        return $this->hasMany(TurmaTurnoReferente::class, 'ed336_turma', 'ed57_i_codigo')
            ->with("matriculas");
    }

    public function regencias()
    {
        return $this->hasMany(Regencia::class, 'ed59_i_turma', 'ed57_i_codigo')
            ->with('disciplinaEnsino')
            ->orderBy('ed59_i_ordenacao', 'ASC');
    }

    public function escola()
    {
        return $this->belongsTo(Escola::class, 'ed57_i_escola', 'ed18_i_codigo');
    }

    public function etapaRegimeMatricula()
    {
        return $this->hasOne(TurmaEtapaRegimeMatricula::class, 'ed220_i_turma', 'ed57_i_codigo')
            ->with('procedimento');
    }

    public function turno()
    {
        return  $this->belongsTo(Turno::class, 'ed57_i_turno', 'ed15_i_codigo');
    }

    public function base()
    {
        return  $this->belongsTo(Base::class, 'ed57_i_base', 'ed31_i_codigo');
    }

    public function getEtapaAttribute()
    {
        if ($this->regencias->count() > 0) {
            return $this->regencias->first()->etapa;
        }
        return null;
    }

    public function getRegenciaGlobalAttribute()
    {
        if ($this->base->ed31_c_contrfreq === 'G' && !is_null($this->etapa)) {
            $disicplinaEnsino = $this
                ->base
                ->disciplinasBase
                ->where('ed34_disiciplinaglobalizada', true)
                ->where('ed34_i_serie', $this->etapa->ed11_i_codigo)->first();
            if (isset($disicplinaEnsino) && is_object($disicplinaEnsino)) {
                $codDisciplina = $disicplinaEnsino->getAttribute('ed34_i_disciplina');
                return $this->regencias()->where('ed59_i_disciplina', $codDisciplina)->first();
            }
        }
        return null;
    }

    public function etapasTurma()
    {
        return $this->hasMany(TurmaEtapaRegimeMatricula::class, 'ed220_i_turma', 'ed57_i_codigo')
            ->with('procedimento');
    }
}
