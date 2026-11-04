<?php

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Retencao subcontratacao
 *
 * @property int $e163_sequencial
 * @property int $e163_numcgm
 * @property int $e163_retencaoreceitas
 * @property int $e163_retencaotiporec
 * @property float $e163_valor
 */
class RetencaoReceitasSubcontratacao extends Model
{
    protected $table = 'empenho.retencaoreceitassubcontratacao';
    protected $primaryKey = 'e163_sequencial';
    public $timestamps = false;
}
