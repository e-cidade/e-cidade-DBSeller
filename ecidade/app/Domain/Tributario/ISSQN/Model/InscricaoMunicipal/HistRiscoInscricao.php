<?php

namespace App\Domain\Tributario\ISSQN\Model\HistRiscoInscri;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int q201_inscr
 * @property string q201_risco
 * @property string q201_dtrisco
 */
class HistRiscoInscricao extends Model
{
    protected $table = 'issqn.histriscoinscr';

    public $fillable = [
        'q201_inscr',
        'q201_risco',
        'q201_dtrisco'
    ];
}
