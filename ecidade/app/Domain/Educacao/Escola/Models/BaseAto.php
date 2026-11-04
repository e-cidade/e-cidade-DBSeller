<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

class BaseAto extends Model
{
    protected $table = 'escola.baseato';
    protected $primaryKey = 'ed278_i_codigo';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'ed278_i_codigo', 'ed278_i_escolabase', 'ed278_i_atolegal'
    ];

    public function ato()
    {
        return $this->belongsTo(AtoLegal::class, 'ed278_i_atolegal', 'ed05_i_codigo');
    }
}
