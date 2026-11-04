<?php

namespace App\Domain\Saude\Farmacia\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $fa72_id
 * @property integer $fa72_bnafarinconsistencia
 * @property integer $fa72_usuario
 * @property \DateTime $fa72_data
 */
class BnafarConferencia extends Model
{
    public $timestamps = false;
    protected $table = 'farmacia.bnafarconferencias';
    protected $primaryKey = 'fa72_id';

    public $casts = [
        'fa72_data' => 'datetime'
    ];
}
