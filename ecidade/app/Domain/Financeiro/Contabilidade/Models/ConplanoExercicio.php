<?php

namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ConplanoExercicio
 *
 * @property $c62_anousu
 * @property $c62_reduz
 * @property $c62_codrec
 * @property $c62_vlrcre
 * @property $c62_vlrdeb
 */
class ConplanoExercicio extends Model
{
    protected $table = 'contabilidade.conplanoexe';
    protected $primaryKey = 'c62_reduz';
    public $timestamps = false;

    protected $fillable = [
        "c62_anousu",
        "c62_reduz",
        "c62_codrec",
        "c62_vlrcre",
        "c62_vlrdeb"
    ];
}
