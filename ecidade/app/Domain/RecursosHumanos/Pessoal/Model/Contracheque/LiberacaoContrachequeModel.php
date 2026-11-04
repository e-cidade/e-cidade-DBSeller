<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model\Contracheque;

use Illuminate\Database\Eloquent\Model;

class LiberacaoContrachequeModel extends Model
{
    protected $table = "pessoal.rhliberacontracheque";
    protected $primaryKey = 'rh301_sequencial';
    public $timestamps = false;
    public $incrementing = true;

    protected $maps = [
        'rh301_sequencial' => 'codigo',
        'rh301_ano' => 'ano',
        'rh301_mes' => 'mes',
        'rh301_instituicao' => "instituicao",
        'rh301_salario' => 'salario',
        'rh301_rescisao' => 'rescisao',
        'rh301_complementar' => 'complementar',
        'rh301_decimo' => 'decimo',
        'rh301_adiantamento' => 'adiantamento',
        'rh301_suplementar' => 'suplementar'
    ];

    protected $hidden = [
        'rh301_sequencial',
        'rh301_ano',
        'rh301_mes',
        'rh301_instituicao',
        'rh301_salario',
        'rh301_rescisao',
        'rh301_complementar',
        'rh301_decimo',
        'rh301_adiantamento',
        'rh301_suplementar',
    ];

    protected $appends = [
        'codigo',
        'instituicao',
        'ano',
        'mes',
        'salario',
        'rescisao',
        'complementar',
        'decimo',
        'adiantamento',
        'suplementar'
    ];
    
    public function getCodigoAttribute()
    {
        return $this->attributes['rh301_sequencial'];
    }

    public function getInstituicaoAttribute()
    {
        return $this->attributes['rh301_instituicao'];
    }

    public function getAnoAttribute()
    {
        return $this->attributes['rh301_ano'];
    }

    public function getMesAttribute()
    {
        return $this->attributes['rh301_mes'];
    }

    public function getSalarioAttribute()
    {
        return $this->attributes['rh301_salario'];
    }

    public function getRescisaoAttribute()
    {
        return $this->attributes['rh301_rescisao'];
    }

    public function getComplementarAttribute()
    {
        return $this->attributes['rh301_complementar'];
    }

    public function getDecimoAttribute()
    {
        return $this->attributes['rh301_decimo'];
    }

    public function getAdiantamentoAttribute()
    {
        return $this->attributes['rh301_adiantamento'];
    }

    public function getSuplementarAttribute()
    {
        return $this->attributes['rh301_suplementar'];
    }
}
