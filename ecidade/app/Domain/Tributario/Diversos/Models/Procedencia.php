<?php

namespace App\Domain\Tributario\Diversos\Models;

use Illuminate\Database\Eloquent\Model;

class Procedencia extends Model
{
    public $timestamps = false;
    protected $table = 'diversos.procdiver';
    protected $primaryKey = 'dv09_procdiver';
    protected $fillable = [
        "dv09_procdiver",
        "dv09_descra",
        "dv09_descr",
        "dv09_receit",
        "dv09_hist",
        "dv09_proced",
        "dv09_tipo",
        "dv09_instit",
        "dv09_dtlimite",
        "dv09_cobranca"
    ];
}
