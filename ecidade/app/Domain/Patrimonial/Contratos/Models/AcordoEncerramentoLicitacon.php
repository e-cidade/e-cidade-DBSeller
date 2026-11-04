<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;

class AcordoEncerramentoLicitacon extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'ac58_sequencial';
    protected $table = 'acordoencerramentolicitacon';
    protected $fillable = [
        'ac58_acordo',
        'ac58_data'
    ];
}
