<?php

namespace App\Domain\Tributario\ITBI\Models;

use Illuminate\Database\Eloquent\Model;

class AnexosItbi extends Model
{
    public $timestamps = false;

    protected $table = 'itbi.anexositbi';

    protected $primaryKey = 'it39_sequencial';

    protected $fillable = [
        'it39_arquivo',
        'it39_idstorage',
        'it39_descricao',
        'it39_observacao',
        'it39_data',
        'it39_usuario',
        'it39_itbi',
        'it39_usuarioexclusao',
        'it39_dataexclusao'
    ];
}
