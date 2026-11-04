<?php

namespace App\Domain\Financeiro\Empenho\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Natureza de Rendimento
 *
 * @property int $e167_sequencial
 * @property string $e167_codigo
 * @property string $e167_descricao
 * @property string $e167_tributo
 * @property string $e167_declarante
 */
class NaturezaRendimento extends Model
{
    protected $table = 'empenho.naturezarendimento';
    protected $primaryKey = 'e167_sequencial';
    public $timestamps = false;
}
