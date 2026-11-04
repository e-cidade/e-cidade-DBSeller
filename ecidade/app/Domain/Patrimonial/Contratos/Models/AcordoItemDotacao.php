<?php

namespace App\Domain\Patrimonial\Contratos\Models;

use Illuminate\Database\Eloquent\Model;

class AcordoItemDotacao extends Model
{
    protected $primaryKey = 'ac22_sequencial';
    protected $table = 'acordoitemdotacao';
    protected $fillable = [];
    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(AcordoItem::class, 'ac22_acordoitem', 'ac20_sequencial');
    }
}
