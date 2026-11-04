<?php

namespace App\Domain\Educacao\Escola\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AtestadoFrequenciaItem
 * @package App\Domain\Educacao\Escola\Models
 * TODO: documentar..
 * @property int $j13_codi
 */
class AtestadoFrequenciaResumo extends Model
{
    protected $table = 'escola.atestadofrequenciaresumo';
    protected $primaryKey = 'ed364_codigo';

    public $timestamps = true;
    const CREATED_AT = 'ed364_created_at';
    const UPDATED_AT = 'ed364_updated_at';

    public $fillable = [
        'ed364_rechumanoescola',
        'ed364_atestado',
        'ed364_observacao',
        'ed364_dificilacesso',
        'ed364_regencia',
    ];

    public function getHashidAttribute()
    {
        return $this->atestado . '_' . $this->rechumanoescola;
    }

    /**
     * @return BelongsTo
     */
    public function atestado()
    {
        return $this->belongsTo(
            AtestadoFrequencia::class,
            'ed361_atestadofrequencia',
            'ed357_codigo'
        );
    }
}
