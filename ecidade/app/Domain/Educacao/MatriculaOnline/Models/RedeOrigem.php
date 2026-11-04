<?php

namespace App\Domain\Educacao\MatriculaOnline\Models;

use Illuminate\Database\Eloquent\Model;

class RedeOrigem extends Model
{
    protected $table = 'plugins.redeorigem';
    public $timestamps = false;
    protected $primaryKey = 'mo05_codigo';
    public $incrementing = true;
}
