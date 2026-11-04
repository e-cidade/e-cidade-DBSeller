<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\IssQuant
 *
 * @mixin \Eloquent
 * @property int $q30_anousu
 * @property int $q30_inscr
 * @property float|null $q30_quant
 * @property float|null $q30_mult
 * @property float|null $q30_area
 * @property float|null $q30_tempofuncionamento
 * @property float|null $q30_areapublicidade
 * @property string|null $q30_graurisco
 */
class IssQuant extends Model
{
    protected $table = 'issquant';
    public $timestamps = false;
    public $incrementing = false;
}
