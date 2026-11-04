<?php

namespace App\Domain\RecursosHumanos\Pessoal\Model;

use Illuminate\Database\Eloquent\Model;

class PrevidenciaComplementarModel extends Model
{
    protected $table = "pessoal.servidorprevidenciacomplementar";
    protected $primaryKey = 'rh312_sequencial';
    public $timestamps = false;
    public $incrementing = true;

    protected $maps = [
        'rh312_sequencial' => 'codigo',
        'rh312_instituicao' => 'instituicao',
        'rh312_matricula' => 'matricula',
        'rh312_tipoprevidencia' => 'tipo',
        'rh312_cnpj' => 'cnpj',
        'rh312_deducaorelativa' => 'deducao',
        'rh312_contribuicaopatrocinador' => 'contribuicao'
    ];

    protected $hidden = [
        'rh312_sequencial',
        'rh312_instituicao',
        'rh312_matricula',
        'rh312_tipoprevidencia',
        'rh312_cnpj',
        'rh312_deducaorelativa',
        'rh312_contribuicaopatrocinador'
    ];

    protected $appends = [
        'codigo',
        'instituicao',
        'matricula',
        'tipoPrevidencia',
        'cnpj',
        'deducaoRelativa',
        'contribuicaoPatrocinador'
    ];
    
    public function getCodigoAttribute()
    {
        return $this->attributes['rh312_sequencial'];
    }

    public function getInstituicaoAttribute()
    {
        return $this->attributes['rh312_instituicao'];
    }

    public function getMatriculaAttribute()
    {
        return $this->attributes['rh312_matricula'];
    }

    public function getTipoPrevidenciaAttribute()
    {
        return $this->attributes['rh312_tipoprevidencia'];
    }

    public function getCnpjAttribute()
    {
        return $this->attributes['rh312_cnpj'];
    }

    public function getDeducaoRelativaAttribute()
    {
        return $this->attributes['rh312_deducaorelativa'];
    }

    public function getContribuicaoPatrocinadorAttribute()
    {
        return $this->attributes['rh312_contribuicaopatrocinador'];
    }

    public function setCodigo($codigo)
    {
        $this->attributes['rh312_sequencial'] = $codigo;
    }

    public function setInstituicao($codigoInstituicao)
    {
        $this->attributes['rh312_instituicao'] = $codigoInstituicao;
    }

    public function setMatricula($matricula)
    {
        $this->attributes['rh312_matricula'] = $matricula;
    }

    public function setTipoPrevidencia($tipoprevidencia)
    {
        $this->attributes['rh312_tipoprevidencia'] = $tipoprevidencia;
    }

    public function setCnpj($cnpj)
    {
        $this->attributes['rh312_cnpj'] = $cnpj;
    }

    public function setDeducaoRelativa($deducaorelativa)
    {
        $this->attributes['rh312_deducaorelativa'] = $deducaorelativa;
    }

    public function setContribuicaoPatrocinador($contribuicaopatrocinador)
    {
        $this->attributes['rh312_contribuicaopatrocinador'] = $contribuicaopatrocinador;
    }
}
