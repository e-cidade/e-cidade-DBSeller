<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;

class AcordoItem extends Model
{
    protected $primaryKey = 'ac20_sequencial';
    protected $table = 'acordoitem';
    protected $fillable = [];
    public $timestamps = false;

    public function posicao()
    {
        $this->belongsTo(AcordoPosicao::class, 'ac20_acordoposicao', 'ac26_sequencial');
    }
    public function dotacoes()
    {
        return $this->hasMany(AcordoItemDotacao::class, 'ac22_acordoitem', 'ac20_sequencial');
    }
}
