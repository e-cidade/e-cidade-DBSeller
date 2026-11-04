<?php

namespace App\Domain\Tributario\ISSQN\Model\Base;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Domain\Tributario\ISSQN\Model\Base\TabAtiv
 *
 * @property int $q07_inscr
 * @property int $q07_seq
 * @property int $q07_ativ
 * @property string|null $q07_datain
 * @property string|null $q07_datafi
 * @property string|null $q07_databx
 * @property int|null $q07_quant
 * @property string|null $q07_tipbx
 * @property bool|null $q07_perman
 * @property string|null $q07_horaini
 * @property string|null $q07_horafim
 * @property string|null $q07_val_ativ_int
 * @property string|null $q07_imprimealvara
 * @method static Builder|TabAtiv isActive($date)
 * @mixin \Eloquent
 */
class TabAtiv extends Model
{
    protected $table = 'tabativ';
    public $timestamps = false;
    public $incrementing = false;

    public function scopeIsActive(Builder $query, $date)
    {
        $query->where(function ($builder) use ($date) {
            $builder->whereNull("q07_datafi");
            $builder->orWhere("q07_datafi", ">", $date);
        });

        $query->where(function ($builder) use ($date) {
            $builder->whereNull("q07_databx");
            $builder->orWhere("q07_databx", ">", $date);
        });

        return $query;
    }
}
