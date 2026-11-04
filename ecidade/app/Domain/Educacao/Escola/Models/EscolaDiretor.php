<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class EscolaDiretor extends Model
{
    protected $table = 'escola.escoladiretor';
    protected $primaryKey = 'ed254_i_codigo';
    public $timestamps = false;
    public $incrementing = false;
}
