<?php

namespace App\Domain\Tributario\Cadastro\Models;

use App\Domain\Educacao\CentralMatriculas\Models\EscolaBairro;
use App\Domain\Educacao\Escola\Models\Escola;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\Cadastro\Models\Bairro
 *
 * @property int $j13_codi
 * @property string|null $j13_descr
 * @property string|null $j13_codant
 * @property bool|null $j13_rural
 * @method static Builder|Bairro nome($nome)
 * @mixin \Eloquent
 */
class Bairro extends Model
{
    protected $table = "cadastro.bairro";

    /**
     * @return int
     */
    public function getCodigo()
    {
        return $this->j13_codi;
    }

    /**
     * @param int $j13_codi
     */
    public function setCodigo($j13_codi)
    {
        $this->j13_codi = $j13_codi;
    }

    /**
     * @return string
     */
    public function getDescricao()
    {
        return $this->j13_descr;
    }

    /**
     * @param string $j13_descr
     */
    public function setDescricao($j13_descr)
    {
        $this->j13_descr = $j13_descr;
    }

    /**
     * @return string
     */
    public function getCodant()
    {
        return $this->j13_codant;
    }

    /**
     * @param string $j13_codant
     */
    public function setCodant($j13_codant)
    {
        $this->j13_codant = $j13_codant;
    }

    /**
     * @return bool
     */
    public function isRural()
    {
        return $this->j13_rural;
    }

    /**
     * @param bool $j13_rural
     */
    public function setRural($j13_rural)
    {
        $this->j13_rural = $j13_rural;
    }

    /**
     * Filtra o bairro pelo nome
     * @param Builder $query
     * @param $nome
     * @return Builder
     */
    public function scopeNome(Builder $query, $nome)
    {
        return $query->whereRaw(
            "upper(to_ascii(j13_descr)) = ?",
            [strtoupper(\DBString::removerCaracteresEspeciaisAcentos($nome))]
        );
    }

    public function escolas()
    {
        return $this->hasMany(Escola::class, 'ed18_i_bairro', 'j13_codi');
    }
}
