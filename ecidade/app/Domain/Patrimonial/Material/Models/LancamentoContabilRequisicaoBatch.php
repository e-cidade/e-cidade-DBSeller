<?php

namespace App\Domain\Patrimonial\Material\Models;

use App\Domain\Core\Models\BatchJob;
use Illuminate\Database\Eloquent\Model;

class LancamentoContabilRequisicaoBatch extends Model
{
    protected $table = "material.lancamentocontabilrequisicaobatch";
    protected $primaryKey = "m106_sequencial";
    protected $fillable = ["m106_batch", "m106_parametros"];

    public function batch()
    {
        return $this->belongsTo(BatchJob::class, "m106_sequencial", "id");
    }
}
