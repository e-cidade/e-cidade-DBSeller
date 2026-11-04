<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;
use test\Mockery\HasUnknownClassAsTypeHintOnMethod;

/**
 * @package App\Domain\Educacao\MatriculaOnline\Models
 * @property integer $mo04_codigo
 * @property string $mo04_desc
 * @property integer $mo04_anousu
 * @property \DateTime|string $mo04_dtfim
 * @property \DateTime|string $mo04_dtini
 * @property integer $mo04_ciclo
 * @property \DateTime|string $mo04_datacorte
 * @property boolean $mo04_processada
 * @property boolean $mo04_encerrada
 * @property array $mo04_publicos_alvo
 * @property boolean $mo04_exibe_escola_origem
 * @property integer $mo04_opcoes_escolha
 * @property integer $mo04_opcoes_obrigatoria
 * @property \DateTime|string $mo04_hora_inicial
 * @property \DateTime|string $mo04_hora_final
 */

class Fase extends Model
{
    protected $table = 'plugins.fase';
    public $timestamps = false;
    protected $primaryKey = 'mo04_codigo';
    public $incrementing = true;
    protected $dates = [
        'mo04_datacorte',
        'mo04_dtfim',
        'mo04_dtini'
    ];

    protected $fillable = [
        'mo04_codigo',
        'mo04_desc',
        'mo04_anousu',
        'mo04_dtfim',
        'mo04_dtini',
        'mo04_ciclo',
        'mo04_datacorte',
        'mo04_processada',
        'mo04_encerrada',
        'mo04_publicos_alvo',
        'mo04_exibe_escola_origem',
        'mo04_hora_final',
        'mo04_hora_inicial',
        'mo04_opcoes_escolha',
        'mo04_opcoes_obrigatoria'
    ];

    public function getPublicoAlvo()
    {
        return $this->mo04_publicos_alvo;
    }

    public function setPublicoAlvo($publicoAlvo)
    {
        $this->mo04_publicos_alvo = $publicoAlvo;
    }

    public function ciclo()
    {
        return $this->belongsTo(Ciclo::class, 'mo04_ciclo', 'mo09_codigo');
    }
}
