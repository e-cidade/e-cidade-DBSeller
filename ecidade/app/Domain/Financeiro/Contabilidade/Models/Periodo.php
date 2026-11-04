<?php
namespace App\Domain\Financeiro\Contabilidade\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Periodo
 * @package App\Domain\Financeiro\Contabilidade\Models
 * @property $o114_sequencial;
 * @property $o114_descricao;
 * @property $o114_qdtporano;
 * @property $o114_diainicial;
 * @property $o114_mesinicial;
 * @property $o114_diafinal;
 * @property $o114_mesfinal;
 * @property $o114_sigla;
 * @property $o114_ordem;
 */

class Periodo extends Model
{
    protected $table = 'configuracoes.periodo';
    protected $primaryKey = 'o114_sequencial';
    public $timestamps = false;
}
