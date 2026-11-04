<?php

namespace App\Domain\Tributario\ISSQN\Model\ArquivoSimples;

use Illuminate\Database\Eloquent\Model;

class ArquivoSimplesEnvio extends Model
{
    protected $table = 'arqsimplesenvio';

    protected $primaryKey = 'q186_sequencial';

    public $timestamps = false;

    protected $fillable = [
        'q186_arqsimples',
        'q186_arqsimplesreg',
        'q186_situacao',
    ];
}
