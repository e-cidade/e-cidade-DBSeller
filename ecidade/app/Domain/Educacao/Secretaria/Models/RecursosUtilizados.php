<?php


namespace App\Domain\Educacao\Secretaria\Models;

use Illuminate\Database\Eloquent\Model;

class RecursosUtilizados extends Model
{
    protected $table = 'escola.recursosutilizados';
    protected $primaryKey = 'ed198_codigo';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable  = [
        "ed198_codigo",
        "ed198_descricao"
    ];
}
