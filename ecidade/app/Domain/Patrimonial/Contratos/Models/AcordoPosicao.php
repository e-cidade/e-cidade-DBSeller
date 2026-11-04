<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;

class AcordoPosicao extends Model
{
    const TIPO_INCLUSAO = 1;
    const TIPO_REEQUILIBRIO = 2;
    const TIPO_REALINHAMENTO = 3;
    const TIPO_ADITAMENTO = 4;
    const TIPO_RENOVACAO = 5;
    const TIPO_VIGENCIA = 6;
    const TIPO_ALTERACAO_DOTACAO = 7;
    const TIPO_SUPRESSAO = 8;
    const TIPO_ALTERACAO_CESSAO_CONTRATADO = 9;
    const TIPO_APOSTILAMENTO = 10;
    protected $primaryKey = 'ac26_sequencial';
    protected $table = 'acordoposicao';
    protected $fillable = [];
    public $timestamps = false;

    public function acordo()
    {
        return $this->belongsTo(Acordo::class, 'ac26_acordo', 'ac16_sequencial');
    }

    public function itens()
    {
        return $this->hasMany(AcordoItem::class, 'ac20_acordoposicao', 'ac26_sequencial');
    }

    public function periodo()
    {
        return $this->hasMany(AcordoPosicaoPeriodo::class, 'ac36_acordoposicao', 'ac26_sequencial');
    }
}
