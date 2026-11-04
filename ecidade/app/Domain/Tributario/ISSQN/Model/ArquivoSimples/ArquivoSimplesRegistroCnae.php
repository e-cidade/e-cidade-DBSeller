<?php

namespace App\Domain\Tributario\ISSQN\Model\ArquivoSimples;

use Illuminate\Database\Eloquent\Model;

class ArquivoSimplesRegistroCnae extends Model
{
    protected $table = 'arqsimplesregcnae';

    protected $primaryKey = 'q185_sequencial';

    public $timestamps = false;

    protected $fillable = [
        'q185_arqsimplesreg',
        'q185_cnae',
        'q185_tipo',
    ];
}
