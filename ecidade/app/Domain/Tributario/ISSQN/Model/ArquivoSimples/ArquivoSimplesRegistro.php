<?php

namespace App\Domain\Tributario\ISSQN\Model\ArquivoSimples;

use Illuminate\Database\Eloquent\Model;

class ArquivoSimplesRegistro extends Model
{
    protected $table = 'arqsimplesreg';

    protected $primaryKey = 'q184_sequencial';

    public $timestamps = false;

    protected $fillable = [
        'q184_arqsimples',
        'q184_dt_solicitacao',
        'q184_cnpj',
    ];
}
