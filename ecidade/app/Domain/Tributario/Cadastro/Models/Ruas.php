<?php

namespace App\Domain\Tributario\Cadastro\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\Cadastro\Models\Ruas
 *
 * @property int $j14_codigo
 * @property string|null $j14_nome
 * @property int|null $j14_tipo
 * @property bool|null $j14_rural
 * @property string|null $j14_lei
 * @property string|null $j14_dtlei
 * @property string|null $j14_bairro
 * @property string|null $j14_obs
 * @method static Builder|Ruas cep($cep)
 * @method static Builder|Ruas joinCep()
 * @method static Builder|Ruas nome($nome)
 * @mixin \Eloquent
 */
class Ruas extends Model
{
    protected $table = "ruas";

    /**
     * Filtra a rua pelo nome
     * @param Builder $query
     * @param $nome
     * @return Builder
     */
    public function scopeNome(Builder $query, $nome)
    {
        return $query->whereRaw(
            "upper(to_ascii(j14_nome)) = ?",
            [strtoupper(\DBString::removerCaracteresEspeciaisAcentos($nome))]
        );
    }

    public function scopeJoinCep(Builder $query)
    {
        return $query->join("ruascep", "j29_codigo", "j14_codigo");
    }

    public function scopeCep(Builder $query, $cep)
    {
        return $query->where("j29_cep", $cep);
    }
}
