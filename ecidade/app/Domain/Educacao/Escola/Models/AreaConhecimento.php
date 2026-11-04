<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class AreaConhecimento extends Model
{
    protected $table = 'escola.areaconhecimento';
    protected $primaryKey = 'ed293_sequencial';
    public $timestamps = false;
    public $incrementing = false;
}
