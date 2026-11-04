<?php

namespace App\Domain\Tributario\ISSQN\Model\ArquivoSimples;

use Illuminate\Database\Eloquent\Model;

class ArquivoSimples extends Model
{
    protected $table = 'arqsimples';

    protected $primaryKey = 'q183_sequencial';

    public $timestamps = false;

    protected $fillable = [
        'q183_nomearq',
        'q183_dt_import',
        'q183_periodo_ini',
        'q183_periodo_fim',
        'q183_data_limite'
    ];
}
