<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class AtoLegal extends Model
{
    protected $table = 'escola.atolegal';
    protected $primaryKey = 'ed05_i_codigo';
    public $timestamps = false;
    public $incrementing = false;
}
