<?php

namespace App\Domain\Financeiro\Tesouraria\Models;

use Illuminate\Database\Eloquent\Model;

class Slip extends Model
{
    protected $table = 'caixa.slip';
    protected $primaryKey = 'k17_codigo';

    public $timestamps = false;

    protected $dates = [
        'k17_data',
        'k17_dtaut',
        'k17_dtanu',
        'k17_dtestorno',
    ];

    public function decendio()
    {
        return $this->hasOne(Decendio::class, 'k230_slip', 'k17_codigo');
    }
}
